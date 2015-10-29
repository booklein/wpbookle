<?php

// Porto Links Block
add_shortcode('porto_links_block', 'porto_shortcode_links_block');
add_action('vc_after_init', 'porto_load_links_block_shortcode');

function porto_shortcode_links_block($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_links_block'))
        include $template;
    return ob_get_clean();
}

function porto_load_links_block_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Links Block", 'porto-shortcodes'),
        "base" => "porto_links_block",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_links_block",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "as_parent" => array('only' => 'porto_links_item'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", 'porto-shortcodes'),
                "param_name" => "title",
                "value" => "Navigation",
                "admin_label" => true
            ),
            array(
                'type' => 'checkbox',
                'heading' => __('Show FontAwesome Icon', 'porto-shortcodes'),
                'param_name' => 'show_icon',
                'value' => array(__('Yes, please', 'js_composer') => 'true')
            ),
            array(
                'type' => 'iconpicker',
                'heading' => __('Select FontAwesome Icon', 'porto-shortcodes'),
                'param_name' => 'icon',
                'dependency' => array('element' => 'show_icon', 'not_empty' => true)
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Links_Block')) {
        class WPBakeryShortCode_Porto_Links_Block extends WPBakeryShortCodesContainer {
        }
    }
}