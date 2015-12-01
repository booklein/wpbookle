<div class="item"><?php
    $o = new wpdreamsCustomSelect("cf_logic", "Custom Fields connection Logic",
        array(
            'selects' => wpdreams_setval_or_getoption($sd, 'cf_logic_def', $_dk),
            'value' => wpdreams_setval_or_getoption($sd, 'cf_logic', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $fields = wpdreams_setval_or_getoption($sd, 'field_order', $_dk);
    if (strpos($fields, "general") === false) $fields = "general|" . $fields;
    $o = new wpdreamsSortable("field_order", "Field order",
        $fields);
    $params[$o->getName()] = $o->getData();
    ?>
</div>