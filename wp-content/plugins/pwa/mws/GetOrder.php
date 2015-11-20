<?php
 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class Get_Order  extends  PWA_Mws {

	public $serviceUrl = MWS_ENDPOINT_URL;
	
	public $AmazonOrderId = "";
	
	public $order_status_array = array('Pending'=>'pending','Unshipped'=>'processing');

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
		require_once('MarketplaceWebServiceOrders/Model/GetOrderRequest.php');
		
	}

	function init($AmazonOrderId)
	{
		 $this->AmazonOrderId = $AmazonOrderId;
		
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

		$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
		$request->setSellerId(MERCHANT_ID);
		$request->setAmazonOrderId($this->AmazonOrderId);
		
		// object or array of parameters
		$this->invokeGetOrder($service, $request);
	}


  function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
		  
        $response = $service->GetOrder($request);
        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        return $dom->saveXML();

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
			$message  =  'MWS Order API : Caught Exception : '.$ex->getMessage(). "\n";
			$message .= "Response Status Code: " . $ex->getStatusCode() . "\n";
			$message .= "Error Code: " . $ex->getErrorCode() . "\n";
			$message .= "Error Type: " . $ex->getErrorType() . "\n";

			$param['message'] = $message;
			$this->generate_log($param);
     }
 }
 
}

