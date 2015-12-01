<ul id="subtabs"  class='tabs'>
    <li><a tabid="301" class='subtheme current'>General</a></li>
    <li><a tabid="302" class='subtheme'>Categories & Taxonomies</a></li>
    <li><a tabid="303" class='subtheme'>Custom Fields</a></li>
    <li><a tabid="304" class='subtheme'>Advanced</a></li>
    <li><a tabid="305" class='subtheme'>Suggested keywords</a></li>
</ul>
<div class='tabscontent'>
    <div tabid="301">

            <?php include(ASP_PATH."backend/tabs/instance/frontend/general.php"); ?>

    </div>
    <div tabid="302">

            <?php include(ASP_PATH."backend/tabs/instance/frontend/categories_taxonomies.php"); ?>

    </div>
    <div tabid="303">

            <?php include(ASP_PATH."backend/tabs/instance/frontend/custom_fields.php"); ?>

    </div>
    <div tabid="304">

            <?php include(ASP_PATH."backend/tabs/instance/frontend/advanced.php"); ?>

    </div>
    <div tabid="305">

        <?php include(ASP_PATH."backend/tabs/instance/frontend/suggestions.php"); ?>

    </div>
</div>
<div class="item">
    <input name="submit_<?php echo $search['id']; ?>" type="submit" value="Save all tabs!" />
</div>