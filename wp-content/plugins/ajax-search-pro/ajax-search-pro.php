<?php
/*
Plugin Name: Ajax Search Pro
Plugin URI: http://wp-dreams.com
Description: The most powerful ajax powered live search engine for WordPress.
Version: 4.6
Author: Ernest Marcinko
Author URI: http://wp-dreams.com
*/
?>
<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

define('ASP_PATH', plugin_dir_path(__FILE__));
define('ASP_CSS_PATH', plugin_dir_path(__FILE__)."/css/");
define('ASP_CACHE_PATH', plugin_dir_path(__FILE__)."/cache/");
define('ASP_INCLUDES_PATH', plugin_dir_path(__FILE__)."/includes/");
define('ASP_TT_CACHE_PATH', plugin_dir_path(__FILE__)."/includes/cache/");
define('ASP_DIR', 'ajax-search-pro');
define('ASP_PLUGIN_NAME', 'ajax-search-pro/ajax-search-pro.php');
define('ASP_URL',  plugin_dir_url(__FILE__));
define('ASP_CURR_VER', 4605);
define('ASP_CURR_VER_STRING', "4.6.0");
define('ASP_PLUGIN_SLUG', plugin_basename(__FILE__) );
define('ASP_DEBUG', 0);
define('ASP_DEMO', get_option('wd_asp_demo', 0) );

global $asp_admin_pages;

$asp_admin_pages = array(
    "ajax-search-pro/backend/settings.php",
    "ajax-search-pro/backend/priorities.php",
	"ajax-search-pro/backend/index_table.php",
    "ajax-search-pro/backend/statistics.php",
    "ajax-search-pro/backend/analytics.php",
    "ajax-search-pro/backend/comp_check.php",
    "ajax-search-pro/backend/cache_settings.php",
    "ajax-search-pro/backend/performance.php",
    "ajax-search-pro/backend/compatibility_settings.php",
	"ajax-search-pro/backend/updates_help.php",
    "ajax-search-pro/backend/export_import.php"
);

require_once(ASP_PATH . "/includes/asp_init.class.php");
require_once(ASP_PATH . "/functions.php");
require_once(ASP_PATH . "/includes/functions/general.php");
require_once(ASP_PATH . "/includes/functions/export_import.php");
require_once(ASP_PATH . "/backend/settings/functions.php");
require_once(ASP_PATH . "/includes/wpdreams-fulltext.class.php");
require_once(ASP_PATH . "/includes/aspdebug.class.php");

// This must be here!! If it's in a conditional statement, it will fail..
require_once(ASP_PATH . "/backend/vc/vc.extend.php");

/* Includes only on ASP ajax requests  */
if (isset($_POST) && isset($_POST['action']) &&
    (
        $_POST['action'] == 'ajaxsearchpro_search' ||
        $_POST['action'] == 'ajaxsearchpro_autocomplete' ||
        $_POST['action'] == 'ajaxsearchpro_preview' ||
        $_POST['action'] == 'ajaxsearchpro_precache' ||
        $_POST['action'] == 'ajaxsearchpro_deletecache' ||
        $_POST['action'] == 'ajaxsearchpro_deletekeyword'
    )
) {
	// We need the shortcodes as well here. Otherwise the strip_shortcodes(..) will not work.
	require_once(ASP_PATH . "/includes/shortcodes.php");
    require_once(ASP_PATH . "/search.php");
    return;
}

$funcs = new aspInit();

/* Includes only on ASP admin pages */
if (wpdreams_on_backend_page($asp_admin_pages) == true) {
    require_once(ASP_PATH . "/backend/settings/types.inc.php");
    require_once(ASP_PATH . "/includes/compatibility.class.php");
    require_once(ASP_PATH . "/compatibility.php");
    add_action('admin_enqueue_scripts', array($funcs, 'scripts'));
}

/* Includes only on full backend, frontend, non-ajax requests */
require_once(ASP_PATH . "/includes/etc/indextable.class.php");
require_once(ASP_PATH . "/includes/etc/updates.class.php");
require_once(ASP_PATH . "/includes/etc/updates_manager.class.php");
require_once(ASP_PATH . "/includes/etc/helpers_factory.php");
require_once(ASP_PATH . "/backend/settings/default_options.php");
require_once(ASP_PATH . "/backend/settings/admin-ajax.php");
require_once(ASP_PATH . "/includes/shortcodes.php");
require_once(ASP_PATH . "/includes/hooks.php");

add_action('admin_menu', array($funcs, 'navigation_menu'));
register_activation_hook(__FILE__, array($funcs, 'ajaxsearchpro_activate'));

// We need the scripts and styles only on the ASP pages and the front-end
if (!is_admin() || wpdreams_on_backend_page($asp_admin_pages) == true) {
    add_action('wp_enqueue_scripts', array($funcs, 'styles'));
    add_action('wp_enqueue_scripts', array($funcs, 'scripts'));
    add_action('wp_footer', array($funcs, 'footer'));
}

/* Includes on Post/Page/Custom post type edit pages */
if (wpdreams_on_backend_post_editor()) {
    require_once(ASP_PATH . "/backend/tinymce/buttons.php");
}

require_once(ASP_PATH . "/includes/widgets.php");

// Check if plugin needs re-activation in case of un-expected update
// .. fixes mising table errors
$funcs->safety_check();

// Here comes the Auto Updater code SOON