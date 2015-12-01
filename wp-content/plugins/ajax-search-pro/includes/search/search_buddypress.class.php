<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchBuddyPress')) {
    /**
     * BuddyPress Group and Activity search
     *
     * @class       wpdreams_searchBuddyPress
     * @version     1.0
     * @package     AjaxSearchPro/Classes
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchBuddyPress extends wpdreams_search {

        /**
         * @var array of query parts
         */
        private $parts = array();
        /**
         * @var int the remaining limit (number of items to look for)
         */
        private $remaining_limit;
        /**
         * @var string the final query
         */
        private $query;

        /**
         * The search function
         *
         * @return array|string
         */
        protected function do_search() {
            global $wpdb;

            if (isset($wpdb->base_prefix)) {
                $_prefix = $wpdb->base_prefix;
            } else {
                $_prefix = $wpdb->prefix;
            }

            $options = $this->options;
            $searchData = $this->searchData;

            $kw_logic = w_isset_def($searchData['keyword_logic'], 'or');
            $q_config['language'] = $options['qtranslate_lang'];

            $s = $this->s; // full keyword
            $_s = $this->_s; // array of keywords

            if ($kw_logic == 'orex')
                $_si = "[[:<:]]" . implode('[[:>:]]|[[:<:]]', $_s) . "[[:>:]]"; // imploded exact phrase for regexp
            else
                $_si = implode('|', $_s); // imploded phrase for regexp

            $_si = $_si != '' ? $_si : $s;

            $repliesresults = array();
            $userresults = array();
            $groupresults = array();
            $activityresults = array();

            $words = $options['set_exactonly']==1 ? array($s) : $_s;
            $regexp_words = count($_s > 0) ? implode('|', $_s) : $s;

            if (function_exists('bp_core_get_user_domain')) {
                $parts = array();
                $relevance_parts = array();
                /*----------------------- User query ---------------------------*/

                // User query had been replaced with user results..

                /*---------------------------------------------------------------*/

                /*----------------------- Groups query --------------------------*/
                if ($searchData['search_in_bp_groups'] && bp_is_active('groups')) {
                    $parts = array();
                    $relevance_parts = array();
                    /*------------------------- Statuses ----------------------------*/
                    $statuses = array();
                    if ($searchData['search_in_bp_groups_public'] == 1)
                        $statuses[] = 'public';
                    if ($searchData['search_in_bp_groups_private'] == 1)
                        $statuses[] = 'private';
                    if ($searchData['search_in_bp_groups_hidden'] == 1)
                        $statuses[] = 'hidden';
                    if (count($statuses) < 1)
                        return '';
                    $swords = implode('|', $statuses);
                    $group_statuses = "(lower(" . $wpdb->prefix . "bp_groups.status) REGEXP '$swords')";
                    /*---------------------------------------------------------------*/

                    /*------------------------- Title query -------------------------*/

                    if ($kw_logic == 'or' || $kw_logic == 'and') {
                        $op = strtoupper($kw_logic);
                        if (count($_s)>0)
                            $_like = implode("%' ".$op." lower(" . $wpdb->prefix . "bp_groups.name) LIKE '%", $words);
                        else
                            $_like = $s;
                        $parts[] = "( lower(" . $wpdb->prefix . "bp_groups.name) LIKE '%".$_like."%' )";
                    } else {
                        $_like = array();
                        $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                        foreach ($words as $word) {
                            $_like[] = "
                           (lower(" . $wpdb->prefix . "bp_groups.name) LIKE '% ".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_groups.name) LIKE '".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_groups.name) LIKE '% ".$word."'
                        OR  lower(" . $wpdb->prefix . "bp_groups.name) = '".$word."')";
                        }
                        $parts[] = "(" . implode(' '.$op.' ', $_like) . ")";
                    }

                    /*$words = $options['set_exactonly'] == 1 ? $s : $_si;
                    if ($kw_logic == 'or' || $kw_logic == 'orex') {
                        $parts[] = "(lower(" . $wpdb->prefix . "bp_groups.name) REGEXP '$words')";
                    } else {
                        if (count($_s) > 0)
                            $and_like = implode("$r_sign' AND lower(" . $wpdb->prefix . "bp_groups.name) RLIKE '$l_sign", $_s);
                        else
                            $and_like = $s;
                        $parts[] = "lower(" . $wpdb->prefix . "bp_groups.name) RLIKE '$l_sign" . $and_like . "$r_sign'";
                    }*/
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_groups.name) REGEXP '$regexp_words')
                     then $searchData[titleweight] else 0 end)";
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_groups.name) = '$s')
                     then $searchData[etitleweight] else 0 end)";

                    // The first word relevance is higher
                    if (count($_s) > 0)
                        $relevance_parts[] = "(case when
                      (lower(" . $wpdb->prefix . "bp_groups.name) REGEXP '" . $_s[0] . "')
                       then $searchData[etitleweight] else 0 end)";
                    /*---------------------------------------------------------------*/

                    /*---------------------- Description query ----------------------*/
                    if ($kw_logic == 'or' || $kw_logic == 'and') {
                        $op = strtoupper($kw_logic);
                        if (count($_s)>0)
                            $_like = implode("%' ".$op." lower(" . $wpdb->prefix . "bp_groups.description) LIKE '%", $words);
                        else
                            $_like = $s;
                        $parts[] = "( lower(" . $wpdb->prefix . "bp_groups.description) LIKE '%".$_like."%' )";
                    } else {
                        $_like = array();
                        $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                        foreach ($words as $word) {
                            $_like[] = "
                           (lower(" . $wpdb->prefix . "bp_groups.description) LIKE '% ".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_groups.description) LIKE '".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_groups.description) LIKE '% ".$word."'
                        OR  lower(" . $wpdb->prefix . "bp_groups.description) = '".$word."')";
                        }
                        $parts[] = "(" . implode(' '.$op.' ', $_like) . ")";
                    }


                    /*if ($kw_logic == 'or' || $kw_logic == 'orex') {
                        $parts[] = "(lower(" . $wpdb->prefix . "bp_groups.description) REGEXP '$words')";
                    } else {
                        if (count($_s) > 0)
                            $and_like = implode("$r_sign' AND lower(" . $wpdb->prefix . "bp_groups.description) RLIKE '$l_sign", $_s);
                        else
                            $and_like = $s;
                        $parts[] = "lower(" . $wpdb->prefix . "bp_groups.description) RLIKE '$l_sign" . $and_like . "$r_sign'";
                    }*/
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_groups.description) REGEXP '$regexp_words')
                     then $searchData[contentweight] else 0 end)";
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_groups.description) = '$s')
                     then $searchData[econtentweight] else 0 end)";
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

                    $querystr = "
             SELECT
               " . $wpdb->prefix . "bp_groups.id as id,
               " . $wpdb->prefix . "bp_groups.name as title,
               " . $wpdb->prefix . "bp_groups.description as content,
               " . $wpdb->prefix . "bp_groups.date_created as date,
               $wpdb->users.user_nicename as author,
               'bp_group' as content_type,
               $relevance as relevance
             FROM
               " . $wpdb->prefix . "bp_groups
             LEFT JOIN $wpdb->users ON $wpdb->users.ID = " . $wpdb->prefix . "bp_groups.creator_id
             WHERE
                  $group_statuses
              AND $like_query
              ORDER BY relevance DESC, title ASC";


                    $groupresults = $wpdb->get_results($querystr, OBJECT);
                    foreach ($groupresults as $k => $v) {
                        $group = new BP_Groups_Group($v->id);
                        $groupresults[$k]->link = bp_get_group_permalink($group);
                        if ($searchData['image_options']['show_images'] == 1) {
                            $avatar_options = array('item_id' => $v->id, 'object' => 'group', 'type' => 'full', 'html' => false);
                            $im = bp_core_fetch_avatar($avatar_options);

                            if ($im != '') {
                                $groupresults[$k]->image = $im;
                            }
                        }
                        if ($groupresults[$k]->content != '')
                            $groupresults[$k]->content = wd_substr_at_word(strip_tags($groupresults[$k]->content), $searchData['descriptionlength']) . "...";
                    }
                }
                /*---------------------------------------------------------------*/

                /*----------------------- Activity query ------------------------*/

                if ($searchData['search_in_bp_activities'] && bp_is_active('groups')) {
                    $parts = array();
                    $relevance_parts = array();
                    /*---------------------- Description query ----------------------*/
                    if ($kw_logic == 'or' || $kw_logic == 'and') {
                        $op = strtoupper($kw_logic);
                        if (count($_s)>0)
                            $_like = implode("%' ".$op." lower(" . $wpdb->prefix . "bp_activity.content) LIKE '%", $words);
                        else
                            $_like = $s;
                        $parts[] = "( lower(" . $wpdb->prefix . "bp_activity.content) LIKE '%".$_like."%' )";
                    } else {
                        $_like = array();
                        $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                        foreach ($words as $word) {
                            $_like[] = "
                           (lower(" . $wpdb->prefix . "bp_activity.content) LIKE '% ".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_activity.content) LIKE '".$word." %'
                        OR  lower(" . $wpdb->prefix . "bp_activity.content) LIKE '% ".$word."'
                        OR  lower(" . $wpdb->prefix . "bp_activity.content) = '".$word."')";
                        }
                        $parts[] = "(" . implode(' '.$op.' ', $_like) . ")";
                    }

                    /*$words = $options['set_exactonly'] == 1 ? $s : $_si;
                    if ($kw_logic == 'or' || $kw_logic == 'orex') {
                        $parts[] = "(lower(" . $wpdb->prefix . "bp_activity.content) REGEXP '$words')";
                    } else {
                        if (count($_s) > 0)
                            $and_like = implode("$r_sign' AND lower(" . $wpdb->prefix . "bp_activity.content) RLIKE '$l_sign", $_s);
                        else
                            $and_like = $s;
                        $parts[] = "lower(" . $wpdb->prefix . "bp_activity.content) RLIKE '$l_sign" . $and_like . "$r_sign'";
                    }*/
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_activity.content) REGEXP '$regexp_words')
                     then $searchData[contentweight] else 0 end)";
                    $relevance_parts[] = "(case when
                    (lower(" . $wpdb->prefix . "bp_activity.content) = '$s')
                     then $searchData[econtentweight] else 0 end)";
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

                    $querystr = "
                 SELECT
                   " . $wpdb->prefix . "bp_activity.id as id,
                   " . $wpdb->prefix . "bp_activity.content as title,
                   " . $wpdb->prefix . "bp_activity.content as content,
                   " . $wpdb->prefix . "bp_activity.date_recorded as date,
                   $wpdb->users.user_nicename as author,
                   " . $wpdb->prefix . "bp_activity.user_id as author_id,
                   'bp_activity' as content_type,
                   $relevance as relevance
                 FROM
                   " . $wpdb->prefix . "bp_activity
                 LEFT JOIN $wpdb->users ON $wpdb->users.ID = " . $wpdb->prefix . "bp_activity.user_id
                 WHERE
                   (" . $wpdb->prefix . "bp_activity.component = 'activity' AND " . $wpdb->prefix . "bp_activity.is_spam = 0)
                   AND $like_query
                   ORDER BY relevance DESC, title ASC";

                    $activityresults = $wpdb->get_results($querystr, OBJECT);

                    foreach ($activityresults as $k => $v) {
                        $activityresults[$k]->link = bp_activity_get_permalink($v->id);
                        $activityresults[$k]->image = bp_core_fetch_avatar(array('item_id' => $v->author_id, 'html' => false));
                    }
                }


                do_action('bbpress_init');
            }

            $this->results = array(
                'repliesresults' => $repliesresults,
                'groupresults' => $groupresults,
                'activityresults' => $activityresults
            );
            return $this->results;
        }

        /**
         * @param $parts
         * @param bool $is_multi
         * @return mixed
         */
        protected function build_query($parts, $is_multi = false) {

            $searchData = $this->searchData;

            $l_parts = array();
            $r_parts = array();

            if ($is_multi == true) {
                foreach ($parts as $part) {
                    $l_parts = array_merge($l_parts, $part[0]);
                    $r_parts = array_merge($r_parts, $part[1]);
                }
            } else {
                $l_parts = $parts[0];
                $r_parts = $parts[1];
            }

            //var_dump($l_parts);var_dump($r_parts);

            /*------------------------- Build like --------------------------*/
            $like_query = implode(' OR ', $l_parts);
            if ($like_query == "")
                $like_query = "(1)";
            else {
                $like_query = "($like_query)";
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Build relevance ------------------------*/
            $relevance = implode(' + ', $r_parts);
            if ($searchData['userelevance'] != 1 || $relevance == "")
                $relevance = "(1)";
            else {
                $relevance = "($relevance)";
            }
            /*---------------------------------------------------------------*/


            return str_replace(
                array("{relevance_query}", "{like_query}", "{remaining_limit}"),
                array($relevance, $like_query, $this->remaining_limit),
                $this->query
            );

        }

    }
}