<?php

// Porto Section
add_shortcode('porto_section', 'porto_shortcode_section');
add_action('vc_after_init', 'porto_load_section_shortcode');

function porto_shortcode_section($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_section'))
        include $template;
    return ob_get_clean();
}

function porto_load_section_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Section", 'porto-shortcodes'),
        "base" => "porto_section",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_section",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Anchor Name", 'porto-shortcodes'),
                "param_name" => "anchor",
                "admin_label" => true
            ),
            array(
                'type' => 'checkbox',
                'heading' => __("Wrap as Container", 'porto-shortcodes'),
                'param_name' => 'container',
                'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Section')) {
        class WPBakeryShortCode_Porto_Section extends WPBakeryShortCodesContainer {
        }
    }
}