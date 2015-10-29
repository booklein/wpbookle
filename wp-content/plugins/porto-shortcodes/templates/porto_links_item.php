<?php
$output = $label = $link = $icon = $el_class = '';
extract(shortcode_atts(array(
    'label' => '',
    'link' => '',
    'show_icon' => false,
    'icon' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

if ($label) {
    $output = '<li class="porto-links-item ' . $el_class . '">';

    if ($link) {
        $output .= '<a href="' . esc_url($link) . '">';
    } else {
        $output .= '<span>';
    }

    $output .= ($show_icon && $icon ? '<i class="' . $icon . '"></i>' : '' ) . $label;

    if ($link) {
        $output .= '</a>';
    } else {
        $output .= '</span>';
    }

    $output .= '</li>';
}

echo $output;