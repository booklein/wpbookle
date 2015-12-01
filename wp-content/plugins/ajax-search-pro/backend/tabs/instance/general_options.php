<ul id="subtabs"  class='tabs'>
    <li><a tabid="101" class='subtheme current'>Sources</a></li>
    <li><a tabid="105" class='subtheme'>Sources 2</a></li>
	<li><a tabid="109" class='subtheme'>Attachments</a></li>
	<li><a tabid="108" class='subtheme'>User Search</a></li>
    <li><a tabid="102" class='subtheme'>Behavior</a></li>
    <li><a tabid="103" class='subtheme'>Image Options</a></li>
    <li><a tabid="104" class='subtheme'>BuddyPress</a></li>
    <li><a tabid="107" class='subtheme'>Ordering</a></li>
    <li><a tabid="106" class='subtheme'>AAPL options</a></li>

</ul>
<div class='tabscontent'>
    <div tabid="101">
        <fieldset>
            <legend>Output options</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/sources.php"); ?>
        </fieldset>
    </div>
    <div tabid="102">
        <fieldset>
            <legend>Basic Behavior</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/behaviour.php"); ?>
        </fieldset>
    </div>
    <div tabid="103">
        <fieldset>
            <legend>Image Options</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/image_options.php"); ?>
        </fieldset>
    </div>
    <div tabid="104">
        <fieldset>
            <legend>BuddyPress Options</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/buddypress_options.php"); ?>
        </fieldset>
    </div>
    <div tabid="108">
        <fieldset>
            <legend>User Search</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/user_search.php"); ?>
        </fieldset>
    </div>
    <div tabid="105">
        <fieldset>
            <legend>Sources 2</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/sources2.php"); ?>
        </fieldset>
    </div>
    <div tabid="107">
        <fieldset>
            <legend>Ordering</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/ordering.php"); ?>
        </fieldset>
    </div>
    <div tabid="106">
        <fieldset>
            <legend>Advanced Ajax Page Loader options</legend>
            <?php include(ASP_PATH."backend/tabs/instance/general/ajax_page_loader.php"); ?>
        </fieldset>
    </div>
	<div tabid="109">
		<fieldset>
			<legend>Attachment Search</legend>
			<?php include(ASP_PATH."backend/tabs/instance/general/attachment_results.php"); ?>
		</fieldset>
	</div>
</div>
<div class="item">
    <input name="submit_<?php echo $search['id']; ?>" type="submit" value="Save all tabs!" />
</div>