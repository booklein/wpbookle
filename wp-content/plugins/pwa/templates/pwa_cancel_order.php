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
	Sorry!! You cancelled your payment. If you have any issue please contact us.
</div>


<?php get_footer( 'shop' ); ?>
