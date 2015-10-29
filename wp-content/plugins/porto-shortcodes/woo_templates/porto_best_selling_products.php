<?php

$output = $title = $view = $per_page = $columns = $column_width = $addlinks_pos = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract( shortcode_atts( array(
    'title' => '',
    'view' => 'grid',
    'per_page' => 12,
    'columns' => 4,
    'column_width' => '',
    'addlinks_pos' => '',
    'navigation' => 1,
    'pagination' => 0,
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
    if ($view == 'products-slider')
        $output .= '<h2 class="slider-title"><span class="inline-title">'.$title.'</span><span class="line"></span></h2>';
    else
        $output .= '<h2 class="section-title">'.$title.'</h2>';
}

if ($view == 'products-slider')
    $output .= '<div class="slider-wrapper">';

global $woocommerce_loop;

$woocommerce_loop['view'] = $view;
$woocommerce_loop['columns'] = $columns;
$woocommerce_loop['column_width'] = $column_width;
$woocommerce_loop['pagination'] = $pagination;
$woocommerce_loop['navigation'] = $navigation;
$woocommerce_loop['addlinks_pos'] = $addlinks_pos;

$output .= do_shortcode('[best_selling_products per_page="'.$per_page.'" columns="'.$columns.'"]');

if ($view == 'products-slider')
    $output .= '</div>';

$output .= '</div>';

echo $output;