<div class='item'>
    <p class='infoMsg'>
        These options work with the <b>Regular Engine</b>. If you are using the Index table engine, you can adjust the values on the Index Table panel on this page.
    </p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("userelevance", "Use relevance?", wpdreams_setval_or_getoption($sd, 'userelevance', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<fieldset>
    <legend>Exact matches weight</legend>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("etitleweight", "Title weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'etitleweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("econtentweight", "Content weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'econtentweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("eexcerptweight", "Excerpt weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'eexcerptweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("etermsweight", "Terms weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'etermsweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
</fieldset>
<fieldset>
    <legend>Random matches weight</legend>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("titleweight", "Title weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'titleweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("contentweight", "Content weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'contentweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("excerptweight", "Excerpt weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'excerptweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsCustomSelect("termsweight", "Terms weight", array('selects' => wpdreams_setval_or_getoption($sd, 'weight_def', $_dk), 'value' => wpdreams_setval_or_getoption($sd, 'termsweight', $_dk)));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
</fieldset>