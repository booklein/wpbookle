<?php
$output = $container_selector = $min_width = $top = $bottom = $active_class = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'container_selector' => '',
    'min_width' => '767',
    'top' => 110,
    'bottom' => 0,
    'active_class' => 'sticky-active',
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

if ($animation_type)
    $el_class .= ' appear-animation';

$output = '<div class="porto-sticky ' . $el_class . '"';
if ($container_selector)
    $output .= ' data-container-selector="'.$container_selector.'"';
if ($min_width)
    $output .= ' data-min-width="'.$min_width.'"';
if ($top)
    $output .= ' data-top="'.$top.'"';
if ($bottom)
    $output .= ' data-bottom="'.$bottom.'"';
if ($active_class)
    $output .= ' data-active-class="'.$active_class.'"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
$output .= '>';

$output .= do_shortcode($content);

$output .= '</div>';

echo $output;