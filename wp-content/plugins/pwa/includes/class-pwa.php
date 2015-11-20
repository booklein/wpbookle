<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pay with Amazon
 *
 * The PWA Cba class generate pay with amazon button.
 *
 * @class 		PWA_Cba
 * @version		1.0.0
 * @package		PWA/Includes
 * @author 		Amazon
 * path         http://www.example.com/pwa/iopn
 */
 
 
class PWA_Cba {
	
	
	/**
	 * Constructor for the iopn class. Loads options and hooks in the init method.
	 */
	public function __construct() {
		$this->includes();
	}
	
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once('signature/merchant/cart/html/MerchantHTMLCartFactory.php');
		require_once('signature/common/cart/xml/XMLCartFactory.php');
		require_once('signature/common/signature/SignatureCalculator.php');
	}
	
	
	/*
	 * Generate pay with amazon button
	 */
	public function pay_with_amazon_button()
	{
		$pwacheckkout = new Pwacheckout();

		if( $pwacheckkout->get_option('enabled') == 'yes'){

			$merchantID    =$pwacheckkout->get_option('merchant_id');
			$accessKeyID   =$pwacheckkout->get_option('access_key');
			$secretKeyID   =$pwacheckkout->get_option('secret_key');
			$pwa_btn_color =$pwacheckkout->get_option('pwa_btn_color');
			$pwa_btn_bkgd  =$pwacheckkout->get_option('pwa_btn_bkgd');
			$pwa_btn_size  =$pwacheckkout->get_option('pwa_btn_size');
	
			if (!defined('PWA_BTN_COLOR')) {
				define('PWA_BTN_COLOR', $pwa_btn_color);
			}
			if (!defined('PWA_BTN_BKGD')) {
				define('PWA_BTN_BKGD', $pwa_btn_bkgd);
			}
			if (!defined('PWA_BTN_SIZE')) {
				define('PWA_BTN_SIZE', $pwa_btn_size);
			}

			$cartFactory = new XMLCartFactory();
			$calculator = new SignatureCalculator();

			$cart = $cartFactory->getSignatureInput($merchantID, $accessKeyID);
			$signature = $calculator->calculateRFC2104HMAC($cart, $secretKeyID);
			$cartHtml = $cartFactory->getCartHTML($merchantID, $accessKeyID, $signature);

			echo $cartHtml;
		}
	}
	
}
