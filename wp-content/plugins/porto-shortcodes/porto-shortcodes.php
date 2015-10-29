<?php
/*
Plugin Name: Porto Shortcodes
Plugin URI: http://themeforest.net/user/SW-THEMES
Description: Shortcodes for Porto Wordpress Theme.
Version: 1.3.4
Author: SW-THEMES
Author URI: http://themeforest.net/user/SW-THEMES
*/

// don't load directly
if (!defined('ABSPATH'))
    die('-1');

define('PORTO_SHORTCODES_URL', plugin_dir_url(__FILE__));
define('PORTO_SHORTCODES_PATH', dirname(__FILE__) . '/shortcodes/');
define('PORTO_SHORTCODES_WOO_PATH', dirname(__FILE__) . '/woo_shortcodes/');
define('PORTO_SHORTCODES_LIB', dirname(__FILE__) . '/lib/');
define('PORTO_SHORTCODES_TEMPLATES', dirname(__FILE__) . '/templates/');
define('PORTO_SHORTCODES_WOO_TEMPLATES', dirname(__FILE__) . '/woo_templates/');

class PortoShortcodesClass {

    private $shortcodes = array("porto_block", "porto_container", "porto_animation", "porto_testimonial", "porto_content_box", "porto_history", "porto_grid_container", "porto_grid_item", "porto_links_block", "porto_links_item", "porto_recent_posts", "porto_recent_portfolios", "porto_recent_members", "porto_blog", "porto_portfolios", "porto_faqs", "porto_members", "porto_concept", "porto_map_section", "porto_section", "porto_toggles", "porto_blockquote", "porto_tooltip", "porto_popover", "porto_price_boxes", "porto_price_box","porto_sort_filters","porto_sort_filter","porto_sort_container","porto_sort_item","porto_preview_image");

    private $woo_shortcodes = array("porto_recent_products", "porto_featured_products", "porto_sale_products", "porto_best_selling_products", "porto_top_rated_products", "porto_products", "porto_product_category", "porto_product_attribute", "porto_product", "porto_product_categories", "porto_widget_woo_products", "porto_widget_woo_top_rated_products", "porto_widget_woo_recently_viewed", "porto_widget_woo_recent_reviews", "porto_widget_woo_product_tags");

    function __construct() {

        // Load text domain
        add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );

        // Init plugins
        add_action( 'init', array( $this, 'initPlugin' ) );

        $this->addShortcodes();

        add_action( 'admin_enqueue_scripts', array( $this, 'loadAdminCssAndJs' ) );
        add_filter( 'the_content', array( $this, 'formatShortcodes' ) );
        add_filter( 'widget_text', array( $this, 'formatShortcodes' ) );
    }

    // Init plugins
    function initPlugin() {
        $this->addTinyMCEButtons();
    }

    // load plugin text domain
    function loadTextDomain() {
        load_plugin_textdomain( 'porto-shortcodes', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
    }

    // load css and js
    function loadAdminCssAndJs() {
        wp_register_style( 'porto_shortcodes_admin', PORTO_SHORTCODES_URL . 'assets/css/admin.css' );
        wp_enqueue_style( 'porto_shortcodes_admin' );
    }

    // Add buttons to tinyMCE
    function addTinyMCEButtons() {
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;

        if ( get_user_option('rich_editing') == 'true' ) {
            add_filter( 'mce_external_plugins', array(&$this, 'addTinyMCEJS') );
            add_filter( 'mce_buttons', array(&$this, 'registerTinyMCEButtons') );
        }
    }

    function addTinyMCEJS($plugin_array) {
        if (get_bloginfo('version') >= 3.9)
            $plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes_4.js';
        else
            $plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes.js';

        $plugin_array['porto_shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/porto_shortcodes' . (WP_DEBUG?'':'.min') . '.js';
        return $plugin_array;
    }

    function registerTinyMCEButtons($buttons) {
        array_push($buttons, "porto_shortcodes_button");
        return $buttons;
    }

    // Add shortcodes
    function addShortcodes() {

        if (function_exists('get_plugin_data')) {
            $plugin = get_plugin_data(dirname(__FILE__) . '/porto-shortcodes.php');
            define('PORTO_SHORTCODES_VERSION', $plugin['Version']);
        } else {
            define('PORTO_SHORTCODES_VERSION', '');
        }

        require_once(PORTO_SHORTCODES_LIB . 'functions.php');
        foreach ($this->shortcodes as $shortcode) {
            require_once(PORTO_SHORTCODES_PATH . $shortcode . '.php');
        }
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            foreach ($this->woo_shortcodes as $woo_shortcode) {
                require_once(PORTO_SHORTCODES_WOO_PATH . $woo_shortcode . '.php');
            }
        }
    }

    // Format shortcodes content
    function formatShortcodes($content) {
        $block = join("|", $this->shortcodes);
        // opening tag
        $content = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]", $content);
        // closing tag
        $content = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/","[/$2]", $content);

        $woo_block = join("|", $this->woo_shortcodes);
        // opening tag
        $content = preg_replace("/(<p>)?\[($woo_block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]", $content);
        // closing tag
        $content = preg_replace("/(<p>)?\[\/($woo_block)](<\/p>|<br \/>)/","[/$2]", $content);

        return $content;
    }

}

// Finally initialize code
new PortoShortcodesClass();

global $porto_ult_assets;

add_action('wp_loaded', 'porto_get_ult_path');

function porto_get_ult_path() {
    global $porto_ult_assets;
    if (class_exists('Ultimate_VC_Addons')) {
        $uvc_addons = new Ultimate_VC_Addons();
        $uvc_addons->aio_front_scripts();
        $porto_ult_assets = str_replace('js/', '', $uvc_addons->assets_js);
    }
}