<?php

require_once(PWA_INCLUDE_DIR."includes/signature/common/cart/CartFactory.php");

/**
 * Returns a simple static cart to generate a signature from,
 * and the final complete cart html.
 *
 * Copyright 2008-2011 Amazon.com, Inc., or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *    http://aws.amazon.com/apache2.0/
 *
 * or in the "license" file accompanying this file.
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

class XMLCartFactory extends CartFactory {

   protected static $CART_ORDER_INPUT_FIELD ="type:merchant-signed-order/aws-accesskey/1;order:[ORDER];signature:[SIGNATURE];aws-access-key-id:[AWS_ACCESS_KEY_ID]";

  

   public function XMLCartFactory() {
      add_action('plugins_loaded', 'XMLCartFactory');
   }



   /**
    * Gets cart html fragment used to generate entire cart html
    * Base 64 encode the cart.
    * 
    */

    public function getCart($merchantID, $awsAccessKeyID) {
        $cartXML = $this->getCartXML($merchantID, $awsAccessKeyID);
        return base64_encode($cartXML);
    }

   

   /**
    * Returns the concatenated cart used for signature generation.
    * @see CartFactory
    */

   public function getSignatureInput($merchantID, $awsAccessKeyID) {
        return $this->getCartXML($merchantID, $awsAccessKeyID);
   }


   /**
    * Returns a finalized full cart html including the base 64 encoded cart,
    * signature, and buy button image link.
    */

   public function getCartHTML($merchantID, $awsAccessKeyID, $signature) {
        $cartHTML = '';
        if(WORK_ENVIRONMENT == 'prod') 
          $cartHTML = $cartHTML . CartFactory::$PROD_CART_JAVASCRIPT_START;
        else
          $cartHTML = $cartHTML . CartFactory::$SAND_CART_JAVASCRIPT_START;

        if(CUSTOM_CHECKOUT_IMAGE)
          $cartHTML = $cartHTML . "<div id=\"cbaButton\"><img style=\"cursor: pointer;\" src= \"".CUSTOM_CHECKOUT_IMAGE."\" id = \"customamazonbutton\" onclick = \"clickoriginalamazon()\" /></div>";
        else
          $cartHTML = $cartHTML . CartFactory::$CBA_BUTTON_DIV;
              
        // construct the order-input section
        $encodedCart = $this->getCart($merchantID, $awsAccessKeyID);
        $input = preg_replace("/\\[ORDER\\]/", $encodedCart, XMLCartFactory::$CART_ORDER_INPUT_FIELD);
        $input = preg_replace("/\\[SIGNATURE\\]/", $signature, $input);
        $input = preg_replace("/\\[AWS_ACCESS_KEY_ID\\]/", $awsAccessKeyID, $input);

        if(CUSTOM_CHECKOUT_IMAGE)
          $widgetScript = preg_replace("/\\[CART_TYPE\\]/", "XML",CartFactory::$STANDARD_CHECKOUT_WIDGET_SCRIPT_CUSTOM);
        else
          $widgetScript = preg_replace("/\\[CART_TYPE\\]/", "XML",CartFactory::$STANDARD_CHECKOUT_WIDGET_SCRIPT);
        
        $widgetScript = preg_replace("/\\[MERCHANT_ID\\]/", $merchantID,$widgetScript);
        $widgetScript =preg_replace ("/\\[CART_VALUE\\]/",$input ,$widgetScript);
        $widgetScript =preg_replace ("/\\[PWA_BTN_COLOR\\]/",PWA_BTN_COLOR,$widgetScript);
        $widgetScript =preg_replace ("/\\[PWA_BTN_BKGD\\]/",PWA_BTN_BKGD,$widgetScript);
        $widgetScript =preg_replace ("/\\[PWA_BTN_SIZE\\]/",PWA_BTN_SIZE,$widgetScript);

        $cartHTML = $cartHTML . $widgetScript;        

        return $cartHTML;
   }

  

    /**
     * Replace with your own cart here to try out
     * different promotions, tax, shipping, etc. 
     * 
     * @param merchantID
     * @param awsAccessKeyID
     */

    private function getCartXML($merchantID, $awsAccessKeyID) {

        return $this->simple_product_xml($merchantID, $awsAccessKeyID);
    }


    public function simple_product_xml($merchantID, $awsAccessKeyID)
    {
        global  $wpdb;
        $client_id = '';
        $prefix = $wpdb->prefix;
        $table = $prefix."pwa_before_cart_save";
        $sess_data = new WC_Session_Handler();
        $sess_detail = $sess_data->get_session_cookie();
        $sess_cust_id = $sess_detail[0];
        if ( is_user_logged_in() )
        {
          $user_ID = get_current_user_id();
        }
        else
        {
          $user_ID = 0;
        }
        if(UPDATE_ODER_FROM == 'woocart')
        {
                $query = 'SELECT id FROM '.$table.' where user_id = "'.$user_ID.'" ORDER BY id DESC LIMIT 1';
                $result = $wpdb->get_results($query);
                $client_id = $result[0]->id;
        }
        else
                $client_id = $user_ID;

        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $root = $doc->createElement('Order');
        $attribute = $doc->createAttribute('xmlns');
        $attribute->value = 'http://payments.amazon.com/checkout/2009-05-15/';
        $root_attr = $root->appendChild($attribute);
        $root = $doc->appendChild($root);

        $ClientRequestId = $doc->createElement('ClientRequestId',$client_id);
        $ClientRequestId = $root->appendChild($ClientRequestId);

        $Cart = $doc->createElement('Cart');
        $Cart = $root->appendChild($Cart);

        $Items = $doc->createElement('Items');
        $Items = $Cart->appendChild($Items);

        $CartPromotionId = $doc->createElement('CartPromotionId','Total_Discount');
        $CartPromotionId = $Cart->appendChild($CartPromotionId);

        $Promotions = $doc->createElement('Promotions');
        $Promotions = $root->appendChild($Promotions);

        $cartObj = WC()->cart;
        $all_fees = $cartObj->get_fees();
        $real_cart = WC()->cart->get_cart();

        $_pf = new WC_Product_Factory();
        $currency = get_woocommerce_currency();
        $cart_total = 0;
        $total_fees = 0;
        $cart_total_wocom = WC()->cart->total;
        $cart_subtotal = $cartObj->subtotal;
        
        $amazon_total_disc = 0;
        
        $cart_discount_amount = $cartObj->get_cart_discount_total();
        $cart_coupon_code = '';
        
        if(!empty($cartObj->coupon_discount_tax_amounts))
        {
            foreach($cartObj->coupon_discount_tax_amounts as $key => $coupon)
            {
                $cart_coupon_code .= $key.","; 
                $cart_discount_amount += $coupon;
            }
        }
        else
        {
            $cart_discount_amount = (float)$cartObj->discount_cart + (float)$cartObj->discount_total;
        }
        
        $neg_fees_Amout = 0;
        foreach ($all_fees as $fee)
        {
              $fees_Amout = $fee->amount+$fee->tax;
              if($fees_Amout < 0)
              {
                   $neg_fees_Amout = abs($fees_Amout);
              }
        }
  
        $cart_discount_amount += $neg_fees_Amout;
        $cart_discount_amount = (float)$cart_discount_amount;
        if($cart_discount_amount)
        {
            $Promotion = $doc->createElement('Promotion');
            $Promotion = $Promotions->appendChild($Promotion);

            $Promotion_pro_id = $doc->createElement('PromotionId','Total_Discount');
            $Promotion_pro_id = $Promotion->appendChild($Promotion_pro_id);

            $Promotion_pro_desc = $doc->createElement('Description','desc');
            $Promotion_pro_desc = $Promotion->appendChild($Promotion_pro_desc);

            $Promotion_pro_benf = $doc->createElement('Benefit');
            $Promotion_pro_benf = $Promotion->appendChild($Promotion_pro_benf);

            $Promotion_pro_benf_fad = $doc->createElement('FixedAmountDiscount');
            $Promotion_pro_benf_fad = $Promotion_pro_benf->appendChild($Promotion_pro_benf_fad);

            $Promotion_pro_benf_fad_amount = $doc->createElement('Amount',$cart_discount_amount);
            $Promotion_pro_benf_fad_amount = $Promotion_pro_benf_fad->appendChild($Promotion_pro_benf_fad_amount);

            $Promotion_pro_benf_fad_currency = $doc->createElement('CurrencyCode',$currency);
            $Promotion_pro_benf_fad_currency = $Promotion_pro_benf_fad->appendChild($Promotion_pro_benf_fad_currency);
        }
        foreach($real_cart as $key => $item)
        { 
            
            $product_id = (isset($item['variation_id']) && $item['variation_id']!='') ? $item['variation_id'] : $item['product_id'];

            $variation_id = $item['variation_id'];
            $productObj = new WC_Product($product_id);

            $product = $_pf->get_product($product_id);

            $sku = $product->get_sku();
            $sku = substr($sku,0,40);
      
            $title = $product->get_title();
            if(!$title)
                $title = 'Title';

            $title = substr($title,0,80);
           
            $description = $product->get_post_data()->post_content;
            $description = substr($description,0,1900);
           
            $quantity = $item['quantity'];
            $weight = $product->get_weight();
            $weight_unit = get_option('woocommerce_weight_unit');
            if($weight_unit != 'kg' && $weight > 0)
            {
              $weight = $weight/1000 ;
              $weight_unit = 'kg';
            }
            
            $variations = (isset($item['variation']) && $item['variation']!='' ) ? $item['variation'] : array();
            
            if($cartObj->prices_include_tax)
            {
              $product_price = ($item['line_subtotal']+$item['line_subtotal_tax'])/$quantity;
            }
            else
            {
              $product_price = $item['line_subtotal']/$quantity;
            }

            $amazon_product_price = ($item['line_subtotal']+$item['line_subtotal_tax'])/$quantity;
            
            $cart_total += $amazon_end_line_subtotal;

            /** create xml of the basic product **/

            $Item = $doc->createElement('Item');
            $Item = $Items->appendChild($Item);
            
            $sku = $this->replace_char($sku);
            $sku = htmlentities($sku,ENT_QUOTES,'UTF-8');

            $SKU = $doc->createElement('SKU',$sku);
            $SKU = $Item->appendChild($SKU);

            $MerchantId = $doc->createElement('MerchantId',$merchantID);
            $MerchantId = $Item->appendChild($MerchantId);

            $title =  $this->replace_char($title);
            $title = htmlentities($title,ENT_QUOTES,'UTF-8');
            
            $Title = $doc->createElement('Title',$title);
            $Title = $Item->appendChild($Title);

            $description =  $this->replace_char($description);
            $description = htmlentities($description,ENT_QUOTES,'UTF-8');

            $Description = $doc->createElement('Description',$description);
            $Description = $Item->appendChild($Description);

            $Price = $doc->createElement('Price');
            $Price = $Item->appendChild($Price);

            $Amount = $doc->createElement('Amount',$amazon_product_price);
            $Amount = $Price->appendChild($Amount);

            $CurrencyCode = $doc->createElement('CurrencyCode',$currency);
            $CurrencyCode = $Price->appendChild($CurrencyCode);

            $Quantity = $doc->createElement('Quantity',$quantity);
            $Quantity = $Item->appendChild($Quantity);

            if($weight)
            {
                $Weight = $doc->createElement('Weight');
                $Weight = $Item->appendChild($Weight);

                $Amount_wt = $doc->createElement('Amount',$weight);
                $Amount_wt = $Weight->appendChild($Amount_wt);

                $Wt_unit = $doc->createElement('Unit',$weight_unit);
                $Wt_unit = $Weight->appendChild($Wt_unit);
            }
            
            $ItemCustomData = $doc->createElement('ItemCustomData');
            $ItemCustomData = $Item->appendChild($ItemCustomData);

            $Product_id = $doc->createElement('Product_id',$item['product_id']);
            $Product_id = $ItemCustomData->appendChild($Product_id);

            $Variation_id = $doc->createElement('Variation_id',$variation_id);
            $Variation_id = $ItemCustomData->appendChild($Variation_id);

            foreach ($variations as $key => $variant) 
            {
                $Item_attribute = $doc->createElement('Item_attribute');
                $Item_attribute = $ItemCustomData->appendChild($Item_attribute);
                
                $key = substr($key,0,40);
        
                $key = $this->replace_char($key);
                $key = htmlentities($key,ENT_QUOTES,'UTF-8');

                $Attribute_name = $doc->createElement('Attribute_name',$key);
                $Attribute_name = $Item_attribute->appendChild($Attribute_name);

                $variant = substr($variant,0,40);
                $variant = $this->replace_char($variant);
                $variant = htmlentities($variant,ENT_QUOTES,'UTF-8');

                $Attribute_val = $doc->createElement('Attribute_val',$variant);
                $Attribute_val = $Item_attribute->appendChild($Attribute_val);            
            }
          
        }
        
        foreach ($all_fees as $fee)
        {
            $fees_Amout = $fee->amount+$fee->tax;
            if($fees_Amout > 0)
            {
              $Item = $doc->createElement('Item');
              $Item = $Items->appendChild($Item);

              $fee_id = substr($fee->id,0,40);
              $fee_id = $this->replace_char($fee_id);
              $fee_id = htmlentities($fee_id,ENT_QUOTES,'UTF-8');

              $SKU = $doc->createElement('SKU',$fee_id);
              $SKU = $Item->appendChild($SKU);

              $MerchantId = $doc->createElement('MerchantId',$merchantID);
              $MerchantId = $Item->appendChild($MerchantId);
        
              $fee_name = substr($fee->name,0,80);
        
              if(!$fee_name)
                $fee_name = 'Order Fees';
                
              $fee_name = $this->replace_char($fee_name);
              $fee_name = htmlentities($fee_name,ENT_QUOTES,'UTF-8');
        
              $Title = $doc->createElement('Title',$fee_name);
              $Title = $Item->appendChild($Title);

              $Description = $doc->createElement('Description','extra fees');
              $Description = $Item->appendChild($Description);

              $Price = $doc->createElement('Price');
              $Price = $Item->appendChild($Price);

              $Amount = $doc->createElement('Amount',$fees_Amout);
              $Amount = $Price->appendChild($Amount);

              $CurrencyCode = $doc->createElement('CurrencyCode',$currency);
              $CurrencyCode = $Price->appendChild($CurrencyCode);

              $Quantity = $doc->createElement('Quantity',1);
              $Quantity = $Item->appendChild($Quantity);
            }
            
        }
        return $doc->saveXML();
    }
    
    public function replace_char($string)
    {
          $string = str_replace('&','',$string);
          $string = str_replace('<','',$string);
          $string = str_replace('>','',$string);
          $string = str_replace('"','',$string);
          $string = str_replace("'","",$string);
          return $string;
    }

}
