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
	Sorry!! it seems that you are URL is wrong. 
	<br />
	<h3>Details : </h3>
	<p>
		1) You are not properly redirected after making successfull payment.
	</p>
	<p>
		2) Your URL doesn't contain amazon order information.
	</p>
	<br />
	Please try again.
</div>


<?php get_footer( 'shop' ); ?>
