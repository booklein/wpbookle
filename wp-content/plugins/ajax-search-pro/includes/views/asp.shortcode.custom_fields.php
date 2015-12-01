<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$x_in = 0;
?>

<?php foreach($asp_f_items as $key=>$item): ?>

<?php
    $x_in++;
    $_in = $id.$x_in;
?>

<?php if ($item->asp_f_type != 'hidden'): ?>
<fieldset class="asp_custom_f">
<?php endif; ?>

<?php if (
	w_isset_def($item->asp_f_show_title, 'asp_checked') == 'asp_checked' &&
	$item->asp_f_type != 'hidden'
	): ?>
    <legend><?php echo $item->asp_f_title; ?></legend>
<?php endif; ?>
<?php switch($item->asp_f_type) { case "radio": ?>
        <?php foreach($item->asp_f_radio_value as $radio): ?>
            <?php preg_match('/^(.*?)\|\|(.*)/', $radio, $matches); ?>
            <input type="radio" class="asp_radio" name="aspf[<?php echo $item->asp_f_field; ?>]"
                   value="<?php echo $matches[1]; ?>" <?php echo strpos($matches[2], '**') > 0 ? ' checked="checked"':''; ?>/>
            <label class="asp_label"><?php echo str_replace('**', '', $matches[2]); ?></label><br>
        <?php endforeach; ?>
        <input type="hidden" name="aspfdata[<?php echo $item->asp_f_field; ?>]" value="<?php echo $asp_f_uncoded_items[$key]; ?>">
    <?php break; ?>
    <?php case "dropdown": ?>
        <div class="asp_select_label<?php echo w_isset_def($item->asp_f_dropdown_multi, 'asp_unchecked') == 'asp_checked'?' asp_select_multiple':' asp_select_single'; ?>">
            <select <?php echo w_isset_def($item->asp_f_dropdown_multi, 'asp_unchecked') == 'asp_checked'?' multiple name="aspf['.$item->asp_f_field.'][]"':'name="aspf['.$item->asp_f_field.']"'; ?> >
            <?php foreach($item->asp_f_dropdown_value as $dropdown): ?>
                <?php preg_match('/^(.*?)\|\|(.*)/', $dropdown, $matches); ?>

                <option value="<?php echo $matches[1]; ?>"<?php echo strpos($matches[2], '**') > 0 ? ' selected':''; ?>><?php echo str_replace('**', '', $matches[2]); ?></option>

            <?php endforeach; ?>
            </select>
        </div>
    <?php break; ?>
    <?php case "checkboxes": ?>
        <?php foreach($item->asp_f_checkboxes_value as $checkbox): ?>
            <?php preg_match('/^(.*?)\|\|(.*)/', $checkbox, $matches); ?>
            <div class="option">
                <input type="checkbox" value="None" id="aspf<?php echo $_in; ?>[<?php echo $item->asp_f_field; ?>][<?php echo $matches[1]; ?>]"
                       name="aspf[<?php echo $item->asp_f_field; ?>][<?php echo $matches[1]; ?>]" <?php echo strpos($matches[2], '**') > 0 ? ' checked="checked"':''; ?>>
                <label for="aspf<?php echo $_in; ?>[<?php echo $item->asp_f_field; ?>][<?php echo $matches[1]; ?>]"></label>
            </div>
            <div class="label"><?php echo str_replace('**', '', $matches[2]); ?></div>
        <?php endforeach; ?>
    <?php break; ?>
    <?php case "hidden": ?>
		<input type="hidden" value="<?php echo $item->asp_f_hidden_value; ?>" id="aspf<?php echo $_in; ?>[<?php echo $item->asp_f_field; ?>]" name="aspf[<?php echo $item->asp_f_field; ?>]">
    <?php break; ?>
    <?php case "text": ?>
        <input type="text" value="<?php echo $item->asp_f_text_value; ?>" id="aspf<?php echo $_in; ?>[<?php echo $item->asp_f_field; ?>]" name="aspf[<?php echo $item->asp_f_field; ?>]">
    <?php break; ?>
    <?php case "slider": ?>
        <div id="slider-handles-<?php echo $_in; ?>"></div>
        <div class="asp_noui_lu">

            <span class="asp_noui_l_pre"><?php echo $item->asp_f_slider_prefix; ?></span>
            <span class="slider-handles-low" id="slider-handles-low-<?php echo $_in; ?>"></span>
            <span class="asp_noui_l_suff"><?php echo $item->asp_f_slider_suffix; ?></span>

            <div class="clear"></div>
        </div>
        <input type="hidden" id="slider-values-low-<?php echo $_in; ?>" name="aspf[<?php echo $item->asp_f_field; ?>]" value="<?php echo $item->asp_f_slider_default; ?>">

        <div id="noui-slider-json<?php echo $_in; ?>" class="noui-slider-json<?php echo $id; ?>" style="display: none !important;">
        {
            "node": "#slider-handles-<?php echo $_in; ?>",
            "main": {
                "start": [ <?php echo $item->asp_f_slider_default; ?> ],
                "step": <?php echo $item->asp_f_slider_step; ?>,
                "range": {
                    "min": [  <?php echo $item->asp_f_slider_from; ?> ],
                    "max": [  <?php echo $item->asp_f_slider_to; ?> ]
                }
            },
            "links": [
                {
                    "handle": "lower",
                    "target": "#slider-handles-low-<?php echo $_in; ?>",
                    "wNumb": {
                        "decimals": <?php echo $item->asp_f_slider_to <= 1 ? 2 : 0; ?>
                    }
                },
                {
                    "handle": "lower",
                    "target": "#slider-values-low-<?php echo $_in; ?>",
                    "wNumb": {
                        "decimals": <?php echo $item->asp_f_slider_to <= 1 ? 2 : 0; ?>
                    }
                }
            ]
        }
        </div>

    <?php break; ?>
    <?php case "range": ?>
    <div id="range-handles-<?php echo $_in; ?>"></div>
    <div class="asp_noui_lu">

        <span class="asp_noui_l_pre"><?php echo $item->asp_f_range_prefix; ?></span>
        <span class="slider-handles-low" id="slider-handles-low-<?php echo $_in; ?>"></span>
        <span class="asp_noui_l_suff"><?php echo $item->asp_f_range_suffix; ?></span>

        <span class="asp_noui_u_suff"><?php echo $item->asp_f_range_suffix; ?></span>
        <span class="slider-handles-up" id="slider-handles-up-<?php echo $_in; ?>"></span>
        <span class="asp_noui_u_pre"><?php echo $item->asp_f_range_prefix; ?></span>

        <div class="clear"></div>
    </div>
    <input type="hidden" id="slider-values-low-<?php echo $_in; ?>" name="aspf[<?php echo $item->asp_f_field; ?>][lower]" value="<?php echo $item->asp_f_range_default1; ?>">
    <input type="hidden" id="slider-values-up-<?php echo $_in; ?>" name="aspf[<?php echo $item->asp_f_field; ?>][upper]" value="<?php echo $item->asp_f_range_default2; ?>">

    <div id="noui-slider-json<?php echo $_in; ?>" class="noui-slider-json<?php echo $id; ?>" style="display: none !important;">
    {
        "node": "#range-handles-<?php echo $_in; ?>",
        "main": {
            "start": [ <?php echo $item->asp_f_range_default1; ?>, <?php echo $item->asp_f_range_default2; ?> ],
            "step": <?php echo $item->asp_f_range_step; ?>,
            "range": {
                "min": [  <?php echo $item->asp_f_range_from; ?> ],
                "max": [  <?php echo $item->asp_f_range_to; ?> ]
            }
        },
        "links": [
            {
                "handle": "lower",
                "target": "#slider-handles-low-<?php echo $_in; ?>",
                "wNumb": {
                    "decimals": <?php echo $item->asp_f_range_to <= 1 ? 2 : 0; ?>
                }
            },
            {
                "handle": "upper",
                "target": "#slider-handles-up-<?php echo $_in; ?>",
                "wNumb": {
                    "decimals": <?php echo $item->asp_f_range_to <= 1 ? 2 : 0; ?>
                }
            },
            {
                "handle": "lower",
                "target": "#slider-values-low-<?php echo $_in; ?>",
                "wNumb": {
                    "decimals": <?php echo $item->asp_f_range_to <= 1 ? 2 : 0; ?>
                }
            },
            {
                "handle": "upper",
                "target": "#slider-values-up-<?php echo $_in; ?>",
                "wNumb": {
                    "decimals": <?php echo $item->asp_f_range_to <= 1 ? 2 : 0; ?>
                }
            }
        ]
    }
    </div>
    <?php break; ?>
<?php } //endswitch ?>

<?php if ($item->asp_f_type != 'hidden'): ?>
</fieldset>
<?php endif; ?>

<input type="hidden" name="aspfdata[<?php echo $item->asp_f_field; ?>]" value="<?php echo $asp_f_uncoded_items[$key]; ?>">
<?php endforeach; ?>