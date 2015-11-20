<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pay with Amazon
 *
 * The PWA MWS class use MWS Order API to update order status
 *
 * @class 		PWA_Mws
 * @version		1.0.0
 * @package		PWA/Mws
 * @category	Class
 * @author 		Amazon
 */
 
 
class PWA_Mws extends PWA {
	
	
	
	/**
	 * Constructor for the PWA MWS class.
	 */
	public function __construct() {
		$this->includes();
	}
	
	
	/**
	 * Include required core files and classes
	 */
	public function includes() {
		require_once('ListOrders.php');
	}
	
	
	/*
	 * Fetch orders using MWS Order API
	 */
	public function list_order_api() {
		$list_orders = new List_Orders();
		$list_orders->init();
		exit;
	}
	
	/*
	 * Fetch order detail using MWS Order API
	 */
	public function get_order_detail_api($AmazonOrderId) {
		$get_order = new Get_Order();
		return $get_order->init($AmazonOrderId);
		exit;
	}
	
	/*
	 * Fetch order lime items using MWS Order API
	 */
	public function list_order_items_api($AmazonOrderId) {	
		$list_order_items = new List_Order_Items();
		return $list_order_items->init($AmazonOrderId);
		exit;
	}
	
	
}
