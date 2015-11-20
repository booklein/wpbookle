<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once('config.inc.php');


/**
 * MWS Report API Class
 *
 * @class Get_Report_Request_List
 * @version	1.0.0
 * 
 * Update order details 
 */
class Get_Report_Request_List  extends  PWA_Mws_Report {
	
	public $serviceUrl = MWS_ENDPOINT_URL;


	/**
	 * Constructor for the list order class.
	 */
	public function __construct() {
		$this->includes();
	}

	/**
	 * Include required core files and classes.
	 */
	public function includes() {
		require_once('MarketplaceWebService/Client.php');
		require_once('MarketplaceWebService/Exception.php');
		require_once('MarketplaceWebService/Model/GetReportRequestListRequest.php');	
	}
	
	function init()
	{
		global $wpdb, $prefix;
		$prefix = $wpdb->prefix . 'pwa_';
		
		$config = array (
		  'ServiceURL' => $this->serviceUrl,
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		);
		
	    $service = new MarketplaceWebService_Client(
		 AWS_ACCESS_KEY_ID, 
		 AWS_SECRET_ACCESS_KEY, 
		 $config,
		 APPLICATION_NAME,
		 APPLICATION_VERSION);
	 
		$request = new MarketplaceWebService_Model_GetReportRequestListRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setReportTypeList(array('0'=>'_GET_ORDERS_DATA_'));
		$request->setReportProcessingStatusList(array('0'=>'_DONE_'));
		$request->setMaxCount(20);
		
		$last_request_date = $wpdb->get_results( "select * from `" . $prefix . "mws_report_cron` order by id desc limit 0,1" );
		if(!empty($last_request_date)) {
			$time = $last_request_date[0]->created_before;
		}
		else{
			$dateTime = new DateTime('-3 day', new DateTimeZone('UTC'));
			$time = $dateTime->format(DATE_ISO8601);  
		}
		$request->setRequestedFromDate($time);
		
		$this->invokeGetReportRequestList($service, $request);
	}
	                                                          

  function invokeGetReportRequestList(MarketplaceWebService_Interface $service, $request) 
  {
	  global $wpdb,$woocommerce;
      try {
              $response = $service->getReportRequestList($request);
              
              if ($response->isSetGetReportRequestListResult()) { 
				 
                    $getReportRequestListResult = $response->getGetReportRequestListResult();
                    $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
                    print_r($reportRequestInfoList);
                    
                    foreach ($reportRequestInfoList as $reportRequestInfo) {
						
						  if( ($reportRequestInfo->isSetReportType() && $reportRequestInfo->getReportType() == '_GET_ORDERS_DATA_') && ($reportRequestInfo->isSetReportProcessingStatus() && $reportRequestInfo->getReportProcessingStatus() == '_DONE_') )
						  {
							  if ($reportRequestInfo->isSetReportRequestId()) 
							  {
								  $ReportRequestId = $reportRequestInfo->getReportRequestId();
							  }
							  
							  if ($reportRequestInfo->isSetGeneratedReportId()) 
							  {
								 $GeneratedReportId = $reportRequestInfo->getGeneratedReportId();
								
								 if($GeneratedReportId == '' && $ReportRequestId != '') {
									 $GeneratedReportId = $this->get_report_list_api($ReportRequestId);
									 $data = $this->get_report_api($GeneratedReportId);
								 }else{ 
									 $data = $this->get_report_api($GeneratedReportId);
								 }
								 
								 
								 
								 $xml = simplexml_load_string($data);
								 
								 // Check and dump MWS Report API Response
								 $pwacheckkout = new Pwacheckout();
								 if( $pwacheckkout->get_option('mws_report_dump') == 'yes'){

									$dir = $pwacheckkout->get_option('mws_report_dump_url');
									if (!file_exists($dir) && !is_dir($dir)) {
										mkdir($dir, 0777);
									} 

									$filename = $dir.$GeneratedReportId.'_mws_report';
									$myfile = fopen($filename, "w");
									fwrite($myfile, $data);
									fclose($myfile);
								 }
								 
								 
								 foreach($xml->Message as $orderdetail) {
										$AmazonOrderID = (string)$orderdetail->OrderReport->AmazonOrderID;
										
										$order_postmeta = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_pwa_order_id' and meta_value = '$AmazonOrderID' " );
		
										if(empty($order_postmeta)) 
										{
											$order =  wc_create_order();
											add_post_meta($order->id, '_pwa_order_id', $AmazonOrderID );
											
											$this->update_order_detail($order->id , $orderdetail);
										}
										else 
										{
											$order_id = $order_postmeta[0]->post_id;
											$this->update_order_detail($order_id , $orderdetail);
										}
								 }
								
							  }
						  }
                    }
                    
                     $dateTime = new DateTime('now', new DateTimeZone('UTC'));
					 $time = $dateTime->format(DATE_ISO8601); 
					 $wpdb->insert($wpdb->prefix . 'pwa_mws_report_cron', array(
								'created_before'   => $time
							));
                } 
               
     } catch (MarketplaceWebService_Exception $ex) {
        $message  =  'MWS Report API : Caught Exception : '.$ex->getMessage(). "\n";
		$message .= "Response Status Code: " . $ex->getStatusCode() . "\n";
		$message .= "Error Code: " . $ex->getErrorCode() . "\n";
		$message .= "Error Type: " . $ex->getErrorType() . "\n";

		$param['message'] = $message;
		$this->generate_log($param);
     }
 }
 
 
	public function update_order_detail($order_id , $orderdetail) {
		global $wpdb,$woocommerce;
		
		$non_received = $wpdb->get_results( "select * from $wpdb->postmeta where meta_key = '_non_received' and post_id = $order_id " );
		if(empty($non_received)) 
		{	
			$this->update_cart_by_xml($order_id,$orderdetail);
			add_post_meta($order_id, '_non_received', '1' );
			
			$order = new WC_Order($order_id);
			$order->update_status('processing');
		}	
	}
	
	
	public function update_cart_by_xml($order_id,$orderdetail){
		global $wpdb,$woocommerce;
		
		$AmazonOrderID = (string)$orderdetail->OrderReport->AmazonOrderID;
		
		$order = new WC_Order($order_id);
		
		$billing_address = array(
			'first_name' => (string)$orderdetail->OrderReport->BillingData->BuyerName,
			'last_name'  => '',
			'company'    => '',
			'email'      => (string)$orderdetail->OrderReport->BillingData->BuyerEmailAddress,
			'phone'      => (string)$orderdetail->OrderReport->BillingData->BuyerPhoneNumber,
			'address_1'  => '',
			'address_2'  => '', 
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => ''
		);

		
		
		$shipping_address = array(
			'first_name' => (string)$orderdetail->OrderReport->FulfillmentData->Address->Name,
			'last_name'  => '',
			'company'    => '',
			'email'      => '',
			'phone'      => (string)$orderdetail->OrderReport->FulfillmentData->Address->PhoneNumber,
			'address_1'  => (string)$orderdetail->OrderReport->FulfillmentData->Address->AddressFieldOne,
			'address_2'  => (string)$orderdetail->OrderReport->FulfillmentData->Address->AddressFieldTwo, 
			'city'       => (string)$orderdetail->OrderReport->FulfillmentData->Address->City,
			'state'      => (string)$orderdetail->OrderReport->FulfillmentData->Address->State,
			'postcode'   => (string)$orderdetail->OrderReport->FulfillmentData->Address->PostalCode,
			'country'    => (string)$orderdetail->OrderReport->FulfillmentData->Address->CountryCode
		);

		$order->set_address( $shipping_address, 'shipping' );
	   
		add_post_meta($order_id, '_payment_method', 'pwa');
		add_post_meta($order_id, '_payment_method_title', 'Pay with Amazon');
		
		$total_amount = 0;
		$shipping_amount = 0;
		$total_promo = 0;
		foreach($orderdetail->OrderReport->Item as $item) {
			$SKU = (string)$item->SKU;	
			$Title = (string)$item->Title;
			$Quantity = (int)$item->Quantity;
			
			foreach($item->ItemPrice->Component as $amount_type) {
				
				$item_charge_type = (string)$amount_type->Type;	
				
				if($item_charge_type == 'Principal') {
					$Principal = (float)$amount_type->Amount;
				}
				
				if($item_charge_type == 'Shipping') {
					$Shipping = (float)$amount_type->Amount;
				}
				
				if($item_charge_type == 'Tax') {
					$Tax = (float)$amount_type->Amount;
				}
				
				if($item_charge_type == 'ShippingTax') {
					$ShippingTax = (float)$amount_type->Amount;
				}
			}
			
			if( !empty($item->Promotion) ) {
				foreach($item->Promotion->Component as $promotion_amount_type) {
					
					$promotion_type = (string)$promotion_amount_type->Type;
					
					if($promotion_type == 'Shipping') {
						$Shipping_Promotions = (float)$promotion_amount_type->Amount;
					}
					
					if($promotion_type == 'Principal') {
						$Principal_Promotions = (float)$promotion_amount_type->Amount;
					}
				}
			}
			
			$product = array();
			$product['order_item_name'] = $Title;
			$product['order_item_type'] = 'line_item';
			$order_item_id = wc_add_order_item( $order_id, $product );
			
			wc_add_order_item_meta( $order_item_id, '_qty' , $Quantity);
			wc_add_order_item_meta( $order_item_id, '_line_total' , ($Principal+$Shipping_Promotions+$Principal_Promotions));
			wc_add_order_item_meta( $order_item_id, '_line_subtotal' , $Principal);
			wc_add_order_item_meta( $order_item_id, '_line_subtotal_tax', 0);
			wc_add_order_item_meta( $order_item_id, '_line_tax', 0);
			
			
			/*
             * Total Item Charge = (Principal - PrincipalPromo) + (Shipping - ShippingPromo) + Tax + ShippingTax
             */

            $total_amount += ($Principal + $Principal_Promotions) + ($Shipping + $Shipping_Promotions) ;
        
			$shipping_amount += $Shipping + $Shipping_Promotions;
			
			$total_promo += $Principal_Promotions + $Shipping_Promotions;
			
			$ClientRequestId = 0;
			foreach($item->CustomizationInfo as $info) {
				
				$info_type = (string)$info->Type;	
				if($info_type == 'url') {
					$info_array = explode(',',$info->Data);
					$customerId_array = explode('=',$info_array[0]);
					$ClientRequestId = $customerId_array[1];
				}
			}
			
			
			  if($ClientRequestId == 0) {
					$order->set_address( $billing_address, 'billing' );
			  }else{
				  if(UPDATE_ODER_FROM == 'xmlcart') {
					    unset($billing_address['email']);
					    $order->set_address( $billing_address, 'billing' );
						update_post_meta($order_id, '_customer_user' , $ClientRequestId);
				  }
				  //update_post_meta($order_id, '_customer_user' , $ClientRequestId);
			  }
			
			$product = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_sku' and meta_value = '$SKU' " );
			if(!empty($product)) {
				$product_id = $product[0]->post_id;
				if($product_id != ''){
					wc_add_order_item_meta( $order_item_id, '_product_id' , $product_id);
				
					$this->reduce_order_stock($product_id ,  $Quantity);
				}
			}
		}
		
		add_post_meta($order_id, '_order_total', $total_amount);
		add_post_meta($order_id, '_order_shipping', $shipping_amount);
	
		add_post_meta($order_id, '_cart_discount', abs($total_promo));
		
		$shipitem = array();
		$shipitem['order_item_name'] = (string)$orderdetail->OrderReport->FulfillmentData->FulfillmentServiceLevel;
		$shipitem['order_item_type'] = 'shipping';
		$order_shipping_id = wc_add_order_item( $order_id, $shipitem );
		
		wc_add_order_item_meta( $order_shipping_id, 'method_id' , str_replace(' ','_',strtolower((string)$orderdetail->OrderReport->FulfillmentData->FulfillmentServiceLevel)));
		wc_add_order_item_meta( $order_shipping_id, 'cost' , $shipping_amount);
		
		// Send notification mails to seller and customer for order 
		//$mail_class = new WC_Emails();
        //$mail_class->emails['WC_Email_New_Order']->trigger($order_id);
        
        
        // Acknowledge the order in seller central using MWS FEED API
        $param['AmazonOrderID'] = $AmazonOrderID;
        $param['MerchantOrderID'] = $order_id;
        $param['StatusCode'] = 'Success';
        $this->submit_acknowledge_feed($param);
		
	}
	
	
	/*
	 * Reduce Inventory
	 */
	public function reduce_order_stock($product_id , $Quantity) {
		global $wpdb,$woocommerce;
		
		if ( 'yes' == get_option('woocommerce_manage_stock') ) 
		{
			$product = get_product($product_id);
			
			if ( $product && $product->exists() && $product->managing_stock() ) 
			{
				if($product->get_stock_quantity() >= $Quantity)
				{
					$product->reduce_stock($Quantity);
				}
				else
				{
					$product->reduce_stock($product->get_stock_quantity());
				}
			}
	   }
	}
 
}
 ?>
