// Javascript For the types
jQuery(function($){
    // ------------------------- GENERICS ------------------------

    /**
     * Simple select
     */
    $('.wpdreamsSelect .wpdreamsselect').change(function () {
        _self.hidden = $(this).next();
        var selhidden = $(this).next().next();
        var val = $(_self.hidden).val().match(/(.*[\S\s]*?)\|\|(.*)/);
        var options = val[1];
        var selected = val[2];
        $(_self.hidden).val(options + "||" + $(this).val());
        selhidden.val($(this).val());
    });
    $('.wpdreamsSelect .triggerer').bind('click', function () {
        var parent = $(this).parent();
        var select = $('select', parent);
        var hidden = select.next();
        var selhidden = hidden.next();
        var val = $(hidden).val().replace(/(\r\n|\n|\r)/gm, "").match(/(.*[\S\s]*?)\|\|(.*)/);
        var selected = $.trim(val[2]);
        select.val(selected);
        selhidden.val(selected);
    });

    /**
     * Textarea as parameter
     */
    $('.wpdreamsTextareaIsParam .triggerer').bind('click', function () {
        $('textarea', $(this).parent()).change();
    });

    /**
     * OnOff button
     */
    $('.wpdreamsOnOff .wpdreamsOnOffInner').on('click', function () {
        var hidden = $(this).prev();
        var val = $(hidden).val();
        if (val == 1) {
            val = 0;
            $(this).parent().removeClass("active");
        } else {
            val = 1;
            $(this).parent().addClass("active");
        }
        $(hidden).val(val);
        $(hidden).change();
    });
    $('.wpdreamsOnOff .triggerer').on('click', function () {
        var hidden = $('input[type=hidden]', $(this).parent());
        var div = $(this).parent();
        var val = $(hidden).val();
        if (val == 0) {
            div.removeClass("active");
        } else {
            div.addClass("active");
        }
    });

    /**
     * YesNo button
     */
    $('.wpdreamsYesNo .wpdreamsYesNoInner').on('click', function () {
        var hidden = $(this).prev();
        var val = $(hidden).val();
        if (val == 1) {
            val = 0;
            $(this).parent().removeClass("active");
        } else {
            val = 1;
            $(this).parent().addClass("active");
        }
        $(hidden).val(val);
        $(hidden).change();
    });
    $('.wpdreamsYesNo .triggerer').on('click', function () {
        var hidden = $('input[type=hidden]', $(this).parent());
        var div = $(this).parent();
        var val = $(hidden).val();
        if (val == 0) {
            div.removeClass("active");
        } else {
            div.addClass("active");
        }
        $(hidden).change();
    });

    /**
     * Up-down arrow
     */
    $('.wpdreams-updown .wpdreams-uparrow').click(function () {
        var prev = $(this).parent().prev();
        while (!prev.is('input')) {
            prev = prev.prev();
        }
        prev.val(parseFloat($(prev).val()) + 1);
        prev.change();
    });
    $('.wpdreams-updown .wpdreams-downarrow').click(function () {
        var prev = $(this).parent().prev();
        while (!prev.is('input')) {
            prev = prev.prev();
        }
        prev.val(parseFloat($(prev).val()) - 1);
        prev.change();
    });

    /**
     * 4 value storage (padding, margin etc..)
     */
    $('.wpdreamsFour input[type=text]').change(function () {
        var value = "";
        $('input[type=text]', $(this).parent()).each(function () {
            value += $(this).val() + "||";
        });
        $('input[isparam=1]', $(this).parent()).val("||" + value);
        $('input[isparam=1]', $(this).parent()).change();
    });
    $('.wpdreamsFour>fieldset>.triggerer').bind('click', function () {
        var hidden = $("input[isparam=1]", $(this).parent());
        var values = hidden.val().match(/\|\|(.*?)\|\|(.*?)\|\|(.*?)\|\|(.*?)\|\|/);
        var i = 1;
        $('input[type=text]', $(this).parent()).each(function () {
            if ($(this).attr('name') != null) {
                $(this).val(values[i]);
                i++;
            }
        });
        hidden.change();
    });

    // --------------------- COMPLEX TYPES -----------------------
    /**
     * Array-chained select
     */
    $('.wpdreamsCustomArraySelect select').change(function () {
        var $hidden = $('input[isparam=1]', $(this).parent());
        var valArr = [];

        $('select', $(this).parent()).each(function(index){
            valArr.push( $(this).val() );
        });

        $hidden.val( valArr.join('||') );
    });


    /**
     * Category selector
     */
    $('div.wpdreamsCategories').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCategories-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {

            }
        }).disableSelection();
        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsCategories')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).attr('bid');
            });
            val = val.substring(1);
            hidden.val(val);
        });
        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id + " li:not(.hiddend)")
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Draggable selector
     */
    $('div.wpdreamsDraggable').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsDraggable-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
            }
        }).disableSelection();

        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsDraggable')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).attr('key');
            });
            val = val.substring(1);
            hidden.val(val);
        });
    });

    /**
     * User role select
     */
    $('div.wpdreamsUserRoleSelect').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsUserRoleSelect-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
            }
        }).disableSelection();

        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsUserRoleSelect')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' +name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).html();
            });
            val = val.substring(1);
            hidden.val(val);
        });

        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id)
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Custom Post types
     */
    $('div.wpdreamsCustomPostTypes').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCustomPostTypes-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {

            }
        }).disableSelection();
        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsCustomPostTypes')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).html();
            });
            val = val.substring(1);
            hidden.val(val);
        });

        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id)
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Custom Post types, built in version
     */
    $('div.wpdreamsCustomPostTypesAll').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCustomPostTypesAll-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {

            }
        }).disableSelection();
        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsCustomPostTypesAll')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).html();
            });
            val = val.substring(1);
            hidden.val(val);
        });

        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id)
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Custom post types, editable version
     */
    $('div.wpdreamsCustomPostTypesEditable').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCustomPostTypesEditable-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).keyup(function () {
            parent = $(this).parent();
            while (!parent.hasClass('wpdreamsCustomPostTypesEditable')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $('label', this).html() + ";" + $('input', this).val();
            });
            val = val.substring(1);
            hidden.val(val);
        });
        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
                $("#sortable_conn" + id + " li input").keyup(function () {
                    parent = $(this).parent();
                    while (!parent.hasClass('wpdreamsCustomPostTypesEditable')) {
                        parent = $(parent).parent();
                    }
                    var items = $('ul[id*=sortable_conn] li', parent);
                    var hidden = $('input[name=' + name + ']', parent);
                    var val = "";
                    //console.log(val);
                    items.each(function () {
                        val += "|" + $('label', this).html() + ";" + $('input', this).val();
                    });
                    val = val.substring(1);
                    hidden.val(val);
                });
                if ($("#sortable_conn" + id + " li input").length != 0) {
                    $("#sortable_conn" + id + " li input").keyup();
                } else {
                    $("#sortable_conn" + id).each(function () {
                        parent = $(this).parent();
                        while (!parent.hasClass('wpdreamsCustomPostTypesEditable')) {
                            parent = $(parent).parent();
                        }
                        var hidden = $('input[name=' + name + ']', parent);
                        hidden.val("");
                    });
                }
            }
        });
    });

    /**
     * Custom field selectors
     */
    $('div.wpdreamsCustomFields').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCustomFields-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
            }
        }).disableSelection();

        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsCustomFields')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).html();
            });
            val = val.substring(1);
            hidden.val(val);
        });

        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id)
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Page parent selector
     */
    $('div.wpdreamsPageParents').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsPageParents-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');

        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
            }
        }).disableSelection();

        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsPageParents')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).attr('bid');
            });
            val = val.substring(1);
            hidden.val(val);
        });

        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id)
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });

    /**
     * Taxonomy term selector
     */
    $('div.wpdreamsCustomTaxonomyTerm').each(function(){
        var id = $(this).attr('id').match(/^wpdreamsCustomTaxonomyTerm-(.*)/)[1];
        var selector = "#sortable" + id +", #sortable_conn" + id;
        var name = $('input[isparam=1]', this).attr('name');
        $(selector).sortable({
            connectWith: ".connectedSortable"
        }, {
            update: function (event, ui) {
            }
        }).disableSelection();
        $("#taxonomy_selector_" + id).change(function () {
            var taxonomy = $(this).val();
            $("li", "#sortable" + id).css('display', 'none').addClass('hiddend');
            $("li[taxonomy='" + taxonomy + "']", "#sortable" + id).css('display', 'block').removeClass('hiddend');
        });
        $("#taxonomy_selector_" + id).change();
        $(selector).on('sortupdate', function(event, ui) {
            if (typeof(ui)!='undefined')
                parent = $(ui.item).parent();
            else
                parent = $(event.target);
            while (!parent.hasClass('wpdreamsCustomTaxonomyTerm')) {
                parent = $(parent).parent();
            }
            var items = $('ul[id*=sortable_conn] li', parent);
            var hidden = $('input[name=' + name + ']', parent);
            var val = "";
            items.each(function () {
                val += "|" + $(this).attr('term_id') + "-" + $(this).attr('taxonomy');
            });
            val = val.substring(1);
            hidden.val(val);
        });
        $("#wpdreamsCustomTaxonomyTerm-" + id + " .hide-children").change(function(){
            if ($(this).get(0).checked)
                $("#sortablecontainer" + id + " li").filter(':not(.termlevel-0)').css('display', 'none');
            else
                $("#sortablecontainer" + id + " li").filter(':not(.termlevel-0)').css('display', 'block');
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-left").click(function(){
            $("#sortable_conn" + id + " li")
                .detach().appendTo("#sortable" + id + "");
            $(selector).trigger("sortupdate");
        });
        $("#sortablecontainer" + id + " .arrow-all-right").click(function(){
            $("#sortable" + id + " li:not(.hiddend)")
                .detach().appendTo("#sortable_conn" + id);
            $(selector).trigger("sortupdate");
        });
    });


    // ----------------------- THEME RELATED ---------------------

    /**
     * Theme Chooser
     *
     * Since 4.5.4 better load balancing and loading bar implemented.
     */
    $('.wpdreamsThemeChooser select').bind('change', function () {
        var c = confirm('Do you really want to load this template?');
        if (!c) return;
        var parent = $(this);
        while (parent.is('form') != true) {
            parent = parent.parent();
        }
        var themeDiv = $('div[name="' + $(this).val() + '"]');
        var items = JSON.parse( themeDiv.text() );
        var itemsCount = $.map(items, function(n, i) { return i; }).length;

        // Loader start here
        $("#wpd_body_loader").css("display", "block");
        $("#wpd_loading_msg").html("Loading " +  "0/" + itemsCount);

        var count = 0;
        var t = null;

        // Delay the start by 1 second for the browser to finish all pending operations
        setTimeout(function() {

            $.each( items, function (key, value) {

                param = $('input[name="' + key + '"]', parent);
                if (param.length == 0)
                    param = $('select[name="' + key + '"]', parent);
                if (param.length == 0)
                    param = $('textarea[name="' + key + '"]', parent);
                param.val(value);

                // Delay execution by 60ms for each item
                setTimeout(function(tc) {

                    $("#wpd_loading_msg").html("Loading " + tc + "/" + itemsCount);

                    // Abort the execution of the loading removal
                    clearTimeout(t);

                    $('>.triggerer', param.parent()).trigger('click');

                    // Give 800 milliseconds to render, until the last one reached
                    // then remove the loader. The last timeout is not aborted,
                    // it is executed after 800 ms, finally removing the loading bar.
                    t = setTimeout(function() {

                        $("#wpd_body_loader").css("display", "none");
                        $("#preview .refresh").click();

                    }, (800));

                }, (count * 60), (count +1) );
                count++;
            });

        }, 1000);

    });

    /**
     * Animation selector
     */
    $('.wpdreamsAnimations .wpdreamsanimationselect').change(function () {
        var parent = $(this).parent();
        $('span', parent).removeClass();
        $('span', parent).addClass("asp_an_" + $(this).val());
    });
    $('.wpdreamsAnimations .triggerer').bind('click', function () {
        var parent = $(this).parent();
        var select = $('select', parent);
        return;
    });

    /**
     * Image Settings
     * The name of the separate params determinates the value outputted in the hidden field.
     */
    $('.wpdreamsImageSettings input, .wpdreamsImageSettings select').change(function () {
        parent = $(this).parent();
        while (parent.hasClass('item') != true) {
            parent = parent.parent();
        }
        var elements = $('input[param!=1], select', parent);
        var hidden = $('input[param=1]', parent);
        var ret = "";
        elements.each(function () {
            ret += $(this).attr("name") + ":" + $(this).val() + ";";
        });
        hidden.val(ret);
    });
    $('.wpdreamsImageSettings>fieldset>.triggerer').bind("click", function () {
        var elements = $('input[param!=1], select', parent);
        var hidden = $('input[param=1]', parent);
        elements.each(function () {
            var name = $(this).attr("name");
            var regex = new RegExp(".*" + name + ":(.*?);.*");
            val = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(regex);
            $(this).val(val[1]);
            if ($(this).next().hasClass('triggerer')) $(this).next().click();
        });
    });
    //Image Settings End

    /**
     * Numeric unit related
     */
    $('.wpdreamsNumericUnit select, .wpdreamsNumericUnit input[name=numeric]').change(function () {
        var value = "";
        var parent = $(this).parent();
        while (parent.hasClass('wpdreamsNumericUnit') != true) {
            parent = $(parent).parent();
        }
        var value = $('input[name=numeric]', parent).val() + $('select', parent).val();
        $('input[type=hidden]', parent).val(value);
    });

    $('.wpdreamsNumericUnit .triggerer').bind('click', function () {
        var value = "";
        var parent = $(this).parent();
        while (parent.hasClass('wpdreamsNumericUnit') != true) {
            parent = $(parent).parent();
        }
        var hiddenval = $('input[type=hidden]', parent).val();
        var value = hiddenval.match(/([0-9]+)(.*)/)
        $('input[name=numeric]', parent).val(value[1]);
        $('select', parent).val(value[2]);
    });

    /**
     * Image chooser (radio image)
     */
    $('.wpdreamsImageRadio img.radioimage').click(function () {
        var $parent = $(this).parent();
        var $hidden = $("input[class=realvalue]", $parent);
        $('img.selected', $parent).removeClass('selected');
        $(this).addClass('selected');
        var value = $(this).attr('sel').substring(1);
        $hidden.val(value);
        $hidden.change();
    });
    $('.wpdreamsImageRadio .triggerer').bind('click', function () {
        var $parent = $(this).parent();
        var $hidden = $("input[class=realvalue]", $parent);
        $('img.selected', $parent).removeClass('selected');
        $('img[src*="' + $hidden.val() + '"]', $parent).addClass('selected');
        $hidden.change();
    });

    /**
     * Loader chooser
     */
    $('.wpdreamsLoaderSelect .asp-select-loader, .wpdreamsLoaderSelect .asp-select-loader-selected').click(function () {
        var $parent = $(this).parent();
        var $hidden = $("input[class=realvalue]", $parent);
        $('div.asp-select-loader-selected', $parent)
            .addClass('asp-select-loader')
            .removeClass('asp-select-loader-selected');

        $(this).addClass('asp-select-loader-selected').removeClass('asp-select-loader');
        var value = $(this).attr('sel');
        $hidden.val(value);
        $hidden.change();
    });
    $('.wpdreamsLoaderSelect .triggerer').bind('click', function () {
        var $parent = $(this).parent();
        var $hidden = $("input[class=realvalue]", $parent);
        $('div.asp-select-loader-selected', $parent)
            .addClass('asp-select-loader')
            .removeClass('asp-select-loader-selected');

        $('div[sel*="' + $hidden.val() + '"]', $parent)
            .addClass('asp-select-loader-selected')
            .removeClass('asp-select-loader');

        $hidden.change();
    });

    /**
     * Spectrum: color chooser
     */
    $(".wpdreamsColorPicker .color").spectrum({
        showInput: true,
        showAlpha: true,
        showPalette: true,
        showSelectionPalette: true
    });
    $('.wpdreamsColorPicker .triggerer').bind('click', function () {
        function hex2rgb(hex, opacity) {
            var rgb = hex.replace('#', '').match(/(.{2})/g);

            var i = 3;
            while (i--) {
                rgb[i] = parseInt(rgb[i], 16);
            }

            if (typeof opacity == 'undefined') {
                return 'rgb(' + rgb.join(', ') + ')';
            }

            return 'rgba(' + rgb.join(', ') + ', ' + opacity + ')';
        }

        var parent = $(this).parent();
        var input = $('input.color', parent);
        var val = input.val();
        if (val.length <= 7) val = hex2rgb(val, 1);
        input.spectrum("set", val);
    });

    /**
     * Gradient chooser
     */
    $(".wpdreamsGradient .color, .wpdreamsGradient .grad_type, .wpdreamsGradient .dslider").change(function () {
        var $parent = $(this);
        while (!$parent.hasClass('wpdreamsGradient')) {
            $parent = $parent.parent();
        }
        var $hidden = $('input.gradient', $parent);
        var $colors = $('input.color', $parent);
        var $type = $('select.grad_type', $parent);
        var $dslider = $('div.dslider', $parent);
        var $grad_ex = $('div.grad_ex', $parent);
        var $dbg = $('div.dbg', $parent);
        var $dtxt = $('div.dtxt', $parent);

        $dbg.css({
            "-webkit-transform": "rotate(" + $dslider.slider('value') + "deg)",
            "-moz-transform": "rotate(" + $dslider.slider('value') + "deg)",
            "transform": "rotate(" + $dslider.slider('value') + "deg)"
        });
        $dtxt.html($dslider.slider('value'));

        grad($grad_ex, $($colors[0]).val(), $($colors[1]).val(), $type.val(), $dslider.slider('value'));

        $hidden.val(
            $type.val() + '-' +
                $dslider.slider('value') + '-' +
                $($colors[0]).val() + '-' +
                $($colors[1]).val()
        );
        $hidden.change();
    });
    $(".wpdreamsGradient>.triggerer").click(function () {
        var $parent = $(this).parent();
        var $hidden = $('input.gradient', $parent);
        var $colors = $('input.color', $parent);
        var $dslider = $('div.dslider', $parent);
        var $type = $('select.grad_type', $parent);
        var colors = $hidden.val().match(/(.*?)-(.*?)-(.*?)-(.*)/);

        if (colors == null || colors[1] == null) {
            //Fallback to older 1 color
            $type.val(0);
            $dslider.slider('value', 0);
            $($colors[0]).spectrum('set', $hidden.val());
            $($colors[1]).spectrum('set', $hidden.val());
        } else {
            $type.val(colors[1]);
            $dslider.slider('value', colors[2]);
            $($colors[0]).val(colors[3]);
            $($colors[1]).val(colors[4]);

            $($colors[0]).spectrum('set', colors[3]);
            $($colors[1]).spectrum('set', colors[4]);
        }
    });
    function grad(el, c1, c2, t, d) {
        if (t != 0) {
            $(el).css('background-image', '-webkit-linear-gradient(' + d + 'deg, ' + c1 + ', ' + c2 + ')')
                .css('background-image', '-moz-linear-gradient(' + d + 'deg,  ' + c1 + ',  ' + c2 + ')')
                .css('background-image', '-ms-linear-gradient(' + d + 'deg,  ' + c1 + ',  ' + c2 + ')')
                .css('background-image', 'linear-gradient(' + d + 'deg,  ' + c1 + ',  ' + c2 + ')')
                .css('background-image', '-o-linear-gradient(' + d + 'deg,  ' + c1 + ',  ' + c2 + ')');
        } else {
            $(el).css('background-image', '-webkit-radial-gradient(center, ellipse cover, ' + c1 + ', ' + c2 + ')')
                .css('background-image', '-moz-radial-gradient(center, ellipse cover, ' + c1 + ',  ' + c2 + ')')
                .css('background-image', '-ms-radial-gradient(center, ellipse cover, ' + c1 + ',  ' + c2 + ')')
                .css('background-image', 'radial-gradient(ellipse at center, ' + c1 + ',  ' + c2 + ')')
                .css('background-image', '-o-radial-gradient(center, ellipse cover, ' + c1 + ',  ' + c2 + ')');
        }
    }

    /**
     * TextShadow chooser
     */
    $('.wpdreamsTextShadow input[type=text]').change(function () {
        var value = "";
        var parent = $(this).parent();
        while (parent.hasClass('wpdreamsTextShadow') != true) {
            parent = $(parent).parent();
        }
        var hlength = $.trim($('input[name*="_xx_hlength_xx_"]', parent).val()) + "px ";
        var vlength = $.trim($('input[name*="_xx_vlength_xx_"]', parent).val()) + "px ";
        var blurradius = $.trim($('input[name*="_xx_blurradius_xx_"]', parent).val()) + "px ";
        var color = $.trim($('input[name*="_xx_color_xx_"]', parent).val()) + " ";
        var boxshadow = "text-shadow:" + hlength + vlength + blurradius + color;
        $('input[type=hidden]', parent).val(boxshadow);
        $('input[type=hidden]', parent).change();
    });
    $('.wpdreamsTextShadow>fieldset>.triggerer').bind('click', function () {
        var parent = $(this).parent();
        var hidden = $("input[type=hidden]", parent);
        var boxshadow = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(/box-shadow:(.*?)px (.*?)px (.*?)px (.*?);/);

        $('input[name*="_xx_hlength_xx_"]', parent).val(boxshadow[1]) + "px ";
        $('input[name*="_xx_vlength_xx_"]', parent).val(boxshadow[2]) + "px ";
        $('input[name*="_xx_blurradius_xx_"]', parent).val(boxshadow[3]) + "px ";
        $('input[name*="_xx_color_xx_"]', parent).val(boxshadow[4]) + " ";
        $('input[name*="_xx_color_xx_"]', parent).keyup();
    });

    /**
     * BoxShadow chooser
     */
    $('.wpdreamsBoxShadow input[type=text], .wpdreamsBoxShadow select').change(function () {
        var value = "";
        var parent = $(this).parent();
        while (parent.hasClass('wpdreamsBoxShadow') != true) {
            parent = $(parent).parent();
        }
        var hlength = $.trim($('input[name*="_xx_hlength_xx_"]', parent).val()) + "px ";
        var vlength = $.trim($('input[name*="_xx_vlength_xx_"]', parent).val()) + "px ";
        var blurradius = $.trim($('input[name*="_xx_blurradius_xx_"]', parent).val()) + "px ";
        var spread = $.trim($('input[name*="_xx_spread_xx_"]', parent).val()) + "px ";
        var color = $.trim($('input[name*="_xx_color_xx_"]', parent).val()) + " ";
        var inset = $.trim($('select[name*="_xx_inset_xx_"]', parent).val()) + ";";
        var boxshadow = "box-shadow:" + hlength + vlength + blurradius + spread + color + inset;

        $('input[type=hidden]', parent).val(boxshadow);
        $('input[type=hidden]', parent).change();
    });
    $('.wpdreamsBoxShadow>fieldset>.triggerer').bind('click', function () {
        var parent = $(this).parent();
        var hidden = $("input[type=hidden]", parent);
        var boxshadow = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(/box-shadow:(.*?)px (.*?)px (.*?)px (.*?)px (.*?)\) (.*?);/);
        var plus = ")";
        if (boxshadow == null) {
            boxshadow = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(/box-shadow:(.*?)px (.*?)px (.*?)px (.*?)px (.*?) (.*?);/);
            plus = '';
        }
        $('input[name*="_xx_hlength_xx_"]', parent).val(boxshadow[1]);
        $('input[name*="_xx_vlength_xx_"]', parent).val(boxshadow[2]);
        $('input[name*="_xx_blurradius_xx_"]', parent).val(boxshadow[3]);
        $('input[name*="_xx_spread_xx_"]', parent).val(boxshadow[4]);
        $('input[name*="_xx_color_xx_"]', parent).val(boxshadow[5] + plus);
        $('select[name*="_xx_inset_xx_"]', parent).val(boxshadow[6]);
        $('input[name*="_xx_color_xx_"]', parent).spectrum('set', boxshadow[5] + plus);
    });

    /**
     * Border chooser
     */
    $('.wpdreamsBorder input[type=text], .wpdreamsBorder select').bind("change", function () {
        var value = "";
        var parent = $(this).parent();
        while (parent.hasClass('wpdreamsBorder') != true) {
            parent = $(parent).parent();
        }
        var width = $('input[name*="_xx_width_xx_"]', parent).val() + "px ";
        var style = $('select[name*="_xx_style_xx_"]', parent).val() + " ";
        var color = $('input[name*="_xx_color_xx_"]', parent).val() + ";";
        var border = "border:" + width + style + color;

        var topleft = $.trim($('input[name*="_xx_topleft_xx_"]', parent).val()) + "px ";
        var topright = $.trim($('input[name*="_xx_topright_xx_"]', parent).val()) + "px ";
        var bottomright = $.trim($('input[name*="_xx_bottomright_xx_"]', parent).val()) + "px ";
        var bottomleft = $.trim($('input[name*="_xx_bottomleft_xx_"]', parent).val()) + "px;";
        var borderradius = "border-radius:" + topleft + topright + bottomright + bottomleft;

        var value = border + borderradius;

        $('input[type=hidden]', parent).val(value);
        $('input[type=hidden]', parent).change();
    });
    $('.wpdreamsBorder>fieldset>.triggerer').bind('click', function () {
        var parent = $(this).parent();
        var hidden = $("input[type=hidden]", parent);
        var border = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(/border:(.*?)px (.*?) (.*?);/);
        $('input[name*="_xx_width_xx_"]', parent).val(border[1]) + "px ";
        $('select[name*="_xx_style_xx_"]', parent).val(border[2]) + " ";
        $('input[name*="_xx_color_xx_"]', parent).val(border[3]) + ";";

        var borderradius = hidden.val().replace(/(\r\n|\n|\r)/gm, "").match(/border-radius:(.*?)px(.*?)px(.*?)px(.*?)px;/);
        $('input[name*="_xx_topleft_xx_"]', parent).val(borderradius[1]) + "px ";
        $('input[name*="_xx_topright_xx_"]', parent).val(borderradius[2]) + "px ";
        $('input[name*="_xx_bottomright_xx_"]', parent).val(borderradius[3]) + "px ";
        $('input[name*="_xx_bottomleft_xx_"]', parent).val(borderradius[4]) + "px;";
        $('input[name*="_xx_color_xx_"]', parent).spectrum('set', border[3]);
    });

    /**
     * Border Radius chooser
     */
    $('.wpdreamsBorderRadius input[type=text]').change(function () {
        var value = "";
        $('input[type=text]', $(this).parent()).each(function () {
            value += " " + $(this).val() + "px";
        });
        $('input[type=hidden]', $(this).parent()).val("border-radius:" + value + ";");
        $('input[type=hidden]', $(this).parent()).change();
    });
    $('.wpdreamsBorderRadius>fieldset>.triggerer').bind('click', function () {
        var hidden = $("input[type=hidden]", $(this).parent());
        var values = hidden.val().match(/(.*?)px(.*?)px(.*?)px(.*?)px;/);
        var i = 1;
        $('input[type=text]', $(this).parent()).each(function () {
            if ($(this).attr('name') != null) {
                $(this).val(values[i]);
                i++;
            }
        });
    });


    // ----------------------- ETC.. ---------------------

    $('.successMsg').each(function () {
        $(this).delay(4000).fadeOut();
    });
    $('img.delete').click(function () {
        var del = confirm("Do yo really want to delete this item?");
        if (del) {
            $(this).next().submit();
        }
    });

});
