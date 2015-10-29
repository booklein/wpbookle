<?php

$output = $title = $view = $column_width = $addlinks_pos = $id = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract( shortcode_atts( array(
    'title' => '',
    'view' => 'grid',
    'column_width' => '',
    'id' => '',
    'addlinks_pos' => '',
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts ) );

$el_class = porto_shortcode_extract_class( $el_class );

if ($animation_type)
    $el_class .= ' appear-animation';

$output = '<div class="porto-products wpb_content_element' . $el_class . '"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
$output .= '>';

if ( $title ) {
    $output .= '<h2 class="section-title">'.$title.'</h2>';
}

global $woocommerce_loop;

$woocommerce_loop['view'] = $view;
$woocommerce_loop['column_width'] = $column_width;
$woocommerce_loop['addlinks_pos'] = $addlinks_pos;

$output .= do_shortcode('[product id="'.$id.'"]');

$output .= '</div>';

echo $output;