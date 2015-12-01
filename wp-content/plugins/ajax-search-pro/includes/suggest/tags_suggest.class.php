<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpd_tagsKeywordSuggest')) {
    class wpd_tagsKeywordSuggest extends wpd_keywordSuggestAbstract {

        function __construct($args = array()) {
	        $defaults = array(
		        'maxCount' => 10,
		        'maxCharsPerWord' => 25,
		        'match_start' => false
	        );
	        $args = wp_parse_args( $args, $defaults );

            $this->maxCount = $args['maxCount'];
            $this->maxCharsPerWord = $args['maxCharsPerWord'];
	        $this->matchStart = $args['match_start'];
        }

        function getKeywords($q) {
            $res = array();
            $tags = get_tags(array('search' => $q, 'number'=>$this->maxCount));

            foreach ($tags as $tag) {
                $t = strtolower($tag->name);
	            if (
		            $t != $q &&
		            ('' != $str = wd_substr_at_word($t, $this->maxCharsPerWord))
	            ) {
		            if ($this->matchStart && strpos($t, $q) === 0)
			            $res[] = $str;
		            elseif (!$this->matchStart)
			            $res[] = $str;
	            }
            }
            return $res;
        }

    }
}