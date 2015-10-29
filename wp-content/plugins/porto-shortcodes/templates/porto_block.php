<?php
$output = $id = $name = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'id' => '',
    'name' => '',
    'animation_type' => '',
    'animation_duration' => '',
    'animation_delay' => '',
    'el_class' => '',
), $atts));

if (!post_type_exists('block'))
    return;

if ($id || $name) {
    $el_class = porto_shortcode_extract_class( $el_class );

    if ($animation_type)
        $el_class .= ' appear-animation';

    if ($id)
        $posts = new WP_Query(array( 'include' => $id, 'post_type' => 'block' ));

    if ($name)
        $posts = new WP_Query(array( 'name' => $name, 'post_type' => 'block' ));

    if ($posts->have_posts()) {
        $posts->the_post();

        global $post;

        wp_enqueue_script('wpb_composer_front_js');
        wp_enqueue_style('js_composer_front');

        $post_content = $post->post_content;

        if (class_exists('Ultimate_VC_Addons')) {
            global $porto_ult_assets;

            $isAjax = false;
            $ultimate_ajax_theme = get_option('ultimate_ajax_theme');
            if($ultimate_ajax_theme == 'enable')
                $isAjax = true;
            $dependancy = array('jquery');

            // register js
            wp_register_script('ultimate-script', $porto_ult_assets . 'min-js/ultimate.min.js',array('jquery'), ULTIMATE_VERSION, false);
            wp_register_script('ultimate-appear', $porto_ult_assets . 'min-js/jquery-appear.min.js',array('jquery'), ULTIMATE_VERSION);
            wp_register_script('ultimate-custom', $porto_ult_assets . 'min-js/custom.min.js',array('jquery'), ULTIMATE_VERSION);
            wp_register_script('ultimate-vc-params', $porto_ult_assets . 'min-js/ultimate-params.min.js',array('jquery'), ULTIMATE_VERSION);
            wp_register_script('ultimate-smooth-scroll', $porto_ult_assets . 'min-js/SmoothScroll.min.js',array('jquery'),ULTIMATE_VERSION,true);
            wp_register_script("ultimate-modernizr", $porto_ult_assets . 'min-js/modernizr-custom.min.js',array('jquery'),ULTIMATE_VERSION);
            wp_register_script("ultimate-tooltip", $porto_ult_assets . 'min-js/tooltip.min.js',array('jquery'),ULTIMATE_VERSION);

            // register css
            wp_register_style('ultimate-animate', $porto_ult_assets . 'min-css/animate.min.css',array(),ULTIMATE_VERSION);
            wp_register_style('ultimate-style', $porto_ult_assets . 'min-css/style.min.css',array(),ULTIMATE_VERSION);
            wp_register_style('ultimate-style-min', $porto_ult_assets . 'min-css/ultimate.min.css',array(),ULTIMATE_VERSION);
            wp_register_style('ultimate-tooltip', $porto_ult_assets . 'min-css/tooltip.min.css',array(),ULTIMATE_VERSION);


            $ultimate_smooth_scroll = get_option('ultimate_smooth_scroll');
            if($ultimate_smooth_scroll == "enable")
                wp_enqueue_script('ultimate-smooth-scroll');

            if(function_exists('vc_is_editor')){
                if(vc_is_editor()){
                    wp_enqueue_style('vc-fronteditor', $porto_ult_assets . 'min-css/vc-fronteditor.min.css');
                }
            }
            $fonts = get_option('smile_fonts');
            if(is_array($fonts))
            {
                foreach($fonts as $font => $info)
                {
                    $style_url = $info['style'];
                    if(strpos($style_url, 'http://' ) !== false) {
                        wp_enqueue_style('bsf-'.$font,$info['style']);
                    } else {
                        $paths = wp_upload_dir();
                        $paths['fonts'] = 'smile_fonts';
                        $paths['fonturl'] = set_url_scheme(trailingslashit($paths['baseurl']).$paths['fonts']);
                        wp_enqueue_style('bsf-'.$font, trailingslashit($paths['fonturl']).$info['style']);
                    }
                }
            }

            $bsf_options = get_option('bsf_options');
            $ultimate_global_scripts = (isset($bsf_options['ultimate_global_scripts'])) ? $bsf_options['ultimate_global_scripts'] : false;

            if($ultimate_global_scripts === 'enable') {
                wp_enqueue_script('ultimate-modernizr');
                wp_enqueue_script('jquery_ui');
                wp_enqueue_script('masonry');
                wp_enqueue_script('googleapis');
                wp_enqueue_script('ultimate-script');
                wp_enqueue_script('ultimate-modal-all');
                wp_enqueue_script('jquery.shake', $porto_ult_assets . 'min-js/jparallax.min.js');
                wp_enqueue_script('jquery.vhparallax', $porto_ult_assets . 'min-js/vhparallax.min.js');

                wp_enqueue_style('ultimate-style-min');
                wp_enqueue_style("ult-icons");
                wp_enqueue_style('ultimate-vidcons', $porto_ult_assets . 'fonts/vidcons.css');
                wp_enqueue_script('jquery.ytplayer', $porto_ult_assets . 'min-js/mb-YTPlayer.min.js');
                return false;
            }

            if(stripos($post_content, 'font_call:'))
            {
                preg_match_all('/font_call:(.*?)"/',$post_content, $display);
                enquque_ultimate_google_fonts_optimzed($display[1]);
            }

            $ultimate_js = get_option('ultimate_js', 'disable');
            $bsf_dev_mode = (isset($bsf_options['dev_mode'])) ? $bsf_options['dev_mode'] : false;

            if(($ultimate_js == 'enable' || $isAjax == true) && ($bsf_dev_mode != 'enable') )
            {
                if(
                    stripos( $post_content, '[swatch_container')
                    || stripos( $post_content, '[ultimate_modal')
                )
                {
                    wp_enqueue_script('ultimate-modernizr');
                }

                if( stripos( $post_content, '[ultimate_exp_section') ||
                    stripos( $post_content, '[info_circle') ) {
                    wp_enqueue_script('jquery_ui');
                }

                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_script('masonry');
                }

                if($isAjax == true) { // if ajax site load all js
                    wp_enqueue_script('masonry');
                }

                if( stripos( $post_content, '[ultimate_google_map') ) {
                    wp_enqueue_script('googleapis');
                }

                wp_enqueue_script('ultimate-script');

                if( stripos( $post_content, '[ultimate_modal') ) {
                    //$modal_fixer = get_option('ultimate_modal_fixer');
                    //if($modal_fixer === 'enable')
                    //wp_enqueue_script('ultimate-modal-all-switched');
                    //else
                    wp_enqueue_script('ultimate-modal-all');
                }
            }
            else if($ultimate_js == 'disable')
            {
                wp_enqueue_script('ultimate-vc-params');

                if(
                    stripos( $post_content, '[ultimate_spacer')
                    || stripos( $post_content, '[ult_buttons')
                    || stripos( $post_content, '[ultimate_icon_list')
                ) {
                    wp_enqueue_script('ultimate-custom');
                }
                if(
                    stripos( $post_content, '[just_icon')
                    || stripos( $post_content, '[ult_animation_block')
                    || stripos( $post_content, '[icon_counter')
                    || stripos( $post_content, '[ultimate_google_map')
                    || stripos( $post_content, '[icon_timeline')
                    || stripos( $post_content, '[bsf-info-box')
                    || stripos( $post_content, '[info_list')
                    || stripos( $post_content, '[ultimate_info_table')
                    || stripos( $post_content, '[interactive_banner_2')
                    || stripos( $post_content, '[interactive_banner')
                    || stripos( $post_content, '[ultimate_pricing')
                    || stripos( $post_content, '[ultimate_icons')
                ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ultimate-custom');
                }
                if( stripos( $post_content, '[ultimate_heading') ) {
                    wp_enqueue_script("ultimate-headings-script");
                }
                if( stripos( $post_content, '[ultimate_carousel') ) {
                    wp_enqueue_script('ult-slick');
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult-slick-custom');
                }
                if( stripos( $post_content, '[ult_countdown') ) {
                    wp_enqueue_script('jquery.timecircle');
                    wp_enqueue_script('jquery.countdown');
                }
                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_script('masonry');
                }
                if( stripos( $post_content, '[ultimate_info_banner') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('utl-info-banner-script');
                }
                if( stripos( $post_content, '[ultimate_google_map') ) {
                    wp_enqueue_script('googleapis');
                }
                if( stripos( $post_content, '[swatch_container') ) {
                    wp_enqueue_script('ultimate-modernizr');
                    wp_enqueue_script('swatchbook-js');
                }
                if( stripos( $post_content, '[ult_ihover') ) {
                    wp_enqueue_script('ult_ihover_js');
                }
                if( stripos( $post_content, '[ult_hotspot') ) {
                    wp_enqueue_script('ult_hotspot_tooltipster_js');
                    wp_enqueue_script('ult_hotspot_js');
                }
                if( stripos( $post_content, '[ult_content_box') ) {
                    wp_enqueue_script('ult_content_box_js');
                }
                if( stripos( $post_content, '[bsf-info-box') ) {
                    wp_enqueue_script('info_box_js');
                }
                if( stripos( $post_content, '[icon_counter') ) {
                    wp_enqueue_script('flip_box_js');
                }
                if( stripos( $post_content, '[ultimate_ctation') ) {
                    wp_enqueue_script('utl-ctaction-script');
                }
                if( stripos( $post_content, '[stat_counter') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult-stats-counter-js');
                    //wp_enqueue_script('ult-slick-custom');
                    wp_enqueue_script('ultimate-custom');
                    array_push($dependancy,'stats-counter-js');
                }
                if( stripos( $post_content, '[ultimate_video_banner') ) {
                    wp_enqueue_script('ultimate-video-banner-script');
                }
                if( stripos( $post_content, '[ult_dualbutton') ) {
                    wp_enqueue_script('jquery.dualbtn');

                }
                if( stripos( $post_content, '[ult_createlink') ) {
                    wp_enqueue_script('jquery.ult_cllink');
                }
                if( stripos( $post_content, '[ultimate_img_separator') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult-easy-separator-script');
                    wp_enqueue_script('ultimate-custom');
                }

                if( stripos( $post_content, '[ult_tab_element') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult_tabs_rotate');
                    wp_enqueue_script('ult_tabs_acordian_js');
                }
                if( stripos( $post_content, '[ultimate_exp_section') ) {
                    wp_enqueue_script('jquery_ui');
                    wp_enqueue_script('jquery_ultimate_expsection');
                }

                if( stripos( $post_content, '[info_circle') ) {
                    wp_enqueue_script('jquery_ui');
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('info-circle');
                    //wp_enqueue_script('info-circle-ui-effect');
                }

                if( stripos( $post_content, '[ultimate_modal') ) {
                    wp_enqueue_script('ultimate-modernizr');
                    //$modal_fixer = get_option('ultimate_modal_fixer');
                    //if($modal_fixer === 'enable')
                    //wp_enqueue_script('ultimate-modal-all-switched');
                    //else
                    wp_enqueue_script('ultimate-modal-all');
                }
            }

            $ultimate_css = get_option('ultimate_css');

            if($ultimate_css == "enable"){
                wp_enqueue_style('ultimate-style-min');
                if( stripos( $post_content, '[ultimate_carousel') ) {
                    wp_enqueue_style("ult-icons");
                }
            } else {
                wp_enqueue_style('ultimate-style');

                if( stripos( $post_content, '[ult_animation_block') ) {
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[icon_counter') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ult-flip-style');
                }
                if( stripos( $post_content, '[ult_countdown') ) {
                    wp_enqueue_style('ult-countdown');
                }
                if( stripos( $post_content, '[ultimate_icon_list') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-tooltip');
                }
                if( stripos( $post_content, '[ultimate_carousel') ) {
                    wp_enqueue_style("ult-slick");
                    wp_enqueue_style("ult-icons");
                    wp_enqueue_style("ultimate-animate");
                }
                if( stripos( $post_content, '[ultimate_fancytext') ) {
                    wp_enqueue_style('ultimate-fancytext-style');
                }
                if( stripos( $post_content, '[ultimate_ctation') ) {
                    wp_enqueue_style('utl-ctaction-style');
                }
                if( stripos( $post_content, '[ult_buttons') ) {
                    wp_enqueue_style( 'ult-btn' );
                }
                if( stripos( $post_content, '[ultimate_heading') ) {
                    wp_enqueue_style("ultimate-headings-style");
                }
                if( stripos( $post_content, '[ultimate_icons') || stripos( $post_content, '[single_icon')) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-tooltip');
                }
                if( stripos( $post_content, '[ult_ihover') ) {
                    wp_enqueue_style( 'ult_ihover_css' );
                }
                if( stripos( $post_content, '[ult_hotspot') ) {
                    wp_enqueue_style( 'ult_hotspot_css' );
                    wp_enqueue_style( 'ult_hotspot_tooltipster_css' );
                }
                if( stripos( $post_content, '[ult_content_box') ) {
                    wp_enqueue_style('ult_content_box_css');
                }
                if( stripos( $post_content, '[bsf-info-box') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('info-box-style');
                }
                if( stripos( $post_content, '[info_circle') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('info-circle');
                }
                if( stripos( $post_content, '[ultimate_info_banner') ) {
                    wp_enqueue_style('utl-info-banner-style');
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-timeline-style');
                }
                if( stripos( $post_content, '[just_icon') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-tooltip');
                }
                if( stripos( $post_content, '[interactive_banner_2') ) {
                    wp_enqueue_style('ult-ib2-style');
                }
                if( stripos( $post_content, '[interactive_banner') && !stripos( $post_content, '[interactive_banner_2') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ult-interactive-banner');
                }
                if( stripos( $post_content, '[info_list') ) {
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[ultimate_modal') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-modal');
                }
                if( stripos( $post_content, '[ultimate_info_table') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style("ultimate-pricing");
                }
                if( stripos( $post_content, '[ultimate_pricing') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style("ultimate-pricing");
                }
                if( stripos( $post_content, '[swatch_container') ) {
                    wp_enqueue_style('swatchbook-css');
                }
                if( stripos( $post_content, '[stat_counter') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ult-stats-counter-style');
                }
                if( stripos( $post_content, '[ultimate_video_banner') ) {
                    wp_enqueue_style('ultimate-video-banner-style');
                }
                if( stripos( $post_content, '[ult_dualbutton') ) {
                    wp_enqueue_style('ult-dualbutton');
                }
                if( stripos( $post_content, '[ult_createlink') ) {
                    wp_enqueue_style('ult_cllink');
                }
                if( stripos( $post_content, '[ultimate_img_separator') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ult-easy-separator-style');
                }
                if( stripos( $post_content, '[ult_tab_element') ) {
                    wp_enqueue_style('ult_tabs');
                    wp_enqueue_style('ult_tabs_acordian');
                }
                if( stripos( $post_content, '[ultimate_exp_section') ) {
                    wp_enqueue_style('style_ultimate_expsection');

                }
            }

            if( stripos( $post_content, '[ultimate_google_map') ) {
                if (!wp_script_is('googleapis', 'done')) {
                    echo "<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&ver=".PORTO_SHORTCODES_VERSION."'></script>";
                    wp_dequeue_script( 'googleapis' );
                }
            }
        }

        $shortcodes_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
        if ( ! empty( $shortcodes_custom_css ) ) {
            $output .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
            $output .= $shortcodes_custom_css;
            $output .= '</style>';
        }

        $output .= '<div class="porto-block '. $el_class . '"';
        if ($animation_type)
            $output .= ' data-appear-animation="'.$animation_type.'"';
        if ($animation_delay)
            $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
        if ($animation_duration && $animation_duration != 1000)
            $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
        $output .= '>';
        if (get_post_meta( get_the_ID(), 'container', true ) == 'yes')
            $output .= '<div class="container">';
        $output .= do_shortcode($post_content);
        if (get_post_meta( get_the_ID(), 'container', true ) == 'yes')
            $output .= '</div>';
        $output .= '</div>';

        echo $output;
    }

    wp_reset_postdata();
}