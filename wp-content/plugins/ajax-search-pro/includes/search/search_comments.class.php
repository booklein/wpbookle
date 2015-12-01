<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchComments')) {
    /**
     * Comment search class
     *
     * @class       wpdreams_searchComments
     * @version     1.0
     * @package     AjaxSearchPro/Abstracts
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchComments extends wpdreams_search {

        /**
         * The search function
         *
         * @return array of results
         */
        protected function do_search() {
            global $wpdb;
            $commentsresults = array();

            if (isset($wpdb->base_prefix)) {
                $_prefix = $wpdb->base_prefix;
            } else {
                $_prefix = $wpdb->prefix;
            }

            $options = $this->options;
            $searchData = $this->searchData;
            $s = $this->s;
            $_s = $this->_s;

            if ($options['set_incomments'] && count($_s) > 0) {

                $like = "lower($wpdb->comments.comment_content) REGEXP '" . implode('|', $_s) . "'";
                $querystr = "
      		SELECT 
            $wpdb->comments.comment_ID as id,
            $wpdb->comments.comment_post_ID as post_id,
            $wpdb->comments.user_id as user_id,
            $wpdb->comments.comment_content as content,
            'comment' as content_type,
            $wpdb->comments.comment_date as date
      		FROM $wpdb->comments
      		WHERE
          ($wpdb->comments.comment_approved=1)
          AND
          (" . $like . ")
      		ORDER BY $wpdb->comments.comment_ID DESC
      		LIMIT " . $searchData['maxresults'];
                //var_dump($querystr);
                $commentsresults = $wpdb->get_results($querystr, OBJECT);
                if (is_array($commentsresults)) {
                    foreach ($commentsresults as $k => $v) {
                        $commentsresults[$k]->link = get_comment_link($v->id);
                        $commentsresults[$k]->author = get_comment_author($v->id);

                        $commentsresults[$k]->title = wd_substr_at_word($commentsresults[$k]->content, 40) . "...";

                    }
                }
            }
            $this->results = $commentsresults;
            return $commentsresults;
        }

    }
}