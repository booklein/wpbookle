<div class='item'>
    <p class='infoMsg'>
        These options work with the <b>Index Table Engine</b>. If you are using the Regular engine, you can adjust the values on the Regular Engine panel on this page.
    </p>
</div>
<fieldset>
    <legend>Matches weight</legend>
    <p class='infoMsg'>
        Please use numbers between <b>0 - 100</b>
    </p>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_title_weight", "Title weight", wpdreams_setval_or_getoption($sd, 'it_title_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_content_weight", "Content weight", wpdreams_setval_or_getoption($sd, 'it_content_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_excerpt_weight", "Excerpt weight", wpdreams_setval_or_getoption($sd, 'it_excerpt_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_terms_weight", "Terms weight", wpdreams_setval_or_getoption($sd, 'it_terms_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_cf_weight", "Custom fields weight", wpdreams_setval_or_getoption($sd, 'it_cf_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsTextSmall("it_author_weight", "Author weight", wpdreams_setval_or_getoption($sd, 'it_author_weight', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
    </div>
</fieldset>