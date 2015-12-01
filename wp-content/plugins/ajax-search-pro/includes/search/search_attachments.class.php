<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchAttachments')) {
	/**
	 * Attachment search
	 *
	 * @class       wpdreams_searchAttachments
	 * @version     1.0
	 * @package     AjaxSearchPro/Classes
	 * @category    Class
	 * @author      Ernest Marcinko
	 */
	class wpdreams_searchAttachments extends wpdreams_searchContent {

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

			// General variables
			$parts = array();
			$relevance_parts = array();
			$types = array();
			$post_types = "(1)";
			$post_statuses = "(1)";
			$all_pageposts = array();
			$postmeta_join = "";

			// Prefixes and suffixes
			$pre_field = '';
			$suf_field = '';
			$pre_like = '';
			$suf_like = '';

			/**
			 *  On forced case sensitivity: Let's add BINARY keyword before the LIKE
			 *  On forced case in-sensitivity: Append the lower() function around each field
			 */
			if (w_isset_def($comp_options['db_force_case'], 'none') == 'sensitivity') {
				$pre_like = 'BINARY ';
			} else if (w_isset_def($comp_options['db_force_case'], 'none') == 'insensitivity') {
				if (function_exists('mb_convert_case'))
					$this->s = mb_convert_case($this->s, MB_CASE_LOWER, "UTF-8");
				else
					$this->s = strtoupper($this->s); // if no mb_ functions :(
				$this->_s = array_unique( explode(" ", $this->s) );

				$pre_field .= 'lower(';
				$suf_field .= ')';
			}

			/**
			 *  Check if utf8 is forced on LIKE
			 */
			if (w_isset_def($comp_options['db_force_utf8_like'], 0) == 1) {
				$pre_like .= '_utf8';
			}

			/**
			 *  Check if unicode is forced on LIKE, but only apply if utf8 is not
			 */
			if (w_isset_def($comp_options['db_force_unicode'], 0) == 1
			    && w_isset_def($comp_options['db_force_utf8_like'], 0) == 0) {
				$pre_like .= 'N';
			}


			$kw_logic = w_isset_def($searchData['keyword_logic'], 'or');
			$q_config['language'] = $options['qtranslate_lang'];

			$s = $this->s; // full keyword
			$_s = $this->_s; // array of keywords


			$this->remaining_limit = $searchData['maxresults'];


			/*------------------------- Statuses ----------------------------*/
			// Attachments are inherit only
			$post_statuses = "(".$pre_field.$wpdb->posts.".post_status".$suf_field." = 'inherit' )";
			/*---------------------------------------------------------------*/

			/*----------------------- Gather Types --------------------------*/
			$post_types = "($wpdb->posts.post_type = 'attachment' )";
			/*---------------------------------------------------------------*/

			/*------------------------- Mime Types --------------------------*/
			$mime_types = "(1)";
			if (w_isset_def($searchData['attachment_mime_types'], "") != "") {
				$mimes_arr = explode(",", base64_decode($searchData['attachment_mime_types']));
				foreach ($mimes_arr as $k => $v) {
					$mimes_arr[$k] = trim($v);
				}
				$mime_types = "( $wpdb->posts.post_mime_type IN ('" . implode("','", $mimes_arr). "') )";
			}
			/*---------------------------------------------------------------*/

			/*------------------------ Exclude id's -------------------------*/
			if (w_isset_def($searchData['attachment_exclude'], "") != "") {
				$exclude_posts = "($wpdb->posts.ID NOT IN (" . $searchData['attachment_exclude'] . "))";
			} else {
				$exclude_posts = "(1)";
			}
			/*---------------------------------------------------------------*/


            /*------------------------ Term JOIN -------------------------*/
            // If the search in terms is not active, we don't need this unnecessary big join
            $term_join = "";
            if ( w_isset_def($searchData['search_attachments_terms'], 0) == 1 ) {
                $term_join = "
                LEFT JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
                LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
                LEFT JOIN $wpdb->terms ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id";
            }
            /*---------------------------------------------------------------*/


			/**
			 * This is the main query.
			 *
			 * The ttid field is a bit tricky as the term_taxonomy_id doesn't always equal term_id,
			 * so we need the LEFT JOINS :(
			 */
			$orderby = w_isset_def($searchData['orderby'], "post_date DESC");
			$this->query = "
    		SELECT
    		DISTINCT($wpdb->posts.ID) as id,
            $wpdb->posts.post_title as title,
            $wpdb->posts.post_date as date,
            $wpdb->posts.post_content as content,
            $wpdb->posts.post_excerpt as excerpt,
            $wpdb->posts.post_type as post_type,
            $wpdb->posts.post_mime_type as post_mime_type,
            $wpdb->posts.guid as guid,
            'pagepost' as content_type,
            (SELECT
                $wpdb->users." . w_isset_def($searchData['author_field'], 'display_name') . " as author
                FROM $wpdb->users
                WHERE $wpdb->users.ID = $wpdb->posts.post_author
            ) as author,
            '' as ttid,
            $wpdb->posts.post_type as post_type,
            100 AS priority,
            {relevance_query} as relevance
            FROM $wpdb->posts
            $term_join
            WHERE
                    $post_types
                AND $post_statuses
                AND {like_query}
                AND $exclude_posts
                AND $mime_types
            ORDER BY priority DESC, relevance DESC, " . $wpdb->posts . "." . $orderby . "
            LIMIT {remaining_limit}";


			$words = $options['set_exactonly'] == 1 ? array($s) : $_s;

			/*----------------------- Title query ---------------------------*/
			if ($searchData['search_attachments_title']) {
				$parts = array();
				$relevance_parts = array();

				if ($kw_logic == 'or' || $kw_logic == 'and') {
					$op = strtoupper($kw_logic);
					if (count($_s) > 0)
						$_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE $pre_like'%", $words);
					else
						$_like = $s;
					$parts[] = "( ".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ($words as $word) {
						$_like[] = "
                           ( ".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_title".$suf_field." = '" . $word . "')";
					}
					$parts[] = "(" . implode(' ' . $op . ' ', $_like) . ")";
				}

				/*$relevance_parts[] = "(case when
				(".$pre_field.$wpdb->posts.".post_title".$suf_field." REGEXP '$regexp_words')
				 then $searchData[titleweight] else 0 end)";*/
				$relevance_parts[] = "(case when
                (".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE '%$s%')
                 then $searchData[etitleweight] else 0 end)";

				// The first word relevance is higher
				if (count($_s) > 0)
					$relevance_parts[] = "(case when
                  (".$pre_field.$wpdb->posts.".post_title".$suf_field." LIKE '%" . $_s[0] . "%')
                   then $searchData[etitleweight] else 0 end)";

				$this->parts[] = array($parts, $relevance_parts);
			}
			/*---------------------------------------------------------------*/

			/*---------------------- Content query --------------------------*/
			if ($searchData['search_attachments_content']) {
				$parts = array();
				$relevance_parts = array();

				if ($kw_logic == 'or' || $kw_logic == 'and') {
					$op = strtoupper($kw_logic);
					if (count($_s) > 0)
						$_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE $pre_like'%", $words);
					else
						$_like = $s;
					$parts[] = "( ".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like )";
				} else {
					$_like = array();
					$op = $kw_logic == 'andex' ? 'AND' : 'OR';
					foreach ($words as $word) {
						$_like[] = "
                           (".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_content".$suf_field." = '" . $word . "')";
					}
					$parts[] = "(" . implode(' ' . $op . ' ', $_like) . ")";
				}

				if (count($_s) > 0)
					$relevance_parts[] = "(case when
                    (".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE '%".$_s[0]."%')
                     then $searchData[contentweight] else 0 end)";
				$relevance_parts[] = "(case when
                (".$pre_field.$wpdb->posts.".post_content".$suf_field." LIKE '%$s%')
                 then $searchData[econtentweight] else 0 end)";

				$this->parts[] = array($parts, $relevance_parts);
			}
			/*---------------------------------------------------------------*/

            /*------------------- Caption/Excerpt query ---------------------*/
            if ( w_isset_def($searchData['search_attachments_caption'], 1) ) {
                $parts = array();
                $relevance_parts = array();

                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( ".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                           (".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                        OR  ".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "(" . implode(' ' . $op . ' ', $_like) . ")";
                }

                if (count($_s) > 0)
                    $relevance_parts[] = "(case when
                    (".$pre_field.$wpdb->posts.".post_excerpt".$suf_field." LIKE '%".$_s[0]."%')
                     then $searchData[contentweight] else 0 end)";

                $this->parts[] = array($parts, $relevance_parts);
            }
            /*---------------------------------------------------------------*/

            /*------------------------ Term query ---------------------------*/
            if ( w_isset_def($searchData['search_attachments_terms'], 0) ) {
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

                $this->parts[] = array( $parts, $relevance_parts );
            }
            /*---------------------------------------------------------------*/


			$querystr =  $this->build_query($this->parts, true);
			//var_dump($querystr); //die("!!");
			$attachments = $wpdb->get_results($querystr, OBJECT);

			if (count($attachments) > 0) {
				if (w_isset_def($searchData['userelevance'], 1) == 1) {
					usort($attachments, array($this, 'compare_by_rp'));
				} else if ($orderby == 'post_date DESC') {
					usort($attachments, array($this, 'compare_by_rd_desc'));
				} else if ($orderby == 'post_date ASC') {
					usort($attachments, array($this, 'compare_by_rd_asc'));
				} else if ($orderby == 'post_title DESC') {
					usort($attachments, array($this, 'compare_by_title_desc'));
				} else {
					usort($attachments, array($this, 'compare_by_title_asc'));
				}
			}

			$this->results = $attachments;

			return $attachments;

		}

		protected function post_process() {
			parent::post_process();

			$searchId = $this->searchId;
			$searchData = $this->searchData;

			if (w_isset_def($searchData['attachment_use_image'], 1) == 1)
				foreach ($this->results as $k => $r) {
					if (strpos($r->post_mime_type, 'image/') !== false && $r->guid != "")
					$this->results[$k]->image = $r->guid;
				}

			return $this->results;
		}

		protected function group() {
			return $this->results;
		}
	}
}