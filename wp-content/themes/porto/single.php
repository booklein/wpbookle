<?php get_header() ?>

<?php
global $porto_settings, $porto_layout;

wp_reset_postdata();
?>

<div id="content" role="main">

    <?php if (have_posts()) : the_post();
        $post_layout = get_post_meta($post->ID, 'post_layout', true);
        $post_layout = ($post_layout == 'default' || !$post_layout) ? $porto_settings['post-content-layout'] : $post_layout;
        ?>

        <?php get_template_part('content', 'post-' . $post_layout); ?>

        <hr class="tall"/>

        <?php
        if ($porto_settings['post-related']) :
            $related_posts = porto_get_related_posts($post->ID);
            if ($related_posts->have_posts()) : ?>
                <div class="related-posts">
                    <h4 class="sub-title"><?php echo __('Related <strong>Posts</strong>', 'porto'); ?></h4>
                    <div class="row">
                        <?php if (isset($porto_settings['post-related-style']) && 'style-4' == $porto_settings['post-related-style']) : ?>
                        <div class="post-carousel owl-carousel" data-cols-lg="<?php echo ($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') ? '1' : '2' ?>" data-cols-md="1" data-cols-sm="1">
                        <?php else : ?>
                        <div class="post-carousel owl-carousel" data-cols-lg="<?php echo ($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') ? '3' : '4' ?>" data-cols-md="3" data-cols-sm="2">
                        <?php endif; ?>
                        <?php
                        while ($related_posts->have_posts()) {
                            $related_posts->the_post();

                            get_template_part('content', 'post-item');
                        }
                        ?>
                        </div>
                    </div>
                </div>
            <?php endif;
        endif;
    endif; ?>

</div>

<?php get_footer() ?>