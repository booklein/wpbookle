<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchTerms')) {
    /**
     * Search class wpdreams_searchTerms
     *
     * Term search class
     *
     * @class       wpdreams_search
     * @version     1.1
     * @package     AjaxSearchPro/Classes
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchTerms extends wpdreams_search {

        /**
         * The search function
         *
         * @return array of results
         */
        protected function do_search() {
            global $wpdb;
            global $q_config;

            $options = $this->options;
            $comp_options = get_option('asp_compatibility');
            $searchData = $this->searchData;

            $parts = array();
            $relevance_parts = array();
            $types = array();

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
                $this->_s = explode(" ", $this->s);

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
            $_s = $this->_s;    // array of keywords

            if ($kw_logic == 'orex')
                $_si = "[[:<:]]" . implode('[[:>:]]|[[:<:]]', $_s) . "[[:>:]]"; // imploded exact phrase for regexp
            else
                $_si = implode('|', $_s);                                       // imploded phrase for regexp

            $_si = $_si!=''?$_si:$s;

            $q_config['language'] = $options['qtranslate_lang'];
            $words = $options['set_exactonly']==1 ? array($s) : $_s;
            $regexp_words = count($_s > 0) ? implode('|', $_s) : $s;


            /*----------------------- Gather Types --------------------------*/
            if ($searchData['return_categories'] == 1)
                $types[] = "category";
	        if ( w_isset_def($searchData['return_tags'], 0) == 1 )
		        $types[] = "post_tag";
            if (isset($searchData['selected-return_terms']) && is_array($searchData['selected-return_terms']) && count($searchData['selected-return_terms']) > 0)
                $types = array_merge($types, $searchData['selected-return_terms']);
            if (count($types) < 1) {
                return '';
            } else {
                $twords = implode('[[:>:]]|[[:<:]]', $types);
                $taxonomies = "($wpdb->term_taxonomy.taxonomy REGEXP '[[:<:]]".$twords."[[:>:]]')";
            }
            /*---------------------------------------------------------------*/


            /*----------------------- Title query ---------------------------*/

            if ($kw_logic == 'or' || $kw_logic == 'and') {
                $op = strtoupper($kw_logic);
                if (count($_s)>0)
                    $_like = implode("%'$suf_like ".$op." ".$pre_field.$wpdb->terms.".name".$suf_field." LIKE $pre_like'%", $words);
                else
                    $_like = $s;
                $parts[] = "( ".$pre_field.$wpdb->terms.".name".$suf_field." LIKE $pre_like'%".$_like."%'$suf_like )";
            } else {
                $_like = array();
                $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                foreach ($words as $word) {
                    $_like[] = "
                           (".$pre_field.$wpdb->terms.".name".$suf_field." LIKE $pre_like'% ".$word." %'$suf_like
                        OR  ".$pre_field.$wpdb->terms.".name".$suf_field." LIKE $pre_like'".$word." %'$suf_like
                        OR  ".$pre_field.$wpdb->terms.".name".$suf_field." LIKE $pre_like'% ".$word."'$suf_like
                        OR  ".$pre_field.$wpdb->terms.".name".$suf_field." = '".$word."')";
                }
                $parts[] = "(" . implode(' '.$op.' ', $_like) . ")";
            }

            /*$words = $options['set_exactonly']==1?$s:$_si;
            if ($kw_logic == 'or' || $kw_logic == 'orex') {
                $parts[] = "(lower($wpdb->terms.name) REGEXP '$words')";
            } else {
                if (count($_s)>0)
                    $and_like = implode("$r_sign' AND lower($wpdb->terms.name) RLIKE '$l_sign", $_s);
                else
                    $and_like = $s;
                $parts[] = "lower($wpdb->terms.name) RLIKE '$l_sign".$and_like."$r_sign'";
            }*/
            $relevance_parts[] = "(case when
            (lower($wpdb->terms.name) REGEXP '$regexp_words')
             then $searchData[titleweight] else 0 end)";
            $relevance_parts[] = "(case when
            (lower($wpdb->terms.name) REGEXP '$s')
             then $searchData[etitleweight] else 0 end)";

            // The first word relevance is higher
            if (count($_s)>0)
                $relevance_parts[] = "(case when
              (lower($wpdb->terms.name) REGEXP '".$_s[0]."')
               then $searchData[etitleweight] else 0 end)";

            /*---------------------------------------------------------------*/

            /*------------------------ Exclude id's -------------------------*/
            if (w_isset_def($searchData['return_terms_exclude'], '') != '') {
                $exclude_terms = "($wpdb->terms.term_id NOT IN (" . $searchData['return_terms_exclude'] . "))";
            } else {
                $exclude_terms = "($wpdb->terms.term_id NOT IN (-55))";
            }
            /*---------------------------------------------------------------*/

            /*------------------------- Build like --------------------------*/
            $like_query = implode(' OR ', $parts);
            if ($like_query == "")
                $like_query = "(1)";
            else {
                $like_query = "($like_query)";
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Build relevance ------------------------*/
            $relevance = implode(' + ', $relevance_parts);
            if ($searchData['userelevance'] != 1 || $relevance == "")
                $relevance = "(1)";
            else {
                $relevance = "($relevance)";
            }
            /*---------------------------------------------------------------*/

            /*------------------------- WPML filter -------------------------*/
            /*$wpml_join = "";
            if (defined('ICL_LANGUAGE_CODE')
                && ICL_LANGUAGE_CODE != ''
                && defined('ICL_SITEPRESS_VERSION')
                && w_isset_def($searchData['wpml_compatibility'], 1) == 1
            )
                $wpml_join = "RIGHT JOIN " . $wpdb->base_prefix . "icl_translations ON (
                    $wpdb->terms.term_id = " . $wpdb->base_prefix . "icl_translations.element_id AND
                    " . $wpdb->base_prefix . "icl_translations.language_code = '" . ICL_LANGUAGE_CODE . "' AND
                    " . $wpdb->base_prefix . "icl_translations.element_type LIKE '%tax_%'
                )";*/

	        // New sub-select method instead of join
	        $wpml_query = "(1)";
	        if ( isset($options['wpml_lang'])
	             && w_isset_def($searchData['wpml_compatibility'], 1) == 1
	        )
		        $wpml_query = "
				EXISTS (
					SELECT DISTINCT(wpml.element_id)
					FROM " . $wpdb->base_prefix . "icl_translations as wpml
					WHERE
	                    $wpdb->terms.term_id = wpml.element_id AND
	                    wpml.language_code = '" . $this->escape($options['wpml_lang']) . "' AND
	                    wpml.element_type LIKE 'tax_%'
                )";
            /*---------------------------------------------------------------*/


            $querystr = "
    		SELECT 
          $wpdb->terms.name as title,
          $wpdb->terms.term_id as id,
          '' as content,
          '' as date,
          '' as author,
          $wpdb->term_taxonomy.taxonomy as taxonomy,
          'term' as content_type,
          $relevance as relevance
    		FROM $wpdb->terms
        LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id

    	WHERE
    	    $taxonomies
            AND $like_query
            AND $exclude_terms
            AND $wpml_query
        GROUP BY
          $wpdb->terms.term_id
         ";
            $querystr .= " ORDER BY relevance DESC, $wpdb->terms.name ASC
        LIMIT " . $searchData['maxresults'];

            $term_res = $wpdb->get_results($querystr, OBJECT);
            //var_dump($querystr);//die("!!");
            //var_dump($term_res);die("!!");

            $this->results = $term_res;

            return $term_res;

        }

        /**
         * Post-processing the results
         *
         * @return array
         */
        protected function post_process() {

            $term_res = is_array($this->results)?$this->results:array();
            $options = $this->options;
            $searchData = $this->searchData;
            $s = $this->s;
            $_s = $this->_s;

            foreach ($term_res as $k=>$v) {
                $term_res[$k]->link = get_term_link( (int)$v->id, $v->taxonomy);
            }

	        // Get term affected post count if enabled
	        if ( w_isset_def($searchData["display_number_posts_affected"], 0) == 1 ) {
		        foreach ($term_res as $k=>$v) {
			        $term = get_term_by('id', $v->id, $v->taxonomy);
			        $term_res[$k]->title .= " (" . $term->count . ")";
		        }
	        }


            /* WooCommerce Term image integration */
            if (function_exists('get_woocommerce_term_meta')) {
                foreach($term_res as $k => $result) {
                    if ( !empty($result->image) ) continue;

                    $thumbnail_id = get_woocommerce_term_meta( $result->id, 'thumbnail_id', true );
                    $image = wp_get_attachment_url( $thumbnail_id );
                    if (!empty($image))
                        $term_res[$k]->image = $image;
                }
            }

            return $term_res;

        }

    }
}