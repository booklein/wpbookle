<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (!class_exists('wpdreams_searchBlogs')) {
    /**
     * Blog name search class
     *
     * @class       wpdreams_searchBlogs
     * @version     1.0
     * @package     AjaxSearchPro/Classes
     * @category    Class
     * @author      Ernest Marcinko
     */
    class wpdreams_searchBlogs extends wpdreams_search {

        /**
         * The search function
         *
         * @return array of results
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
            $s = $this->s;
            $_s = $this->_s;

            $blogresults = array();

            if (isset($searchData['searchinblogtitles']) && $searchData['searchinblogtitles'] == 1 && is_multisite()) {
                $blog_list = wpdreams_get_blog_list(0, 'all');
                foreach ($blog_list as $bk => $blog) {
                    $_det = get_blog_details($blog['blog_id']);
                    $blog_list[$bk]['name'] = $_det->blogname;
                    $blog_list[$bk]['siteurl'] = $_det->siteurl;
                    $blog_list[$bk]['match'] = 0;
                }

                if (isset($search) && $searchData['exactonly'] != 1) {
                    $_s = explode(" ", $s);
                    foreach ($_s as $keyword) {
                        foreach ($blog_list as $bk => $blog) {
                            if ($blog_list[$bk]['match'] == 1) continue;
                            $pos = strpos(strtolower($blog['name']), $keyword);
                            if ($pos !== false) $blog_list[$bk]['match'] = 1;
                        }
                    }
                }
                foreach ($blog_list as $bk => $blog) {
                    if ($blog_list[$bk]['match'] == 1) continue;
                    $pos = strpos(strtolower($blog['name']), $s);
                    if ($pos !== false) $blog_list[$bk]['match'] = 1;
                }
                foreach ($blog_list as $bk => $blog) {
                    if ($blog_list[$bk]['match'] == 1) {
                        $_blogres = new stdClass();
                        switch_to_blog($blog['blog_id']);
                        $_blogres->title = $blog['name'];
                        $_blogres->link = get_bloginfo('siteurl');
                        $_blogres->content = get_bloginfo('description');
                        $_blogres->author = "";
                        $_blogres->date = "";
                        $_blogres->content_type = "blog";
                        $blogresults[] = $_blogres;
                    }
                }
                if ($searchData['blogtitleorderby'] == 'asc') {
                    $blogresults = array_reverse($blogresults);
                }
                restore_current_blog();
            }
            $this->results = $blogresults;
            return $blogresults;
        }

    }
}