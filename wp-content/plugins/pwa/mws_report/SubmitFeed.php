<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


require_once('config.inc.php');

class Submit_Feed extends  PWA_Mws_Report {
	
	public $serviceUrl = MWS_ENDPOINT_URL;

	/**
	 * Constructor for the submit feed class.
	 */
	public function __construct() {
		$this->includes();
	}

	/**
	 * Include required core files and classes.
	 */
	public function includes() {
		require_once('MarketplaceWebService/Client.php');
		require_once('MarketplaceWebService/Model/SubmitFeedRequest.php');
	}
	
	/*
	 *Acknowledge Amazon seller central order with Merchant Order Id updation
	 */
	function acknowledge_feed($param)
	{
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
 
$feed = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <DocumentVersion>1.02</DocumentVersion>
        <MerchantIdentifier>'.MERCHANT_ID.'</MerchantIdentifier>
    </Header>
    <MessageType>OrderAcknowledgement</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderAcknowledgement>
            <AmazonOrderID>'.$param['AmazonOrderID'].'</AmazonOrderID>
            <MerchantOrderID>'.$param['MerchantOrderID'].'</MerchantOrderID>
            <StatusCode>'.$param['StatusCode'].'</StatusCode>
		</OrderAcknowledgement>
    </Message>
</AmazonEnvelope>';

		$marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

		$feedHandle = @fopen('php://memory', 'rw+');
		fwrite($feedHandle, $feed);
		rewind($feedHandle);

		$request = new MarketplaceWebService_Model_SubmitFeedRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setMarketplaceIdList($marketplaceIdArray);
		$request->setFeedType('_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
		$request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
		rewind($feedHandle);
		$request->setPurgeAndReplace(false);
		$request->setFeedContent($feedHandle);

		rewind($feedHandle);

		return $this->invokeSubmitFeed($service, $request);

		@fclose($feedHandle);                
    }
   
    /*
	 *Cancel Amazon seller central order with Merchant Order Id updation
	 */
    function cancel_feed($param)
	{
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
 


$feed = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <DocumentVersion>1.02</DocumentVersion>
        <MerchantIdentifier>'.MERCHANT_ID.'</MerchantIdentifier>
    </Header>
    <MessageType>OrderAcknowledgement</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderAcknowledgement>
            <AmazonOrderID>'.$param['AmazonOrderID'].'</AmazonOrderID>
            <MerchantOrderID>'.$param['MerchantOrderID'].'</MerchantOrderID>
            <StatusCode>'.$param['StatusCode'].'</StatusCode>
		</OrderAcknowledgement>
    </Message>
</AmazonEnvelope>';

		$marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

		$feedHandle = @fopen('php://memory', 'rw+');
		fwrite($feedHandle, $feed);
		rewind($feedHandle);

		$request = new MarketplaceWebService_Model_SubmitFeedRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setMarketplaceIdList($marketplaceIdArray);
		$request->setFeedType('_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
		$request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
		rewind($feedHandle);
		$request->setPurgeAndReplace(false);
		$request->setFeedContent($feedHandle);

		rewind($feedHandle);

		return $this->invokeSubmitFeed($service, $request);

		@fclose($feedHandle);                
    }
   
    /*
	 *Refund on Amazon seller central order
	 */
    function refund_feed($param)
	{
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
 


$feed = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <DocumentVersion>1.02</DocumentVersion>
        <MerchantIdentifier>'.MERCHANT_ID.'</MerchantIdentifier>
    </Header>
    <MessageType>OrderAcknowledgement</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderAcknowledgement>
            <AmazonOrderID>'.$param['AmazonOrderID'].'</AmazonOrderID>
            <MerchantOrderID>'.$param['MerchantOrderID'].'</MerchantOrderID>
            <StatusCode>'.$param['StatusCode'].'</StatusCode>
		</OrderAcknowledgement>
    </Message>
</AmazonEnvelope>';

		$marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

		$feedHandle = @fopen('php://memory', 'rw+');
		fwrite($feedHandle, $feed);
		rewind($feedHandle);

		$request = new MarketplaceWebService_Model_SubmitFeedRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setMarketplaceIdList($marketplaceIdArray);
		$request->setFeedType('_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
		$request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
		rewind($feedHandle);
		$request->setPurgeAndReplace(false);
		$request->setFeedContent($feedHandle);

		rewind($feedHandle);

		return $this->invokeSubmitFeed($service, $request);

		@fclose($feedHandle);                
   }
  
  
   function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
   {
       try {
              $response = $service->submitFeed($request);
              
              if ($response->isSetSubmitFeedResult()) { 
                    
                    $submitFeedResult = $response->getSubmitFeedResult();
                    
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                       
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            return $feedSubmissionInfo->getFeedSubmissionId();
                        }
                    } 
              }
                
     } catch (MarketplaceWebService_Exception $ex) {
		$message  =  'MWS Feed API : Caught Exception : '.$ex->getMessage(). "\n";
		$message .= "Response Status Code: " . $ex->getStatusCode() . "\n";
		$message .= "Error Code: " . $ex->getErrorCode() . "\n";
		$message .= "Error Type: " . $ex->getErrorType() . "\n";

		$param['message'] = $message;
		$this->generate_log($param);
     }
   }
 
}
                                                                
