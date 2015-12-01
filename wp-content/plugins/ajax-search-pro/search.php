<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

add_action('wp_ajax_nopriv_ajaxsearchpro_autocomplete', 'ajaxsearchpro_autocomplete');
add_action('wp_ajax_ajaxsearchpro_autocomplete', 'ajaxsearchpro_autocomplete');
add_action('wp_ajax_nopriv_ajaxsearchpro_search', 'ajaxsearchpro_search');
add_action('wp_ajax_ajaxsearchpro_search', 'ajaxsearchpro_search');
add_action('wp_ajax_ajaxsearchpro_preview', 'ajaxsearchpro_preview');
add_action('wp_ajax_ajaxsearchpro_precache', 'ajaxsearchpro_precache');
add_action('wp_ajax_ajaxsearchpro_deletecache', 'ajaxsearchpro_deletecache');

require_once(ASP_PATH . "/includes/suggest/suggest.inc.php");

require_once(ASP_PATH . "/includes/etc/performance.class.php");

require_once(ASP_PATH . "/includes/imagecache.class.php");
require_once(ASP_PATH . "/includes/bfi_thumb.php");
require_once(ASP_PATH . "/includes/textcache.class.php");

/* Include the search core classes */
require_once(ASP_PATH . "/includes/search/search.class.php");
require_once(ASP_PATH . "/includes/search/search_blogs.class.php");
require_once(ASP_PATH . "/includes/search/search_content.class.php");
require_once(ASP_PATH . "/includes/search/search_content_fulltext.class.php");
require_once(ASP_PATH . "/includes/search/search_indextable.class.php");
require_once(ASP_PATH . "/includes/search/search_demo.class.php");
require_once(ASP_PATH . "/includes/search/search_comments.class.php");
require_once(ASP_PATH . "/includes/search/search_buddypress.class.php");
require_once(ASP_PATH . "/includes/search/search_terms.class.php");
require_once(ASP_PATH . "/includes/search/search_users.class.php");
require_once(ASP_PATH . "/includes/search/search_attachments.class.php");


if (!function_exists('ajaxsearchpro_search')) {
    function ajaxsearchpro_search($forceMulti = false, $dontGroup = false) {
        global $wpdb;
        global $switched;
        global $search;

        $multi_posts = array(); // Posts/Custom Posts arranged by Multisite id

        $perf_options = get_option('asp_performance');
	    $caching_options = w_boolean_def(get_option('asp_caching'), get_option('asp_caching_def'));

        if (w_isset_def($perf_options['enabled'], 1)) {
            $performance = new wpd_Performance('asp_performance_stats');
            $performance->start_measuring();
        }

        /*print "in ajaxsearchpro_search();";
        print_r(array()); return;  */

        $s = $_POST['aspp'];
        $s = apply_filters('asp_search_phrase_before_cleaning', $s);

        $s = stripcslashes($s);
        $s = trim($s);
        $s = preg_replace('/\s+/', ' ', $s);

        $s = apply_filters('asp_search_phrase_after_cleaning', $s);

        $id = (int)$_POST['asid'];

        $stat = get_option("asp_stat");
        if (isset($wpdb->base_prefix)) {
            $_prefix = $wpdb->base_prefix;
        } else {
            $_prefix = $wpdb->prefix;
        }
        if ($stat == 1) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $in = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE " . $_prefix . "ajaxsearchpro_statistics SET num=num+1, last_date=%d WHERE (keyword='%s' AND search_id=%d)",
                    time(),
                    strip_tags($s),
                    $id
                )
            );
            if ($in == false) {
                $wpdb->query(
                    $wpdb->prepare(
                        "INSERT INTO " . $_prefix . "ajaxsearchpro_statistics (search_id, keyword, num, last_date) VALUES (%d, '%s', 1, %d)",
                        $id,
                        strip_tags($s),
                        time()
                    )
                );
            }
        }

        $def_data = get_option('asp_defaults');
        $search = $wpdb->get_row("SELECT * FROM " . $_prefix . "ajaxsearchpro WHERE id=" . $id, ARRAY_A);
        $search['data'] = json_decode($search['data'], true);
        $search['data'] = array_merge($def_data, $search['data']);

        //var_dump(current_user_can("manage_options")); die();
        // See if we post the preview data through
        if (!empty($_POST['asp_preview_options']) && current_user_can("manage_options")) {
            //$search['data'] = array_merge($search['data'], unserialize(base64_decode($_POST['asp_preview_options'])));
            $search['data'] = array_merge($search['data'], unserialize(base64_decode($_POST['asp_preview_options'])));
        }

        $sd = $search['data'];

        $search['data']['image_options'] = array(
	        'image_cropping' => w_isset_def($caching_options['image_cropping'], 1),
            'show_images' => $search['data']['show_images'],
            'image_bg_color' => $search['data']['image_bg_color'],
            'image_transparency' => $search['data']['image_transparency'],
            'image_crop_location' => w_isset_def($search['data']['image_crop_location'], "c"),
            'apply_content_filter' => w_isset_def($search['data']['image_apply_content_filter'], 0),
            'image_width' => $search['data']['image_width'],
            'image_height' => $search['data']['image_height'],
            'image_source1' => $search['data']['image_source1'],
            'image_source2' => $search['data']['image_source2'],
            'image_source3' => $search['data']['image_source3'],
            'image_source4' => $search['data']['image_source4'],
            'image_source5' => $search['data']['image_source5'],
            'image_default' => $search['data']['image_default'],
            'image_custom_field' => $search['data']['image_custom_field']
        );

        if (isset($_POST['asp_get_as_array']))
            $search['data']['image_options']['show_images'] = 0;

        // ----------------- Recalculate image width/height ---------------
        switch ($search['data']['resultstype']) {
            case "horizontal":
                /* Same width as height */
                $search['data']['image_options']['image_width'] = wpdreams_width_from_px($search['data']['hreswidth']);
                $search['data']['image_options']['image_height'] = wpdreams_width_from_px($search['data']['hreswidth']);
                break;
            case "polaroid":
                $search['data']['image_options']['image_width'] = intval($search['data']['preswidth']);
                $search['data']['image_options']['image_height'] = intval($search['data']['preswidth']);
                break;
            case "isotopic":
                $search['data']['image_options']['image_width'] = intval($search['data']['i_item_width'] * 1.5);
                $search['data']['image_options']['image_height'] = intval($search['data']['i_item_height'] * 1.5);
                break;
        }

        if (isset($search['data']['selected-imagesettings'])) {
            $search['data']['settings-imagesettings'] = $search['data']['selected-imagesettings'];
        }
        /*if (isset($search) && $search['data']['exactonly']!=1) {
          $_s = explode(" ", $s);
        }*/
        if (isset($_POST['options'])) {
            if (is_array($_POST['options']))
                $search['options'] = $_POST['options'];
            else
                parse_str($_POST['options'], $search['options']);
        }


        $blogresults = array();

        $allbuddypresults = array(
            'repliesresults' => array(),
            'groupresults' => array(),
            'userresults' => array(),
            'activityresults' => array()
        );

        $alltermsresults = array();
        $allpageposts = array();
        $pageposts = array();
        $repliesresults = array();
        $allcommentsresults = array();
        $commentsresults = array();
        $userresults = array();
	    $attachment_results = array();

	    $search['data']['selected-blogs'] = w_isset_def($search['data']['selected-blogs'], array(0 => get_current_blog_id()));

        if ($search['data']['selected-blogs'] === "all") {
	        if (is_multisite())
	            $search['data']['selected-blogs'] = wpdreams_get_blog_list(0, "all", true);
	        else
		        $search['data']['selected-blogs'] = array(0 => get_current_blog_id());
        }
	    if (count($search['data']['selected-blogs']) <= 0) {
            $search['data']['selected-blogs'] = array(0 => get_current_blog_id());
        }

        do_action('asp_before_search', $s);

        if (is_array($caching_options) && w_isset_def($caching_options['caching'], 0) && ASP_DEBUG != 1) {
            $filename = ASP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . md5(json_encode($search['options']) . $s . $_POST['asp_inst_id']) . ".wp";
            $textcache = new wpdreamsTextCache($filename, w_isset_def($caching_options['cachinginterval'], 3600) * 60);
            $cache_content = $textcache->getCache();
            if ($cache_content != false) {
                $cache_content = apply_filters('asp_cached_content', $cache_content);
                do_action('asp_after_search', $s, json_decode($cache_content, true));
                print "cached(" . date("F d Y H:i:s.", filemtime($filename)) . ")";
                print_r($cache_content);
                die;
            }
        }

	    // True if only CPT search in the index table is active
	    $search_only_it_posts =
		    w_isset_def($search['data']['search_engine'], 'regular') != 'regular' &&
		    w_isset_def($search['data']['return_categories'], 0) == 0 &&
		    w_isset_def($search['data']['return_terms'], '') == '' &&
		    w_isset_def($search['data']['return_attachments'], 0) == 0;

	    if (is_multisite() && $search_only_it_posts) {
		    // Save huge amounts of server resources by not swapping all the blogs around
		    if ($_POST['asid'] == "") {
			    $_dposts      = new wpdreams_searchDemo( array() );
			    $dpageposts   = $_dposts->search( $s );
			    $allpageposts = array_merge( $allpageposts, $dpageposts );
		    } else {
			    $search['options']['switch_on_preprocess'] = 1;
                $search['options']['dont_group'] = $dontGroup;
			    $params = array(
                    'id' => $id,
                    'data' => $search['data'],
                    'options' => $search['options']
                );
			    $_posts = new asp_searchIndexTable($params);
			    $pageposts = $_posts->search($s);

			    do_action('asp_after_pagepost_results', $s, $pageposts);
			    $allpageposts = array_merge( $allpageposts, $pageposts );
		    }
	    } else {
			// Regularly swap through blogs
		    foreach ($search['data']['selected-blogs'] as $blog) {
			    if (is_multisite()) switch_to_blog($blog);

			    aspDebug::start('search-blog-' . $blog);

			    if ($_POST['asid'] == "") {
				    $_dposts = new wpdreams_searchDemo(array());
				    $dpageposts = $_dposts->search($s);
				    $allpageposts = array_merge($allpageposts, $dpageposts);
			    } else {

                    $search['options']['dont_group'] = $dontGroup;
				    $params = array(
                        'id' => $id,
                        'data' => $search['data'],
                        'options' => $search['options']
                    );

				    $_terms = new wpdreams_searchTerms($params);
				    $termsresults = $_terms->search($s);
				    $alltermsresults = array_merge($alltermsresults, $termsresults);

				    // For exact matches the regular engine is used
				    if (
					    w_isset_def($search['data']['search_engine'], 'regular') == 'regular' ||
					    isset($params['options']['set_exactonly'])
				    )
					    $_posts = new wpdreams_searchContent($params);
				    else
					    $_posts = new asp_searchIndexTable($params);
				    $pageposts = $_posts->search($s);

				    //var_dump($pageposts);die();
				    $allpageposts = array_merge($allpageposts, $pageposts);
				    if ($forceMulti) {
					    foreach ($pageposts as $kk => $vv)
						    $pageposts[$kk]->blogid = $blog;
					    $multi_posts[$blog] = $pageposts;
				    }
				    do_action('asp_after_pagepost_results', $s, $pageposts);

				    $_comments = new wpdreams_searchComments($params);
				    $commentsresults = $_comments->search($s);
				    $allcommentsresults = array_merge($allcommentsresults, $commentsresults);


				    if (w_isset_def($search['data']['return_attachments'], 0)) {
					    $_attachments = new wpdreams_searchAttachments($params);
					    $attachment_results = array_merge($attachment_results, $_attachments->search($s));
				    }

				    do_action('asp_after_attachment_results', $s, $attachment_results);

			    }
		    }

	    }

	    do_action('asp_after_comments_results', $s, $commentsresults);

	    $_buddyp = new wpdreams_searchBuddyPress($params);
	    $buddypresults = $_buddyp->search($s); // !!! returns array for each result (group, user, reply) !!!
	    foreach ($buddypresults as $k => $v) {
		    $allbuddypresults[$k] = array_merge($allbuddypresults[$k], $v);
	    }

	    do_action('asp_after_buddypress_results', $s, $buddypresults);

	    if (w_isset_def($search['data']['user_search'], 0)) {
		    $_users = new wpdreams_searchUsers($params);
		    $userresults = $_users->search($s);
	    }

	    do_action('asp_after_user_results', $s, $userresults);


        aspDebug::stop('search-blog-' . $blog);

        if (is_multisite()) restore_current_blog();

        $_blogs = new wpdreams_searchBlogs($params);
        $blogresults = $_blogs->search($s);


        $alltermsresults = apply_filters('asp_terms_results', $alltermsresults, $id);
        $allpageposts = apply_filters('asp_pagepost_results', $allpageposts, $id);
        $allcommentsresults = apply_filters('asp_comment_results', $allcommentsresults, $id);
        $buddypresults = apply_filters('asp_buddyp_results', $buddypresults, $id);
        $blogresults = apply_filters('asp_blog_results', $blogresults, $id);
        $userresults = apply_filters('asp_user_results', $userresults, $id);
	    $attachment_results = apply_filters('asp_attachment_results', $attachment_results, $id);

        /* Remove the results in polaroid mode */
        if ($search['data']['resultstype'] == 'polaroid' && $search['data']['pifnoimage'] == 'removeres') {
            foreach ($allpageposts as $_k => $_v) {
                if ($_v->image == null || $_v->image == '')
                    unset($allpageposts[$_k]);
            }
            foreach ($allcommentsresults as $_k => $_v) {
                if ($_v->image == null || $_v->image == '')
                    unset($allcommentsresults[$_k]);
            }
            foreach ($buddypresults as $_k => $_v) {
                if ($_v->image == null || $_v->image == '')
                    unset($buddypresults[$_k]);
            }
            foreach ($blogresults as $_k => $_v) {
                if ($_v->image == null || $_v->image == '')
                    unset($blogresults[$_k]);
            }
        }

        // Results as array, unordered
        $results_arr = array(
            'terms' => $alltermsresults,
            'blogs' => $blogresults,
            'bp_activities' => $allbuddypresults['activityresults'],
            'comments' => $allcommentsresults,
            'bp_groups' => $allbuddypresults['groupresults'],
            'bp_users' => $userresults,
            'post_page_cpt' => $allpageposts,
	        'attachments' => $attachment_results
        );

        $results_order = w_isset_def($search['data']['results_order'], 'terms|blogs|bp_activities|comments|bp_groups|bp_users|post_page_cpt|attachments');

	    if (strpos($results_order, 'attachments') === false)
		    $results_order .= "|attachments";

        // These keys are in the right order
        $results_order_arr = explode('|', $results_order);


        // Grouping again, respecting ordering
        if ($search['data']['resultstype'] == 'vertical' && ($search['data']['groupby'] == 1 || $search['data']['groupby'] == 2) && !$forceMulti) {

	        $results = $allpageposts;

	        $results['items'] = array();
	        $results['grouped'] = 1;

	        // CPM
	        if (count($allpageposts) > 0) {
		        $i = 9000 + strpos($results_order, 'post_page_cpt');
		        $results['items'][$i] = array();
		        $results['items'][$i]['data'] = $allpageposts;
		        $results['items'][$i]['digdeeper'] = 1;
	        }

	        // Term results
	        if (count($alltermsresults) > 0) {
		        $num = $search['data']['showpostnumber'] == 1 ? " (" . count($alltermsresults) . ")" : "";
		        $i = 9000 + strpos($results_order, 'terms');
		        $results['items'][$i] = array();
		        $results['items'][$i]['name'] = asp_icl_t( "Term group header", w_isset_def($search['data']['term_group_text'], 'Terms' ) ) . $num;
		        $results['items'][$i]['data'] = $alltermsresults;
		        $results['items'][$i]['digdeeper'] = 0;
		        $results['grouped'] = 1;
	        }

	        // Blog results
            if (count($blogresults) > 0) {
	            $num = $search['data']['showpostnumber'] == 1 ? " (" . count($blogresults) . ")" : "";
	            $i = 9000 + strpos($results_order, 'blogs');
                $results['items'][$i] = array();
                $results['items'][$i]['name'] = asp_icl_t("Blog results group header", $search['data']['blogresultstext']) . $num;
                $results['items'][$i]['data'] = $blogresults;
	            $results['items'][$i]['digdeeper'] = 0;
                $results['grouped'] = 1;
            }

	        // Activity results
            if (count($allbuddypresults['activityresults']) > 0) {
	            $num = $search['data']['showpostnumber'] == 1 ? " (" . count($allbuddypresults['activityresults']) . ")" : "";
	            $i = 9000 + strpos($results_order, 'bp_activities');
	            $results['items'][$i] = array();
	            $results['items'][$i]['name'] = asp_icl_t( "BuddyPress activity group header",$search['data']['bbpressreplytext'] ) . $num;
	            $results['items'][$i]['data'] = $allbuddypresults['activityresults'];
	            $results['items'][$i]['digdeeper'] = 0;
                $results['grouped'] = 1;
            }

	        // Comments results
            if (count($allcommentsresults) > 0) {
	            $num = $search['data']['showpostnumber'] == 1 ? " (" . count($allcommentsresults) . ")" : "";
	            $i = 9000 + strpos($results_order, 'comments');
                $results['items'][$i] = array();
                $results['items'][$i]['name'] = asp_icl_t( "Group by comments header", $search['data']['commentstext'] ) . $num;
                $results['items'][$i]['data'] = $allcommentsresults;
	            $results['items'][$i]['digdeeper'] = 0;
                $results['grouped'] = 1;
            }

	        // Buddypress groups
            if (count($allbuddypresults['groupresults']) > 0) {
	            $num = $search['data']['showpostnumber'] == 1 ? " (" . count($allbuddypresults['groupresults']) . ")" : "";
	            $i = 9000 + strpos($results_order, 'bp_groups');
	            $results['items'][$i] = array();
	            $results['items'][$i]['name'] = asp_icl_t("BuddyPress group header", $search['data']['bbpressgroupstext']) . $num;
	            $results['items'][$i]['data'] = $allbuddypresults['groupresults'];
	            $results['items'][$i]['digdeeper'] = 0;
                $results['grouped'] = 1;
            }

	        // Users
            if (count($userresults) > 0) {
	            $num = $search['data']['showpostnumber'] == 1 ? " (" . count($userresults) . ")" : "";
	            $i = 9000 + strpos($results_order, 'bp_users');
	            $results['items'][$i] = array();
	            $results['items'][$i]['name'] = asp_icl_t("User group header", $search['data']['bbpressuserstext']) . $num;
	            $results['items'][$i]['data'] = $userresults;
	            $results['items'][$i]['digdeeper'] = 0;
                $results['grouped'] = 1;
            }

	        // Attachments
	        if (count($attachment_results) > 0) {
		        $num = $search['data']['showpostnumber'] == 1 ? " (" . count($attachment_results) . ")" : "";
		        $i = 9000 + strpos($results_order, 'attachments');
		        $results['items'][$i] = array();
		        $results['items'][$i]['name'] = asp_icl_t( "Attachment group header", w_isset_def($search['data']['attachment_group_text'], 'Attachments') ) . $num;
		        $results['items'][$i]['data'] = $attachment_results;
		        $results['items'][$i]['digdeeper'] = 0;
		        $results['grouped'] = 1;
	        }

	        if (isset($results['items']) && count($results['items'])>0)
	            ksort($results['items']);
	        else
		        $results = array();

        } else {
            $results = array();
            foreach ($results_order_arr as $rk => $rv) {
                $results = array_merge($results, $results_arr[$rv]);
            }
        }


        if (count($results) <= 0 && $search['data']['keywordsuggestions']) {
            $keywords = array();
            $types = array();

            if ($sd['searchinposts'] == 1)
                $types[] = "post";
            if ($sd['searchinpages'] == 1)
                $types[] = "page";
            if (isset($sd['selected-customtypes']) && count($sd['selected-customtypes']) > 0)
                $types = array_merge($types, $sd['selected-customtypes']);

            foreach (w_isset_def($search['data']['selected-keyword_suggestion_source'], array('google')) as $source) {
                $remaining_count = w_isset_def($sd['keyword_suggestion_count'], 10) - count($keywords);
                if ($remaining_count <= 0) break;

	            $taxonomy = "";
	            // Check if this is a taxonomy
	            if (strpos($source, 'xtax_') !== false) {
		            $taxonomy = str_replace('xtax_', '', $source);
		            $source = "terms";
	            }

                //$class_name = "wpd_" . $source . "KeywordSuggest";

                $t = new  wpd_keywordSuggest($source, array(
                    'maxCount' => $remaining_count,
                    'maxCharsPerWord' => w_isset_def($sd['keyword_suggestion_length'], 50),
                    'postTypes' => $types,
                    'lang' => $sd['keywordsuggestionslang'],
                    'overrideUrl' => '',
	                'taxonomy' => $taxonomy
                ));

                $keywords = array_merge($keywords, $t->getKeywords($s));

            }

            if ($keywords != false) {
                $results['keywords'] = $keywords;
                $results['nores'] = 1;
                $results = apply_filters('asp_only_keyword_results', $results);
            }
        } else if (count($results > 0)) {
            $results = apply_filters('asp_only_non_keyword_results', $results);
        }


        $results = apply_filters('asp_results', $results, $id);

        do_action('asp_after_search', $s, $results, $id);

        //
        if (w_isset_def($perf_options['enabled'], 1)) {
            $performance->stop_measuring();
            //$performance->dump_data();
        }

        // If preview, we need the details
        if (isset($_POST['action']) && $_POST['action'] == "ajaxsearchpro_preview") {
            print "wassup!";
            require_once(ASP_PATH . "backend" . DIRECTORY_SEPARATOR . "settings" . DIRECTORY_SEPARATOR . "types.inc.php");
            parse_str($_POST['formdata'], $preview_params);
            $preview_params = wpdreams_parse_params($preview_params);
            $preview_params = wp_parse_args( $preview_params, $search['data'] );
            $html_results = asp_generate_html_results($results, $preview_params, $search['data']['resultstype']);
        } else {
            $html_results = asp_generate_html_results($results, $search['data'], $search['data']['resultstype']);
        }


        if (is_array($caching_options) && w_isset_def($caching_options['caching'], 0) && ASP_DEBUG != 1)
            $cache_content = $textcache->setCache('!!ASPSTART!!' . $html_results . "!!ASPEND!!");

        // Override from hooks.php
        if (isset($_POST['asp_get_as_array'])) {
            if ($forceMulti)
                $results['_multi'] = $results;
            return $results;
        }

        /* Clear output buffer, possible warnings */
        print "!!ASPSTART!!";
        //var_dump($results);die();
        //print_r(json_encode($results));
        print_r($html_results);
        print "!!ASPEND!!";
        die();
    }
}

function ajaxsearchpro_autocomplete() {

    global $wpdb;
    $s = trim($_POST['sauto']);
    $s = preg_replace('/\s+/', ' ', $s);

    do_action('asp_before_autocomplete', $s);

    if (!isset($_POST['asid'])) return "";

    // Set the prefix for multisites
    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }

    // Forcing numeric value
    $_POST['asid'] = $_POST['asid'] + 0;

    $search = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM " . $_prefix . "ajaxsearchpro WHERE id=%d",
        $_POST['asid']
    ), ARRAY_A);

    if (!isset($search['data']))
        return;

    $sd = json_decode($search['data'], true);

    $keyword = '';
    $types = array();

    if ($sd['searchinposts'] == 1)
        $types[] = "post";
    if ($sd['searchinpages'] == 1)
        $types[] = "page";
    if (isset($sd['selected-customtypes']) && count($sd['selected-customtypes']) > 0)
        $types = array_merge($types, $sd['selected-customtypes']);

    foreach (w_isset_def($sd['selected-autocomplete_source'], array('google')) as $source) {

	    $taxonomy = "";
	    // Check if this is a taxonomy
	    if (strpos($source, 'xtax_') !== false) {
		    $taxonomy = str_replace('xtax_', '', $source);
		    $source = "terms";
	    }

        $t = new  wpd_keywordSuggest($source, array(
            'maxCount' => 10,
            'maxCharsPerWord' => w_isset_def($sd['autocomplete_length'], 60),
            'postTypes' => $types,
            'lang' => $sd['keywordsuggestionslang'],
            'overrideUrl' => '',
	        'taxonomy' => $taxonomy,
            'match_start' => true
        ));

        $res = $t->getKeywords($s);
        if (isset($res[0]) && $keyword = $res[0])
            break;
    }

    do_action('asp_after_autocomplete', $s, $keyword);
    print $keyword;
    die();

}

function ajaxsearchpro_preview() {
    require_once(ASP_PATH . "/includes/shortcodes.php");
    $o = aspShortcodeContainer::get_instance();
    $out = $o->wpdreams_asp_shortcode(array(
        "id" => $_POST['asid']
    ));
    print $out;
    die();
}

/* Delete the cache */
function ajaxsearchpro_deletecache() {
    $count = 0;
    function delTree($dir) {
        $count = 0;
        $files = @glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/')
                @delTree($file);
            else
                @unlink($file);
            $count++;
        }
        return $count;
    }

    print delTree(ASP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR);
    exit;
}

function ajaxsearchpro_deletekeyword() {
    global $wpdb;
    if (isset($_POST['keywordid'])) {
        $id = $_POST['keywordid'] + 0; // injection protection
        echo $wpdb->query( $wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "ajaxsearchpro_statistics WHERE id=%d", $id) ) ;
        exit;
    }
    echo 0;
    exit;
}