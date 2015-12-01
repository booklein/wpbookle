<?php
$options = array();

/* Performance Tracking options */
$options['asp_performance_def']['enabled'] = 1;

/* Index table options */
$options['asp_it_def'] = array(
	'it_index_title' => 1,
	'it_index_content' => 1,
	'it_index_excerpt' => 1,
	'it_post_types' => 'post|page',
	'it_index_tags' => 0,
	'it_index_categories' => 0,
	'it_index_taxonomies' => '',
	'it_index_customfields' => '',
	'it_post_statuses' => 'publish',
	'it_index_author_name' => 0,
	'it_index_author_bio' => 0,
    'it_replace_dash_like' => 0,
	'it_blog_ids' => '',
	'it_limit' => 25,
	'it_use_stopwords' => 1,
	'it_stopwords' => @file_get_contents(ASP_PATH . '/stopwords.txt'),
	'it_min_word_length' => 3,
	'it_extract_shortcodes' => 1,
	'it_exclude_shortcodes' => 'wpdreams_rpl, wpdreams_rpp',
    'it_cron_enable'        => 0,
    'it_cron_period'        => "hourly"
);

/* Analytics options */
$options['asp_analytics_def']['analytics'] = 0;
$options['asp_analytics_def']['analytics_string'] = "ajax_search-{asp_term}";


/* Default caching options */
$options['asp_caching_def']['caching'] = 0;
$options['asp_caching_def']['image_cropping'] = 0;
$options['asp_caching_def']['cachinginterval'] = 1440;
$options['asp_caching_def']['use_bfi_thumb'] = 1;


/* Compatibility Options */

// CSS and JS
$options['asp_compatibility_def']['jsminified'] = 1;
$options['asp_compatibility_def']['js_source'] = "min-scoped";
$options['asp_compatibility_def']['js_source_def'] = array(
    array('option' => 'Non minified', 'value' => 'nomin'),
    array('option' => 'Minified', 'value' => 'min'),
    array('option' => 'Non-minified scoped', 'value' => 'nomin-scoped'),
    array('option' => 'Minified scoped', 'value' => 'min-scoped'),
);
$options['asp_compatibility_def']['load_in_footer'] = 1;
$options['asp_compatibility_def']['css_compatibility_level'] = "medium";
$options['asp_compatibility_def']['forceinlinestyles'] = 0;
$options['asp_compatibility_def']['css_async_load'] = 0;
$options['asp_compatibility_def']['loadpolaroidjs'] = 1;
$options['asp_compatibility_def']['load_isotope_js'] = 1;
$options['asp_compatibility_def']['load_noui_js'] = 1;
$options['asp_compatibility_def']['usetimbthumb'] = 1;
$options['asp_compatibility_def']['usecustomajaxhandler'] = 0;

// Query options
$options['asp_compatibility_def']['db_force_case_selects'] = array(
    array('option' => 'None', 'value' => 'none'),
    array('option' => 'Sensitivity', 'value' => 'sensitivity'),
    array('option' => 'InSensitivity', 'value' => 'insensitivity')
);
$options['asp_compatibility_def']['db_force_case'] = 'none';
$options['asp_compatibility_def']['db_force_unicode'] = 0;
$options['asp_compatibility_def']['db_force_utf8_like'] = 0;


/* Fulltext search options */
$options['asp_fulltexto_def']['dbusefulltext'] = 0;
$options['asp_fulltexto_def']['dbuseregularwhenshort'] = 0;



/* Default new search options */

// General options
$options['asp_defaults']['search_engine'] = 'regular';
$options['asp_defaults']['triggeronclick'] = 1;
$options['asp_defaults']['triggeronreturn'] = 1;
$options['asp_defaults']['trigger_on_facet'] = 0;
$options['asp_defaults']['redirectonclick'] = 0;
$options['asp_defaults']['redirect_click_to'] = 'results_page';
$options['asp_defaults']['redirect_on_enter'] = 0;
$options['asp_defaults']['redirect_enter_to'] = 'results_page';
$options['asp_defaults']['redirect_url'] = '?s={phrase}';
$options['asp_defaults']['override_default_results'] = 0;
$options['asp_defaults']['triggerontype'] = 1;
$options['asp_defaults']['searchinposts'] = 1;
$options['asp_defaults']['searchinpages'] = 1;
$options['asp_defaults']['customtypes'] = "";
$options['asp_defaults']['searchinproducts'] = 1;
$options['asp_defaults']['searchintitle'] = 1;
$options['asp_defaults']['searchincontent'] = 1;
$options['asp_defaults']['searchincomments'] = 0;
$options['asp_defaults']['searchinexcerpt'] = 1;
$options['asp_defaults']['customfields'] = "";
$options['asp_defaults']['searchinbpusers'] = 0;
$options['asp_defaults']['searchinbpgroups'] = 0;
$options['asp_defaults']['searchinbpforums'] = 0;
$options['asp_defaults']['searchindrafts'] = 0;
$options['asp_defaults']['searchinpending'] = 0;
$options['asp_defaults']['exactonly'] = 0;
$options['asp_defaults']['searchinterms'] = 0;

// General/Sources 2
$options['asp_defaults']['return_categories'] = 0;
$options['asp_defaults']['return_tags'] = 0;
$options['asp_defaults']['return_terms'] = '';
$options['asp_defaults']['display_number_posts_affected'] = 0;
$options['asp_defaults']['return_terms_exclude'] = '';

// General / Attachments
$options['asp_defaults']['return_attachments'] = 0;
$options['asp_defaults']['search_attachments_title'] = 1;
$options['asp_defaults']['search_attachments_content'] = 1;
$options['asp_defaults']['search_attachments_caption'] = 1;
$options['asp_defaults']['search_attachments_terms'] = 0;
// base64: image/jpeg, image/gif, image/png, image/tiff, image/x-icon
$options['asp_defaults']['attachment_mime_types'] = 'aW1hZ2UvanBlZywgaW1hZ2UvZ2lmLCBpbWFnZS9wbmcsIGltYWdlL3RpZmYsIGltYWdlL3gtaWNvbg==';
$options['asp_defaults']['attachment_use_image'] = 1;
$options['asp_defaults']['attachment_exclude'] = "";

// General / Ordering
$options['asp_defaults']['results_order'] = 'terms|blogs|bp_activities|comments|bp_groups|bp_users|post_page_cpt|attachments';

// General/Behaviour
$options['asp_defaults']['charcount'] = 3;
$options['asp_defaults']['maxresults'] = 10;
$options['asp_defaults']['itemscount'] = 4;
$options['asp_defaults']['resultitemheight'] = "auto";

// General/AAPL
$options['asp_defaults']['apl_on_result_click'] = 0;
$options['asp_defaults']['apl_on_magnifier_click'] = 0;
$options['asp_defaults']['apl_on_enter'] = 0;
$options['asp_defaults']['apl_on_typing'] = 0;

$options['asp_defaults']['keyword_logic_def'] = array(
    array('option' => 'OR', 'value' => 'or'),
    array('option' => 'OR with exact word matches', 'value' => 'orex'),
    array('option' => 'AND', 'value' => 'and'),
    array('option' => 'AND with exact word matches', 'value' => 'andex')
);
$options['asp_defaults']['keyword_logic'] = 'or';

$options['asp_defaults']['term_logic_def'] = array(
    array('option' => 'OR', 'value' => 'or'),
    array('option' => 'AND', 'value' => 'and')
);
$options['asp_defaults']['term_logic'] = 'and';

/*$options['asp_defaults']['orderby_def'] = array(
    array('option' => 'Title descending', 'value' => 'post_title DESC'),
    array('option' => 'Title ascending', 'value' => 'post_title ASC'),
    array('option' => 'Date descending', 'value' => 'post_date DESC'),
    array('option' => 'Date ascending', 'value' => 'post_date ASC')
);*/
$options['asp_defaults']['orderby_primary'] = 'relevance DESC';
$options['asp_defaults']['orderby'] = 'post_date DESC';

// General/Image
$options['asp_defaults']['show_images'] = 1;
$options['asp_defaults']['image_transparency'] = 1;
$options['asp_defaults']['image_bg_color'] = "#FFFFFF";
$options['asp_defaults']['image_width'] = 70;
$options['asp_defaults']['image_height'] = 70;
$options['asp_defaults']['image_apply_content_filter'] = 0;

$options['asp_defaults']['image_crop_location'] = 'c';
$options['asp_defaults']['image_crop_location_selects'] = array(
    array('option' => 'In the center', 'value' => 'c'),
    array('option' => 'Align top', 'value' => 't'),
    array('option' => 'Align top right', 'value' => 'tr'),
    array('option' => 'Align top left', 'value' => 'tl'),
    array('option' => 'Align bottom', 'value' => 'b'),
    array('option' => 'Align bottom right', 'value' => 'br'),
    array('option' => 'Align bottom left', 'value' => 'bl'),
    array('option' => 'Align left', 'value' => 'l'),
    array('option' => 'Align right', 'value' => 'r')
);

$options['asp_defaults']['image_sources'] = array(
    array('option' => 'Featured image', 'value' => 'featured'),
    array('option' => 'Post Content', 'value' => 'content'),
    array('option' => 'Post Excerpt', 'value' => 'excerpt'),
    array('option' => 'Custom field', 'value' => 'custom'),
    array('option' => 'Page Screenshot', 'value' => 'screenshot'),
    array('option' => 'Default image', 'value' => 'default'),
    array('option' => 'Post format icon', 'value' => 'post_format'),
    array('option' => 'Disabled', 'value' => 'disabled')
);

$options['asp_defaults']['image_source1'] = 'featured';
$options['asp_defaults']['image_source2'] = 'content';
$options['asp_defaults']['image_source3'] = 'excerpt';
$options['asp_defaults']['image_source4'] = 'custom';
$options['asp_defaults']['image_source5'] = 'default';

$options['asp_defaults']['image_default'] = "";
$options['asp_defaults']['image_custom_field'] = '';
$options['asp_defaults']['use_timthumb'] = 1;

/* BuddyPress Options */
$options['asp_defaults']['search_in_bp_activities'] = 0;
$options['asp_defaults']['search_in_bp_groups'] = 0;
$options['asp_defaults']['search_in_bp_groups_public'] = 0;
$options['asp_defaults']['search_in_bp_groups_private'] = 0;
$options['asp_defaults']['search_in_bp_groups_hidden'] = 0;

/* User Search Options */
$options['asp_defaults']['user_search'] = 0;
$options['asp_defaults']['user_login_search'] = 1;
$options['asp_defaults']['user_display_name_search'] = 1;
$options['asp_defaults']['user_first_name_search'] = 1;
$options['asp_defaults']['user_last_name_search'] = 1;
$options['asp_defaults']['user_bio_search'] = 1;
$options['asp_defaults']['user_search_exclude_roles'] = "";
$options['asp_defaults']['user_search_display_images'] = 1;
$options['asp_defaults']['user_search_image_source'] = 'default';
$options['asp_defaults']['user_search_meta_fields'] = "";
$options['asp_defaults']['user_search_title_field_def'] = array(
    array('option' => 'Login Name', 'value' => 'login'),
    array('option' => 'Display Name', 'value' => 'display_name')
);
$options['asp_defaults']['user_search_title_field'] = 'display_name';
$options['asp_defaults']['user_search_description_field_def'] = array(
    array('option' => 'Biography', 'value' => 'bio'),
    array('option' => 'BuddyPress Last Activity', 'value' => 'buddypress_last_activity'),
    array('option' => 'Nothing', 'value' => 'nothing')
);
$options['asp_defaults']['user_search_description_field'] = 'bio';
$options['asp_defaults']['user_search_advanced_title_field'] = '{titlefield}';
$options['asp_defaults']['user_search_advanced_description_field'] = '{descriptionfield}';
$options['asp_defaults']['user_search_redirect_to_custom_url'] = 0;
$options['asp_defaults']['user_search_url_source'] = 'default';
$options['asp_defaults']['user_search_custom_url'] = '?author={USER_ID}';


/* Multisite Options */
$options['asp_defaults']['searchinblogtitles'] = 0;
$options['asp_defaults']['blogresultstext'] = "Blogs";

/* Frontend search settings Options */
// suggestions
$options['asp_defaults']['frontend_show_suggestions'] = 0;
$options['asp_defaults']['frontend_suggestions_text'] = "Try these:";
$options['asp_defaults']['frontend_suggestions_text_color'] = "rgb(85, 85, 85)";
$options['asp_defaults']['frontend_suggestions_keywords'] = "phrase 1, phrase 2, phrase 3";
$options['asp_defaults']['frontend_suggestions_keywords_color'] = "rgb(255, 181, 86)";

// general
$options['asp_defaults']['show_frontend_search_settings'] = 1;
$options['asp_defaults']['frontend_search_settings_visible'] = 0;
$options['asp_defaults']['frontend_search_settings_position_def'] = array(
    array('option' => 'Hovering (default)', 'value' => 'hover'),
    array('option' => 'Block or custom', 'value' => 'block')
);
$options['asp_defaults']['frontend_search_settings_position'] = 'hover';


$options['asp_defaults']['showexactmatches'] = 1;
$options['asp_defaults']['showsearchinposts'] = 1;
$options['asp_defaults']['showsearchinpages'] = 1;
$options['asp_defaults']['showsearchintitle'] = 1;
$options['asp_defaults']['showsearchincontent'] = 1;
$options['asp_defaults']['showsearchincomments'] = 1;
$options['asp_defaults']['showsearchinexcerpt'] = 1;
$options['asp_defaults']['showsearchinbpusers'] = 0;
$options['asp_defaults']['showsearchinbpgroups'] = 0;
$options['asp_defaults']['showsearchinbpforums'] = 0;

$options['asp_defaults']['exactmatchestext'] = "Exact matches only";
$options['asp_defaults']['searchinpoststext'] = "Search in posts";
$options['asp_defaults']['searchinpagestext'] = "Search in pages";
$options['asp_defaults']['searchintitletext'] = "Search in title";
$options['asp_defaults']['searchincontenttext'] = "Search in content";
$options['asp_defaults']['searchincommentstext'] = "Search in comments";
$options['asp_defaults']['searchinexcerpttext'] = "Search in excerpt";
$options['asp_defaults']['searchinbpuserstext'] = "Search in users";
$options['asp_defaults']['searchinbpgroupstext'] = "Search in groups";
$options['asp_defaults']['searchinbpforumstext'] = "Search in forums";

$options['asp_defaults']['showcustomtypes'] = '';
$options['asp_defaults']['custom_types_label'] = 'Filter by Custom Post Type';

$options['asp_defaults']['showsearchincategories'] = 1;
$options['asp_defaults']['showuncategorised'] = 1;
$options['asp_defaults']['exsearchincategories'] = "";
$options['asp_defaults']['frontend_cat_hierarchy'] = 1;
$options['asp_defaults']['frontend_cat_order'] = 'name||DESC';
$options['asp_defaults']['exsearchincategoriesheight'] = 200;
$options['asp_defaults']['showsearchintaxonomies'] = 1;
$options['asp_defaults']['showterms'] = "";
$options['asp_defaults']['frontend_term_hierarchy'] = 1;
$options['asp_defaults']['frontend_term_order'] = 'name||DESC';
$options['asp_defaults']['showseparatefilterboxes'] = 1;
$options['asp_defaults']['exsearchintaxonomiestext'] = "Filter by";
$options['asp_defaults']['exsearchincategoriestext'] = "Filter by Categories";
$options['asp_defaults']['exsearchincategoriestextfont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgb(26, 71, 98);font-size:13px;line-height:15px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';

$options['asp_defaults']['custom_field_items'] = '';
$options['asp_defaults']['cf_null_values'] = 0;
$options['asp_defaults']['cf_logic_def'] = array(
    array('option' => 'AND', 'value' => 'AND'),
    array('option' => 'OR', 'value' => 'OR')
);
$options['asp_defaults']['cf_logic'] = 'AND';
$options['asp_defaults']['field_order'] = 'general|custom_post_types|custom_fields|categories_terms';

/* Layout Options */
$options['asp_defaults']['resultstype_def'] = array(
    array('option' => 'Vertical Results', 'value' => 'vertical'),
    array('option' => 'Horizontal Results', 'value' => 'horizontal'),
    array('option' => 'Isotopic Results', 'value' => 'isotopic'),
    array('option' => 'Polaroid style Results', 'value' => 'polaroid')
);
$options['asp_defaults']['resultstype'] = 'vertical';
$options['asp_defaults']['resultsposition_def'] = array(
    array('option' => 'Hover - over content', 'value' => 'hover'),
    array('option' => 'Block - pushes content', 'value' => 'block')
);
$options['asp_defaults']['resultsposition'] = 'hover';
$options['asp_defaults']['resultsmargintop'] = '12px';

$options['asp_defaults']['defaultsearchtext'] = 'Search here...';
$options['asp_defaults']['showmoreresults'] = 0;
$options['asp_defaults']['showmoreresultstext'] = 'More results...';
$options['asp_defaults']['more_redirect_url'] = '?s={phrase}';
$options['asp_defaults']['showmorefont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(5, 94, 148, 1);font-size:12px;line-height:15px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';

$options['asp_defaults']['results_click_blank'] = 0;
$options['asp_defaults']['scroll_to_results'] = 0;
$options['asp_defaults']['resultareaclickable'] = 1;
$options['asp_defaults']['close_on_document_click'] = 1;
$options['asp_defaults']['show_close_icon'] = 1;
$options['asp_defaults']['showauthor'] = 0;
$options['asp_defaults']['author_field'] = "display_name";
$options['asp_defaults']['showdate'] = 0;
$options['asp_defaults']['custom_date'] = 0;
$options['asp_defaults']['custom_date_format'] = "Y-m-d H:i:s";
$options['asp_defaults']['showdescription'] = 1;
$options['asp_defaults']['descriptionlength'] = 100;
$options['asp_defaults']['description_context'] = 1;
$options['asp_defaults']['noresultstext'] = "No results!";
$options['asp_defaults']['didyoumeantext'] = "Did you mean:";
$options['asp_defaults']['highlight'] = 0;
$options['asp_defaults']['highlightwholewords'] = 1;
$options['asp_defaults']['highlightcolor'] = "#d9312b";
$options['asp_defaults']['highlightbgcolor'] = "#eee";

/* Layout Options / Compact Search Layout */
$options['asp_defaults']['box_compact_layout'] = 0;
$options['asp_defaults']['box_compact_close_on_magn'] = 1;
$options['asp_defaults']['box_compact_close_on_document'] = 0;
$options['asp_defaults']['box_compact_width'] = "100%";
$options['asp_defaults']['box_compact_overlay'] = 0;
$options['asp_defaults']['box_compact_overlay_color'] = "rgba(255, 255, 255, 0.5)";
$options['asp_defaults']['box_compact_float'] = "inherit";
$options['asp_defaults']['box_compact_float_def'] = array(
    array('option' => 'No floating', 'value' => 'none'),
    array('option' => 'Left', 'value' => 'left'),
    array('option' => 'Right', 'value' => 'right')
);
$options['asp_defaults']['box_compact_position'] = "static";
$options['asp_defaults']['box_compact_position_def'] = array(
    array('option' => 'Static (default)', 'value' => 'static'),
    array('option' => 'Fixed', 'value' => 'fixed'),
    array('option' => 'Absolute', 'value' => 'absolute')
);
$options['asp_defaults']['box_compact_screen_position'] = '||20%||auto||0px||auto||';
$options['asp_defaults']['box_compact_position_z'] = '1000';

/* Autocomplete & Keyword suggestion options */
$options['asp_defaults']['keywordsuggestions'] = 1;
$options['asp_defaults']['keyword_suggestion_source_def'] = array(
    'google' => 'Google keywords',
    'statistics' => 'Statistics database',
    'tags' => 'Post tags',
    'titles' => 'Post titles'
);
$options['asp_defaults']['keyword_suggestion_source'] = 'google';
$options['asp_defaults']['keywordsuggestionslang'] = "en";
$options['asp_defaults']['keyword_suggestion_count'] = 10;
$options['asp_defaults']['keyword_suggestion_length'] = 60;

$options['asp_defaults']['autocomplete'] = 1;
$options['asp_defaults']['autocomplete_source'] = 'google';
$options['asp_defaults']['autocompleteexceptions'] = '';
$options['asp_defaults']['autocomplete_length'] = 60;
$options['asp_defaults']['autocomplete_google_lang'] = "en";

// Advanced Options - Content
$options['asp_defaults']['striptagsexclude'] = '<span><b>';
$options['asp_defaults']['runshortcode'] = 1;
$options['asp_defaults']['stripshortcode'] = 0;
$options['asp_defaults']['pageswithcategories'] = 0;

$options['asp_defaults']['titlefield_def'] = array(
    array('option' => 'Post Title', 'value' => 0),
    array('option' => 'Post Excerpt', 'value' => 1)
);
$options['asp_defaults']['titlefield'] = 0;

$options['asp_defaults']['descriptionfield_def'] = array(
    array('option' => 'Post Description', 'value' => 0),
    array('option' => 'Post Excerpt', 'value' => 1),
    array('option' => 'Post Title', 'value' => 2)
);
$options['asp_defaults']['descriptionfield'] = 0;

$options['asp_defaults']['advtitlefield'] = '{titlefield}';
$options['asp_defaults']['advdescriptionfield'] = '{descriptionfield}';

$options['asp_defaults']['excludecategories'] = '';
$options['asp_defaults']['excludeterms'] = '';
$options['asp_defaults']['excludeposts'] = '';
$options['asp_defaults']['exclude_page_parent_child'] = '';

// Advanced Options - Grouping
$options['asp_defaults']['groupby_def'] = array(
    array('option' => 'No grouping', 'value' => 0),
    array('option' => 'Group By Categories', 'value' => 1),
    array('option' => 'Group By Post Type', 'value' => 2)
);
$options['asp_defaults']['groupby'] = 0;

$options['asp_defaults']['group_exclude_duplicates'] = 0;

$options['asp_defaults']['groupbytext'] = "Posts from: %GROUP%";

$options['asp_defaults']['bbpressreplytext'] = "BuddyPress group Results";
$options['asp_defaults']['bbpressgroupstext'] = "BuddyPress activity Results";
$options['asp_defaults']['bbpressuserstext'] = "User Results";
$options['asp_defaults']['term_group_text'] = "Term Results";
$options['asp_defaults']['attachment_group_text'] = "Attachment results";

$options['asp_defaults']['excludewoocommerceskus'] = 0;

$options['asp_defaults']['commentstext'] = "Comment Results";
$options['asp_defaults']['uncategorizedtext'] = "Other Results";
$options['asp_defaults']['showpostnumber'] = 1;
$options['asp_defaults']['bpgroupstitle_def'] = array(
    array('option' => 'Topic title', 'value' => 'bb_topics.topic_title as title'),
    array('option' => 'Post Content', 'value' => 'bb_posts.post_text as title')
);
$options['asp_defaults']['bpgroupstitle'] = "bb_topics.topic_title as title";
$options['asp_defaults']['wpml_compatibility'] = 1;

// Advanced Options - Animations
// Desktop
$options['asp_defaults']['sett_box_animation'] = "fadedrop";
$options['asp_defaults']['sett_box_animation_duration'] = 300;
$options['asp_defaults']['res_box_animation'] = "fadedrop";
$options['asp_defaults']['res_box_animation_duration'] = 300;
$options['asp_defaults']['res_items_animation'] = "fadeInDown";
// Mobile
$options['asp_defaults']['sett_box_animation_m'] = "fadedrop";
$options['asp_defaults']['sett_box_animation_duration_m'] = 300;
$options['asp_defaults']['res_box_animation_m'] = "fadedrop";
$options['asp_defaults']['res_box_animation_duration_m'] = 300;
$options['asp_defaults']['res_items_animation_m'] = "voidanim";


/* Theme options */
$options['asp_defaults']['box_width'] = '100%';
$options['asp_defaults']['boxheight'] = '28px';
$options['asp_defaults']['boxmargin'] = '4px';
$options['asp_defaults']['boxbackground'] = '0-60-rgb(219, 233, 238)-rgb(219, 233, 238)';
$options['asp_defaults']['boxborder'] = 'border:0px none rgba(0, 0, 0, 1);border-radius:5px 5px 5px 5px;';
$options['asp_defaults']['boxshadow'] = 'box-shadow:0px 10px 18px -13px #000000 ;';
$options['asp_defaults']['inputbackground'] = '0-60-rgb(255, 255, 255)-rgb(255, 255, 255)';
$options['asp_defaults']['inputborder'] = 'border:1px solid rgb(104, 174, 199);border-radius:3px 3px 3px 3px;';
$options['asp_defaults']['inputshadow'] = 'box-shadow:1px 0px 6px -3px rgb(181, 181, 181) inset;';
$options['asp_defaults']['inputfont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgb(0, 0, 0);font-size:12px;line-height:15px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';

$options['asp_defaults']['settingsimagepos_def'] = array(
    array('option' => 'Left', 'value' => 'left'),
    array('option' => 'Right', 'value' => 'right')
);
$options['asp_defaults']['settingsimagepos'] = "left";
$options['asp_defaults']['settingsbackground'] = '1-185-rgb(104, 174, 199)-rgb(108, 209, 245)';
$options['asp_defaults']['settingsboxshadow'] = 'box-shadow:1px 1px 0px 0px rgba(255, 255, 255, 0.63) inset;';
$options['asp_defaults']['settingsbackgroundborder'] = 'border:0px solid rgb(104, 174, 199);border-radius:0px 0px 0px 0px;';
$options['asp_defaults']['settingsdropbackground'] = '1-185-rgba(109, 204, 237, 1)-rgb(104, 174, 199)';
$options['asp_defaults']['settingsdropfont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgb(255, 255, 255);font-size:12px;line-height:15px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['settingsdropboxshadow'] = 'box-shadow:2px 2px 3px -1px rgba(170, 170, 170, 1);';
$options['asp_defaults']['settingsdroptickcolor'] = 'rgba(255, 255, 255, 1)';
$options['asp_defaults']['settingsdroptickbggradient'] = '1-180-rgba(34, 34, 34, 1)-rgba(69, 72, 77, 1)';

$options['asp_defaults']['vresultinanim'] = 'rollIn';
$options['asp_defaults']['vresulthbg'] = '0-60-rgb(235, 250, 255)-rgb(235, 250, 255)';
$options['asp_defaults']['resultsborder'] = 'border:0px none #000000;border-radius:3px 3px 3px 3px;';
$options['asp_defaults']['resultshadow'] = 'box-shadow:0px 0px 0px 0px #000000 ;';
$options['asp_defaults']['resultsbackground'] = 'rgb(153, 218, 241)';
$options['asp_defaults']['resultscontainerbackground'] = 'rgb(255, 255, 255)';
$options['asp_defaults']['resultscontentbackground'] = '#ffffff';
$options['asp_defaults']['titlefont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgba(20, 84, 169, 1);font-size:14px;line-height:20px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['import-titlefont'] = "@import url(https://fonts.googleapis.com/css?family=Open+Sans:300|Open+Sans:400|Open+Sans:700);";
$options['asp_defaults']['titlehoverfont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgb(46, 107, 188);font-size:14px;line-height:20px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['authorfont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgba(161, 161, 161, 1);font-size:12px;line-height:13px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['datefont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(173, 173, 173, 1);font-size:12px;line-height:15px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['descfont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(74, 74, 74, 1);font-size:13px;line-height:13px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['import-descfont'] = "@import url(https://fonts.googleapis.com/css?family=Lato:300|Lato:400|Lato:700);";
$options['asp_defaults']['groupfont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(74, 74, 74, 1);font-size:13px;line-height:13px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['groupingbordercolor'] = 'rgb(248, 248, 248)';
$options['asp_defaults']['spacercolor'] = 'rgba(204, 204, 204, 1)';
$options['asp_defaults']['arrowcolor'] = 'rgba(10, 63, 77, 1)';
$options['asp_defaults']['harrowcolor'] = 'rgb(98, 150, 172)';
$options['asp_defaults']['overflowcolor'] = 'rgba(255, 255, 255, 1)';

$options['asp_defaults']['magnifierimage_selects'] = array(
    "/ajax-search-pro/img/svg/magnifiers/magn1.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn2.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn3.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn4.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn5.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn6.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn7.svg",
    "/ajax-search-pro/img/svg/arrows/arrow1.svg",
    "/ajax-search-pro/img/svg/arrows/arrow2.svg",
    "/ajax-search-pro/img/svg/arrows/arrow3.svg",
    "/ajax-search-pro/img/svg/arrows/arrow4.svg",
    "/ajax-search-pro/img/svg/arrows/arrow5.svg",
    "/ajax-search-pro/img/svg/arrows/arrow6.svg",
    "/ajax-search-pro/img/svg/arrows/arrow7.svg",
    "/ajax-search-pro/img/svg/arrows/arrow8.svg",
    "/ajax-search-pro/img/svg/arrows/arrow9.svg",
    "/ajax-search-pro/img/svg/arrows/arrow10.svg",
    "/ajax-search-pro/img/svg/arrows/arrow11.svg",
    "/ajax-search-pro/img/svg/arrows/arrow12.svg",
    "/ajax-search-pro/img/svg/arrows/arrow13.svg",
    "/ajax-search-pro/img/svg/arrows/arrow14.svg",
    "/ajax-search-pro/img/svg/arrows/arrow15.svg",
    "/ajax-search-pro/img/svg/arrows/arrow16.svg"
);

$options['asp_defaults']['magnifierimage'] = "/ajax-search-pro/img/svg/magnifiers/magn4.svg";

$options['asp_defaults']['magnifier_position'] = 'right';
$options['asp_defaults']['magnifierimage_color'] = "rgba(0, 0, 0, 1);";
$options['asp_defaults']['magnifierimage_custom'] = "";

// Theme options - Search text button
$options['asp_defaults']['display_search_text'] = 0;
$options['asp_defaults']['hide_magnifier'] = 0;
$options['asp_defaults']['search_text'] = "Search";
$options['asp_defaults']['search_text_position'] = "right";
$options['asp_defaults']['search_text_font'] = "'font-weight:normal;font-family:--g--Open Sans;color:rgba(51, 51, 51, 1);font-size:15px;line-height:auto;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';";

// Theme options - Settings image
$options['asp_defaults']['settingsimage_selects'] = array(
    "/ajax-search-pro/img/svg/menu/menu1.svg",
    "/ajax-search-pro/img/svg/menu/menu2.svg",
    "/ajax-search-pro/img/svg/menu/menu3.svg",
    "/ajax-search-pro/img/svg/menu/menu4.svg",
    "/ajax-search-pro/img/svg/menu/menu5.svg",
    "/ajax-search-pro/img/svg/menu/menu6.svg",
    "/ajax-search-pro/img/svg/menu/menu7.svg",
    "/ajax-search-pro/img/svg/menu/menu8.svg",
    "/ajax-search-pro/img/svg/menu/menu9.svg",
    "/ajax-search-pro/img/svg/menu/menu10.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow1.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow2.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow3.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow4.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow5.svg",
    "/ajax-search-pro/img/svg/arrows-down/arrow6.svg",
    "/ajax-search-pro/img/svg/control-panel/cp1.svg",
    "/ajax-search-pro/img/svg/control-panel/cp2.svg",
    "/ajax-search-pro/img/svg/control-panel/cp3.svg",
    "/ajax-search-pro/img/svg/control-panel/cp4.svg",
    "/ajax-search-pro/img/svg/control-panel/cp5.svg",
    "/ajax-search-pro/img/svg/control-panel/cp6.svg",
    "/ajax-search-pro/img/svg/control-panel/cp7.svg",
    "/ajax-search-pro/img/svg/control-panel/cp8.svg",
    "/ajax-search-pro/img/svg/control-panel/cp9.svg",
    "/ajax-search-pro/img/svg/control-panel/cp10.svg",
    "/ajax-search-pro/img/svg/control-panel/cp11.svg",
    "/ajax-search-pro/img/svg/control-panel/cp12.svg",
    "/ajax-search-pro/img/svg/control-panel/cp13.svg",
    "/ajax-search-pro/img/svg/control-panel/cp14.svg",
    "/ajax-search-pro/img/svg/control-panel/cp15.svg",
    "/ajax-search-pro/img/svg/control-panel/cp16.svg"
);
$options['asp_defaults']['settingsimage'] = "/ajax-search-pro/img/svg/arrows-down/arrow1.svg";
$options['asp_defaults']['settingsimage_color'] = "rgba(0, 0, 0, 1);";
$options['asp_defaults']['settingsimage_custom'] = "";


/*$options['asp_defaults']['loadingimage_selects'] = array(
  "/ajax-search-pro/img/loading/newload1.gif",
  "/ajax-search-pro/img/loading/newload2.gif",
  "/ajax-search-pro/img/loading/newload3.gif",
  "/ajax-search-pro/img/loading/newload4.gif",
  "/ajax-search-pro/img/loading/newload5.gif",
  "/ajax-search-pro/img/loading/newload6.gif",
  "/ajax-search-pro/img/loading/newload7.gif",
  "/ajax-search-pro/img/loading/newload8.gif",
  "/ajax-search-pro/img/loading/newload9.gif",
  "/ajax-search-pro/img/loading/newload10.gif",
  "/ajax-search-pro/img/loading/newload11.gif",
  "/ajax-search-pro/img/loading/newload12.gif",
  "/ajax-search-pro/img/loading/newload13.gif",
  "/ajax-search-pro/img/loading/newload14.gif",
  "/ajax-search-pro/img/loading/newload14r.gif",
  "/ajax-search-pro/img/loading/newload15.gif",
  "/ajax-search-pro/img/loading/newload16.gif",
  "/ajax-search-pro/img/loading/newload17.gif",
  "/ajax-search-pro/img/loading/newload18.gif",
  "/ajax-search-pro/img/loading/loading1.gif",
  "/ajax-search-pro/img/loading/loading2.gif",
  "/ajax-search-pro/img/loading/loading3.gif",
  "/ajax-search-pro/img/loading/loading4.gif",
  "/ajax-search-pro/img/loading/loading5.gif",
  "/ajax-search-pro/img/loading/loading6.gif",
  "/ajax-search-pro/img/loading/loading7.gif",
  "/ajax-search-pro/img/loading/loading8.gif",
  "/ajax-search-pro/img/loading/loading9.gif",
  "/ajax-search-pro/img/loading/loading10.gif",
  "/ajax-search-pro/img/loading/loading11.gif"
);*/

$options['asp_defaults']['loadingimage_selects'] = array(
    "/ajax-search-pro/img/svg/loading/loading-bars.svg",
    "/ajax-search-pro/img/svg/loading/loading-balls.svg",
    "/ajax-search-pro/img/svg/loading/loading-bubbles.svg",
    "/ajax-search-pro/img/svg/loading/loading-cubes.svg",
    "/ajax-search-pro/img/svg/loading/loading-cylon.svg",
    "/ajax-search-pro/img/svg/loading/loading-fb.svg",
    "/ajax-search-pro/img/svg/loading/loading-poi.svg",
    "/ajax-search-pro/img/svg/loading/loading-spin.svg",
    "/ajax-search-pro/img/svg/loading/loading-spinning-bubbles.svg",
    "/ajax-search-pro/img/svg/loading/loading-spokes.svg"
);

// Maybe in future releases?
/*
$options['asp_defaults']['loadingimage_selects'] = array(
    "/ajax-search-pro/img/html/loaders/loader1.html",
    "/ajax-search-pro/img/html/loaders/loader2.html",
    "/ajax-search-pro/img/html/loaders/loader3.html",
    "/ajax-search-pro/img/html/loaders/loader4.html",
    "/ajax-search-pro/img/html/loaders/loader5.html",
    "/ajax-search-pro/img/html/loaders/loader6.html",
    "/ajax-search-pro/img/html/loaders/loader7.html",
    "/ajax-search-pro/img/html/loaders/loader8.html"
);
*/

$options['asp_defaults']['loadingimage'] = "/ajax-search-pro/img/svg/loading/loading-spin.svg";
$options['asp_defaults']['loader_image'] = "simple-circle";
$options['asp_defaults']['loadingimage_color'] = "rgba(0, 0, 0, 1);";
$options['asp_defaults']['loadingimage_custom'] = "";

$options['asp_defaults']['magnifierbackground'] = "1-180-rgb(132, 197, 220)-rgb(108, 209, 245)";
$options['asp_defaults']['magnifierbackgroundborder'] = 'border:0px solid rgb(104, 174, 199);border-radius:0px 0px 0px 0px;';
$options['asp_defaults']['magnifierboxshadow'] = 'box-shadow:-1px 1px 0px 0px rgba(255, 255, 255, 0.61) inset;';
$options['asp_defaults']['descriptionlength'] = 130;
$options['asp_defaults']['groupbytextfont'] = 'font-weight:bold;font-family:--g--Open Sans;color:rgba(5, 94, 148, 1);font-size:11px;line-height:13px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['exsearchincategoriesboxcolor'] = "rgb(246, 246, 246)";

$options['asp_defaults']['blogtitleorderby_def'] = array(
    array('option' => 'Blog titles descending', 'value' => 'desc'),
    array('option' => 'Blog titles ascending', 'value' => 'asc')
);
$options['asp_defaults']['blogtitleorderby'] = 'desc';

$options['asp_defaults']['hreswidth'] = '150px';
$options['asp_defaults']['hresheight'] = '170px';
$options['asp_defaults']['hressidemargin'] = '8px';
$options['asp_defaults']['hrespadding'] = '7px';
$options['asp_defaults']['hresultinanim'] = 'bounceIn';
$options['asp_defaults']['hboxbg'] = '0-60-rgb(136, 197, 219)-rgb(153, 218, 241)';
$options['asp_defaults']['hboxborder'] = 'border:5px solid rgb(219, 233, 238);border-radius:5px 5px 5px 5px;';
$options['asp_defaults']['hboxshadow'] = 'box-shadow:0px 0px 4px -3px rgb(0, 0, 0) inset;';
$options['asp_defaults']['hresultbg'] = '0-60-rgba(255, 255, 255, 1)-rgba(255, 255, 255, 1)';
$options['asp_defaults']['hresulthbg'] = '0-60-rgba(255, 255, 255, 1)-rgba(255, 255, 255, 1)';
$options['asp_defaults']['hresultborder'] = 'border:0px none rgb(250, 250, 250);border-radius:3px 3px 3px 3px;';
$options['asp_defaults']['hresultshadow'] = 'box-shadow:0px 0px 6px -3px rgb(0, 0, 0);';
$options['asp_defaults']['hresultimageborder'] = 'border:0px none rgb(250, 250, 250);border-radius:7px 7px 7px 7px;';
$options['asp_defaults']['hresultimageshadow'] = 'box-shadow:0px 0px 9px -5px rgb(0, 0, 0) inset;';
$options['asp_defaults']['hhidedesc'] = 1;
$options['asp_defaults']['hoverflowcolor'] = "rgb(250, 250, 250)";

//Isotopic Syle options
$options['asp_defaults']['i_ifnoimage_def'] = array(
    array('option' => 'Show the default image', 'value' => 'defaultimage'),
    array('option' => 'Show the description', 'value' => 'description'),
    array('option' => 'Dont show that result', 'value' => 'removeres')
);
$options['asp_defaults']['i_ifnoimage'] = "description";
$options['asp_defaults']['i_item_width'] = 200;
$options['asp_defaults']['i_item_height'] = 200;
$options['asp_defaults']['i_res_item_content_background'] = 'rgba(0, 0, 0, 0.28);';
$options['asp_defaults']['i_res_magnifierimage_selects'] = array(
    "/ajax-search-pro/img/svg/magnifiers/magn1.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn2.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn3.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn4.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn5.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn6.svg",
    "/ajax-search-pro/img/svg/magnifiers/magn7.svg",
    "/ajax-search-pro/img/svg/arrows/arrow1.svg",
    "/ajax-search-pro/img/svg/arrows/arrow2.svg",
    "/ajax-search-pro/img/svg/arrows/arrow3.svg",
    "/ajax-search-pro/img/svg/arrows/arrow4.svg",
    "/ajax-search-pro/img/svg/arrows/arrow5.svg",
    "/ajax-search-pro/img/svg/arrows/arrow6.svg",
    "/ajax-search-pro/img/svg/arrows/arrow7.svg",
    "/ajax-search-pro/img/svg/arrows/arrow8.svg",
    "/ajax-search-pro/img/svg/arrows/arrow9.svg",
    "/ajax-search-pro/img/svg/arrows/arrow10.svg",
    "/ajax-search-pro/img/svg/arrows/arrow11.svg",
    "/ajax-search-pro/img/svg/arrows/arrow12.svg",
    "/ajax-search-pro/img/svg/arrows/arrow13.svg",
    "/ajax-search-pro/img/svg/arrows/arrow14.svg",
    "/ajax-search-pro/img/svg/arrows/arrow15.svg",
    "/ajax-search-pro/img/svg/arrows/arrow16.svg"
);

$options['asp_defaults']['i_res_magnifierimage'] = "/ajax-search-pro/img/svg/magnifiers/magn4.svg";
$options['asp_defaults']['i_res_custom_magnifierimage'] = "";

$options['asp_defaults']['i_overlay'] = 1;
$options['asp_defaults']['i_overlay_blur'] = 1;
$options['asp_defaults']['i_hide_content'] = 1;
$options['asp_defaults']['i_animation'] = 'bounceIn';
$options['asp_defaults']['i_rows'] = 2;
$options['asp_defaults']['i_res_container_margin'] = "||0px||0px||0px||0px||";
$options['asp_defaults']['i_res_container_padding'] = "||0px||0px||0px||0px||";
$options['asp_defaults']['i_res_container_bg'] = 'rgba(255, 255, 255, 0);';


$options['asp_defaults']['i_pagination_position_def'] = array(
    array('option' => 'Top', 'value' => 'top'),
    array('option' => 'Bottom', 'value' => 'bottom')
);
$options['asp_defaults']['i_pagination_position'] = "top";
$options['asp_defaults']['i_pagination_background'] = "rgb(228, 228, 228);";
$options['asp_defaults']['i_pagination_arrow_selects'] = array(
    "/ajax-search-pro/img/svg/arrows/arrow1.svg",
    "/ajax-search-pro/img/svg/arrows/arrow2.svg",
    "/ajax-search-pro/img/svg/arrows/arrow3.svg",
    "/ajax-search-pro/img/svg/arrows/arrow4.svg",
    "/ajax-search-pro/img/svg/arrows/arrow5.svg",
    "/ajax-search-pro/img/svg/arrows/arrow6.svg",
    "/ajax-search-pro/img/svg/arrows/arrow7.svg",
    "/ajax-search-pro/img/svg/arrows/arrow8.svg",
    "/ajax-search-pro/img/svg/arrows/arrow9.svg",
    "/ajax-search-pro/img/svg/arrows/arrow10.svg",
    "/ajax-search-pro/img/svg/arrows/arrow11.svg",
    "/ajax-search-pro/img/svg/arrows/arrow12.svg",
    "/ajax-search-pro/img/svg/arrows/arrow13.svg",
    "/ajax-search-pro/img/svg/arrows/arrow14.svg",
    "/ajax-search-pro/img/svg/arrows/arrow15.svg",
    "/ajax-search-pro/img/svg/arrows/arrow16.svg",
    "/ajax-search-pro/img/svg/arrows/arrow17.svg",
    "/ajax-search-pro/img/svg/arrows/arrow18.svg"
);
$options['asp_defaults']['i_pagination_arrow'] = "/ajax-search-pro/img/svg/arrows/arrow1.svg";
$options['asp_defaults']['i_pagination_arrow_background'] = "rgb(76, 76, 76);";
$options['asp_defaults']['i_pagination_arrow_color'] = "rgb(255, 255, 255);";
$options['asp_defaults']['i_pagination_page_background'] = "rgb(244, 244, 244);";
$options['asp_defaults']['i_pagination_font_color'] = "rgb(126, 126, 126);";


//Polaroid Style options
$options['asp_defaults']['pifnoimage'] = "removeres";
$options['asp_defaults']['pshowdesc'] = 1;
$options['asp_defaults']['prescontainerheight'] = '400px';
$options['asp_defaults']['preswidth'] = '200px';
$options['asp_defaults']['presheight'] = '240px';
$options['asp_defaults']['prespadding'] = '25px';
$options['asp_defaults']['prestitlefont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(167, 160, 162, 1);font-size:16px;line-height:20px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['pressubtitlefont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(133, 133, 133, 1);font-size:13px;line-height:18px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['pshowsubtitle'] = 0;

$options['asp_defaults']['presdescfont'] = 'font-weight:normal;font-family:--g--Open Sans;color:rgba(167, 160, 162, 1);font-size:14px;line-height:17px;text-shadow:0px 0px 0px rgba(255, 255, 255, 0);';
$options['asp_defaults']['prescontainerbg'] = '0-60-rgba(221, 221, 221, 1)-rgba(221, 221, 221, 1)';
$options['asp_defaults']['pdotssmallcolor'] = '0-60-rgba(170, 170, 170, 1)-rgba(170, 170, 170, 1)';
$options['asp_defaults']['pdotscurrentcolor'] = '0-60-rgba(136, 136, 136, 1)-rgba(136, 136, 136, 1)';
$options['asp_defaults']['pdotsflippedcolor'] = '0-60-rgba(85, 85, 85, 1)-rgba(85, 85, 85, 1)';

// Custom CSS
$options['asp_defaults']['custom_css'] = '';

//Relevance options
$options['asp_defaults']['userelevance'] = 1;
$options['asp_defaults']['weight_def'] = array(
    array('option' => '10 - Highest weight', 'value' => 10),
    array('option' => '9', 'value' => 9),
    array('option' => '8', 'value' => 8),
    array('option' => '7', 'value' => 7),
    array('option' => '6', 'value' => 6),
    array('option' => '5', 'value' => 5),
    array('option' => '4', 'value' => 4),
    array('option' => '3', 'value' => 3),
    array('option' => '2', 'value' => 2),
    array('option' => '1 - Lowest weight', 'value' => 1)
);
$options['asp_defaults']['etitleweight'] = 10;
$options['asp_defaults']['econtentweight'] = 9;
$options['asp_defaults']['eexcerptweight'] = 9;
$options['asp_defaults']['etermsweight'] = 7;
$options['asp_defaults']['titleweight'] = 3;
$options['asp_defaults']['contentweight'] = 2;
$options['asp_defaults']['excerptweight'] = 2;
$options['asp_defaults']['termsweight'] = 2;

$options['asp_defaults']['it_title_weight'] = 100;
$options['asp_defaults']['it_content_weight'] = 20;
$options['asp_defaults']['it_excerpt_weight'] = 10;
$options['asp_defaults']['it_terms_weight'] = 10;
$options['asp_defaults']['it_cf_weight'] = 8;
$options['asp_defaults']['it_author_weight'] = 8;


/* Save the default options if not exist */
$_asp_ver = get_option('asp_version');

// Update the default options if not exist/newer version available/debug mode is on
foreach ($options as $key => $value) {
    if (get_option($key) === false || $_asp_ver != ASP_CURR_VER || ASP_DEBUG == 1) {
        update_option($key, $value);
    }
}