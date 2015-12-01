<?php
    // -- AJAX_SEARCH.PHP --
    //mimic the actual admin-ajax
    define('DOING_AJAX', true);
    
    if (!isset( $_POST['action']))
        die('-1');
    
    //make sure you update this line 
    //to the relative location of the wp-load.php
    require_once('../../../wp-load.php'); 
    //require_once('search.php');
    
    //Typical headers
    header('Content-Type: text/html');
    send_nosniff_header();
    
    //Disable caching
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');
    
    
    $action = esc_attr(trim($_POST['action']));
    
    //A bit of security
    $allowed_actions = array(
        'ajaxsearchpro_search',
        'ajaxsearchpro_autocomplete',
        'ajaxsearchpro_preview',
        'ajaxsearchpro_precache',
        'ajaxsearchpro_deletecache',
        'ajaxsearchpro_deletekeyword'
    );
    
    //$action_name =  "ajaxsearchpro_search";
    //For logged in users
    
    add_action('ASP_ajaxsearchpro_search', 'ajaxsearchpro_search');
    add_action('ASP_nopriv_ajaxsearchpro_search', 'ajaxsearchpro_search');

    add_action('ASP_nopriv_ajaxsearchpro_autocomplete', 'ajaxsearchpro_autocomplete');
    add_action('ASP_ajaxsearchpro_autocomplete', 'ajaxsearchpro_autocomplete');
    add_action('ASP_ajaxsearchpro_preview', 'ajaxsearchpro_preview');
    add_action('ASP_ajaxsearchpro_precache', 'ajaxsearchpro_precache');
    add_action('ASP_ajaxsearchpro_deletecache', 'ajaxsearchpro_deletecache');
    add_action('ASP_ajaxsearchpro_deletekeyword', 'ajaxsearchpro_deletekeyword');
    
    if(in_array($action, $allowed_actions)) {
        if(is_user_logged_in())
            do_action('ASP_'.$action);
        else
            do_action('ASP_nopriv_'.$action);
    } else {
        die('-1');
    } 
    
?>