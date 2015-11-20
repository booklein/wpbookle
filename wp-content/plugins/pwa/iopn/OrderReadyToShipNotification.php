<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class OrderReadyToShipNotification extends PWA_Iopn {

  
	public function __construct() {
	  
	}

  
	public function update_order_status ($data , $iopn_record_id) {
		global $wpdb,$woocommerce;
		
		$xml = simplexml_load_string($data);
		
		$NotificationReferenceId = $xml->NotificationReferenceId;
		$OrderChannel = $xml->ProcessedOrder->OrderChannel;
		$AmazonOrderID = $xml->ProcessedOrder->AmazonOrderID;
		$OrderDate = $xml->ProcessedOrder->OrderDate;
		
		$param['NotificationReferenceId'] = $NotificationReferenceId;
		$param['AmazonOrderID'] = $AmazonOrderID;
		$param['iopn_record_id'] = $iopn_record_id;

		$order_postmeta = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_pwa_order_id' and meta_value = '$AmazonOrderID' " );
		if(empty($order_postmeta)) 
		{
				$request_time = $wpdb->get_results( "select * from ".$prefix."iopn_records where amazon_order_id = '$AmazonOrderID' and notification_reference_id = '$NotificationReferenceId' and status = 'Rejected' " );
				if(!empty($request_time)) 
				{	
					$param['Status'] = 'Accepted';
					$this->update_request($param);
				
					$order =  wc_create_order();
					add_post_meta($order->id, '_pwa_order_id', $AmazonOrderID );
					
					$this->update_order_detail($order->id , $data);
				}
				else 
				{
					$param['Status'] = 'Rejected';
					$this->update_request($param);
				
					header('HTTP/1.1 503 SERVICE_UNAVAILABLE');
					exit;
				}
		} 
		else 
		{	
			$param['Status'] = 'Accepted';
			$this->update_request($param);
			
			$order_id = $order_postmeta[0]->post_id;
			$this->update_order_detail($order_id , $data);
		}
	}
  
	public function update_order_detail($order_id , $data) {
		global $wpdb,$woocommerce,$pwa;
		
		$non_received = $wpdb->get_results( "select * from $wpdb->postmeta where meta_key = '_non_received' and post_id = $order_id " );
		if(empty($non_received))
		{
			$pwacheckkout = new Pwacheckout();
			if( $pwacheckkout->get_option('iopn_dump') == 'yes'){

				$dir = $pwacheckkout->get_option('iopn_dump_url');
				if (!file_exists($dir) && !is_dir($dir)) {
					mkdir($dir, 0777);
				} 

				$filename = $dir.$order_id.'_iopn_non';
			 	$myfile = fopen($filename, "w");
			 	fwrite($myfile, $data);
			 	fclose($myfile);
			}
			
			add_post_meta($order_id, '_non_received', '1' );
				
			if(UPDATE_ODER_FROM == 'xmlcart')
				$this->update_cart_by_xml($order_id,$data);
			else
				$this->update_cart_by_woocart($order_id,$data);
		}
		else
		{
			$this->update_detail($order_id,$data);
		}
		
		$order = new WC_Order($order_id);
		$order->update_status('processing');
		
		// Acknowledge the order in seller central using MWS FEED API
        $pwa->pwa_acknowledge_feed($order_id); 
	
		header('HTTP/1.1 200 OK');
		exit;
	}
  
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
	
	
	public function update_cart_by_xml($order_id,$data){
    global $wpdb,$woocommerce,$pwa;
      
      $xml = simplexml_load_string($data);
      
      $order = new WC_Order($order_id);
      
      $billing_address = array(
			'first_name' => (string)$xml->ProcessedOrder->BuyerInfo->BuyerName,
			'last_name'  => '',
			'company'    => '',
			'email'      => (string)$xml->ProcessedOrder->BuyerInfo->BuyerEmailAddress,
			'phone'      => (string)$xml->ProcessedOrder->ShippingAddress->PhoneNumber,
			'address_1'  => '',
			'address_2'  => '', 
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => ''
		);

		
      
      $shipping_address = array(
        'first_name' => (string)$xml->ProcessedOrder->ShippingAddress->Name,
        'last_name'  => '',
        'company'    => '',
        'email'      => '',
        'phone'      => (string)$xml->ProcessedOrder->ShippingAddress->PhoneNumber,
        'address_1'  => (string)$xml->ProcessedOrder->ShippingAddress->AddressFieldOne,
        'address_2'  => (string)$xml->ProcessedOrder->ShippingAddress->AddressFieldTwo, 
        'city'       => (string)$xml->ProcessedOrder->ShippingAddress->City,
        'state'      => (string)$xml->ProcessedOrder->ShippingAddress->State,
        'postcode'   => (string)$xml->ProcessedOrder->ShippingAddress->PostalCode,
        'country'    => (string)$xml->ProcessedOrder->ShippingAddress->CountryCode
      );

      $order->set_address( $shipping_address, 'shipping' );
       
      
      add_post_meta($order_id, '_payment_method', 'pwa');
      add_post_meta($order_id, '_payment_method_title', 'Pay with Amazon');
      
      $total_amount = 0;
      $shipping_amount = 0;
      $total_promo = 0;
      try {
		  
		  foreach($xml->ProcessedOrder->ProcessedOrderItems->ProcessedOrderItem as $item) {
			$SKU = (string)$item->SKU;  
			$Title = (string)$item->Title;  
			$Amount = (float)$item->Price->Amount;
			$ClientRequestId = (int)$item->ClientRequestId;
			$other_promo = 0;
			foreach($item->ItemCharges->Component as $amount_type) {
			  
			  $item_charge_type = (string)$amount_type->Type; 
			  if($item_charge_type == 'Principal') {
				$principal = (string)$amount_type->Charge->Amount;
			  }
			  if($item_charge_type == 'Shipping') {
				$Shipping = (string)$amount_type->Charge->Amount;
			  }
			  if($item_charge_type == 'PrincipalPromo') {
				$principal_promo = (string)$amount_type->Charge->Amount;
			  }
			  if($item_charge_type == 'ShippingPromo') {
				$shipping_promo = (string)$amount_type->Charge->Amount;
			  }
			  if($item_charge_type == 'OtherPromo') {
				$other_promo = (string)$amount_type->Charge->Amount;
			  }
			}
			$CurrencyCode = (string)$item->Price->CurrencyCode; 
			$Quantity = (int)$item->Quantity; 
			$Category = $item->Category;  
			$variation_id = (int)$item->ItemCustomData->Variation_id;
			$product_id = (int)$item->ItemCustomData->Product_id;

			$product = array();
			$product['order_item_name'] = $Title;
			$product['order_item_type'] = 'line_item';
			$order_item_id = wc_add_order_item( $order_id, $product );
			
			wc_add_order_item_meta( $order_item_id, '_variation_id', $variation_id);
			wc_add_order_item_meta( $order_item_id, '_product_id' , $product_id);
			wc_add_order_item_meta( $order_item_id, '_qty' , $Quantity);
			wc_add_order_item_meta( $order_item_id, '_line_total' , ($principal - $principal_promo- $shipping_promo));
			wc_add_order_item_meta( $order_item_id, '_line_subtotal' , $principal);
			wc_add_order_item_meta( $order_item_id, '_line_subtotal_tax', 0);
			wc_add_order_item_meta( $order_item_id, '_line_tax', 0);
			
			//$product = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = '_sku' and meta_value = '$SKU' " );
			//$product_id = $product[0]->post_id;
			//wc_add_order_item_meta( $order_item_id, '_product_id' , $product_id);
	
			try {
				foreach ($item->ItemCustomData->Item_attribute as $value) {
				  $att_name = str_replace("attribute_","",(string)$value->Attribute_name);
				  $att_name = trim($att_name);
				  wc_add_order_item_meta( $order_item_id, $att_name, (string)$value->Attribute_val);
				}
			} catch (Exception $e) {
				 	$param['message'] = 'IOPN Notifications : Caught exception : '.$e->getMessage().'.';
					$this->generate_log($param);
			  }
			
			$this->reduce_order_stock($product_id ,  $Quantity);
			
			/*
			 * Total Item Charge = (Principal - PrincipalPromo) + (Shipping - ShippingPromo) + Tax + ShippingTax
			 */

			$total_amount += ($principal - $principal_promo) + ($Shipping - $shipping_promo) ;
			
			$shipping_amount += $Shipping;

			$total_promo += $principal_promo + $shipping_promo + $other_promo;
		  }
      } catch (Exception $e) {
			 $param['message'] = 'IOPN Notifications : Caught exception : '.$e->getMessage().'.';
			 $this->generate_log($param);
	  }
	  
	  if($ClientRequestId == 0) {
			$order->set_address( $billing_address, 'billing' );
	  }else{
			unset($billing_address['email']);
			$order->set_address( $billing_address, 'billing' );
			update_post_meta($order_id, '_customer_user' , $ClientRequestId);
	  }
	  
      add_post_meta($order_id, '_order_total', $total_amount);
      add_post_meta($order_id, '_order_shipping', $shipping_amount);      
      add_post_meta($order_id, '_cart_discount', (float)$total_promo);

      $shipitem = array();
      $shipitem['order_item_name'] = (string)$xml->ProcessedOrder->ShippingServiceLevel;
      $shipitem['order_item_type'] = 'shipping';
      $order_shipping_id = wc_add_order_item( $order_id, $shipitem );
      
      wc_add_order_item_meta( $order_shipping_id, 'method_id' , str_replace(' ','_',strtolower((string)$xml->ProcessedOrder->ShippingServiceLevel)));
      wc_add_order_item_meta( $order_shipping_id, 'cost' , $shipping_amount);
      
      // Send notification mails to seller and customer for order 
      //$mail_class = new WC_Emails();
      //$mail_class->emails['WC_Email_New_Order']->trigger($order_id);
      
  }
	
	
	public function update_cart_by_woocart($order_id,$data) {
		global $wp;
		global $wpdb,$woocommerce,$pwa;
		
		$xml = simplexml_load_string($data);
		
		$order = new WC_Order($order_id);
		
		$billing_address = array(
			'first_name' => (string)$xml->ProcessedOrder->BuyerInfo->BuyerName,
			'last_name'  => '',
			'company'    => '',
			'email'      => (string)$xml->ProcessedOrder->BuyerInfo->BuyerEmailAddress,
			'phone'      => '',
			'address_1'  => '',
			'address_2'  => '', 
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => ''
		);

		$order->set_address( $billing_address, 'billing' );
		
		$shipping_address = array(
			'first_name' => (string)$xml->ProcessedOrder->ShippingAddress->Name,
			'last_name'  => '',
			'company'    => '',
			'email'      => '',
			'phone'      => '',
			'address_1'  => (string)$xml->ProcessedOrder->ShippingAddress->AddressFieldOne,
			'address_2'  => (string)$xml->ProcessedOrder->ShippingAddress->AddressFieldTwo, 
			'city'       => (string)$xml->ProcessedOrder->ShippingAddress->City,
			'state'      => (string)$xml->ProcessedOrder->ShippingAddress->State,
			'postcode'   => (string)$xml->ProcessedOrder->ShippingAddress->PostalCode,
			'country'    => (string)$xml->ProcessedOrder->ShippingAddress->CountryCode
		);

		$order->set_address( $shipping_address, 'shipping' );
	   
		add_post_meta($order_id, '_payment_method', 'pwa');
		add_post_meta($order_id, '_payment_method_title', 'Pay with Amazon');
		
		$total_amount = 0;
		$subtotal_amount = 0;
		$shipping_amount = 0;
		$ClientRequestId = 0;
		try {
			foreach($xml->ProcessedOrder->ProcessedOrderItems->ProcessedOrderItem as $item) {
				// XML DATA
				$ClientRequestId = (int)$item->ClientRequestId;	
				foreach($item->ItemCharges->Component as $amount_type) {
					
					$item_charge_type = (string)$amount_type->Type;	
					if($item_charge_type == 'Shipping') {
						$Shipping = (string)$amount_type->Charge->Amount;
					}
				}
				$shipping_amount = $shipping_amount + $Shipping;
			}
		} catch (Exception $e) {
			 $param['message'] = 'IOPN Notifications : Caught exception : '.$e->getMessage().'.';
			 $this->generate_log($param);
		}	
			// CART DATA
			$cartdata = '';
			$user_id = 0;
			$prefix  = $wpdb->prefix;
			$carts = $wpdb->get_results( "SELECT * FROM `" . $prefix . "pwa_before_cart_save` WHERE id = $ClientRequestId ");
			foreach($carts as $key => $value) {
				$cartdata = maybe_unserialize($value->cart_data);
				$user_id  = $value->user_id;
			}
			  
		    update_post_meta($order_id, '_customer_user' , $user_id);
				
			// ENTRY
			try {
				foreach($cartdata->cart_contents as $key => $value) {
						$product_id = $value['product_id'];
						$cart_product  = get_product( $product_id );
						
						$product = array();
						$product['order_item_name'] = $cart_product->get_title();
						$product['order_item_type'] = 'line_item';
						$order_item_id = wc_add_order_item( $order_id , $product );
						
						
						wc_add_order_item_meta( $order_item_id, '_qty' , $value['quantity']);
						wc_add_order_item_meta( $order_item_id, '_product_id' , $product_id);
						wc_add_order_item_meta( $order_item_id, '_line_total' , $value['line_total']);
						wc_add_order_item_meta( $order_item_id, '_line_subtotal' , $value['line_subtotal']);
						wc_add_order_item_meta( $order_item_id, '_line_tax' , $value['line_tax']);
						wc_add_order_item_meta( $order_item_id, '_line_subtotal_tax' , $value['line_subtotal_tax']);
						wc_add_order_item_meta( $order_item_id, '_line_tax_data' , maybe_serialize($value['line_tax_data']));
						
						foreach($value['line_tax_data']['total'] as $tax_rate_id => $tax_data){
							
							$tax_class =  $wpdb->get_results( "SELECT * FROM `" . $prefix . "woocommerce_tax_rates` WHERE tax_rate_id = $tax_rate_id " );
							wc_add_order_item_meta( $order_item_id, '_tax_class' , $tax_class[0]->tax_rate_class);
						}
						
						if($value['variation_id'] > 0) {
							wc_add_order_item_meta( $order_item_id, '_variation_id' , $value['variation_id']);
						}
						
						foreach($value['variation'] as $attrib_key => $attrib_value) {
							$meta_key = str_replace('attribute_','',$attrib_key);
							wc_add_order_item_meta( $order_item_id, $meta_key , $attrib_value);
						}
						
						$this->reduce_order_stock($product_id , $value['quantity']);
						
						$total_amount = $total_amount + $value['line_total'] + $value['line_tax'];
						
						$subtotal_amount = $subtotal_amount + $value['line_subtotal'];
				}
			} catch (Exception $e) {
			   $param['message'] = 'IOPN Notifications : Caught exception : '.$e->getMessage().'.';
			   $this->generate_log($param);
			}	
			
					
		add_post_meta($order_id, '_order_total', $total_amount+$shipping_amount);
		add_post_meta($order_id, '_order_shipping', $shipping_amount);
		
		add_post_meta($order_id, '_cart_discount', $cartdata->discount_cart);
		add_post_meta($order_id, '_cart_discount_tax', $cartdata->discount_cart_tax);
		add_post_meta($order_id, '_order_tax', $cartdata->tax_total);
		
		$shipitem = array();
		$shipitem['order_item_name'] = (string)$xml->ProcessedOrder->ShippingServiceLevel;
		$shipitem['order_item_type'] = 'shipping';
		$order_shipping_id = wc_add_order_item( $order_id, $shipitem );
		
		wc_add_order_item_meta( $order_shipping_id, 'method_id' , str_replace(' ','_',strtolower((string)$xml->ProcessedOrder->ShippingServiceLevel)));
		wc_add_order_item_meta( $order_shipping_id, 'cost' , $shipping_amount);
		
		
		if(!empty($cartdata->taxes)) {
			foreach($cartdata->taxes as $key => $value){
				$order->add_tax( $key , $value );
			}
		}
		
		if(!empty($cartdata->applied_coupons)) {
			foreach($cartdata->applied_coupons as $key => $value){
				$order->add_coupon( $value, $cartdata->coupon_discount_amounts[$value], $cartdata->coupon_discount_tax_amounts[$value] );
			}
		}
		
		// Send notification mails to seller and customer for order 
		//$mail_class = new WC_Emails();
        //$mail_class->emails['WC_Email_New_Order']->trigger($order_id);
        
	}
	
	
	public function update_request($param) {
		global $wpdb,$woocommerce;
		$prefix = $wpdb->prefix . 'pwa_';
		
		$wpdb->update($prefix . 'iopn_records', array(
			'notification_reference_id'   => $param['NotificationReferenceId'],
			'amazon_order_id'   => $param['AmazonOrderID'],
			'status'   => $param['Status'],
		),array('id'=>$param['iopn_record_id']));
	}
	
	public function update_detail($order_id,$data){
		
	  global $wpdb,$woocommerce,$pwa;
      
      $xml = simplexml_load_string($data);
      
      $order = new WC_Order($order_id);
      
      $billing_address = array(
			'phone'      => (string)$xml->ProcessedOrder->ShippingAddress->PhoneNumber
		);
     
      $shipping_address = array(
        'phone'      => (string)$xml->ProcessedOrder->ShippingAddress->PhoneNumber
      );

      $order->set_address( $billing_address, 'billing' );
      $order->set_address( $shipping_address, 'shipping' );
	}
  
}
?>
