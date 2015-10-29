<?php
$output = $width = $el_class = '';
extract(shortcode_atts(array(
    'width' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-grid-item ' . $el_class . ($width ? '" style="width:' . esc_attr($width) : '') . '">';
$output .= do_shortcode($content);
$output .= '</div>';

echo $output;