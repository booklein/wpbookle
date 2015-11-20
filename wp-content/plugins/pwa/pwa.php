<?php
/**
 * Plugin Name: Pay with Amazon.
 * Depends: woocommerce
 * Plugin URI:  https://wordpress.org/plugins/pwa/
 * Description: Now pay with amazon is easy. Download plugin and install it. 
 * Version: 1.0.0
 * Author: Amazon
 * Author URI: http://amazon.com/
 * License: A short license name. Example: GPL2
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


if ( ! class_exists( 'PWA' ) ) :

/**
 * Main PWA Class
 *
 * @class PWA
 * @version	1.0.0
 */
class PWA {
	
	/**
	 * @var PWA the single instance of the class
	 */
	protected static $_instance = null;
	
	/**
	 * Main PWA Instance
	 *
	 * Ensures only one instance of PWA is loaded or can be loaded.
	 *
	 * @return PWA - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	/**
	 * PWA Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();
	}
	
	
	/**
	 * Define PWA Constants
	 */
	private function define_constants() {
		$this->define( 'PWA_INCLUDE_DIR' , plugin_dir_path( __FILE__ ));
		$this->define( 'PWA_PLUGIN_FILE' , __FILE__ );
		$this->define( 'PWA_TEMPLATE_DIR' , dirname(__FILE__). '/templates/' );
		$this->define( 'PLUGIN_PATH' , dirname(__FILE__).'/mws_report/');
		$this->define( 'IMAGE_FOLDER_URL' , dirname(__FILE__). '/assests/images/');
		$this->define( 'IMAGE_FOLDER_HTTPURL' , plugins_url('assests/images/', __FILE__));
		
		$pwacheckkout = new Pwacheckout();

		if( $pwacheckkout->get_option('enabled') == 'yes') {
			
			define('AWS_ACCESS_KEY_ID', $pwacheckkout->get_option('access_key'));
			define('AWS_SECRET_ACCESS_KEY', $pwacheckkout->get_option('secret_key'));
			define('APPLICATION_NAME', 'pwa_mws');
			define('APPLICATION_VERSION', '1.0.0');
			define('MERCHANT_ID', $pwacheckkout->get_option('merchant_id'));
			define('MARKETPLACE_ID', $pwacheckkout->get_option('marketplace_id'));
			define('WORK_ENVIRONMENT',$pwacheckkout->get_option('environment'));
			define('UPDATE_ODER_FROM',$pwacheckkout->get_option('pwa_order_update'));
			define('CUSTOM_CHECKOUT_IMAGE',$pwacheckkout->get_option('pwa_btn_img_hidden'));
			define('MWS_ENDPOINT_URL','https://mws.amazonservices.in/');
		}
	}
	
	
	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( 'includes/class-pwa-install.php' );
		include_once( 'includes/class-pwa-uninstall.php' );
		include_once( 'includes/class-pwa.php' );
		include_once( 'iopn/class-pwa-iopn.php' );
		include_once( 'mws/class-pwa-mws.php' );	
		include_once( 'mws_report/class-pwa-mws-report.php' );
	}
	
	
	/**
	 * Hook into actions and filters
	 */
	public function init_hooks(){
		
		add_action('plugins_loaded',  array($this , 'pwa_init_gateway') );
		
		// Activate plugin 
		register_activation_hook(__FILE__, array($this , 'check_compatibility') );
		register_activation_hook( __FILE__, array( 'PWA_Install', 'install' ) );
	
		//register script for pwa checkout settings in admin panel 
		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'pwas_admin_pwa_btn' ) );

		add_action('init', array($this, 'make_pwa_endpoint'));
		add_filter('query_vars', array($this, 'add_query_vars'));
		add_action('parse_request', array($this, 'sniff_requests'));
		
		add_action('init', array($this, 'register_script'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_style') );
		
		//add button on cart page before procede to checkout button 
		add_action('woocommerce_proceed_to_checkout',array($this, 'add_button_before_checkout'));
		
		//add button on check out page before place order button  
		add_action('woocommerce_review_order_after_submit',array($this, 'add_button_before_placeorder'));
		
		add_filter('script_loader_tag',array($this,'add_attribute_to_script'),10,2);
		//add_filter('script_loader_src',array($this,'add_attribute_to_script'),10,2);
		//add_filter('clean_url',array($this,'unclean_url'),10,3);
		
		add_action(	'woocommerce_cart_updated' , array($this, 'add_cart_data_temp'));
		
		add_action( 'woocommerce_order_status_cancelled',  array($this, 'pwa_cancel_feed') );
		
		//add_action( 'woocommerce_order_status_refunded',  array($this, 'pwa_cancel_feed') );
		//add_action( 'woocommerce_cancelled_order', array($this, 'pwa_cancel_feed') );
		
		add_shortcode( 'amazon_checkout_button', array($this, 'amazon_checkout_button') );
		
		register_uninstall_hook( __FILE__, array( 'PWA_Uninstall', 'uninstall' ) );
		
		add_action( 'woocommerce_admin_order_data_after_order_details', array($this, 'show_amazon_order') );
		
	}

	public function add_attribute_to_script($tag, $handle) {
		if ($handle != 'pwa_script') 
			return $tag;
			return str_replace( ' src', " data-cfasync='false' src", $tag );
	}
	
	function unclean_url( $good_protocol_url, $original_url, $_context) {
		if (false !== strpos($original_url, 'data-cfasync')){
		  remove_filter('clean_url','unclean_url',10,3);
		  $url_parts = parse_url($good_protocol_url);
		  return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' data-cfasync='false";
		}
		return $good_protocol_url;
	}
	
	/*
	 * Get woocommerce version
	 */
	function wpbo_get_woo_version_number() {
        // If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';
		
		// If the plugin version number is set, return it 
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			return $plugin_folder[$plugin_file]['Version'];

		} else {
			// Otherwise return null
			return NULL;
		}
	}
	
	/*
	 * Check Woocommerce version compatibility
	 */
	public function check_compatibility()
	{
		global $woocommerce;
		
		$woo_version = $this->wpbo_get_woo_version_number();
		
		if ( $woo_version >= 2.2 ) {
			
		} else if ( $woo_version < 2.2 ) {
			 echo '<div class="error"><p>Sorry! Your WooCommerce version is less than the minimal version we support. Please update your WooCommerce Plugin.</p></div>';
			 exit;
		} else {
			 // User does not have plugin installed
			 return false;
		}
	}
	
	
	public static function pwas_admin_pwa_btn() {
		wp_enqueue_style( 'pwa-order-style' );
		wp_enqueue_script( 'pwa_script', plugins_url( './assests/js/pwa-admin-btn.js', __FILE__ ), false, '1.0.0', 'all' ); 		
	} 

	 
	/**
	 *  Register jquery and style on initialization
	 */
	function register_script(){
		wp_register_style( 'pwa-order-style', plugins_url('./assests/css/pwa-order-style.css', __FILE__), false, '1.0.0', 'all');
	}
 
	/**
	 *  Use the registered jquery and style
	 */
	function enqueue_style(){
		wp_enqueue_style( 'pwa-order-style' );
	}
	
	/*
	 * Add query vars 
	 */
	public function add_query_vars($vars){
		$vars[] = 'pwa_iopn';
		$vars[] = 'pwa_order';
		$vars[] = 'pwa_cancel_order';
		$vars[] = 'pwa_mws_report';
		$vars[] = 'pwa_report_schedule';
		$vars[] = 'pwa_mws';
		$vars[] = 'amznPmtsOrderIds';
		$vars[] = 'amznPmtsReqId';
		$vars[] = 'amznPageSource';
		$vars[] = 'merchName';
		$vars[] = 'amznPmtsYALink';
		$vars[] = 'amznPmtsPaymentStatus';
		$vars[] = 'operation';
		return $vars;
	}
	
	
	/*
	 * Define rewrite rule to make endpoints
	 */
	public function make_pwa_endpoint(){
		add_rewrite_rule('^pwa_iopn/?([0-9]+)?/?','index.php?pagename=pwa_iopn','top');
		add_rewrite_rule('^pwa_order/?([0-9]+)?/?','index.php?pagename=pwa_order','top');
		add_rewrite_rule('^pwa_cancel_order/?([0-9]+)?/?','index.php?pagename=pwa_cancel_order','top');
		add_rewrite_rule('^pwa_mws_report/?([0-9]+)?/?','index.php?pagename=pwa_mws_report','top');
		add_rewrite_rule('^pwa_report_schedule/?([0-9]+)?/?','index.php?pagename=pwa_report_schedule','top');
		add_rewrite_rule('^pwa_mws/?([0-9]+)?/?','index.php?pagename=pwa_mws','top');
	}
	
	
	/*
	 * Call appropriate function as per endpoints
	 */
	public function sniff_requests(){
		global $wp;
		global $wp_query;
		
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_iopn'){
			$this->iopn_notifications();
		}
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_order'){
			$this->pwa_order();
		}
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_cancel_order'){
			$this->pwa_cancel_order();
		}
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_mws'){
			$this->mws_service();
		}
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_mws_report'){
			$this->mws_report_service();
		}
		
		if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'pwa_report_schedule'){
			$this->mws_report_service_schedule();
		}
	}
	 
	
	/*
	 * Accept IOPN notification
	 */ 
	public function iopn_notifications() {
		$data = $_POST;
		
		$pwacheckkout = new Pwacheckout();

		if( $pwacheckkout->get_option('enabled') == 'yes'){
			if($pwacheckkout->get_option('order_update_api') == 'IOPN') {
				if(!empty($data)) {
					$param['message'] = 'IOPN Notifications : IOPN function called with some POST data.';
					$this->generate_log($param);
					
					$iopn = new PWA_Iopn();
					$iopn->notifications($data); 
				}else{
					$param['message'] = 'IOPN Notifications : IOPN function called without POST data.';
					$this->generate_log($param);
				}
			}
			else
			{
				header('HTTP/1.1 503 SERVICE_UNAVAILABLE');
			}
		}
		else
		{
			header('HTTP/1.1 503 SERVICE_UNAVAILABLE');
		}
		exit;
	}
	
	/*
	 * Buyer will be redirected here after making payment using pay with amazon
	 * 
	 * Will generate an order with empty details and Amazon Order ID will be saved in post meta table for reference
	 * 
	 * Show thankyou message with Woocommerce Order ID 
	 */
	public function pwa_order() {
		global $wp,$wpdb;	
		global $order,$pwa_order_status,$pwa_order_id;
		global $woocommerce;
		
		$pwa_order_id = @$wp->query_vars['amznPmtsOrderIds'];
		$pwa_order_status = @$wp->query_vars['amznPmtsPaymentStatus'];
		
		if(isset($pwa_order_id) && $pwa_order_id != '') {
			$order_postmeta = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_pwa_order_id' and meta_value = '$pwa_order_id' " );
			if(empty($order_postmeta)) {
				$order =  wc_create_order();
				$order_id = $order->id;
				
				add_post_meta( $order->id, '_pwa_order_id', $pwa_order_id );
				add_post_meta( $order->id, '_payment_method', 'pwa');
				add_post_meta( $order->id, '_payment_method_title', 'Pay with Amazon');
			
				$woocommerce->cart->empty_cart(); 
				
				
			}else {
				$order_id = $order_postmeta[0]->post_id;
				$order    = new WC_Order($order_id);
				
				$woocommerce->cart->empty_cart(); 
			}
			
			//$url = 'www.inlifehealthcare.com/checkout?orderType=pwa&orderId='.$order_id.'&amznPmtsOrderIds='.$pwa_order_id.'&amznPmtsPaymentStatus='.$pwa_order_status.'';
			//echo '<script>window.location.href = "'.$url.'" </script>';
			//header('Location:'.$url);
			
			include PWA_TEMPLATE_DIR . 'pwa_order.php';
			exit;
		}
		else{
			include PWA_TEMPLATE_DIR . 'pwa_order_error.php';
			exit;
		}
	}
	
	
	/*
	 * Show payment cancel message when amazon redirect user after payment cancelled
	 */
	public function pwa_cancel_order() {
		global $wp;	
		global $woocommerce;
		
		include PWA_TEMPLATE_DIR . 'pwa_cancel_order.php';
		exit;
	}
	
	/*
	 *  MWS Order Api to update order status
	 */
	public function mws_service() {
		global $wp;	
		
		$pwacheckkout = new Pwacheckout();
		
		if( $pwacheckkout->get_option('enabled') == 'yes'){
			if($pwacheckkout->get_option('order_update_api') == 'MWS') {
				$mws = new PWA_Mws();
				$mws->list_order_api(); 
			}
			else{
				echo "Sorry! MWS is not enabled in plugin settings.";
			}
		}
		exit;
	}
	
	/*
	 *  MWS Report Api to fetch ready to ship orders.
	 */
	public function mws_report_service() {
		global $wp;	
		
		$pwacheckkout = new Pwacheckout();
		
		if( $pwacheckkout->get_option('enabled') == 'yes'){
			if($pwacheckkout->get_option('order_update_api') == 'MWS') {
				$mws_report = new PWA_Mws_Report();
				$mws_report->get_report_request_list_api();		
			}
			else{
				echo "Sorry! MWS is not enabled in plugin settings.";
			}
		}
		exit;
	}
	
	/*
	 *  Schedule MWS Report Api for 15 Minutes
	 */
	public function mws_report_service_schedule() {
		global $wp;	
		
		$mws_report = new PWA_Mws_Report();
		$mws_report->schedule_report_api();
		exit;
	}
	
	
	/*
	 * MWS Feed API to cancel an order
	 */
	public function pwa_cancel_feed($order_id) {
		global $wp;	
		
		$AmazonOrderID = get_post_meta($order_id , '_pwa_order_id');
		$param['AmazonOrderID'] = $AmazonOrderID[0];
        $param['MerchantOrderID'] = $order_id;
        $param['StatusCode'] = 'Failure';
        
		$mws_feed = new PWA_Mws_Report();
		$mws_feed->submit_cancel_feed($param);
	}
	
	/*
	 * MWS Feed API to acknowledge an order
	 */
	public function pwa_acknowledge_feed($order_id) {
		global $wp;	
		
		$AmazonOrderID = get_post_meta($order_id , '_pwa_order_id');
		$param['AmazonOrderID'] = $AmazonOrderID[0];
        $param['MerchantOrderID'] = $order_id;
        $param['StatusCode'] = 'Success';
        
		$mws_feed = new PWA_Mws_Report();
		$mws_feed->submit_acknowledge_feed($param);
	}
	
	/*
	 * Generate Pay with amazon button
	 */
	public function add_button_before_checkout()
	{
		$pwacheckkout = new Pwacheckout();
		if( $pwacheckkout->get_option('show_pwa_button') == 'yes'){
			if ( is_user_logged_in() )
			{
				$cba = new PWA_Cba();
				$cba->pay_with_amazon_button();
			}
		}
		else{
				//echo "user not logged in";
				$cba = new PWA_Cba();
				$cba->pay_with_amazon_button();
		}
	}
	
	
	public function add_button_before_placeorder()
	{
		wp_enqueue_script( 'pwa_script', plugins_url( './assests/js/script.js', __FILE__ ), false, '1.0.0', 'all' );  
		
		$cba = new PWA_Cba();
		$cba->pay_with_amazon_button();
				
		/*$pwacheckkout = new Pwacheckout();
		if( $pwacheckkout->get_option('show_pwa_button') == 'yes'){
			if ( is_user_logged_in() )
			{
				$cba = new PWA_Cba();
				$cba->pay_with_amazon_button();
			}
		}
		else{
				//echo "user not logged in";
				$cba = new PWA_Cba();
				$cba->pay_with_amazon_button();
		}*/
	}
	
	/*
	 * Save cart detail in table and use it during updating order when iopn comes
	 */
	public function add_cart_data_temp()
	{
		if(UPDATE_ODER_FROM == 'woocart')
		{
				global 	$wpdb;
				$prefix = $wpdb->prefix;
				$table = $prefix."pwa_before_cart_save";
				$data = maybe_serialize(WC()->cart);
				$sess_data = new WC_Session_Handler();
				$sess_detail = $sess_data->get_session_cookie();
				$sess_cust_id = $sess_detail[0];
				if ( is_user_logged_in() )
				{
					$user_ID = get_current_user_id();
					$query = 'SELECT id FROM '.$table.' where user_id = "'.$sess_cust_id.'"';
					$result = $wpdb->get_results($query);
					$ids = '';
					$id = $result[0]->id;
					if($id)
					{
						foreach($result as $rid)
						{
							$ids .= $rid->id.',';
						}
						$ids = substr($ids, 0, -1);
					
						$qry = "UPDATE ".$table." SET user_id = '".$user_ID."' WHERE id in (".$ids.")";
						$wpdb->query($qry);
					}
				}
				else
				{
					$user_ID = $sess_cust_id;
				}
				if($user_ID)
				{
					$qry = "INSERT INTO ".$table." (user_id, cart_data, status) VALUES ('".$user_ID."', '".addslashes($data)."',0)";
					$wpdb->query($qry);
				}
		}
	}
	
	/*
	 * Check Woocommerce Payment Gateway if enabled or disabled before installing plugin. 
	 */ 
	public function pwa_init_gateway()  
	{
		if (class_exists('WC_Payment_Gateway'))
		{
			include_once( 'includes/class-pwa-gateway.php' );
			$this->define_constants();
		}
	}
	
	public function show_amazon_order()
    {
		global $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order = $theorder;
		$amazon_order_id = esc_html( get_post_meta( str_replace('#','',$order->get_order_number()), '_pwa_order_id','true') );
 		echo  "<p class='form-field form-field-wide'><label>Amazon Order ID:</label> <a href='https://sellercentral.amazon.in/gp/orders-v2/details/ref=cb_orddet_cont_myo?ie=UTF8&orderID=$amazon_order_id' target='_blank'> $amazon_order_id </a></p>"; 
    }
	
	
	/*
	 * Generate log for every activity of plugin
	 */ 
	public function generate_log($param) {

		$filename =  PWA_INCLUDE_DIR.'pwa_error.log';	
		if(!file_exists($filename)) {
			$myfile = fopen($filename, "w");
			$entry = date('Y-m-d H:i:s').' : '.$param['message'].'';
			fwrite($myfile, $entry);
			fclose($myfile);	
		}else{
			$myfile = fopen($filename, "r+");
			$filedata = fread($myfile,filesize($filename));
			$entry = date('Y-m-d H:i:s').' : '.$param['message'].'';
			fwrite($myfile, $entry.PHP_EOL);
			fclose($myfile);
		}
	}

	public function amazon_checkout_button() {
    	ob_start();
    	$this->add_button_before_checkout();
    	return ob_get_clean();
	} 

}

endif;


/**
 * Returns the main instance of PWA to prevent the need to use globals.
 * 
 * @return PWA
 */
function PWA_LOAD() {
	return PWA::instance();
}

// Global for backwards compatibility.
$GLOBALS['pwa'] = PWA_LOAD();

}

?>
