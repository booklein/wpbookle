<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("resultstype", "Results layout type", array(
        'selects'=>wpdreams_setval_or_getoption($sd, 'resultstype_def', $_dk),
        'value'=>wpdreams_setval_or_getoption($sd, 'resultstype', $_dk)
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<p class='infoMsg'>If you are using <b>Polaroid</b> layout type, then <b>block</b> position is highly recommended!</p>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("resultsposition", "Results layout position", array(
        'selects'=>wpdreams_setval_or_getoption($sd, 'resultsposition_def', $_dk),
        'value'=>wpdreams_setval_or_getoption($sd, 'resultsposition', $_dk)
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsNumericUnit("resultsmargintop", "Block layout margin top", array(
        'value' => wpdreams_setval_or_getoption($sd, 'resultsmargintop', $_dk),
        'units'=>array('px'=>'px')
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("defaultsearchtext", "Default search text", wpdreams_setval_or_getoption($sd, 'defaultsearchtext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showmoreresults", "Show 'More results..' text in the bottom of the search box?", wpdreams_setval_or_getoption($sd, 'showmoreresults', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("showmoreresultstext", "' Show more results..' text", wpdreams_setval_or_getoption($sd, 'showmoreresultstext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("more_redirect_url", "' Show more results..' url", wpdreams_setval_or_getoption($sd, 'more_redirect_url', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showauthor", "Show author in results?", wpdreams_setval_or_getoption($sd, 'showauthor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsCustomSelect("author_field", "Author field",
		array(
			'selects' => array(
				array('option' => 'Display name', 'value' => 'display_name'),
				array('option' => 'Login name', 'value' => 'user_login')
			),
			'value' => wpdreams_setval_or_getoption($sd, 'author_field', $_dk)
		));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showdate", "Show date in results?", wpdreams_setval_or_getoption($sd, 'showdate', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item item-flex-nogrow item-conditional" style="flex-wrap: wrap;">
    <?php
        $o = new wpdreamsYesNo("custom_date", "Use custom date format?",
            wpdreams_setval_or_getoption($sd, 'custom_date', $_dk));
        $params[$o->getName()] = $o->getData();
    ?>
    <?php
        $o = new wpdreamsText("custom_date_format", " format",
            wpdreams_setval_or_getoption($sd, 'custom_date_format', $_dk));
        $params[$o->getName()] = $o->getData();
    ?>
    <div class='descMsg' style="min-width: 100%;
    flex-wrap: wrap;
    flex-basis: auto;
    flex-grow: 1;
    box-sizing: border-box;">If turned OFF, it will use WordPress defaults. Default custom value: <b>Y-m-d H:i:s</b></div>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showdescription", "Show description (content) in results?", wpdreams_setval_or_getoption($sd, 'showdescription', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextSmall("descriptionlength", "Description (content) length", wpdreams_setval_or_getoption($sd, 'descriptionlength', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Content length in characters.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("description_context", "Display the description context?", wpdreams_setval_or_getoption($sd, 'description_context', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Will display the description from around the search phrase, not from the beginning.</p>
</div>