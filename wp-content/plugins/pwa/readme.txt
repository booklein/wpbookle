=== Pay with Amazon ===
Contributer : ##Wordpress Account user name 
Tags : Amazon, Seller central, Pay with Amazon, Checkout by Amazon, Payment, Orders, IOPN, MWS API
Wordpress Version : Requires at least 4.0
Woocommerce Version: 2.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Pay with Amazon is a plugin to Checkout by Amazon and also manage orders.

=== Description ===
By using PWA plugin, Seller can provide an option to his customers to make payment using Checkout by Amazon. Where Seller do not need to worry about Checkout and Payments. 
Amazon will take care of everything.


=== Installation ===

= Minimum Requirements =
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater


= Dependencies =
This plugin will only work with woocommerce. 
You will not be able to use this plugin with any other e-commerce plugin or your customized e-commerce plugin.


= Automatic installation =
Automatic installation is the best option as WordPress handles the file transfers itself. To do an automatic install of PWA, log in to your WordPress Admin Panel, go to the Plugins menu and click Add New.
In the search field type “pwa” and click Search Plugins. Once you’ve found pwa plugin you can view details about it . You can install it by simply clicking “Install Now”.


= Manual installation =
1) Download the pwa.zip file.
2) Extract it and copy in wp-content/plugins folder.
3) Go to Wordpress Admin Panel -> Plugins 
4) You will see this plugin listed here with Pay with Amazon name. Activate the plugin by clicking on activate. 


= Enable Plugin & Plugin Settings =
After successful activation you need to enable plugin and need to do some settings.
1) Go to Woocommerce->settings->Checkout->Pay with Amazon
2) Enable Pay with Amazon
3) Insert correct Merchant ID , Access Key , Secret Key which you get from Amazon Seller Central. (Please mind that these keys should be correct or plugin will fail to function properly)
4) Do some other settings as per your requirement (Information on other points are also listed in this file)


=== Settings in Seller Central ===
To checkout by Amazon you need to do following settings in Seller Central
1) Go to Seller Central https://sellercentral.amazon.in
2) Login With your credentials
3) Go to Settings -> Integration Settings -> Edit
4) Add Successful Payment Return URL under Checkout Pipeline Settings. (You can find this URL on PWA plugin settings page.)


=== Setup IOPNs Properly ===
To make IOPN works properly you need to follow these steps.
1) Set "Merchant URL" or "Integrator URL" under Instant Order Processing Notification Settings in Integration Settings in Seller Central. 

Note : - 
1) You need to provide a valid SSL URL, Because IOPN will work only with Secure URLs. You can find this URL on PWA plugin settings page named 
as "IOPN Merchant Url". 
2) Plugin will only accept signed carts IOPN.
3) If you are setting both "Merchant URL" and "Integrator URL", make sure both URL should be different, otherwise duplicate orders may be generate.

Check is IOPN URL working properly : -
1) Please hit your "IOPN Merchant Url" in browser and if browser didn't give you any 404 error and show blank page it means it's working.
2) If browser gives you 404 error then please check your permalinks and .htaccess or try this URL using index.php before "pwa_iopn" in url.
ex: https://www.example.com/index.php/pwa_iopn


=== Setup MWS Properly ===
1) Schedule MWS Report by hitting the "MWS Schedule Report API Url" manually (Only hit once). You can find this URL on plugin settings page. 
2) Setup cron jobs to fetch generated report automatically and reflect orders in woocommerce admin panel. You need to setup cron on "MWS Report API Url" and "MWS Order API Url".
You can find respective cron URLs on plugin's settings page.
Note: - To setup cron please concern with your developer or your host provider.

Check is MWS URL working properly : -
1) Please hit your "MWS Report API Url" and "MWS Order API Url" in browser and if browser didn't give you any 404 error and show blank page it means it's working.
2) If browser gives you 404 error then please check your permalinks and .htaccess or try this URL using index.php before "pwa_mws_report" and "pwa_mws" in url.
ex: https://www.example.com/index.php/pwa_mws_report


=== PWA  Mails ===
1) IOPN 
1.a) Admin will get a New Order Email when New Order Notification received.
1.b) Admin will get the same Email with same Mail Content when Order Ready to Ship Notification received.
1.c) Customer will get Order Processing Email when Order Ready to Ship Notification received.
1.d) Admin and Customer will not be notified if order is cancelled from processing state.
1.e) Admin will be notified for cancelled order. (if previously order were in pending or on-hold state )


2) MWS 
2.a) Admin will get a New Order Email when order details will get reflected.
2.b) Customer will get Processing Order Email when order details will get reflected.
2.c) Admin and Customer will not be notified if order is cancelled from processing state.


=== Things to Note === 
1) It is highly recommended that please request to Amazon to generate Report for your orders whether you are going to use MWS or not.
   You can request this by manually hitting an URL. You can find this URL on plugin settings page named as "MWS Schedule Report API Url:"
   Note : Please only hit once, no need to hit again and again to generate reports.
2) Plugin will accept only Signed Carts IOPN.
3) About MWS : Few things you need to know if you want to use MWS now or in future.
	3.1) When you hit "MWS Schedule Report API Url" it will start generating report from the current timestamp and will generate after every 15 Minutes. So if an order got into Unshipped state on Seller Central
		 it will take max 15 Minute to reflect in next report.
	3.2) When you hit "MWS Schedule Report API Url" it will start generating report from the current timestamp. Means orders before this timestamp will not reflect in Reports.
		 So if your cron will run it will not reflect the orders that are not in reports.
	3.3) If there is more than 3 days difference in between your report generation time and first cron run time then cron will fetch only last three days reports and will reflect the orders
		 that come only under these reports.
		 You can read more about MWS here : http://docs.developer.amazonservices.com/en_IN/dev_guide/index.html



=== Common Issues/ FAQ ===
Que 1) How i can display Pay with Amazon button on any page?
Ans 1) To display Pay with Amazon button on any page use shortcode [amazon_checkout_button]. 

Que 2) Why default Pay with Amazon image not displaying more than one time on a page?
Ans 2) Yes, default Pay with Amazon image will not work more than one time on a page.

Que 3) May i change default Pay with amazon button image?
Ans 3) Yes you can set your own pay with amazon image. To do so browse an image for "Pay With Amazon button Image" option and save.

Que 4) Will Custom Pay with Amazon button display more than one times on a page?
Ans 4) Yes If you add your own Pay with Amazon button image it will display more than one time on a single page.

Que 5) Why my Woocommerce cart total amount and total amount on Amazon is different?
Ans 5) Actually plugin will add tax and promotions on items and them send to amazon. So sometimes if quantity is more than one then due to rounding issue there may be 1 paisa difference.

Que 6) Why my Woocommerce shipping price are not applying/displaying on Amazon Checkout page?
Ans 6) Actually Amazon does not support shipping prices so to apply shipping charges you need to set these on seller central.


Que 7) When my inventory will be reduced if i am using IOPN?
Ans 7) When you will get an New Order Notification it will update order details and will reduce your inventory.

Que 8) When my inventory will be reduced if i am using MWS.
Ans 8) As in MWS, order details will not be reflected before order goes into Unshipped condition so at the order details updation time inventory will reduce and order status will also change 
       to Processing from pending.

Que 9) Why my inventory is not updating if user/admin cancel an order?
Ans 9) As Woocommerce does not support the same so you need to manually update your inventory. 
	   https://support.woothemes.com/hc/en-us/articles/202723293-How-to-Automatically-Re-Stock-Items-in-Cancelled-Refunded-Orders


Que 10) Is PWA plugin is generating exception error logs in any file?
Ans 10) Yes PWA plugin will generate and maintain all your exception logs in error log file. 
	   You can find error log file in your plugin folder.
	   File Name : pwa_error.log


Que 11) Can i check or dump the IOPN XML Notification Data?
Ans 11) Yes you can dump the IOPN XML Notification Data, To do the same you need to enable it on plugin setting page.
		Check "Enable IOPN for debugging purpose"  and set path under  "Set Path for IOPNs dump file" option.
		Default is : wp-content/uploads/pwa_iopn_dump/ but you can change it. But if you change the destination make sure you have given 777 permission to that folder.


Que 12) Can i check or dump the MWS XML Response?
Ans 12) Yes you can dump theMWS XML Response, To do the same you need to enable it on plugin setting page.
		Check "Generate MWS Report Dump file"  and set path under "Set Path for MWS report dump file" option.
		Default is : wp-content/uploads/pwa_report_dump/ but you can change it. But if you change the destination make sure you have given 777 permission to that folder.
		
		Note :- Do the same for MWS Order API.
		
Que 13) IOPNs are coming but order details is not reflecting in woocommerce admin panel.
Ans 13) If your IOPNs are coming but order details are not reflecting in Woocommerce Admin Panel then there might be some issue with Signed carts.
		As plugin will accept only signed carts. So before reflecting order details in Woocommerce Admin Panel we verify it.
		Check 5.1.2 point under this IOPN Documentation file
		http://amazonpayments.s3.amazonaws.com/documents/Instant_Order_Processing_Notification_API_Guide.pdf
		
Que 14) Pay with Amazon button is not showing on Cart page.
Ans 14) If cart page doesn't show Pay with Amazon button then place following code in your cart template file.
		​<?php echo do_shortcode('[amazon_checkout_button]'); ?>

		Generally this file location is theme dependent but more over less you can find it here.
		[your-theme]->woocommerce->cart->cart.php
		
		Wherever you want to show Pay with Amazon button place this php code over there.







