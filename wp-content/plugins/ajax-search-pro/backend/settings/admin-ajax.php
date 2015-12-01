<?php
  /* Ajax functions here */


  // --------------------  FULLTEXT RELATED ----------------------

  add_action('wp_ajax_ajaxsearchpro_activate_fulltext', 'ajaxsearchpro_activate_fulltext');
  function ajaxsearchpro_activate_fulltext() {
    global $wpdb;
    $fulltext = wpdreamsFulltext::getInstance();
    $tables = array('posts');
    $indexes = array(
       array('table'=>'posts', 'index'=>'asp_title', 'columns'=>'post_title'),
       array('table'=>'posts', 'index'=>'asp_content', 'columns'=>'post_content'),
       array('table'=>'posts', 'index'=>'asp_excerpt', 'columns'=>'post_excerpt'),
       array('table'=>'posts', 'index'=>'asp_title_content', 'columns'=>'post_title, post_content'),
       array('table'=>'posts', 'index'=>'asp_title_excerpt', 'columns'=>'post_title, post_excerpt'),
       array('table'=>'posts', 'index'=>'asp_content_excerpt', 'columns'=>'post_content, post_excerpt'),
       array('table'=>'posts', 'index'=>'asp_title_content_excerpt', 'columns'=>'post_title, post_content, post_excerpt')
    );
    $blogs = wpdreams_get_blog_list(0, 'all');
    
    if (is_multisite() && is_array($blogs) && count($blogs)) {
       foreach($blogs as $k=>$blog) {
         switch_to_blog($blog['blog_id']);
         if($fulltext->check($tables)) {
            update_option('asp_fulltext', 1);
            if ($fulltext->createIndexes($indexes))
              update_option('asp_fulltext_indexed', 1);
         } 
       }
       restore_current_blog();
    } else {
       if($fulltext->check($tables)) {
          update_option('asp_fulltext', 1);
          if ($fulltext->createIndexes($indexes))
            update_option('asp_fulltext_indexed', 1);
       }
    }
    if (get_option('asp_fulltext')==1) {
      print "<div class='psuccessMsg'>MyIsam tables enabled, fulltext search available!</div>";
      if (get_option('asp_fulltext_indexed')==1)
         print "<div class='psuccessMsg'>Indexes created!</div>";
      else
         print "<div class='perrorMsg'>Couldn't create indexes, using BOOLEAN MODE instead!</div>";
    } else {
      print "<div class='perrorMsg'>MyIsam tables disabled, fulltext search not available!</div>";
    }
    die();
  }
  
  add_action('wp_ajax_ajaxsearchpro_deactivate_fulltext', 'ajaxsearchpro_deactivate_fulltext');
  function ajaxsearchpro_deactivate_fulltext() {
    global $wpdb;
    $fulltext = wpdreamsFulltext::getInstance();
    $indexes = array(
       'posts'=>array(
        'asp_title',
        'asp_content',
        'asp_excerpt',
        'asp_title_content',
        'asp_title_excerpt',
        'asp_content_excerpt',
        'asp_title_content_excerpt'
    ));
    $blogs = wpdreams_get_blog_list(0, 'all');
    
    if (is_multisite() && is_array($blogs) && count($blogs)) {
       foreach($blogs as $k=>$blog) {
          switch_to_blog($blog['blog_id']);
          $fulltext->removeIndexes($indexes); 
       }
       restore_current_blog();
    } else {
       $fulltext->removeIndexes($indexes);
    }
    update_option('asp_fulltext_indexed', 0);
    print "<div class='psuccessMsg'>Indexes removed!</div>";
    die();
  }

// -------------------------------------------------------------

// --------------------  PRIORITY RELATED ----------------------
add_action('wp_ajax_ajaxsearchpro_priorities_get_posts', 'ajaxsearchpro_priorities_get_posts');
function ajaxsearchpro_priorities_get_posts() {
    global $wpdb;
    parse_str($_POST['options'], $o);

    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }

    $w_post_type = '';
    $w_filter = '';
    $w_limit = (int)$o['p_asp_limit'];

    if (isset($o['blog_id']) && $o['blog_id'] != 0 && is_multisite())
        switch_to_blog($o['p_asp_blog']);

    if ($o['p_asp_filter'] != '') {
        $w_filter = "AND $wpdb->posts.post_title LIKE '%".$o['p_asp_filter']."%'";
    }

    if ($o['p_asp_post_type'] != 'all') {
        $w_post_type = "AND $wpdb->posts.post_type = '".$o['p_asp_post_type'] . "'";
    }

    $querystr = "
    		SELECT
          $wpdb->posts.post_title as title,
          $wpdb->posts.ID as id,
          $wpdb->posts.post_date as date,
          $wpdb->users.user_nicename as author,
          $wpdb->posts.post_type as post_type,
          CASE WHEN ".$_prefix."ajaxsearchpro_priorities.priority IS NULL
                   THEN 100
                   ELSE ".$_prefix."ajaxsearchpro_priorities.priority
          END AS priority
    		FROM $wpdb->posts
        LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->posts.post_author
        LEFT JOIN ".$_prefix."ajaxsearchpro_priorities ON (".$_prefix."ajaxsearchpro_priorities.post_id = $wpdb->posts.ID AND ".$_prefix."ajaxsearchpro_priorities.blog_id = ".get_current_blog_id().")
    	WHERE
          $wpdb->posts.ID>0 AND
          $wpdb->posts.post_status IN ('publish', 'pending') AND
          $wpdb->posts.post_type NOT IN ('revision', 'attachment')
          $w_post_type
          $w_filter
        GROUP BY
          $wpdb->posts.ID
        ORDER BY ".$o['p_asp_ordering']."
        LIMIT $w_limit";

    echo "!!PASPSTART!!" . json_encode($wpdb->get_results($querystr, OBJECT)) . '!!PASPEND!!';

    if (is_multisite()) restore_current_blog();

    die();
}

add_action('wp_ajax_ajaxsearchpro_priorities_set_priorities', 'ajaxsearchpro_priorities_set_priorities');
function ajaxsearchpro_priorities_set_priorities() {
    global $wpdb;
    $i = 0;
    parse_str($_POST['options'], $o);

    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }

    if ($o['p_blogid'] == 0)
        $o['p_blogid'] = get_current_blog_id();

    foreach ($o['priority'] as $k=>$v) {

        // See if the value changed, count them
        if ($v != $o['old_priority'][$k]) {

            $i++;
            $query = "INSERT INTO ".$_prefix."ajaxsearchpro_priorities (post_id, blog_id, priority) VALUES($k, ".$o['p_blogid'].", $v) ON DUPLICATE KEY UPDATE priority=".$v;
            $wpdb->query($query);
        }
    }
    echo "!!PSASPSTART!!".$i."!!PSASPEND!!";

    if (is_multisite()) restore_current_blog();

	ajaxsearchpro_priorities_clear();

    die();
}

add_action('wp_ajax_ajaxsearchpro_priorities_clear', 'ajaxsearchpro_priorities_clear');
function ajaxsearchpro_priorities_clear() {
	global $wpdb;
	if (isset($wpdb->base_prefix)) {
		$_prefix = $wpdb->base_prefix;
	} else {
		$_prefix = $wpdb->prefix;
	}

	$wpdb->query( "DELETE FROM ".$_prefix."ajaxsearchpro_priorities WHERE priority=100" );
}
// -------------------------------------------------------------