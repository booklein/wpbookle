<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * A better method to store the init data.
 *
 * The JSON data is stored inside this invisible element, the it is parsed
 * and passed as an argument to the initialization method.
 */
?>
<div class="asp_init_data" style="display:none !important;" id="asp_init_id_<?php echo $id; ?>">
    {
        "homeurl": "<?php echo home_url('/'); ?>",
        "resultstype": "<?php echo ((isset($style['resultstype']) && $style['resultstype']!="")?$style['resultstype']:"vertical"); ?>",
        "resultsposition": "<?php echo ((isset($style['resultsposition']) && $style['resultsposition']!="")?$style['resultsposition']:"vertical"); ?>",
        "itemscount": <?php echo ((isset($style['itemscount']) && $style['itemscount']!="")?$style['itemscount']:"2"); ?>,
        "imagewidth": <?php echo ((isset($style['settings-imagesettings']['width']))?$style['settings-imagesettings']['width']:"70"); ?>,
        "imageheight": <?php echo ((isset($style['settings-imagesettings']['height']))?$style['settings-imagesettings']['height']:"70"); ?>,
        "resultitemheight": "<?php echo ((isset($style['resultitemheight']) && $style['resultitemheight']!="")?$style['resultitemheight']:"70"); ?>",
        "showauthor": <?php echo ((isset($style['showauthor']) && $style['showauthor']!="")?$style['showauthor']:"1"); ?>,
        "showdate": <?php echo ((isset($style['showdate']) && $style['showdate']!="")?$style['showdate']:"1"); ?>,
        "showdescription": <?php echo ((isset($style['showdescription']) && $style['showdescription']!="")?$style['showdescription']:"1"); ?>,
        "charcount":  <?php echo ((isset($style['charcount']) && $style['charcount']!="")?$style['charcount']:"3"); ?>,
        "noresultstext": "<?php echo ((isset($style['noresultstext']) && $style['noresultstext']!="")?$style['noresultstext']:"3"); ?>",
        "didyoumeantext": "<?php echo asp_icl_t("Did you mean text", w_isset_def($style['didyoumeantext'], 'Did you mean:')); ?>",
        "defaultImage": "<?php echo w_isset_def($style['image_default'], "")==""?ASP_URL."img/default.jpg":$style['image_default']; ?>",
        "highlight": <?php echo ((isset($style['highlight']) && $style['highlight']!="")?$style['highlight']:1); ?>,
        "highlightwholewords": <?php echo ((isset($style['highlightwholewords']) && $style['highlightwholewords']!="")?$style['highlightwholewords']:1); ?>,
        "openToBlank": <?php echo w_isset_def($style['results_click_blank'], 0); ?>,
        "scrollToResults": <?php echo w_isset_def($style['scroll_to_results'], 1); ?>,
        "resultareaclickable": <?php echo ((isset($style['resultareaclickable']) && $style['resultareaclickable']!="")?$style['resultareaclickable']:0); ?>,
        "autocomplete": {
            "enabled": <?php echo ((isset($style['autocomplete']) && $style['autocomplete']!="")?$style['autocomplete']:1); ?>,
            "googleOnly": <?php echo w_isset_def($style['autocomplete_source'], 'google') == 'google' ? 1 : 0; ?>,
            "lang": "<?php echo w_isset_def($style['autocomplete_google_lang'], 'en'); ?>"
        },
        "triggerontype": <?php echo ((isset($style['triggerontype']) && $style['triggerontype']!="")?$style['triggerontype']:1); ?>,
        "triggeronclick": <?php echo ((isset($style['triggeronclick']) && $style['triggeronclick']!="")?$style['triggeronclick']:1); ?>,
        "triggeronreturn": <?php echo w_isset_def($style['triggeronreturn'], 1); ?>,
        "triggerOnFacetChange": <?php echo w_isset_def($style['trigger_on_facet'], 0); ?>,
        "overridewpdefault": <?php echo w_isset_def($style['override_default_results'], 0); ?>,
        "redirectonclick": <?php echo ((isset($style['redirectonclick']) && $style['redirectonclick']!="")?$style['redirectonclick']:0); ?>,
        "redirectClickTo": "<?php echo w_isset_def($style['redirect_click_to'], 'results_page'); ?>",
        "redirect_on_enter": <?php echo w_isset_def($style['redirect_on_enter'], 0); ?>,
        "redirectEnterTo": "<?php echo w_isset_def($style['redirect_enter_to'], 'results_page'); ?>",
        "redirect_url": "<?php echo w_isset_def($style['redirect_url'], '?s={phrase}'); ?>",
        "more_redirect_url": "<?php echo w_isset_def($style['more_redirect_url'], '?s={phrase}'); ?>",
        "settingsimagepos": "<?php echo ((isset($style['settingsimagepos']) && $style['settingsimagepos']!="")?$style['settingsimagepos']:0); ?>",
        "settingsVisible": <?php echo w_isset_def($style['frontend_search_settings_visible'], 0); ?>,
        "hresulthidedesc": "<?php echo ((isset($style['hhidedesc']) && $style['hhidedesc']!="")?$style['hhidedesc']:1); ?>",
        "prescontainerheight": "<?php echo ((isset($style['prescontainerheight']) && $style['prescontainerheight']!="")?$style['prescontainerheight']:"400px"); ?>",
        "pshowsubtitle": "<?php echo ((isset($style['pshowsubtitle']) && $style['pshowsubtitle']!="")?$style['pshowsubtitle']:0); ?>",
        "pshowdesc": "<?php echo ((isset($style['pshowdesc']) && $style['pshowdesc']!="")?$style['pshowdesc']:1); ?>",
        "closeOnDocClick": <?php echo w_isset_def($style['close_on_document_click'], 1); ?>,
        "iifNoImage": "<?php echo w_isset_def($style['i_ifnoimage'], 'description'); ?>",
        "iiRows": <?php echo w_isset_def($style['i_rows'], 2); ?>,
        "iitemsWidth": <?php echo w_isset_def($style['i_item_width'], 200); ?>,
        "iitemsHeight": <?php echo w_isset_def($style['i_item_height'], 200); ?>,
        "iishowOverlay": <?php echo w_isset_def($style['i_overlay'], 1); ?>,
        "iiblurOverlay": <?php echo w_isset_def($style['i_overlay_blur'], 1); ?>,
        "iihideContent": <?php echo w_isset_def($style['i_hide_content'], 1); ?>,
        "analytics": <?php echo w_isset_def($ana_options['analytics'], 0); ?>,
        "analyticsString": "<?php echo w_isset_def($ana_options['analytics_string'], ""); ?>",
        "aapl": {
            "on_click": <?php echo w_isset_def($style['apl_on_result_click'], 0); ?>,
            "on_magnifier": <?php echo w_isset_def($style['apl_on_magnifier_click'], 0); ?>,
            "on_enter": <?php echo w_isset_def($style['apl_on_enter'], 0); ?>,
            "on_typing": <?php echo w_isset_def($style['apl_on_typing'], 0); ?>
        },
        "compact": {
            "enabled": <?php echo w_isset_def($style['box_compact_layout'], 0); ?>,
            "width": "<?php echo w_isset_def($style['box_compact_width'], "100%"); ?>",
            "closeOnMagnifier": <?php echo w_isset_def($style['box_compact_close_on_magn'], 1); ?>,
            "closeOnDocument": <?php echo w_isset_def($style['box_compact_close_on_document'], 0); ?>,
            "position": "<?php echo w_isset_def($style['box_compact_position'], 0); ?>",
            "overlay": <?php echo w_isset_def($style['box_compact_overlay'], 0); ?>
        },
        "animations": {
            "pc": {
                "settings": {
                    "anim" : "<?php echo w_isset_def($style['sett_box_animation'], 'fadedrop'); ?>",
                    "dur"  : <?php echo w_isset_def($style['sett_box_animation_duration'], 200); ?>
                },
                "results" : {
                    "anim" : "<?php echo w_isset_def($style['res_box_animation'], 'fadedrop'); ?>",
                    "dur"  : <?php echo w_isset_def($style['res_box_animation_duration'], 200); ?>
                },
                "items" : "<?php echo w_isset_def($style['res_items_animation'], 'fadeInUp'); ?>"
            },
            "mob": {
                "settings": {
                    "anim" : "<?php echo w_isset_def($style['sett_box_animation_m'], 'fade'); ?>",
                    "dur"  : <?php echo w_isset_def($style['sett_box_animation_duration_m'], 200); ?>
                },
                "results" : {
                    "anim" : "<?php echo w_isset_def($style['res_box_animation_m'], 'fade'); ?>",
                    "dur"  : <?php echo w_isset_def($style['res_box_animation_duration_m'], 200); ?>
                },
                "items" : "<?php echo w_isset_def($style['res_items_animation_m'], 'voidanim'); ?>"
            }
        }
    }
</div>