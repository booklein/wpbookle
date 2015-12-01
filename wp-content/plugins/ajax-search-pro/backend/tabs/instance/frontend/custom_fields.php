<?php

$custom_fields = w_get_custom_fields();

?>
<script>
    jQuery(function($) {
        var sortableCont = $("#csf_sortable");
        var $deleteIcon = $("<a class='deleteIcon'></a>");
        var $editIcon = $("<a class='editIcon'></a>");
        var resetValues = {};
        var $current = null;

        //$('#asp_edit_field').fadeOut(0);

        // Store defaults
        $('#asp_new_field input, #asp_new_field select, #asp_new_field textarea').each(function(){
           resetValues[$(this).attr('name')] = $(this).val();
        });

        // Fields for checking
        var fields = ['asp_f_title'];

        function checkEmpty() {

            var empty = false;
            $(fields).each(function () {
                if ($('#asp_new_field *[name="' + this.toString() + '"]').val() == '') {
                    $('#asp_new_field *[name="' + this.toString() + '"]').addClass('missing');
                    empty = true;
                }
            });
            return empty;
        }
        $('#asp_new_field').click(function(e){
            if ($(e.target).attr('name') == 'add') return;
            $(fields).each(function () {
                    $('#asp_new_field *[name="' + this.toString() + '"]').removeClass('missing');
            });
        });



            /* Type change */
        $('select[name="asp_f_type"]').on('change', function(){
            var id = $(this).parent().parent()[0].id;
            $('#' + id + ' .asp_f_type').addClass('hiddend');
            $('#' + id + ' .asp_f_' + $(this).val()).removeClass('hiddend');
            if ($(this).val() == 'slider') {
                $($('#' + id + ' .asp_f_operator optgroup')[1]).addClass('hiddend');
                $('#' + id + ' .asp_f_operator select').val('eq');
            } else {
                $($('#' + id + ' .asp_f_operator optgroup')[1]).removeClass('hiddend');
            }
            if ($(this).val() == 'checkboxes') {
                $('#' + id + ' .asp_f_operator select').val('like');
            }
            if ($(this).val() == 'range') {
                $('#' + id + ' .asp_f_operator').addClass('hiddend');
            } else {
                $('#' + id + ' .asp_f_operator').removeClass('hiddend');
            }
        });
        /* Reset it on page load */
        $('select[name="asp_f_type"]').change();

        /* Sortable */
        sortableCont.sortable({
        }, {
            update: function (event, ui) {
                var parent = $('#asp_new_field').parent();
                 var items = $('#csf_sortable li');
                 var hidden = $('input[name=custom_field_items]', parent);
                 //console.log(items, hidden);
                 var val = "";
                 items.each(function () {
                    val += "|" + $(this).attr('custom-data');
                 });
                 val = val.substring(1);
                 hidden.val(val);
            }
        }).disableSelection();

        // Add the items to the sortable on initialisation
        var fields_val = $('input[name=custom_field_items]').val();
        if (typeof(fields_val) != 'undefined' && fields_val != '') {
            var items = fields_val.split('|');
            $.each(items, function(key, value){
                vals = JSON.parse(Base64.decode(value));
                var $li = $("<li class='ui-state-default'/>").html(vals.asp_f_title + "<a class='deleteIcon'></a><a class='editIcon'></a>");
                $li.attr("custom-data", value);
                sortableCont.append($li);
            });
            sortableCont.sortable("refresh");
            sortableCont.sortable('option', 'update').call(sortableCont);
        }


        // Add new item
        $('#asp_new_field button[name=add]').click(function(){
            var data = {};

            if (checkEmpty() == true) return;

            $('#asp_new_field input, #asp_new_field select, #asp_new_field textarea').each(function(){
                if ($(this).parent().hasClass('hiddend')) return;
                if ($(this).attr('type') == 'checkbox') {
                    if ($(this).prop('checked') == true)
                        data[$(this).attr('name')] = 'asp_checked';
                    else
                        data[$(this).attr('name')] = 'asp_unchecked';
                } else {
                    data[$(this).attr('name')] = $(this).val();
                }
            });

            var $li = $("<li class='ui-state-default'/>")
                .html(data.asp_f_title + "<a class='deleteIcon'></a><a class='editIcon'></a>");
            $li.attr("custom-data", Base64.encode(JSON.stringify(data)));

            sortableCont.append($li);
            sortableCont.sortable("refresh");
            sortableCont.sortable('option', 'update').call(sortableCont);
        });

        // Remove item
        $('#csf_sortable').on('click', 'li a.deleteIcon', function(){
            $(this).parent().remove();
            sortableCont.sortable("refresh");
            sortableCont.sortable('option', 'update').call(sortableCont);
            $('#asp_edit_field button[name=back]').click();
        });

        // Edit item
        $('#csf_sortable').on('click', 'li a.editIcon', function(e){
            $('#asp_new_field').fadeOut(0);
            $('#asp_edit_field').fadeIn();
            $current = $(e.target).parent();
            var data = JSON.parse(Base64.decode($current.attr("custom-data")));
            $('#asp_edit_title').text(data.asp_f_title);

            $.each(data, function(key, val){
                if (val == 'asp_checked') {
                    $('#asp_edit_field *[name=' + key + ']').prop('checked', true);
                } else if (val == 'asp_unchecked') {
                    $('#asp_edit_field *[name=' + key + ']').prop('checked', false);
                } else {
                    $('#asp_edit_field *[name=' + key + ']').val(val);
                }
                if (key == 'asp_f_type')
                    $('#asp_edit_field select[name=asp_f_type]').change();
            });

        });

        // Back to new
        $('#asp_edit_field button[name=back]').click(function(){
            $('#asp_edit_field').fadeOut(0);
            $('#asp_new_field').fadeIn();
        });

        // Save modifications
        $('#asp_edit_field button[name=save]').click(function(){
            var data = {};
            $('#asp_edit_field input, #asp_edit_field select, #asp_edit_field textarea').each(function(){
                if ($(this).parent().hasClass('hiddend')) return;

                if ($(this).attr('type') == 'checkbox') {
                    if ($(this).prop('checked') == true)
                        data[$(this).attr('name')] = 'asp_checked';
                    else
                        data[$(this).attr('name')] = 'asp_unchecked';
                } else {
                    data[$(this).attr('name')] = $(this).val();
                }

            });
            $current.attr("custom-data", Base64.encode(JSON.stringify(data)));

            sortableCont.sortable("refresh");
            sortableCont.sortable('option', 'update').call(sortableCont);
            $('#asp_edit_field button[name=back]').click();
        });

        // Reset Values
        $('#asp_new_field button[name=reset]').click(function(){
            //console.log(resetValues);
            $('#asp_new_field input, #asp_new_field select, #asp_new_field textarea').each(function(){
                $(this).val(resetValues[$(this).attr('name')]);
            });
        });



    });
</script>
<div class="wpd-60-pc customContent">

    <fieldset class="wpd-text-right" id="asp_new_field">
        <legend>Add new item</legend>
        <div class='one-item'>
            <label for='asp_f_title'>Title label</label>
            <input type='text' placeholder="Title here.." name='asp_f_title'/>
        </div>
        <div class='one-item'>
            <label for='asp_f_show_title'>Show the label on the frontend?</label>
            <input type='checkbox' name='asp_f_show_title' value="yes" checked/>
        </div>
        <div class='one-item'>
            <label for='asp_f_field'>Custom Field</label>
            <select name='asp_f_field'/>
            <?php foreach($custom_fields as $key=>$v): ?>
                <option value="<?php echo $v['meta_key'] ?>"><?php echo $v['meta_key'] ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class='one-item'>
            <label for='asp_f_type'>Type</label>
            <select name='asp_f_type'/>
            <option value="radio">Radio</option>
            <option value="dropdown">Dropdown</option>
            <option value="checkboxes">Checkboxes</option>
	        <option value="hidden">Hidden</option>
            <option value="text">Text</option>
            <option value="slider">Slider</option>
            <option value="range">Range Slider</option>
            </select>
        </div>
        <div class='one-item asp_f_radio asp_f_type'>
            <label for='asp_f_radio_value'>Radio values</label>
            <textarea name='asp_f_radio_value'/>
sample_value1||Sample Label 1
sample_value2||Sample Label 2**
sample_value3||Sample Label 3</textarea>
            <p class="descMsg">One item per line, for more info see the <a target="_blank" href="http://wp-dreams.com/demo/wp-ajax-search-pro3/docs/#frontend_search_settings_creating_custom_selectors_from_custom_fields">documentation</a>.</p>
        </div>
        <div class='one-item asp_f_dropdown asp_f_type hiddend'>
            <label for='asp_f_dropdown_multi'>Multiselect?</label>
            <input type='checkbox' name='asp_f_dropdown_multi' value="yes" /><br><br>
            <label for='asp_f_dropdown_value'>Dropdown values</label>
            <textarea name='asp_f_dropdown_value'/>
sample_value1||Sample Label 1
sample_value2||Sample Label 2**
sample_value3||Sample Label 3</textarea>
            <p class="descMsg">One item per line, for more info see the <a target="_blank" href="http://wp-dreams.com/demo/wp-ajax-search-pro3/docs/#frontend_search_settings_creating_custom_selectors_from_custom_fields">documentation</a>.</p>
        </div>
        <div class='one-item asp_f_checkboxes asp_f_type hiddend'>
            <label for='asp_f_checkboxes_value'>Checkbox values</label>
            <textarea name='asp_f_checkboxes_value'/>
sample_value1||Sample Label 1**
sample_value2||Sample Label 2
sample_value3||Sample Label 3**</textarea>
            <p class="descMsg">One item per line, for more info see the <a target="_blank" href="http://wp-dreams.com/demo/wp-ajax-search-pro3/docs/#frontend_search_settings_creating_custom_selectors_from_custom_fields">documentation</a>.</p>
            <br><br>
            <label for='asp_f_checkboxes_logic'>Checkbox logic</label>
            <select name='asp_f_checkboxes_logic'/>
                <option value="OR">OR</option>
                <option value="AND">AND</option>
            </select>
        </div>
	    <div class='one-item asp_f_hidden asp_f_type'>
		    <label for='asp_f_hidden_value'>Hidden value</label>
		    <textarea name='asp_f_hidden_value'/></textarea>
		    <p class="descMsg">An invisible element. Used for filtering every time without user input.</p>
	    </div>
        <div class='one-item asp_f_text asp_f_type'>
            <label for='asp_f_text_value'>Text input</label>
            <textarea name='asp_f_text_value'/></textarea>
            <p class="descMsg">A text input element.</p>
        </div>
        <div style='line-height: 33px;' class='one-item asp_f_slider asp_f_type hiddend'>
            <label for='asp_f_slider_from'>Slider range</label>
            <input class="threedigit" type='text' value="1" name='asp_f_slider_from'/> - <input class="threedigit" value="1000" type='text' name='asp_f_slider_to'/><br />
            <label for='asp_f_slider_step'>Step</label>
            <input class="threedigit" type='text' value="1" name='asp_f_slider_step'/><br />
            <label for='asp_f_slider_prefix'>Prefix</label>
            <input class="threedigit" type='text' value="$" name='asp_f_slider_prefix'/>
            <label for='asp_f_slider_suffix'>Suffix</label>
            <input class="threedigit" type='text' value=",-" name='asp_f_slider_suffix'/><br />
            <label for='asp_f_slider_default'>Default Value</label>
            <input class="threedigit" type='text' value="500" name='asp_f_slider_default'/>
        </div>
        <div style='line-height: 33px;' class='one-item asp_f_range asp_f_type hiddend'>
            <label for='asp_f_range_from'>Slider range</label>
            <input class="threedigit" type='text' value="1" name='asp_f_range_from'/> - <input class="threedigit" value="1000" type='text' name='asp_f_range_to'/><br />
            <label for='asp_f_slider_step'>Step</label>
            <input class="threedigit" type='text' value="1" name='asp_f_range_step'/><br />
            <label for='asp_f_slider_prefix'>Prefix</label>
            <input class="threedigit" type='text' value="$" name='asp_f_range_prefix'/>
            <label for='asp_f_slider_suffix'>Suffix</label>
            <input class="threedigit" type='text' value=",-" name='asp_f_range_suffix'/><br />
            <label for='asp_f_range_default1'>Track 1 default</label>
            <input class="threedigit" type='text' value="250" name='asp_f_range_default1'/>
            <label for='asp_f_range_default2'>Track 2 default</label>
            <input class="threedigit" type='text' value="750" name='asp_f_range_default2'/>
        </div>
        <div class='one-item asp_f_operator'>
            <label for='asp_f_operator'>Operator</label>
            <select name='asp_f_operator'/>
            <optgroup label="Numeric operators">
                <option value="eq">EQUALS</option>
                <option value="neq">NOT EQUALS</option>
                <option value="lt">LESS THEN</option>
                <option value="gt">MORE THEN</option>
            </optgroup>
            <optgroup label="String operators">
                <option value="elike">EXACTLY LIKE</option>
                <option value="like" selected="selected">LIKE</option>
            </optgroup>
            </select>
            <p class="descMsg">Use the numeric operators for numeric values and string operators for text values.</p>
        </div>
        <div class='one-item'>
            <button type='button' style='margin-right: 20px;' name='reset'>Reset</button>
            <button type='button' name='add'>Add!</button>
        </div>
    </fieldset>

    <fieldset class="wpd-text-right" style="display:none;" id="asp_edit_field">
        <legend>Edit: <strong><span id="asp_edit_title"></span></strong></legend>
        <div class='one-item'>
            <label for='asp_f_title'>Title label</label>
            <input type='text' placeholder="Title here.." name='asp_f_title'/>
        </div>
        <div class='one-item'>
            <label for='asp_f_show_title'>Show the label on the frontend?</label>
            <input type='checkbox' name='asp_f_show_title' value="yes" checked/>
        </div>
        <div class='one-item'>
            <label for='asp_f_field'>Custom Field</label>
            <select name='asp_f_field'/>
            <?php foreach($custom_fields as $key=>$v): ?>
                <option value="<?php echo $v['meta_key'] ?>"><?php echo $v['meta_key'] ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class='one-item'>
            <label for='asp_f_type'>Type</label>
            <select name='asp_f_type'/>
            <option value="radio">Radio</option>
            <option value="dropdown">Dropdown</option>
            <option value="checkboxes">Checkboxes</option>
	        <option value="hidden">Hidden</option>
            <option value="text">Text</option>
            <option value="slider">Slider</option>
            <option value="range">Range Slider</option>
            </select>
        </div>
        <div class='one-item asp_f_radio asp_f_type'>
            <label for='asp_f_radio_value'>Radio values</label>
            <textarea name='asp_f_radio_value'/></textarea>
        </div>
        <div class='one-item asp_f_dropdown asp_f_type hiddend'>
            <label for='asp_f_dropdown_multi'>Multiselect?</label>
            <input type='checkbox' name='asp_f_dropdown_multi' value="yes" /><br><br>
            <label for='asp_f_dropdown_value'>Dropdown values</label>
            <textarea name='asp_f_dropdown_value'/></textarea>
        </div>
        <div class='one-item asp_f_checkboxes asp_f_type hiddend'>
            <label for='asp_f_checkboxes_value'>Checkbox values</label>
            <textarea name='asp_f_checkboxes_value'/></textarea><br><br>
            <label for='asp_f_checkboxes_logic'>Checkbox logic</label>
            <select name='asp_f_checkboxes_logic'/>
                <option value="OR">OR</option>
                <option value="AND">AND</option>
            </select>
        </div>
	    <div class='one-item asp_f_hidden asp_f_type'>
            <label for='asp_f_hidden_value'>Hidden value</label>
            <textarea name='asp_f_hidden_value'/></textarea>
            <p class="descMsg">An invisible element. Used for filtering every time without user input.</p>
        </div>
        <div class='one-item asp_f_text asp_f_type'>
            <label for='asp_f_text_value'>Text input</label>
            <textarea name='asp_f_text_value'/></textarea>
            <p class="descMsg">A text input element.</p>
        </div>
        <div style='line-height: 33px;' class='one-item asp_f_slider asp_f_type hiddend'>
            <label for='asp_f_slider_from'>Slider range</label>
            <input class="threedigit" type='text' value="" name='asp_f_slider_from'/> - <input class="threedigit" value="" type='text' name='asp_f_slider_to'/><br />
            <label for='asp_f_slider_step'>Step</label>
            <input class="threedigit" type='text' value="1" name='asp_f_slider_step'/><br />
            <label for='asp_f_slider_prefix'>Prefix</label>
            <input class="threedigit" type='text' value="$" name='asp_f_slider_prefix'/>
            <label for='asp_f_slider_suffix'>Suffix</label>
            <input class="threedigit" type='text' value=",-" name='asp_f_slider_suffix'/><br />
            <label for='asp_f_slider_default'>Default Value</label>
            <input class="threedigit" type='text' value="" name='asp_f_slider_default'/>
        </div>
        <div style='line-height: 33px;' class='one-item asp_f_range asp_f_type hiddend'>
            <label for='asp_f_range_from'>Slider range</label>
            <input class="threedigit" type='text' value="" name='asp_f_range_from'/> - <input class="threedigit" value="" type='text' name='asp_f_range_to'/><br />
            <label for='asp_f_slider_step'>Step</label>
            <input class="threedigit" type='text' value="1" name='asp_f_range_step'/><br />
            <label for='asp_f_slider_prefix'>Prefix</label>
            <input class="threedigit" type='text' value="$" name='asp_f_range_prefix'/>
            <label for='asp_f_slider_suffix'>Suffix</label>
            <input class="threedigit" type='text' value=",-" name='asp_f_range_suffix'/><br />
            <label for='asp_f_range_default1'>Track 1 default</label>
            <input class="threedigit" type='text' value="" name='asp_f_range_default1'/>
            <label for='asp_f_range_default2'>Track 2 default</label>
            <input class="threedigit" type='text' value="" name='asp_f_range_default2'/>
        </div>
        <div class='one-item asp_f_operator'>
            <label for='asp_f_operator'>Operator</label>
            <select name='asp_f_operator'/>
            <optgroup label="Numeric operators">
                <option value="eq">EQUALS</option>
                <option value="neq">NOT EQUALS</option>
                <option value="lt">LESS THEN</option>
                <option value="gt">MORE THEN</option>
            </optgroup>
            <optgroup label="String operators">
                <option value="elike">EXACTLY LIKE</option>
                <option value="like">LIKE</option>
            </optgroup>
            </select>
            <p class="descMsg">Use the numeric operators for numeric values and string operators for text values.</p>
        </div>
        <div class='one-item'>
            <button type='button' style='margin-right: 20px;' name='back'>Back</button>
            <button type='button' name='save'>Save!</button>
        </div>
    </fieldset>

    <input type="hidden" name="custom_field_items" value="<?php
        if (isset($_POST['custom_field_items']))
            echo $_POST['custom_field_items'];
        else
            echo wpdreams_setval_or_getoption($sd, 'custom_field_items', $_dk);

    ?>" />
</div>
<div class="wpd-40-pc customFieldsSortable">
    <div class="sortablecontainer">
        <ul id="csf_sortable">

        </ul>
    </div>
</div>