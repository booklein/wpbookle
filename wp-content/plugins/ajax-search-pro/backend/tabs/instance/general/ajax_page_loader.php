<div class='item'>
    <p class='infoMsg'>
        These options will only work, if you have the <strong><a href="https://wordpress.org/plugins/advanced-ajax-page-loader/">Advanced Ajax Page Loader</a></strong> plugin installed<br />
        and <strong>properly configured!</strong>
    </p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("apl_on_result_click", "Use ajax page loader on result click?",
        wpdreams_setval_or_getoption($sd, 'apl_on_result_click', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("apl_on_magnifier_click", "Use ajax page loader on magnifier click?",
        wpdreams_setval_or_getoption($sd, 'apl_on_magnifier_click', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("apl_on_enter", "Use ajax page loader on return button?",
        wpdreams_setval_or_getoption($sd, 'apl_on_enter', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("apl_on_typing", "Use ajax page loader on typing?",
        wpdreams_setval_or_getoption($sd, 'apl_on_typing', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>