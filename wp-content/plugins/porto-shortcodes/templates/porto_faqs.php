<?php
$output = $title = $cat = $cats = $post_in = $number = $view_more = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'cats' => '',
    'cat' => '',
    'post_in' => '',
    'number' => 8,
    'view_more' => false,
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$args = array(
    'post_type' => 'faq',
    'posts_per_page' => $number
);

if (!$cats)
    $cats = $cat;

if ($cats) {
    $cat = explode(',', $cats);
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'faq_cat',
            'field' => 'term_id',
            'terms' => $cat,
        )
    );
}

if ($post_in)
    $args['post__in'] = explode(',', $post_in);

$posts = new WP_Query($args);

if ($posts->have_posts()) {
    $el_class = porto_shortcode_extract_class( $el_class );

    if ($animation_type)
        $el_class .= ' appear-animation';

    $output = '<div class="porto-faqs wpb_content_element ' . $el_class . '"';
    if ($animation_type)
        $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
    $output .= '>';

    $output .= porto_shortcode_widget_title( array( 'title' => $title, 'extraclass' => '' ) );

    ob_start(); ?>

    <div class="page-faqs clearfix">

        <div class="faq-row">
            <?php
            while ($posts->have_posts()) {
                $posts->the_post();

                get_template_part('content', 'archive-faq');
            }
            ?>
        </div>

    </div>

    <?php if ($view_more) : ?>
        <div class="push-top m-b-xxl text-center">
            <a class="button btn-small" href="<?php echo get_post_type_archive_link( 'faq' ) ?>"><?php _e("View More", 'porto-shortcodes') ?></a>
        </div>
    <?php endif; ?>

    <?php
    $output .= ob_get_clean();

    $output .= '</div>';

    echo $output;
}

wp_reset_postdata();