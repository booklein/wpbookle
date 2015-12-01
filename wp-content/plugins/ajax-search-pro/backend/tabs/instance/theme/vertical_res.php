<div class="item">
    <?php
    $o = new wpdreamsBorder("resultsborder", "Results box border", wpdreams_setval_or_getoption($sd, 'resultsborder', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsBoxShadow("resultshadow", "Results box Shadow", wpdreams_setval_or_getoption($sd, 'resultshadow', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    /*$o = new wpdreamsNumericUnit("resultitemheight", "One result item height", array(
        'value' => wpdreams_setval_or_getoption($sd, 'resultitemheight', $_dk),
        'units'=>array('px'=>'px')));*/
    $o = new wpdreamsTextSmall("resultitemheight", "One result item height", wpdreams_setval_or_getoption($sd, 'resultitemheight', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Use with units (70px or 50% or auto). Default: <strong>auto</strong></p>
</div>
<div class="item"><?php
	$o = new wpdreamsTextSmall("itemscount", "Results box viewport (in item numbers)", wpdreams_setval_or_getoption($sd, 'itemscount', $_dk), array(array("func" => "ctype_digit", "op" => "eq", "val" => true)));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("resultsbackground","Results box background color", wpdreams_setval_or_getoption($sd, 'resultsbackground', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("resultscontainerbackground","Result items container box background color", wpdreams_setval_or_getoption($sd, 'resultscontainerbackground', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsGradient("vresulthbg", "Result item mouse hover box background gradient", wpdreams_setval_or_getoption($sd, 'vresulthbg', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("spacercolor","Spacer color between results", wpdreams_setval_or_getoption($sd, 'spacercolor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("arrowcolor","Resultbar arrow color", wpdreams_setval_or_getoption($sd, 'arrowcolor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("overflowcolor","Resultbar overflow color", wpdreams_setval_or_getoption($sd, 'overflowcolor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>