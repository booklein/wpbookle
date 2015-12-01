<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * Class aspInit
 *
 * AJAX SEARCH PRO initializator Class
 */
class aspInit {

    /**
     * Runs on activation
     */
    function ajaxsearchpro_activate() {

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	    $charset_collate_bin_column = '';
	    $charset_collate = '';

	    if (!empty($wpdb->charset)) {
		    $charset_collate_bin_column = "CHARACTER SET $wpdb->charset";
		    $charset_collate = "DEFAULT $charset_collate_bin_column";
	    }
	    if (strpos($wpdb->collate, "_") > 0) {
		    $charset_collate_bin_column .= " COLLATE " . substr($wpdb->collate, 0, strpos($wpdb->collate, '_')) . "_bin";
		    $charset_collate .= " COLLATE $wpdb->collate";
	    } else {
		    if ($wpdb->collate == '' && $wpdb->charset == "utf8") {
			    $charset_collate_bin_column .= " COLLATE utf8_bin";
		    }
	    }

        if (isset($wpdb->base_prefix)) {
            $_prefix = $wpdb->base_prefix;
        } else {
            $_prefix = $wpdb->prefix;
        }

        $table_name = $_prefix . "ajaxsearchpro";
        $query = "
        CREATE TABLE IF NOT EXISTS `$table_name` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` text NOT NULL,
          `data` text NOT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
      ";
        dbDelta($query);
        $table_name = $_prefix . "ajaxsearchpro_statistics";
        $query = "
        CREATE TABLE IF NOT EXISTS `$table_name` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `search_id` int(11) NOT NULL,
          `keyword` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
          `num` int(11) NOT NULL,
          `last_date` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
      ";
        dbDelta($query);

        $query = "ALTER TABLE `$table_name` MODIFY `keyword` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
        dbDelta($query);
        $wpdb->query($query);

        $table_name = $_prefix . "ajaxsearchpro_priorities";
        $query = "
        CREATE TABLE IF NOT EXISTS `$table_name` (
          `post_id` int(11) NOT NULL,
          `blog_id` int(11) NOT NULL,
          `priority` int(11) NOT NULL,
          PRIMARY KEY (`post_id`, `blog_id`)
        ) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
      ";
        dbDelta($query);

        $query = "SHOW INDEX FROM `$table_name` WHERE KEY_NAME = 'post_blog_id'";
        $index_exists = $wpdb->query($query);
        if ($index_exists == 0) {
            $query = "ALTER TABLE `$table_name` ADD INDEX `post_blog_id` (`post_id`, `blog_id`);";
            $wpdb->query($query);
        }

	    $this->create_index();

        //@deprecated since 4.5
        //$this->fulltext();
        $this->chmod();

        /**
         * If the asp_version option exists, then the plugin was used before.
         * In this case, re-creating the CSS files is highly advised.
         */
        if ( get_option('asp_version') !== false )
            asp_generate_the_css();

        /**
         * Store the version number after everything is done. This is going to help distinguishing
         * stored asp_version from the ASP_CURR_VER variable. These two are different in cases:
         *  - Uninstalling, installing new versions
         *  - Uploading and overwriting old version with a new one
         */
        update_option('asp_version', ASP_CURR_VER);
    }

	/**
	 * Checks and creates the index table if neccessary.
	 */
	function create_index() {
		$indexObj = new asp_indexTable();
		$indexObj->createIndexTable();
	}

    /**
     *  Checks if the user correctly updated the plugin and fixes if not
     */
    function safety_check() {
        $curr_stored_ver = get_option('asp_version', 0);

        // Run the re-activation actions if this is actually a newer version
        if ($curr_stored_ver != ASP_CURR_VER)
            $this->ajaxsearchpro_activate();
    }

    /**
     * Generates the navigation menu
     */
    function navigation_menu() {

        // On the back-end demo we need to let readers access the ASP back-end
        $capability = ASP_DEMO == 1 ? 'read' : 'manage_options';

        if (current_user_can($capability)) {
            if (!defined("EMU2_I18N_DOMAIN")) define('EMU2_I18N_DOMAIN', "");
            add_menu_page(
                __('Ajax Search Pro', EMU2_I18N_DOMAIN),
                __('Ajax Search Pro', EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/settings.php',
                '',
                ASP_URL . 'icon.png',
                "207.9"
            );
	        add_submenu_page(
		        ASP_DIR . '/backend/settings.php',
		        __("Ajax Search Pro", EMU2_I18N_DOMAIN),
		        __("Index Table", EMU2_I18N_DOMAIN),
		        $capability,
		        ASP_DIR . '/backend/index_table.php'
	        );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Priorities", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/priorities.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Search Statistics", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/statistics.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Analytics Integration", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/analytics.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Error check", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/comp_check.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Cache Settings", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/cache_settings.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Performance tracking", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/performance.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Compatibility Settings", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/compatibility_settings.php'
            );
            add_submenu_page(
                ASP_DIR . '/backend/settings.php',
                __("Ajax Search Pro", EMU2_I18N_DOMAIN),
                __("Export/Import", EMU2_I18N_DOMAIN),
                $capability,
                ASP_DIR . '/backend/export_import.php'
            );
	        add_submenu_page(
		        ASP_DIR . '/backend/settings.php',
		        __("Ajax Search Pro", EMU2_I18N_DOMAIN),
		        __("Help & Updates", EMU2_I18N_DOMAIN),
		        $capability,
		        ASP_DIR . '/backend/updates_help.php'
	        );
        }
    }

    /**
     * Extra styles if needed..
     */
    function styles() {
	    // Fallback on IE<=8
	    if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [6-8]/',$_SERVER['HTTP_USER_AGENT']) ) {
		    // Oh, this is IE 8 or below, abort mission
		    return;
	    }
    }

    /**
     * Prints the scripts
     */
    function scripts() {
        global $wp_version;

	    // ------------ Dequeue some scripts causing issues on the back-end --------------
	    wp_dequeue_script( 'otw-admin-colorpicker' );
	    wp_dequeue_script( 'otw-admin-select2' );
	    wp_dequeue_script( 'otw-admin-otwpreview' );
	    wp_dequeue_script( 'otw-admin-fonts');
	    wp_dequeue_script( 'otw-admin-functions');
	    wp_dequeue_script( 'otw-admin-variables');

	    // Fallback on IE<=8
	    if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [6-8]/',$_SERVER['HTTP_USER_AGENT']) ) {
		    // Oh, this is IE 8 or below, abort mission
		    return;
	    }

	    $comp_settings = get_option('asp_compatibility');
        $load_in_footer = w_isset_def($comp_settings['load_in_footer'], 1) == 1 ? true : false;
        $css_async_load = w_isset_def($comp_settings['css_async_load'], 0) == 1 ? true : false;

        if ($comp_settings !== false && isset($comp_settings['loadpolaroidjs']) && $comp_settings['loadpolaroidjs'] == 0) {
            ;
        } else {
            wp_register_script('wpdreams-photostack', ASP_URL . 'js/nomin/photostack.js', array("jquery"), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-photostack');
        }

        $js_source = w_isset_def($comp_settings['js_source'], 'min-scoped');
        $load_noui = w_isset_def($comp_settings['load_noui_js'], 1);
        $load_isotope = w_isset_def($comp_settings['load_isotope_js'], 1);
        $minify_string = (($load_noui == 1) ? '-noui' : '') . (($load_isotope == 1) ? '-isotope' : '');

        if (ASP_DEBUG) $js_source = 'nomin';

        if ( $css_async_load ) {
            wp_register_script('wd-asp-async-loader', ASP_URL . 'js/nomin/async.css.js', array("jquery"), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wd-asp-async-loader');
        }

        if ($js_source == 'nomin' || $js_source == 'nomin-scoped') {
            $prereq = "jquery";
            if ($js_source == "nomin-scoped") {
                $prereq = "wpdreams-aspjquery";
                wp_register_script('wpdreams-aspjquery', ASP_URL . 'js/' . $js_source . '/aspjquery.js', array(), ASP_CURR_VER_STRING, $load_in_footer);
                wp_enqueue_script('wpdreams-aspjquery');
            }

            wp_register_script('wpdreams-gestures', ASP_URL . 'js/' . $js_source . '/jquery.gestures.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-gestures');
            wp_register_script('wpdreams-easing', ASP_URL . 'js/' . $js_source . '/jquery.easing.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-easing');
            /*wp_register_script('wpdreams-mousewheel', ASP_URL . 'js/' . $js_source . '/jquery.mousewheel.js', array($prereq));
            wp_enqueue_script('wpdreams-mousewheel');*/
            wp_register_script('wpdreams-scroll', ASP_URL . 'js/' . $js_source . '/jquery.mCustomScrollbar.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-scroll');
            wp_register_script('wpdreams-highlight', ASP_URL . 'js/' . $js_source . '/jquery.highlight.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-highlight');
            if ($load_noui) {
                wp_register_script('wpdreams-nouislider', ASP_URL . 'js/' . $js_source . '/jquery.nouislider.all.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
                wp_enqueue_script('wpdreams-nouislider');
            }
            if ($load_isotope) {
                wp_register_script('wpdreams-rpp-isotope', ASP_URL . 'js/' . $js_source . '/rpp_isotope.js', array($prereq), ASP_CURR_VER_STRING, $load_in_footer);
                wp_enqueue_script('wpdreams-rpp-isotope');
            }
            wp_register_script('wpdreams-ajaxsearchpro', ASP_URL . 'js/' . $js_source . '/jquery.ajaxsearchpro.js', array($prereq, "wpdreams-scroll"), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-ajaxsearchpro');

            wp_register_script('wpdreams-ajaxsearchpro-widgets', ASP_URL . 'js/' . $js_source . '/asp_widgets.js', array($prereq, "wpdreams-ajaxsearchpro"), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-ajaxsearchpro-widgets');

            wp_register_script('wpdreams-ajaxsearchpro-wrapper', ASP_URL . 'js/' . $js_source . '/asp_wrapper.js', array($prereq, "wpdreams-ajaxsearchpro"), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-ajaxsearchpro-wrapper');
        } else {
            wp_enqueue_script('jquery');
            wp_register_script('wpdreams-ajaxsearchpro', ASP_URL . "js/" . $js_source . "/jquery.ajaxsearchpro" . $minify_string . ".min.js", array(), ASP_CURR_VER_STRING, $load_in_footer);
            wp_enqueue_script('wpdreams-ajaxsearchpro');
        }

        $ajax_url = admin_url('admin-ajax.php');
        if (w_isset_def($comp_settings['usecustomajaxhandler'], 0) == 1) {
            $ajax_url = ASP_URL . 'ajax_search.php';
        }

        if (ASP_DEBUG < 1 && strpos(w_isset_def($comp_settings['js_source'], 'min-scoped'), "scoped") !== false) {
            $scope = "aspjQuery";
        } else {
            $scope = "jQuery";
        }

        /**
         * This stays here for a bit, let customers transition smoothly
         *
         * @deprecated since version 4.5.3
         */
        wp_localize_script('wpdreams-ajaxsearchpro', 'ajaxsearchpro', array(
            'ajaxurl' => $ajax_url,
            'backend_ajaxurl' => admin_url('admin-ajax.php'),
            'js_scope' => $scope
        ));

        // The new variable is ASP
        wp_localize_script('wpdreams-ajaxsearchpro', 'ASP', array(
            'ajaxurl' => $ajax_url,
            'backend_ajaxurl' => admin_url('admin-ajax.php'),
            'js_scope' => $scope,
            'asp_url' => ASP_URL,
            'media_query' => get_option("asp_media_query", "defn")
        ));

    }

    /**
     *  Checks if fulltext is available
     */
    function fulltext() {
        global $wpdb;
        $fulltext = wpdreamsFulltext::getInstance();
        $tables = array('posts');
        $blogs = wpdreams_get_blog_list(0, 'all');

        update_option('asp_fulltext', $fulltext->isFulltextAvailable());
        update_option('asp_fulltext_indexed', 0);
    }

    /**
     *  Tries to chmod the CSS and CACHE directories
     */
    function chmod() {
        if (@chmod(ASP_CSS_PATH, 0755) == false)
            @chmod(ASP_CSS_PATH, 0755);
        if (@chmod(ASP_CSS_PATH . "async/", 0755) == false)
            @chmod(ASP_CSS_PATH . "async/", 0755);
        if (@chmod(ASP_CACHE_PATH, 0755) == false)
            @chmod(ASP_CACHE_PATH, 0755);
    }


    /**
     *  If anything we need in the footer
     */
    function footer() {

    }
}