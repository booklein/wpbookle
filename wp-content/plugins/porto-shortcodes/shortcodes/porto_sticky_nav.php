<?php

// Porto Sticky Nav
add_shortcode('porto_sticky_nav', 'porto_shortcode_sticky_nav');
add_action('vc_after_init', 'porto_load_sticky_nav_shortcode');

function porto_shortcode_sticky_nav($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_sticky_nav'))
        include $template;
    return ob_get_clean();
}

function porto_load_sticky_nav_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Sticky Nav", 'porto-shortcodes'),
        "base" => "porto_sticky_nav",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_sticky_nav",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "as_parent" => array('only' => 'porto_sticky_nav_link'),
        "params" => array(
            array(
                'type' => 'checkbox',
                'heading' => __("Wrap as Container", 'porto-shortcodes'),
                'param_name' => 'container',
                'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
            ),
            array(
                "type" => "textfield",
                "heading" => __("Min Width (unit: px)", 'porto-shortcodes'),
                "param_name" => "min_width",
                "description" => __("Wll be disable sticky if window width is smaller than min width", 'porto-shortcodes'),
                "value" => "991"
            ),
            array(
                'type' => 'colorpicker',
                'heading' => __('Background Color', 'porto-shortcodes'),
                'param_name' => 'bg_color'
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Skin Color', 'porto-shortcodes'),
                'param_name' => 'skin',
                'std' => 'custom',
                'value' => porto_vc_commons('colors')
            ),
            array(
                'type' => 'colorpicker',
                'heading' => __('Link Color', 'porto-shortcodes'),
                'param_name' => 'link_color',
                'dependency' => array('element' => 'skin', 'value' => array( 'custom' ))
            ),
            array(
                'type' => 'colorpicker',
                'heading' => __('Link Background Color', 'porto-shortcodes'),
                'param_name' => 'link_bg_color',
                'dependency' => array('element' => 'skin', 'value' => array( 'custom' ))
            ),
            array(
                'type' => 'colorpicker',
                'heading' => __('Link Active Color', 'porto-shortcodes'),
                'param_name' => 'link_acolor',
                'dependency' => array('element' => 'skin', 'value' => array( 'custom' ))
            ),
            array(
                'type' => 'colorpicker',
                'heading' => __('Link Active Background Color', 'porto-shortcodes'),
                'param_name' => 'link_abg_color',
                'dependency' => array('element' => 'skin', 'value' => array( 'custom' ))
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Sticky_Nav')) {
        class WPBakeryShortCode_Porto_Sticky_Nav extends WPBakeryShortCodesContainer {
        }
    }
}