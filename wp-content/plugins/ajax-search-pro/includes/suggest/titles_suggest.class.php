<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpd_titlesKeywordSuggest')) {
    class wpd_titlesKeywordSuggest extends wpd_keywordSuggestAbstract {

        private $res = array();

        function __construct($args = array()) {
	        $defaults = array(
		        'maxCount' => 10,
		        'maxCharsPerWord' => 25,
		        'postTypes' => 'any',
		        'match_start' => false
	        );
	        $args = wp_parse_args( $args, $defaults );

            $this->maxCount = $args['maxCount'];
            $this->maxCharsPerWord = $args['maxCharsPerWord'];
            $this->postTypes = $args['postTypes'];
	        $this->matchStart = $args['match_start'];
        }

        function getKeywords($q) {

            $this->getResults($q);

            return $this->res;
        }

        function getResults($q) {
            global $wpdb;

            $q = $this->escape($q);
            $count = $this->maxCount - count($this->res);
            $words = implode("','", $this->postTypes);
            $post_types = "($wpdb->posts.post_type IN ('" . $words . "') )";

            $pre = $this->matchStart ? "" : "%";

            if ($count <= 0) return;

            $the_query = "
                SELECT
                  ID as id,
                  post_title as title
                FROM $wpdb->posts
                WHERE
                  post_title LIKE '".$pre."$q%' AND
                  $post_types AND
                  post_status = 'publish'
                LIMIT $count
            ";

            $results = $wpdb->get_results($the_query, OBJECT);

            // The Loop
            if ( is_array($results) ) {
                foreach ($results as $res) {
                    $t = strtolower(get_the_title($res->id));
                    if (
                        $q != $t  &&
                        !in_array($t, $this->res) &&
                        ('' != $str = wd_substr_at_word($t, $this->maxCharsPerWord))
                    ) {
	                    if ($this->matchStart && strpos($t, $q) === 0)
		                    $this->res[] = $str;
	                    elseif (!$this->matchStart)
		                    $this->res[] = $str;
                    }
                }
            }
        }

    }
}