<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
    return;
}

$porto_woo_version = porto_get_woo_version_number();

if ( version_compare($porto_woo_version, '2.3', '<') ) {
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_rating_count();
} else {
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();
}
$average = $product->get_average_rating();

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<div class="star-rating" title="<?php echo $average ?>">
			<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( __( 'out of %s5%s', 'woocommerce' ), '', '' ); ?>
			</span>
		</div>
        <meta content="<?php echo $rating_count; ?>" itemprop="ratingCount" />
        <meta content="5" itemprop="bestRating" />
        <?php if ( comments_open() ) : ?>
		    <div class="review-link"><a href="<?php if (porto_is_ajax()) the_permalink() ?>#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?></a>|<a href="<?php if (porto_is_ajax()) the_permalink() ?>#review_form" class="woocommerce-write-review-link" rel="nofollow"><?php echo __( 'Add a review', 'woocommerce' ); ?></a></div>
        <?php endif; ?>
	</div>

<?php else : ?>

    <?php if ( comments_open() ) : ?>
        <div class="woocommerce-product-rating noreview">
            <a href="<?php if (porto_is_ajax()) the_permalink() ?>#review_form" class="woocommerce-write-review-link" rel="nofollow"><?php echo __( 'Be the first to review', 'woocommerce' ); ?></a>
        </div>
    <?php endif; ?>

<?php endif; ?>