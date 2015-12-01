<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$cat_content = '';
$term_content = '';

$cat_ordering = w_isset_def($style['selected-frontend_cat_order'], array('name', 'ASC'));
$term_ordering = w_isset_def($style['selected-frontend_term_order'], array('name', 'ASC'));

if ($style['showsearchincategories']) {
    ob_start();
?>
    <div class='categoryfilter asp_sett_scroll'>
    <?php
    /* Categories */
    if (!isset($style['selected-exsearchincategories']) || !is_array($style['selected-exsearchincategories']))
        $style['selected-exsearchincategories'] = array();
    if (!isset($style['selected-excludecategories']) || !is_array($style['selected-excludecategories']))
        $style['selected-excludecategories'] = array();



    //$_all_cat = get_all_category_ids();
    $_all_cat = get_terms('category', array(
	    'fields' => 'ids'
    ));
    $_needed_cat = array_diff($_all_cat, $style['selected-exsearchincategories']);

    $_needed_cat_full = get_terms('category', array(
	    'orderby' => $cat_ordering[0],
	    'order'   => $cat_ordering[1],
	    'include' => $_needed_cat
    ));

    $_needed_cat_sorted = array();
    $needed_cat_flat = array();

    if (w_isset_def($style['frontend_cat_hierarchy'], 1) == 1) {
	    wd_sort_terms_hierarchicaly( $_needed_cat_full, $_needed_cat_sorted );
	    wd_flatten_hierarchical_terms( $_needed_cat_sorted, $needed_cat_flat );
    } else {
	    $needed_cat_flat = $_needed_cat_full;
    }

    $as_select = 0;
    if ($as_select == 0) {
	    foreach ($needed_cat_flat as $k => $cat) {
		    $selected = ! in_array( $cat->term_id, $style['selected-excludecategories'] );
		    //$cat = get_category($v);
		    $val = $cat->name;
		    if ( ! isset( $cat->level ) ) {
			    $cat->level = 0;
		    }

		    $hidden = ( ( $style['showsearchincategories'] ) == 0 ? " hiddend" : "" );
		    if ( $style['showuncategorised'] == 0 && $cat->term_id == 1 ) {
			    $hidden = ' hiddend';
		    }
		    ?>
		    <div class="asp_option_cat asp_option_cat_level-<?php echo $cat->level; ?>"
		         asp_cat_parent="<?php echo $cat->parent; ?>">
			    <div class="option<?php echo $hidden; ?>">
				    <input type="checkbox" value="<?php echo $cat->term_id; ?>"
				           id="<?php echo $id; ?>categoryset_<?php echo $cat->term_id; ?>"
				           name="categoryset[]" <?php echo( ( $selected ) ? 'checked="checked"' : '' ); ?>/>
				    <label for="<?php echo $id; ?>categoryset_<?php echo $cat->term_id; ?>"></label>
			    </div>
			    <div class="label<?php echo $hidden; ?>">
				    <?php echo $val; ?>
			    </div>
		    </div>
	    <?php
	    }
    } else {
	    echo "<div class='asp_select_label asp_select_single'><select>";
	    foreach ($needed_cat_flat as $k => $cat) {
		    $selected = ! in_array( $cat->term_id, $style['selected-excludecategories'] );
		    $val = $cat->name;
		    if ( ! isset( $cat->level ) )
			    $cat->level = 0;
		    ?>
		    <option class="asp_option_cat asp_option_cat_level-<?php echo $cat->level; ?>"
		            asp_cat_parent="<?php echo $cat->parent; ?>"
		            value="<?php echo $cat->term_id; ?>"><?php echo $val; ?></option>
	    <?php
	    }
	    echo "</select></div>";
    }
    ?>
</div>
<?php
    $cat_content = ob_get_clean();
}
?>


<?php do_action('asp_layout_settings_after_last_item', $id); ?>
<?php

/* Terms */
if ($style['showsearchintaxonomies'] == 1) {
    if (!isset($style['selected-excludeterms']) || !is_array($style['selected-excludeterms']))
        $style['selected-excludeterms'] = array();
    if (!isset($style['selected-showterms']) || !is_array($style['selected-showterms']))
        $style['selected-showterms'] = array();

    //$_all_term_ids = wpdreams_get_all_term_ids();

    //$_needed_terms = array_diff($_all_term_ids, $style['selected-excludeterms']);

    //$_invisible_terms = array_diff($_needed_terms, $style['selected-showterms']);

    $_close_fieldset = false;

    $_terms = array();
    $visible_terms = array();

    ob_start();

    foreach ($style['selected-showterms'] as $taxonomy => $terms) {
        if (is_array($terms)) {

	        $_needed_terms_full = get_terms($taxonomy, array(
		        'orderby' => $term_ordering[0],
		        'order'   => $term_ordering[1],
		        'include' => $terms
	        ));

            $_needed_terms_full = apply_filters('asp_fontend_get_taxonomy_terms',
                $_needed_terms_full,
                $taxonomy,
                array(
                    'orderby' => $term_ordering[0],
                    'order'   => $term_ordering[1],
                    'include' => $terms
                )
            );

            //var_dump($taxonomy);

	        $_needed_terms_sorted = array();
	        $needed_terms_flat = array();

	        if (w_isset_def($style['frontend_term_hierarchy'], 1) == 1) {
		        wd_sort_terms_hierarchicaly( $_needed_terms_full, $_needed_terms_sorted );
		        wd_flatten_hierarchical_terms( $_needed_terms_sorted, $needed_terms_flat );
	        } else {
		        $needed_terms_flat = $_needed_terms_full;
	        }

            if ($style['showseparatefilterboxes'] != 0) {
                $_x_term = get_taxonomies(array("name" => $taxonomy), "objects");
                //var_dump($_x_term);
                if (isset($_x_term[$taxonomy]))
                    $_tax_name = $_x_term[$taxonomy]->label;
                ?>
                <fieldset>
                <legend><?php echo asp_icl_t("Taxonomy filter box text", $style['exsearchintaxonomiestext']) . " " . $_tax_name; ?></legend>
                <div class='categoryfilter asp_sett_scroll'>
            <?php
            }

            foreach ($needed_terms_flat as $k => $term) {
                $checked = wd_in_array_r($term->term_id, $style['selected-excludeterms']) ? '' : 'checked="checked"';
                ?>
		        <div class="asp_option_cat asp_option_cat_level-<?php echo $term->level; ?>" asp_cat_parent="<?php echo $term->parent; ?>">
	                <div class="option">
	                    <input type="checkbox" value="<?php echo $term->term_id; ?>" id="<?php echo $id; ?>termset_<?php echo $term->term_id; ?>"
	                           name="termset[]" <?php echo $checked; ?>/>
	                    <label for="<?php echo $id; ?>termset_<?php echo $term->term_id; ?>"></label>
	                </div>
	                <div class="label">
	                    <?php echo $term->name; ?>
	                </div>
		        </div>
                <?php
                //$counter++;
            }

            if ($style['showseparatefilterboxes'] != 0) {
                ?>
                </div>
                </fieldset>
            <?php
            }

        }
    }

    $term_content = ob_get_clean();
}
?>
<fieldset<?php echo trim($cat_content) == '' || count( w_isset_def($needed_cat_flat, array()) ) <= 0 ? ' class="hiddend"' : ''; ?>>
    <?php if ($style['exsearchincategoriestext'] != ""): ?>
        <legend><?php echo asp_icl_t("Categories filter box text", $style['exsearchincategoriestext']); ?></legend>
        <?php echo $cat_content; ?>
    <?php endif; ?>
</fieldset>

<?php if ($style['showseparatefilterboxes'] == 0): ?>
    <fieldset<?php echo count($style['selected-showterms']) > 0 ? '' : ' class="hiddend"'; ?>>
        <div class='categoryfilter'>
        <?php echo $term_content; ?>
        </div>
    </fieldset>
<?php else: ?>
    <?php echo $term_content; ?>
<?php endif; ?>
