
// Uploading files
var file_frame;
var clickedID;

jQuery(document).on( 'click', '.button_upload_image', function( event ){

    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( !file_frame ) {
        // Create the media frame.
        file_frame = wp.media.frames.downloadable_file = wp.media({
            title: 'Choose an image',
            button: {
                text: 'Use image'
            },
            multiple: false
        });
    }

    file_frame.open();
    
    clickedID = jQuery(this).attr('id');
    
    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
        attachment = file_frame.state().get('selection').first().toJSON();

        jQuery('#' + clickedID).val( attachment.url );
        if (jQuery('#' + clickedID).attr('data-name'))
            jQuery('#' + clickedID).attr('name', jQuery('#' + clickedID).attr('data-name'));

        file_frame.close();
    });
});

jQuery(document).on( 'click', '.button_remove_image', function( event ){
    
    var clickedID = jQuery(this).attr('id');
    jQuery('#' + clickedID).val( '' );

    return false;
});

jQuery(function($) {

    function updatePortoMenuOptions(elem, shift) {
        var current_elem = elem;
        var depth_shift = shift;
        var classNames = current_elem.attr('class').split(' ');

        for (var i = 0; i < classNames.length; i+=1) {
            if (classNames[i].indexOf('menu-item-depth-') >= 0) {
                var depth = classNames[i].split('menu-item-depth-');
                var id = current_elem.attr('id');

                depth = parseInt(depth[1]) + depth_shift;
                id = id.replace('menu-item-', '');

                if (depth == 0) {
                    current_elem.find('.edit-menu-item-level1-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else if (depth == 1) {
                    current_elem.find('.edit-menu-item-level0-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else {
                    current_elem.find('.edit-menu-item-level0-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                }
            }
        }
    }

    $(document).on('change', '.menu-item select, .menu-item textarea, .menu-item input[type="text"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).val();
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $(document).on('change', '.menu-item input[type="checkbox"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).is(':checked');
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $('#update-nav-menu').bind('click', function(e) {
        if ( e.target && e.target.className ) {
            if ( -1 != e.target.className.indexOf('item-delete') ) {
                var clickedEl = e.target;
                var itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);
                var menu_item = $('#menu-item-' + itemID);
                var children = menu_item.childMenuItems();
                children.each(function() {
                    updatePortoMenuOptions($(this), -1);
                });
            }
        }
    });

    $( "#menu-to-edit" ).on( "sortstop", function( event, ui ) {
        var menu_item = ui.item;
        setTimeout(function() {
            updatePortoMenuOptions(menu_item, 0);
            var children = menu_item.childMenuItems();
            children.each(function() {
                updatePortoMenuOptions($(this), 0);
            })
        }, 200);
    } );

    // Import Theme Options
    $('body').on('click', '#porto_settings-theme-type .redux-image-select label img', function(e) {

        var answer = confirm(js_porto_admin_vars.import_options_msg);
        if (answer){
            e.preventDefault();
            var demo = $(this).prev().val();
            window.onbeforeunload = null;
            window.location.href = js_porto_admin_vars.theme_option_url + '&import_theme_options=' + demo;
            return false;
        }
    });

    // Remove import success values
    if ($('#redux-form-wrapper').length) {
        var $referer = $('#redux-form-wrapper input[name="_wp_http_referer"]');
        var value = $referer.val();
        value = value.replace('&import_success=true', '');
        value = value.replace('&import_masterslider_success=true', '');
        value = value.replace('&import_widget_success=true', '');
        value = value.replace('&import_options_success=true', '');
        value = value.replace('&compile_theme_success=true', '');
        value = value.replace('&compile_theme_rtl_success=true', '');
        value = value.replace('&compile_plugins_success=true', '');
        value = value.replace('&compile_plugins_rtl_success=true', '');
        $referer.val(value);
    }

    function alertLeavePage() {
        return "Are you sure you want to leave?";
    }

    function addAlertLeavePage() {
        $('.redux-container .import-button').attr('disabled', 'disabled');
        $(window).bind('beforeunload', alertLeavePage);
    }

    function removeAlertLeavePage() {
        $('.redux-container .import-button').removeAttr('disabled');
        $(window).unbind('beforeunload', alertLeavePage);
        setTimeout(function() {
            $('.redux-container .import-status').slideUp();
        }, 5000);
    }

    function showImportMessage(message, count, index) {
        html = '';
        if (message) {
            html += '<br><strong>' + message + '</strong>';
        }
        if (count && index) {
            percent = index / count * 100;
            if (percent > 100)
                percent = 100;
            html += '<div class="import-progress-bar"><div style="width:' + percent + '%;"></div></div>';
        }
        $('.redux-container .import-status').stop().show().html(html);
    }

    // import dummy content
    $('.redux-container .import_porto_dummy').click(function(e) {
        e.preventDefault();

        if (confirm('Do you want to import dummy content?')) {
            addAlertLeavePage();
            showImportMessage('Importing...');

            var data = {'action': 'porto_import_dummy', 'process':'import_start'};
            var index = 0, count = 0, process = 'import_start';
            function import_dummy(args) {
                jQuery.post(ajaxurl, args, function(response) {
                    if (response) {
                        response = $.parseJSON(response);
                        if (response.process != 'complete') {
                            var requests = {'action': 'porto_import_dummy'};
                            if (response.process) requests.process = response.process;
                            if (response.index) requests.index = response.index;

                            import_dummy(requests);
                            index = response.index;
                            count = response.count;
                            process = response.process;

                            showImportMessage(response.message, count, index);
                        } else {
                            showImportMessage(response.message);
                            removeAlertLeavePage();
                        }
                    } else {
                        removeAlertLeavePage();
                    }
                }).fail(function(response) {
                        if (index < count) {
                            var requests = {'action': 'porto_import_dummy'};
                            requests.process = process;
                            requests.index = ++index;

                            import_dummy(requests);
                        } else {
                            removeAlertLeavePage();
                        }
                    });
            }

            import_dummy(data);
        }
    });

    // import widgets
    $('.redux-container .import_porto_widgets').click(function(e) {
        e.preventDefault();

        if (confirm('Do you want to import widgets?')) {
            addAlertLeavePage();
            showImportMessage('Importing...');

            var data = {'action': 'porto_import_widgets'};
            jQuery.post(ajaxurl, data, function(response) {
                showImportMessage(response);
                removeAlertLeavePage();
            }).fail(function(response) {
                removeAlertLeavePage();
            });
        }
    });

    // import master sliders
    $('.redux-container .import_porto_mastersliders').click(function(e) {
        e.preventDefault();

        if (confirm('Do you want to import master slider?')) {
            addAlertLeavePage();
            showImportMessage('Importing...');

            var data = {'action': 'porto_import_mastersliders'};
            var index = 0, count = 0;
            function import_mastersliders(args) {
                jQuery.post(ajaxurl, args, function(response) {
                    if (response) {
                        response = $.parseJSON(response);
                        if (response.index) {
                            var requests = {'action': 'porto_import_mastersliders'};
                            if (response.index) requests.index = response.index;

                            import_mastersliders(requests);

                            index = response.index;
                            count = response.count;
                            showImportMessage(response.message, count, index);
                        } else {
                            showImportMessage(response.message);
                            removeAlertLeavePage();
                        }
                    } else {
                        removeAlertLeavePage();
                    }
                }).fail(function(response) {
                    if (index < count) {
                        var requests = {'action': 'porto_import_mastersliders'};
                        requests.index = ++index;

                        import_mastersliders(requests);
                    } else {
                        removeAlertLeavePage();
                    }
                });
            }

            import_mastersliders(data);
        }
    });

    // import icons
    $('.redux-container .import_porto_icons').click(function(e) {
        e.preventDefault();

        if (confirm('Do you want to import simple line icon?')) {
            addAlertLeavePage();
            showImportMessage('Importing...');

            var data = {'action': 'porto_import_icons'};
            jQuery.post(ajaxurl, data, function(response) {
                showImportMessage(response);
                removeAlertLeavePage();
            }).fail(function(response) {
                    removeAlertLeavePage();
                });
        }
    });

});

