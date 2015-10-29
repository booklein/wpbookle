<?php
$output = $anchor = $container = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'anchor' => '',
    'container' => false,
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

if ($animation_type)
    $el_class .= ' appear-animation';

if ($container)
    $el_class .= ' container';

$id = '';
if ($anchor)
    $id = ' id="' . $anchor . '"';
$output = '<section' . $id . ' class="porto-section">';
$output .= '<div class="' . $el_class . '"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
$output .= '>';
$output .= do_shortcode($content);
$output .= '</div>';
$output .= '</section>';

echo $output;