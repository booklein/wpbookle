<div class="item">
    <?php
    $o = new wpdreamsYesNo("keywordsuggestions", "Keyword suggestions on no results?",
        wpdreams_setval_or_getoption($sd, 'keywordsuggestions', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsDraggable("keyword_suggestion_source", "Keyword suggestion sources", array(
        'selects'=> $sugg_select_arr,
        'value'=>wpdreams_setval_or_getoption($sd, 'keyword_suggestion_source', $_dk),
        'description'=>'Select which sources you prefer for keyword suggestions. Order counts.'
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsTextSmall("keyword_suggestion_count", "Max. suggestion count",
        wpdreams_setval_or_getoption($sd, 'keyword_suggestion_count', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">The number of possible suggestions.</p>
</div>
<div class="item"><?php
    $o = new wpdreamsTextSmall("keyword_suggestion_length", "Max. suggestion length",
        wpdreams_setval_or_getoption($sd, 'keyword_suggestion_length', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">The length of each suggestion in characters. 30-50 is a good number to avoid too long suggestions.</p>
</div>
<div class="item"><?php
    $o = new wpdreamsLanguageSelect("keywordsuggestionslang", "Google keyword suggestions language",
        wpdreams_setval_or_getoption($sd, 'keywordsuggestionslang', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>