<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $porto_settings, $product;

$wishlist = class_exists('YITH_WCWL') && $porto_settings['product-wishlist'];
$quickview = $porto_settings['product-quickview'];

$porto_woo_version = porto_get_woo_version_number();

?>
<div class="add-links-wrap">
    <div class="add-links <?php if (!$wishlist && !$quickview) echo 'no-effect' ?> clearfix">
        <?php
        global $porto_settings;

        $catalog_mode = false;
        if ($porto_settings['catalog-enable']) {
            if ($porto_settings['catalog-admin'] || (!$porto_settings['catalog-admin'] && !(current_user_can( 'administrator' ) && is_user_logged_in())) ) {
                if (!$porto_settings['catalog-cart']) {
                    $catalog_mode = true;
                }
            }
        }
        if (!$catalog_mode) {
            if ( version_compare($porto_woo_version, '2.5', '<') ) {
                echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                    sprintf( '<%s rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</%s>',
                        (isset($porto_settings['category-addlinks-convert']) && $porto_settings['category-addlinks-convert']) ? 'span' : 'a',
                        esc_url( $product->add_to_cart_url() ),
                        esc_attr( isset( $quantity ) ? $quantity : 1 ),
                        esc_attr( $product->id ),
                        esc_attr( $product->get_sku() ),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'add_to_cart_read_more',
                        esc_attr( $product->product_type ),
                        esc_html( $product->add_to_cart_text() ),
                        (isset($porto_settings['category-addlinks-convert']) && $porto_settings['category-addlinks-convert']) ? 'span' : 'a'
                    ),
                    $product );
            } else {
                echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                    sprintf( '<%s rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</%s>',
                        (isset($porto_settings['category-addlinks-convert']) && $porto_settings['category-addlinks-convert']) ? 'span' : 'a',
                        esc_url( $product->add_to_cart_url() ),
                        esc_attr( isset( $quantity ) ? $quantity : 1 ),
                        esc_attr( $product->id ),
                        esc_attr( $product->get_sku() ),
                        esc_attr( isset( $class ) ? $class : 'button' ),
                        esc_html( $product->add_to_cart_text() ),
                        (isset($porto_settings['category-addlinks-convert']) && $porto_settings['category-addlinks-convert']) ? 'span' : 'a'
                    ),
                    $product );
            }
        } else {
            $more_link = get_post_meta(get_the_id(), 'product_more_link', true);
            if (!$more_link) {
                $more_link = apply_filters( 'the_permalink', get_permalink() );
            }
            echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
                    esc_url( $more_link ),
                    esc_attr( $product->id ),
                    esc_attr( $product->get_sku() ),
                    esc_attr( isset( $quantity ) ? $quantity : 1 ),
                    'add_to_cart_read_more',
                    esc_attr( $product->product_type ),
                    esc_html( $porto_settings['catalog-readmore-label'] )
                ),
                $product );
        }

        if ($wishlist)
            echo do_shortcode('[yith_wcwl_add_to_wishlist]');
        if ($quickview) {
            $label = ((isset($porto_settings['product-quickview-label']) && $porto_settings['product-quickview-label']) ? $porto_settings['product-quickview-label'] : __('Quick View', 'porto'));
            echo '<div class="quickview" data-id="'.$product->id.'" title="' . $label . '">' . $label . '</div>';
        }
        ?>
    </div>
</div>