<div class="item">
	<?php
	$o = new wpdreamsYesNo("return_attachments", "Return attachments as results?",
		wpdreams_setval_or_getoption($sd, "return_attachments", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("search_attachments_title", "Search in attachment titles?",
		wpdreams_setval_or_getoption($sd, "search_attachments_title", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("search_attachments_content", "Search in attachment content?",
		wpdreams_setval_or_getoption($sd, "search_attachments_content", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_attachments_caption", "Search in attachment captions?",
        wpdreams_setval_or_getoption($sd, "search_attachments_caption", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("search_attachments_terms", "Search in attachment terms (tags, etc..)?",
        wpdreams_setval_or_getoption($sd, "search_attachments_terms", $_dk));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">Will search in terms (categories, tags) related to the attachments.</p>
    <p class="errorMsg">WARNING: <strong>Search in terms</strong> can be database heavy operation. Not recommended for big databases.</p>
</div>
<div class="item">
	<?php
	$o = new wpdreamsTextareaBase64("attachment_mime_types", "Allowed mime types",
		wpdreams_setval_or_getoption($sd, "attachment_mime_types", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg"><strong>Comma separated list</strong> of allowed mime types. List of <a href="https://codex.wordpress.org/Function_Reference/get_allowed_mime_types"
	target="_blank">default allowed mime types</a> in WordPress.</p>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("attachment_use_image", "Use the image of image mime types as the result image?",
		wpdreams_setval_or_getoption($sd, "attachment_use_image", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsTextarea("attachment_exclude", "Exclude attachment IDs",
		wpdreams_setval_or_getoption($sd, "attachment_exclude", $_dk));
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg"><strong>Comma separated list</strong> of attachment IDs to exclude.</p>
</div>