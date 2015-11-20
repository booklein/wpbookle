<?php
/**
 * Uninstallation related functions and actions.
 *
 * @category 	Admin
 * @package 	PWA/Includes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PWA_Uninstall Class
 */
class PWA_Uninstall {

	
	/**
	 * Constructor for the PWA Uninstall class.
	 */
	public function __construct() {
		
	}
	
	/**
	 * Uninstall PWA
	 */
	public static function uninstall($networkwide ) {
		$option_name = 'woocommerce_Pwacheckout_settings';
		delete_option( $option_name );

		if ( get_option('woocommerce_default_gateway') == 'Pwacheckout' ) 
		{
			update_option( 'woocommerce_default_gateway', '');
		}

		global $wpdb, $prefix;
		$prefix = $wpdb->prefix . 'pwa_';
		
		$sql = "DROP TABLE `". $prefix . "iopn_records`;" ;
		$wpdb->query( $sql );
		
		$sql = "DROP TABLE `". $prefix . "before_cart_save`;" ;
		$wpdb->query( $sql );
		
		$sql = "DROP TABLE `". $prefix . "mws_order_cron`;" ;
		$wpdb->query( $sql );
		
		$sql = "DROP TABLE `". $prefix . "mws_report_cron`;" ;
		$wpdb->query( $sql );
	}
	
	

}
