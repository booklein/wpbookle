<div class="item">
	<?php
	$o = new wpdreamsYesNo("display_search_text", "Display the search text button?",
		wpdreams_setval_or_getoption($sd, 'display_search_text', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("hide_magnifier", "Hide the magnifier icon?",
		wpdreams_setval_or_getoption($sd, 'hide_magnifier', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("search_text", "Button text",
		wpdreams_setval_or_getoption($sd, 'search_text', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsCustomSelect("search_text_position", "Button position", array(
		'selects'=>array(
			array('option' => 'Left to the magnifier', 'value' => "left"),
			array('option' => 'Right to the magnifier', 'value' => "right")
		),
		'value'=>wpdreams_setval_or_getoption($sd, 'search_text_position', $_dk)) );
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item"><?php
	$o = new wpdreamsFontComplete("search_text_font", "Button font", wpdreams_setval_or_getoption($sd, 'search_text_font', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>