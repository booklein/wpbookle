<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pay with Amazon
 *
 * The PWA Iopn class accept the notifications, parse them and update orders.
 *
 * @class 		PWA_Iopn
 * @version		1.0.0
 * @package		PWA/Iopn
 * @category	Class
 * @author 		Amazon
 */
 
 
class PWA_Iopn extends  PWA {
	
	
	
	/**
	 * Constructor for the iopn class. Loads options, files and hooks.
	 */
	public function __construct() {
		$this->includes();
	}
	
	/**
	 * Include required core files used to update the orders.
	 */
	public function includes() {
		include_once( 'SignatureCalculator.php' );
		include_once( 'NewOrderNotification.php' );
		include_once( 'OrderReadyToShipNotification.php' );
		include_once( 'OrderCancelledNotification.php' );
	}
	
	
	/*
	 * Accept notifications data and parse them to update orders.
	 */
	public function notifications($param) {
		
		global $wpdb,$prefix;
		$prefix = $wpdb->prefix . 'pwa_';
		
		try {
			
			$uuid 			   = urldecode($param['UUID']);
			$timestamp  	   = urldecode($param['Timestamp']);
			$Signature  	   = str_replace(' ','+',urldecode($param['Signature']));
			$AWSAccessKeyId    = urldecode($param['AWSAccessKeyId']);
			$NotificationType  = urldecode($param['NotificationType']);
			$NotificationData  = stripslashes(urldecode($param['NotificationData']));
			
			$wpdb->insert($prefix . 'iopn_records', array(
					'uuid'   => $uuid,
					'timestamp'   => $timestamp,
					'notification_type'   => $NotificationType
				));
			$iopn_record_id = $wpdb->insert_id;
			
			// Verify that the notification request is valid by verifying the Signature
			$concatenate = $uuid.$timestamp;
			
			$pwacheckkout = new Pwacheckout();
			$secretKeyID = $pwacheckkout->get_option('secret_key');
			
			$calculator = new SignatureCalculator();
			$generatedSignature = $calculator->calculateRFC2104HMAC($concatenate, $secretKeyID);
			
			if($Signature == $generatedSignature) 
			{
				// Verify the Timestamp
				//$this->time_difference($timestamp) > 15
				if(1) {
					
					if($NotificationType == 'NewOrderNotification') {
						
						$new_order = new NewOrderNotification();
						$new_order->update_order($NotificationData , $iopn_record_id);
					}
					
					if($NotificationType == 'OrderReadyToShipNotification') {
						
						$confirm_order = new OrderReadyToShipNotification();
						$confirm_order->update_order_status($NotificationData , $iopn_record_id);
					}
					
					if($NotificationType == 'OrderCancelledNotification') {
						
						$cancel_order = new OrderCancelledNotification();
						$cancel_order->cancel_order($NotificationData , $iopn_record_id);
					}
				}
				else
				{
					$param['message'] = 'IOPN Notifications : '.$NotificationType.' : IOPN function called and with wrong timestamp.';
					$this->generate_log($param);
					
					// Respond to the Request
					header('HTTP/1.1 403 PERMISSION_DENIED');
				}
			}
			else
			{
				
				$param['message'] = 'IOPN Notifications : '.$NotificationType.' : IOPN function called and with wrong signature.';
				$this->generate_log($param);
						
				// Respond to the Request
				header('HTTP/1.1 403 PERMISSION_DENIED');
			}
			
		} catch (Exception $e) {
			 $param['message'] = 'IOPN Notifications : Caught exception : '.$e->getMessage().'.';
			 $this->generate_log($param);
		}
	}
	
	/*
	 * Calculate time difference 
	 */  
	public function time_difference($timestamp) {
		date_default_timezone_set("GMT");
		$mytimestamp =  date("Y-m-d H:i:s");
		
		$start_date = new DateTime($timestamp);
		$since_start = $start_date->diff(new DateTime($mytimestamp));
		
		$minutes = $since_start->days * 24 * 60;
		$minutes += $since_start->h * 60;
		$minutes += $since_start->i;
		return $minutes;
	}
	
	
}
