<?php
global $porto_settings, $porto_layout, $porto_sidebar;
?>
<div class="category-filter">
    <div class="filter-toggle"><i class="fa fa-filter"></i></div>
    <div class="filter-content">
        <?php
        do_action('porto_before_sidebar');
        dynamic_sidebar( $porto_sidebar );
        do_action('porto_after_sidebar');
        ?>
    </div>
</div>
