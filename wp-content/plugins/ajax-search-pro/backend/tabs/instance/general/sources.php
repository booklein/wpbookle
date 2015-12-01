<div class="item"><?php
	$_it_engine_val = isset($_POST['search_engine']) ? $_POST['search_engine'] : wpdreams_setval_or_getoption($sd, 'search_engine', $_dk);
	$o = new wpdreamsCustomSelect("search_engine", "Search engine",
		array(
			'selects' => array(
				array('option' => 'Regular engine', 'value' => 'regular'),
				array('option' => 'Index table engine', 'value' => 'index')
			),
			'value' => wpdreams_setval_or_getoption($sd, 'search_engine', $_dk)
		));
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg">Index table engine will only work if you have the
		<a href="<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/index_table.php"; ?>">index table</a>
	generated. To learn more about the pros. and cons. of the index table read the
		<a href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/index_table.html" target="_blank">documentation about the index table</a>.
	</p>
</div>
<?php
	$it_options_visibility = $_it_engine_val == 'index' ? ' hiddend' : '';
?>
<div class="item it_engine_index_d" style="text-align: center;">
	Since you have the Index table engine selected, some options here are disabled,<br> because they are available
	on the <a href="<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/index_table.php"; ?>" target="_blank">index table</a>
	options page.
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("searchinposts", "Search in posts?",
        wpdreams_setval_or_getoption($sd, "searchinposts", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("searchinpages", "Search in pages?",
        wpdreams_setval_or_getoption($sd, 'searchinpages', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsCustomPostTypes("customtypes", "Search in custom post types",
        wpdreams_setval_or_getoption($sd, 'customtypes', $_dk));
    $params[$o->getName()] = $o->getData();
    $params['selected-'.$o->getName()] = $o->getSelected();
    ?></div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchintitle", "Search in title?",
        wpdreams_setval_or_getoption($sd, 'searchintitle', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchincontent", "Search in content?",
        wpdreams_setval_or_getoption($sd, 'searchincontent', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchincomments", "Search in comments?",
        wpdreams_setval_or_getoption($sd, 'searchincomments', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchinexcerpt", "Search in post excerpts?",
        wpdreams_setval_or_getoption($sd, 'searchinexcerpt', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index"><?php
    $o = new wpdreamsCustomFields("customfields", "Search in custom fields",
        wpdreams_setval_or_getoption($sd, 'customfields', $_dk));
    $params[$o->getName()] = $o->getData();
    $params['selected-'.$o->getName()] = $o->getSelected();
    ?>
</div>

<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchindrafts", "Search in draft posts?",
        wpdreams_setval_or_getoption($sd, 'searchindrafts', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchinpending", "Search in pending posts?",
        wpdreams_setval_or_getoption($sd, 'searchinpending', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item it_engine_index">
    <?php
    $o = new wpdreamsYesNo("searchinterms", "Search in terms? (categories, tags)",
        wpdreams_setval_or_getoption($sd, 'searchinterms', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Will search in terms (categories, tags) related to posts.</p>
    <p class="errorMsg">WARNING: <strong>Search in terms</strong> can be database heavy operation. Not recommended for big databases.</p>
</div>
<script>
jQuery(function($) {
	$('select[name="search_engine"]').change(function() {
		if ($(this).val() == 'index') {
			$("#wpdreams .item.it_engine_index").css('display', 'none');
			$("#wpdreams .item.it_engine_index_d").css('display', 'block');
		} else {
			$("#wpdreams .item.it_engine_index").css('display', 'block');
			$("#wpdreams .item.it_engine_index_d").css('display', 'none');
		}
	});
	$('select[name="search_engine"]').change();
});
</script>