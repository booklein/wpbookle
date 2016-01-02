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
    'post_type' => 'member',
    'posts_per_page' => $number
);

if (!$cats)
    $cats = $cat;

if ($cats) {
    $cat = explode(',', $cats);
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'member_cat',
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

$member_taxs = '';

if ($filter) {
    if (is_array($posts->posts) && !empty($posts->posts)) {
        foreach($posts->posts as $post) {
            $post_taxs = wp_get_post_terms($post->ID, 'member_cat', array("fields" => "all"));
            if (is_array($post_taxs) && !empty($post_taxs)) {
                foreach ($post_taxs as $post_tax) {
                    if (is_array($cat) && !empty($cat) && in_array($post_tax->term_id, $cat)) {
                        $member_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }

                    if(empty($cat) || !isset($cat)) {
                        $member_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }
                }
            }
        }
    }

    if(is_array($member_taxs)) {
        asort($member_taxs);
    }
}

$shortcode_id = md5(json_encode($atts));

if ($posts->have_posts()) {
    $el_class = porto_shortcode_extract_class( $el_class );

    if ($animation_type)
        $el_class .= ' appear-animation';

    $output = '<div class="porto-members porto-members' . $shortcode_id . ' wpb_content_element ' . $el_class . '"';
    if ($animation_type)
        $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
    $output .= '>';

    $output .= porto_shortcode_widget_title( array( 'title' => $title, 'extraclass' => '' ) );

    ob_start(); ?>

    <div class="page-members clearfix <?php echo $title ? 'm-t-lg' : '' ?>">

        <?php if (is_array($member_taxs) && !empty($member_taxs)):
            ?>
            <ul class="member-filter nav nav-pills sort-source">
                <li class="active" data-filter="*"><a><?php echo __('Show All', 'porto'); ?></a></li>
                <?php foreach ($member_taxs as $member_tax_slug => $member_tax_name) : ?>
                    <li data-filter="<?php echo esc_attr($member_tax_slug) ?>"><a><?php echo esc_html($member_tax_name) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <hr>
        <?php endif; ?>

        <div class="member-row">
            <?php
            while ($posts->have_posts()) {
                $posts->the_post();

                get_template_part('content', 'archive-member');
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
            <a class="button btn-small" href="<?php echo get_post_type_archive_link( 'member' ) ?>"><?php _e("View More", 'porto-shortcodes') ?></a>
        </div>
    <?php endif; ?>

    <?php
    $output .= ob_get_clean();

    $output .= '</div>';

    echo $output;
}

wp_reset_postdata();