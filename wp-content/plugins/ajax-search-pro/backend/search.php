<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$params = array();

$_themes = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'themes.json');

if (isset($wpdb->base_prefix)) {
    $_prefix = $wpdb->base_prefix;
} else {
    $_prefix = $wpdb->prefix;
}

$search = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM ".$_prefix."ajaxsearchpro WHERE id=%d",
    $_GET['asp_sid']
), ARRAY_A );

$sd = json_decode($search['data'], true);
//var_dump($_sd);
$_def = get_option('asp_defaults');
$_dk = 'asp_defaults';
?>
<link rel="stylesheet" href="<?php echo ASP_URL . 'css/style.basic.css'; ?>" />

<script>
    (function ($) {
        $(document).ready(function () {

            $('.asp_b_shortcodes_menu').click(function(){
               $(this).parent().toggleClass('asp_open');
            });

            //var ajaxurl = '<?php bloginfo("url"); ?>' + "/wp-content/plugins/ajax-search-pro/ajax_search.php";

            jQuery(jQuery('.tabs a')[0]).trigger('click');
            $('.tabs a[tabid=6]').click(function () {
                $('.tabs a[tabid=601]').click();
            });
            $('.tabs a[tabid=1]').click(function () {
                $('.tabs a[tabid=101]').click();
            });
            $('.tabs a[tabid=4]').click(function () {
                $('.tabs a[tabid=401]').click();
            });
            $('.tabs a[tabid=3]').click(function () {
                $('.tabs a[tabid=301]').click();
            });
            $('.tabs a[tabid=5]').click(function () {
                $('.tabs a[tabid=501]').click();
            });
	        $('.tabs a[tabid=7]').click(function () {
		        $('.tabs a[tabid=701]').click();
	        });
            $('.tabs a[tabid=8]').click(function () {
                $('.tabs a[tabid=801]').click();
            });

            $('.tabs a').on('click', function(){
                $('#sett_tabid').val($(this).attr('tabid'));
            });


            <?php if (!empty($_POST['sett_tabid'])): ?>

            <?php if ($_POST['sett_tabid'] > 100): ?>

            $('.tabs a[tabid=<?php echo floor($_POST['sett_tabid'] / 100); ?>]').click();

            <?php endif; ?>

            $('.tabs a[tabid=<?php echo $_POST['sett_tabid']; ?>]').click();


            <?php else: ?>

            $('.tabs a[tabid=1]').click();

            <?php endif; ?>

            $('#wpdreams .settings').click(function () {
                $("#preview input[name=refresh]").attr('searchid', $(this).attr('searchid'));
            });
            $("select[id^=wpdreamsThemeChooser]").change(function () {
                $("#preview input[name=refresh]").click();
            });
            $("#preview .refresh").click(function (e) {
                e.preventDefault();
                var $this = $(this).parent();
                var id = <?php echo $_GET['asp_sid']; ?>;
                var loading = $('.big-loading', $this);

                // Remove duplicates first
                $('body>div[id^=ajaxsearchpro]').remove();

                $('.data', $this).html("");
                $('.data', $this).addClass('hidden');
                loading.removeClass('hidden');
                var data = {
                    action: 'ajaxsearchpro_preview',
                    asid: id,
                    formdata: $('form[name="asp_data"]').serialize()
                };
                $.post(ajaxurl, data, function (response) {
                    loading.addClass('hidden');
                    $('.data', $this).html(response);
                    $('.data', $this).removeClass('hidden');
                    ASP.initialize();
                    setTimeout(
                        function () {
                            if (typeof aspjQuery != 'undefined')
                                aspjQuery(window).resize();
                            else if (typeof jQuery != 'undefined')
                                jQuery(window).resize();
                        },
                        1000);
                });
            });

            $("#preview .maximise").click(function (e) {
                e.preventDefault();
                $this = $(this.parentNode);
                if ($(this).html() == "Show") {
                    $this.animate({
                        bottom: "-2px",
                        height: "90%"
                    });
                    $(this).html('Hide');
                    $("#preview a.refresh").trigger('click');
                } else {
                    $this.animate({
                        bottom: "-2px",
                        height: "40px"
                    });
                    $(this).html('Show');
                }
            });

            
            if (typeof ($.fn.spectrum) != 'undefined')
                $("#bgcolorpicker").spectrum({
                    showInput: true,
                    showPalette: true,
                    showSelectionPalette: true,
                    change: function (color) {
                        $("#preview").css("background", color.toHexString()); // #ff0000
                    }
                });

        });
    }(jQuery));
</script>

<div id="wpd_body_loader"><div id="wpd_loading_msg">Loading...</div></div>

<div id='preview'>
    <span>Preview</span>
    <a name='refresh' class='refresh' searchid='0' href='#'>Refresh</a>
    <a name='hide' class='maximise'/>Show</a>
    <label>Background: </label><input type="text" id="bgcolorpicker" value="#ffffff"/>

    <div style="text-align: center;
        margin: 11px 0 17px;
        font-size: 12px;
        color: #aaa;">Please note, that some functions may not work in preview mode.<br>The first loading can take up to
        15 seconds!
    </div>
    <div class='big-loading hidden'></div>
    <div class="data hidden asp_preview_data"></div>
</div>

<div id="wpdreams" class='wpdreams wrap'>
    <?php if (ASP_DEBUG == 1): ?>
        <p class='infoMsg'>Debug mode is on!</p>
    <?php endif; ?>

	<?php if (asp_updates()->needsUpdate()): ?>
		<p class='infoMsgBox'>Version <strong><?php echo asp_updates()->getVersionString(); ?></strong> is available.
			Download the new version from Codecanyon. <a target="_blank" href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/update_notes.html">How to update?</a></p>
	<?php endif; ?>

    <a class='back' href='<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/settings.php"; ?>'>Back
        to the search list</a>
    <a class='statistics'
       href='<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/statistics.php"; ?>'>Search
        Statistics</a>
    <a class='error' href='<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/comp_check.php"; ?>'>Compatibility
        checking</a>
    <a class='cache'
       href='<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/cache_settings.php"; ?>'>Caching
        options</a>
    <?php ob_start(); ?>
    <div class="wpdreams-box asp_b_shortcodes">
        <?php if (defined('ASL_PATH')): ?>
            <p class="errorMsg">Warning:  <strong>Ajax Search Lite</strong> is still activated, please deactivate it to assure every PRO feature works properly.</p>
        <?php endif; ?>

        <div class="asp_b_shortcodes_menu">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18px" height="24px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
              <polygon id="arrow-25-icon" transform = "rotate(90 256 256)" points="142.332,104.886 197.48,50 402.5,256 197.48,462 142.332,407.113 292.727,256 "/>
            </svg>
            <span class="asp_b_shortcodes_title">Toggle shortcodes for <strong><?php echo $search['name']; ?></strong></span>
        </div>
        <fieldset>
            <legend>Simple shortcodes</legend>
            <label class="shortcode">Search shortcode:</label>
            <input type="text" class="shortcode" value="[wpdreams_ajaxsearchpro id=<?php echo $search['id']; ?>]"
                   readonly="readonly"/>
            <label class="shortcode">Search shortcode for templates:</label>
            <input type="text" class="shortcode"
                   value="&lt;?php echo do_shortcode('[wpdreams_ajaxsearchpro id=<?php echo $search['id']; ?>]'); ?&gt;"
                   readonly="readonly"/>
        </fieldset>
        <fieldset>
            <legend>Result shortcodes</legend>
            <p style='margin:19px 10px 9px;'>Shortcodes for placing the result box elsewhere. (only works if the result
                layout position is <b>block</b> - see in layout options tab)</p>
            <label class="shortcode">Result box shortcode:</label>
            <input type="text" class="shortcode"
                   value="[wpdreams_ajaxsearchpro_results id=<?php echo $search['id']; ?> element='div']"
                   readonly="readonly"/>
            <label class="shortcode">Result shortcode for templates:</label>
            <input type="text" class="shortcode"
                   value="&lt;?php echo do_shortcode('[wpdreams_ajaxsearchpro_results id=<?php echo $search['id']; ?> element=&quot;div&quot;]'); ?&gt;"
                   readonly="readonly"/>
        </fieldset>
        <fieldset>
            <legend>Settings shortcodes</legend>
            <p style='margin:19px 10px 9px;'>Shortcodes for placing the settings box elsewhere.</p>
            <label class="shortcode">Settings box shortcode:</label>
            <input type="text" class="shortcode"
                   value="[wpdreams_asp_settings id=<?php echo $search['id']; ?> element='div']"
                   readonly="readonly"/>
            <label class="shortcode">Shortcode for templates:</label>
            <input type="text" class="shortcode"
                   value="&lt;?php echo do_shortcode('[wpdreams_asp_settings id=<?php echo $search['id']; ?> element=&quot;div&quot;]'); ?&gt;"
                   readonly="readonly"/>
        </fieldset>
        <fieldset>
            <legend>Two Column Shortcode</legend>
            <p style='margin:19px 10px 9px;'>Will place a search box (left) and a result box (right) next to each other, like the one on the demo front page.</p>
            <label class="shortcode">TC shortcode:</label>
            <input type="text" class="shortcode"
                   value="[wpdreams_ajaxsearchpro_two_column id=<?php echo $search['id']; ?> search_width=50 results_width=50 invert=0 element='div']"
                   readonly="readonly"/>
            <label class="shortcode">TC shortcode for templates:</label>
            <input type="text" class="shortcode"
                   value="&lt;?php echo do_shortcode('[wpdreams_ajaxsearchpro_two_column id=<?php echo $search['id']; ?> search_width=50 results_width=50 invert=0 element=&quot;div&quot;]'); ?&gt;"
                   readonly="readonly"/>
            <p style='margin:19px 10px 9px;'><strong>Extra Parameters</strong></p>
            <ul style='margin:19px 10px 9px;'>
                <li>search_width - {integer} the search bar width (in %, not px)</li>
                <li>results_width - {integer} the results box width (in %, not px)</li>
                <li>invert - {0 or 1} inverts the search and results box position from left to right</li>
            </ul>
        </fieldset>
    </div>
    <div class="wpdreams-box">
        <form action='' method='POST' name='asp_data'>
            <ul id="tabs" class='tabs'>
                <li><a tabid="1" class='current general'>General Options</a></li>
                <li><a tabid="2" class='multisite'>Multisite Options</a></li>
                <li><a tabid="3" class='frontend'>Frontend Search Settings</a></li>
                <li><a tabid="4" class='layout'>Layout options</a></li>
                <li><a tabid="5" class='autocomplete'>Autocomplete & Suggestions</a></li>
                <li><a tabid="6" class='theme'>Theme options</a></li>
                <li><a tabid="8" class='advanced'>Relevance options</a></li>
                <li><a tabid="7" class='advanced'>Advanced options</a></li>
            </ul>
            <div id="content" class='tabscontent'>
                <div tabid="1">
                    <fieldset>
                        <legend>Genearal Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/general_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="2">
                    <fieldset>
                        <legend>Multisite Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/multisite_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="3">
                    <fieldset>
                        <legend>Frontend Search Settings options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/frontend_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="4">
                    <fieldset>
                        <legend>Layout Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/layout_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="5">
                    <fieldset>
                        <legend>Autocomplete & Suggestions</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/autocomplete_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="6">
                    <fieldset>
                        <legend>Theme Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/theme_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="8">
                    <fieldset>
                        <legend>Relevance Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/relevance_options.php"); ?>

                    </fieldset>
                </div>
                <div tabid="7">
                    <fieldset>
                        <legend>Advanced Options</legend>

                        <?php include(ASP_PATH . "backend/tabs/instance/advanced_options.php"); ?>

                    </fieldset>
                </div>
            </div>
            <input type="hidden" name="sett_tabid" id="sett_tabid" value="1" />
        </form>
    </div>
    <?php $output = ob_get_clean(); ?>
    <?php
    if (isset($_POST['submit_' . $search['id']])) {
        
        $params = wpdreams_parse_params($_POST);
        
        //print_r($params);
        $data = wd_mysql_escape_mimic(json_encode($params));
        //print_r($_POST);
        $search['id'] = (int)$search['id']; // secure the parameter

        $wpdb->query("
            UPDATE " . $_prefix . "ajaxsearchpro
            SET data = '" . $data . "'
            WHERE id = " . $search['id'] . "
        ");

        $style = $params;
        $id = $search['id'];

	    asp_register_wpml_translations($params);
        asp_generate_the_css();

        echo "<div class='successMsg'>Search settings saved!</div>";
    }
    echo $output;
    ?>
</div>      