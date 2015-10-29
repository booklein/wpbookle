<?php

// Porto Recent Posts
add_shortcode('porto_recent_posts', 'porto_shortcode_recent_posts');
add_action('vc_after_init', 'porto_load_recent_posts_shortcode');

function porto_shortcode_recent_posts($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_recent_posts'))
        include $template;
    return ob_get_clean();
}

function porto_load_recent_posts_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        'name' => "Porto " . __('Recent Posts', 'porto-shortcodes'),
        'base' => 'porto_recent_posts',
        'category' => __('Porto', 'porto-shortcodes'),
        'icon' => 'porto_vc_recent_posts',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", 'porto-shortcodes'),
                "param_name" => "title",
                "admin_label" => true
            ),
            array(
                "type" => "textfield",
                "heading" => __("Posts Count", 'porto-shortcodes'),
                "param_name" => "number",
                "value" => "8",
                "admin_label" => true
            ),
            array(
                "type" => "textfield",
                "heading" => __("Category IDs", 'porto-shortcodes'),
                "param_name" => "cats",
                "admin_label" => true
            ),
            array(
                'type' => 'checkbox',
                'heading' => __( 'Show Post Image', 'porto-shortcodes' ),
                'param_name' => 'show_image',
                'std' => 'yes',
                'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
            ),
            array(
                "type" => "textfield",
                "heading" => __("Items to show on Desktop", 'porto-shortcodes'),
                "param_name" => "items_desktop",
                "value" => "4"
            ),
            array(
                "type" => "textfield",
                "heading" => __("Items to show on Tablets", 'porto-shortcodes'),
                "param_name" => "items_tablets",
                "value" => "3"
            ),
            array(
                "type" => "textfield",
                "heading" => __("Items to show on Mobile", 'porto-shortcodes'),
                "param_name" => "items_mobile",
                "value" => "2"
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Recent_Posts')) {
        class WPBakeryShortCode_Porto_Recent_Posts extends WPBakeryShortCode {
        }
    }
}