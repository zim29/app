$(function(){
    reset_profiles();
});

$(document).on('change', 'td.fields select[name*=field]', function(){
    show_hide_switch_button($(this));
});

function disable_profile_inputs() {
    if(typeof extension_version === 'undefined')
        return true;
    if(extension_version >= 875)
        return false;
}
function show_hide_switch_button(select){
    var value = $(select).val();
    var valueArr = value.split('-');
    if (valueArr[4] !== undefined && valueArr[4] === 'allow_ids'){
        var optionSelected = $(select).children('option:selected')[0];
        if ($(optionSelected).attr('allow-ids') != "true"){
            $(select).parent().parent().find('div.switch-allow-ids input').prop("checked", false);
            $(select).parent().parent().parent().find('div.switch-allow-ids input').prop("checked", false);
        }
        else{
            $(select).parent().parent().find('div.switch-allow-ids input').prop("checked", true);
            $(select).parent().parent().parent().find('div.switch-allow-ids input').prop("checked", true);
        }
        $(select).parent().parent().children('div.switch-allow-ids').css("display", "block");
        $(select).parent().parent().parent().children('div.switch-allow-ids').css("display", "block");
    }
    else{
        $(select).parent().parent().children('div.switch-allow-ids').css("display", "none");
        $(select).parent().parent().parent().children('div.switch-allow-ids').css("display", "none");
    }
}

function update_field_type($elm){
    var select = $elm.parent().parent().parent().find('select[name*=field]')[0];
    var optionSelected = $(select).children('option:selected')[0];
    var value = $(select).val();
    var valueArr = value.split('-');
    if ($($elm).prop('checked')){
        $(optionSelected).attr('allow-ids', true);
        valueArr[3] = 'number';
    }
    else{
        $(optionSelected).attr('allow-ids', false);
        valueArr[3] = 'string';
    }
    $(optionSelected).val(valueArr.join('-'));
    $(select).val(valueArr.join('-'));
    profile_filter_reset_profile($(select).closest('tr'));
}

$(document).on('change', 'td.applyto select[name*=applyto]', function () {
    var inputNameArr = $(this).attr('name').split('[');
    var inputNameArr = inputNameArr[1].split(']');
    var index = inputNameArr[0];
    if ($(this).val() == 'shop'){
        var actionsSelect = $('td.actions select[name="export_filter[' + index +'][action]"]');
        actionsSelect.children('option').each(function () {
            if ($(this).val() == 'skip') {
                $(this).prop('disabled', true);
            }
        });
        actionsSelect.val('delete');
        actionsSelect.selectpicker('render');
        updateFieldNames(index, 'shop');
    }
    else{
        $('td.actions select[name="export_filter[' + index +'][action]"]').children('option').each(function () {
            if ($(this).val() == 'skip') {
                $(this).prop('disabled', false);
                $(this).parent().selectpicker('render');
            }
        });
        updateFieldNames(index, 'file');
    }
});

$( document ).ajaxComplete(function() {
    $('td.applyto select[name*=applyto]').each(function(){
        var inputNameArr = $(this).attr('name').split('[');
        inputNameArr = inputNameArr[1].split(']');
        var index = inputNameArr[0];
        if ($(this).val() == 'shop'){
            var actionsSelect = $('td.actions select[name="export_filter[' + index +'][action]"]');
            actionsSelect.children('option').each(function () {
                if ($(this).val() == 'skip') {
                    $(this).prop('disabled', true);
                }
            });
            actionsSelect.selectpicker('render');
            updateFieldNames(index, 'shop');
        }
        else
            updateFieldNames(index, 'file');
    });

    //updating filters switch buttons
    $('td.fields select[name*=field]').each(function () {
        show_hide_switch_button($(this));
    })
});

function updateFieldNames(index, type){
    var select = $('td.fields select[name="export_filter[' + index + '][field]"]');
    var showOptionsProcessed = [];
    select.children('option').each(function(){
        $(this).show();
        var value = $(this).val();
        var valueArr = value.split('-');
        if (type == 'file'){
            var name = valueArr[2];
            name = name.split('_').join(' ');
            $(this).html(name);
        }
        else if (type == 'shop') {
            var html_name = jsUcfirst(valueArr[0]).split('_').join(' ') + ' - ' + jsUcfirst(valueArr[1]).split('_').join(' ') + ' (' + valueArr[3] + ')';
            var name = valueArr[0] + '-' + valueArr[1];
            $(this).html(html_name);
            if (showOptionsProcessed.indexOf(name) >= 0) {
                $(this).hide();
            }
            else{
                showOptionsProcessed.push(name);
            }

        }
    });
    select.selectpicker('refresh');
}

function jsUcfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

$(document).on('confirmation', '.remodal.profile_import_spreadsheet_remodal', function () {
    var button_confirm_text = remodal_button_confirm_get_text();

    var formData = new FormData();
    formData.append('file', $('input[name="spreadsheet_json"]')[0].files[0]);

    $.ajax({
        url: spread_sheet_upload_json,
        data: formData,
        type: "POST",
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function(data) {
            remodal_button_confirm_loading_on();
        },
        success: function(data) {
            remodal_button_confirm_loading_off(button_confirm_text);
            if(data.error) {
                remodal_notification(data.message);
            } else {
                remodal_notification(data.message, 'success');
                setTimeout( function(){
                    location.reload();
                }  , 4000 );
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            remodal_button_confirm_loading_off(button_confirm_text);
            remodal_notification(thrownError);
        }
    });
});

$(document).on('change', '.configuration:not(.columns_configuration):not(.columns_fixed_configuration):not(.filters_configuration):not(.no_refresh_columns) input[type="checkbox"], .configuration:not(.columns_configuration):not(.filters_configuration):not(.columns_fixed_configuration):not(.sort_order_configuration):not(.no_refresh_columns) select', function() {
    profile_get_columns_html();
});

$(document).on('change', 'input[name="import_xls_category_tree"]', function() {
    check_cat_tree_no_tree_toogle();
});

var finishTypingInterval = 1000;
var typingTimer;
var inputs_update_columns = '.configuration:not(.profile_name):not(.main_configuration):not(.filters_configuration):not(.columns_fixed_configuration):not(.sort_order_configuration):not(.no_refresh_columns):not(.columns_configuration) input[type="text"]:not(.custom_name):not(.default_value):not(.conditional_value):not(.extra_column_configuration)';
$(document).on('keyup', inputs_update_columns, function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function (){
        profile_get_columns_html();
        if (get_current_profile() == 'import')
            profile_get_filters_html();
    }, finishTypingInterval);
});

$(document).on('keydown', inputs_update_columns, function () {
    clearTimeout(typingTimer);
});

function reset_profiles() {
    var tab_profiles = _get_tab_profiles();
    tab_profiles.find('.profile_import, .profile_export, .profile_import.configuration').hide();
}

function _get_tab_profiles() {
    var tab_profiles = $('div#profiles');

    if($('div#tab-profiles').length)
        tab_profiles = $('div#tab-profiles');
    else if($('div#tab-ÐŸÑ€Ð¾Ñ„Ð¸Ð»Ð¸').length)
        tab_profiles = $('div#tab-ÐŸÑ€Ð¾Ñ„Ð¸Ð»Ð¸');
    else if($('div.container_create_profile').length)
        tab_profiles = $('div.container_create_profile');

    return tab_profiles;
}

function profile_create(type, profile_id) {
    var tab_profiles = _get_tab_profiles();
    profile_id = typeof profile_id!= 'undefined' ? profile_id : '';
    reset_profiles();
    $('input[name="profile_type"]').val(type);
    $('input[name="profile_id"]').val(profile_id);

    if(profile_id != '') {
        $('select[name="import_xls_i_want"]').attr('disabled', 'disabled').selectpicker('refresh');
    } else {
        tab_profiles.find('select[name="import_xls_profiles"]').val('').selectpicker('refresh');
        $('select[name="import_xls_i_want"]').removeAttr('disabled').selectpicker('refresh');
        $('input[name="import_xls_multilanguage"], input[name="import_xls_category_tree"]').removeAttr('disabled');
        $('.profile_import.configuration.products input[type="text"]').removeAttr('disabled');
    }

    var tab_profiles = _get_tab_profiles();
    tab_profiles.find('.profile_'+type+':not(.configuration)').show();
    tab_profiles.find('.profile_'+type+'.main_configuration').show();

    if(profile_id != '') {
        $('.button_delete_profile.profile_'+type).show();
    } else {
        $('.button_delete_profile').hide();
    }

    profile_check_format();

    tab_profiles.find('.profile_import.spreadsheet_name, .profile_import.ftp, .profile_import.url').hide();
    if(type == 'import') {
        tab_profiles.find('.profile_export:not(.profile_import)').hide();
    } else {
        tab_profiles.find('.profile_import:not(.profile_export)').hide();
    }

    if(profile_id == '')
        profile_check_i_want();
}

function check_cat_tree_no_tree_toogle() {
    var checked = $('input[name="import_xls_category_tree"]').is(':checked');
    var container_cat_number = $('input#import_xls_cat_number').closest('div.form-group-columns');
    var container_cat_tree_parent_number = $('input#import_xls_cat_tree_number').closest('div.form-group-columns');
    var container_cat_tree_children_number = $('input#import_xls_cat_tree_children_number').closest('div.form-group-columns');
    var container_cat_tree_last_child_assign = $('input[name="import_xls_category_tree_last_child"]').closest('div.form-group-columns');
    container_cat_number.hide();
    container_cat_tree_parent_number.hide();
    container_cat_tree_children_number.hide();
    container_cat_tree_last_child_assign.hide();

    if(checked) {
        container_cat_tree_parent_number.show();
        container_cat_tree_children_number.show();
        //if(get_current_profile() == 'import')
        container_cat_tree_last_child_assign.show();
    } else {
        container_cat_number.show();
    }
}
function profile_load(select) {
    var profile_id = select.val();

    $('input[name="profile_id"]').val(profile_id);
    if(profile_id != '') {
        var request = $.ajax({
            url: profile_load_url,
            dataType: 'json',
            data: {profile_id: profile_id},
            type: "POST",
            beforeSend: function (data) {
                ajax_loading_open();
            },
            success: function (data) {
                ajax_loading_close();
                if (!data.error) {
                    profile_create(data.type, data.id);
                    $.each(data.profile, function (field_name, val) {
                        if (field_name != 'columns') {
                            var input = $('input[name="' + field_name + '"]');

                            if (input.length > 0) {
                                var type = input.attr('type');
                                if (type == 'text')
                                    input.val(val);
                                else if (type == 'checkbox') {
                                    if (val == 1)
                                        input.prop('checked', true);
                                    else
                                        input.prop('checked', false);
                                }
                            }
                            else {
                                var select = $('select[name="' + field_name + '"]');
                                if (select.length > 0) {
                                    select.val(val);
                                    select.selectpicker('refresh');
                                }
                                else {
                                    var select = $('select[name="' + field_name + '[]"]');
                                    if (select.length > 0) {
                                        select.val(val);
                                        select.selectpicker('refresh');
                                    }
                                    else {
                                        var textarea = $('textarea[name="' + field_name + '"]');
                                        if (textarea.length > 0)
                                            textarea.val(val);
                                    }
                                }
                            }
                        }
                    });
                    profile_check_i_want();

                    if (data.profile.import_xls_i_want == 'products' && disable_profile_inputs()) {
                        $('input[name="import_xls_multilanguage"], input[name="import_xls_category_tree"]').attr('disabled', 'disabled');
                        $('.profile_import.configuration.products input[type="text"]:not(#import_xls_profile_name):not(#import_xls_download_image_route)').attr('disabled', 'disabled');
                    }
                }
                else
                    open_manual_notification(data.message, 'warning', 'exclamation');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                open_manual_notification(thrownError, 'warning', 'exclamation');
                ajax_loading_close();
            }
        });
    }
}

function profile_check_format(format) {

    var profile_type = get_current_profile();

    format = typeof format == 'undefined' ? get_current_format() : format;

    var tab_profiles = _get_tab_profiles();

    tab_profiles.find('.node_xml').hide();
    tab_profiles.find('.spreadsheet_name').hide();
    tab_profiles.find('.csv_separator').hide();
    tab_profiles.find('.force_utf8').hide();
    tab_profiles.find('.only_csv').hide();

    tab_profiles.find('a[data-remodal-target="mapping_xml_columns"]').css('display', 'none');

    if(profile_type == 'import') {
        tab_profiles.find('.profile_import.file_origin').hide();
        if (format == 'spreadsheet') {
            tab_profiles.find('.profile_import.file_origin').hide();
        } else if (format != 'spreadsheet') {
            tab_profiles.find('.profile_import.file_origin').show();
        }
        if(format == 'xml')
            tab_profiles.find('a[data-remodal-target="mapping_xml_columns"]').css('display', 'block');

        profile_import_check_origin(tab_profiles.find('select[name="import_xls_file_origin"]').val());
    } else if(profile_type == 'export') {
        tab_profiles.find('.profile_export.file_destiny').hide();

        if (format == 'spreadsheet') {
            tab_profiles.find('.profile_export.file_destiny').hide();
        } else if (format != 'spreadsheet') {
            tab_profiles.find('.profile_export.file_destiny').show();
        }
        profile_export_check_destiny(tab_profiles.find('select[name="import_xls_file_destiny"]').val());
    }

    if(format == 'xml') {
        tab_profiles.find('.node_xml').show();
    }
    else if(format == 'spreadsheet') {
        tab_profiles.find('.spreadsheet_name').show();
    }else if(format == 'csv') {
        tab_profiles.find('.csv_separator').show();
        tab_profiles.find('.only_csv').show();
        if(profile_type == 'import')
            tab_profiles.find('.force_utf8').show();
    }
}

function profile_import_check_origin(origin) {
    var format = get_current_format();
    var tab_profiles = _get_tab_profiles();
    tab_profiles.find('.profile_import.ftp, .profile_import.url').hide();
    if(origin == 'ftp' && format != 'spreadsheet')
        tab_profiles.find('.profile_import.ftp').show();
    else if(origin == 'url' && format != 'spreadsheet')
        tab_profiles.find('.profile_import.url').show();
}

function profile_export_check_destiny(destiny) {
    var tab_profiles = _get_tab_profiles();
    tab_profiles.find('.profile_export.server, .profile_export.ftp').hide();
    if(destiny == 'server')
        tab_profiles.find('.profile_export.server').show();
    else if(destiny == 'external_server')
        tab_profiles.find('.profile_export.ftp').show();
}

function profile_check_i_want() {
    var type = get_current_profile();
    var tab_profiles = _get_tab_profiles();
    var i_want = get_i_want();
    tab_profiles.find('.profile_'+type+'.configuration').hide();
    tab_profiles.find('.profile_'+type+'.configuration.main_configuration').show();
    profile_check_format();
    if(i_want != '') {
        tab_profiles.find('.profile_' + type + '.configuration.' + i_want).show();
        tab_profiles.find('.profile_' + type + '.configuration.generic').show();
        profile_get_columns_html();
        profile_get_filters_html();
        if(type == 'export') {
            profile_get_columns_fixed_html();
            profile_get_sort_order_html();
        }
        $('div.legend_save_profile legend').trigger('click');
    }
    if(i_want == 'products')
        check_cat_tree_no_tree_toogle();
}

function get_current_format() {
    var format = $('select[name="import_xls_file_format"]').val();
    return format;
}

function get_current_profile() {
    return $('input[name="profile_type"]').val();
}

function get_current_profile_id() {
    return $('input[name="profile_id"]').val();
}

function profile_get_columns_html() {
    var selector = get_config_selector();
    var config_values = get_profile_configuration_values();
    var type = get_current_profile();
    var i_want = get_i_want();
    var container = $('.columns_configuration');
    if(i_want != '') {
        var request = $.ajax({
            url: get_columns_html_url,
            dataType: 'json',
            data: config_values,
            type: "POST",
            beforeSend: function (data) {
                container.html('');
                ajax_loading_open(container);
            },
            success: function (data) {
                ajax_loading_close(container);
                container.html(data.html);
                container.find('table').sortable({
                    containerSelector: 'table',
                    itemPath: '> tbody',
                    itemSelector: 'tr',
                    handle: 'i.fa-reorder',
                    placeholder: '<tr class="placeholder"/>'
                });
                container.find('select').selectpicker();
                remodal_event(container);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                ajax_loading_close(container);
                container.html(xhr.responseText);
            }
        });
    }
}

function remodal_event(selector) {

    $(selector).find('div.remodal').each(function(){
        var remodal_id = $(this).attr('data-remodal-id');
        if($('div.remodal-wrapper > div.'+remodal_id).length > 0) {
            $('div.remodal-wrapper > div.'+remodal_id).parent().remove();
        }
        $(this).remodal();
    });
}

function profile_get_filters_html() {
    var selector = get_config_selector();
    var config_values = get_profile_configuration_values();
    var type = get_current_profile();
    var i_want = get_i_want();
    var profile_id = get_current_profile_id();
    var container = $('.filters_configuration');

    if(i_want != '') {
        var request = $.ajax({
            url: get_filters_html_url,
            dataType: 'json',
            data: config_values,
            type: "POST",
            beforeSend: function (data) {
                container.html('');
                ajax_loading_open(container);
            },
            success: function (data) {
                var selector = '.filters_configuration';
                container.html(data.html);
                container.find('select').selectpicker();

                var filter_table = container.find('table tbody');
                filter_table.find('tr:not(.filter_model)').each(function(){
                    profile_filter_reset_profile($(this));
                });
                ajax_loading_close(container);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                container.html(xhr.responseText);
                ajax_loading_close(container);
            }
        });
    }
}

function profile_get_columns_fixed_html() {
    if($('div.columns_fixed_configuration').length) {
        var selector = get_config_selector();
        var config_values = get_profile_configuration_values();
        var type = get_current_profile();
        var i_want = get_i_want();
        var profile_id = get_current_profile_id();
        var container = $('.columns_fixed_configuration');

        if (i_want != '') {
            var request = $.ajax({
                url: get_columns_fixed_html_url,
                dataType: 'json',
                data: config_values,
                type: "POST",
                beforeSend: function (data) {
                    container.html('');
                    ajax_loading_open(container);
                },
                success: function (data) {
                    var selector = '.columns_fixed_configuration';
                    container.html(data.html);
                    remodal_event(container);
                    ajax_loading_close(container);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    container.html(xhr.responseText);
                    ajax_loading_close(container);
                }
            });
        }
    }
}


function profile_get_sort_order_html() {
    var selector = get_config_selector();
    var config_values = get_profile_configuration_values();
    var type = get_current_profile();
    var i_want = get_i_want();
    var profile_id = get_current_profile_id();
    var container = $('.sort_order_configuration');

    if(i_want != '') {
        var request = $.ajax({
            url: get_sort_order_html_url,
            dataType: 'json',
            data: config_values,
            type: "POST",
            beforeSend: function (data) {
            },
            success: function (data) {
                container.html(data.html);
                container.find('select').selectpicker();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                container.html(xhr.responseText);
            }
        });
    }
}

function profile_add_filter(button_pressed) {
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
    profile_filter_reset_profile(tr_inserted);
    //END Reset all filter fields

    button_pressed.closest('table').find('tr.filter_model').attr('data-filternumber', (filter_number+1));
    profile_filter_show_hide_main_conditional();
}

function profile_filter_show_hide_main_conditional() {
    var filter_table = $('div.filters_configuration table');
    var filter_number = parseInt(filter_table.find('tbody tr:not(.filter_model)').length);

    var tfoot = filter_table.find('tfoot');
    if(filter_number > 0)
        tfoot.show();
    else
        tfoot.hide();
}

function profile_filter_reset_profile(tr) {
    var row_fields = tr.find('td.fields');
    var row_conditionals = tr.find('td.conditionals');
    var row_values = tr.find('td.values');
    tr.find('td.conditionals > div, td.values > input').hide();
    tr.find('td.values > div').hide();
    var field_value = row_fields.find('select').val();
    field_value_split = field_value.split('-');
    if (get_current_profile() == 'import')
        var type = field_value_split[3];
    else
        var type = field_value_split[2];

    tr.find('td.conditionals > div.conditional.'+type).show();

    if(type != 'boolean')
        tr.find('td.values > input').show();
    profile_filter_show_hide_main_conditional();
}

function profile_remove_filter(button_pressed) {
    button_pressed.closest('tr').remove();
    profile_filter_show_hide_main_conditional();
}

function profile_add_column_fixed(button_pressed) {
    var model_row = button_pressed.closest('table').find('tr.custom_column_fixed_model');
    var filter_number = parseInt(model_row.attr('data-customcolumnfixednumber'));
    var clone = model_row.html();
    tr = clone.replaceAll('replace_by_number', (filter_number));

    table = $('div.columns_fixed_configuration table tbody');

    table.append('<tr>'+tr+'</tr>');

    button_pressed.closest('table').find('tr.custom_column_fixed_model').attr('data-customcolumnfixednumber', (filter_number+1));
}
function profile_remove_column_fixed(button_pressed) {
    button_pressed.closest('tr').remove();
}

function profile_get_custom_names_from_profile(select) {
    var profile_id = select.val();
    if(profile_id != '') {
        var request = $.ajax({
            url: get_columns_from_profile_url,
            dataType: 'json',
            data: {profile_id : profile_id},
            type: "POST",
            beforeSend: function (data) {
                ajax_loading_open();
            },
            success: function (data) {
                $.each(data, function( real_name, column_data ) {
                    $('div.columns_configuration').find('input[name="columns['+real_name+'][custom_name]"]').val(column_data.custom_name);
                    $('div.columns_configuration').find('input[name="columns['+real_name+'][default_value]"]').val(column_data.default_value);
                    $('div.columns_configuration').find('input[name="columns['+real_name+'][conditional_value]"]').val(column_data.conditional_value);
                    if(typeof column_data.true_value != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][true_value]"]').val(column_data.true_value);
                    }
                    if(typeof column_data.false_value != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][false_value]"]').val(column_data.false_value);
                    }

                    if(typeof column_data.product_id_identificator != 'undefined') {
                        $('div.columns_configuration').find('select[name="columns['+real_name+'][product_id_identificator]"]').val(column_data.product_id_identificator).selectpicker('refresh');;
                    }

                    if(typeof column_data.name_instead_id != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][name_instead_id]"]').prop('checked', 'checked');
                    } else if($('div.columns_configuration').find('input[name="columns['+real_name+'][name_instead_id]"]').length) {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][name_instead_id]"]').prop('checked', false);
                    }

                    if(typeof column_data.id_instead_of_name != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][id_instead_of_name]"]').prop('checked', 'checked');
                    } else if($('div.columns_configuration').find('input[name="columns['+real_name+'][id_instead_of_name]"]').length) {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][id_instead_of_name]"]').prop('checked', false);
                    }

                    if(typeof column_data.image_full_link != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][image_full_link]"]').prop('checked', 'checked');
                    } else if($('div.columns_configuration').find('input[name="columns['+real_name+'][image_full_link]"]').length) {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][image_full_link]"]').prop('checked', false);
                    }

                    if(typeof column_data.status != 'undefined') {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][status]"]').prop('checked', 'checked');
                    } else if($('div.columns_configuration').find('input[name="columns['+real_name+'][status]"]').length) {
                        $('div.columns_configuration').find('input[name="columns['+real_name+'][status]"]').prop('checked', false);
                    }
                });
                ajax_loading_close();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
                ajax_loading_close();
            }
        });
    }
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

function get_config_selector() {
    var type = get_current_profile();
    var i_want = get_i_want();
    var tab_profiles = _get_tab_profiles();
    var selector = '.profile_'+type+'.'+i_want;
    return selector;
}

function  get_profile_configuration_values() {
    var selector = get_config_selector();
    var tab_profiles = _get_tab_profiles();
    var type = get_current_profile();

    var i_want = get_i_want();

    if(i_want != '') {
        find_string = selector + ' input[type=checkbox]:checked, ';
        find_string += selector + ' input[type=text], ';
        find_string += selector + ' input[type=hidden], ';
        find_string += selector + ' select, input[type="hidden"], ';

        find_string += ' .profile_' + type + '.main_configuration input[type=checkbox]:checked, ';
        find_string += ' .profile_' + type + '.main_configuration input[type="text"], ';
        find_string += ' .profile_' + type + '.main_configuration input[type="hidden"], ';
        find_string += ' .profile_' + type + '.main_configuration select,';

        find_string += ' .profile_' + type + '.configuration.generic input[type=checkbox]:checked, ';
        find_string += ' .profile_' + type + '.configuration.generic input[type="text"], ';
        find_string += ' .profile_' + type + '.configuration.generic input[type="hidden"], ';
        find_string += ' .profile_' + type + '.configuration.generic select';

        var config_values = tab_profiles.find(find_string);

        return config_values;
    } else {
        return false;
    }
}

function profile_check_uncheck_all(checkbox) {
    var checked = checkbox.is(':checked');
    var table = checkbox.closest('table').find('tbody');
    table.find('input[type="checkbox"]').prop('checked', checked);
}

function get_i_want() {
    var type = get_current_profile();
    return $('.profile_'+type).find('select[name="import_xls_i_want"]').val();
}

function profile_delete(type) {
    var request = $.ajax({
        url: profile_delete_url,
        dataType: 'json',
        data: {profile_id : get_current_profile_id()},
        type: "POST",
        beforeSend: function (data) {
            ajax_loading_open();
        },
        success: function (data) {
            if (data.error) {
                ajax_loading_close();
                open_manual_notification(data.message, 'warning', 'exclamation');
            }
            else
                location.reload();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ajax_loading_close();
            open_manual_notification(thrownError, 'warning', 'exclamation');
        }
    });
}

function profile_save(type) {
    var i_want = get_i_want();
    if(i_want != '') {
        var config_values = get_profile_configuration_values();
        remove_disabled_from_all_inputs();
        config_values = config_values.serialize();
        var request = $.ajax({
            url: profile_save_url,
            dataType: 'json',
            data: config_values,
            type: "POST",
            beforeSend: function (data) {
                ajax_loading_open();
            },
            success: function (data) {
                if (data.error) {
                    ajax_loading_close();
                    open_manual_notification(data.message, 'warning', 'exclamation');
                }
                else
                    location.reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                ajax_loading_close();
                open_manual_notification(thrownError, 'warning', 'exclamation');
            }
        });
    } else {
        open_manual_notification(profile_error_uncompleted, 'warning', 'exclamation');
    }
}

function remove_disabled_from_all_inputs() {
    var container = _get_tab_profiles();
    container.find('select:disabled, input:disabled, textarea:disabled').removeAttr('disabled');

}

$(document).on('ready', function(){
    $('div.container_create_profile_steps legend').on('click', function () {
        profile_toggle_step($(this));
    })
});

function profile_toggle_step(legend_pressed) {
    var container_step = legend_pressed.closest('div.form-group').nextAll('div.container_step').first();
    if(container_step.is(':visible'))
        legend_pressed.removeClass('opened');
    else
        legend_pressed.addClass('opened');
    container_step.slideToggle('fast');
}

function profile_reset_steps() {
    $('div.container_create_profile_steps legend').each(function(){
        $(this).removeClass('opened');
    });
    $('div.container_create_profile_steps div.container_step').each(function(){
        $(this).hide();
    });
}