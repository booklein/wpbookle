<?php

// Porto Sort Filters
add_shortcode('porto_sort_filters', 'porto_shortcode_sort_filters');
add_action('vc_after_init', 'porto_load_sort_filters_shortcode');

function porto_shortcode_sort_filters($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_sort_filters'))
        include $template;
    return ob_get_clean();
}

function porto_load_sort_filters_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Sort Filters", 'porto-shortcodes'),
        "base" => "porto_sort_filters",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_sort_filters",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "show_settings_on_create" => false,
        "as_parent" => array('only' => 'porto_sort_filter'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Sort Container ID", 'porto-shortcodes'),
                "param_name" => "container",
                'admin_label' => true
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Sort Style", 'porto-shortcodes'),
                "param_name" => "style",
                'std' => '',
                "value" => porto_vc_commons('sort_style')
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Sort Align', 'porto'),
                'param_name' => 'align',
                'value' => porto_vc_commons('align'),
                'dependency' => array('element' => 'style', 'value' => array('style-2')),
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Sort_Filters')) {
        class WPBakeryShortCode_Porto_Sort_Filters extends WPBakeryShortCodesContainer {
        }
    }
}