<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_in_bp_activities", "Search in buddypress activities?",
        wpdreams_setval_or_getoption($sd, 'search_in_bp_activities', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_in_bp_groups", "Search in buddypress groups?",
        wpdreams_setval_or_getoption($sd, 'search_in_bp_groups', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_in_bp_groups_public", "Search in public groups?",
        wpdreams_setval_or_getoption($sd, 'search_in_bp_groups_public', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_in_bp_groups_private", "Search in private groups?",
        wpdreams_setval_or_getoption($sd, 'search_in_bp_groups_private', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_in_bp_groups_hidden", "Search in hidden groups?",
        wpdreams_setval_or_getoption($sd, 'search_in_bp_groups_hidden', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
