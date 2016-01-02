<?php
/*
Plugin Name: Brainstorm Core
Plugin URI: https://brainstormforce.com
Author: Brainstorm Force
Author URI: https://brainstormforce.com
Version: 1.0
Description: Brainstorm Core
Text Domain: bsf
*/

/*
	Instrunctions - Product Registration & Updater
	# Copy "auto-upadater" folder to admin folder
	# Change "include_once" and "require_once" directory path as per your "auto-updater" path (Line no. 72, 78, 79)

*/
/* product registration */
//add_action( 'init', 'init_bsf_auto_updater' );
//if ( ! function_exists( 'init_bsf_auto_updater' ) ) {
	//function init_bsf_auto_updater() {
		require_once 'auto-update/admin-functions.php';
		require_once 'auto-update/updater.php';
	//}
//}
add_action('admin_init', 'set_bsf_core_constant',1);
	if(!function_exists('set_bsf_core_constant')) {
	function set_bsf_core_constant() {
		if(!defined('BSF_CORE')) {
			define('BSF_CORE',true);
		}
	}
}

if ( ! function_exists( 'register_bsf_products_registration_page' ) ) {
	function register_bsf_products_registration_page() {
		if ( defined( 'BSF_UNREG_MENU' ) && ( BSF_UNREG_MENU === true || BSF_UNREG_MENU === 'true' ) ) {
			return false;
		}
		if ( empty ( $GLOBALS['admin_page_hooks']['bsf-registration'] ) ) {
			$place = bsf_get_free_menu_position( 200, 1 );
			if ( ! defined( 'BSF_MENU_POS' ) ) {
				define( 'BSF_MENU_POS', $place );
			}
			$page = add_dashboard_page( 'Brainstorm Force', 'Brainstorm', 'administrator', 'bsf-registration', 'bsf_registration' );
		}
	}
}
if ( ! function_exists( 'bsf_registration' ) ) {
	function bsf_registration() {
		include_once 'auto-update/index.php';
	}
}

if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'register_bsf_products_registration_page', 98 );
} else {
	add_action( 'admin_menu', 'register_bsf_products_registration_page', 98 );
}

/*
	Instrunctions - Plugin Installer
	# Copy "plugin-installer" folder to theme's admin folder
	# Change "include_once" and "require_once" directory path as per your "plugin-installer" path (Line no. 101, 113)
*/
add_action( 'admin_init', 'init_bsf_plugin_installer' );
if ( ! function_exists( 'init_bsf_plugin_installer' ) ) {
	function init_bsf_plugin_installer() {
		require_once 'plugin-installer/admin-functions.php';
	}
}

if(!is_multisite())
	add_action('admin_menu', 'register_bsf_extension_page',999);
else
	add_action('network_admin_menu', 'register_bsf_extension_page_network',999);
if(!function_exists('register_bsf_extension_page')) {
	function register_bsf_extension_page() {
		add_submenu_page( 'imedica_options', __('Extensions','bsf'), __('Extensions','bsf'), 'manage_options', 'bsf-extensions', 'bsf_extensions_callback' );
	}
}
if(!function_exists('register_bsf_extension_page_network')) {
	function register_bsf_extension_page_network() {
		add_submenu_page( 'bsf-registration', __('Extensions','bsf'), __('Extensions','bsf'), 'manage_options', 'bsf-extensions', 'bsf_extensions_callback' );
	}
}
if ( ! function_exists( 'bsf_extensions_callback' ) ) {
	function bsf_extensions_callback() {
		include_once 'plugin-installer/index.php';
	}
}

if(!function_exists('bsf_extract_product_id')) {
	function bsf_extract_product_id($path) {
		$id = false;
		$file = rtrim($path,'/').'/bsf.yml';
		if(!is_file($file))
			return false;
		$filelines = file_get_contents($file);
		if(stripos($filelines, 'ID:[')) {
			preg_match_all("/ID:\[(.*?)\]/", $filelines, $matches);
			if(isset($matches[1])) {
				$id = (isset($matches[1][0])) ? $matches[1][0] : '';
			}
		}
		return $id;
	}
}

add_action( 'admin_init', 'init_bsf_core' );
if(!function_exists('init_bsf_core')) {
	function init_bsf_core() {
		$plugins = get_plugins();
		$themes = wp_get_themes();

		$bsf_products = array();
		foreach($plugins as $plugin => $plugin_data)
		{
			if(trim($plugin_data['Author']) === 'Brainstorm Force')
			{
				$plugin_data['type'] = 'plugin';
				$plugin_data['template'] = $plugin;
				$plugin_data['path'] = dirname(realpath(WP_PLUGIN_DIR.'/'.$plugin));
				$id = bsf_extract_product_id($plugin_data['path']);
				if($id !== false)
					$plugin_data['id'] = $id; // without readme.txt filename
				array_push($bsf_products, $plugin_data);
			}
		}

		foreach($themes as $theme => $theme_data)
		{
			$temp = array();
			$theme_author = trim($theme_data->display('Author', FALSE));
			if($theme_author === 'Brainstorm Force')
			{
				$temp['Name'] = $theme_data->get('Name');
				$temp['ThemeURI'] = $theme_data->get('ThemeURI');
				$temp['Description'] = $theme_data->get('Description');
				$temp['Author'] = $theme_data->get('Author');
				$temp['AuthorURI'] = $theme_data->get('AuthorURI');
				$temp['Version'] = $theme_data->get('Version');
				$temp['type'] = 'theme';
				$temp['template'] = $theme;
				$temp['path'] = realpath(get_theme_root().'/'.$theme);
				$id = bsf_extract_product_id($temp['path']);
				if($id !== false)
					$temp['id'] = $id; // without readme.txt filename
				array_push($bsf_products, $temp);
			}
		}

		$brainstrom_products = ( get_option( 'brainstrom_products' ) ) ? get_option( 'brainstrom_products' ) : array();

		if(!empty($bsf_products)) {
			foreach ($bsf_products as $key => $product) {
				if(!(isset($product['id'])) || $product['id'] === '')
					continue;
				if(isset($brainstrom_products[$product['type'].'s'][$product['id']]))
					$bsf_product_info = $brainstrom_products[$product['type'].'s'][$product['id']];
				else
					$bsf_product_info = array();
				$bsf_product_info['template'] = $product['template'];
				$bsf_product_info['type'] = $product['type'];
				$bsf_product_info['id'] = $product['id'];
				$brainstrom_products[$product['type'].'s'][$product['id']] = $bsf_product_info;
			}
		}

		update_option('brainstrom_products', $brainstrom_products);
	}
}
if(is_multisite()) {
	$brainstrom_products = (get_option('brainstrom_products')) ? get_option('brainstrom_products') : array();
	if(!empty($brainstrom_products)) {
		$bsf_product_themes = (isset($brainstrom_products['themes'])) ? $brainstrom_products['themes'] : array();
		if(!empty($bsf_product_themes)) {
			foreach ($bsf_product_themes as $id => $theme) {
				global $bsf_theme_template;
				$template = $theme['template'];
				$bsf_theme_template = $template;
			}
		}
	}
}
// assets
add_action( 'admin_enqueue_scripts', 'register_bsf_core_admin_styles', 1 );
if(!function_exists('register_bsf_core_admin_styles')) {
	function register_bsf_core_admin_styles($hook) {
		//echo '--------------------------------------........'.$hook;die();

		// bsf core style
		$hook_array = array(
			'toplevel_page_bsf-registration',
			'imedica_page_bsf-extensions',
			'brainstorm_page_bsf-extensions',
			'update-core.php',
			'dashboard_page_bsf-registration',
			'index_page_bsf-registration',
			'admin_page_bsf-extensions'
		);
		$hook_array = apply_filters('bsf_core_style_screens',$hook_array);
		if(in_array($hook, $hook_array)){
			if(is_file(get_template_directory().'/admin/bsf-core/assets/css/style.css'))
				$path = get_template_directory_uri().'/admin/bsf-core/assets/css/style.css';
			else
				$path = plugin_dir_url( __FILE__ ).'assets/css/style.css';
			wp_register_style( 'bsf-core-admin', $path );
			wp_enqueue_style( 'bsf-core-admin' );
		}

		// frosty script
		$hook_frosty_array = array();
		$hook_frosty_array = apply_filters('bsf_core_frosty_screens',$hook_frosty_array);
		if(in_array($hook, $hook_frosty_array)){
			if(is_file(get_template_directory().'/admin/bsf-core/assets/js/frosty.js'))
				$path = get_template_directory_uri().'/admin/bsf-core/assets/js/frosty.js';
			else
				$path = plugin_dir_url( __FILE__ ).'assets/js/frosty.js';

			if(is_file(get_template_directory().'/admin/bsf-core/assets/css/frosty.css'))
				$css_path = get_template_directory_uri().'/admin/bsf-core/assets/css/frosty.css';
			else
				$css_path = plugin_dir_url( __FILE__ ).'assets/css/frosty.css';

			wp_register_script( 'bsf-core-frosty', $path );
			wp_enqueue_script( 'bsf-core-frosty' );

			wp_register_style( 'bsf-core-frosty-style', $css_path );
			wp_enqueue_style( 'bsf-core-frosty-style' );
		}
	}
}
/*add_action('admin_print_scripts', 'print_bsf_styles');
if(!function_exists('print_bsf_styles')) {
	function print_bsf_styles() {
		if(is_dir(get_template_directory().'/admin/auto-update/fonts'))
			$path = get_template_directory_uri().'/admin/auto-update/fonts';
		else
			$path = plugin_dir_url( __FILE__ ).'fonts';
		echo "<style>
			@font-face {
				font-family: 'brainstorm';
				src:url('".$path."/brainstorm.eot');
				src:url('".$path."/brainstorm.eot') format('embedded-opentype'),
					url('".$path."/brainstorm.woff') format('woff'),
					url('".$path."/brainstorm.ttf') format('truetype'),
					url('".$path."/brainstorm.svg') format('svg');
				font-weight: normal;
				font-style: normal;
			}
			.toplevel_page_bsf-registration > div.wp-menu-image:before {
				content: \"\\e603\" !important;
				font-family: 'brainstorm' !important;
				speak: none;
				font-style: normal;
				font-weight: normal;
				font-variant: normal;
				text-transform: none;
				line-height: 1;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
		</style>";
	}
}*/
?>