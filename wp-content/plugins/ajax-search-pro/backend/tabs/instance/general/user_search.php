<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_search", "Enable search in users?",
        wpdreams_setval_or_getoption($sd, 'user_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_login_search", "Search in user login names?",
        wpdreams_setval_or_getoption($sd, 'user_login_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_display_name_search", "Search in user display names?",
        wpdreams_setval_or_getoption($sd, 'user_display_name_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_first_name_search", "Search in user first names?",
        wpdreams_setval_or_getoption($sd, 'user_first_name_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_last_name_search", "Search in user last names?",
        wpdreams_setval_or_getoption($sd, 'user_last_name_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_bio_search", "Search in user bio?",
        wpdreams_setval_or_getoption($sd, 'user_bio_search', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsUserRoleSelect("user_search_exclude_roles", "User roles exclude",
        wpdreams_setval_or_getoption($sd, 'user_search_exclude_roles', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_search_display_images", "Display user images?",
        wpdreams_setval_or_getoption($sd, 'user_search_display_images', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("user_search_image_source", "Image source",
        array(
            'selects' => array(
                array('option' => 'Default', 'value' => 'default'),
                array('option' => 'BuddyPress avatar', 'value' => 'buddypress')
            ),
            'value' => wpdreams_setval_or_getoption($sd, 'user_search_image_source', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextarea("user_search_meta_fields", "Search in following user meta fields",
        wpdreams_setval_or_getoption($sd, 'user_search_meta_fields', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Comma separated list, like: user_meta1, user_meta2</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("user_search_title_field", "Title field",
        array(
            'selects' => wpdreams_setval_or_getoption($sd, 'user_search_title_field_def', $_dk),
            'value' => wpdreams_setval_or_getoption($sd, 'user_search_title_field', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("user_search_description_field", "Description field",
        array(
            'selects' => wpdreams_setval_or_getoption($sd, 'user_search_description_field_def', $_dk),
            'value' => wpdreams_setval_or_getoption($sd, 'user_search_description_field', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("user_search_advanced_title_field", "Advanced title field",
        wpdreams_setval_or_getoption($sd, 'user_search_advanced_title_field', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Variable {titlefield} will be replaced with the Title field value. Use the format {meta_field} to get user meta.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("user_search_advanced_description_field", "Advanced description field",
        wpdreams_setval_or_getoption($sd, 'user_search_advanced_description_field', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Variable {descriptionfield} will be replaced with the Description field value. Use the format {meta_field} to get user meta.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("user_search_redirect_to_custom_url", "Redirect to custom url when clicking on a result?",
        wpdreams_setval_or_getoption($sd, 'user_search_redirect_to_custom_url', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("user_search_url_source", "Result url source",
        array(
            'selects' => array(
                array('option' => 'Default', 'value' => 'default'),
                array('option' => 'BuddyPress profile', 'value' => 'bp_profile'),
                array('option' => 'Custom scheme', 'value' => 'custom')
            ),
            'value' => wpdreams_setval_or_getoption($sd, 'user_search_url_source', $_dk)
        ));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">This is the result URL destination. By default it's the author profile link.</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsText("user_search_custom_url", "Custom url scheme",
        wpdreams_setval_or_getoption($sd, 'user_search_custom_url', $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">You can use these variables: {USER_ID}, {USER_LOGIN}, {USER_NICENAME}, {USER_DISPLAYNAME}</p>
</div>