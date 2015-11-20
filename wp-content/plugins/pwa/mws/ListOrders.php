<?php
 
 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MWS Order API Class
 *
 * @class List_Orders
 * @version	1.0.0
 * 
 * Update order status (Only update order status when order get cancelled)
 */
class List_Orders  extends  PWA_Mws {

	public $serviceUrl = MWS_ENDPOINT_URL;
	
	public $LastUpdatedAfter = "";
	
	public $order_status_array = array('Pending'=>'pending','Unshipped'=>'processing','Canceled'=>'cancelled');

	/**
	 * Constructor for the list order class.
	 */
	public function __construct() {
		$this->includes();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once('MarketplaceWebServiceOrders/Client.php');
		require_once('MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
		
	}

	
	function init()
	{
		global $wp,$wpdb;
		
		$result = $wpdb->get_results('select * from `'. $wpdb->prefix .'pwa_mws_order_cron` order by id desc limit 0 , 1');
		if(!empty($result)) {
			$this->LastUpdatedAfter = $result[0]->created_before;
		}else{
			$dateTime = new DateTime('-3 day', new DateTimeZone('UTC'));
			$time = $dateTime->format(DATE_ISO8601);  
			$this->LastUpdatedAfter = $time;
		}
		
		$config = array (
		   'ServiceURL' => $this->serviceUrl."Orders/2013-09-01",
		   'ProxyHost' => null,
		   'ProxyPort' => -1,
		   'ProxyUsername' => null,
		   'ProxyPassword' => null,
		   'MaxErrorRetry' => 3,
		 );
		 
		 $service = new MarketplaceWebServiceOrders_Client(
					AWS_ACCESS_KEY_ID,
					AWS_SECRET_ACCESS_KEY,
					APPLICATION_NAME,
					APPLICATION_VERSION,
					$config);
		
		 $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
		 $request->setSellerId(MERCHANT_ID);
		 $request->setMarketplaceId(MARKETPLACE_ID);
		 $request->setLastUpdatedAfter($this->LastUpdatedAfter);
		
		// object or array of parameters
		$this->invokeListOrders($service, $request);
	}

	
	function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		  try {
			  
			$response = $service->ListOrders($request);
			$dom = new DOMDocument();
			$dom->loadXML($response->toXML());
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$xml = $dom->saveXML();
			$this->update_order($xml);

		 } catch (MarketplaceWebServiceOrders_Exception $ex) {
			 
			$message  =  'MWS Order API : Caught Exception : '.$ex->getMessage(). "\n";
			$message .= "Response Status Code: " . $ex->getStatusCode() . "\n";
			$message .= "Error Code: " . $ex->getErrorCode() . "\n";
			$message .= "Error Type: " . $ex->getErrorType() . "\n";

			$param['message'] = $message;
			$this->generate_log($param);
		 }
	}
	
	public function update_order($data) {
		global $wpdb,$woocommerce;
		
		$xml = simplexml_load_string($data);
		
		 // Check and dump MWS Report API Response
		 $pwacheckkout = new Pwacheckout();
		 if( $pwacheckkout->get_option('mws_order_dump') == 'yes'){

			$dir = $pwacheckkout->get_option('mws_order_dump_url');
			if (!file_exists($dir) && !is_dir($dir)) {
				mkdir($dir, 0777);
			} 

			$filename = $dir.time().'_mws_order';
			$myfile = fopen($filename, "w");
			fwrite($myfile, $data);
			fclose($myfile);
		 }
		
		$LastUpdatedBefore = $xml->ListOrdersResult->LastUpdatedBefore;
		$wpdb->insert($wpdb->prefix . 'pwa_mws_order_cron', array(
				'created_before'   => $LastUpdatedBefore
			));
		
		foreach($xml->ListOrdersResult->Orders->Order as $order)
		{
			$AmazonOrderId = (string)$order->AmazonOrderId;
			$OrderStatus   = (string)$order->OrderStatus;
			
			$order_postmeta = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_pwa_order_id' and meta_value = '$AmazonOrderId' " );
		
			if(!empty($order_postmeta)) 
			{
				if($OrderStatus == 'Canceled'){
					$order_id = $order_postmeta[0]->post_id;	
					$order = new WC_Order($order_id);
					$order->update_status('cancelled');
				}
			}
		}
	}
	
	
	
	

}
