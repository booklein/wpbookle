<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchUsers')) {
    /**
     * User search class
     *
     * @class       wpdreams_searchUsers
     * @version     1.0
     * @package     AjaxSearchPro/Classes
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchUsers extends wpdreams_search {

        /**
         * @var int remaining items to look for
         */
        private $remaining_limit;
        /**
         * @var string final query
         */
        private $query;

        /**
         * The search function
         *
         * @return array
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
            $comp_options = get_option('asp_compatibility');

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

            // Keyword logics
            $kw_logic = w_isset_def($searchData['keyword_logic'], 'or');
            $q_config['language'] = $options['qtranslate_lang'];

            $s = $this->s; // full keyword
            $_s = $this->_s; // array of keywords

            if ($kw_logic == 'orex')
                $_si = "[[:<:]]" . implode('[[:>:]]|[[:<:]]', $_s) . "[[:>:]]"; // imploded exact phrase for regexp
            else
                $_si = implode('|', $_s); // imploded phrase for regexp

            $_si = $_si != '' ? $_si : $s;

            $userresults = array();

            $words = $options['set_exactonly'] == 1 ? array($s) : $_s;
            $regexp_words = count($_s > 0) ? implode('|', $_s) : $s;

            $parts = array();
            $relevance_parts = array();

            /*---------------------- Login Name query ------------------------*/
            if ( w_isset_def($searchData['user_login_search'], 1) ) {
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( ".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->users.".user_login".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "(" . implode(' ' . $op . ' ', $_like) . ")";
                }

                if (count($_s) > 0)
                    $relevance_parts[] = "(case when
                        (".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE '%".$_s[0]."%')
                         then $searchData[titleweight] else 0 end)";
                $relevance_parts[] = "(case when
                    (".$pre_field.$wpdb->users.".user_login".$suf_field." LIKE '%$s%')
                     then $searchData[titleweight] else 0 end)";
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Display Name query ------------------------*/
            if ( w_isset_def($searchData['user_display_name_search'], 1) ) {
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( ".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->users.".display_name".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "(" . implode(' ' . $op . ' ', $_like) . ")";
                }

                if (count($_s) > 0)
                    $relevance_parts[] = "(case when
                        (".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE '%".$_s[0]."%')
                         then $searchData[titleweight] else 0 end)";
                $relevance_parts[] = "(case when
                    (".$pre_field.$wpdb->users.".display_name".$suf_field." LIKE '%$s%')
                     then $searchData[titleweight] else 0 end)";
            }
            /*---------------------------------------------------------------*/

            /*---------------------- First Name query -----------------------*/
            if ( w_isset_def($searchData['user_first_name_search'], 1) ) {
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( $wpdb->usermeta.meta_key = 'first_name' AND ( ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like ) )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "($wpdb->usermeta.meta_key = 'first_name' AND (" . implode(' ' . $op . ' ', $_like) . ") )";
                }
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Last Name query ------------------------*/
            if ( w_isset_def($searchData['user_last_name_search'], 1) ) {
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( $wpdb->usermeta.meta_key = 'last_name' AND ( ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like ) )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "( $wpdb->usermeta.meta_key = 'last_name' AND ( " . implode(' ' . $op . ' ', $_like) . ") )";
                }
            }
            /*---------------------------------------------------------------*/

            /*---------------------- Biography query ------------------------*/
            if ( w_isset_def($searchData['user_bio_search'], 1) ) {
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( $wpdb->usermeta.meta_key = 'description' AND ( ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like ) )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "( $wpdb->usermeta.meta_key = 'description' AND (" . implode(' ' . $op . ' ', $_like) . ") )";
                }
            }
            /*---------------------------------------------------------------*/

            /*------------------------ Exclude Roles ------------------------*/
            $roles_query = '';
            if ( count(w_isset_def($searchData['selected-user_search_exclude_roles'], array())) > 0 ) {
                $role_parts = array();
                foreach ($searchData['selected-user_search_exclude_roles'] as $role) {
                    $role_parts[] = $wpdb->usermeta . '.meta_value LIKE \'%"' . $role . '"%\'';
                }
                $roles_query = "AND $wpdb->users.ID NOT IN (
                    SELECT DISTINCT($wpdb->usermeta.user_id)
                    FROM $wpdb->usermeta
                    WHERE $wpdb->usermeta.meta_key = 'wp_capabilities' AND (" . implode(' AND ', $role_parts) . ")
                )";
            }
            /*---------------------------------------------------------------*/

            /*-------------------- Other selected meta ----------------------*/
            $user_search_meta_fields = explode(',', w_isset_def($searchData['user_search_meta_fields'], ''));
            foreach ($user_search_meta_fields as $meta_field) {
                $meta_field = trim($meta_field);
                if ($kw_logic == 'or' || $kw_logic == 'and') {
                    $op = strtoupper($kw_logic);
                    if (count($_s) > 0)
                        $_like = implode("%'$suf_like " . $op . " ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%", $words);
                    else
                        $_like = $s;
                    $parts[] = "( $wpdb->usermeta.meta_key = '".$meta_field."' AND ( ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'%" . $_like . "%'$suf_like ) )";
                } else {
                    $_like = array();
                    $op = $kw_logic == 'andex' ? 'AND' : 'OR';
                    foreach ($words as $word) {
                        $_like[] = "
                               (".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'" . $word . " %'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." LIKE $pre_like'% " . $word . "'$suf_like
                            OR  ".$pre_field.$wpdb->usermeta.".meta_value".$suf_field." = '" . $word . "')";
                    }
                    $parts[] = "( $wpdb->usermeta.meta_key = '".$meta_field."' AND (" . implode(' ' . $op . ' ', $_like) . ") )";
                }
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

            /*----------------------- Title Field ---------------------------*/
            switch ( w_isset_def($searchData['user_search_title_field'], 'display_name') ) {
                case 'login':
                    $uname_select = "$wpdb->users.user_login";
                    break;
                case 'display_name':
                    $uname_select = "$wpdb->users.display_name";
                    break;
                default:
                    $uname_select = "$wpdb->users.display_name";
                    break;
            }
            /*---------------------------------------------------------------*/



            $querystr = "
                       SELECT
                         $wpdb->users.ID as id,
                         $uname_select as title,
                         '' as date,
                         '' as author,
                         '' as content,
                         'user' as content_type,
                         $relevance as relevance,
                         $wpdb->users.user_login as user_login,
                         $wpdb->users.user_nicename as user_nicename,
                         $wpdb->users.display_name as user_display_name
                       FROM
                         $wpdb->users
                       LEFT JOIN $wpdb->usermeta ON $wpdb->usermeta.user_id = $wpdb->users.ID
                       WHERE
                         $like_query
                         $roles_query
                       GROUP BY id
                       ORDER BY relevance DESC, title ASC
                    ";

            //var_dump($querystr);//die();

            $userresults = $wpdb->get_results($querystr, OBJECT);

            $this->results = $userresults;

            return $this->results;
        }


        /**
         * Post processing the user results
         */
        protected function post_process() {
            $userresults = is_array($this->results) ? $this->results : array();
            $options = $this->options;
            $searchData = $this->searchData;
            $s = $this->s;
            $_s = $this->_s;

            foreach ($userresults as $k => $v) {

                /*--------------------------- Link ------------------------------*/
                switch ( w_isset_def($searchData['user_search_url_source'], 'default') ) {
                    case "bp_profile":
                        if (function_exists('bp_core_get_user_domain'))
                            $userresults[$k]->link = bp_core_get_user_domain($v->id);
                        else
                            $userresults[$k]->link = get_author_posts_url($v->id);
                        break;
                    case "custom":
                        $userresults[$k]->link = str_replace(
                            array("{USER_ID}", "{USER_LOGIN}", "{USER_NICENAME}", "{USER_DISPLAYNAME}"),
                            array($v->id, $v->user_login, $v->user_nicename, $v->user_display_name),
                            w_isset_def($searchData['user_search_custom_url'], '?author={USER_ID}')
                        );
                        break;
                    default:
                        $userresults[$k]->link = get_author_posts_url($v->id);
                }
                /*---------------------------------------------------------------*/

                /*-------------------------- Image ------------------------------*/
                if (w_isset_def($searchData['user_search_display_images'], 1)) {
                    if ( w_isset_def($searchData['user_search_image_source'], 'default') == 'buddypress' &&
                         function_exists('bp_core_fetch_avatar') ) {

                        $im = bp_core_fetch_avatar(array('item_id' => $v->id, 'html' => false));
                        if ($im != '')
                            $userresults[$k]->image = $im;
                    } else {
                        $im = $this->get_avatar_url($v->id);
                        if ($im != '')
                            $userresults[$k]->image = $im;
                    }
                }
                /*---------------------------------------------------------------*/

                $userresults[$k]->title = $this->adv_title($v->title, $v->id);

                /*---------------------- Description ----------------------------*/
                switch ( w_isset_def($searchData['user_search_description_field'], 'bio') ) {
                    case 'buddypress_last_activity':
                        $update = get_user_meta($v->id, 'bp_latest_update', true);
                        if (is_array($update) && isset($update['content']))
                            $userresults[$k]->content = $update['content'];
                        if ($userresults[$k]->content != '') {
                            $userresults[$k]->content = wd_substr_at_word(strip_tags($userresults[$k]->content), $searchData['descriptionlength']) . "...";
                        } else {
                            $userresults[$k]->content = "";
                        }
                        break;
                    case 'nothing':
                        $userresults[$k]->content = "";
                        break;
                    default:
                        $content = get_user_meta($v->id, 'description', true);
                        if ($content != '')
                            $userresults[$k]->content = $content;
                        if ($userresults[$k]->content != '') {
                            $userresults[$k]->content = wd_substr_at_word(strip_tags($userresults[$k]->content), $searchData['descriptionlength']) . "...";
                        } else {
                            $userresults[$k]->content = "";
                        }
                }

                $userresults[$k]->content = $this->adv_desc($v->content, $v->id);
                /*---------------------------------------------------------------*/

            }
        }

        /**
         * Gets the avatar URL as a similar function is only supported in WP 4.2 +
         *
         * @param $user_id int the user ID
         * @param int $size int the size of the avatar
         * @return mixed
         */
        protected function get_avatar_url($user_id, $size = 96){
            $get_avatar = get_avatar($user_id, $size);
            preg_match('/src=(.*?) /i', $get_avatar, $matches);
	        if (isset($matches[1]))
                return str_replace(array('"',"'"), '', $matches[1]);
        }

        /**
         * Generates the user result title based on the advanced title field
         *
         * @param $title string post title
         * @param $id int post id
         * @return string final post title
         */
        protected function adv_title($title, $id) {

            $titlefield = w_isset_def($this->searchData['user_search_advanced_title_field'], '');
            if ($titlefield == '') return $title;
            preg_match_all("/{(.*?)}/", $titlefield, $matches);
            if (isset($matches[0]) && isset($matches[1]) && is_array($matches[1])) {
                foreach ($matches[1] as $field) {
                    if ($field == 'titlefield') {
                        $titlefield = str_replace('{titlefield}', $title, $titlefield);
                    } else {
                        $val = get_user_meta($id, $field, true);
                        $titlefield = str_replace('{' . $field . '}', $val, $titlefield);
                    }
                }
            }
            return $titlefield;
        }

        /**
         * Generates the user result description based on the advanced description field
         *
         * @param $title string post description
         * @param $id int post id
         * @return string final post description
         */
        protected function adv_desc($desc, $id) {
            $descfield = w_isset_def($this->searchData['user_search_advanced_description_field'], '');
            if ($descfield == '') return $desc;
            preg_match_all("/{(.*?)}/", $descfield, $matches);
            if (isset($matches[0]) && isset($matches[1]) && is_array($matches[1])) {
                foreach ($matches[1] as $field) {
                    if ($field == 'descriptionfield') {
                        $descfield = str_replace('{descriptionfield}', $desc, $descfield);
                    } else {
                        $val = get_user_meta($id, $field, true);
                        $descfield = str_replace('{' . $field . '}', $val, $descfield);
                    }
                }
            }
            return $descfield;
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