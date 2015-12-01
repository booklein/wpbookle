<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$types = get_post_types(array(
    '_builtin' => false
));
$i = 1;
if (!isset($style['selected-customtypes']) || !is_array($style['selected-customtypes']))
    $style['selected-customtypes'] = array();
if (!isset($style['selected-showcustomtypes']) || !is_array($style['selected-showcustomtypes']))
    $style['selected-showcustomtypes'] = array();
$flat_show_customtypes = array();

ob_start();

foreach ($style['selected-showcustomtypes'] as $k => $v) {
    $selected = in_array($v[0], $style['selected-customtypes']);
    $hidden = "";
    $flat_show_customtypes[] = $v[0];
    ?>
    <div class="option<?php echo $hidden; ?>">
        <input type="checkbox" value="<?php echo $v[0]; ?>" id="<?php echo $id; ?>customset_<?php echo $id . $i; ?>"
               name="customset[]" <?php echo(($selected) ? 'checked="checked"' : ''); ?>/>
        <label for="<?php echo $id; ?>customset_<?php echo $id . $i; ?>"></label>
    </div>
    <div class="label<?php echo $hidden; ?>">
        <?php echo asp_icl_t($v[0], $v[1]); ?>
    </div>
    <?php
    $i++;
}


$hidden_types = array();
$hidden_types = array_diff($style['selected-customtypes'], $flat_show_customtypes);

foreach ($hidden_types as $k => $v) {

    ?>
    <div class="option hiddend">
        <input type="checkbox" value="<?php echo $v; ?>"
               id="<?php echo $id; ?>customset_<?php echo $id . $i; ?>"
               name="customset[]" checked="checked"/>
        <label for="<?php echo $id; ?>customset_<?php echo $id . $i; ?>"></label>
    </div>
    <div class="label hiddend"></div>
<?php
$i++;
}

$cpt_content = ob_get_clean();

$cpt_label = w_isset_def($style['custom_types_label'], 'Filter by Custom Post Type');
?>
<fieldset class="asp_sett_scroll<?php echo count($style['selected-showcustomtypes']) > 0 ? '' : ' hiddend'; ?>">
    <?php if ($cpt_label != ''): ?>
    <legend><?php echo asp_icl_t("Custom post types label", $cpt_label);  ?></legend>
    <?php endif; ?>
    <?php echo $cpt_content; ?>
</fieldset>