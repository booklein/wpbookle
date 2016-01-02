<?php
$output = $label = $link = $icon = $el_class = '';
extract(shortcode_atts(array(
    'label' => '',
    'link' => '',
    'show_icon' => false,
    'icon_type' => 'fontawesome',
    'icon' => '',
    'icon_simpleline' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

switch ($icon_type) {
    case 'simpleline': $icon_class = $icon_simpleline; break;
    default: $icon_class = $icon;
}
if (!$show_icon)
    $icon_class = '';

if ($label) {
    $output = '<li class="porto-links-item ' . $el_class . '">';

    if ($link) {
        $output .= '<a href="' . esc_url($link) . '">';
    } else {
        $output .= '<span>';
    }

    $output .= ($icon_class ? '<i class="' . $icon_class . '"></i>' : '' ) . $label;

    if ($link) {
        $output .= '</a>';
    } else {
        $output .= '</span>';
    }

    $output .= '</li>';
}

echo $output;