<?php
$output = $title = $icon = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'show_icon' => false,
    'icon_type' => 'fontawesome',
    'icon' => '',
    'icon_simpleline' => '',
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

switch ($icon_type) {
    case 'simpleline': $icon_class = $icon_simpleline; break;
    default: $icon_class = $icon;
}
if (!$show_icon)
    $icon_class = '';

if ($animation_type)
    $el_class .= ' appear-animation';

$output = '<div class="porto-links-block wpb_content_element ' . $el_class . '"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
$output .= '>';

if ($title) {
    $output .= '<div class="links-title">' . ($icon_class ? '<i class="' . $icon_class . '"></i>' : '' ) . $title . '</div>';
}

$output .= '<div class="links-content"><ul>' . do_shortcode($content) . '</ul></div>';

$output .= '</div>';

echo $output;