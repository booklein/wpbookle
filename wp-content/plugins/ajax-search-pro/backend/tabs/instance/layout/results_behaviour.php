<div class="item">
    <?php
    $o = new wpdreamsYesNo("results_click_blank", "Open the results in a new window?", wpdreams_setval_or_getoption($sd, 'results_click_blank', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("scroll_to_results", "Sroll the window to the result list?", wpdreams_setval_or_getoption($sd, 'scroll_to_results', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("resultareaclickable", "Make the whole result area clickable?", wpdreams_setval_or_getoption($sd, 'resultareaclickable', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("close_on_document_click", "Close result list on document click?", wpdreams_setval_or_getoption($sd, 'close_on_document_click', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("show_close_icon", "Show the close icon?", wpdreams_setval_or_getoption($sd, 'show_close_icon', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsText("noresultstext", "No results text", wpdreams_setval_or_getoption($sd, 'noresultstext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsText("didyoumeantext", "Did you mean text", wpdreams_setval_or_getoption($sd, 'didyoumeantext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsYesNo("highlight", "Highlight search text in results?", wpdreams_setval_or_getoption($sd, 'highlight', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsYesNo("highlightwholewords", "Highlight only whole words?", wpdreams_setval_or_getoption($sd, 'highlightwholewords', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("highlightcolor", "Highlight text color", wpdreams_setval_or_getoption($sd, 'highlightcolor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item"><?php
    $o = new wpdreamsColorPicker("highlightbgcolor", "Highlight-text background color", wpdreams_setval_or_getoption($sd, 'highlightbgcolor', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>