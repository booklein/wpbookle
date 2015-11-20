<?php    

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
class Pwacheckout extends WC_Payment_Gateway
{
    protected static $instance = NULL;
  
    public function __construct()
    {
		$this->id              = 'Pwacheckout';
		$this->method_title    = __( 'Pay with Amazon', 'pay_amazon' );
        $this->init_form_fields();
        $this->init_settings();
        $this->title           = 'Pay with Amazon'.'<img style="max-width:80px" src="'.IMAGE_FOLDER_HTTPURL.'pwa_large.png" />';
        $siteurls              =  get_option('siteurl');
        if(! preg_match ('/http/',$siteurls))
        {
            $siteurls ="https://".$siteurls;
        }
        else
        {
			$site_url_arr = explode(':',$siteurls);
			$siteurl_iopn ="https:".$site_url_arr[1];
		}
		
        if (substr($siteurls, -1) != '/') {
            $siteurls =$siteurls."/";
            
        }
        
         if (substr($siteurl_iopn, -1) != '/') {
            $siteurl_iopn =$siteurl_iopn."/";
            
        }
        $this->method_description = "<input type=\"hidden\" id=\"pwa_urls\" value=".$siteurls."><input type=\"hidden\" id=\"pwa_iopn_urls\" value=".$siteurl_iopn.">";
                                    

        $this->description   = $this->settings['description_text'];
        $this->success_payment_return_url =$siteurls;
        // tell WooCommerce to save options
        add_action('woocommerce_update_options_payment_gateways_' . $this->id , array($this, 'process_admin_options'));
    }

    /*
     * Creates a new instance if there isn't one.
     *
     * @wp-hook init
     * @return object
     */
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    public function process_admin_options()
    {   
        $target_dir = IMAGE_FOLDER_URL;
        $target_file = $target_dir . basename($_FILES["woocommerce_Pwacheckout_pwa_btn_img"]["name"]);
        if( $_FILES["woocommerce_Pwacheckout_pwa_btn_img"]["name"] )
        {
           try {
                move_uploaded_file($_FILES["woocommerce_Pwacheckout_pwa_btn_img"]["tmp_name"], $target_file); 
                chmod($target_file , 0777);
            }
            catch(Exception $e) {
                echo 'error in uploading file';
            }  
        }
        $value = isset($_POST['woocommerce_Pwacheckout_pwa_delete_img']) ? $_POST['woocommerce_Pwacheckout_pwa_delete_img'] : 0 ;
        if(!$value && $_FILES['woocommerce_Pwacheckout_pwa_btn_img']['name'])
            $_POST['woocommerce_Pwacheckout_pwa_btn_img_hidden'] = IMAGE_FOLDER_HTTPURL.$_FILES['woocommerce_Pwacheckout_pwa_btn_img']['name'] ;
        elseif($value)
            $_POST['woocommerce_Pwacheckout_pwa_btn_img_hidden'] = '';

        $_POST['woocommerce_Pwacheckout_pwa_delete_img'] = 0;
       parent::process_admin_options();
        
        
    }

    public function init_form_fields()
    {
		if( empty($this->settings) ){
			$this->enabled     = 'no';
		}else{
			$this->enabled     = $this->settings['enabled'];
		}
		
        $img_hidden_path =   $this->get_option('pwa_btn_img_hidden');
        $preview = '';
        if($img_hidden_path)
            $preview = '<img src="'.$img_hidden_path.'" width="150" height="50">';

        $this->form_fields = array(
            'enabled' => array(
                'type'        => 'checkbox',
                'title'       => __('Enable/Disable', 'pay_amazon'),
                'label'       => __('Enable Pay With Amazon', 'pay_amazon'),
                'default'     => 'no'
            ),
             'pwa_order_update' =>array(
                'id'        =>  'pwa_order_update',
                'type'      =>  'hidden',
                'title'     => __('Amazon settings','pay_amazon'),
				'default'     => 'xmlcart',
                'options' => array(
                        'xmlcart' => 'By XMl Cart',
                        'woocart' => 'By WOO Cart',
                    ),
            ),
             'description_text' => array(
                'type'        => 'text',
                'title'       => __('Description', 'pay_amazon'),
                'description'       => __('Description on the payment method', 'pay_amazon'),
                'default'     => 'Pay with Amazon',
            ),
            'merchant_id' => array(
                'type'        => 'text',
                'title'       => __('Merchant ID<span class ="error">*</span>:', 'pay_amazon'),
                'description'       => __('Merchant Id given by amazon payments.<br /><span id="merchant_id_error" class ="error"></span>', 'pay_amazon'),
                'default'     => ''
            ),
           
            'access_key' => array(
                'type'        => 'text',
                'title'       => __('Access Key<span class ="error">*</span>:', 'pay_amazon'),
                'description'       => __('An application identifier associates your site, and Amazon application.<br /><span id="access_key_error" class ="error"></span>', 'pay_amazon'),
                'default'     => ''
                 
            ),
            'secret_key' => array(
                'type'        => 'text',
                'title'       => __('Secret Key<span class ="error">*</span>:', 'pay_amazon'),
                'description'       => __('The secret code from Amazon.<br /><span id="secret_key_error" class ="error"></span>', 'pay_amazon'),
                'default'     => ''
            ),
             'marketplace_id' => array(
                'type'        => 'select',
                'title'       => __('Marketplace ID:', 'pay_amazon'),
                'description'       => __('The Marketplace ID from Amazon.', 'pay_amazon'),
                'default'     => 'A3PY9OQTG31F3H',
                'options'     => array(
                    'A3PY9OQTG31F3H'    => __( 'Production / A3PY9OQTG31F3H', 'pay_amazon' ),
                    'AXGTNDD750VEM' => __( 'Sandbox / AXGTNDD750VEM', 'pay_amazon' )
                )
            ),
             'environment' => array(
                'title'       => __( 'Environment', 'pay_amazon' ),
                'type'        => 'select',      
                'description' => __( 'In Production environment order will be placed at seller central in Production view <br>
                                     In Sandbox environment order will be placed at seller central in Sandbox view.', 'pay_amazon' ),
                'default'     => 'prod',
                'options'     => array(
                    'prod'    => __( 'Production', 'pay_amazon' ),
                    'sandbox' => __( 'Sandbox', 'pay_amazon' )
                )
            ),
             'show_pwa_button' => array(
                'title'       => __( 'Show Pay With Amazon Button when', 'pay_amazon' ),
                'type'        => 'select',      
                'description' => __( '', 'pay_amazon' ),
                'default'     => '',
                'options'     => array(
                    'yes'    => __( 'user logged in', 'pay_amazon' ),
                    'no' => __( 'user not logged in', 'pay_amazon' )
                )
            ),
             'order_update_api' =>array(
                'type'      =>  'select',
                'title'     => __('Use IOPN or MWS Report API:','pay_amazon'),
                'description' => __( 'IOPN will be preferred over MWS. But IOPN will only work if SSL is enabled on server. <br>
                                     MWS is cron based so you need to setup cron. So It will only update the details when cron will run.', 'pay_amazon' ),
                'default'     => 'IOPN',
                'options' => array(
                        'IOPN' => 'IOPN',
                        'MWS' => 'MWS',
                    ),
            ),
             'success_payment_return_url' => array(
                'type'        => 'text',
                'title'       => __('Successful Payment Return Url:', 'pay_amazon'),
                'description'       => __('Use this url in amazon seller central settings.', 'pay_amazon'),
                'default'     => ''
            ),
            'iopn_dump' => array(
                'type'        => 'checkbox',
                'title'       => __('Enable IOPN for debugging purpose:', 'pay_amazon'),
                'label'       => __('check if you want to generate IOPN dump file.', 'pay_amazon'),
                'description' => __( 'Will be in effect only when IOPN is enabled.', 'pay_amazon' ),
                'default'     => 'no'
            ),
            'iopn_dump_url' => array(
                'type'        => 'text',
                'placeholder' =>'wp-content/uploads/pwa_iopn_dump/',
                'title'       => __('Set Path for IOPN dump file:', 'pay_amazon'),
                'description'       => __('Type the path of folder for IOPN dump file.', 'pay_amazon'),
                'default'     => 'wp-content/uploads/pwa_iopn_dump/'
            ),
             'iopn_merchant_url' => array(
                'type'        => 'text',
                'title'       => __('IOPN Merchant Url:', 'pay_amazon'),
                'description'       => __('', 'pay_amazon'),
                'default'     => ''
            ),

            'mws_order_dump' => array(
                'type'        => 'checkbox',
                'title'       => __('Generate MWS Order Dump file:', 'pay_amazon'),
                'label'       => __('check if you want to generate MWS Order dump file.', 'pay_amazon'),
                'default'     => 'no'
            ),
            'mws_order_dump_url' => array(
                'type'        => 'text',
                'placeholder' =>'wp-content/uploads/pwa_order_dump/',
                'title'       => __('Set Path for MWS Order dump file:', 'pay_amazon'),
                'description'       => __('Type the path of folder for MWS Order dump file.', 'pay_amazon'),
                'default'     => 'wp-content/uploads/pwa_order_dump/'
            ),
             'mws_order_api_url' => array(
                'type'        => 'text',
                'title'       => __('MWS Order API Url:', 'pay_amazon'),
                'description'       => __('', 'pay_amazon'),
                'default'     => ''
            ),
            'mws_report_dump' => array(
                'type'        => 'checkbox',
                'title'       => __('Generate MWS Report Dump file:', 'pay_amazon'),
                'label'       => __('check if you want to generate MWS Report dump file.', 'pay_amazon'),
                'default'     => 'no'
            ),
            'mws_report_dump_url' => array(
                'type'        => 'text',
                'placeholder' =>'wp-content/uploads/pwa_report_dump/',
                'title'       => __('Set Path for MWS report dump file:', 'pay_amazon'),
                'description'       => __('Type the path of folder for MWS report dump file.', 'pay_amazon'),
                'default'     => 'wp-content/uploads/pwa_report_dump/'
            ),
             'mws_schedule_report_api_url' => array(
                'type'        => 'text',
                'title'       => __('MWS Schedule Report API Url:', 'pay_amazon'),
                'description'       => __('', 'pay_amazon'),
                'default'     => ''
            ),
             'mws_report_api_url' => array(
                'type'        => 'text',
                'title'       => __('MWS Report API Url:', 'pay_amazon'),
                'description'       => __('', 'pay_amazon'),
                'default'     => ''
            ),
            'pwa_btn_color' =>array(
                'id'        =>  'pwa_btn_color',
                'type'      =>  'select',
                'title'     => __('Choose a colour for the button:','pay_amazon'),
                'default'     => 'orange',
                'options' => array(
                        'orange' => 'orange',
                        'tan' => 'tan',
                    ),
            ),
            'pwa_btn_bkgd' =>array(
                'id'        =>  'pwa_btn_color',
                'type'      =>  'select',
                'title'     => __('Choose a background colour for the button:','pay_amazon'),      
                'default'     => 'white',
                   'options' => array(
                        'white' => 'white',
                        'other' => 'other',
                    ),
            ),
            'pwa_btn_size' =>array(
                'id'        =>  'pwa_btn_size',
                'type'      =>  'select',
                'title'     => __('Choose a size for the button:','pay_amazon'),
                'default'   => 'medium',
                'options' => array(
                        'medium' => __( 'medium', 'pay_amazon' ),
                        'large' =>  __('large', 'pay_amazon'),
                    ),
            ),
            'pwa_btn_view' => array(
                'title'     => __('Pay With Amazon button view on front end:','pay_amazon'),
                'type'  =>     'hidden',
                'description'   =>'<div id="div_pwa_btn_admin" style="position: relative;background: white;padding: 15px;width: 145px;border: 2px solid;"> <img id="view_pwa_btn_admin" src="https://paywithamazon.amazon.in/gp/cba/button?color=orange&amp;size=medium&amp;background=white"> </div>',
                'section'       => 'info',
            ),
             'pwa_btn_img' => array(
                'title'     => __('Add custom PWA button image:','pay_amazon'),            
                'type'  =>     'file',
                'section'       => 'info',
                'description'  => $preview. '<span id="pwa_btn_img_error" class ="error"></span>',  

            ),
            'pwa_delete_img' => array(
                'type'        => 'checkbox',
                'label'       => __('Delete custom image to show default Pay with Amazon button', 'pay_amazon'),
                'default'     => 'no',
            ),
            'pwa_btn_img_hidden' => array(
                'type'  =>     'hidden',
            ),
       );
    } 
	
}

function add_pwa_gateway($methods)
{
	//array_push($methods, 'Pwacheckout');
	array_unshift($methods, 'Pwacheckout');
	return $methods;
}

add_filter('woocommerce_payment_gateways','add_pwa_gateway');

    
