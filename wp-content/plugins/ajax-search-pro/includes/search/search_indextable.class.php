<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('asp_searchIndexTable')) {
	/**
	 * Index table search class
	 *
	 * @class       asp_searchIndexTable
	 * @version     1.0
	 * @package     AjaxSearchPro/Classes
	 * @category    Class
	 * @author      Ernest Marcinko
	 */
	class asp_searchIndexTable extends wpdreams_searchContent {

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

		/**
		 * @var array results from the index table
		 */
		protected $raw_results = array();

		/**
		 * Content search function
		 *
		 * @return array|string
		 */
		protected function do_search() {
			global $wpdb;
			global $q_config;

			$options = $this->options;
			$comp_options = get_option('asp_compatibility');
			$searchId = $this->searchId;
			$searchData = $this->searchData;
			$current_blog_id = get_current_blog_id();

			// General variables
			$parts = array();
			$relevance_parts = array();
			$types = array();
			$post_types = "(1)";
			$term_query = "(1)";
			$post_statuses = "(1)";

			$kw_logic = w_isset_def($searchData['keyword_logic'], 'or');
			$q_config['language'] = $options['qtranslate_lang'];

			$s = $this->s; // full keyword
			$_s = $this->_s; // array of keywords

            $sr = $this->sr; // full reversed keyword
            $_sr = $this->_sr; // array of reversed keywords


			if (isset($wpdb->base_prefix)) {
				$_prefix = $wpdb->base_prefix;
			} else {
				$_prefix = $wpdb->prefix;
			}


			/**
			 * Determine if the priorities table should be used or not.
			 */
			$priority_rows = (int)$wpdb->get_var("SELECT COUNT(*) FROM " . $_prefix . "ajaxsearchpro_priorities");
			$priority_select = $priority_rows > 0 ? "
	        IFNULL((
            	SELECT
	            aspp.priority
	            FROM " . $_prefix . "ajaxsearchpro_priorities as aspp
	            WHERE aspp.post_id = asp_index.doc AND aspp.blog_id = " . get_current_blog_id() . "
            ), 100)
	        " : 100;


			/*------------------------- Statuses ----------------------------*/
			// Removed - it is already considered at index generation
			/*---------------------------------------------------------------*/

			/*----------------------- Gather Types --------------------------*/
			$page_q = "";
			if ($options['set_inposts'] == 1)
				$types[] = "post";
			if ($options['set_inpages']) {
				if (w_isset_def($searchData['exclude_page_parent_child'], '') != '')
					// .. page exists thats parent isnt on the list
					$page_q = " OR (asp_index.post_type = 'page' AND
						EXISTS (SELECT ID FROM $wpdb->posts xxp WHERE
							xxp.ID = asp_index.doc AND
							xxp.post_parent NOT IN (".str_replace('|', ',', $searchData['exclude_page_parent_child']).") AND
							xxp.ID NOT IN (".str_replace('|', ',', $searchData['exclude_page_parent_child']).")
						)
					)";
				else
					$types[] = "page";
			}
			if (isset($options['customset']) && count($options['customset']) > 0)
				$types = array_merge($types, $options['customset']);

			// If no post types selected, well then return
			if (count($types) < 1 && $page_q == "") {
				return '';
			} else {
				$words = implode("','", $types);
				$post_types = "(asp_index.post_type IN ('$words') $page_q)";
			}
			/*---------------------------------------------------------------*/


			// ------------------------ Categories/taxonomies ----------------------
			if (!isset($options['categoryset']) || $options['categoryset'] == "")
				$options['categoryset'] = array();
			if (!isset($options['termset']) || $options['termset'] == "")
				$options['termset'] = array();

			/*
				By default it's 'AND', so all the categories must fit in order to show
				that result.
			*/
			$term_logic = w_isset_def($searchData['term_logic'], 'and');

			$exclude_categories = array();
			$searchData['selected-exsearchincategories'] = w_isset_def($searchData['selected-exsearchincategories'], array());
			$searchData['selected-excludecategories'] = w_isset_def($searchData['selected-excludecategories'], array());

			if (count($searchData['selected-exsearchincategories']) > 0 ||
			    count($searchData['selected-excludecategories']) > 0 ||
			    count($options['categoryset']) > 0 ||
			    $searchData['showsearchincategories'] == 1
			) {

				aspDebug::start('--searchContent-categories');

				// If the category settings are invisible, ignore the excluded frontend categories, reset to empty array
				if ($searchData['showsearchincategories'] == 0)
					$searchData['selected-exsearchincategories'] = array();

				$_all_cat = get_terms('category', array('fields' => 'ids'));
				$_needed_cat = array_diff($_all_cat, $searchData['selected-exsearchincategories']);
				$_needed_cat = !is_array($_needed_cat) ? array() : $_needed_cat;

				// I am pretty sure this is where the devil is born
				/*
					AND -> Posts NOT in an array of term ids
					OR  -> Posts in an array of term ids
				  */
				if ($term_logic == 'and') {
					if ($searchData['showsearchincategories'] == 1)
						// If the settings is visible, count for the options
						$exclude_categories = array_diff(array_merge($_needed_cat, $searchData['selected-excludecategories']), $options['categoryset']);
					else
						// ..if the settings is not visible, then only the excluded categories count
						$exclude_categories = $searchData['selected-excludecategories'];
				} else {
					if ($searchData['showsearchincategories'] == 1) {
						// If the settings is visible, check which is selected
						$exclude_categories = count($options['categoryset']) == 0 ? array(-10) : $options['categoryset'];
					} else {
						// .. otherwise this thing here
						$exclude_categories = array_diff($_needed_cat, $searchData['selected-excludecategories']);
						$exclude_categories = count($exclude_categories) == 0 ? array(-10) : $exclude_categories;
					}
				}

				// If every category is selected, then we don't need to filter anything out.
				/*if (count($exclude_categories) == count($_all_cat))
					$exclude_categories = array();  */

				aspDebug::stop('--searchContent-categories');
			}

			$exclude_terms = array();
			$exclude_showterms = array();
			$searchData['selected-showterms'] = w_isset_def($searchData['selected-showterms'], array());
			$searchData['selected-excludeterms'] = w_isset_def($searchData['selected-excludeterms'], array());

			if (count($searchData['selected-showterms']) > 0 ||
			    count($searchData['selected-excludeterms']) > 0 ||
			    count($options['termset']) > 0
			) {

				aspDebug::start('--searchContent-terms');

				foreach ($searchData['selected-excludeterms'] as $tax => $terms) {
					$exclude_terms = array_merge($exclude_terms, $terms);
				}
				// If the term settings are invisible, ignore the excluded frontend terms, reset to empty array
				if ($searchData['showsearchintaxonomies'] == 0)
					$searchData['selected-showterms'] = array();
				foreach ($searchData['selected-showterms'] as $tax => $terms) {
					$exclude_showterms = array_merge($exclude_showterms, $terms);
				}

				/*if ($term_logic == 'and')
					$exclude_terms = array_diff(array_merge($exclude_terms, $exclude_showterms), $options['termset']);
				else
					$exclude_terms = count($options['termset']) == 0 ? array(-10) : $options['termset'];  */

				aspDebug::stop('--searchContent-terms');

				/*
					AND -> Posts NOT in an array of term ids
					OR  -> Posts in an array of term ids
				  */
				if ($term_logic == 'and') {
					if ($searchData['showsearchintaxonomies'] == 1)
						// If the settings is visible, count for the options
						$exclude_terms = array_diff(array_merge($exclude_terms, $exclude_showterms), $options['termset']);
					else
						// ..if the settings is not visible, then only the excluded categories count
						$exclude_terms = $exclude_terms;
				} else {
					if ($searchData['showsearchintaxonomies'] == 1) {
						// If the settings is visible, check which is selected
						$exclude_terms = count($options['termset']) == 0 ? array(-10) : $options['termset'];
					} else {
						// .. otherwise we bail out, and exclude everything. NOT SOLVED!
						// But here we would need all term IDs, which is not an option
						$exclude_terms = array(-15);
					}
				}

			}

			$all_terms = array();
			$all_terms = array_merge($exclude_categories, $exclude_terms);
			//var_dump($all_terms);

			/**
			 *  New method since ASP 4.1
			 *
			 *  This is way more efficient, despite it looks more complicated.
			 *  Multiple sub-select is not an issue, since the query can use PRIMARY keys as indexes
			 */
			if (count($all_terms) > 0) {
				$words = implode(',', $all_terms);

				// Quick explanation for the AND
				// .. MAIN SELECT: selects all object_ids that are not in the array
				// .. SUBSELECT:   excludes all the object_ids that are part of the array
				// This is used because of multiple object_ids (posts in more than 1 category)
				if ($term_logic == 'and')
					$term_query = "(
              NOT EXISTS (SELECT * FROM $wpdb->term_relationships as xt WHERE xt.object_id = asp_index.doc)
              OR
						asp_index.doc IN (
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
				else
					$term_query = "(
                NOT EXISTS (SELECT * FROM $wpdb->term_relationships as xt WHERE xt.object_id = asp_index.doc)
                OR
                asp_index.doc IN ( SELECT DISTINCT(tr.object_id)
			            FROM $wpdb->term_relationships AS tr
			            LEFT JOIN $wpdb->term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			            WHERE tt.term_id IN ($words)
			          ) )";
			}

			// ---------------------------------------------------------------------


			/*------------ ttids in the main SELECT, we might not need it ---------*/
			// ttid is only used if grouping by category or filtering by category is active
			$term_select = '""';
			if ($searchData['groupby'] == 1 || count($all_terms) > 0) {
				$term_select = "(SELECT DISTINCT CONCAT('--', GROUP_CONCAT( $wpdb->term_relationships.term_taxonomy_id SEPARATOR '----' ), '--')
                FROM $wpdb->term_relationships
                WHERE ($wpdb->term_relationships.object_id = asp_index.doc) )";
			}
			// ---------------------------------------------------------------------


			/*------------- Custom Fields with Custom selectors -------------*/
			if (isset($options['aspf']) && isset($options['aspfdata'])) {

				aspDebug::start('--searchContent-cf');

				$parts = array();

				foreach ($options['aspfdata'] as $u_data) {
					$data = json_decode(base64_decode($u_data));
					$posted = $this->escape( $options['aspf'][$data->asp_f_field] );

					$ll_like = "";
					$rr_like = "";

					if (isset($data->asp_f_operator)) {
						switch ($data->asp_f_operator) {
							case 'eq':
								$operator = "=";
								$posted = $this->force_numeric( $posted );
								break;
							case 'neq':
								$operator = "<>";
								$posted = $this->force_numeric( $posted );
								break;
							case 'lt':
								$operator = "<";
								$posted = $this->force_numeric( $posted );
								break;
							case 'gt':
								$operator = ">";
								$posted = $this->force_numeric( $posted );
								break;
							case 'elike':
								$operator = "=";
								$ll_like = "'";
								$rr_like = "'";
								break;
							case 'like':
								$operator = "LIKE";
								$ll_like = "'%";
								$rr_like = "%'";
								break;
							default:
								$operator = "=";
								$posted = $this->force_numeric( $posted );
								break;
						}
					}

					if ($data->asp_f_type == 'range' && isset($posted['lower'])) {
						$posted = $this->force_numeric( $posted );
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value BETWEEN " . $posted['lower'] . " AND " . $posted['upper'] . " ) )";
					} else if ($data->asp_f_type == 'slider' && isset($posted)) {
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator $posted  ) )";
					} else if ( ($data->asp_f_type == 'radio' || $data->asp_f_type == 'hidden' || $data->asp_f_type == 'text' ) && isset($posted)) {
						$parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator " . $ll_like . $posted . $rr_like . " ) )";
					} else if ($data->asp_f_type == 'dropdown' && isset($posted)) {
						if (w_isset_def($data->asp_f_dropdown_multi, 'asp_unchecked') == 'asp_checked' && count($posted) > 0) {
							// The AND logic doesn't make any sense
							$logic = 'OR';
							$values = '';
							foreach ($posted as $v) {
								if ($values != '')
									$values .= " $logic $wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
								else
									$values .= "$wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
							}

							$values = $values == '' ? '0' : $values;
							$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";
						} else {
							$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                        ($wpdb->postmeta.meta_value $operator " . $ll_like . $posted . $rr_like . " ) )";
						}

					} else if ($data->asp_f_type == 'checkboxes' && isset($posted)) {

						$logic = $data->asp_f_checkboxes_logic;
						$values = '';
						foreach ($posted as $v => $vv) {
							if ($values != '')
								$values .= " $logic $wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
							else
								$values .= "$wpdb->postmeta.meta_value $operator " . $ll_like . $v . $rr_like;
						}
						$values = $values == '' ? '0' : $values;
						$parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";

					}

				}

				$this->cf_parts = $parts;

				aspDebug::stop('--searchContent-cf');
			}

			$meta_count = count($this->cf_parts);

			$cf_query = implode(" OR ", $this->cf_parts);
			if ($cf_query == "") {
				$cf_select = "(1)";
				//$cf_having = "";
			} else {
				if (w_isset_def($searchData['cf_logic'], 'AND') == 'AND')
					$cf_count = $meta_count;
				else
					$cf_count = 1;

				/**
				 * Far effective method for custom fields, bypassing the HAVING
				 */
				$cf_select = "
				( (
	                SELECT COUNT(*) FROM $wpdb->postmeta WHERE
	                    $wpdb->postmeta.post_id = asp_index.doc
	                AND
	                    ($cf_query)
                ) >= $cf_count )";
			}
			/*---------------------------------------------------------------*/


			/*------------------------ Exclude id's -------------------------*/
			if (isset($searchData['excludeposts']) && $searchData['excludeposts'] != "") {
				$exclude_posts = "(asp_index.doc NOT IN (" . $searchData['excludeposts'] . "))";
			} else {
				$exclude_posts = "(1)";
			}
			/*---------------------------------------------------------------*/

			/*------------------------ Term JOIN -------------------------*/
			// No need, this should be indexed...
			/*---------------------------------------------------------------*/

            /*------------------------- WPML filter -------------------------*/
            $wpml_query = "(1)";
            if ( isset($options['wpml_lang'])
                && $options['wpml_lang'] != ""
                && w_isset_def($searchData['wpml_compatibility'], 1) == 1
            ) {
                global $sitepress;
                $site_lang_selected = false;

                if ( is_object($sitepress) && method_exists($sitepress, 'get_default_language') ) {
                    $site_lang_selected = $sitepress->get_default_language() == $options['wpml_lang'] ? true : false;
                }

                $wpml_query = "asp_index.lang = '" . $this->escape($options['wpml_lang']) . "'";

                /**
                 * Imported or some custom post types might have missing translations for the site default language.
                 * If the user currently searches on the default language, empty translation string is allowed.
                 */
                if ($site_lang_selected)
                    $wpml_query .= " OR asp_index.lang = ''";
            }

            /*---------------------------------------------------------------*/

			/*----------------------- Optimal LIMIT -------------------------*/
            // If custom field filtering is enabled, we need to be careful with the limiting
            if ( count($this->cf_parts) > 0) {
                $limit = floor( $searchData['maxresults'] );
            } else {
                /*$limit = count(get_option('asp_posts_indexed')) / 2;
                $limit = $limit < 200 ? 200 : $limit;
                $limit = $limit > 2000 ? 2000 : $limit;
                $limit = ($limit * 5) < $searchData['maxresults'] ? ($limit * 5) : $limit;
                $limit = floor( $limit );*/

                /**
                 * Instead of the flippy-floppy calculations, just set this to 5k
                 * This is the pool size for each result set. It is important when using
                 * AND and similar logics.
                 * DO NOT set it too high, as PHP might run out of memory.
                 */
                $limit = 5000;
            }
			/*---------------------------------------------------------------*/

			/*---------------------- Blog switching? ------------------------*/
			if (isset($options['switch_on_preprocess']))
				$blog_query = "asp_index.blogid IN (".implode(',', $searchData['selected-blogs']) . ")";
			else
				$blog_query = "asp_index.blogid = ".$current_blog_id;
			/*---------------------------------------------------------------*/

            /*---------------------- Relevance Values -----------------------*/
            $rel_val_title = w_isset_def($searchData["it_title_weight"], 100);
            $rel_val_content = w_isset_def($searchData["it_content_weight"], 20);
            $rel_val_excerpt = w_isset_def($searchData["it_excerpt_weight"], 10);
            $rel_val_terms = w_isset_def($searchData["it_terms_weight"], 10);
            $rel_val_cf = w_isset_def($searchData["it_cf_weight"], 8);
            $rel_val_author = w_isset_def($searchData["it_author_weight"], 8);
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

			$this->query = "
    		SELECT 
            asp_index.doc as id,
            asp_index.blogid as blogid,
            'pagepost' as content_type,
            $term_select as ttid,
            $priority_select AS priority,
            asp_index.post_type AS post_type,
            (
	             asp_index.title * $rel_val_title * {rmod} +
	             asp_index.content * $rel_val_content * {rmod}  +
	             asp_index.excerpt * $rel_val_excerpt * {rmod}  +
	             asp_index.comment * $rel_val_terms * {rmod}  +
	             asp_index.tag * $rel_val_terms * {rmod}  +
	             asp_index.customfield * $rel_val_cf * {rmod}  +
	             asp_index.author * $rel_val_author * {rmod}
            )as relevance
            FROM ".$_prefix."asp_index as asp_index
            WHERE
                    {like_query}
                AND $post_types
                AND $blog_query
				AND ( $wpml_query )
                AND $term_query
                AND $cf_select
                AND $post_statuses
                AND $exclude_posts
            LIMIT $limit";

			$queries = array();
			$results_arr = array();

			//$words = $options['set_exactonly'] == 1 ? array($s) : $_s;
            $words = $_s;

			if ($kw_logic == "orex") {
                $rmod = 1;
				$like_query = "asp_index.term = '" . implode( "' OR asp_index.term = '", $words ) . "'";
                $queries[]  = str_replace( array('{like_query}', '{rmod}'), array($like_query, $rmod), $this->query );
			} else if ($kw_logic == "andex") {
				foreach ( $words as $wk => $word ) {
                    $rmod = 10 - ( $wk * 8 ) < 1 ? 1 : 10 - ( $wk * 8 );

					$like_query = "asp_index.term = '$word'";
                    $queries[]  = str_replace( array('{like_query}', '{rmod}'), array($like_query, $rmod), $this->query );
				}
			} else {
				foreach ( $words as $wk => $word ) {
                    $rmod = 10 - ( $wk * 8 ) < 1 ? 1 : 10 - ( $wk * 8 );

					$like_query = "asp_index.term LIKE '".$word."%'";
					$queries[]  = str_replace( array('{like_query}', '{rmod}'), array($like_query, $rmod), $this->query );

					$like_query = "asp_index.term_reverse LIKE '". (isset($_sr[$wk]) ? $_sr[$wk] : $sr ) ."%'";
					$queries[]  = str_replace( array('{like_query}', '{rmod}'), array($like_query, $rmod), $this->query );
				}
			}

			if (count($queries) > 0) {
				foreach ($queries as $query) {
					$results_arr[] = $wpdb->get_results($query);
				}
			}

			// Merge results depending on the logic
			$results_arr = $this->merge_raw_results($results_arr, $kw_logic);

			// We need to save this array with keys, will need the values later.
			$this->raw_results = $results_arr;

			// Sort results by priority > relevance
			usort($results_arr, array($this, 'compare_by_pr'));

			// Leave only the the LIMIT amount, 500 for non-ajax cases
			if ( isset($options['non_ajax_search']) )
				$results_arr = array_slice($results_arr, 0, 500, true);
			else
				$results_arr = array_slice($results_arr, 0, $searchData['maxresults'], true);

			$this->results = $results_arr;

			// Do some pre-processing
			$this->pre_process_results();

			return $this->results;

		}


		/**
		 * Merges the initial results array, creating an union or intersection.
		 *
		 * The function also adds up the relevance values of the results object.
		 *
		 * @param array $results_arr
		 * @param bool $kw_logic keyword logic (and, or, andex, orex)
		 * @return array results array
		 */
		protected function merge_raw_results($results_arr, $kw_logic = "or") {

			/*
			 * When using the "and" logic, the $results_arr contains the results in [term, term_reverse]
			 * results format. These should not be intersected with each other, so this small code
			 * snippet here divides the results array by groups of 2, then it merges ever pair to one result.
			 * This way it turns into [term1, term1_reverse, term2 ...]  array to [term1 union term1_reversed, ...]
			 *
			 * This is only neccessary with the "and" logic. Others work fine.
			 */
			if ($kw_logic == 'and') {
				$new_ra = array();
				$i = 0;
				$tmp_v = array();
				foreach ($results_arr as $_k => $_v) {
					if ($i & 1){
						// odd, so merge the previous with the current
						$new_ra[] = array_merge($tmp_v, $_v);
					}
					$tmp_v = $_v;
					$i++;
				}
				$results_arr = $new_ra;
			}

			$final_results = array();
			foreach ($results_arr as $results) {
				foreach ($results as $k => $r) {
					if ( isset( $final_results[ $r->blogid . "x" . $r->id ] ) ) {
						$final_results[ $r->blogid . "x" . $r->id ]->relevance += $r->relevance;
					} else {
						$final_results[ $r->blogid . "x" . $r->id ] = $r;
					}
				}
			}

			if ($kw_logic == 'or' || $kw_logic == 'orex')
				return $final_results;

			foreach ($results_arr as $results) {
				$final_results = array_uintersect($final_results, $results, array($this, 'compare_results'));
			}

			return $final_results;
		}


		/**
		 * A custom comparison function for results intersection
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return mixed
		 */
		protected function compare_results($a, $b) {
			if ($a->blogid === $b->blogid)
				return $b->id - $a->id;
			// For different blogids return difference
			return $b->blogid - $a->blogid;
		}

		/**
		 * usort() custom function, sort by ID
		 *
		 * @param $obj_a
		 * @param $obj_b
		 * @return mixed
		 */
		protected function compare_posts($obj_a, $obj_b) {
			return $obj_a->id - $obj_b->id;
		}

		/**
		 * usort() custom function, sort by priority > relevance
		 *
		 * @param $a
		 * @param $b
		 * @return int
		 */
		protected function compare_by_pr($a, $b) {
			if ($a->priority === $b->priority)
				return $b->relevance - $a->relevance;
			return $b->priority - $a->priority;
		}

		private function pre_process_results() {
			// No results, save some resources
			if (count($this->results) == 0)
				return array();

			$pageposts = array();
			$options = $this->options;
			$searchId = $this->searchId;
			$searchData = $this->searchData;
			$post_ids = array();
			//$orderby = w_isset_def($searchData['orderby'], "post_date DESC");
			$the_posts = array();

			// Get the post IDs
			foreach ($this->results as $k => $v) {
				$post_ids[$v->blogid][] = $v->id;
			}

			foreach ( $post_ids as $blogid => $the_ids ) {
				if (isset($options['switch_on_preprocess']) && is_multisite())
					switch_to_blog($blogid);
				$args      = array(
					'post__in'       => $the_ids,
					'order_by'       => 'post__in',
					'posts_per_page' => 1000000,
					'post_type'      => 'any'
				);

				$get_posts = get_posts( $args );
				foreach ($get_posts as $gk=>$gv)
					$get_posts[$gk]->blogid = $blogid;
				$the_posts = array_merge( $the_posts, $get_posts );
			}

			if (isset($options['switch_on_preprocess']) && is_multisite())
				restore_current_blog();


			// Merge the posts with the raw results to a new array
			foreach ($the_posts as $k => $r) {
				$new_result = new stdClass();

				$new_result->id = w_isset_def($r->ID, null);
				$new_result->blogid = $r->blogid;
				$new_result->title = w_isset_def($r->post_title, null);
                $new_result->post_title = $new_result->title;
				$new_result->content = w_isset_def($r->post_content, null);
				$new_result->excerpt = w_isset_def($r->post_excerpt, null);
				$new_result->image = null;


                if ($searchData['showauthor'] == 1) {
                    $post_user = get_user_by("id", $r->post_author);

                    if ( $post_user !== false ) {
                        if ( w_isset_def( $searchData['author_field'], 'display_name' ) == "display_name" )
                            $new_result->author = $post_user->data->display_name;
                        else
                            $new_result->author = $post_user->data->user_login;
                    } else {
                        $new_result->author = null;
                    }
                }


				$new_result->date = w_isset_def($r->post_date, null);
                $new_result->post_date = $new_result->date;

				// Get the relevance and priority values
				$new_result->relevance = (int)$this->raw_results[$new_result->blogid . "x" . $new_result->id]->relevance;
				$new_result->priority = (int)$this->raw_results[$new_result->blogid . "x" . $new_result->id]->priority;
				$new_result->post_type = $this->raw_results[$new_result->blogid . "x" . $new_result->id]->post_type;
				$new_result->content_type = "pagepost";
				$new_result->ttid = $this->raw_results[$new_result->blogid . "x" . $new_result->id]->ttid;

				$pageposts[] = $new_result;
			}

			// Order them once again
			if (count($pageposts) > 0) {

                usort( $pageposts, array( $this, 'compare_by_primary' ) );

                /**
                 * Let us save some time. There is going to be a user selecting the same sorting
                 * for both primary and secondary. Only do secondary if it is different from the primary.
                 */
                if ( $this->ordering['primary'] != $this->ordering['secondary'] ) {
                    $i = 0;
                    foreach ($pageposts as $pk => $pp) {
                        $pageposts[$pk]->primary_order = $i;
                        $i++;
                    }
                    usort( $pageposts, array( $this, 'compare_by_secondary' ) );
                }
			}

			$this->results = $pageposts;
		}
	}
}