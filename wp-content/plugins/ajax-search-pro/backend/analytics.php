<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

$ana_options = get_option('asp_analytics');
?>

<div id="wpdreams" class='wpdreams wrap'>
    <div class="wpdreams-box">
        <?php ob_start(); ?>
        <div class="item">
            <?php $o = new wpdreamsYesNo("analytics", "Enable search Google Analytics integration?",
                wpdreams_setval_or_getoption($ana_options, 'analytics', 'asp_analytics_def')
            ); ?>
        </div>
        <div class="item">
            <?php $o = new wpdreamsText("analytics_string", "Google analytics pageview string",
                wpdreams_setval_or_getoption($ana_options, "analytics_string", 'asp_analytics_def')
            ); ?>
            <p class='infoMsg'>
                This is how the pageview will look like on the google analytics website. Use the {asp_term} variable to add the search term to the pageview.
            </p>
        </div>
        <div class="item">
            <input type='submit' class='submit' value='Save options'/>
        </div>
        <?php $_r = ob_get_clean(); ?>

        <?php
        $updated = false;
        if (isset($_POST) && isset($_POST['asp_analytics']) && (wpdreamsType::getErrorNum()==0)) {
            print "saving!";
            $values = array(
                "analytics" => $_POST['analytics'],
                "analytics_string" => $_POST['analytics_string']
            );
            update_option('asp_analytics', $values);
            $updated = true;
        }
        ?>
        <?php
        $_comp = wpdreamsCompatibility::Instance();
        if ($_comp->has_errors()):
            ?>
            <div class="wpdreams-slider errorbox">
                <p class='errors'>Possible incompatibility! Please go to the <a href="<?php echo get_admin_url()."admin.php?page=ajax-search-pro/backend/comp_check.php"; ?>">error check</a> page to see the details and solutions!</p>
            </div>
        <?php endif; ?>
        <div class='wpdreams-slider'>
            <form name='asp_analytics1' method='post'>
                <?php if($updated): ?><div class='successMsg'>Analytics options successfuly updated!</div><?php endif; ?>
                <fieldset>
                    <legend>Analytics options</legend>
                    <?php print $_r; ?>
                    <input type='hidden' name='asp_analytics' value='1' />
                </fieldset>
                <fieldset>
                    <legend>Result</legend>
                    <p class='infoMsg'>
                        After some time you should be able to see the hits on your analytics board.
                    </p>
                    <img src="http://i.imgur.com/s7BXiPV.png">
                </fieldset>
            </form>
        </div>
    </div>
</div>