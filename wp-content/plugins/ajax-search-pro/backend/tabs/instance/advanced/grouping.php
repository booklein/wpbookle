<div class="item">
	<?php
	$o = new wpdreamsCustomSelect("groupby", "Group results by", array(
		'selects'=>wpdreams_setval_or_getoption($sd, 'groupby_def', $_dk),
		'value'=>wpdreams_setval_or_getoption($sd, 'groupby', $_dk)) );
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("group_exclude_duplicates", "Display duplicates only in the first group match?", wpdreams_setval_or_getoption($sd, 'group_exclude_duplicates', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg">For example posts in multiple categories will be displayed in the first matching category only.</p>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("groupbytext", "Group by default text (%GROUP% is changed into the current cateogry/post type name)", wpdreams_setval_or_getoption($sd, 'groupbytext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("bbpressreplytext", "BuddyPress activity results group default text", wpdreams_setval_or_getoption($sd, 'bbpressreplytext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("bbpressgroupstext", "BuddyPress group results group default text", wpdreams_setval_or_getoption($sd, 'bbpressgroupstext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("bbpressuserstext", "User results group default text", wpdreams_setval_or_getoption($sd, 'bbpressuserstext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("commentstext", "Comments results group default text", wpdreams_setval_or_getoption($sd, 'commentstext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("term_group_text", "Term group default text", wpdreams_setval_or_getoption($sd, 'term_group_text', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("attachment_group_text", "Attachment group header text", wpdreams_setval_or_getoption($sd, 'attachment_group_text', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsText("uncategorizedtext", "Uncategorized group text", wpdreams_setval_or_getoption($sd, 'uncategorizedtext', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("showpostnumber", "Show Post Numbering", wpdreams_setval_or_getoption($sd, 'showpostnumber', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsYesNo("wpml_compatibility", "WPML compatibility", wpdreams_setval_or_getoption($sd, 'wpml_compatibility', $_dk));
	$params[$o->getName()] = $o->getData();
	?>
</div>