<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");
?>
<form name='options'>
    <?php
    $fields = w_isset_def($style['field_order'], 'general|custom_post_types|custom_fields|categories_terms');
    if (strpos($fields, "general") === false) $fields = "general|" . $fields;
    $field_order = explode( '|', $fields );
    foreach ($field_order as $field)
        include("asp.shortcode.$field.php");
    ?>
</form>