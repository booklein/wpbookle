<fieldset>
    <legend>Desktop browsers</legend>
    <div class="item item-flex-nogrow" style="flex-wrap: wrap;">
        <?php
        $o = new wpdreamsCustomSelect("sett_box_animation", "Settings drop-down box animation", array(
            'selects'=>array(
                array('option' => 'None', 'value' => 'none'),
                array('option' => 'Fade', 'value' => 'fade'),
                array('option' => 'Fade and Drop', 'value' => 'fadedrop')
            ),
            'value'=>wpdreams_setval_or_getoption($sd, 'sett_box_animation', $_dk)) );
        $params[$o->getName()] = $o->getData();
        ?>
        <?php
        $o = new wpdreamsTextSmall("sett_box_animation_duration", ".. animation duration (ms)",
            wpdreams_setval_or_getoption($sd, 'sett_box_animation_duration', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg" style="min-width: 100%;flex-wrap: wrap;flex-basis: auto;flex-grow: 1;box-sizing: border-box;">
            The animation of the appearing settings box when clicking on the settings icon.
        </div>
    </div>
    <div class="item item-flex-nogrow" style="flex-wrap: wrap;">
        <?php
        $o = new wpdreamsCustomSelect("res_box_animation", "Results container box animation", array(
            'selects'=>array(
                array('option' => 'None', 'value' => 'none'),
                array('option' => 'Fade', 'value' => 'fade'),
                array('option' => 'Fade and Drop', 'value' => 'fadedrop')
            ),
            'value'=>wpdreams_setval_or_getoption($sd, 'res_box_animation', $_dk)) );
        $params[$o->getName()] = $o->getData();
        ?>
        <?php
        $o = new wpdreamsTextSmall("res_box_animation_duration", ".. animation duration (ms)",
            wpdreams_setval_or_getoption($sd, 'res_box_animation_duration', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg" style="min-width: 100%;flex-wrap: wrap;flex-basis: auto;flex-grow: 1;box-sizing: border-box;">
            The animation of the appearing results box when finishing the search.
        </div>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsAnimations("res_items_animation", "Result items animation", wpdreams_setval_or_getoption($sd, 'res_items_animation', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg">
            The animation of each result when the results box is opening.
        </div>
    </div>
</fieldset>
<fieldset>
    <legend>Mobile browsers</legend>
    <div class="item item-flex-nogrow" style="flex-wrap: wrap;">
        <?php
        $o = new wpdreamsCustomSelect("sett_box_animation_m", "Settings drop-down box animation", array(
            'selects'=>array(
                array('option' => 'None', 'value' => 'none'),
                array('option' => 'Fade', 'value' => 'fade'),
                array('option' => 'Fade and Drop', 'value' => 'fadedrop')
            ),
            'value'=>wpdreams_setval_or_getoption($sd, 'sett_box_animation_m', $_dk)) );
        $params[$o->getName()] = $o->getData();
        ?>
        <?php
        $o = new wpdreamsTextSmall("sett_box_animation_duration_m", ".. animation duration (ms)",
            wpdreams_setval_or_getoption($sd, 'sett_box_animation_duration_m', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg" style="min-width: 100%;flex-wrap: wrap;flex-basis: auto;flex-grow: 1;box-sizing: border-box;">
            The animation of the appearing settings box when clicking on the settings icon.
        </div>
    </div>
    <div class="item item-flex-nogrow" style="flex-wrap: wrap;">
        <?php
        $o = new wpdreamsCustomSelect("res_box_animation_m", "Results container box animation", array(
            'selects'=>array(
                array('option' => 'None', 'value' => 'none'),
                array('option' => 'Fade', 'value' => 'fade'),
                array('option' => 'Fade and Drop', 'value' => 'fadedrop')
            ),
            'value'=>wpdreams_setval_or_getoption($sd, 'res_box_animation_m', $_dk)) );
        $params[$o->getName()] = $o->getData();
        ?>
        <?php
        $o = new wpdreamsTextSmall("res_box_animation_duration_m", ".. animation duration (ms)",
            wpdreams_setval_or_getoption($sd, 'res_box_animation_duration_m', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg" style="min-width: 100%;flex-wrap: wrap;flex-basis: auto;flex-grow: 1;box-sizing: border-box;">
            The animation of the appearing results box when finishing the search.
        </div>
    </div>
    <div class="item">
        <?php
        $o = new wpdreamsAnimations("res_items_animation_m", "Result items animation", wpdreams_setval_or_getoption($sd, 'res_items_animation_m', $_dk));
        $params[$o->getName()] = $o->getData();
        ?>
        <div class="descMsg">
            The animation of each result when the results box is opening.
        </div>
    </div>
</fieldset>