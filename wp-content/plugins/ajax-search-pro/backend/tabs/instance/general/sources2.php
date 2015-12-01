<div class="item">
    <?php
    $o = new wpdreamsYesNo("return_categories", "Return post categories as results?",
        wpdreams_setval_or_getoption($sd, "return_categories", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("return_tags", "Return post tags as results?",
		wpdreams_setval_or_getoption($sd, "return_tags", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTaxonomySelect("return_terms", "Return taxonomy terms as results", array(
        "value"=>wpdreams_setval_or_getoption($sd, 'return_terms', $_dk),
        "type"=>"include"));
    $params[$o->getName()] = $o->getData();
    $params['selected-'.$o->getName()] = $o->getSelected();
    ?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("display_number_posts_affected", "Display the number of posts associated with the terms?",
		wpdreams_setval_or_getoption($sd, "display_number_posts_affected", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg">Will display the number of associated posts in a bracket after the term.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextarea("return_terms_exclude", "Exclude categories/terms by ID",
        wpdreams_setval_or_getoption($sd, "return_terms_exclude", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Comma "," separated list of category/term IDs.</p>
</div>