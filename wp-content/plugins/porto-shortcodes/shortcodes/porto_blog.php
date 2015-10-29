<?php

// Porto Blog
add_shortcode('porto_blog', 'porto_shortcode_blog');
add_action('vc_after_init', 'porto_load_blog_shortcode');

function porto_shortcode_blog($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_blog'))
        include $template;
    return ob_get_clean();
}

function porto_load_blog_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        'name' => "Porto " . __('Blog', 'porto-shortcodes'),
        'base' => 'porto_blog',
        'category' => __('Porto', 'porto-shortcodes'),
        'icon' => 'porto_vc_blog',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", 'porto-shortcodes'),
                "param_name" => "title",
                "admin_label" => true
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Blog Layout", 'porto-shortcodes'),
                "param_name" => "post_layout",
                'std' => 'timeline',
                "value" => porto_vc_commons('blog_layout'),
                "admin_label" => true
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Columns", 'porto-shortcodes'),
                "param_name" => "columns",
                'dependency' => Array('element' => 'post_layout', 'value' => array( 'grid' )),
                'std' => '3',
                "value" => porto_vc_commons('blog_grid_columns')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Category IDs", 'porto-shortcodes'),
                "description" => __("comma separated list of category ids", 'porto-shortcodes'),
                "param_name" => "cats",
                "admin_label" => true
            ),
            array(
                "type" => "textfield",
                "heading" => __("Post IDs", 'porto-shortcodes'),
                "description" => __("comma separated list of post ids", 'porto-shortcodes'),
                "param_name" => "post_in"
            ),
            array(
                "type" => "textfield",
                "heading" => __("Posts Count", 'porto-shortcodes'),
                "param_name" => "number",
                "value" => '8'
            ),
            array(
                'type' => 'checkbox',
                'heading' => __("Show View More", 'porto-shortcodes'),
                'param_name' => 'view_more',
                'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Blog')) {
        class WPBakeryShortCode_Porto_Blog extends WPBakeryShortCode {
        }
    }
}