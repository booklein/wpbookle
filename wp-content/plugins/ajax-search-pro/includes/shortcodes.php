<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

add_shortcode( 'wpdreams_ajaxsearchpro_results', 'add_ajaxsearchpro_results');
add_shortcode( 'wpdreams_asp_settings', 'add_asp_settings');
add_shortcode( 'wpdreams_ajaxsearchpro', array( aspShortcodeContainer::get_instance(), 'wpdreams_asp_shortcode' ) );
add_shortcode( 'wpdreams_ajaxsearchpro_two_column', 'add_asp_two_column');

class aspShortcodeContainer {

    protected static $instance = NULL;
    private static $instanceCount = 0;
    private static $dataPrinted = false;
    private static $perInstanceCount = array();

    public static function get_instance() {
        // create an object
        NULL === self::$instance and self::$instance = new self;
        return self::$instance; // return the object
    }

    function wpdreams_asp_shortcode($atts) {
        $style = null;

        // Fallback on IE<=8
        if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [6-8]/',$_SERVER['HTTP_USER_AGENT']) ) {
            get_search_form( true );
            return;
        }

        extract(shortcode_atts(array(
            'id' => 'something',
            'extra_class' => ''
        ), $atts));


        if (isset($_POST['action']) && $_POST['action'] == "ajaxsearchpro_preview") {
            require_once(ASP_PATH . "backend" . DIRECTORY_SEPARATOR . "settings" . DIRECTORY_SEPARATOR . "types.inc.php");
            parse_str($_POST['formdata'], $style);
            $style = wpdreams_parse_params($style);
            ob_start();
            include(ASP_PATH . "/css/style.css.php");
            $out = ob_get_contents();
            ob_end_clean();
            //file_put_contents($file, $out, FILE_TEXT);
            ?>
            <div style='display: none;' id="asp_preview_options"><?php echo base64_encode(serialize($style)); ?></div>
            <style>
                @import url('<?php echo plugin_dir_url(__FILE__); ?>../css/style.basic.css?r=<?php echo rand(1, 123123123); ?>');
                <?php echo $out; ?>
            </style>
        <?php
        } else {
            global $wpdb;
            if (isset($wpdb->base_prefix)) {
                $_prefix = $wpdb->base_prefix;
            } else {
                $_prefix = $wpdb->prefix;
            }

	        // Visual composer, first selected row, no data, so select the first one
	        if ($id == 99999) {
		        $search = $wpdb->get_results(
			        "SELECT * FROM " . $_prefix . "ajaxsearchpro LIMIT 1",
			        ARRAY_A
		        );
		        if (!isset($search[0]))
			        return "There are no search instances to display. Please create one.";
	        } else {
		        $search = $wpdb->get_results(
			        $wpdb->prepare("SELECT * FROM " . $_prefix . "ajaxsearchpro WHERE id=%d",  $id),
			        ARRAY_A
		        );
		        if (!isset($search[0]))
			        return "This search form (with id $id) does not exist!";
	        }

	        // Parse the id again for correction
	        $id = $search[0]['id'] + 0;
            $wpdreams_ajaxsearchpros[$search[0]['id']] = 1;

            $search[0]['data'] = json_decode($search[0]['data'], true);
            $style = $search[0]['data'];
        }

        // Don't move this above any return statements!
        self::$instanceCount++;
        if (isset(self::$perInstanceCount[$id]))
            self::$perInstanceCount[$id]++;
        else
            self::$perInstanceCount[$id] = 1;

        $def_data = get_option('asp_defaults');
        $style = array_merge($def_data, $style);


        $settingsHidden = w_isset_def($style['show_frontend_search_settings'], 1) != 1 ? true : false;

        $asp_f_items = array();
        if (w_isset_def($style['custom_field_items'], "") != "") {
            $asp_f_items = explode('|', $style['custom_field_items']);
            $asp_f_uncoded_items = $asp_f_items;
            foreach ($asp_f_items as $k=>$v) {
                $asp_f_items[$k] = json_decode(base64_decode($v));
                if (isset($asp_f_items[$k]->asp_f_radio_value))
                    $asp_f_items[$k]->asp_f_radio_value = preg_split("/\\r\\n|\\r|\\n/", $asp_f_items[$k]->asp_f_radio_value);
                if (isset($asp_f_items[$k]->asp_f_dropdown_value))
                    $asp_f_items[$k]->asp_f_dropdown_value = preg_split("/\\r\\n|\\r|\\n/", $asp_f_items[$k]->asp_f_dropdown_value);
                if (isset($asp_f_items[$k]->asp_f_checkboxes_value))
                    $asp_f_items[$k]->asp_f_checkboxes_value = preg_split("/\\r\\n|\\r|\\n/", $asp_f_items[$k]->asp_f_checkboxes_value);
            }
        }

        do_action('asp_layout_before_shortcode', $id);

        $out = "";
        ob_start();
        include(ASP_PATH."includes/views/asp.shortcode.php");
        $out = ob_get_clean();

        do_action('asp_layout_after_shortcode', $id);

        return $out;
    }
}

function add_ajaxsearchpro_results( $atts ) {
	global $wpdb;

    extract( shortcode_atts( array(
        'id' => '0',
        'element' => 'div'
    ), $atts ) );
    if ($id == "") return;

	// Visual composer bug, get the first instance ID
	if ($id == 99999) {
		if (isset($wpdb->base_prefix)) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}

		$search = $wpdb->get_results(
			"SELECT * FROM " . $_prefix . "ajaxsearchpro LIMIT 1",
			ARRAY_A
		);
		if (!isset($search[0]))
			return "";

		$id = $search[0]['id'] + 0;
	}

    return "<".$element." id='wpdreams_asp_results_".$id."'></".$element.">";
}

function add_asp_settings( $atts ) {
	global $wpdb;

    extract( shortcode_atts( array(
        'id' => '0',
        'element' => 'div'
    ), $atts ) );
    if ($id == "") return;

	// Visual composer bug, get the first instance ID
	if ($id == 99999) {
		if (isset($wpdb->base_prefix)) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}

		$search = $wpdb->get_results(
			"SELECT * FROM " . $_prefix . "ajaxsearchpro LIMIT 1",
			ARRAY_A
		);
		if (!isset($search[0]))
			return "";

		$id = $search[0]['id'] + 0;
	}

    return "<".$element." id='wpdreams_asp_settings_".$id."'></".$element.">";
}

function add_asp_two_column ( $atts ) {
	global $wpdb;

    extract( shortcode_atts( array(
        'id' => '0',
        'element' => 'div',
        'search_width' => 50,
        'results_width' => 50,
        'invert' => 0,
    ), $atts ) );
    if ($id == "") return;

	// Visual composer bug, get the first instance ID
	if ($id == 99999) {
		if (isset($wpdb->base_prefix)) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}

		$search = $wpdb->get_results(
			"SELECT * FROM " . $_prefix . "ajaxsearchpro LIMIT 1",
			ARRAY_A
		);
		if (!isset($search[0]))
			return "";

		$id = $search[0]['id'] + 0;
	}

    $search_width -= 2;
    $results_width -= 2;
    $s_extra_style = "";
    $r_extra_style = "";

    if ($search_width != 45 || $results_width != 45) {
        $s_extra_style = " style='width:".$search_width."%'";
        $r_extra_style = " style='width:".$results_width."%'";
    }

    if ($invert != 0) {
        return "
        <div class='asp_two_column'>
            <div class='asp_two_column_first'$r_extra_style>".do_shortcode('[wpdreams_ajaxsearchpro_results id='.$id.']')."</div>
            <div class='asp_two_column_last'$s_extra_style>".do_shortcode('[wpdreams_ajaxsearchpro id='.$id.']')."</div>
            <div style='clear: both;'></div>
        </div>
        ";
    } else {
        return "
        <div class='asp_two_column'>
            <div class='asp_two_column_first'$s_extra_style>".do_shortcode('[wpdreams_ajaxsearchpro id='.$id.']')."</div>
            <div class='asp_two_column_last'$r_extra_style>".do_shortcode('[wpdreams_ajaxsearchpro_results id='.$id.']')."</div>
            <div style='clear: both;'></div>
        </div>
        ";
    }
}