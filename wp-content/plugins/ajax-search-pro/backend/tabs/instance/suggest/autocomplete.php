<div class="item"><?php
    $o = new wpdreamsYesNo("autocomplete", "Turn on search autocomplete?", setval_or_getoption($sd, 'autocomplete', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsDraggable("autocomplete_source", "Keyword suggestion sources", array(
        'selects'=>$sugg_select_arr,
        'value'=>wpdreams_setval_or_getoption($sd, 'autocomplete_source', $_dk),
        'description'=>'Select which sources you prefer for autocomplete. Order counts.'
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsTextSmall("autocomplete_length", "Max. suggestion length",
        wpdreams_setval_or_getoption($sd, 'autocomplete_length', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">The length of each suggestion in characters. 30-60 is a good number to avoid too long suggestions.</p>
</div>
<div class="item"><?php
    $o = new wpdreamsLanguageSelect("autocomplete_google_lang", "Google autocomplete suggestions language",
        wpdreams_setval_or_getoption($sd, 'autocomplete_google_lang', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextarea("autocompleteexceptions", "Keyword exceptions (comma separated)", wpdreams_setval_or_getoption($sd, 'autocompleteexceptions', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>