<?php
$output = $title = $portfolio_layout = $columns = $view = $cat = $cats = $post_in = $number = $view_more = $filter = $pagination = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'portfolio_layout' => 'timeline',
    'columns' => '3',
    'view' => 'classic',
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
    'post_type' => 'portfolio',
    'posts_per_page' => $number
);

if (!$cats)
    $cats = $cat;

if ($cats) {
    $cat = explode(',', $cats);
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'portfolio_cat',
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

$portfolio_taxs = '';

if ($filter) {
    if (is_array($posts->posts) && !empty($posts->posts)) {
        foreach($posts->posts as $post) {
            $post_taxs = wp_get_post_terms($post->ID, 'portfolio_cat', array("fields" => "all"));
            if (is_array($post_taxs) && !empty($post_taxs)) {
                foreach ($post_taxs as $post_tax) {
                    if (is_array($cat) && !empty($cat) && in_array($post_tax->term_id, $cat)) {
                        $portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }

                    if(empty($cat) || !isset($cat)) {
                        $portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
                    }
                }
            }
        }
    }

    if(is_array($portfolio_taxs)) {
        asort($portfolio_taxs);
    }
}

$shortcode_id = md5(json_encode($atts));

if ($posts->have_posts()) {
    $el_class = porto_shortcode_extract_class( $el_class );

    if ($animation_type)
        $el_class .= ' appear-animation';

    $output = '<div class="porto-portfolios porto-portfolios' . $shortcode_id . ' wpb_content_element ' . $el_class . '"';
    if ($animation_type)
        $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
    $output .= '>';

    $output .= porto_shortcode_widget_title( array( 'title' => $title, 'extraclass' => '' ) );

    global $porto_portfolio_columns, $porto_portfolio_view;

    $porto_portfolio_columns = $columns;
    $porto_portfolio_view = $view;
    $portfolio_columns = $columns;
    $portfolio_view = $view;

    ob_start(); ?>

    <div class="page-portfolios portfolios-<?php echo $portfolio_layout ?> clearfix <?php echo $title ? 'm-t-lg' : '' ?>">

    <?php if (is_array($portfolio_taxs) && !empty($portfolio_taxs)):
    ?>
    <ul class="portfolio-filter nav nav-pills sort-source">
        <li class="active" data-filter="*"><a><?php echo __('Show All', 'porto'); ?></a></li>
        <?php foreach ($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name) : ?>
            <li data-filter="<?php echo esc_attr($portfolio_tax_slug) ?>"><a><?php echo esc_html($portfolio_tax_name) ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php if ($portfolio_layout == 'grid') { ?>
        <hr>
    <?php } else if ($portfolio_layout == 'timeline') { ?>
        <hr class="invisible">
    <?php } else { ?>
        <hr class="tall">
    <?php } ?>
    <?php endif; ?>

    <?php if ($portfolio_layout == 'timeline') :
        global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

        $prev_post_year = null;
        $prev_post_month = null;
        $first_timeline_loop = false;
        $post_count = 1;
        ?>

        <section class="timeline">

            <div class="timeline-body">

    <?php else : ?>

        <div class="clearfix <?php if ($portfolio_layout == 'grid') : ?> portfolio-row portfolio-row-<?php echo $portfolio_columns ?> <?php echo $portfolio_view ?><?php endif; ?>">

    <?php endif; ?>

    <?php
    while ($posts->have_posts()) {
        $posts->the_post();

        get_template_part('content', 'archive-portfolio-'.$portfolio_layout);
    }
    ?>

    <?php if ($portfolio_layout == 'timeline') : ?>

            </div>

        </section>

    <?php else : ?>

        </div>

    <?php endif; ?>

    <?php if ($pagination && function_exists('porto_pagination')) : ?>
        <input type="hidden" class="shortcode-id" value="<?php echo esc_attr($shortcode_id) ?>"/>
        <?php porto_pagination($posts->max_num_pages); ?>
    <?php endif; ?>

    </div>

    <?php if ($view_more) : ?>
        <div class="<?php if ($portfolio_layout == 'timeline') echo 'm-t-n-xxl'; else echo 'push-top'; ?> m-b-xxl text-center">
            <a class="button btn-small" href="<?php echo get_post_type_archive_link( 'portfolio' ) ?>"><?php _e("View More", 'porto-shortcodes') ?></a>
        </div>
    <?php endif; ?>

    <?php
    $output .= ob_get_clean();

    $porto_portfolio_columns = '';
    $porto_portfolio_view = '';

    $output .= '</div>';

    echo $output;
}

wp_reset_postdata();