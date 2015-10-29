<?php
$output = $container = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'container' => false,
    'customize' => false,
    'image' => '',
    'gap' => 164,
    'min_height' => 400,
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => ''
), $atts));

$el_class = porto_shortcode_extract_class( $el_class );

if ($animation_type)
    $el_class .= ' appear-animation';

$output = '<div class="porto-map-section ' . $el_class . '"';
if ($animation_type)
    $output .= ' data-appear-animation="'.$animation_type.'"';
if ($animation_delay)
    $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
if ($animation_duration && $animation_duration != 1000)
    $output .= ' data-appear-animation-duration="'.$animation_duration.'"';

if ($customize) {
    $img_id = preg_replace('/[^\d]/', '', $image);
    $img = porto_shortcode_get_image_by_size(array( 'attach_id' => $img_id, 'thumb_size' => '145x145' ));
    $img_url = $img['p_img_large'][0];
    $gap = (int)$gap;
    $output .= ' style="background-image:url(' . str_replace(array('http:', 'https:'), '', $img_url) . ');' . ($gap != 164 ? 'padding-top:'.$gap.'px' : '') . '"';
}

$output .= '>';

$output .= '<section class="map-content"'.($customize && $min_height != 400 ? ' style="min-height:'.$min_height.'px"' : '').'>';
if ($container)
    $output .= '<div class="container">';
$output .= do_shortcode($content);
if ($container)
    $output .= '</div>';
$output .= '</section>';
$output .= '</div>';

echo $output;