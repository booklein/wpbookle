<?php

/**
 * Porto Theme Options
 */

require_once( porto_admin . '/functions.php' );

// include redux framework core functions
require_once( porto_admin . '/ReduxCore/framework.php' );

// porto theme settings options
require_once( porto_admin . '/porto/settings.php' );

require_once( porto_admin . '/porto/save_settings.php' );

if (get_option('porto_init_theme', '0') != '1') {
    porto_check_theme_options();
}

// regenerate default css, skin css files after update theme
$porto_cur_version = get_option('porto_version', '1.0');
if ( version_compare(porto_version, $porto_cur_version, '>') && version_compare( phpversion(), '5.3', '>=') ) {

    update_option('porto_version', porto_version);

    @ini_set('max_execution_time', '10000');
    @ini_set('memory_limit', '256M');

    // regenerate default css
    if (file_exists(porto_dir.'/css/plugins_rtl_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/plugins_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_rtl_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_rtl_shop_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_shop_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_rtl_bbpress_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/theme_bbpress_'.porto_get_blog_id().'.css')) {
        porto_compile_css(true);
    }

    // regenerate skin css
    if (file_exists(porto_dir.'/css/skin_rtl_'.porto_get_blog_id().'.css') ||
        file_exists(porto_dir.'/css/skin_'.porto_get_blog_id().'.css')) {
        porto_save_theme_settings();
    }
}
update_option('porto_version', porto_version);
