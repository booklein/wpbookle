<?php
/**
 * General Functions for AjaxSearchPro
 *
 * Generic Functions for all WPDreams producst
 *
 * @version  1.0
 * @package  AjaxSearchPro/Functions
 * @category Functions
 * @author   Ernest Marcinko
 */

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");


/**
 * Returns all search instances as database rows
 *
 * @return array of search instances
 */
function get_search_instances() {
    global $wpdb;
    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }
    return $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
}

/**
 * Returns all search instances with decoded data
 *
 * @return array of search instances
 */
function get_search_instances_and_data() {
    $instances = get_search_instances();
    foreach ($instances as $k => $instance) {
        $instances[$k]['data'] = json_decode($instance['data'], true);
    }
    return $instances;
}


if (!function_exists("setval_or_getoption")) {
    /**
     * Returns an option value or default if not set
     *
     * Returns an empty string "" if nothing is found
     *
     * @param $options
     * @param $key
     * @return string
     */
    function setval_or_getoption($options, $key)
    {
        if (isset($options) && isset($options[$key]))
            return $options[$key];
        $def_options = get_option('asp_defaults');
        if (isset($def_options[$key]))
            return $def_options[$key];
        else
            return "";
    }
}

if (!function_exists("asp_gen_rnd_str")) {
    function asp_gen_rnd_str($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists("asp_generate_the_css")) {
    /**
     * Generates all Ajax Search Pro CSS code
     */
    function asp_generate_the_css() {
        global $wpdb;

        $css_arr = array();

        if (isset($wpdb->base_prefix)) {
            $_prefix = $wpdb->base_prefix;
        } else {
            $_prefix = $wpdb->prefix;
        }

        $comp_settings = get_option('asp_compatibility');
        $async_load = w_isset_def($comp_settings['css_async_load'], false);

        $search = $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
        if (is_array($search) && count($search)>0) {
            foreach ($search as $s) {
                $s['data'] = json_decode($s['data'], true);
	            // $style and $id needed in the include
                $style = $s['data'];
                $id = $s['id'];
                ob_start();
                include(ASP_PATH . "/css/style.css.php");
                $out = ob_get_contents();
                $css_arr[$id] = $out;
                ob_end_clean();
            }
            // Too big, disabled...
            //update_option('asp_styles_base64', base64_encode($css));
            $css = implode(" ", $css_arr);

            if ( $async_load == 1 ) {
                foreach ($css_arr as $sid => $c) {
                    wpd_put_file(ASP_CSS_PATH . "/async/search".$sid.".css", $c);
                }
            } else {
                wpd_put_file(ASP_CSS_PATH . "/style.instances.css", $css);
            }

            update_option( "asp_media_query", asp_gen_rnd_str() );

            return $css;
        }
    }
}

if (!function_exists("asp_register_wpml_translations")) {
	/**
	 * Registers the WPML translations
	 */
	function asp_register_wpml_translations($params) {
		// No WPML yo..
		if (!function_exists('icl_register_string')) return;

		// These options are registered
		$options_to_reg = array(
			"exactmatchestext" => "Exact matches option",
			"searchinpoststext" => "Search in posts option",
			"searchinpagestext" => "Search in pages option",
			"searchintitletext" => "Search in title option",
			"searchincontenttext" => "Search in content option",
			"searchincommentstext" => "Search in comments option",
			"searchinexcerpttext" => "Search in excerpt option",
			"custom_types_label" => "Custom post types label",
			"exsearchincategoriestext" => "Categories filter box text",
			"exsearchintaxonomiestext" => "Taxonomy filter box text",
			"defaultsearchtext" => "Search bar placeholder text",
			"noresultstext" => "No results text",
			"didyoumeantext" => "Did you mean text",
			"showmoreresultstext" => "More results text",
			"groupbytext" => "Group by header text",
			"blogresultstext" => "Blog results group header",
			"bbpressgroupstext" => "BuddyPress group header",
			"bbpressreplytext" => "BuddyPress activity group header",
			"bbpressuserstext" => "User group header",
			"attachment_group_text" => "Attachment group header",
			"term_group_text" => "Term group header",
			"commentstext" => "Group by comments header",
			"uncategorizedtext" => "Uncategorized group header"
		);

		foreach ($options_to_reg as $key => $option) {
			icl_register_string('ajax-search-pro', $options_to_reg[$key], $params[$key]);
		}

		// Custom post types visible on the front-end
		if (isset($params['selected-showcustomtypes']) && is_array($params['selected-showcustomtypes']))
			foreach ($params['selected-showcustomtypes'] as $data) {
				icl_register_string('ajax-search-pro', $data[0], $data[1]);
			}
	}
}

if (!function_exists('asp_icl_t')) {
	/* Ajax Search pro wrapper for WPML print */
	function asp_icl_t($name, $value) {
		if (function_exists('icl_t'))
			return icl_t('ajax-search-pro', $name, $value);
		return $value;
	}
}

if (!function_exists("asp_generate_html_results")) {
    /**
     * Converts the results array to HTML code
     *
     * Since ASP 4.0 the results are returned as plain HTML codes instead of JSON
     * to allow templating. This function includes the needed template files
     * to generate the correct HTML code. Supports grouping.
     *
     * @since 4.0
     * @param $results
     * @param $s_options
     * @param string $theme
     * @return string
     */
    function asp_generate_html_results($results, $s_options, $theme='vertical') {
        $html = "";
        $theme_path = get_template_directory() . "/asp/";

        if (empty($results) || !empty($results['nores'])) {
            if (!empty($results['keywords'])) {
                $s_keywords = $results['keywords'];
                // Get the keyword suggestions template
                ob_start();
                if ( file_exists( $theme_path . "keyword-suggestions.php" ) )
                    include( $theme_path . "keyword-suggestions.php" );
                else
                    include( ASP_INCLUDES_PATH . "views/results/keyword-suggestions.php" );
                $html .= ob_get_clean();
            } else {
                // No results at all.
                ob_start();
                ob_start();
                if ( file_exists( $theme_path . "keyword-suggestions.php" ) )
                    include( $theme_path . "no-results.php" );
                else
                    include( ASP_INCLUDES_PATH . "views/results/no-results.php" );
                $html .= ob_get_clean();
            }
        } else {
            if (isset($results['grouped'])) {
                foreach($results['items'] as $k=>$g) {
	                // For posts/custom post types we need a deeper loop
	                if (isset($g['digdeeper']) && $g['digdeeper'] == 1) {
		                if (!isset($g['data']['items'])) continue;

		                // Need to call this recursively to avoid excessive code
		                $html .= asp_generate_html_results(array(
			                'grouped'=> 1,
		                    'items' => $g['data']['items'],
			                'digdeeper' => 0
		                ), $s_options, $theme);

	                } else {
		                $group_name = $g['name'];

		                // Get the group headers
		                ob_start();
                        if ( file_exists( $theme_path . "keyword-suggestions.php" ) )
                            include( $theme_path . "group-header.php" );
                        else
		                    include(ASP_INCLUDES_PATH . "views/results/group-header.php");
		                $html .= ob_get_clean();

		                // Get the item HTML
		                foreach($g['data'] as $kk=>$r) {
			                ob_start();
                            if ( file_exists( $theme_path . $theme . ".php" ) )
                                include( $theme_path . $theme . ".php" );
                            else
			                    include( ASP_INCLUDES_PATH . "views/results/" . $theme . ".php" );
			                $html .= ob_get_clean();
		                }

		                // Get the gorup footers
		                ob_start();
                        if ( file_exists( $theme_path . "keyword-suggestions.php" ) )
                            include( $theme_path . "group-footer.php" );
                        else
		                    include( ASP_INCLUDES_PATH . "views/results/group-footer.php" );
		                $html .= ob_get_clean();
	                }
                }
            } else {
                // Get the item HTML
                foreach($results as $k=>$r) {
                    ob_start();
                    if ( file_exists( $theme_path . $theme . ".php" ) )
                        include( $theme_path . $theme . ".php" );
                    else
                        include(ASP_INCLUDES_PATH . "views/results/" . $theme . ".php");
                    $html .= ob_get_clean();
                }
            }
        }
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $html);
    }
}