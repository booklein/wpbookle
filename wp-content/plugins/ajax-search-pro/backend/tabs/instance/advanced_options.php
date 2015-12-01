<ul id="subtabs"  class='tabs'>
	<li><a tabid="701" class='subtheme current'>Content</a></li>
	<li><a tabid="702" class='subtheme'>Grouping</a></li>
    <li><a tabid="703" class='subtheme'>Animations</a></li>
</ul>
<div class='tabscontent'>
	<div tabid="701">
		<fieldset>
			<legend>Content</legend>
			<?php include(ASP_PATH."backend/tabs/instance/advanced/content.php"); ?>
		</fieldset>
	</div>
	<div tabid="702">
		<fieldset>
			<legend>Grouping</legend>
			<?php include(ASP_PATH."backend/tabs/instance/advanced/grouping.php"); ?>
		</fieldset>
	</div>
    <div tabid="703">
        <fieldset>
            <legend>Animations</legend>
            <?php include(ASP_PATH."backend/tabs/instance/advanced/animations.php"); ?>
        </fieldset>
    </div>
</div>
<div class="item">
    <input type="hidden" name='asp_submit' value=1 />
    <input name="submit_<?php echo $search['id']; ?>" type="submit" value="Save this search!" />
</div>