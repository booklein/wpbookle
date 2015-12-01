<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (isset($_GET) && isset($_GET['asp_sid'])) {
    include('search.php');
    return;
}

global $sliders;
global $wpdb;

$params = array();

if (isset($wpdb->base_prefix)) {
    $_prefix = $wpdb->base_prefix;
} else {
    $_prefix = $wpdb->prefix;
}

$_comp = wpdreamsCompatibility::Instance();
?>
<div id="wpdreams" class='wpdreams wrap'>

    <?php if (defined('ASL_PATH')): ?>
        <p class="errorMsg">Warning: Please deactivate the Ajax Search Lite to assure every PRO feature works properly.</p>
    <?php endif; ?>

    <?php if ($_comp->has_errors()): ?>
        <div class="wpdreams-box errorbox">
            <p class='errors'>Possible incompatibility! Please go to the <a
                    href="<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/comp_check.php"; ?>">error
                    check</a> page to see the details and solutions!</p>
        </div>
    <?php endif; ?>

	<?php if (asp_updates()->needsUpdate()): ?>
		<p class='infoMsgBox'>Version <strong><?php echo asp_updates()->getVersionString(); ?></strong> is available.
			Download the new version from Codecanyon. <a target="_blank" href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/update_notes.html">How to update?</a></p>
	<?php endif; ?>

    <div class="wpdreams-box">
        <form name="add-slider" action="" method="POST">
            <fieldset>
                <legend>Create a new search instance</legend>
                <?php
                $new_slider = new wpdreamsText("addsearch", "Search form name:", "", array(array("func" => "wd_isEmpty", "op" => "eq", "val" => false)), "Please enter a valid form name!");
                ?>
                <input name="submit" type="submit" value="Add"/>
                <?php
                if (isset($_POST['addsearch']) && !$new_slider->getError()) {
                    $_search_default = get_option('asp_defaults');

                    $wpdb->query(
                        "INSERT INTO " . $_prefix . "ajaxsearchpro
                        (name, data) VALUES
                        ('" . esc_sql($_POST['addsearch']) . "', '" . wd_mysql_escape_mimic(json_encode($_search_default)) . "')"
                    );
                    $id = $wpdb->insert_id;
                    asp_generate_the_css();
                    echo "<div class='successMsg'>Search Form Successfuly added!</div>";
                }
                if (isset($_POST['instance_new_name'])
                    && isset($_POST['instance_id'])
                ) {
                    if ($_POST['instance_new_name'] != ''
                        && strlen($_POST['instance_new_name']) > 0
                    ) {
                        $wpdb->query(
                            $wpdb->prepare("UPDATE " . $_prefix . "ajaxsearchpro SET name = '%s' WHERE id = %d", $_POST['instance_new_name'], $_POST['instance_id'])
                        );
                        echo "<div class='infoMsg'>Form name changed!</div>";
                    } else {
                        echo "<div class='errorMsg'>Failure. Form name must be at least 1 character long</div>";
                    }
                }
                if (isset($_POST['instance_copy_id'])) {
                    if ($_POST['instance_copy_id'] != '') {
                        $wpdb->query(
                            $wpdb->prepare("INSERT INTO " . $_prefix . "ajaxsearchpro( name, data ) SELECT CONCAT(name, ' duplicate'), data FROM " . $_prefix . "ajaxsearchpro WHERE id=%d;", $_POST['instance_copy_id'])
                        );
                        echo "<div class='infoMsg'>Form duplicated!</div>";
                    } else {
                        echo "<div class='errorMsg'>Failure :(</div>";
                    }
                }
                ?>
            </fieldset>
        </form>
    </div>

    <?php

    if (isset($_POST['delete'])) {
        $wpdb->query("DELETE FROM " . $_prefix . "ajaxsearchpro WHERE id=" . $_POST['did']);
    }


    $searchforms = $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
    $i = 0;
    if (is_array($searchforms))
        foreach ($searchforms as $search) {
            $search['data'] = json_decode($search['data'], true);
            $i++;
            // Needed for the tabindex for the CSS :focus to work with div
            ?>
            <div class="wpdreams-box" tabindex="<?php echo $i; ?>">
                <div class="slider-info">
                    <a href='<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/settings.php"; ?>&asp_sid=<?php echo $search['id']; ?>'><img
                            title="Click on this icon for search settings!"
                            src="<?php echo plugins_url('/settings/assets/icons/settings.png', __FILE__) ?>"
                            class="settings" searchid="<?php echo $search['id']; ?>"/></a>
                    <img title="Click here if you want to delete this search!"
                         src="<?php echo plugins_url('/settings/assets/icons/delete.png', __FILE__) ?>" class="delete"/>

                    <form name="polaroid_slider_del_<?php echo $search['id']; ?>" action="" style="display:none;"
                          method="POST">
                        <?php
                        new wpdreamsHidden("z", "z", time());
                        new wpdreamsHidden("delete", "delete", "delete");
                        new wpdreamsHidden("did", "did", $search['id']);
                        ?>
                    </form>
                <span class="wpd_instance_name"><?php
                  echo $search['name'];
                  ?>
                </span>

                <form style="display: inline" name="instance_new_name_form" class="instance_new_name_form"
                      method="post">
                    <input type="text" class="instance_new_name" name="instance_new_name"
                           value="<?php echo $search['name']; ?>">
                    <input type="hidden" name="instance_id" value="<?php echo $search['id']; ?>"/>
                    <img title="Click here to rename this form!"
                         src="<?php echo plugins_url('/settings/assets/icons/edit24x24.png', __FILE__) ?>"
                         class="wpd_instance_edit_icon"/>
                </form>
                <form style="display: inline" name="instance_copy_form" class="instance_copy_form"
                      method="post">
                    <input type="hidden" name="instance_copy_id" value="<?php echo $search['id']; ?>"/>
                    <img title="Click here to duplicate this form!"
                         src="<?php echo plugins_url('/settings/assets/icons/duplicate18x18.png', __FILE__) ?>"
                         class="wpd_instance_edit_icon"/>
                </form>
                <span style='float:right;'>
                 <label class="shortcode">Quick shortcode:</label>
                 <input type="text" class="quick_shortcode" value="[wpdreams_ajaxsearchpro id=<?php echo $search['id']; ?>]"
                        readonly="readonly"/>
                </span>
                </div>

                <form name="polaroid_slider_<?php echo $search['id']; ?>" action="" method="POST">

                </form>

            </div>
        <?php


        }
    ?>
    <script>
        jQuery(function ($) {
            $('input.instance_new_name').focus(function () {
                $(this).parent().prev().css('display', 'none');
            }).blur(function () {
                    $(this).parent().prev().css('display', '');
                });
            $('.instance_new_name_form').submit(function () {
                if (!confirm('Do you want to change the name of this form?'))
                    return false;
            });
            $('.instance_copy_form').submit(function () {
                if (!confirm('Do you want to duplicate this form?'))
                    return false;
            });
            $('.wpd_instance_edit_icon').click(function () {
                $(this).parent().submit();
            });
        });
    </script>
</div>

