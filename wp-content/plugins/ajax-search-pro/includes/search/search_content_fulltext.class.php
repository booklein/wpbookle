<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchContentFulltext')) {
    /**
     * Fulltext content search class
     *
     * @class       wpdreams_searchContentFulltext
     * @version     1.0
     * @package     AjaxSearchPro/Classes
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchContentFulltext extends wpdreams_searchContent {

        /**
         * The search function
         *
         * @return array|string
         */
        protected function do_search() {
            global $wpdb;
            global $q_config;

            $options = $this->options;
            $searchData = $this->searchData;

            $parts = array();
            $relevance_parts = array();
            $types = array();
            $post_types = "";

            $s = $this->s; // full keyword
            $_s = $this->_s; // array of keywords

            $_si = implode('|', $_s); // imploded phrase for regexp
            $_si = $_si != '' ? $_si : $s;

            //$kw_logic = w_isset_def($searchData['keyword_logic'], 'or');
            $q_config['language'] = $options['qtranslate_lang'];

            if (isset($wpdb->base_prefix)) {
                $_prefix = $wpdb->base_prefix;
            } else {
                $_prefix = $wpdb->prefix;
            }

            /*------------------------- Statuses ----------------------------*/
            $statuses = array('publish');
            if ($searchData['searchinpending'])
                $statuses[] = 'pending';
            if ($searchData['searchindrafts'])
                $statuses[] = 'draft';
            $words = implode('|', $statuses);
            $post_statuses = "(lower($wpdb->posts.post_status) REGEXP '$words')";
            /*---------------------------------------------------------------*/

            /*----------------------- Gather Types --------------------------*/
            if ($options['set_inposts'] == 1)
                $types[] = "post";
            if ($options['set_inpages'])
                $types[] = "page";
            if (isset($options['customset']) && count($options['customset']) > 0)
                $types = array_merge($types, $options['customset']);
            if (count($types) < 1) {
                return '';
            } else {
                $words = implode('[[:>:]]|[[:<:]]', $types);
                $post_types = "($wpdb->posts.post_type REGEXP '[[:<:]]".$words."[[:>:]]')";
            }
            /*---------------------------------------------------------------*/


            $is_too_short = false;
            $not_exact_phrase = '';
            $fulltext = wpdreamsFulltext::getInstance();
            foreach ($_s as $_pp) {
                if (strlen($_pp) < $fulltext->getMinWordLength() || !$options['set_exactonly']) {
                    $is_too_short = true;
                    $not_exact_phrase .= " *" . $_pp . "*";
                } else {
                    $not_exact_phrase .= " " . $_pp;
                }
            }

            $not_exact_phrase = trim($not_exact_phrase);
            $exact_phrase = '"' . $s . '"';

            $ful_options = get_option('asp_fulltexto');

            if (w_isset_def($ful_options['dbuseregularwhenshort'], 0) && $is_too_short)
                return parent::do_search();

            /**
             * Construct the INDEX name to search
             */
            $match_against = '1';
            $relevance = '';
            $fixed_phrase = ($options['set_exactonly']) ? $exact_phrase : $not_exact_phrase;
            $boolean_mode = (get_option('asp_fulltext_indexed') == 0 || $is_too_short || !$options['set_exactonly']) ? ' IN BOOLEAN MODE' : '';
            $index_name = ($options['set_intitle']) ? "$wpdb->posts.post_title" : '';
            if ($index_name == '')
                $index_name .= ($options['set_incontent']) ? "$wpdb->posts.post_content" : '';
            else
                $index_name .= ($options['set_incontent']) ? ", $wpdb->posts.post_content" : '';
            if ($index_name == '')
                $index_name .= ($options['set_inexcerpt']) ? "$wpdb->posts.post_excerpt" : '';
            else
                $index_name .= ($options['set_inexcerpt']) ? ", $wpdb->posts.post_excerpt" : '';

            if ($index_name != '')
                $match_against = " MATCH(" . $index_name . ") AGAINST ('" . $fixed_phrase . "'" . $boolean_mode . ") ";

            if ($match_against != '1') {
                $relevance = "
          (
           MATCH(" . $index_name . ") AGAINST ('" . $exact_phrase . "'" . $boolean_mode . ") +
           MATCH(" . $index_name . ") AGAINST ('" . $not_exact_phrase . "'" . $boolean_mode . ")
           )
        ";
            }


            // ------------------------ Categories/taxonomies ----------------------
            if (!isset($options['categoryset']) || $options['categoryset'] == "")
                $options['categoryset'] = array();
            if (!isset($options['termset']) || $options['termset'] == "")
                $options['termset'] = array();

            $exclude_categories = array();
            $searchData['selected-exsearchincategories'] = w_isset_def($searchData['selected-exsearchincategories'], array());
            $searchData['selected-excludecategories'] = w_isset_def($searchData['selected-excludecategories'], array());
            $_all_cat = get_terms('category', array('fields'=>'ids'));
            $_needed_cat = array_diff($_all_cat, $searchData['selected-exsearchincategories']);
            $_needed_cat = !is_array($_needed_cat)?array():$_needed_cat;
            $exclude_categories = array_diff(array_merge($_needed_cat, $searchData['selected-excludecategories']), $options['categoryset']);

            $exclude_terms = array();
            $exclude_showterms = array();
            $searchData['selected-showterms'] = w_isset_def($searchData['selected-showterms'], array());
            $searchData['selected-excludeterms'] = w_isset_def($searchData['selected-excludeterms'], array());
            foreach ($searchData['selected-excludeterms'] as $tax=>$terms) {
                $exclude_terms = array_merge($exclude_terms, $terms);
            }
            foreach ($searchData['selected-showterms'] as $tax=>$terms) {
                $exclude_showterms = array_merge($exclude_showterms, $terms);
            }

            $exclude_terms = array_diff(array_merge($exclude_terms, $exclude_showterms), $options['termset']);

            $all_terms = array();
            $all_terms = array_merge($exclude_categories, $exclude_terms);
            if (count($all_terms) > 0) {
                $words = '--'.implode('--|--', $all_terms).'--';
                $term_query = "HAVING (ttid NOT REGEXP '$words')";
            }
            // ---------------------------------------------------------------------

            /*------------- Custom Fields with Custom selectors -------------*/
            if (isset($options['aspf']) && isset($options['aspfdata'])) {
                $parts = array();

                foreach ($options['aspfdata'] as $u_data) {
                    $data = json_decode(base64_decode($u_data));
                    $posted = $options['aspf'][$data->asp_f_field];

                    $ll_like = "";
                    $rr_like = "";
                    $operator = "=";

                    if (isset($data->asp_f_operator)) {
                        switch ($data->asp_f_operator) {
                            case 'eq':
                                $operator = "=";
                                break;
                            case 'neq':
                                $operator = "<>";
                                break;
                            case 'lt':
                                $operator = "<";
                                break;
                            case 'gt':
                                $operator = ">";
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
                                break;
                        }
                    }

                    //var_dump($data);
                    /*if (w_isset_def($searchData['cf_null_values'], 1) == 1) {
                        $cf_key_is_null .= " OR $wpdb->postmeta.meta_key IS NULL";
                        $cf_val_is_null .= " OR $wpdb->postmeta.meta_value IS NULL";
                    }*/

                    if ($data->asp_f_type == 'range' && isset($posted['lower'])) {
                        $parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value BETWEEN ".$posted['lower']." AND ".$posted['upper']." ) )";
                    } else if ($data->asp_f_type == 'slider' && isset($posted)) {
                        $parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator $posted  ) )";
                    } else if ($data->asp_f_type == 'radio' && isset($posted)) {
                        $parts[] = " ( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                     ($wpdb->postmeta.meta_value $operator ".$ll_like.$posted.$rr_like." ) )";
                    } else if ($data->asp_f_type == 'dropdown' && isset($posted)) {
                        if (isset($data->asp_f_dropdown_multi) && count($posted) > 0) {
                            // The AND logic doesn't make any sense
                            $logic = 'OR';
                            $values = '';
                            foreach ($posted as $v) {
                                if ($values != '')
                                    $values .= " $logic $wpdb->postmeta.meta_value $operator ".$ll_like.$v.$rr_like;
                                else
                                    $values .= "$wpdb->postmeta.meta_value $operator ".$ll_like.$v.$rr_like;
                            }

                            $values = $values == '' ? '0' : $values;
                            $parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";
                        } else {
                            $parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND
                                        ($wpdb->postmeta.meta_value $operator ".$ll_like.$posted.$rr_like." ) )";
                        }

                    } else if ($data->asp_f_type == 'checkboxes' && isset($posted)) {

                        $logic = $data->asp_f_checkboxes_logic;
                        $values = '';
                        foreach ($posted as $v => $vv) {
                            if ($values != '')
                                $values .= " $logic $wpdb->postmeta.meta_value $operator ".$ll_like.$v.$rr_like;
                            else
                                $values .= "$wpdb->postmeta.meta_value $operator ".$ll_like.$v.$rr_like;
                        }
                        $values = $values == '' ? '0' : $values;
                        $parts[] = "( ($wpdb->postmeta.meta_key='$data->asp_f_field') AND ($values) )";

                    }

                }

                $this->cf_parts = $parts;
            }

            $meta_count = count($this->cf_parts);

            $cf_query = implode(" OR ", $this->cf_parts);
            if ($cf_query == "") {
                $cf_select = "0";
                $cf_having = "";
            } else {
                $cf_select = "
                SELECT COUNT(*) FROM $wpdb->postmeta WHERE
                    $wpdb->postmeta.post_id = $wpdb->posts.ID
                AND
                    ($cf_query)";
                if (w_isset_def($searchData['cf_logic'], 'AND') == 'AND' )
                    $cf_having = "meta_matches >= ".$meta_count;
                else
                    $cf_having = "meta_matches >= 1";
                if ($term_query != '')
                    $cf_having = " AND ".$cf_having;
                else
                    $cf_having = "HAVING ".$cf_having;
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Custom Fields --------------------------*/
            if (isset($searchData['selected-customfields'])) {
                $selected_customfields = $searchData['selected-customfields'];
                if (is_array($selected_customfields) && count($selected_customfields) > 0) {
                    $words = $options['set_exactonly'] == 1 ? $s : $_si;
                    foreach ($selected_customfields as $cfield) {
                        $parts[] = "($wpdb->postmeta.meta_key='$cfield' AND
                                   lower($wpdb->postmeta.meta_value) REGEXP '$words')";
                    }
                }
            }
            /*---------------------------------------------------------------*/

            /*------------------------ Exclude id's -------------------------*/
            if (isset($searchData['excludeposts']) && $searchData['excludeposts'] != "") {
                $exclude_posts = "$wpdb->posts.ID NOT IN (" . $searchData['excludeposts'] . ")";
            } else {
                $exclude_posts = "$wpdb->posts.ID NOT IN (-55)";
            }
            /*---------------------------------------------------------------*/


            /*------------------------- Build like --------------------------*/
            $like_query = implode(' OR ', $parts);
            if ($like_query == "")
                $like_query = "(0)";
            else {
                $like_query = "($like_query)";
            }
            /*---------------------------------------------------------------*/

            /*------------------------- WPML filter -------------------------*/
            $wpml_join = "";
            if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != '' && w_isset_def($searchData['wpml_compatibility'], 1) == 1)
                $wpml_join = "RIGHT JOIN ".$wpdb->base_prefix."icl_translations ON ($wpdb->posts.ID = ".$wpdb->base_prefix."icl_translations.element_id AND ".$wpdb->base_prefix."icl_translations.language_code = '".ICL_LANGUAGE_CODE."')";
            /*---------------------------------------------------------------*/


            $orderby = ((isset($searchData['selected-orderby']) && $searchData['selected-orderby'] != '') ? $searchData['selected-orderby'] : "post_date DESC");
            $querystr = "
    		SELECT 
          $wpdb->posts.post_title as title,
          $wpdb->posts.ID as id,
          $wpdb->posts.post_date as date,               
          $wpdb->posts.post_content as content,
          $wpdb->posts.post_excerpt as excerpt,
          $wpdb->users.user_nicename as author,
          'pagepost' as content_type,
          $wpdb->posts.post_type as post_type,
          ($cf_select) as meta_matches,
            CASE WHEN ".$_prefix."ajaxsearchpro_priorities.priority IS NULL
                   THEN 100
                   ELSE ".$_prefix."ajaxsearchpro_priorities.priority
            END AS priority,
          CONCAT('--', GROUP_CONCAT(DISTINCT $wpdb->terms.term_id SEPARATOR '----'), '--') as ttid,
          $wpdb->posts.post_type as post_type,";
            if ($searchData['userelevance'] == 1 && $relevance != '') {
                $querystr .= $relevance;
            } else {
                $querystr .= "1 ";
            }
            $querystr .= "
          as relevance
    		FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID
        LEFT JOIN ".$_prefix."ajaxsearchpro_priorities ON (".$_prefix."ajaxsearchpro_priorities.post_id = $wpdb->posts.ID AND ".$_prefix."ajaxsearchpro_priorities.blog_id = ".get_current_blog_id().")
        LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->posts.post_author
        LEFT JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
        LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
        LEFT JOIN $wpdb->terms ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
        $wpml_join
    		WHERE
            $post_types
        AND $post_statuses
        AND (" . $match_against . " OR (" . $like_query . "))
        AND (" . $exclude_posts . ")
        GROUP BY
          $wpdb->posts.ID
          $term_query
          $cf_having
         ";
            $querystr .= " ORDER BY priority DESC, relevance DESC, " . $wpdb->posts . "." . $orderby . "
        LIMIT " . $searchData['maxresults'];

            $pageposts = $wpdb->get_results($querystr, OBJECT);

            //var_dump($querystr); var_dump($pageposts);die("!!");

            $this->results = $pageposts;


            return $pageposts;

        }

    }
}