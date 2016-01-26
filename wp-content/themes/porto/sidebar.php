<?php
global $porto_settings, $porto_layout, $porto_sidebar;
wp_reset_postdata();
$content_bottom = porto_get_meta_value('content_bottom');
$content_inner_bottom = porto_get_meta_value('content_inner_bottom');
$wrapper = porto_get_wrapper_type();
?>

<?php
do_action('porto_before_content_inner_bottom');
if ($content_inner_bottom) : ?>
    <div id="content-inner-bottom"><!-- begin content inner bottom -->
        <?php echo do_shortcode('[porto_block name="'.$content_inner_bottom.'"]') ?>
    </div><!-- begin content inner bottom -->
<?php endif;
do_action('porto_after_content_inner_bottom');
?>

</div><!-- end main content -->

<?php
$is_category_filter = class_exists('WooCommerce') && $porto_settings['category-mobile-filter'] && (is_shop() || porto_is_product_archive());
if (($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar')) : ?>
    <div class="col-md-3 sidebar <?php echo str_replace('wide-', '', $porto_layout) ?><?php echo $is_category_filter ? ' mobile-hide-sidebar' : '' ?>"><!-- main sidebar -->
        <?php
        do_action('porto_before_sidebar');
        // show sidebar menu
        $sidebar_menu = porto_sidebar_menu();
        if ($sidebar_menu) : ?>
            <div id="main-sidebar-menu" class="widget_sidebar_menu">
                <?php if ($porto_settings['menu-sidebar-title']) : ?>
                    <div class="widget-title">
                        <?php echo force_balance_tags($porto_settings['menu-sidebar-title']) ?>
                        <?php if ($porto_settings['menu-sidebar-toggle']) : ?>
                            <div class="toggle"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="sidebar-menu-wrap">
                    <?php echo $sidebar_menu ?>
                </div>
            </div>
        <?php endif; ?>
        <?php dynamic_sidebar( $porto_sidebar );
        do_action('porto_after_sidebar');
        ?>
    </div><!-- end main sidebar -->
<?php endif; ?>

<?php if ($wrapper == 'boxed' || $porto_layout == 'fullwidth' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') : ?>
    </div>
</div>
<?php endif; ?>

<?php
do_action('porto_before_content_bottom');
if ($content_bottom) : ?>
    <div id="content-bottom"><!-- begin content bottom -->
        <?php echo do_shortcode('[porto_block name="'.$content_bottom.'"]') ?>
    </div><!-- begin content bottom -->
<?php endif;
do_action('porto_before_content_bottom');
?>
