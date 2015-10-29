<?php

// Porto Grid Item
add_shortcode('porto_grid_item', 'porto_shortcode_grid_item');
add_action('vc_after_init', 'porto_load_grid_item_shortcode');

function porto_shortcode_grid_item($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_grid_item'))
        include $template;
    return ob_get_clean();
}

function porto_load_grid_item_shortcode() {
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Grid Item", 'porto-shortcodes'),
        "base" => "porto_grid_item",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_grid_item",
        'is_container' => true,
        'weight' => - 50,
        'js_view' => 'VcColumnView',
        "as_child" => array('only' => 'porto_grid_container'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Width", "porto-shortcodes"),
                "param_name" => "width"
            ),
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Grid_Item')) {
        class WPBakeryShortCode_Porto_Grid_Item extends WPBakeryShortCodesContainer {
        }
    }
}