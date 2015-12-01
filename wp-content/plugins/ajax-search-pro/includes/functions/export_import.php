<?php
/**
 * Export/Import functions for AjaxSearchPro
 *
 * Generic Functions for all WPDreams producst
 *
 * @version  1.0
 * @package  AjaxSearchPro/Functions
 * @category Functions
 * @author   Ernest Marcinko
 */

/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * Generates exported search instances in serialized base 64 encded format
 *
 * @return array
 */
function asp_get_all_exported_instances() {
    global $wpdb;
    $return = array();
    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }
    $search_instances = $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
    foreach ($search_instances as $instance)
        $return[$instance['id']] = base64_encode(serialize($instance));
    return $return;
}

/**
 * Get a single exported search instance by ID
 *
 * @param int $id
 * @return bool
 */
function asp_get_exported_instance($id=0) {
    $instances = asp_get_all_exported_instances();
    return isset($instances[$id])?$instances[$id]:false;
}

/**
 * Imports the search instance
 *
 * @param $data
 * @return false on failure, affected rows on success
 */
function asp_import_instances($data) {
    global $wpdb;
    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }
    $s_data = json_decode(stripcslashes($data));
    if (is_array($s_data)) {
        foreach ($s_data as $dec_instance) {
            $_instance = unserialize(base64_decode($dec_instance));
            if (is_array($_instance)) {
                return $wpdb->insert(
                        $_prefix . "ajaxsearchpro",
                        array(
                            'name' => $_instance['name'].' Imported',
                            'data' => $_instance['data']
                        ),
                        array('%s', '%s')
                );
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}

function asp_import_settings($id, $data) {
    global $wpdb;
    if (isset($wpdb->base_prefix)) {
        $_prefix = $wpdb->base_prefix;
    } else {
        $_prefix = $wpdb->prefix;
    }

    //$data = stripcslashes($data);
    $data = unserialize(base64_decode($data));

    if (is_array($data)) {
        return $wpdb->update(
            $_prefix . "ajaxsearchpro",
            array(
                'data' => $data['data']
            ),
            array( 'id' => $id ),
            array(
                '%s'
            ),
            array( '%d' )
        );
    } else {
        return false;
    }
}