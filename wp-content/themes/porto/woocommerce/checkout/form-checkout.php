<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$porto_woo_version = porto_get_woo_version_number();

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = version_compare($porto_woo_version, '2.5', '<') ? apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ) : wc_get_checkout_url(); ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="row" id="customer_details">
			<div class="col-sm-6">
                <div class="featured-box align-left">
                    <div class="box-content">
				        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    </div>
                </div>
			</div>

			<div class="col-sm-6">
                <div class="featured-box align-left">
                    <div class="box-content">
				        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                    </div>
                </div>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

    <div class="checkout-order-review featured-box align-left">
        <div class="box-content">
            <h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

            <?php if ( version_compare($porto_woo_version, '2.3', '<') ) : ?>
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            <?php else : ?>
                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>

                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
            <?php endif; ?>
        </div>
    </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>