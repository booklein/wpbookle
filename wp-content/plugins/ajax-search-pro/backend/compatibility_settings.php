<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$com_options = get_option('asp_compatibility');
$ful_options = get_option('asp_fulltexto');

$fulltext = wpdreamsFulltext::getInstance();

/**
 * @deprecated since 4.5
 * $fulltext_enabled = $fulltext->check(array('posts'));
 * $fulltext_indexed = $fulltext->indexExists('posts', 'asp_title');
 */
$fulltext_enabled = false;
$fulltext_indexed = false;

if (ASP_DEMO) $_POST = null;
?>

<div id="wpdreams" class='wpdreams wrap'>
    <div class="wpdreams-box">

        <?php ob_start(); ?>

        <div tabid="1">
            <fieldset>
                <legend>CSS and JS compatibility</legend>

                <?php include(ASP_PATH . "backend/tabs/compatibility/cssjs_options.php"); ?>

            </fieldset>
        </div>
        <div tabid="2">
            <fieldset>
                <legend>Query compatibility options</legend>

                <?php include(ASP_PATH . "backend/tabs/compatibility/query_options.php"); ?>

            </fieldset>
        </div>

        <?php $_r = ob_get_clean(); ?>


        <?php ob_start(); ?>

        <?php if($fulltext_enabled): ?>
        <div tabid="3">

            <?php if(!$fulltext_indexed): ?>
                <fieldset>
                    <legend>Create Fulltext Indexes</legend>
                    <div class="item">
                        <p class='infoMsg'>Will create the fulltext indexes on the <b>posts</b> table. It can take a long time!</p>
                        <input type='submit' class="red" name='Create Indexes' id='createindexes' value='Create Indexes'>
                    </div>
                </fieldset>
            <?php else: ?>
                <fieldset>
                    <legend>Remove Fulltext Indexes</legend>
                    <div class="item">
                        <p class='psuccessMsg'>You have fulltext indexes created!</p>
                        <p class='infoMsg'>Will remove the fulltext indexes from the <b>posts</b> table.</p>
                        <input type='submit' class="red" name='Remove Indexes' id='removeindexes' value='Remove Indexes'>
                    </div>
                </fieldset>
            <?php endif; ?>


            <fieldset>
                <legend>Fulltext search options</legend>
                <div class="item">
                    <p class='infoMsg'>Only recommended for big databases.</p>
                    <?php $o = new wpdreamsYesNo("dbusefulltext", "Use fulltext search when possible",
                        wpdreams_setval_or_getoption($ful_options, 'dbusefulltext', 'asp_fulltext_def')
                    ); ?>
                </div>
                <div class="item">
                    <p class='infoMsg'>Only set to YES if you have performance issues!</p>
                    <?php $o = new wpdreamsYesNo("dbuseregularwhenshort", "Use regular search if the phrase is lower then the min. char count, instead of Boolean mode ",
                        wpdreams_setval_or_getoption($ful_options, 'dbuseregularwhenshort', 'asp_fulltext_def')
                    ); ?>
                </div>
                <div class="item">
                    <input type='submit' class='submit' value='Save options'/>
                </div>
                <input type='hidden' name='asp_fulltext' value='1' />
            </fieldset>
        </div>
        <?php endif; ?>

        <?php $_fr = ob_get_clean(); ?>


        <?php

        // Compatibility stuff
        $updated = false;
        if (isset($_POST) && isset($_POST['asp_compatibility']) && (wpdreamsType::getErrorNum() == 0)) {
            $values = array(
                // CSS and JS
                "js_source" => $_POST['js_source'],
                "load_in_footer" => $_POST['load_in_footer'],
                "css_compatibility_level" => $_POST['css_compatibility_level'],
                "forceinlinestyles" => $_POST['forceinlinestyles'],
                "css_async_load" => $_POST['css_async_load'],
                "loadpolaroidjs" => $_POST['loadpolaroidjs'],
                "load_noui_js" => $_POST['load_noui_js'],
                "load_isotope_js" => $_POST['load_isotope_js'],
                "usecustomajaxhandler" => $_POST['usecustomajaxhandler'],
                // Query options
                "db_force_case" => $_POST['db_force_case'],
                "db_force_unicode" => $_POST['db_force_unicode'],
                "db_force_utf8_like" => $_POST['db_force_utf8_like'],
            );
            update_option('asp_compatibility', $values);
            $updated = true;
            asp_generate_the_css();
        }

        // Fulltext stuff

        if (isset($_POST) && isset($_POST['asp_fulltext']) && (wpdreamsType::getErrorNum()==0)) {
            $values = array(
                "dbusefulltext" => $_POST['dbusefulltext'],
                "dbuseregularwhenshort" => $_POST['dbuseregularwhenshort']
            );
            update_option('asp_fulltexto', $values);
            $updated = true;
        }
        ?>

        <?php
        $_comp = wpdreamsCompatibility::Instance();
        if ($_comp->has_errors()):
            ?>
            <div class="wpdreams-slider errorbox">
                <p class='errors'>Possible incompatibility! Please go to the <a
                        href="<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/comp_check.php"; ?>">error
                        check</a> page to see the details and solutions!</p>
            </div>
        <?php endif; ?>
        <div class='wpdreams-slider'>

            <?php if ($updated): ?>
                <div class='successMsg'>Search compatibility settings successfuly updated!</div><?php endif; ?>

            <?php if (ASP_DEMO): ?>
                <p class="infoMsg">DEMO MODE ENABLED - Please note, that these options are read-only</p>
            <?php endif; ?>

            <ul id="tabs" class='tabs'>
                <li><a tabid="1" class='current multisite'>CSS and JS compatibility</a></li>
                <li><a tabid="2" class='general'>Query compatibility options</a></li>
                <?php if($fulltext_enabled): ?>
                <li><a tabid="3" class='general'>Fulltext options</a></li>
                <?php endif; ?>
            </ul>

            <div id="content" class='tabscontent'>

            <!-- Compatibility form -->
            <form name='compatibility' method='post'>

                <?php print $_r; ?>

                <div class="item">
                    <input type='submit' class='submit' value='Save options'/>
                </div>
                <input type='hidden' name='asp_compatibility' value='1'/>
            </form>

            <!-- FullText form -->
            <form name='asp_fulltext1' method='post'>

                <?php print $_fr; ?>

            </form>

            </div>
        </div>
    </div>
</div>
<script>
    // Simulate a click on the first element to initialize the tabs
    jQuery(function ($) {
        $('.tabs a[tabid=1]').click();
    });

    jQuery(document).ready((function($) {

        $('#createindexes').on('click', function(e){
            e.preventDefault();

            var r = confirm('Do you really want to start creating indexes?');
            if (r!=true) return;
            var button = $(this);
            var data = {
                action: 'ajaxsearchpro_activate_fulltext'
            };
            button.attr("disabled", true);

            button.attr("value", "Loading...");
            button.addClass('blink');
            $.post(ASP.backend_ajaxurl, data, function(response) {
                button.parent().append(response);
                button.attr("value", "Done!");
                button.removeClass('blink');
            }, "text");
        });

        $('#removeindexes').on('click', function(e){
            e.preventDefault();

            var r = confirm('Do you really want to remove indexes?');
            if (r!=true) return;
            var button = $(this);
            var data = {
                action: 'ajaxsearchpro_deactivate_fulltext'
            };
            button.attr("disabled", true);

            button.attr("value", "Loading...");
            button.addClass('blink');
            $.post(ASP.backend_ajaxurl, data, function(response) {
                button.parent().append(response);
                button.attr("value", "Done!");
                button.removeClass('blink');
            }, "text");
        });

    })(jQuery));

</script>