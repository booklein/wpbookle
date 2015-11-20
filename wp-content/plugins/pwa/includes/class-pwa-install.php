<?php
/**
 * Installation related functions and actions.
 *
 * @category 	Admin
 * @package 	PWA/Includes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PWA_Install Class
 */
class PWA_Install {

	
	/**
	 * Constructor for the install class. Loads options and hooks in the init method.
	 */
	public function __construct() {
		$this->init();
		
	}
	
	
	/**
	 * Hooks.
	 */
	public static function init() {
	}
	
	/**
	 * Install PWA
	 */
	public static function install($networkwide ) {
		global $wpdb;
		/* Activation function for network */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			
			/* check if it is a network activation - if so, run the activation function for each blog id */
			if ( $networkwide ) {
				
				$old_blog = $wpdb->blogid;
				
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::create_tables();
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		self::create_tables();
	}
	
	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *		pwa_settings - Table for storing amazon keys and other settings
	 *		pwa_iopn_records - Table to keep tracking of IOPN records
	 *		pwa_before_cart_save - 
	 *		pwa_mws_cron - Table to keep tracking of MWS Cron
	 *		
	 *
	 * @return void
	 */
	private static function create_tables() {
		global $wpdb, $prefix;
		$prefix = $wpdb->prefix . 'pwa_';
		
		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		
		foreach ( self::get_schema() as $key => $value ) {
				dbDelta( $value );
		}
	}
	
	
	/**
	 * Get Table schema
	 */
	private static function get_schema() {
		global $wpdb, $prefix;
		$prefix = $wpdb->prefix . 'pwa_';

		$query = array();
		
		$query[0] = "CREATE TABLE `" . $prefix . "iopn_records` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`uuid` varchar(100) NOT NULL,
					`timestamp` datetime NOT NULL,
					`notification_type` varchar(50) NOT NULL,
					`notification_reference_id` varchar(100) NOT NULL,
					`amazon_order_id` varchar(50) NOT NULL,
					`status` varchar(20) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

		$query[1] = "CREATE TABLE  `" . $prefix . "before_cart_save` (
					 `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					 `user_id` VARCHAR( 100 ) NOT NULL ,
					 `cart_data` LONGTEXT,
					 `status` INT( 11 ) NOT NULL ,
					PRIMARY KEY (  `ID` )
					) ENGINE = INNODB DEFAULT CHARSET = latin1 AUTO_INCREMENT =1;";
					
		$query[2] = "CREATE TABLE IF NOT EXISTS  `" . $prefix . "mws_order_cron` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `created_before` varchar(80) NOT NULL,
					 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
					
		$query[3] = "CREATE TABLE IF NOT EXISTS  `" . $prefix . "mws_report_cron` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `created_before` varchar(80) NOT NULL,
					 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					 PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
		
		return $query;
	}
	

}

PWA_Install::init();
