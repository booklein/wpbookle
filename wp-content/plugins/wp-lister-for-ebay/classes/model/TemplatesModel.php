<?php

class TemplatesModel extends WPL_Model {

	public $foldername;
	public $folderpath;
	public $stylesheet;
	public $fields = array();

	function TemplatesModel( $foldername = false )
	{
		// global $wpl_logger;
		// $this->logger = &$wpl_logger;

		if ( $foldername ) {

			// folder name
			$foldername = basename($foldername);
			$this->foldername = $foldername;

			// full absolute paths
			$upload_dir = wp_upload_dir();
			$this->folderpath = $upload_dir['basedir'] . '/wp-lister/templates/' . $foldername;
			$this->stylesheet = $this->folderpath . '/style.css';

			// save / return item (?)
			$this->item = $this->getItem( $foldername );
			return $this->item;
		}
	}
	

	function getAll() {

		// get user templates
		$upload_dir = wp_upload_dir();

		// if there is a problem with the uploads folder, wp might return an error
		if ( $upload_dir['error'] ) {
			$this->showMessage( $upload_dir['error'], 1, true );
			return array();
		}

		$templates = array();
		$files = glob( $upload_dir['basedir'].'/wp-lister/templates/*/template.html' );
		if ( is_array($files) ) {
			foreach ($files as $file) {
				// save template path relative to WP_CONTENT_DIR
				// $file = str_replace(WP_CONTENT_DIR,'',$file);
				$file = basename(dirname( $file ));
				$templates[] = $this->getItem( $file );
			}		
		}

		return $templates;	
	}

	function getBuiltIn() {

		// get build in templates
		$files = glob( WPLISTER_PATH . '/templates/*/template.html' );

		$templates = array();
		foreach ($files as $file) {
			// save template path relative to WP_CONTENT_DIR
			$file = str_replace(WP_CONTENT_DIR,'',$file);
			$templates[] = $this->getItem( $file, false, 'built-in' );
		}

		return $templates;	
	}

	function getItem( $foldername = false, $fullpath = false, $type = 'user' ) {

		// set templates root folder
		$upload_dir = wp_upload_dir();
		$templates_dir = $upload_dir['basedir'].'/wp-lister/templates/';

		if ( $fullpath ) {
			// do nothing
		} elseif ( $foldername ) {
			$fullpath = $templates_dir . $foldername;
		} else {
			$fullpath = $this->folderpath;
		}

		// build item
		$item = array();

		// default template name
		$item['template_name'] = basename($fullpath);
		$item['template_path'] = str_replace(WP_CONTENT_DIR,'',$fullpath);

		// last modified date
		$item['last_modified'] = file_exists($fullpath.'/template.html') ? filemtime($fullpath.'/template.html') : time();

		// template type
		$item['type'] = $type;

		// template slug
		$item['template_id'] = urlencode( $item['template_name'] );

		// check css file for more info
		$stylesheet = $fullpath . '/style.css';
		if ( file_exists( $stylesheet ) ) {

			// $stylesheet = dirname( )
			$tplroot = realpath( dirname($stylesheet).'/..' );
			$tplfolder = basename(dirname($stylesheet));
			
			// wp_get_theme is WP 3.4+ only
			if ( function_exists('wp_get_theme')) {
				$tpl = wp_get_theme( $tplfolder, $tplroot );
				// echo "<pre>";print_r($tpl);echo "</pre>";
				$item['template_name'] = $tpl->Template;
				$item['template_version'] = $tpl->Version;
				$item['template_description'] = $tpl->Description;
			} else {
				$item['template_name'] = basename($fullpath);
				$item['template_version'] = '';
				$item['template_description'] = 'Please update to WordPress 3.4';				
			}
		}


		return $item;		
	}


	function newItem() {
		$item = array(
			"template_id" => false,
			"template_name" => "New listing template",
			"template_path" => "enter a unique folder name here",
			"template_version" => "1.2",
			"template_description" => ""
		);
		$this->folderpath = WPLISTER_PATH . '/templates/default';
		return $item;		
	}


	// check syntax for php file (lint)
	public function checkSyntax( $file ) {

		// disable syntax check by default
		if ( get_option( 'wplister_enable_php_syntax_check', 0 ) != 1 )
			return true;

		// check if exec() is enabled
	    // $exec_enabled =
	    //     function_exists('exec')                                            					&&
	    //     ! in_array('exec', array_map('trim',explode(',', ini_get('disable_functions'))))   &&
	    //     ! (strtolower( ini_get( 'safe_mode' ) ) != 'off')
	    // ;

		// if ( ! $exec_enabled ) {
	    	// echo "exec is disabled<br>";
	    	// echo "<pre>disabled functions: ";print_r( explode(',',ini_get('disable_functions')) );echo"</pre>";#die();
	    	// echo "<pre>exec() exists: ";print_r( function_exists('exec') );echo"</pre>";#die();
	    	// echo "<pre>safe mode: ";print_r( ini_get( 'safe_mode' ) );echo"</pre>";#die();
	    	// phpinfo();
	    	// return true;
		// }

		// attempt to call php -l
		try {

			// call php lint
	        $cmd = 'php -l ' . $file;
    	    exec( $cmd, $output, $retval );
    	    // echo "<pre>out: ";print_r($output);echo"</pre>";#die();
    	    // echo "<pre>ret: ";print_r($retval);echo"</pre>";#die();

		} catch (Exception $e) {
		    // if exec() fails to execute, pass syntax check
		    echo 'Exception caught while checking syntax of php code: ',  $e->getMessage(), "<br>";
		    echo "<pre>";print_r($e);echo"</pre>";#die();
		    return true;
		}

		// process result
        if ( is_array($output) ) {
        	if ( $output[0] == '' ) unset( $output[0] ); // remove empty first line
        	$output = join('<br>',$output);	
        } 
        // echo "<pre>";print_r($output);echo"</pre>";die();

        // check for syntax errors
        if ( $output && ( substr($output, 0, 16 ) != 'No syntax errors') ) {
        	$this->showMessage( 'There is a problem with some PHP code in your template. Please fix the following error:<br><br><code>'.$output.'</code>', 1, 1 );
        	return false;
        }

        // all good
        return true;
	}


	// initialize listing template
	public function initTemplate( $check_syntax = false ) {
		global $wpl_tpl_fields;

		if ( ! $this->folderpath ) {
			$this->showMessage("No template was assigned to this listing. Please check your listing profile and either re-apply the current profile or select a different listing profile.",1,1);
			// echo "<pre>";debug_print_backtrace();
			return false;
		}

		if ( ! file_exists( $this->folderpath . '/template.html' ) ) {
			$this->showMessage("Template file is missing: ".$this->folderpath . '/template.html',1,1);
			return false;
		}

		// load functions.php if present
		$file = $this->folderpath . '/functions.php';
		if ( file_exists($file) ) {

			if ( ( ! $check_syntax ) || $this->checkSyntax( $file ) ) {
				// echo "<pre>";print_r( debug_backtrace() );echo"</pre>";die();
				include_once( $file );
				do_action( 'wplister_template_init' );
			}

		}

		// load config.json
		$config_file = $this->folderpath . '/config.json';
		if ( file_exists($config_file) ) {
			$config = @json_decode( file_get_contents($config_file), true );
			// echo "<pre>";print_r($config);echo"</pre>";die();
			if ( $config && is_array($config)) {
				$this->config = $config;

				// echo "<pre>";print_r($config);echo"</pre>";die();
				foreach ($config as $key => $value) {
					if ( isset( $wpl_tpl_fields[$key] ) ) {
						$wpl_tpl_fields[$key]->value = $value;
					}
				}
			}

			$this->fields = $wpl_tpl_fields;
		}

	}


	public function processItem( $item, $ItemObj = false, $preview = false ) {
		
		$ibm = new ItemBuilderModel();

		// let other plugin know we are doing an eBay listing
   		if ( ! defined( 'WPL_EBAY_LISTING' ) )
   			define( 'WPL_EBAY_LISTING', true );
  
		// let other plugin know we are doing an eBay listing
   		if ( $preview && ! defined( 'WPL_EBAY_PREVIEW' ) )
   			define( 'WPL_EBAY_PREVIEW', true );
  
		// load template content
		$this->initTemplate( $preview );
		$tpl_html = $this->getContent();

		// handle errors
		if ( ! $tpl_html ) {
			WPLE()->logger->error( 'template not found ' . $item['template'] );
			WPLE()->logger->error( 'should be here: ' . WP_CONTENT_DIR . '/uploads/wp-lister/templates/' . $item['template']  );
			$this->showMessage( 'There was a problem processing your listing template',1,1);
			return '';
			// echo 'Template not found: '.$item['template'];
			// die();
		}
		// WPLE()->logger->debug( 'template loaded from ' . $tpl_path );
		// WPLE()->logger->info( $tpl_html );
		// TODO: check if $item['post_id'] point to a valid product. Could have been deleted...

		// custom template hook
		$tpl_html = apply_filters( 'wplister_before_process_template_html', $tpl_html, $item );


		// replace shortcodes
		$tpl_html = str_replace( '[[product_title]]', $ibm->prepareTitleAsHTML( $item['auction_title'] ), $tpl_html );


		// process simple text shortcodes (used for title as well)
		$tpl_html = $this->processAllTextShortcodes( $item['post_id'], $tpl_html, false, $ItemObj );

		// process custom fields
		$tpl_html = $this->processCustomFields( $tpl_html );

		// process embedded code
		$tpl_html = $this->processEmbeddedCode( $tpl_html );

		// process ajax galleries
		$tpl_html = $this->processGalleryShortcodes( $item['id'], $tpl_html );

		// process item shortcodes
		$tpl_html = $this->processEbayItemShortcodes( $item, $ItemObj, $tpl_html );


		// handle images...
		$main_image = $ibm->getProductMainImageURL( $item['post_id'] );
		$images = $ibm->getProductImagesURL( $item['post_id'] );
		WPLE()->logger->debug( 'images found ' . print_r($images,1) );
		
		// [[product_main_image]]
		$the_main_image = '<img class="wpl_product_image" src="'.$main_image.'" alt="main product image" />';
		$tpl_html = str_replace( '[[product_main_image]]', $the_main_image, $tpl_html );

		// [[product_main_image_url]]
		$tpl_html = str_replace( '[[product_main_image_url]]', $main_image, $tpl_html );
		
		// handle [[add_img_1]] to [[add_img_12]]
		// and [[add_img_url_1]] to [[add_img_url_12]]
		for ($i=0; $i < 12; $i++) { 
			
			if ( isset( $images[ $i ] ) ) {
				$img_url = $images[ $i ];
				$img_tag = '<img class="wpl_additional_product_image img_'.($i+1).'" src="'.$img_url.'" />';
			} else {
				$img_url = '';
				$img_tag = '';
			}

			$tpl_html = str_replace( '[[img_'.($i+1).']]',     $img_tag, $tpl_html );
			$tpl_html = str_replace( '[[img_url_'.($i+1).']]', $img_url, $tpl_html );

		}

		// handle all additional images
		// [[additional_product_images]]
		$imagelist = $this->processThumbnailsShortcode( $images, $item );
		$tpl_html = str_replace( '[[additional_product_images]]', $imagelist, $tpl_html );

		// process wp shortcodes in listing template - if enabled
 		if ( 'full' == get_option( 'wplister_process_shortcodes', 'content' ) ) {
 			$tpl_html = do_shortcode( $tpl_html );
 		}

		// custom template hook
		$tpl_html = apply_filters( 'wplister_process_template_html', $tpl_html, $item, $images );

		// return html
		return $tpl_html;
	}


	public function processThumbnailsShortcode( $images, $item ) {
		
		$html = '';
		if ( ! count($images) > 1 ) return $html;

		// get path to thumbnails.php
		$view = WPLISTER_PATH.'/views/template/thumbnails.php';
		if ( $item ) {
			// if thumbnails.php exists in listing template, use it
			$thumbnails_tpl_file = WPLISTER_PATH.'/../../' . $item['template'] . '/thumbnails.php';
			if ( file_exists( $thumbnails_tpl_file ) ) $view = $thumbnails_tpl_file;
			// the above might fail if wp-content has been moved - better use wp_upload_dir() to get actual template path:
			$upload_dir = wp_upload_dir();
			$thumbnails_tpl_file = $upload_dir['basedir'] . '/wp-lister/templates/' . basename( $item['template'] ) . '/thumbnails.php';
			if ( file_exists( $thumbnails_tpl_file ) ) $view = $thumbnails_tpl_file;
		}

		// fetch content
		ob_start();
			include( $view );
			$html = ob_get_contents();
		ob_end_clean();

		// 	// loop all images
		// 	for ($i=0; $i < count($images); $i++) { 
		// 		$image_url  = $images[$i];
		// 		$image_alt  = basename( $images[$i] );
		// 		$js_hover   = "if (typeof wplOnThumbnailHover == 'function') wplOnThumbnailHover('".$image_url."');return false;";
		// 		$js_click   = "if (typeof wplOnThumbnailClick == 'function') wplOnThumbnailClick('".$image_url."');return false;";
		// 		$imagelist .= '<a onmouseover="'.$js_hover.'" onclick="'.$js_click.'" href="#">';
		// 		$imagelist .= '<img class="wpl_thumb thumb_'.($i+1).'" src="'.$image_url.'" alt="'.$image_alt.'" /></a>'."\n";
		// 	}

		return $html;
	}


	public function processEbayItemShortcodes( $item, $ItemObj, $tpl_html ) {
		if ( ! $ItemObj ) return $tpl_html;

		// ebay_item_id
		$tpl_html = str_replace( '[[ebay_item_id]]', $item['ebay_id'], $tpl_html );

		// ebay_store_category_id
		$tpl_html = str_replace( '[[ebay_store_category_id]]', $ItemObj->Storefront->StoreCategoryID, $tpl_html );

		// ebay_store_category_name
		$tpl_html = str_replace( '[[ebay_store_category_name]]', EbayCategoriesModel::getStoreCategoryName( $ItemObj->Storefront->StoreCategoryID ), $tpl_html );

		// ebay_store_url
		// TODO: fetch StoreURL for active account
		$user_details = get_option( 'wplister_ebay_user' );
		if ( isset( $user_details->StoreURL ) )
			$tpl_html = str_replace( '[[ebay_store_url]]', $user_details->StoreURL, $tpl_html );

		return $tpl_html;
	}


	public function processMainContentShortcode( $post_id, $tpl_html, $item ) {

		// use latest post_content from product
		$post = get_post( $item['post_id'] );
		$item['post_content'] = $post->post_content;


		// handle variations
		$variations_html = '';
        if ( ProductWrapper::hasVariations( $item['post_id'] ) ) {

        	// generate variations table
        	$variations_html = $this->getVariationsHTML( $item );

        	// add variations table to item description
        	if ( isset($item['profile_data']['details']['add_variations_table']) && $item['profile_data']['details']['add_variations_table'] ) {
        		$item['post_content'] .= $variations_html;
        	}

        }
		// replace shortcodes
		$tpl_html = str_replace( '[[product_variations]]', $variations_html, $tpl_html );

		// handle split variations - get description from parent post_id
		if ( ProductWrapper::isSingleVariation( $post_id ) ) {
			$post = get_post( $item['parent_id'] );
			$item['post_content'] = $post->post_content;
		}

		// handle addons
    	// generate addons table
    	$addons_html = $this->getAddonsHTML( $item );
    	// add addons table to item description
    	if ( isset($item['profile_data']['details']['add_variations_table']) && $item['profile_data']['details']['add_variations_table'] ) {
    		$item['post_content'] .= $addons_html;
    	}
		// replace shortcodes
		$tpl_html = str_replace( '[[product_addons]]', $addons_html, $tpl_html );

		
		// remove ALL links from post content by default
 		if ( 'default' == get_option( 'wplister_remove_links', 'default' ) ) {
			/* $item['post_content'] = preg_replace('#<a.*?>([^<]*)</a>#i', '$1', $item['post_content'] ); */
			// regex improved to work in cases like <a ...><b>text</b></a>
			/* $item['post_content'] = preg_replace('#<a.*?>(.*)</a>#iU', '$1', $item['post_content'] ); */
			// improved for multiple links per line case
			$item['post_content'] = preg_replace('#<a.*?>(.*?)</a>#i', ' $1 ', $item['post_content'] );
 		}

 		// fixed whitespace pasted from ms word
 		// details: http://stackoverflow.com/questions/1431034/can-anyone-tell-me-what-this-ascii-character-is
		$whitespace = chr(194).chr(160);
		$item['post_content'] = str_replace( $whitespace, ' ', $item['post_content'] );


		// process and insert main content
 		if ( 'off' == get_option( 'wplister_process_shortcodes', 'content' ) ) {

 			// off - do nothing, except wpautop() for proper paragraphs
	 		$tpl_html = str_replace( '[[product_content]]', wpautop( $item['post_content'] ), $tpl_html );

 		} elseif ( 'remove' == get_option( 'wplister_process_shortcodes', 'content' ) ) {

 			// remove - remove all shortcodes from product description
 			$post_content = $item['post_content'];

			// find and remove all placeholders
			if ( preg_match_all( '/\[([^\]]+)\]/', $post_content, $matches ) ) {
				foreach ($matches[0] as $placeholder) {
			 		$post_content = str_replace( $placeholder, '', $post_content );
				}
			}

			// insert content into template html
	 		$tpl_html = str_replace( '[[product_content]]', wpautop( $post_content ), $tpl_html );

 		} else {

 			// make sure, WooCommerce template functions are loaded (WC2.2)
 			if ( ! function_exists('woocommerce_product_loop_start') && version_compare( WC_VERSION, '2.2', '>=' ) ) {
 				// WC()->include_template_functions(); // won't work unless is_admin() == true
				include_once( dirname( WC_PLUGIN_FILE) . '/includes/wc-template-functions.php' );
 			}

 			// default - apply the_content filter to make description look the same as in WP
	 		$tpl_html = str_replace( '[[product_content]]', apply_filters('the_content', $item['post_content'] ), $tpl_html );

 		}

		return $tpl_html;
	} // processMainContentShortcode()


	public function processAllTextShortcodes( $post_id, $tpl_html, $max_length = false, $ItemObj = false ) {

		// get item object
		$listing_id    = WPLE_ListingQueryHelper::getListingIDFromPostID( $post_id );
		$item          = ListingsModel::getItem( $listing_id );

		// main content - [[product_content]] (unless updating title when saving product...)
		if ( ! isset( $_REQUEST['action'] ) || ( $_REQUEST['action'] != 'editpost' ) || isset( $_REQUEST['wpl_ebay_revise_on_update'] )  || isset( $_REQUEST['wpl_ebay_relist_on_update'] ) ) {
		 	$tpl_html = $this->processMainContentShortcode( $post_id, $tpl_html, $item );
		}

		// product excerpt
		$product_id = $item['parent_id'] ? $item['parent_id'] : $item['post_id']; // maybe use parent post_id (for split variations)
		$tpl_html = str_replace( '[[product_excerpt]]', 				        WPLE_ListingQueryHelper::getRawPostExcerpt( $product_id )  , $tpl_html );
		$tpl_html = str_replace( '[[product_excerpt_nl2br]]', 			 nl2br( WPLE_ListingQueryHelper::getRawPostExcerpt( $product_id ) ), $tpl_html );
		$tpl_html = str_replace( '[[product_additional_content]]', 	   wpautop( WPLE_ListingQueryHelper::getRawPostExcerpt( $product_id ) ), $tpl_html );
		$tpl_html = str_replace( '[[product_additional_content_nl2br]]', nl2br( WPLE_ListingQueryHelper::getRawPostExcerpt( $product_id ) ), $tpl_html );
		
		// product price
		$item_price = $item['price'];
		if ( $ItemObj && $ItemObj->StartPrice ) $item_price = $ItemObj->StartPrice->value;
		if ( $ItemObj && $ItemObj->Variations ) $item_price = $ItemObj->Variations->Variation[0]->StartPrice;
		$tpl_html = str_replace( '[[product_price]]', number_format_i18n( floatval($item_price), 2 ), $tpl_html );
		$tpl_html = str_replace( '[[product_price_raw]]', $item_price, $tpl_html );

		// product_category
		$tpl_html = str_replace( '[[product_category]]', ProductWrapper::getProductCategoryName( $post_id ), $tpl_html );

		// SKU
		$tpl_html = str_replace( '[[product_sku]]', ProductWrapper::getSKU( $post_id ), $tpl_html );

		// weight
		$tpl_html = str_replace( '[[product_weight]]', ProductWrapper::getWeight( $post_id, true ), $tpl_html );

		// dimensions
		$dimensions = ProductWrapper::getDimensions( $post_id );
		$width  = @$dimensions['width']  . ' ' . @$dimensions['width_unit'];
		$height = @$dimensions['height'] . ' ' . @$dimensions['height_unit'];
		$length = @$dimensions['length'] . ' ' . @$dimensions['length_unit'];
		$tpl_html = str_replace( '[[product_width]]' , $width,  $tpl_html );
		$tpl_html = str_replace( '[[product_height]]', $height, $tpl_html );
		$tpl_html = str_replace( '[[product_length]]', $length,  $tpl_html );		

		// attributes
		$tpl_html = $this->processAttributeShortcodes( $post_id, $tpl_html, $max_length );

		// custom meta
		$tpl_html = $this->processCustomMetaShortcodes( $post_id, $tpl_html, $max_length );

		return $tpl_html;
	}


	public function processGalleryShortcodes( $listing_id, $tpl_html ) {

		$gallery_types = array('new','featured','ending','related');

		foreach ($gallery_types as $type) {
			
			$url = admin_url( 'admin-ajax.php' ) . '?action=wpl_gallery&type='.$type.'&id='.$listing_id;

			// build attributes for iframe tag
			// $iframe_attributes_html = ' id="wpl_widget_new_listings" class="wpl_gallery" style="height:175px;width:100%;border:none;" src="'.$url.'" border="0" ';
			$iframe_attributes_array = array(
				'id'     => 'wpl_widget_'.$type.'_listings',
				'class'  => 'wpl_gallery',
				'style'  => 'height:175px; width:100%; border:none;',
				'src'    => $url,
				'border' => '0',
			);
			$iframe_attributes_array = apply_filters( 'wple_gallery_iframe_attributes', $iframe_attributes_array, $listing_id, $type );

			// convert array to attributes string
			$iframe_attributes_html = '';
			foreach ( $iframe_attributes_array as $key => $value ) {
				$iframe_attributes_html .= ' ' . $key   . '=';
				$iframe_attributes_html .= '"' . $value . '"';
			}

			// javascript iframe - workaround to list on eBay
			$html = '
			<script type="text/javascript">
				document.write("<"+"if"+"ra"+"me");
				document.write(" '.addslashes( $iframe_attributes_html ).' ");
				document.write("></"+"if"+"ra"+"me"+">");
			</script>
			';

			// plain iframe - wont verify, but works well for preview
			if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'preview_auction' ) {
				$html = '<iframe '.$iframe_attributes_html.'></iframe>';
			}
			if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'preview_template' ) {
				$html = '<iframe '.$iframe_attributes_html.'></iframe>';
			}

			// this is how the ebay billboard app does it:
			// (for reference only)
			// <script>document.write('<'+'sc'+'rip'+'t src=\'http://www.domain.com/viewer/'+'swfobject.js\'></'+'sc'+'rip'+'t>')</script><script>if (swfobject.hasFlashPlayerVersion('9.0.18')) {var headerFn = function() {var att = { data:'http://www.domain.com/viewer/ViewerApplication.swf', width:'840', height:'280' }; var par = { flashvars:'docId=-123456789&docType=header', wmode:'transparent', allowScriptAccess:'always' }; var id = 'billboardsHeaderContent'; var myObject = swfobject.createSWF(att, par, id); }; swfobject.addDomLoadEvent(headerFn); } </script>


			$tpl_html = str_replace( '[[widget_'.$type.'_listings]]' , $html,  $tpl_html );

		}

		return $tpl_html;
	}


	public function processAttributeShortcodes( $post_id, $tpl_html, $max_length = false ) {

		// check for attribute shortcodes
		if ( preg_match_all("/\\[\\[attribute_(.*)\\]\\]/uUsm", $tpl_html, $matches ) ) {

			// attribute shortcodes i.e. [[attribute_Brand]]
			$product_attributes = ProductWrapper::getAttributes( $post_id );
			WPLE()->logger->debug('processAttributeShortcodes() - product_attributes: '.print_r($product_attributes,1));

			// parent attribute for split child variations
			$parent_post_id    = ProductWrapper::getVariationParent( $post_id );
			$parent_attributes = $parent_post_id ? ProductWrapper::getAttributes( $parent_post_id ) : array();
			WPLE()->logger->debug('processAttributeShortcodes() - parent_attributes: '.print_r($parent_attributes,1));

			// process each found shortcode
			foreach ( $matches[1] as $attribute ) {

				// check product and parent attributes
				if ( isset( $product_attributes[ $attribute ] )){
					$attribute_value = $product_attributes[ $attribute ];
				} elseif ( isset( $parent_attributes[ $attribute ] )){
					$attribute_value = $parent_attributes[ $attribute ];
				} else {					
					$attribute_value = '';
				}

				// format multiple attribute values
				$attribute_value = str_replace( '|', '<br/>', $attribute_value );

				// replace placeholder
				$processed_html = str_replace( '[[attribute_'.$attribute.']]', $attribute_value,  $tpl_html );

				// check if string exceeds max_length after processing shortcode
				if ( $max_length && ( $this->mb_strlen( $processed_html ) > $max_length ) ) {
					$attribute_value = '';
					$processed_html = str_replace( '[[attribute_'.$attribute.']]', $attribute_value,  $tpl_html );
				}

				$tpl_html = $processed_html;

			}

		}
		return $tpl_html;
	}

	public function processCustomFields( $tpl_html ) {

		if ( ! is_array( $this->fields )) return $tpl_html;

		foreach ( $this->fields as $field ) {
			$tpl_html = str_replace( '[['.$field->slug.']]', $field->value,  $tpl_html );		
			$tpl_html = str_replace(  '$'.$field->slug.'', $field->value,  $tpl_html );		
		}

		return $tpl_html;
	}

	public function processEmbeddedCode( $tpl_html ) {

		// convert iframes to js
		if ( preg_match_all("/<iframe.*iframe>/uiUsm", $tpl_html, $matches ) ) {
			foreach ( $matches[0] as $iframe_html ) {

				$converted_iframe = addslashes( $iframe_html );
				$converted_iframe = str_ireplace('iframe', 'if"+"ra"+"me', $converted_iframe );
				$iframe_js = '<script>document.write("' . $converted_iframe . '");</script>';
				$tpl_html = str_replace( $iframe_html, $iframe_js,  $tpl_html );		

			}
		}

		return $tpl_html;
	}

	public function processCustomMetaShortcodes( $post_id, $tpl_html, $max_length = false ) {

		// custom meta shortcodes i.e. [[meta_Name]]
		if ( preg_match_all("/\\[\\[meta_(.*)\\]\\]/uUsm", $tpl_html, $matches ) ) {
			foreach ( $matches[1] as $meta_name ) {
				$meta_value = get_post_meta( $post_id, $meta_name, true );
				// $meta_value = wpautop( $meta_value ); // might do more harm than good 
				$meta_value = nl2br( $meta_value );      // required for WYSIWYG fields by Advanced Custom Field plugin (and probably others)
				$processed_html = str_replace( '[[meta_'.$meta_name.']]', $meta_value,  $tpl_html );		

				// check if string exceeds max_length after processing shortcode
				if ( $max_length && ( $this->mb_strlen( $processed_html ) > $max_length ) ) {
					$meta_value = '';
					$processed_html = str_replace( '[[meta_'.$meta_name.']]', $meta_value,  $tpl_html );		
				}

				$tpl_html = $processed_html;

			}
		}
		return $tpl_html;
	}


	function getAddonsHTML( $item ) {
        
        // get addons
        $addons = ProductWrapper::getAddons( $item['post_id'] );
        if ( sizeof($addons) == 0 ) return '';

        // build html table
        $addons_html .= '<table style="margin-bottom: 8px;">';
        foreach ($addons as $addonGroup) {

            // first column: quantity
            $addons_html .= '<tr><td colspan="2" align="left"><h5>';
            $addons_html .= $addonGroup->name;
            $addons_html .= '</h5></td></tr>';

            foreach ($addonGroup->options as $addon) {
                $addons_html .= '<tr><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $addons_html .= $addon->name;
                $addons_html .= '</td><td align="right">';
                $addons_html .= number_format_i18n( floatval($addon->price), 2 );
                $addons_html .= '</td></tr>';
            }
            
        }
        $addons_html .= '</table>';
        return $addons_html;
	}


	function getVariationsHTML( $item ) {

        $profile_data = ListingsModel::decodeObject( $item['profile_data'], true );
        $variations   = ProductWrapper::getVariations( $item['post_id'] );

        $variations_html = '<div class="variations_list" style="margin:10px 0;">';
        $variations_html .= '<table style="margin-bottom: 8px;">';

        //table header
        if (true) {

            // first column: quantity
            $variations_html .= '<tr>';

            $first_variation = reset( $variations );
            if ( is_array( $first_variation['variation_attributes'] ) ) 
            foreach ($first_variation['variation_attributes'] as $name => $value) {
                $variations_html .= '<th>';
                $variations_html .= $name;
                $variations_html .= '</th>';
            }
            
            // last column: price
            $variations_html .= '<th align="right">';
            $variations_html .= __('Price','wplister');
            $variations_html .= '</th></tr>';

        }

        //table body
        foreach ($variations as $var) {

            // first column: quantity
            // $variations_html .= '<tr><td align="right">';
            // $variations_html .= intval( $var['stock'] ) . '&nbsp;x';
            // $variations_html .= '</td><td>';
            $variations_html .= '<tr>';

            foreach ($var['variation_attributes'] as $name => $value) {
                // $variations_html .= $name.': '.$value ;
	            $variations_html .= '<td>';
                $variations_html .= $value ;
                $variations_html .= '</td>';
            }
            // $variations_html .= '('.$var['sku'].') ';
            // $variations_html .= '('.$var['image'].') ';
            
            // last column: price
            $variations_html .= '<td align="right">';
            $price = ListingsModel::applyProfilePrice( $var['price'], $profile_data['details']['start_price'] );
            $variations_html .= number_format_i18n( floatval($price), 2 );

            $variations_html .= '</td></tr>';

        }

        $variations_html .= '</table>';
        $variations_html .= '</div>';

		// return html
		return $variations_html;
	}


	function getContent() {
		if ( ! $this->folderpath ) return;

		// load template.html
		$tpl_html = $this->getHTML();

		// load and embed style.css
		$tpl_css  = $this->getCSS();
		$tpl_html = '<style type="text/css">'.$tpl_css.'</style>'."\n\n".$tpl_html;

		// include header.php
		$tpl_header  = $this->getDynamicContent( $this->folderpath . '/header.php' );
		$tpl_html = $tpl_header."\n\n".$tpl_html;

		// include footer.php
		$tpl_footer  = $this->getDynamicContent( $this->folderpath . '/footer.php' );
		$tpl_html = $tpl_html."\n\n".$tpl_footer;

		return $tpl_html;

	}

	function getHTML( $folder = false) {
		if ( ! $folder ) $folder = $this->folderpath;
		$file = $folder . '/template.html';		
		return @file_get_contents( $file );
	}
	function getCSS( $folder = false ) {
		if ( ! $folder ) $folder = $this->folderpath;
		$file = $folder . '/style.css';		
		return @file_get_contents( $file );
	}
	function getHeader( $folder = false ) {
		if ( ! $folder ) $folder = $this->folderpath;
		$file = $folder . '/header.php';		
		return @file_get_contents( $file );
	}
	function getFooter( $folder = false ) {
		if ( ! $folder ) $folder = $this->folderpath;
		$file = $folder . '/footer.php';		
		return @file_get_contents( $file );
	}
	function getFunctions( $folder = false ) {
		if ( ! $folder ) $folder = $this->folderpath;
		$file = $folder . '/functions.php';		
		return @file_get_contents( $file );
	}

	public function getDynamicContent( $sFile, $inaData = array() ) {

		if ( !is_file( $sFile ) ) {

			// check if there is a problem with the uploads folder
			$upload_dir = wp_upload_dir();
			if ( $upload_dir['error'] ) {
				$this->showMessage( "There seems to be a problem with your uploads folder: ".$upload_dir['error'], 1, true );
			}

			$msg  = "The template file <code>".basename($sFile)."</code> could not found at: <code>".$sFile."</code>";
			$msg .= "<br><br>Please check your upload folder permissions or contact support.";
			$this->showMessage( $msg ,1,1);

			return false;
		}
		
		if ( count( $inaData ) > 0 ) {
			extract( $inaData, EXTR_PREFIX_ALL, 'wpl' );
		}
		
		ob_start();
			include( $sFile );
			$sContents = ob_get_contents();
		ob_end_clean();
		
		return $sContents;

	}

	function deleteTemplate( $id ) {
		$item = $this->getItem( $id );
		$fullpath = WP_CONTENT_DIR . $item['template_path'];

		// delete each template file
		$files = glob( $fullpath . '/*' );
		foreach ($files as $file) {
			unlink($file);
		}

		// delete folder
		rmdir($fullpath);

	}

	static function getCache() {
		
		$templates_cache = get_option( 'wplister_templates_cache' );

		if ( $templates_cache == '' ) 
			return array();

		return $templates_cache;
	}

	static function getNameFromCache( $id ) {
		
		$templates_cache = self::getCache();

		if ( isset( $templates_cache[ $id ] ) ) 
			return $templates_cache[ $id ]['template_name'];

		return $id;
	}

	function insertTemplate($id, $data) {
	}
	function updateTemplate($id, $data) {
	}
	function duplicateTemplate($id) {
	}


}

//
// Template API functions
// 

function wplister_register_custom_fields( $type, $id, $default, $label, $config = array() ) {
	global $wpl_tpl_fields;
	if ( ! $wpl_tpl_fields ) $wpl_tpl_fields = array();

	if ( ! $type || ! $id ) return;

	// create field
	$field = new stdClass();
	$field->id      = $id;
	$field->type    = $type;
	$field->label   = $label;
	$field->default = $default;
	$field->value   = $default;
	$field->slug    = isset($config['slug']) ? $config['slug'] : $id;
	$field->options = isset($config['options']) ? $config['options'] : array();

	// add to template fields
	$wpl_tpl_fields[$id] = $field;

}


