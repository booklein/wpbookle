<?php

// Porto Members
add_shortcode('porto_members', 'porto_shortcode_members');
add_action('vc_after_init', 'porto_load_members_shortcode');

function porto_shortcode_members($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_members'))
        include $template;
    return ob_get_clean();
}

function porto_load_members_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        'name' => "Porto " . __('Members', 'porto-shortcodes'),
        'base' => 'porto_members',
        'category' => __('Porto', 'porto-shortcodes'),
        'icon' => 'porto_vc_members',
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
                "heading" => __("Category IDs", 'porto-shortcodes'),
                "description" => __("comma separated list of category ids", 'porto-shortcodes'),
                "param_name" => "cats",
                "admin_label" => true
            ),
            array(
                "type" => "textfield",
                "heading" => __("Member IDs", 'porto-shortcodes'),
                "description" => __("comma separated list of member ids", 'porto-shortcodes'),
                "param_name" => "post_in"
            ),
            array(
                "type" => "textfield",
                "heading" => __("Members Count", 'porto-shortcodes'),
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

    if (!class_exists('WPBakeryShortCode_Porto_Members')) {
        class WPBakeryShortCode_Porto_Members extends WPBakeryShortCode {
        }
    }
}