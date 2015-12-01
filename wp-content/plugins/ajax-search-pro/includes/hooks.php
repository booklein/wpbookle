<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

// Override the default search hook, posts_results 

function asp_search_filter_posts($posts, $wp_query) {

    // Not working on paginated results, multisite
    /*if (!$wp_query->is_search())
        return $posts;*/

	// Is this a search query
    if (empty($wp_query->query_vars['s'])) {
	    return $posts;
    }

	$cookie_cleared = false;

	// Is this just a NEW regular search?
	if (
		!isset($_GET['asp_active'])
	) {
		// Then clear the cookie
		setcookie("asp_cookie", "", time()-3600);
		$cookie_cleared = true;
	}

    if (isset($_POST['p_asp_data']) && $_POST['p_asp_data'] != '') {
        $_method = &$_POST;
        parse_str($_method['p_asp_data'], $s_data);
        $_POST['asid'] = $_POST['p_asid'];

	    // this is a new POST search, so set the cookies
	    setcookie("asp_cookie", base64_encode(serialize(array(
		    "asp_data" => $s_data,
		    "asid" => $_POST['p_asid']
	    ))) );
    } else if (isset($_GET['asp_data']) && $_GET['asp_data'] != '') {
        $_method = &$_GET;
        parse_str(base64_decode($_method['asp_data']), $s_data);
        $_POST['asid'] = $s_data['asid'];

	    // this is a new GET search, so set the cookies
	    setcookie("asp_cookie", base64_encode(serialize(array(
		    "asp_data" => $s_data,
		    "asid" => $_POST['p_asid']
	    ))) );
    } else {
		if ( isset($_COOKIE["asp_cookie"]) && !$cookie_cleared ) {
			$c_val = unserialize( base64_decode($_COOKIE["asp_cookie"]) );
			$s_data = $c_val['asp_data'];
			$_POST['asid'] = $c_val['asid'];
		} else {
		    return $posts;
	    }
    }

    $_POST['options'] = $s_data;
	$_POST['options']['non_ajax_search'] = true;
    $_POST['aspp'] = $_GET['s'];
    $_POST['asp_get_as_array'] = 1;

    $post_ids = array();
    $blog_ids = array();

    require_once(ASP_PATH . '/search.php');

    $res = ajaxsearchpro_search(is_multisite(), true);

    if (isset($res['_multi'])) {
        foreach ($res['_multi'] as $k => $v) {
            // Map the result ID's with the blog id
            if (isset($v->post_type) && $v->post_type != "attachment") {
                $post_ids[] = $v->id;
                $blog_ids[] = $v->blogid;
            }
        }
    } else {
        foreach ($res as $k => $v) {
            if (isset($v->post_type) && $v->post_type != "attachment")
                $post_ids[] = $v->id;
        }
    }

	// The get_query_var() is malfunctioning in some cases!!! use $_GET['paged']
    //$paged = (get_query_var('paged') != 0) ? get_query_var('paged') : 1;
    if ( isset($_GET['paged']) ) {
        $paged = $_GET['paged'];
    } else if ( isset($wp_query->query_vars['paged']) ) {
        $paged = $wp_query->query_vars['paged'];
    } else {
        $paged = 1;
    }

    $paged = $paged <= 0 ? 1 : $paged;

    $posts_per_page = (int)get_option('posts_per_page');

    $mod_post_ids = array_slice($post_ids, (($paged-1) * $posts_per_page), $posts_per_page);

    if (count($blog_ids) > 0)
        $mod_blog_ids = array_slice($blog_ids, (($paged-1) * $posts_per_page), $posts_per_page);

    if (empty($mod_post_ids))
        $mod_post_ids[] = -123;

    /**
     * We need to gather the post types, because apparently, this is not working
     * correctly with the "any" argument in some cases...
     */
    $_post_types = array();

    if (isset($s_data['set_inposts']))
        $_post_types[] = 'post';
    if (isset($s_data['set_inpages']))
        $_post_types[] = 'page';
    if (isset($s_data['customset']) && is_array($s_data['customset']))
        $_post_types = array_merge($_post_types, $s_data['customset']);

    if (count($blog_ids) > 0) {
        $n_posts = array();
        foreach($mod_post_ids as $key=>$postid) {
            switch_to_blog($mod_blog_ids[$key]);
            $m_posts = get_posts(array(
                'posts_per_page' => $posts_per_page,
                'post__in' => array($postid),
                'orderby' => 'post__in',
                'ignore_sticky_posts' => true,
                'post_type' => $_post_types
            ));
            // Save the right permalink
            foreach($m_posts as $kk=>$vv) {
                $m_posts[$kk]->asp_guid =  apply_filters('the_permalink', $vv->guid);
            }
            if (is_array($m_posts) && count($m_posts)>0)
                $n_posts = array_merge($n_posts, $m_posts);
            restore_current_blog();
        }

    } else {
        $n_posts = get_posts(array(
            'posts_per_page' => $posts_per_page,
            'post__in' => $mod_post_ids,
            'orderby' => 'post__in',
            'ignore_sticky_posts' => true,
            'post_type' => $_post_types
        ));
    }

    $wp_query->found_posts = count($post_ids);
    if (($wp_query->found_posts / $posts_per_page) > 1)
        $wp_query->max_num_pages = ceil($wp_query->found_posts / $posts_per_page);
    else
        $wp_query->max_num_pages = 0;

    return $n_posts;
}

/* Fix the correct permalink */
function asp_fix_multisite_link( $url, $post, $leavename ) {
    if (isset($post->asp_guid))
        return $post->asp_guid;
    return $url;
}

add_filter('post_link', 'asp_fix_multisite_link', 10, 3 );
add_filter('posts_results', 'asp_search_filter_posts', 1, 2);


function asp_order_posts($posts, $order) {
    $result = array();
    foreach ($order as $id) {
        $i = 0;
        foreach ($posts as $post) {
            if ($post->ID == $id) {
                array_push($result, $post);
                unset($posts[$i]);
                $posts = array_values($posts);
            }
            $i++;
        }
    }
    return $result;
}

function wpdreams_asp_echo_out() {
    global $asp_head_out;
    ?>
    <style type="text/css" xmlns="http://www.w3.org/1999/html">
        <?php echo $asp_head_out; ?>
    </style>
<?php
}

function search_stylesheets() {
    global $wpdb;
    global $asp_head_out;
    global $asp_admin_pages;

    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
        if (isset($screen) && isset($screen->id) && $screen->id == 'widgets')
            return;
    }

    $comp_settings = get_option('asp_compatibility');
    $force_inline = w_isset_def($comp_settings['forceinlinestyles'], false);
    $async_load = w_isset_def($comp_settings['css_async_load'], false);

    // Asynchronous loader enabled, load the basic only, then abort
    if (ASP_DEBUG != 1 && $force_inline != 1 && $async_load) {
        $asp_head_out = ".wpdreams_asp_sc{ display: none; }";
        add_action('wp_head', 'wpdreams_asp_echo_out', 10, 0);
        return;
    }

    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }
    $search = $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
    if (!is_array($search) || count($search)<=0)
        return;

    wp_register_style('wpdreams-asp-basic', ASP_URL . 'css/style.basic.css', array(), ASP_CURR_VER_STRING);
    wp_enqueue_style('wpdreams-asp-basic');

    if (ASP_DEBUG == 1) {

        asp_generate_the_css();
        $css = asp_generate_the_css();
        $asp_head_out = $css;
        add_action('wp_head', 'wpdreams_asp_echo_out', 10, 0);
        return;

    } else if ($force_inline == 1) {

        $css = asp_generate_the_css();
        // If it's still false, we have a problem
        if ($css === false || $css == '') return;

        $asp_head_out = $css;
        add_action('wp_head', 'wpdreams_asp_echo_out', 10, 0);
        return;

    } else if (!file_exists(ASP_CSS_PATH . "/style.instances.css") || @filesize(ASP_CSS_PATH . "/style.instances.css")<1025) {
        /* Check if the CSS exists, if not, then try to force-create it */
        asp_generate_the_css();
        // Check again, if doesn't exist, we need to force inline styles
        if (!file_exists(ASP_CSS_PATH . "/style.instances.css") || @filesize(ASP_CSS_PATH . "/style.instances.css")<1025) {
            $css = asp_generate_the_css();
            // Still no CSS? Problem.
            if ($css === false || $css == '')
                return;
            $asp_head_out = $css;
            add_action('wp_head', 'wpdreams_asp_echo_out', 10, 0);

            // Save the force inline
            $comp_settings['forceinlinestyles'] = 1;
            update_option('asp_compatibility', $comp_settings);

            return;
        }
    }

    wp_enqueue_style('wpdreams-ajaxsearchpro-instances', ASP_URL . 'css/style.instances.css', array(), ASP_CURR_VER_STRING);

}

add_action('wp_enqueue_scripts', 'search_stylesheets');


/**
 * This function handles the index table ajax requests
 */
function asp_indextable_admin_ajax() {

	if (isset($_POST['data'])) {
		if (is_array($_POST['data']))
			$options = $_POST['data'];
		else
			parse_str($_POST['data'], $options);
	} else {
		print "No post data detected, function terminated.";
		die();
	}

	$it_obj = new asp_indexTable(array(
		'index_title' => $options['it_index_title'],
		'index_content' => $options['it_index_content'],
		'index_excerpt' => $options['it_index_excerpt'],
		'index_tags' => $options['it_index_tags'],
		'index_categories' => $options['it_index_categories'],
		'post_types' => $options['it_post_types'],
		'post_statuses' => $options['it_post_statuses'],
		'index_taxonomies' =>$options['it_index_taxonomies'],
		'index_custom_fields' => $options['it_index_customfields'],
		'index_author_name'  => $options['it_index_author_name'],
		'index_author_bio'   => $options['it_index_author_bio'],
        'replace_dash_like'   => $options['it_replace_dash_like'],
		'blog_id' => $_POST['blog_id'],
		'extend' => (w_isset_def($_POST['asp_index_action'], 'new') == 'extend' ? 1 : 0),
		'limit'  => $options['it_limit'],
		'use_stopwords' => $options['it_use_stopwords'],
		'stopwords' => $options['it_stopwords'],
		'min_word_length' => $options['it_min_word_length'],
		'extract_shortcodes' => $options['it_extract_shortcodes'],
		'exclude_shortcodes' => $options['it_exclude_shortcodes']
	));

	if (isset($_POST['asp_index_action'])) {
		switch ($_POST['asp_index_action']) {
			case 'new':
				$ret = $it_obj->newIndex();
				print "New index !!!ASP_INDEX_START!!!";
				print_r(json_encode($ret));
				print "!!!ASP_INDEX_STOP!!!";
				die();
				break;
			case 'extend':
				$ret = $it_obj->extendIndex();
				print "Extend index !!!ASP_INDEX_START!!!";
				print_r(json_encode($ret));
				print "!!!ASP_INDEX_STOP!!!";
				die();
				break;
			case 'switching_blog':
				$ret = $it_obj->extendIndex(true);
				print "Extend index (blog_switch) !!!ASP_INDEX_START!!!";
				print_r(json_encode($ret));
				print "!!!ASP_INDEX_STOP!!!";
				die();
				break;
			case 'delete':
                $ret = $it_obj->emptyIndex();
				print "Delete index !!!ASP_INDEX_START!!!";
                print_r(json_encode($ret));
                print "!!!ASP_INDEX_STOP!!!";
				die();
				break;
		}
	}

	// no action set, or other failure
	print "No action !!!ASP_INDEX_START!!!0!!!ASP_INDEX_STOP!!!";
	die();
}
add_action('wp_ajax_asp_indextable_admin_ajax', 'asp_indextable_admin_ajax');

function asp_update_index_table( $post_id ) {

	if ( wp_is_post_revision( $post_id ) )
		return;

	$it_options = get_option('asp_it_options');

	if ($it_options !== false) {
		$args = array();
		foreach ($it_options as $k => $o) {
			$args[str_replace('it_', '', $k)] = $o;
		}
		$it_o = new asp_indexTable( $args );

		$post_status = get_post_status( $post_id );

		if ($post_status == 'trash') {
			$it_o->removeDocument( $post_id, true );
			return true;
		}

		$it_o->indexDocument( $post_id, true, true );
	}

}
add_action( 'save_post', 'asp_update_index_table' );

function asp_delete_item_from_index_table( $post_id ) {
	$it_o = new asp_indexTable();
	$it_o->removeDocument( $post_id, true );
}
add_action( 'delete_post', 'asp_delete_item_from_index_table' );

function asp_updates_manager() {
	new asp_updates_manager(ASP_PLUGIN_NAME, ASP_PLUGIN_SLUG, asp_updates());
}
add_action( 'init', 'asp_updates_manager');

// Index Table CRON
if ( !wp_next_scheduled( 'asp_cron_it_extend' ) ) {

    $asp_it_options = get_option('asp_it_options');

    if ($asp_it_options !== false) {
        if ( w_isset_def($asp_it_options['it_cron_enable'], 0) == 1 )
            wp_schedule_event( time(), w_isset_def($asp_it_options['it_cron_period'], "hourly"), 'asp_cron_it_extend' );
    }
}

add_action( 'asp_cron_it_extend', 'asp_it_extend' );

function asp_it_extend() {
    $asp_it_options = get_option('asp_it_options');

    if ($asp_it_options !== false) {
        $args = array();
        foreach ($asp_it_options as $k => $o) {
            $args[str_replace('it_', '', $k)] = $o;
        }
        $it_obj = new asp_indexTable( $args );
        $res = $it_obj->extendIndex( );
        update_option("asp_it_cron", array(
            "last_run"  => time(),
            "result"    => $res
        ));
    }

}