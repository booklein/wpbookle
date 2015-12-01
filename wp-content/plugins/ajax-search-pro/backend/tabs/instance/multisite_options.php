<div class='item'>
    <p class='infoMsg'>
        If you not choose any site, then the <strong>currently active</strong> blog will be used!<br />
        Also, you can use the same search on multiple blogs!
    </p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("searchinblogtitles", "Search in blog titles?",
         wpdreams_setval_or_getoption($sd, "searchinblogtitles", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsCustomSelect("blogtitleorderby", "Result ordering", array(
        'selects'=> wpdreams_setval_or_getoption($sd, 'blogtitleorderby_def', $_dk),
        'value'=> wpdreams_setval_or_getoption($sd, 'blogtitleorderby', $_dk)
    ) );
    $params[$o->getName()] = $o->getData();
    ?></div>
<div class="item">
    <?php
    $o = new wpdreamsText("blogresultstext", "Blog results group default text",
         wpdreams_setval_or_getoption($sd, 'blogresultstext', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item"><?php
    $o = new wpdreamsBlogselect("blogs", "Blogs",
         wpdreams_setval_or_getoption($sd, 'blogs', $_dk));
    $params[$o->getName()] = $o->getData();
    $params['selected-'.$o->getName()] = $o->getSelected();
    ?>
</div>
<div class="item">
    <input name="submit_<?php echo $search['id']; ?>" type="submit" value="Save all tabs!" />
</div>