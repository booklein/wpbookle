<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$real_id = $id;
$id = $id . '_' . self::$perInstanceCount[$real_id];

$ana_options = get_option('asp_analytics');
$comp_options = get_option('asp_compatibility');

if (ASP_DEBUG < 1 && strpos(w_isset_def($comp_options['js_source'], 'min-scoped'), "scoped") !== false) {
    $scope = "aspjQuery";
} else {
    $scope = "jQuery";
}

?>
<div class='wpdreams_asp_sc ajaxsearchpro asp_main_container <?php echo $extra_class; ?><?php echo w_isset_def($style['box_compact_layout'], 0)==1?' asp_compact':''; ?>' id='ajaxsearchpro<?php echo $id; ?>'>
<?php

/******************** PROBOX INCLUDE ********************/
include('asp.shortcode.probox.php');

/******************** RESULTS INCLUDE ********************/
include('asp.shortcode.results.php');

$blocking = w_isset_def($style['frontend_search_settings_position'], 'hover');
if ($blocking == 'block'): ?>
</div>
<div id='ajaxsearchprobsettings<?php echo $id; ?>' class="wpdreams_asp_sc ajaxsearchpro searchsettings">
<?php else: ?>
    <div id='ajaxsearchprosettings<?php echo $id; ?>' class="wpdreams_asp_sc ajaxsearchpro searchsettings">
<?php endif;

/******************* SETTINGS INCLUDE *******************/
include('asp.shortcode.settings.php');
?>

</div>

<?php if ($blocking != 'block'): ?>
</div>
<?php endif;

/******************* CLEARFIX *******************/
if (w_isset_def($style['box_compact_float'], 'none') != 'none') {
    echo '<div class="wpdreams_clear"></div>';
}

/***************** SUGGESTED PHRASES ******************/
if (w_isset_def($style['frontend_show_suggestions'], 0) == 1) {
    $s_phrases = str_replace(array('  ,', ' , ', ', ', ' ,'), '</a>, <a href="#">', $style['frontend_suggestions_keywords']);
    ?>
    <p id="asp-try-<?php echo $id; ?>" class="asp-try"><?php echo $style['frontend_suggestions_text'].' <a href="#">'.$s_phrases.'</a>'; ?></p><?php
}

/******************** DATA INCLUDE ********************/
if (!self::$dataPrinted){
    include('asp.shortcode.data.php');
    self::$dataPrinted = true;
}

/****************** CUSTOM CSS ECHO *******************/
 if (w_isset_def($style['custom_css'], "") != ""): ?>
    <style type="text/css">
        <?php echo stripcslashes(base64_decode($style['custom_css'])); ?>
    </style>
<?php
endif;

/******************** SCRIPT INCLUDE ********************/
include('asp.shortcode.script.php');
