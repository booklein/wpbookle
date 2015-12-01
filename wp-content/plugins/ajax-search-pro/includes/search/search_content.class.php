<?php
/* Prevent direct access */
defined( 'ABSPATH' ) or die( "You can't access this file directly." );

if ( ! class_exists( 'wpdreams_searchContent' ) ) {
	/**
	 * Content (post,page,CPT) search class
	 *
	 * @class       wpdreams_searchContent
	 * @version     1.0
	 * @package     AjaxSearchPro/Classes
	 * @category    Class
	 * @author      Ernest Marcinko
	 */
	class wpdreams_searchContent extends wpdreams_search {

		/**
		 * @var array of query parts
		 */
		protected $parts = array();
		/**
		 * @var array of custom field query parts
		 */
		protected $cf_parts = array();
		/**
		 * @var int the remaining limit (number of items to look for)
		 */
		protected $remaining_limit;
		/**
		 * @var string the final search query
		 */
		protected $query;

        protected $ordering = array(
            "primary"   => "relevance DESC",
            "secondary" => "post_date DESC",
            "primary_field" => "relevance"
        );

		/**
		 * Content search function
		 *
		 * @return array|string
		 */
		protected function do_search() {
			global $wpdb;
			global $q_config;

			$options      = $this->options;
			$comp_options = get_option( 'asp_compatibility' );
			$searchId     = $this->searchId;
			$searchData   = $this->searchData;

			// General variables
			$parts           = array();
			$relevance_parts = array();
			$types           = array();
			$post_types      = "(1)";
			$term_query      = "(1)";
			$post_statuses   = "(1)";
			$all_pageposts   = array();
			$postmeta_join   = "";

			// Prefixes and suffixes
			$pre_field = '';
			$suf_field = '';
			$pre_like  = '';
			$suf_like  = '';

			/**
			 *  On forced case sensitivity: Let's add BINARY keyword before the LIKE
			 *  On forced case in-sensitivity: Append the lower() function around each field
			 */
			if ( w_isset_def( $comp_options['db_force_case'], 'none' ) == 'sensitivity' ) {
				$pre_like = 'BINARY ';
			} else if ( w_isset_def( $comp_options['db_force_case'], 'none' ) == 'insensitivity' ) {
				if ( function_exists( 'mb_convert_case' ) ) {
					$this->s = mb_convert_case( $this->s, MB_CASE_LOWER, "UTF-8" );
				} else {
					$this->s = strtoupper( $this->s );
				} // if no mb_ functions :(
				$this->_s = array_unique( explode( " ", $this->s ) );

				$pre_field .= 'lower(';
				$suf_field .= ')';
			}

			/**
			 *  Check if utf8 is forced on LIKE
			 */
			if ( w_isset_def( $comp_options['db_force_utf8_like'], 0 ) == 1 ) {
				$pre_like .= '_utf8';
			}

			/**
			 *  Check if unicode is forced on LIKE, but only apply if utf8 is not
			 */
			if ( w_isset_def( $comp_options['db_force_unicode'], 0 ) == 1
			     && w_isset_def( $comp_options['db_force_utf8_like'], 0 ) == 0
			) {
				$pre_like .= 'N';
			}


			$kw_logic             = w_isset_def( $searchData['keyword_logic'], 'or' );
			$q_config['language'] = $options['qtranslate_lang'];

			$s  = $this->s; // full keyword
			$_s = $this->_s; // array of keywords


			if ( isset( $wpdb->base_prefix ) ) {
				$_prefix = $wpdb->base_prefix;
			} else {
				$_prefix = $wpdb->prefix;
			}

			if (isset($options['non_ajax_search']))
				$this->remaining_limit = 500;
			else
				$this->remaining_limit = $searchData['maxresults'];

			/**
			 *  Use separate queries if we have more than 1000 rows.
			 */
			$wpdb->get_results( "SELECT COUNT(*) FROM $wpdb->posts" );
			$use_separate_queries = $wpdb->num_rows > 10000;

			/**
			 * Determine if the priorities table should be used or not.
			 */
			$priority_rows   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . $_prefix . "ajaxsearchpro_priorities" );
			$priority_select = $priority_rows > 0 ? "
	        IFNULL((
            	SELECT
	            aspp.priority
	            FROM " . $_prefix . "ajaxsearchpro_priorities as aspp
	            WHERE aspp.post_id = $wpdb->posts.ID AND aspp.blog_id = " . get_current_blog_id() . "
            ), 100)
	        " : 100;


			/*------------------------- Statuses ----------------------------*/
			$statuses = array( 'publish' );
			if ( $searchData['searchinpending'] ) {
				$statuses[] = 'pending';
			}
			if ( $searchData['searchindrafts'] ) {
				$statuses[] = 'draft';
			}
			$words         = implode( "','", $statuses );
			$post_statuses = "(" . $pre_field . $wpdb->posts . ".post_status" . $suf_field . " IN ('$words') )";
			/*---------------------------------------------------------------*/

			/*----------------------- Gather Types --------------------------*/
			$page_q = "";
			if ( $options['set_inposts'] == 1 ) {
				$types[] = "post";
			}
			if ( $options['set_inpages'] ) {
				if ( w_isset_def( $searchData['exclude_page_parent_child'], '' ) != '' ) {
					$page_q = " OR (
		            $wpdb->posts.post_type = 'page' AND
		            $wpdb->posts.post_parent NOT IN (" . str_replace( '|', ',', $searchData['exclude_page_parent_child'] ) . ") AND
		            $wpdb->posts.ID NOT IN (" . str_replace( '|', ',', $searchData['exclude_page_parent_child'] ) . ")
		            )";
				} else {
					$types[] = "page";
				}
			}
			if ( isset( $options['customset'] ) && count( $options['customset'] ) > 0 ) {
				$types = array_merge( $types, $options['customset'] );
			}

			// If no post types selected, well then return
			if ( count( $types ) < 1 && $page_q == "" ) {
				return '';
			} else {
				$words      = implode( "','", $types );
				$post_types = "($wpdb->posts.post_type IN ('$words') $page_q)";
			}
			/*---------------------------------------------------------------*/


			// ------------------------ Categories/taxonomies ----------------------
			if ( ! isset( $options['categoryset'] ) || $options['categoryset'] == "" ) {
				$options['categoryset'] = array();
			}
			if ( ! isset( $options['termset'] ) || $options['termset'] == "" ) {
				$options['termset'] = array();
			}

			/*
				By default it's 'AND', so all the categories must fit in order to show
				that result.
			*/
			$term_logic = w_isset_def( $searchData['term_logic'], 'and' );

			$exclude_categories                          = array();
			$searchData['selected-exsearchincategories'] = w_isset_def( $searchData['selected-exsearchincategories'], array() );
			$searchData['selected-excludecategories']    = w_isset_def( $searchData['selected-excludecategories'], array() );

			if ( count( $searchData['selected-exsearchincategories'] ) > 0 ||
			     count( $searchData['selected-excludecategories'] ) > 0 ||
			     count( $options['categoryset'] ) > 0 ||
			     $searchData['showsearchincategories'] == 1
			) {

				aspDebug::start( '--searchContent-categories' );

				// If the category settings are invisible, ignore the excluded frontend categories, reset to empty array
				if ( $searchData['showsearchincategories'] == 0 ) {
					$searchData['selected-exsearchincategories'] = array();
				}

				$_all_cat    = get_terms( 'category', array( 'fields' => 'ids' ) );
				$_needed_cat = array_diff( $_all_cat, $searchData['selected-exsearchincategories'] );
				$_needed_cat = ! is_array( $_needed_cat ) ? array() : $_needed_cat;

				// I am pretty sure this is where the devil is born
				/*
					AND -> Posts NOT in an array of term ids
					OR  -> Posts in an array of term ids
				  */
				if ( $term_logic == 'and' ) {
					if ( $searchData['showsearchincategories'] == 1 ) // If the settings is visible, count for the options
					{
						$exclude_categories = array_diff( array_merge( $_needed_cat, $searchData['selected-excludecategories'] ), $options['categoryset'] );
					} else // ..if the settings is not visible, then only the excluded categories count
					{
						$exclude_categories = $searchData['selected-excludecategories'];
					}
				} else {
					if ( $searchData['showsearchincategories'] == 1 ) {
						// If the settings is visible, check which is selected
						$exclude_categories = count( $options['categoryset'] ) == 0 ? array( -10 ) : $options['categoryset'];
					} else {
						// .. otherwise this thing here
						$exclude_categories = array_diff( $_needed_cat, $searchData['selected-excludecategories'] );
						$exclude_categories = count( $exclude_categories ) == 0 ? array( -10 ) : $exclude_categories;
					}
				}

				// If every category is selected, then we don't need to filter anything out.
				/*if (count($exclude_categories) == count($_all_cat))
					$exclude_categories = array();  */

				aspDebug::stop( '--searchContent-categories' );
			}

			$exclude_terms                       = array();
			$exclude_showterms                   = array();
			$searchData['selected-showterms']    = w_isset_def( $searchData['selected-showterms'], array() );
			$searchData['selected-excludeterms'] = w_isset_def( $searchData['selected-excludeterms'], array() );

			if ( count( $searchData['selected-showterms'] ) > 0 ||
			     count( $searchData['selected-excludeterms'] ) > 0 ||
			     count( $options['termset'] ) > 0
			) {

				aspDebug::start( '--searchContent-terms' );

				foreach ( $searchData['selected-excludeterms'] as $tax => $terms ) {
					$exclude_terms = array_merge( $exclude_terms, $terms );
				}
				// If the term settings are invisible, ignore the excluded frontend terms, reset to empty array
				if ( $searchData['showsearchintaxonomies'] == 0 ) {
					$searchData['selected-showterms'] = array();
				}
				foreach ( $searchData['selected-showterms'] as $tax => $terms ) {
					$exclude_showterms = array_merge( $exclude_showterms, $terms );
				}

				/*if ($term_logic == 'and')
					$exclude_terms = array_diff(array_merge($exclude_terms, $exclude_showterms), $options['termset']);
				else
					$exclude_terms = count($options['termset']) == 0 ? array(-10) : $options['termset'];  */

				aspDebug::stop( '--searchContent-terms' );

				/*
					AND -> Posts NOT in an array of term ids
					OR  -> Posts in an array of term ids
				  */
				if ( $term_logic == 'and' ) {
					if ( $searchData['showsearchintaxonomies'] == 1 ) // If the settings is visible, count for the options
					{
						$exclude_terms = array_diff( array_merge( $exclude_terms, $exclude_showterms ), $options['termset'] );
					} else // ..if the settings is not visible, then only the excluded categories count
					{
						$exclude_terms = $exclude_terms;
					}
				} else {
					if ( $searchData['showsearchintaxonomies'] == 1 ) {
						// If the settings is visible, check which is selected
						$exclude_terms = count( $options['termset'] ) == 0 ? array( -10 ) : $options['termset'];
					} else {
						// .. otherwise we bail out, and exclude everything. NOT SOLVED!
						// But here we would need all term IDs, which is not an option
						$exclude_terms = array( -15 );
					}
				}

			}

			$all_terms = array();
			$all_terms = array_merge( $exclude_categories, $exclude_terms );
			//var_dump($all_terms);

			/**
			 *  New method since ASP 4.1
			 *
			 *  This is way more efficient, despite it looks more complicated.
			 *  Multiple sub-select is not an issue, since the query can use PRIMARY keys as indexes
			 */
			if ( count( $all_terms ) > 0 ) {
				$words = implode( ',', $all_terms );

				// Quick explanation for the AND
				// .. MAIN SELECT: selects all object_ids that are not in the array
				// .. SUBSELECT:   excludes all the object_ids that are part of the array
				// This is used because of multiple object_ids (posts in more than 1 category)
				if ( $term_logic == 'and' ) {
					$term_query = "(
              NOT EXISTS (SELECT * FROM $wpdb->term_relationships as xt WHERE xt.object_id = $wpdb->posts.ID)
              OR
						$wpdb->posts.ID IN (
							SELECT DISTINCT(tr.object_id)
								FROM $wpdb->term_relationships AS tr
				                LEFT JOIN $wpdb->term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
												WHERE
													tt.term_id NOT IN ($words)
													AND tr.object_id NOT IN (
														SELECT DISTINCT(trs.object_id)
														FROM $wpdb->term_relationships AS trs
				                    LEFT JOIN $wpdb->term_taxonomy as tts ON trs.term_taxonomy_id = tts.term_taxonomy_id
														WHERE tts.term_id IN ($words)
													)
										)
									)";
				} else {
					$term_query = "(
                NOT EXISTS (SELECT * FROM $wpdb->term_relationships as xt WHERE xt.object_id = $wpdb->posts.ID)
                OR
                $wpdb->posts.ID IN ( SELECT DISTINCT(tr.object_id)
			            FROM $wpdb->term_relationships AS tr
			            LEFT JOIN $wpdb->term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			            WHERE tt.term_id IN ($words)
			          ) )";
				}
			}

			// ---------------------------------------------------------------------


			/*------------ ttids in the main SELECT, we might not need it ---------*/
			// ttid is only used if grouping by category or filtering by category is active
			$term_select = '""';
			if ( $searchData['groupby'] == 1 || count( $all_terms ) > 0 ) {
				$term_select = "(SELECT DISTINCT CONCAT('--', GROUP_CONCAT( $wpdb->term_relationships.term_taxonomy_id SEPARATOR '----' ), '--')
                FROM $wpdb->term_relationships
                WHERE ($wpdb->term_relationships.object_id = $wpdb->posts.ID) )";
			}
			// ---------------------------------------------------------------------


			/*------------- Custom Fields with Custom selectors -------------*/
			if ( isset( $options['aspf'] ) && isset( $options['aspfdata'] ) ) {

				aspDebug::start( '--searchContent-cf' );

				$parts = array();

				foreach ( $options['aspfdata'] as $u_data ) {
					$data   = json_decode( base64_decode( $u_data ) );
					$posted = $this->escape( $options['aspf'][ $data->asp_f_field ] );

					$ll_like = "";
					$rr_like = "";

					if ( isset( $data->asp_f_operator ) ) {
						switch ( $data->asp_f_operator ) {
							case 'eq':
								$operator = "=";
								$posted   = $this->force_numeric( $posted );
								break;
							case 'neq':
								$operator = "<>";
								$posted   = $this->force_numeric( $posted );
								break;
							case 'lt':
								$operator = "<";
								$posted   = $this->force_numeric( $posted );
								break;
							case 'gt':
								$operator = ">";
								$posted   = $this->force_numeric( $posted );
								break;
							case 'elike':
								$operator = "=";
								$ll_like  = "'";
								$rr_like  = "'";
								break;
							case 'like':
								$operator = "LIKE";
								$ll_like  = "'%";
								$rr_like  = "%'";
								break;
							default:
								$operator = "=";
								$posted   = $this->force_numeric( $posted );
								break;
						}
					}

					//var_dump($data);
					/*if (w_isset_def($searchData['cf_null_values'], 1) == 1) {
						$cf_key_is_null .= " OR $wpdb->postmeta.meta_key IS NULL";
						$cf_val_is_null .= " OR $wpdb->postmeta.meta_value IS NULL";
					}*/

					if ( $data->asp_f_type == 'range' && isset( $posted['lower'] ) ) {
						$posted  = $this->force_numeric( $posted );
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value BETWEEN " . $posted['lower'] . " AND " . $posted['upper'] . " ) )";
					} else if ( $data->asp_f_type == 'slider' && isset( $posted ) ) {
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator $posted  ) )";
					} else if ( ( $data->asp_f_type == 'radio' || $data->asp_f_type == 'hidden' || $data->asp_f_type == 'text' ) && isset( $posted ) ) {
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator " . $ll_like . $posted . $rr_like . " ) )";
					} else if ( $data->asp_f_type == 'dropdown' && isset( $posted ) ) {
						if ( w_isset_def( $data->asp_f_dropdown_multi, 'asp_unchecked' ) == 'asp_checked' && count( $posted ) > 0 ) {
							// The AND logic doesn't make any sense
							$logic  = 'OR';
							$values = '';
							foreach ( $posted as $v ) {
								if ( $values != '' ) {
									$values .= " $logic $wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
								} else {
									$values .= "$wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
								}
							}

							$values  = $values == '' ? '0' : $values;
							$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";
						} else {
							$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                        ($wpdb->postmeta.meta_value $operator " . $ll_like . $posted . $rr_like . " ) )";
						}

					} else if ( $data->asp_f_type == 'checkboxes' && isset( $posted ) ) {

						$logic  = $data->asp_f_checkboxes_logic;
						$values = '';
						foreach ( $posted as $v => $vv ) {
							if ( $values != '' ) {
								$values .= " $logic $wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
							} else {
								$values .= "$wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
							}
						}
						$values  = $values == '' ? '0' : $values;
						$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";

					}

				}

				$this->cf_parts = $parts;

				aspDebug::stop( '--searchContent-cf' );
			}

			$meta_count = count( $this->cf_parts );

			$cf_query = implode( " OR ", $this->cf_parts );
			if ( $cf_query == "" ) {
				$cf_select = "(1)";
				//$cf_having = "";
			} else {
				if ( w_isset_def( $searchData['cf_logic'], 'AND' ) == 'AND' ) {
					$cf_count = $meta_count;
				} else {
					$cf_count = 1;
				}

				/**
				 * Far effective method for custom fields, bypassing the HAVING
				 */
				$cf_select = "
				( (
	                SELECT COUNT(*) FROM $wpdb->postmeta WHERE
	                    $wpdb->postmeta.post_id = $wpdb->posts.ID
	                AND
	                    ($cf_query)
                ) >= $cf_count )";
				/*
				if (w_isset_def($searchData['cf_logic'], 'AND') == 'AND')
					$cf_having = "meta_matches >= " . $meta_count;
				else
					$cf_having = "meta_matches >= 1";
				if ($term_query != '')
					$cf_having = " AND " . $cf_having;
				else
					$cf_having = "HAVING " . $cf_having;
				*/
			}
			/*---------------------------------------------------------------*/


			/*------------------------ Exclude id's -------------------------*/
			if ( isset( $searchData['excludeposts'] ) && $searchData['excludeposts'] != "" ) {
				$exclude_posts = "($wpdb->posts.ID NOT IN (" . $searchData['excludeposts'] . "))";
			} else {
				$exclude_posts = "($wpdb->posts.ID NOT IN (-55))";
			}
			/*---------------------------------------------------------------*/

			/*------------------------ Term JOIN -------------------------*/
			// If the search in terms is not active, we don't need this unnecessary big join
			$term_join = "";
			if ( $options['searchinterms'] ) {
				$term_join = "
                LEFT JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
                LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
                LEFT JOIN $wpdb->terms ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id";
			}
			/*---------------------------------------------------------------*/


			/*------------------------- WPML filter -------------------------*/
			/*
			$wpml_join = "";
			if ( isset($options['wpml_lang'])
				&& w_isset_def($searchData['wpml_compatibility'], 1) == 1
			)
				$wpml_join = "RIGHT JOIN " . $wpdb->base_prefix . "icl_translations ON (
					$wpdb->posts.ID = " . $wpdb->base_prefix . "icl_translations.element_id AND
					" . $wpdb->base_prefix . "icl_translations.language_code = '" . $this->escape($options['wpml_lang']) . "' AND
					" . $wpdb->base_prefix . "icl_translations.element_type LIKE '%post_%'
				)";
			*/
            $wpml_query = "(1)";
            if ( isset( $options['wpml_lang'] )
                && w_isset_def( $searchData['wpml_compatibility'], 1 ) == 1
            ) {
                global $sitepress;
                $site_lang_selected = false;
                $wpml_post_types_arr = array();

                foreach ($types as $tt) {
                    $wpml_post_types_arr[] = "post_" . $tt;
                }
                $wpml_post_types = implode( "','", $wpml_post_types_arr );

                // Let us get the default site language if possible
                if ( is_object($sitepress) && method_exists($sitepress, 'get_default_language') ) {
                    $site_lang_selected = $sitepress->get_default_language() == $options['wpml_lang'] ? true : false;
                }

                $wpml_query = "
				EXISTS (
					SELECT DISTINCT(wpml.element_id)
					FROM " . $wpdb->base_prefix . "icl_translations as wpml
					WHERE
	                    $wpdb->posts.ID = wpml.element_id AND
	                    wpml.language_code = '" . $this->escape( $options['wpml_lang'] ) . "' AND
	                    wpml.element_type IN ('$wpml_post_types')
                )";

                /**
                 * For missing translations..
                 * If the site language is used, the translation can be non-existent
                 */
                if ($site_lang_selected) {
                    $wpml_query = "
                    NOT EXISTS (
                        SELECT DISTINCT(wpml.element_id)
                        FROM " . $wpdb->base_prefix . "icl_translations as wpml
                        WHERE
                            $wpdb->posts.ID = wpml.element_id AND
                            wpml.element_type IN ('$wpml_post_types')
                    ) OR
                    " . $wpml_query;
                }
            }
			/*---------------------------------------------------------------*/

            /*--------------------- Other Query stuff -----------------------*/
            // Do not select the content field, if it is not used at all
            $select_content = (
                ( $searchData['showdescription'] == 1 ) ||
                ( $searchData['resultstype'] == "isotopic" && $searchData['i_ifnoimage'] == 'description' ) ||
                ( $searchData['resultstype'] == "polaroid" && ($searchData['pifnoimage'] == 'descinstead' || $searchData['pshowdesc'] == 1) )
            ) ? $wpdb->posts. ".post_content" : "''";

            // Do not select excerpt if its not used at all
            $select_excerpt = (
                w_isset_def($searchData['titlefield'], 0) == 1 ||
                w_isset_def($searchData['descriptionfield'], 0) == 1
            ) ? $wpdb->posts. ".post_excerpt" : "''";
            /*---------------------------------------------------------------*/


			/**
			 * This is the main query.
			 *
			 * The ttid field is a bit tricky as the term_taxonomy_id doesn't always equal term_id,
			 * so we need the LEFT JOINS :(
			 */

            $this->ordering['primary'] = w_isset_def( $searchData['orderby_primary'], "relevance DESC" );
            $this->ordering['secondary'] = w_isset_def( $searchData['orderby'], "post_date DESC" );

            $_primary_field = explode(" ", $this->ordering['primary']);
            $this->ordering['primary_field'] = $_primary_field[0];

            $orderby_primary    = str_replace( "post_", $wpdb->posts . ".post_",  w_isset_def( $searchData['orderby_primary'], "relevance DESC" ) );
			$orderby_secondary  = str_replace( "post_", $wpdb->posts . ".post_",  w_isset_def( $searchData['orderby'], "post_date DESC" ) );

			$this->query = "
    		SELECT 
            $wpdb->posts.post_title as title,
            $wpdb->posts.post_title as post_title,
            $wpdb->posts.ID as id,
            $wpdb->posts.post_date as date,
            $wpdb->posts.post_date as post_date,
            $select_content as content,
            $select_excerpt as excerpt,
            $wpdb->posts.post_type as post_type,
            'pagepost' as content_type,
            (SELECT
                $wpdb->users." . w_isset_def( $searchData['author_field'], 'display_name' ) . " as author
                FROM $wpdb->users
                WHERE $wpdb->users.ID = $wpdb->posts.post_author
            ) as author,
            $term_select as ttid,
            $wpdb->posts.post_type as post_type,
            $priority_select AS priority,
            {relevance_query} as relevance
            FROM $wpdb->posts
            {postmeta_join}
            $term_join
            WHERE
                    $post_types
                AND $term_query
                AND $cf_select
                AND $post_statuses
                AND {like_query}
                AND $exclude_posts
                AND ( $wpml_query )
            GROUP BY
                $wpdb->posts.ID
            ORDER BY priority DESC, $orderby_primary, $orderby_secondary
            LIMIT {remaining_limit}";


			$words        = $options['set_exactonly'] == 1 ? array( $s ) : $_s;
			//$regexp_words = count( $_s > 0 ) ? implode( '|', $_s ) : $s;

			/*----------------------- Title query ---------------------------*/
			if ( $options['set_intitle'] ) {
				$parts           = array();
				$relevance_parts = array();

				if ( $kw_logic == 'or' || $kw_logic == 'and' ) {
					$op = strtoupper( $kw_logic );
					if ( count( $_s ) > 0 ) {
						$_like = implode( "%'$suf_like " . $op . " " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE $pre_like'%", $words );
					} else {
						$_like = $s;
					}
					$parts[] = "( " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op    = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ( $words as $word ) {
						$_like[] = "
                           ( " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE $pre_like'" . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE $pre_like'% " . $word . "'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " = '" . $word . "')";
					}
					$parts[] = "(" . implode( ' ' . $op . ' ', $_like ) . ")";
				}

				/*$relevance_parts[] = "(case when
				(".$pre_field.$wpdb->posts.".post_title".$suf_field." REGEXP '$regexp_words')
				 then $searchData[titleweight] else 0 end)";*/
				$relevance_parts[] = "(case when
                (" . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE '%$s%')
                 then $searchData[etitleweight] else 0 end)";

				// The first word relevance is higher
				if ( count( $_s ) > 0 ) {
					$relevance_parts[] = "(case when
                  (" . $pre_field . $wpdb->posts . ".post_title" . $suf_field . " LIKE '%" . $_s[0] . "%')
                   then $searchData[etitleweight] else 0 end)";
				}

				$this->parts[] = array( $parts, $relevance_parts );
			}
			/*---------------------------------------------------------------*/

			/*---------------------- Content query --------------------------*/
			if ( $options['set_incontent'] ) {
				$parts           = array();
				$relevance_parts = array();

				if ( $kw_logic == 'or' || $kw_logic == 'and' ) {
					$op = strtoupper( $kw_logic );
					if ( count( $_s ) > 0 ) {
						$_like = implode( "%'$suf_like " . $op . " " . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE $pre_like'%", $words );
					} else {
						$_like = $s;
					}
					$parts[] = "( " . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op    = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ( $words as $word ) {
						$_like[] = "
                           (" . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE $pre_like'" . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE $pre_like'% " . $word . "'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " = '" . $word . "')";
					}
					$parts[] = "(" . implode( ' ' . $op . ' ', $_like ) . ")";
				}

				if ( count( $_s ) > 0 ) {
					$relevance_parts[] = "(case when
                    (" . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE '%" . $_s[0] . "%')
                     then $searchData[contentweight] else 0 end)";
				}
				$relevance_parts[] = "(case when
                (" . $pre_field . $wpdb->posts . ".post_content" . $suf_field . " LIKE '%$s%')
                 then $searchData[econtentweight] else 0 end)";

				$this->parts[] = array( $parts, $relevance_parts );
			}
			/*---------------------------------------------------------------*/

			/*---------------------- Excerpt query --------------------------*/
			if ( $options['set_inexcerpt'] ) {
				$parts           = array();
				$relevance_parts = array();

				if ( $kw_logic == 'or' || $kw_logic == 'and' ) {
					$op = strtoupper( $kw_logic );
					if ( count( $_s ) > 0 ) {
						$_like = implode( "%'$suf_like " . $op . " " . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE $pre_like'%", $words );
					} else {
						$_like = $s;
					}
					$parts[] = "( " . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op    = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ( $words as $word ) {
						$_like[] = "
                           (" . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE $pre_like'" . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE $pre_like'% " . $word . "'$suf_like
                        OR  " . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " = '" . $word . "')";
					}
					$parts[] = "(" . implode( ' ' . $op . ' ', $_like ) . ")";
				}

				if ( count( $_s ) > 0 ) {
					$relevance_parts[] = "(case when
                    (" . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE '%" . $_s[0] . "%')
                     then $searchData[excerptweight] else 0 end)";
				}
				$relevance_parts[] = "(case when
                (" . $pre_field . $wpdb->posts . ".post_excerpt" . $suf_field . " LIKE '%$s%')
                 then $searchData[eexcerptweight] else 0 end)";

				$this->parts[] = array( $parts, $relevance_parts );
			}
			/*---------------------------------------------------------------*/

			/*------------------------ Term query ---------------------------*/
			if ( $options['searchinterms'] ) {
				$parts           = array();
				$relevance_parts = array();

				if ( $kw_logic == 'or' || $kw_logic == 'and' ) {
					$op = strtoupper( $kw_logic );
					if ( count( $_s ) > 0 ) {
						$_like = implode( "%'$suf_like " . $op . " " . $pre_field . $wpdb->terms . ".name" . $suf_field . " LIKE $pre_like'%", $words );
					} else {
						$_like = $s;
					}
					$parts[] = "( " . $pre_field . $wpdb->terms . ".name" . $suf_field . " LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op    = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ( $words as $word ) {
						$_like[] = "
                           (" . $pre_field . $wpdb->terms . ".name" . $suf_field . " LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->terms . ".name" . $suf_field . " LIKE $pre_like'" . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->terms . ".name" . $suf_field . " LIKE $pre_like'% " . $word . "'$suf_like
                        OR  " . $pre_field . $wpdb->terms . ".name" . $suf_field . " = '" . $word . "')";
					}
					$parts[] = "(" . implode( ' ' . $op . ' ', $_like ) . ")";
				}


				/*$relevance_parts[] = "(case when
				(".$pre_field.$wpdb->terms.".name".$suf_field." REGEXP '$regexp_words')
				 then $searchData[termsweight] else 0 end)";*/
				$relevance_parts[] = "(case when
                (" . $pre_field . $wpdb->terms . ".name" . $suf_field . " = '$s')
                 then $searchData[etermsweight] else 0 end)";

				$this->parts[] = array( $parts, $relevance_parts );
			}
			/*---------------------------------------------------------------*/

			/*---------------------- Custom Fields --------------------------*/
			if ( isset( $searchData['selected-customfields'] ) ) {
				$parts           = array();
				$relevance_parts = array();
				$postmeta_join   = "LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID";

				$selected_customfields = $searchData['selected-customfields'];
				if ( is_array( $selected_customfields ) && count( $selected_customfields ) > 0 ) {
					foreach ( $selected_customfields as $cfield ) {
						if ( $kw_logic == 'or' || $kw_logic == 'and' ) {
							$op = strtoupper( $kw_logic );
							if ( count( $_s ) > 0 ) {
								$_like = implode( "%'$suf_like " . $op . " " . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " LIKE $pre_like'%", $words );
							} else {
								$_like = $s;
							}
							$parts[] = "( $wpdb->postmeta.meta_key='$cfield' AND " . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " LIKE $pre_like'%" . $_like . "%'$suf_like )";
                        } else {
							$_like = array();
							$op    = $kw_logic == 'andex' ? 'AND' : 'OR';
							foreach ( $words as $word ) {
								$_like[] = "
                           (" . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " LIKE $pre_like'" . $word . " %'$suf_like
                        OR  " . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " LIKE $pre_like'% " . $word . "'$suf_like
                        OR  " . $pre_field . $wpdb->postmeta . ".meta_value" . $suf_field . " = '" . $word . "')";
							}
							$parts[] = "( $wpdb->postmeta.meta_key='$cfield' AND (" . implode( ' ' . $op . ' ', $_like ) . ") )";
						}

					}
				}
				$this->parts[] = array( $parts, $relevance_parts );
			}
			// Add the meta join if needed..
			$this->query = str_replace( '{postmeta_join}', $postmeta_join, $this->query );
			/*---------------------------------------------------------------*/


			if ( $use_separate_queries ) {
				$i = 1;
				foreach ( $this->parts as $part ) {
					$querystr = $this->build_query( $part );
					//var_dump($querystr); //die();
					aspDebug::start( '--searchContent-query' . $i );
					$pageposts = $wpdb->get_results( $querystr, OBJECT );
					aspDebug::stop( '--searchContent-query' . $i );
					$diff = array_udiff( $pageposts, $all_pageposts, array( $this, 'compare_posts' ) );
					//$diff = array_diff(, $all_pageposts);
					$all_pageposts = array_merge( $all_pageposts, $diff );

					$this->remaining_limit -= count( $pageposts );
					if ( $this->remaining_limit <= 0 ) {
						break;
					}
					$i ++;
				}
			} else if ( count( $this->parts ) > 0 ) {
				//var_dump($this->parts);
				$querystr = $this->build_query( $this->parts, true );
				//var_dump($querystr); //die("!!");
				$all_pageposts = $wpdb->get_results( $querystr, OBJECT );
                //var_dump($all_pageposts);
			}

			if ( count( $all_pageposts ) > 0 ) {
				/*if ( w_isset_def( $searchData['userelevance'], 1 ) == 1 ) {
					usort( $all_pageposts, array( $this, 'compare_by_rp' ) );
				} else if ( $orderby == 'post_date DESC' ) {
					usort( $all_pageposts, array( $this, 'compare_by_rd_desc' ) );
				} else if ( $orderby == 'post_date ASC' ) {
					usort( $all_pageposts, array( $this, 'compare_by_rd_asc' ) );
				} else if ( $orderby == 'post_title DESC' ) {
					usort( $all_pageposts, array( $this, 'compare_by_title_desc' ) );
				} else {
					usort( $all_pageposts, array( $this, 'compare_by_title_asc' ) );
				}*/
                usort( $all_pageposts, array( $this, 'compare_by_primary' ) );

                /**
                 * Let us save some time. There is going to be a user selecting the same sorting
                 * for both primary and secondary. Only do secondary if it is different from the primary.
                 */
                if ( $orderby_primary != $orderby_secondary ) {
                    $i = 0;
                    foreach ($all_pageposts as $pk => $pp) {
                        $all_pageposts[$pk]->primary_order = $i;
                        $i++;
                    }
                    usort( $all_pageposts, array( $this, 'compare_by_secondary' ) );
                }

			}

			$this->results = $all_pageposts;

			return $all_pageposts;

		}


        /**
         * usort() custom function, sort by primary
         *
         * @param $a
         * @param $b
         *
         * @return int
         */
        protected function compare_by_primary( $a, $b ) {
            if ( $a->priority === $b->priority ) {

                switch ( $this->ordering['primary'] ) {
                    case "relevance DESC":
                        return $b->relevance - $a->relevance;
                        break;
                    case "post_date DESC":
                        return strtotime( $b->date ) - strtotime( $a->date );
                        break;
                    case "post_date ASC":
                        return strtotime( $a->date ) - strtotime( $b->date );
                        break;
                    case "post_title DESC":
                        return strcasecmp( $b->title, $a->title );
                        break;
                    case "post_title ASC":
                        return strcasecmp( $a->title, $b->title );
                        break;
                    default:
                        return $b->relevance - $a->relevance;
                        break;
                }

            }

            return $b->priority - $a->priority;
        }

        /**
         * usort() custom function, sort by secondary
         *
         * @param $a
         * @param $b
         *
         * @return int
         */
        protected function compare_by_secondary( $a, $b ) {
            if ( $a->priority === $b->priority ) {

                $field = $this->ordering['primary_field'];

                if ( $a->$field == $b->$field ) {

                    switch ( $this->ordering['secondary'] ) {
                        case "relevance DESC":
                            return $b->relevance - $a->relevance;
                            break;
                        case "post_date DESC":
                            return strtotime( $b->date ) - strtotime( $a->date );
                            break;
                        case "post_date ASC":
                            return strtotime( $a->date ) - strtotime( $b->date );
                            break;
                        case "post_title DESC":
                            return strcasecmp( $b->title, $a->title );
                            break;
                        case "post_title ASC":
                            return strcasecmp( $a->title, $b->title );
                            break;
                        default:
                            return $b->relevance - $a->relevance;
                            break;
                    }

                }

                /**
                 * If the primary fields are not equal, then leave it as it is.
                 * The primary sorting already sorted the items out so return 0 - as equals
                 */
                return $a->primary_order - $b->primary_order;

            }

            return $b->priority - $a->priority;
        }

		/**
		 * usort() custom function, sort by ID
		 *
		 * @param $obj_a
		 * @param $obj_b
		 *
		 * @return mixed
		 */
		protected function compare_posts( $obj_a, $obj_b ) {
			return $obj_a->id - $obj_b->id;
		}

		/**
		 * usort() custom function, sort by priority > relevance > date > title
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		protected function compare_by_rp( $a, $b ) {
			if ( $a->priority === $b->priority ) {
				if ( $a->relevance === $b->relevance ) {
					if ( $a->date != null && $a->date != "" ) {
						return strtotime( $b->date ) - strtotime( $a->date );
					} else {
						return strcasecmp( $a->title, $b->title );
					}
				} else {
					return $b->relevance - $a->relevance;
				}
			}

			return $b->priority - $a->priority;
		}

		/**
		 * usort() custom function, sort by priority > date ascending
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		protected function compare_by_rd_asc( $a, $b ) {
			if ( $a->priority === $b->priority ) {
				return strtotime( $a->date ) - strtotime( $b->date );
			}

			return $b->priority - $a->priority;
		}

		/**
		 * usort() custom function, sort by priority > date descending
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		protected function compare_by_rd_desc( $a, $b ) {
			if ( $a->priority === $b->priority ) {
				return strtotime( $b->date ) - strtotime( $a->date );
			}

			return $b->priority - $a->priority;
		}

		/**
		 * usort() custom function, sort by title descending
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		protected function compare_by_title_desc( $a, $b ) {
			return strcasecmp( $b->title, $a->title );
		}

		/**
		 * usort() custom function, sort by title ascending
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		protected function compare_by_title_asc( $a, $b ) {
			return strcasecmp( $a->title, $b->title );
		}


		/**
		 * Builds the query from the parts
		 *
		 * @param $parts
		 * @param bool $is_multi tells if all the parts (like and relevance) are passed at once
		 *
		 * @return string query
		 */
		protected function build_query( $parts, $is_multi = false ) {

			$searchData = $this->searchData;

			$l_parts = array(); // like parts
			$r_parts = array(); // relevance parts

			if ( $is_multi == true ) {
				foreach ( $parts as $part ) {
					$l_parts = array_merge( $l_parts, $part[0] );
					$r_parts = array_merge( $r_parts, $part[1] );
				}
			} else {
				$l_parts = $parts[0];
				$r_parts = $parts[1];
			}

			//var_dump($l_parts);var_dump($r_parts);

			/*------------------------- Build like --------------------------*/
			$like_query = implode( ' OR ', $l_parts );
			if ( $like_query == "" ) {
				$like_query = "(1)";
			} else {
				$like_query = "($like_query)";
			}
			/*---------------------------------------------------------------*/

			/*---------------------- Build relevance ------------------------*/
			$relevance = implode( ' + ', $r_parts );
			if ( $searchData['userelevance'] != 1 || $relevance == "" ) {
				$relevance = "(1)";
			} else {
				$relevance = "($relevance)";
			}

			/*---------------------------------------------------------------*/


			return str_replace(
				array( "{relevance_query}", "{like_query}", "{remaining_limit}" ),
				array( $relevance, $like_query, $this->remaining_limit ),
				$this->query
			);

		}

		/**
		 * Post-processes the results
		 *
		 * @return array of results
		 */
		protected function post_process() {

			$pageposts  = is_array( $this->results ) ? $this->results : array();
			$options    = $this->options;
			$searchId   = $this->searchId;
			$searchData = $this->searchData;
			$s          = $this->s;
			$_s         = $this->_s;

            // Get multibite or regular functions to variables
            extract($this->mb_f);

			// No post processing is needed on non-ajax search
			if ( isset($options['non_ajax_search']) ) {
				$this->results = $pageposts;
				return $pageposts;
			}

			if ( is_multisite() ) {
				$home_url = network_home_url();
			} else {
				$home_url = home_url();
			}


			foreach ( $pageposts as $k => $v ) {
				$r          = &$pageposts[ $k ];
				$r->title   = w_isset_def( $r->title, null );
				$r->content = w_isset_def( $r->content, null );
				$r->image   = w_isset_def( $r->image, null );
				$r->author  = w_isset_def( $r->author, null );
				$r->date    = w_isset_def( $r->date, null );
			}

            /*--------------------- For Image Parser -----------------------*/
            // Do not select the content field, if it is not used at all
            $get_content = (
                ( $searchData['showdescription'] == 1 ) ||
                ( $searchData['resultstype'] == "isotopic" && $searchData['i_ifnoimage'] == 'description' ) ||
                ( $searchData['resultstype'] == "polaroid" && ($searchData['pifnoimage'] == 'descinstead' || $searchData['pshowdesc'] == 1) )
            ) ? false : true;

            // Do not select excerpt if its not used at all
            $get_excerpt = (
                w_isset_def($searchData['titlefield'], 0) == 1 ||
                w_isset_def($searchData['descriptionfield'], 0) == 1
            ) ? false : true;
            /*---------------------------------------------------------------*/

			aspDebug::start( '--searchContent-posptrocess' );

			/* Images, title, desc */
			foreach ( $pageposts as $k => $v ) {

				// Let's simplify things
				$r = &$pageposts[ $k ];

				if ( isset( $options['switch_on_preprocess'] ) && is_multisite() ) {
					switch_to_blog( $r->blogid );
				}

				$r          = apply_filters( 'asp_result_before_prostproc', $r, $searchId );
				$r->title   = apply_filters( 'asp_result_title_before_prostproc', $r->title, $r->id, $searchId );
				$r->content = apply_filters( 'asp_result_content_before_prostproc', $r->content, $r->id, $searchId );
				$r->image   = apply_filters( 'asp_result_image_before_prostproc', $r->image, $r->id, $searchId );
				$r->author  = apply_filters( 'asp_result_author_before_prostproc', $r->author, $r->id, $searchId );
				$r->date    = apply_filters( 'asp_result_date_before_prostproc', $r->date, $r->id, $searchId );

				$r->link = get_permalink( $v->id );

				// ---- URL FIX for WooCommerce product variations
				if ( $r->post_type == 'product_variation' && class_exists( 'WC_Product_Variation' ) ) {
					$wc_prod_var_o = new WC_Product_Variation( $r->id );
					$r->link       = $wc_prod_var_o->get_permalink();
				}

				$caching_options = w_false_def( get_option( 'asp_caching' ), get_option( 'asp_caching_def' ) );

				$use_bfi = w_isset_def( $caching_options['use_bfi_thumb'], 1 );

				$image_settings = $searchData['image_options'];

				if ( $image_settings['show_images'] != 0 ) {
					if ( $image_settings['image_cropping'] == 0 ) {
						// Use the BFI parser, but no caching
						$im = $this->getBFIimage( $r, $get_content, $get_excerpt );
						if ( $im != '' ) {
							$r->image = $im;
						}
					} else if ( $use_bfi == 0 ) {
						$im = $this->getCachedImage( $r, $get_content, $get_excerpt );
						if ( $im != '' ) {
							$r->image = $im;
						}
					} else {
						$im = $this->getBFIimage( $r, $get_content, $get_excerpt );
						if ( $im != '' && strpos( $im, "mshots/v1" ) === false ) {
							if ( w_isset_def( $image_settings['image_transparency'], 1 ) == 1 ) {
								$bfi_params = array( 'width'  => $image_settings['image_width'],
								                     'height' => $image_settings['image_height'],
								                     'crop'   => true
								);
							} else {
								$bfi_params = array( 'width'  => $image_settings['image_width'],
								                     'height' => $image_settings['image_height'],
								                     'crop'   => true,
								                     'color'  => wpdreams_rgb2hex( $image_settings['image_bg_color'] )
								);
							}

							$r->image = bfi_thumb( $im, $bfi_params );
						} else {
							$r->image = $im;
						}
					}
				}

                // If no image and defined, remove the result here, to perevent JS confusions
                if ( empty($r->image) && $searchData['resultstype'] == "isotopic" && $searchData['i_ifnoimage'] == 'removeres' ) {
                    unset($pageposts[ $k ]);
                    continue;
                }


				if ( ! isset( $searchData['titlefield'] ) || $searchData['titlefield'] == "0" || is_array( $searchData['titlefield'] ) ) {
					$r->title = get_the_title( $r->id );
				} else {
					if ( $searchData['titlefield'] == "1" ) {
						if ( $fn_strlen( $r->excerpt ) >= 200 ) {
							$r->title = wd_substr_at_word( $r->excerpt, 200 );
						} else {
							$r->title = $r->excerpt;
						}
					} else {
						$mykey_values = get_post_custom_values( $searchData['titlefield'], $r->id );
						if ( isset( $mykey_values[0] ) ) {
							$r->title = $mykey_values[0];
						} else {
							$r->title = get_the_title( $r->id );
						}
					}
				}
				$r->title = $this->adv_title( $r->title, $r->id );

				if ( ! isset( $searchData['striptagsexclude'] ) ) {
					$searchData['striptagsexclude'] = "<a><span>";
				}

				//remove the search shortcode properly
				add_shortcode( 'wpdreams_ajaxsearchpro', array( $this, 'return_empty_string' ) );

				if ( ! isset( $searchData['descriptionfield'] ) || $searchData['descriptionfield'] == "0" || is_array( $searchData['descriptionfield'] ) ) {
					if ( function_exists( 'qtrans_getLanguage' ) ) {
						$r->content = apply_filters( 'the_content', $r->content, $searchId );
					}
					$_content = strip_tags( $r->content, $searchData['striptagsexclude'] );
				} else {
					if ( $searchData['descriptionfield'] == "1" ) {
						$_content = strip_tags( $r->excerpt, $searchData['striptagsexclude'] );
					} else if ( $searchData['descriptionfield'] == "2" ) {
						$_content = strip_tags( get_the_title( $r->id ), $searchData['striptagsexclude'] );
					} else {
						$mykey_values = get_post_custom_values( $searchData['descriptionfield'], $r->id );
						if ( isset( $mykey_values[0] ) ) {
							$_content = strip_tags( $mykey_values[0], $searchData['striptagsexclude'] );
						} else {
							$_content = strip_tags( $r->content, $searchData['striptagsexclude'] );
						}
					}
				}
				if ( $_content == "" ) {
					$_content = $r->content;
				}
				if ( isset( $searchData['runshortcode'] ) && $searchData['runshortcode'] == 1 ) {
					if ( $_content != "" ) {
						$_content = apply_filters( 'the_content', $_content, $searchId );
					}
				}
				if ( isset( $searchData['stripshortcode'] ) && $searchData['stripshortcode'] == 1 ) {
					if ( $_content != "" ) {
						$_content = strip_shortcodes( $_content );
					}
				}

				// Remove styles and scripts
				$_content = preg_replace( array(
					'#<script(.*?)>(.*?)</script>#is',
					'#<style(.*?)>(.*?)</style>#is'
				), '', $_content );

				$_content = strip_tags( $_content, $searchData['striptagsexclude'] );

                // Get the words from around the search phrase, or just the description
                if ( w_isset_def($searchData['description_context'], 1) == 1 && count( $_s ) > 0 )
                    $_content = $this->context_find( $_content, $_s[0], floor($searchData['descriptionlength'] / 6), $searchData['descriptionlength'] );
				else if ( $_content != '' && ( $fn_strlen( $_content ) > $searchData['descriptionlength'] ) )
					$_content = wd_substr_at_word( $_content, $searchData['descriptionlength'] ) . "...";

				$_content   = wd_closetags( $_content );
				$r->content = $this->adv_desc( $_content, $r->id );

				// -------------------------- Woocommerce Fixes -----------------------------
				// A trick to fix the url
				if ( $r->post_type == 'product_variation' &&
				     class_exists( 'WC_Product_Variation' )
				) {
					$r->title      = preg_replace( "/(Variation) \#(\d+) (of)/si", '', $r->title );
					$wc_prod_var_o = new WC_Product_Variation( $r->id );
					$r->link       = $wc_prod_var_o->get_permalink();
				}
				// --------------------------------------------------------------------------

                // --------------------------------- DATE -----------------------------------
                if ($searchData["showdate"] == 1) {
                    $post_time = strtotime($r->date);

                    if (w_isset_def($searchData['custom_date'], 0)) {
                        $date_format = w_isset_def($searchData['custom_date_format'], "Y-m-d H:i:s");
                    } else {
                        $date_format = get_option('date_format', "Y-m-d") . " " . get_option('time_format', "H:i:s");
                    }

                    $r->date = @date($date_format, $post_time);
                }
                // --------------------------------------------------------------------------


				$r          = apply_filters( 'asp_result_after_prostproc', $r, $searchId );
				$r->title   = apply_filters( 'asp_result_title_after_prostproc', $r->title, $r->id, $searchId );
				$r->content = apply_filters( 'asp_result_content_after_prostproc', $r->content, $r->id, $searchId );
				$r->image   = apply_filters( 'asp_result_image_after_prostproc', $r->image, $r->id, $searchId );
				$r->author  = apply_filters( 'asp_result_author_after_prostproc', $r->author, $r->id, $searchId );
				$r->date    = apply_filters( 'asp_result_date_after_prostproc', $r->date, $r->id, $searchId );
			}

			aspDebug::stop( '--searchContent-posptrocess' );

			if ( isset( $options['switch_on_preprocess'] ) && is_multisite() ) {
				restore_current_blog();
			}

			/* !Images, title, desc */
			//var_dump($pageposts); die();
			$this->results = $pageposts;

			return $pageposts;

		}

		/**
		 * Pre-groups the results if grouping is selected
		 *
		 * @return array of results
		 */
		protected function group() {

			$pageposts    = $this->results;
			$options      = $this->options;
			$searchData   = $this->searchData;
			$allpageposts = array();
			$s            = $this->s;
			$_s           = $this->_s;

			if ( $options['do_group'] == false ) {
				return;
			}

			// Need a suffix to separate blogs
			if ( isset( $blog ) ) {
				$_key_suff = "_" . $blog;
			} else {
				$_key_suff = "";
			}
			/* Regrouping */
			// By category
			if ( $searchData['groupby'] == 1 && count( $pageposts ) > 0 ) {
				$_pageposts = array();
				foreach ( $pageposts as $k => $v ) {
					if ( $v->ttid == "" || ( $v->post_type != 'post' && $searchData['pageswithcategories'] != 1 ) ) {
						$_pageposts['99999']['data'][] = $v;
						continue;
					}
					$_term_ids = str_replace( '----', ',', $v->ttid );
					$_term_ids = str_replace( '--', '', $_term_ids );
					$_term_ids = explode( ',', $_term_ids );
					if ( count( $_term_ids ) <= 0 ) {
						$_term_ids = array( $v->term_id );
					}
					$cat_count = 0;
					foreach ( $_term_ids as $_term_id ) {

						$cat_count ++;
						if (
							w_isset_def( $searchData['group_exclude_duplicates'], 0 ) == 1 &&
							$cat_count > 1
						) {
							break;
						}

						$cat = get_category( $_term_id );
						if ( is_object( $cat ) && trim( $cat->name ) != "" ) {
							$_pageposts[ $_term_id ]['data'][] = $v;
						}
					}
				}

				foreach ( $_pageposts as $k => $v ) {
					if ( $searchData['showpostnumber'] == 1 ) {
						$num = " (" . count( $_pageposts[ $k ]['data'] ) . ")";
					} else {
						$num = "";
					}
					if ( $k != 99999 ) {
						$cat                      = get_category( $k );
						$_pageposts[ $k ]['name'] = str_replace( '%GROUP%', $cat->name, asp_icl_t( "Group by header text", $searchData['groupbytext'] ) ) . $num;
					} else {
						$_pageposts[ $k ]['name'] = asp_icl_t( "Uncategorized group header", $searchData['uncategorizedtext'] ) . $num;
					}
				}

				$pageposts            = null;
				$pageposts['grouped'] = 1;
				$pageposts['items']   = $_pageposts;
				ksort( $pageposts['items'] );
				if ( $_key_suff != "" ) {
					foreach ( $pageposts['items'] as $k => $v ) {
						$pageposts['items'][ $k . $_key_suff ] = $v;
						unset( $pageposts['items'][ $k ] );
					}
				}
				// By post type
			} else if ( $searchData['groupby'] == 2 && count( $pageposts ) > 0 ) {
				foreach ( $pageposts as $k => $v ) {
					$_pageposts[ $v->post_type ]['data'][] = $v;
				}
				foreach ( $_pageposts as $k => $v ) {
					if ( $searchData['showpostnumber'] == 1 ) {
						$num = " (" . count( $_pageposts[ $k ]['data'] ) . ")";
					} else {
						$num = "";
					}
					$obj                      = get_post_type_object( $k );
					$_pageposts[ $k ]['name'] = str_replace( '%GROUP%', $obj->labels->singular_name, asp_icl_t( "Group by header text", $searchData['groupbytext'] ) ) . $num;
				}
				$pageposts            = null;
				$pageposts['grouped'] = 1;
				$pageposts['items']   = $_pageposts;
				ksort( $pageposts['items'] );
			}

			if ( ( $searchData['groupby'] == 1 || $searchData['groupby'] == 2 ) && count( $pageposts ) > 0 && count( $allpageposts ) > 0 ) {
				$allpageposts['items'] = array_merge( $allpageposts['items'], $pageposts['items'] );
			} else {
				$allpageposts = array_merge( $allpageposts, $pageposts );
			}

			$this->results = $allpageposts;

			return $this->results;

		}

		/**
		 * Fetches an image with the imageCache class
		 */
		protected function getCachedImage( $post, $get_content = false, $get_excerpt = false ) {
			if ( $post->image == null ) {
				$i  = 1;
				$im = "";
				for ( $i == 1; $i < 6; $i ++ ) {
					switch ( $this->imageSettings[ 'image_source' . $i ] ) {
						case "featured":
							$__im = wp_get_attachment_url( get_post_thumbnail_id( $post->id ) );
							if ( $__im != "" ) {
								$img = new wpdreamsImageCache(
									$__im, "post" . $post->id,
									ASP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR,
									$this->imageSettings['image_width'], $this->imageSettings['image_height'],
									- 1, $this->imageSettings['image_bg_color']
								);
								$_im = $img->get_image();
								if ( $_im != '' ) {
									$im = plugins_url( '/ajax-search-pro/cache/' . $_im );
								}
							}
							break;
						case "content":
                            $content = $get_content ? get_post_field('post_content', $post->id) : $post->content;
                            $content = apply_filters('the_content', $content);

							$img = new wpdreamsImageCache(
                                $content, "post" . $post->id,
								ASP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR,
								$this->imageSettings['image_width'], $this->imageSettings['image_height'],
								1, $this->imageSettings['image_bg_color']
							);
							$_im = $img->get_image();
							if ( $_im != '' ) {
								$im = plugins_url( '/ajax-search-pro/cache/' . $_im );
							}
							break;
						case "excerpt":
                            $excerpt = $get_excerpt ? get_post_field('post_excerpt', $post->id) : $post->excerpt;

							$img = new wpdreamsImageCache(
                                $excerpt, "post" . $post->id,
								ASP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR,
								$this->imageSettings['image_width'], $this->imageSettings['image_height'],
								1, $this->imageSettings['image_bg_color']
							);
							$_im = $img->get_image();
							if ( $_im != '' ) {
								$im = plugins_url( '/ajax-search-pro/cache/' . $_im );
							}
							break;
						case "screenshot":
							$im = 'http://s.wordpress.com/mshots/v1/' . urlencode( get_permalink( $post->id ) ) .
							      '?w=' . $this->imageSettings['image_width'] . '&h=' . $this->imageSettings['image_height'];
							break;
                        case "post_format":
                            $format = get_post_format( $post->id );

                            switch ($format) {
                                case "audio":
                                    $im = ASP_URL . "img/post_format/audio.png";
                                    break;
                                case "video":
                                    $im = ASP_URL . "img/post_format/video.png";
                                    break;
                                case "quote":
                                    $im = ASP_URL . "img/post_format/quote.png";
                                    break;
                                case "image":
                                    $im = ASP_URL . "img/post_format/image.png";
                                    break;
                                case "gallery":
                                    $im = ASP_URL . "img/post_format/gallery.png";
                                    break;
                                case "link":
                                    $im = ASP_URL . "img/post_format/link.png";
                                    break;
                                default:
                                    $im = ASP_URL . "img/post_format/default.png";
                                    break;
                            }
                            break;
						case "custom":
							if ( $this->imageSettings['image_custom_field'] != "" ) {
								$val = get_post_meta( $post->id, $this->imageSettings['image_custom_field'], true );
								if ( $val != null && $val != "" ) {
									$im = $val;
								}
							}
							break;
						case "default":
							if ( $this->imageSettings['image_default'] != "" ) {
								$im = $this->imageSettings['image_default'];
							}
							break;
						default:
							$im = "";
							break;
					}
					if ( $im != null && $im != '' ) {
						break;
					}
				}

				return $im;
			} else {
				return $post->image;
			}
		}

		/**
		 * Fetches an image for bfi class
		 */
		protected function getBFIimage( $post, $get_content = false, $get_excerpt = false ) {
			if ( ! isset( $post->image ) || $post->image == null ) {
				$home_url = network_home_url();
				$home_url = home_url();

				if ( ! isset( $post->id ) ) {
					return "";
				}
				$i  = 1;
				$im = "";
				for ( $i == 1; $i < 6; $i ++ ) {
					switch ( $this->imageSettings[ 'image_source' . $i ] ) {
						case "featured":
							$im = wp_get_attachment_url( get_post_thumbnail_id( $post->id ) );
							if ( is_multisite() ) {
								$im = str_replace( home_url(), network_home_url(), $im );
							}
							break;
						case "content":
                            $content = $get_content ? get_post_field('post_content', $post->id) : $post->content;

                            if ( $this->imageSettings['apply_content_filter'] == 1 )
                                $content = apply_filters('the_content', $content);

							$im = wpdreams_get_image_from_content( $content, 1 );
							if ( is_multisite() ) {
								$im = str_replace( home_url(), network_home_url(), $im );
							}
							break;
						case "excerpt":
                            $excerpt = $get_excerpt ? get_post_field('post_excerpt', $post->id) : $post->excerpt;

							$im = wpdreams_get_image_from_content( $excerpt, 1 );
							if ( is_multisite() ) {
								$im = str_replace( home_url(), network_home_url(), $im );
							}
							break;
						case "screenshot":
							$im = 'http://s.wordpress.com/mshots/v1/' . urlencode( get_permalink( $post->id ) ) .
							      '?w=' . $this->imageSettings['image_width'] . '&h=' . $this->imageSettings['image_height'];
							break;
                        case "post_format":
                            $format = get_post_format( $post->id );

                            switch ($format) {
                                case "audio":
                                    $im = ASP_URL . "img/post_format/audio.png";
                                    break;
                                case "video":
                                    $im = ASP_URL . "img/post_format/video.png";
                                    break;
                                case "quote":
                                    $im = ASP_URL . "img/post_format/quote.png";
                                    break;
                                case "image":
                                    $im = ASP_URL . "img/post_format/image.png";
                                    break;
                                case "gallery":
                                    $im = ASP_URL . "img/post_format/gallery.png";
                                    break;
                                case "link":
                                    $im = ASP_URL . "img/post_format/link.png";
                                    break;
                                default:
                                    $im = ASP_URL . "img/post_format/default.png";
                                    break;
                            }
                            break;
						case "custom":
							if ( $this->imageSettings['image_custom_field'] != "" ) {
								$val = get_post_meta( $post->id, $this->imageSettings['image_custom_field'], true );
								if ( $val != null && $val != "" ) {
									$im = $val;
								}
							}
							break;
						case "default":
							if ( $this->imageSettings['image_default'] != "" ) {
								$im = $this->imageSettings['image_default'];
							}
							break;
						default:
							$im = "";
							break;
					}
					if ( $im != null && $im != '' ) {
						break;
					}
				}

				return $im;
			} else {
				return $post->image;
			}
		}

        /**
         * Returns the context of a phrase within a text.
         *
         * @param $str string context
         * @param $needle string context
         * @param $context int length of the context
         * @param $maxlength int maximum length of the string in characters
         * @return string
         */
        function context_find($str, $needle, $context, $maxlength) {
            $haystack = ' '.trim($str).' ';

            // Get the multibyte or regular string functions to variables
            extract($this->mb_f);

            $haystack = $fn_strtolower($haystack);
            $needle = $fn_strtolower($needle);


            if ( $i = $fn_strpos($haystack, $needle) ) {
                $start=$i;
                $end=$i;
                $spaces=0;

                while ($spaces < ((int) $context/2) && $start > 0) {
                    $start--;
                    if ($fn_substr($haystack, $start, 1) == ' ') {
                        $spaces++;
                    }
                }

                while ($spaces < ($context +1) && $end < $fn_strlen($haystack)) {
                    $end++;
                    if ($fn_substr($haystack,$end,1) == ' ') {
                        $spaces++;
                    }
                }

                while ($spaces < ($context +1) && $start > 0) {
                    $start--;
                    if ($fn_substr($haystack, $start, 1) == ' ') {
                        $spaces++;
                    }
                }

                $str_start = ($start - 1) < 0 ? 0 : ($start -1);
                $str_end = ($end - 1) < 0 ? 0 : ($end -1);

                $result = trim($fn_substr($str, $str_start, ($str_end - $str_start)));

                // Somewhere inbetween..
                if ( $start != 0 && $end < $fn_strlen($haystack) )
                    return "..." . $result . "...";

                // Beginning
                if ( $start == 0 && $end < $fn_strlen($haystack) )
                    return $result . "...";

                // End
                if ( $start != 0 && $end == $fn_strlen($haystack) )
                    return "..." . $result;

                // If it is too long, strip it
                if ( $fn_strlen($result) > $maxlength)
                    return wd_substr_at_word( $result, $maxlength ) . "...";

                // Else, it is the whole
                return $result;

            } else {
                // If it is too long, strip it
                if ( $fn_strlen($str) > $maxlength)
                    return wd_substr_at_word( $str, $maxlength ) . "...";

                return $str;
            }
        }


		/**
		 * Generates the post title based on the advanced title field
		 *
		 * @param $title string post title
		 * @param $id int post id
		 *
		 * @return string final post title
		 */
		protected function adv_title( $title, $id ) {

			$titlefield = $this->searchData['advtitlefield'];
			if ( $titlefield == '' ) {
				return $title;
			}
			preg_match_all( "/{(.*?)}/", $titlefield, $matches );
			if ( isset( $matches[0] ) && isset( $matches[1] ) && is_array( $matches[1] ) ) {
				foreach ( $matches[1] as $field ) {
					if ( $field == 'titlefield' ) {
						$titlefield = str_replace( '{titlefield}', $title, $titlefield );
					} else {
						$val        = get_post_meta( $id, $field, true );
						$titlefield = str_replace( '{' . $field . '}', $val, $titlefield );
					}
				}
			}

			return $titlefield;
		}

		/**
		 * Generates the post description based on the advanced description field
		 *
		 * @param $title string post description
		 * @param $id int post id
		 *
		 * @return string final post description
		 */
		protected function adv_desc( $desc, $id ) {
			$descfield = $this->searchData['advdescriptionfield'];
			if ( $descfield == '' ) {
				return $desc;
			}
			preg_match_all( "/{(.*?)}/", $descfield, $matches );
			if ( isset( $matches[0] ) && isset( $matches[1] ) && is_array( $matches[1] ) ) {
				foreach ( $matches[1] as $field ) {
					if ( $field == 'descriptionfield' ) {
						$descfield = str_replace( '{descriptionfield}', $desc, $descfield );
					} else {
						$val       = get_post_meta( $id, $field, true );
						$descfield = str_replace( '{' . $field . '}', $val, $descfield );
					}
				}
			}

			return $descfield;
		}

		/**
		 * An empty function to override individual shortcodes, this must be public
		 *
		 * @return string
		 */
		public function return_empty_string() {
			return "";
		}
	}
}