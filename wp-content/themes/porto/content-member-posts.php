<?php
global $porto_settings, $porto_layout;

$post_ids = get_post_meta(get_the_ID(), 'member_posts', true);
$posts = porto_get_posts_by_ids($post_ids);

if ($posts->have_posts()) : ?>
    <div class="post-gap"></div>

    <div class="related-posts <?php echo $porto_settings['post-related-style'] ?>">
        <h4 class="sub-title"><?php echo __('My <strong>Posts</strong>', 'porto'); ?></h4>
        <div class="row">
            <div class="post-carousel owl-carousel" data-cols-lg="<?php echo ($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') ? '3' : '4' ?>" data-cols-md="3" data-cols-sm="2">
                <?php
                while ($posts->have_posts()) {
                    $posts->the_post();

                    get_template_part('content', 'post-item');
                }
                ?>
            </div>
        </div>
    </div>
<?php endif;
wp_reset_postdata();
?>