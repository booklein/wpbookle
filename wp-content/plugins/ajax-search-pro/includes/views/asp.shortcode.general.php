<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$field_visible =
    ($style['showexactmatches'] == 1) ||
    ($style['showexactmatches'] == 1) ||
    ($style['showsearchintitle'] == 1) ||
    ($style['showsearchincontent'] == 1) ||
    ($style['showsearchincomments'] == 1) ||
    ($style['showsearchinexcerpt'] == 1) ||
    ($style['showsearchinposts'] == 1) ||
    ($style['showsearchinpages'] == 1) ||
    ($style['showsearchinbpgroups'] == 1) ||
    ($style['showsearchinbpusers'] == 1) ||
    ($style['showsearchinbpforums'] == 1);

do_action('asp_layout_settings_before_first_item', $id);
?>
<fieldset class="asp_sett_scroll<?php echo ($field_visible) ? "" : " hiddend"; ?>">
    <div class="option hiddend">
        <input type='hidden' name='qtranslate_lang'
               value='<?php echo(function_exists('qtrans_getLanguage') ? qtrans_getLanguage() : '0'); ?>'/>
    </div>

	<?php if (defined('ICL_LANGUAGE_CODE')
	          && ICL_LANGUAGE_CODE != ''
	          && defined('ICL_SITEPRESS_VERSION')
	): ?>
	<div class="option hiddend">
		<input type='hidden' name='wpml_lang'
		       value='<?php echo ICL_LANGUAGE_CODE; ?>'/>
	</div>
	<?php endif; ?>

    <div class="option<?php echo(($style['showexactmatches'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="checked" id="set_exactonly<?php echo $id; ?>"
               name="set_exactonly" <?php echo(($style['exactonly'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_exactonly<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showexactmatches'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Exact matches option", $style['exactmatchestext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchintitle'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_intitle<?php echo $id; ?>"
               name="set_intitle" <?php echo(($style['searchintitle'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_intitle<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchintitle'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in title option", $style['searchintitletext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchincontent'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_incontent<?php echo $id; ?>"
               name="set_incontent" <?php echo(($style['searchincontent'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_incontent<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchincontent'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in content option", $style['searchincontenttext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchincomments'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_incomments<?php echo $id; ?>"
               name="set_incomments" <?php echo(($style['searchincomments'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_incomments<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchincomments'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in comments option", $style['searchincommentstext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchinexcerpt'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inexcerpt<?php echo $id; ?>"
               name="set_inexcerpt" <?php echo(($style['searchinexcerpt'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inexcerpt<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinexcerpt'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in excerpt option", $style['searchinexcerpttext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchinposts'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inposts<?php echo $id; ?>"
               name="set_inposts" <?php echo(($style['searchinposts'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inposts<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinposts'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in posts option", $style['searchinpoststext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchinpages'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inpages<?php echo $id; ?>"
               name="set_inpages" <?php echo(($style['searchinpages'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inpages<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinpages'] != 1) ? " hiddend" : ""); ?>">
        <?php echo asp_icl_t("Search in pages option", $style['searchinpagestext']); ?>
    </div>
    <div class="option<?php echo(($style['showsearchinbpgroups'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inbpgroups<?php echo $id; ?>"
               name="set_inbpgroups" <?php echo(($style['searchinbpgroups'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inbpgroups<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinbpgroups'] != 1) ? " hiddend" : ""); ?>">
        <?php echo $style['searchinbpgroupstext']; ?>
    </div>
    <div class="option<?php echo(($style['showsearchinbpusers'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inbpusers<?php echo $id; ?>"
               name="set_inbpusers" <?php echo(($style['searchinbpusers'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inbpusers<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinbpusers'] != 1) ? " hiddend" : ""); ?>">
        <?php echo $style['searchinbpuserstext']; ?>
    </div>
    <div class="option<?php echo(($style['showsearchinbpforums'] != 1) ? " hiddend" : ""); ?>">
        <input type="checkbox" value="None" id="set_inbpforums<?php echo $id; ?>"
               name="set_inbpforums" <?php echo(($style['searchinbpforums'] == 1) ? 'checked="checked"' : ''); ?>/>
        <label for="set_inbpforums<?php echo $id; ?>"></label>
    </div>
    <div class="label<?php echo(($style['showsearchinbpforums'] != 1) ? " hiddend" : ""); ?>">
        <?php echo $style['searchinbpforumstext']; ?>
    </div>

</fieldset>