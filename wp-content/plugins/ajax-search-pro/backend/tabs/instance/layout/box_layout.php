<div class="item">
    <?php
    $o = new wpdreamsYesNo("box_compact_layout", "Compact layout mode", wpdreams_setval_or_getoption($sd, 'box_compact_layout', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">In compact layout only the search magnifier is visible, and the user has to click on the magnifier first to show the search bar.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("box_compact_close_on_magn", "Close on magnifier click", wpdreams_setval_or_getoption($sd, 'box_compact_close_on_magn', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Closes the box when the magnifier is clicked.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("box_compact_close_on_document", "Close on document click", wpdreams_setval_or_getoption($sd, 'box_compact_close_on_document', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Closes the box when the document is clicked.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("box_compact_width", "Compact layout final width", wpdreams_setval_or_getoption($sd, 'box_compact_width', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Use with units (10px or 50% or auto). Default: <strong>100%</strong><br>
    You might need to adjust this to a static value like 200px, as 100% is not always working in compact mode.
    </p>
</div>
<div class="item item-flex-nogrow" style="flex-wrap: wrap;">
    <?php
    $o = new wpdreamsYesNo("box_compact_overlay", "Display background overlay?", wpdreams_setval_or_getoption($sd, 'box_compact_overlay', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <?php
    $o = new wpdreamsColorPicker("box_compact_overlay_color", " color ", wpdreams_setval_or_getoption($sd, 'box_compact_overlay_color', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <div class="descMsg" style="min-width: 100%;flex-wrap: wrap;flex-basis: auto;flex-grow: 1;box-sizing: border-box;">
        Overlay only works with if the <strong>box position is set to Fixed</strong> below.
    </div>
</div>
<div class="item"><?php
    $o = new wpdreamsCustomSelect("box_compact_float", "Compact layout alignment",
        array(
            'selects' => wpdreams_setval_or_getoption($sd, 'box_compact_float_def', $_dk),
            'value' => wpdreams_setval_or_getoption($sd, 'box_compact_float', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">By default the search box floats with the theme default (none). You can change that here.</p>
</div>
<div class="item"><?php
    $o = new wpdreamsCustomSelect("box_compact_position", "Compact search box position",
        array(
            'selects' => wpdreams_setval_or_getoption($sd, 'box_compact_position_def', $_dk),
            'value' => wpdreams_setval_or_getoption($sd, 'box_compact_position', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">In absolute position the search can not affect it's parent element height as absolutely positioned elements are removed from the flow, thus ignored by other elements.</p>
</div>
<div class="item">
    <?php
    $option_name = "box_compact_screen_position";
    $option_desc = "Position values";
    $option_expl = "You can use auto or include the unit as well, example: 10px or 1em or 90%";
    $o = new wpdreamsFour($option_name, $option_desc,
        array(
            "desc" => $option_expl,
            "value" => wpdreams_setval_or_getoption($sd, $option_name, $_dk)
        )
    );
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("box_compact_position_z", "z-index", wpdreams_setval_or_getoption($sd, 'box_compact_position_z', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">
        In case you have some other elements floating above/below the search icon, you can adjust it's position with the z-index.
    </p>
</div>

