<?php
$output = $title = $desc = $is_popular = $popular_label = $price = $skin = $show_btn = $btn_label = $btn_link = $btn_size = $btn_pos = $btn_skin = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'desc' => '',
    'is_popular' => false,
    'popular_label' => '',
    'price' => '',
    'skin' => 'custom',
    'show_btn' => false,
    'btn_label' => '',
    'btn_link' => '',
    'btn_size' => '',
    'btn_pos' => '',
    'btn_skin' => 'custom',
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

if ($animation_type)
    $el_class .= ' appear-animation';

if ($is_popular)
    $el_class .= ' most-popular';

if ($skin)
    $el_class .= ' plan-' . $skin;

$btn_class = 'btn';
$btn_html = '';
if ($btn_size)
    $btn_class .= ' btn-' . $btn_size;
if ('custom' !== $btn_skin)
    $btn_class .= ' btn-' . $btn_skin;
else
    $btn_class .= ' btn-default';
if ('bottom' !== $btn_pos)
    $btn_class .= ' btn-top';
else
    $btn_class .= ' btn-bottom';

if ($btn_link) {
    $btn_html .= '<a href="' . esc_attr($btn_link) . '" class="' . $btn_class . '">';
} else {
    $btn_html .= '<span class="' . $btn_class . '">';
}

$btn_html .= $btn_label;

if ($btn_link) {
    $btn_html .= '</a>';
} else {
    $btn_html .= '</span>';
}

if ($btn_html) {
    if ('bottom' === $btn_pos) {
        $el_class .= ' plan-btn-bottom';
    } else {
        $el_class .= ' plan-btn-top';
    }
}

global $porto_price_boxes_count_md, $porto_price_boxes_count_sm;

if (false === $porto_price_boxes_count_md)
    $porto_price_boxes_count_md = 4;

if (false === $porto_price_boxes_count_sm)
    $porto_price_boxes_count_sm = 2;

$css_class = ' col-md-' . (12 / $porto_price_boxes_count_md);
$css_class .= ' col-sm-' . (12 / $porto_price_boxes_count_sm);

$output = '<div class="' . $css_class . '"><div class="porto-price-box plan ' . $el_class . '"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
$output .= '>';

if ($is_popular && $popular_label) {
    $output .= '<div class="plan-ribbon-wrapper"><div class="plan-ribbon">' . $popular_label . '</div></div>';
}

if ($title || $price || $desc) {
    $output .= '<h3>';
    if ($title)
        $output .= $title;
    if ($desc)
        $output .= '<em class="desc">' . $desc . '</em>';
    if ($price)
        $output .= '<span>' . $price . '</span>';
    $output .= '</h3>';
}

if ($show_btn && 'bottom' !== $btn_pos) {
    $output .= $btn_html;
}

$output .= porto_shortcode_js_remove_wpautop($content, true);

if ($show_btn && 'bottom' === $btn_pos) {
    $output .= $btn_html;
}

$output .= '</div></div>';

echo $output;