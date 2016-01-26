<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $post, $product, $porto_settings;

$labels = '';
if ($porto_settings['product-hot']) {
    $featured = get_post_meta($post->ID, '_featured', 'true') == 'yes' ? true : false;
    if ($featured) {
        $hot_html = '<div class="onhot">'. ((isset($porto_settings['product-hot-label']) && $porto_settings['product-hot-label']) ? $porto_settings['product-hot-label'] : __('Hot', 'porto')) .'</div>';
        $labels .= $hot_html;
    }
}
if ($porto_settings['product-sale']) {
    if ($product->is_on_sale()) {
        $percentage = 0;
        if ($product->regular_price)
            $percentage = - round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
        if ($porto_settings['product-sale-percent'] && $percentage)
            $sales_html = '<div class="onsale">'. $percentage .'%</div>';
        else
            $sales_html = apply_filters('woocommerce_sale_flash', '<div class="onsale">'. ((isset($porto_settings['product-sale-label']) && $porto_settings['product-sale-label']) ? $porto_settings['product-sale-label'] : __('Sale', 'porto')) .'</div>', $post, $product);
        $labels .= $sales_html;
    }
}
echo '<div class="labels">';

echo  $labels;

$availability = $product->get_availability();
if ( $availability['availability'] == __( 'Out of stock', 'woocommerce' )) {
    if ($porto_settings['product-stock']) {
        echo apply_filters( 'woocommerce_stock_html', '<div class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</div>', $availability['availability'] );
    }
} else {
    echo '<div data-link="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" class="viewcart ' . str_replace('minicart-icon', 'viewcart', $porto_settings['minicart-icon']) .' viewcart-'.$product->id.'" title="' . __('View Cart', 'woocommerce') . '"></div>';
}

echo '</div>';
