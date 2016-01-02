<?php
$output = $title = $cat = $cats = $post_in = $number = $view_more = $filter = $pagination = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'cats' => '',
    'cat' => '',
    'post_in' => '',
    'number' => 8,
    'view_more' => false,
    'filter' => false,
    'pagination' => false,
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

if ($post_in) {
    $args['post__in'] = explode(',', $post_in);
    $args['orderby'] = 'post__in';
}

if ($pagination && $paged = get_query_var('paged')) {
    $args['paged'] = $paged;
}

$posts = new WP_Query($args);

$faq_taxs = '';

if ($filter) {
    if (is_array($posts->posts) && !empty($posts->posts)) {
        foreach($posts->posts as $post) {
            $post_taxs = wp_get_post_terms($post->ID, 'faq_cat', array("fields" => "all"));
            if (is_array($post_taxs) && !empty($post_taxs)) {
                foreach ($post_taxs as $post_tax) {
                    if (is_array($cat) && !empty($cat) && in_array($post_tax->term_id, $cat)) {
                        $faq_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }

                    if(empty($cat) || !isset($cat)) {
                        $faq_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }
                }
            }
        }
    }

    if(is_array($faq_taxs)) {
        asort($faq_taxs);
    }
}

$shortcode_id = md5(json_encode($atts));

if ($posts->have_posts()) {
    $el_class = porto_shortcode_extract_class( $el_class );

    if ($animation_type)
        $el_class .= ' appear-animation';

    $output = '<div class="porto-faqs porto-faqs' . $shortcode_id . ' wpb_content_element ' . $el_class . '"';
    if ($animation_type)
        $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
    $output .= '>';

    $output .= porto_shortcode_widget_title( array( 'title' => $title, 'extraclass' => '' ) );

    ob_start(); ?>

    <div class="page-faqs clearfix <?php echo $title ? 'm-t-lg' : '' ?>">

        <?php if (is_array($faq_taxs) && !empty($faq_taxs)):
            ?>
            <ul class="faq-filter nav nav-pills sort-source">
                <li class="active" data-filter="*"><a><?php echo __('Show All', 'porto'); ?></a></li>
                <?php foreach ($faq_taxs as $faq_tax_slug => $faq_tax_name) : ?>
                    <li data-filter="<?php echo esc_attr($faq_tax_slug) ?>"><a><?php echo esc_html($faq_tax_name) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <hr>
        <?php endif; ?>

        <div class="faq-row">
            <?php
            while ($posts->have_posts()) {
                $posts->the_post();

                get_template_part('content', 'archive-faq');
            }
            ?>
        </div>

        <?php if ($pagination && function_exists('porto_pagination')) : ?>
            <input type="hidden" class="shortcode-id" value="<?php echo esc_attr($shortcode_id) ?>"/>
            <?php porto_pagination($posts->max_num_pages); ?>
        <?php endif; ?>

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