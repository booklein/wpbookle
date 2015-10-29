<?php

// Porto Price Box
add_shortcode('porto_price_box', 'porto_shortcode_price_box');
add_action('vc_after_init', 'porto_load_price_box_shortcode');

function porto_shortcode_price_box($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_price_box'))
        include $template;
    return ob_get_clean();
}

function porto_load_price_box_shortcode() {
    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => __("Price Box", 'porto-shortcodes'),
        "base" => "porto_price_box",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_price_box",
        'weight' => - 50,
        "as_child" => array('only' => 'porto_price_boxes'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", 'porto-shortcodes'),
                "param_name" => "title",
                "admin_label" => true
            ),
            array(
                "type" => "textfield",
                "heading" => __("Description", 'porto-shortcodes'),
                "param_name" => "desc"
            ),
            array(
                'type' => 'checkbox',
                'heading' => __('Popular Price Box', 'porto-shortcodes'),
                'param_name' => 'is_popular',
                'value' => array(__('Yes, please', 'js_composer') => 'true')
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Popular Label', 'porto-shortcodes'),
                'param_name' => 'popular_label',
                'dependency' => array('element' => 'is_popular', 'not_empty' => true)
            ),
            array(
                "type" => "textfield",
                "heading" => __("Price", 'porto-shortcodes'),
                "param_name" => "price",
                "admin_label" => true
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Skin Color', 'porto-shortcodes'),
                'param_name' => 'skin',
                'value' => porto_vc_commons('colors')
            ),
            array(
                'type' => 'checkbox',
                'heading' => __('Show Button', 'porto-shortcodes'),
                'param_name' => 'show_btn',
                'value' => array(__('Yes, please', 'js_composer') => 'true')
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Button Label', 'porto-shortcodes'),
                'param_name' => 'btn_label',
                'dependency' => array('element' => 'show_btn', 'not_empty' => true)
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Button Link', 'porto-shortcodes'),
                'param_name' => 'btn_link',
                'dependency' => array('element' => 'show_btn', 'not_empty' => true)
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Button Size', 'porto-shortcodes'),
                'param_name' => 'btn_size',
                'value' => porto_vc_commons('size'),
                'dependency' => array('element' => 'show_btn', 'not_empty' => true)
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Button Position', 'porto-shortcodes'),
                'param_name' => 'btn_pos',
                'value' => array(
                    __('Top', 'porto-shortcodes') => '',
                    __('Bottom', 'porto-shortcodes') => 'bottom',
                ),
                'dependency' => array('element' => 'show_btn', 'not_empty' => true)
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Button Skin Color', 'porto-shortcodes'),
                'param_name' => 'btn_skin',
                'value' => porto_vc_commons('colors'),
                'dependency' => array('element' => 'show_btn', 'not_empty' => true)
            ),
            array(
                'type' => 'textarea_html',
                'heading' => __('Content', 'porto-shortcodes'),
                'param_name' => 'content'
            ),
            $animation_type,
            $animation_duration,
            $animation_delay,
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Price_Box')) {
        class WPBakeryShortCode_Porto_Price_Box extends WPBakeryShortCode {
        }
    }
}