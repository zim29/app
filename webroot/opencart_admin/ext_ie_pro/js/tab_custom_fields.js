function custom_fields_disable_tables(category_select) {
    var row = category_select.closest('tr');
    var category = category_select.val();
    var options_allowed = custom_fields_tables_allowed[category];
    var select_tables = row.find('select.table');
    var current_value = select_tables.val();

    select_tables.html('');

    if(typeof options_allowed !== 'undefined')
    $.each(options_allowed, function(i, value) {
        select_tables.append("<option value=" + value + ">" + value + "</option>");
    });

    select_tables.val(current_value);
    select_tables.selectpicker('refresh');
}

$(function(){
    var custom_fields_inputs_area = $('div#tab-custom-fields').length ? $('div#tab-custom-fields') : $('div#tab-Произвольные-поля');
    var table = custom_fields_inputs_area.find('table');
    var rows = parseInt(table.data('rows'));
    if(rows > 0) {
        table.find('tr:not(.model_row) select.category').each(function(){
           custom_fields_disable_tables($(this));
        });
    }
});

function save_custom_fields_configuration() {
    var custom_fields_inputs_area = $('div#tab-custom-fields').length ? $('div#tab-custom-fields') : $('div#tab-Произвольные-поля');

    if($('div#tab-custom-fields').length)
        config_values = $('div#tab-custom-fields input, div#tab-custom-fields select');
    else
        config_values = $('div#tab-Произвольные-поля input, div#tab-Произвольные-поля select');

    custom_fields_inputs_area.find('input[type="checkbox"]').each(function(){
        if($(this).is(':checked')) {
            $(this).attr("checked", "checked");
        } else {
            $(this).removeAttr("checked");
        }
    });

    config_values_serialized = config_values.serialize();

    var request = $.ajax({
        url: custom_fields_save_configuration_url,
        dataType: 'json',
        data: config_values_serialized,
        type: "POST",
        beforeSend: function (data) {
            ajax_loading_open();
        },
        success: function (data) {
            ajax_loading_close();
            if (data.error)
                open_manual_notification(data.message, 'warning', 'exclamation');
            else
                open_manual_notification(data.message, 'success', 'ok');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ajax_loading_close();
            open_manual_notification(thrownError, 'warning', 'exclamation');
        }
    });
}