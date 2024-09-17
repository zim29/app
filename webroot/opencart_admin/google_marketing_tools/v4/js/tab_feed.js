$(function(){
    reset_profiles();

    $('.conditional_feed_select select').on('change', function(){
        conditional_feed_select_trigger($(this));
    });
});

function save_configuration_ajax_extended() {
    var tab_feed = _get_tab_feeds();

    if( tab_feed.hasClass('active') && tab_feed.find('div.button_save_profile').css('display') == 'block' ) {
        ajax_loading_close();

        tab_feed.find('div.button_save_profile').find('a').click();
        return false;
    }
    return true;
}

function conditional_feed_select_trigger(select) {
    var select_name = select.attr('name');
    var name_splitted = select_name.split('_');

    name_splitted.pop();
    name_splitted = name_splitted.join('_');

    $('select[name^="'+name_splitted+'"]').each(function() {
       if( $(this).attr('name') != select_name ) {
           $(this).children('option').each(function() {
               $(this).removeAttr('selected');
           });

           $(this).selectpicker('refresh');
       }
    });
}

function _get_tab_feeds() {
    var tab_feeds = $('div#tab-feeds').length ? $('div#tab-feeds') : $('div#tab-Профили');
    return tab_feeds;
}

function reset_profiles() {
    var tab_feeds = _get_tab_feeds();
    tab_feeds.find('.feed_configuration_value, .feed_configuration_product, .feed_configuration_save, .feed_configuration_exclusive').hide();
}

function feed_create(feed_id) {
    var tab_feeds = _get_tab_feeds();
    feed_id = typeof feed_id != 'undefined' ? feed_id : '';

    reset_profiles();

    $('input[name="feed_id"]').val(feed_id);

    tab_feeds.find('.feed_configuration_value').show();
    
    autocheck_feed_configuration();
}

function autocheck_feed_configuration() {
    var tab_feeds = _get_tab_feeds();
    
    check_destiny(tab_feeds.find('select[name="google_all_feed_config_file_destiny"]').val());
    check_feed_type(tab_feeds.find('select[name="google_all_feed_config_file_format"]').val());
}

function check_destiny(destiny) {
    var tab_feeds = _get_tab_feeds();
    tab_feeds.find('.feed_configuration_value.server, .feed_configuration_value.ftp').hide();
    
    if (destiny == 'server') {
        tab_feeds.find('.feed_configuration_value.server').show();
    } else if (destiny == 'external_server') { 
        tab_feeds.find('.feed_configuration_value.ftp').show();
    }
}

function get_current_feed_id() {
    return $('input[name="feed_id"]').val();
}

function remodal_event(selector) {
    $(selector).find('div.remodal').each(function() {
        var remodal_id = $(this).attr('data-remodal-id');

        if( $('div.remodal-wrapper > div.'+remodal_id).length > 0 ) {
            $('div.remodal-wrapper > div.'+remodal_id).parent().remove();
        }

        $(this).remodal();
    });
}

function check_feed_type(type) {
    $('div.feed_configuration_product').hide();

    if( type == '' ) { 
        return false;
    }

    if( type != 'google_reviews' ) {
        $('div.feed_configuration_product').show();
    }

    feed_get_filters_html();

    $('div.feed_configuration_save').show();

    if( get_current_feed_id() != '' ) {
        $('.button_delete_profile').show();
    } else {
        $('.button_delete_profile').hide();
    }

    $('.feed_configuration_exclusive').hide();
    $('.feed_configuration_exclusive.'+type).show();

    $('.also_in_'+type).show();
    check_toogle_main_fields_ready();
}

function feed_get_filters_html() {
    var feed_id = get_current_feed_id();
    var container = $('.filters_configuration');

    var request = $.ajax({
        url: get_filters_html_url,
        dataType: 'json',
        data: {'feed_id' : feed_id},
        type: "POST",
        beforeSend: function (data) {},
        success: function (data) {
            var selector = '.filters_configuration';
            
            container.html(data.html);
            container.find('select').selectpicker();

            var filter_table = container.find('table tbody');
            
            filter_table.find('tr:not(.filter_model)').each(function () {
                feed_filter_reset_profile($(this));
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            container.html(xhr.responseText);
        }
    });
}

String.prototype.replaceAll = function(searchStr, replaceStr) {
	var str = this;
    // no match exists in string?
    if(str.indexOf(searchStr) === -1) {
        // return string
        return str;
    }
    // replace and remove first match, and do another recursirve search/replace
    return (str.replace(searchStr, replaceStr)).replaceAll(searchStr, replaceStr);
}

function input_autocomplete_products(input_name, product_ids) {
    var request = $.ajax({
        url: get_products_autocomplete_url,
        dataType: 'json',
        data: {'product_ids' : product_ids},
        type: "POST",
        success: function (data) {
            $("table#" + input_name + ' tbody').html(data.html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            open_manual_notification(thrownError, 'warning', 'exclamation');
        }
    });
}

function feed_load(select) {
    var feed_id = select.val();

    $('input[name="feed_id"]').val(feed_id);

    if( feed_id != '' ) {
        var request = $.ajax({
            url: feed_load_url,
            dataType: 'json',
            data: {feed_id: feed_id},
            type: "POST",
            beforeSend: function (data) {
                ajax_loading_open();
            },
            success: function (data) {
                ajax_loading_close();

                if( !data.error ) {
                    feed_create(data.id);

                    $.each(data.profile, function(field_name, val) {
                        if($('input[name="' + field_name + '"]').length > 0 && $('input[name="' + field_name + '"]').hasClass("products_autocomplete")) {
                            input_autocomplete_products(field_name, val);
                        } else {
                            var input = $('input[name="' + field_name + '"]');

                            if( input.length > 0 ) {
                                var type = input.attr('type');

                                if (type == 'text') { 
                                    input.val(val);
                                } else if (type == 'checkbox') {
                                    if (val == 1) { 
                                        input.prop('checked', true);
                                    } else {
                                        input.prop('checked', false);
                                    }
                                }
                            } else {
                                var select = $('select[name="' + field_name + '"]');

                                if( select.length > 0 ) {
                                    if( val !== '' ) { 
                                        select.val(val);
                                    }

                                    select.selectpicker('refresh');
                                } else {
                                    var select = $('select[name="' + field_name + '[]"]');

                                    if( select.length > 0 ) {
                                        if (val !== '') { 
                                            select.val(val);
                                        }

                                        select.selectpicker('refresh');
                                    } else {
                                        var textarea = $('textarea[name="' + field_name + '"]');

                                        if( textarea.length > 0 ) { 
                                            textarea.val(val);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    autocheck_feed_configuration();
                } else {
                    open_manual_notification(data.message, 'warning', 'exclamation');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                open_manual_notification(thrownError, 'warning', 'exclamation');
                ajax_loading_close();
            }
        });
    }
}

function feed_add_filter(button_pressed) {
    var model_row = button_pressed.closest('table').find('tr.filter_model');
    var filter_number = parseInt(model_row.attr('data-filternumber'));
    var clone = model_row.html();
    
    tr = clone.replaceAll('replace_by_number', (filter_number));

    table = $('div.filters_configuration table tbody');

    table.append('<tr>'+tr+'</tr>');

    //Reset all filter fields
        tr_inserted = table.find('tr').last();
        tr_inserted.find('.btn.dropdown-toggle').remove();
		tr_inserted.find('select.selectpicker').selectpicker();
        feed_filter_reset_profile(tr_inserted);
    //END Reset all filter fields

    button_pressed.closest('table').find('tr.filter_model').attr('data-filternumber', (filter_number+1));
    feed_filter_show_hide_main_conditional();
}

function feed_filter_show_hide_main_conditional() {
    var filter_table = $('div.filters_configuration table');
    var filter_number = parseInt(filter_table.find('tbody tr:not(.filter_model)').length);

    var tfoot = filter_table.find('tfoot');
    
    if (filter_number > 0) {
        tfoot.show();
    } else { 
        tfoot.hide();
    }
}

function feed_filter_reset_profile(tr) {
    var row_fields = tr.find('td.fields');
    var row_conditionals = tr.find('td.conditionals');
    var row_values = tr.find('td.values');

    tr.find('td.conditionals > div, td.values > input').hide();
    tr.find('td.values > div').hide();

    var field_value = row_fields.find('select').val();
    
    field_value_split = field_value.split('-');
    
    var type = field_value_split[2];
    
    tr.find('td.conditionals > div.conditional.' + type).show();

    if( type != 'boolean' ) { 
        tr.find('td.values > input').show();
    }

    feed_filter_show_hide_main_conditional();
}

function feed_remove_filter(button_pressed) {
    button_pressed.closest('tr').remove();
    feed_filter_show_hide_main_conditional();
}

function get_feed_configuration_values() {
    var tab_feeds = _get_tab_feeds();
    find_string = 'input[type=checkbox]:checked, ';
    find_string += 'input[type=text], ';
    find_string += 'input[type=hidden], ';
    find_string += 'select';

    var config_values = tab_feeds.find(find_string);
    return config_values;
}

function feed_check_uncheck_all(checkbox) {
    var checked = checkbox.is(':checked');
    var table = checkbox.closest('table').find('tbody');
    
    table.find('input[type="checkbox"]').prop('checked', checked);
}

function feed_delete(type) {
    var request = $.ajax({
        url: feed_delete_url,
        dataType: 'json',
        data: {feed_id : get_current_feed_id()},
        type: "POST",
        beforeSend: function (data) {
            ajax_loading_open();
        },
        success: function (data) {
            if (data.error) {
                ajax_loading_close();
                open_manual_notification(data.message, 'warning', 'exclamation');
            } else { 
                location.reload();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ajax_loading_close();
            open_manual_notification(thrownError, 'warning', 'exclamation');
        }
    });
}

function feed_save() {
    var config_values = get_feed_configuration_values();
    config_values = config_values.serialize();
    var request = $.ajax({
        url: feed_save_url,
        dataType: 'json',
        data: config_values,
        type: "POST",
        beforeSend: function (data) {
            ajax_loading_open();
        },
        success: function (data) {
            if( data.error ) {
                ajax_loading_close();
                open_manual_notification(data.message, 'warning', 'exclamation');
            } else {
                location.reload();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ajax_loading_close();
            open_manual_notification(thrownError, 'warning', 'exclamation');
        }
    });
}

function copy_to_clickboard(value) {
    remodal_notification(clipboard_copied, 'success', 'before');

    var $temp = $("<input>");
    $("body").append($temp);

    $temp.val(value).select();
    document.execCommand("copy");
    $temp.remove();
}

function open_tab_taxonomy() {
    if( typeof bootstrap === 'undefined' ) {
		console.log('bootstrap js not defined');
		return;
	}
	
	var taxonomyTabTrigger = document.querySelector('ul.nav-tabs li a.tab_taxonomy');
	
	bootstrap.Tab.getOrCreateInstance(taxonomyTabTrigger).show();
}