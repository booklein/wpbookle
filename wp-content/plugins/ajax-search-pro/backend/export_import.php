<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (isset($wpdb->base_prefix)) {
    $_prefix = $wpdb->base_prefix;
} else {
    $_prefix = $wpdb->prefix;
}

$errormsg = '';
$import_count = 0;
$sett_import_count = 0;

if (isset($_POST['asp_import_textarea'])) {
    if (empty($_POST['asp_import_textarea'])) {
        $errormsg = 'Import data is empty.';
    } else {
        if (false === $import_count = asp_import_instances($_POST['asp_import_textarea']))
            $errormsg = 'Cannot import. Invalid data! Please try again!';
    }
}

if (isset($_POST['asp_import_textarea_sett'])) {
    if (empty($_POST['asp_import_textarea_sett'])) {
        $errormsg = 'Import data is empty.';
    } else {
        if (false === $sett_import_count = asp_import_settings($_POST['asp_import_sett'], $_POST['asp_import_textarea_sett']))
            $errormsg = 'Cannot import. Invalid data! Please try again!';
    }
}

$exported_instances = asp_get_all_exported_instances();
$search_instances = $wpdb->get_results("SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A);
?>

<div id="wpdreams" class='wpdreams wrap'>
    <div class="wpdreams-box">
        <?php if ($errormsg != ''): ?>
            <div class="errorMsg"><?php echo $errormsg; ?></div>
        <?php endif; ?>
        <?php if ($import_count > 0): ?>
            <div class='infoMsg'>Succesfully imported <b><?php echo $import_count; ?></b> search instances.</div>
        <?php endif; ?>
        <?php if ($sett_import_count > 0): ?>
            <div class='infoMsg'>Succesfully imported the search settings.</div>
        <?php endif; ?>
        <ul id="tabs" class='tabs'>
            <li><a tabid="1" class='current multisite'>Instances</a></li>
            <li><a tabid="2" class='general'>Settings Only</a></li>
        </ul>
        <div id="content" class='tabscontent'>
            <div tabid="1">
                <fieldset>
                    <legend>Instances</legend>
                    <p class="biggerDescMsg">On this tab you can import complete search instances. If you prefer to import settings only, click on the Settings Only button on the top of this page.</p>
                    <fieldset>
                        <legend>Export Search instances</legend>
                        <label for="asp_export" style="width: 335px;display: inline-block;">Select the search instances to export</label>
                        <label style="text-align: right;" for="asp_export_textarea">Copy and save the text appearing in this box</label><br>
                        <select id="asp_export" multiple>
                            <?php foreach ($search_instances as $instance): ?>
                                <option value="<?php echo $instance['id']; ?>"><?php echo $instance['name']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <textarea id="asp_export_textarea" class="wd-export-import"></textarea>
                        <input id='asp_export_button' type='button' class='submit' value='Export!'/><span class="small-loading hiddend"></span>
                        <?php foreach ($search_instances as $instance): ?>
                            <div style="display:none" id="asp_exid<?php echo $instance['id']; ?>"><?php echo $exported_instances[$instance['id']]; ?></div>
                        <?php endforeach; ?>
                    </fieldset>
                    <form name="asp_import_instances" method="post" enctype='application/json'>
                    <fieldset>
                        <legend>Import Search instances</legend>
                        <p class="biggerDescMsg">Please note that the search ID's may differ from the exported instances. The imported instance names will have the "Imported" string appended after their names.</p>
                        <label for="asp_import_textarea">Paste the exported code here</label><br>
                        <textarea id="asp_import_textarea" name="asp_import_textarea" class="wd-export-import"></textarea>
                        <br><input id='asp_import_button' type='submit' class='submit' value='Import!'/>
                    </fieldset>
                    </form>
                </fieldset>
            </div>
            <div tabid="2">
                <fieldset>
                    <legend>Settings only</legend>
                    <p class="biggerDescMsg">On this page you can import settings to existing search instances. The import process will overwrite your current search options!</p>
                    <fieldset>
                        <legend>Export settings</legend>
                        <label for="asp_export_sett" style="width: 335px;display: inline-block;">Select the instance to export settings from</label>
                        <label style="text-align: right;" for="asp_export_textarea_sett">Copy and save the text appearing in this box</label><br>
                        <select id="asp_export_sett">
                            <?php foreach ($search_instances as $instance): ?>
                                <option value="<?php echo $instance['id']; ?>"><?php echo $instance['name']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <textarea id="asp_export_textarea_sett" class="wd-export-import"></textarea>
                        <input id='asp_export_button_sett' type='button' class='submit' value='Export!'/><span class="small-loading hiddend"></span>
                    </fieldset>
                    <form name="asp_import_instances_sett" method="post" enctype='application/json'>
                        <fieldset>
                            <legend>Import settings</legend>
                            <label for="asp_import_sett" style="width: 335px;display: inline-block;">Select the instance to import settings to</label>
                            <label style="text-align: right;" for="asp_import_textarea_sett">Paste the exported settings code here</label><br>
                            <select id="asp_import_sett" name="asp_import_sett">
                                <?php foreach ($search_instances as $instance): ?>
                                    <option value="<?php echo $instance['id']; ?>"><?php echo $instance['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <textarea id="asp_import_textarea_sett" name="asp_import_textarea_sett" class="wd-export-import"></textarea>
                            <br><input id='asp_import_button_sett' type='submit' class='submit' value='Import!'/>
                        </fieldset>
                    </form>
                </fieldset>
            </div>
        </div>

    </div>
</div>

<style>
    #asp_export {
        min-width: 350px;
        max-width: 350px;
        min-height: 150px;
        max-height: 150px;
        vertical-align: top;
    }

    #asp_export_sett,
    #asp_import_sett {
        min-width: 350px;
        max-width: 350px;
        vertical-align: top;
    }

    textarea.wd-export-import {
        margin-left: 40px;
    }

    input#asp_export_button,
    input#asp_import_button {
        margin: 13px 6px;
    }

    input#asp_export_button[disabled] {
        opacity: 0.6;
    }

    #wpdreams span.small-loading {
        vertical-align: middle;
        margin: 0 20px;
        display: inline-block;
    }

    #wpdreams span.hiddend {
        display: none;
    }

    #wpdreams .asp_succ_import {
        display: inline;
        vertical-align: top;
        padding: 0 10px;
    }
</style>
<script>
    jQuery(function($){

        $('#asp_export_textarea').click(function(){
            var $this = $(this);
            $this.select();

            // Work around Chrome's little problem
            $this.mouseup(function() {
                // Prevent further mouseup intervention
                $this.unbind("mouseup");
                return false;
            });
        });

        $('#asp_export_button').click(function(){
            if ($('#asp_export :selected').length < 1) return false;
            $(this).next().removeClass('hiddend');
            $(this).prop('disabled', true);
            var $this = $(this);
            setTimeout(function(){
                var searches = [];
                $('#asp_export :selected').each(function(i, selected){
                    searches.push( $('#asp_exid' + $(selected).val()).html() );
                });
                if (searches.length > 0)
                    $('#asp_export_textarea').val(JSON.stringify(searches));
                $this.prop('disabled', false);
                $this.next().addClass('hiddend');
            }, 1000);
        });

        $('#asp_export_button_sett').click(function(){
            if ($('#asp_export_sett :selected').length < 1) return false;
            $(this).next().removeClass('hiddend');
            $(this).prop('disabled', true);
            var $this = $(this);
            setTimeout(function(){
                $('#asp_export_textarea_sett').val($('#asp_exid' + $('#asp_export_sett').val()).html());
                $this.prop('disabled', false);
                $this.next().addClass('hiddend');
            }, 1000);
        });

        $('#asp_import_button_sett').click(function(e){
            return confirm('Are you sure you want to import settings to that search instance?');
        });

        $('.tabs a[tabid=1]').click();
    });
</script>