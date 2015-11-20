<?php
/**
 * The Template for displaying thankyou page after pay with amazon.
 *
 * @package 	Pwa/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

<?php 
	//global $pwa_order_id;
?>
<div class="thankyou">
	<?php if($pwa_order_status == 'REJECTED' || $pwa_order_status == 'DEPENDENCY_REJECT'){ ?>
		<h1>YOUR ORDER HAS BEEN RECEIVED, BUT IT SEEMS THAT YOUR PAYMENT HAS BEEN REJECTED.</h1>
		
		<h2>Thank you for your purchase!</h2>
		
		<h4>Your order id is : #<?php echo $order->id; ?>. Your Amazon Payments ID is: <?php echo $pwa_order_id; ?>.</h4>
		
		<h4>Note: In-case of payment failure, you will receive an email from "Pay with Amazon" asking you to revise the payment.</h4>
		
		<h4>Please note down your order id for future reference.</h4>
	<?php } ?>
	
	<?php if($pwa_order_status == 'APPROVED'){ ?>
		<h1>YOUR ORDER HAS BEEN RECEIVED.</h1>
		
		<h2>Thank you for your purchase!</h2>
		
		<h4>Your order id is : #<?php echo $order->id; ?>. Your Amazon Payments ID is: <?php echo $pwa_order_id; ?>.</h4>
		<h4>You will receive an order confirmation email with details of your order and a link to track its progress.</h4>
		<h4>Note: In-case of payment failure, you will receive an email from "Pay with Amazon" asking you to revise the payment.</h4>
	<?php } ?>
	
</div>




<?php get_footer( 'shop' ); ?>
