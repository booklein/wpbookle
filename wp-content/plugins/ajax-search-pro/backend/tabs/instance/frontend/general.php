<div class="item">
    <?php
    $o = new wpdreamsYesNo("show_frontend_search_settings", "Show search settings switch on the frontend?", wpdreams_setval_or_getoption($sd, 'show_frontend_search_settings', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">This will hide the switch icon, so the user can't open/close the settings.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("frontend_search_settings_visible", "Set the search settings to visible by default?", wpdreams_setval_or_getoption($sd, 'frontend_search_settings_visible', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">If set to Yes, then the settings will be visible/opened by default.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("frontend_search_settings_position", "Search settings position", array(
        'selects'=>wpdreams_setval_or_getoption($sd, 'frontend_search_settings_position_def', $_dk),
        'value'=>wpdreams_setval_or_getoption($sd, 'frontend_search_settings_position', $_dk)
    ));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">The position is automatically set to Block if you use the settings shortcode.</p>
</div>
<div class="item">
    <label class="shortcode">Custom Settings position shortcode:</label>
    <input type="text" class="quick_shortcode" value="[wpdreams_asp_settings id=<?php echo $search['id']; ?>]" readonly="readonly" />
</div>
<div class="item" style="text-align:center;">
    The default values of the checkboxes on the frontend are the values set above.
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showexactmatches", "Show exact matches selector?", wpdreams_setval_or_getoption($sd, 'showexactmatches', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("exactmatchestext", "Text", wpdreams_setval_or_getoption($sd, 'exactmatchestext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchinposts", "Show search in posts selector?", wpdreams_setval_or_getoption($sd, 'showsearchinposts', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchinpoststext", "Text", wpdreams_setval_or_getoption($sd, 'searchinpoststext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchinpages", "Show search in pages selector?", wpdreams_setval_or_getoption($sd, 'showsearchinpages', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchinpagestext", "Text", wpdreams_setval_or_getoption($sd, 'searchinpagestext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchintitle", "Show search in title selector?", wpdreams_setval_or_getoption($sd, 'showsearchintitle', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchintitletext", "Text", wpdreams_setval_or_getoption($sd, 'searchintitletext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchincontent", "Show search in content selector?", wpdreams_setval_or_getoption($sd, 'showsearchincontent', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchincontenttext", "Text", wpdreams_setval_or_getoption($sd, 'searchincontenttext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchincomments", "Show search in comments selector?", wpdreams_setval_or_getoption($sd, 'showsearchincomments', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchincommentstext", "Text", wpdreams_setval_or_getoption($sd, 'searchincommentstext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("showsearchinexcerpt", "Show search in excerpt selector?", wpdreams_setval_or_getoption($sd, 'showsearchinexcerpt', $_dk));
    $params[$o->getName()] = $o->getData();
    $o = new wpdreamsText("searchinexcerpttext", "Text", wpdreams_setval_or_getoption($sd, 'searchinexcerpttext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("custom_types_label", "Custom post types label text", wpdreams_setval_or_getoption($sd, 'custom_types_label', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsCustomPostTypesEditable("showcustomtypes", "Show search in custom post types selectors", wpdreams_setval_or_getoption($sd, 'showcustomtypes', $_dk));
    $params[$o->getName()] = $o->getData();
    $params['selected-' . $o->getName()] = $o->getSelected();
    ?>
</div>