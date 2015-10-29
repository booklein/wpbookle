<?php
/*
Plugin Name: Porto Widgets
Plugin URI: http://themeforest.net/user/SW-THEMES
Description: Widgets for Porto Wordpress Theme.
Version: 1.1.3
Author: SW-THEMES
Author URI: http://themeforest.net/user/SW-THEMES
*/

// don't load directly
if (!defined('ABSPATH'))
    die('-1');

define('PORTO_WIDGETS_PATH', dirname(__FILE__) . '/widgets/');

class PortoWidgetsClass {

    private $widgets = array("block", "recent_posts", "recent_portfolios", "twitter_tweets", "contact_info", "follow_us");

    function __construct() {

        // Load text domain
        add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );

        // Init plugins
        add_action( 'init', array( $this, 'initPlugin' ) );

        $this->loadWidgets();
    }

    // Init plugins
    function initPlugin() {

    }

    // load plugin text domain
    function loadTextDomain() {
        load_plugin_textdomain( 'porto-widgets', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
    }

    // Load widgets
    function loadWidgets() {

        foreach ($this->widgets as $widget) {
            require_once(PORTO_WIDGETS_PATH . $widget . '.php');
        }
    }
}

// Finally initialize code
new PortoWidgetsClass();