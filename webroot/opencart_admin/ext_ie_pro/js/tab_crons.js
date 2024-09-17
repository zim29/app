$(document).on('opening', '[data-remodal-id=cron_config_remodal]', function () {
    $(this).find('div.alert').remove();
});

function get_cron_config(button_pressed) {
    var tr = button_pressed.closest('tr');
    var copy_cron = cron_command;

    var profile_id = tr.find('select.profile_id').val();
    if(profile_id == '') {
        open_manual_notification(cron_error_profile_id, 'warning', 'exclamation');
        return false;
    }
    copy_cron = copy_cron.replace("PROFILEID", profile_id);

    var time_config = '';
    var minutes = tr.find('select.minutes').val();
    time_config += !minutes ? '* ' : minutes.join(',')+' ';

    var hours = tr.find('select.hours').val();
    time_config += !hours ? '* ' : hours.join(',')+' ';

    var days = tr.find('select.days').val();
    time_config += !days ? '* ' : days.join(',')+' ';

    var months = tr.find('select.months').val();
    time_config += !months ? '* ' : months.join(',')+' ';

    var week_days = tr.find('select.week_days').val();
    time_config += !week_days ? '*' : week_days.join(',')+' ';

    copy_cron = copy_cron.replace("TIMECONFIG", time_config);

    var remodal_content = get_remodal_cron_config();
    remodal_content.find('input[name="cron_command"]').remove();
    remodal_content.append('<input name="cron_command" type="hidden" value="'+copy_cron+'">');

    remodal_content.find('input[name="profile_id"]').remove();
    remodal_content.append('<input name="profile_id" type="hidden" value="'+profile_id+'">');

    var exec_now = cron_link_to_exec_now;
    exec_now = exec_now.replace("PROFILEID", profile_id);
    remodal_content.find('a.cron_job_now').attr('href', exec_now);
    var inst = $('[data-remodal-id=cron_config_remodal]').remodal();
    inst.open();
}

function get_cron_url() {
    var path_to_php = $('input#import_xls_ie_pro_cron_path_php').length ? $('input#import_xls_ie_pro_cron_path_php').val() : $('input#ie_pro_cron_path_php').val();
    if(path_to_php == '') {
        remodal_notification(cron_error_path_to_php, 'danger', 'before');
        return false;
    }

    remodal_notification(cron_command_copied, 'success', 'before');
    var remodal_content = get_remodal_cron_config();
    var input = remodal_content.find('input[name="cron_command"]');
    copy_cron = input.val();
    copy_cron = copy_cron.replace("PATHPHP", path_to_php);

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(copy_cron).select();
    document.execCommand("copy");
    $temp.remove();
}

function get_cron_main_path() {
    remodal_notification(cron_command_copied, 'success', 'before');
    copy_cron = cron_main_path;

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(copy_cron).select();
    document.execCommand("copy");
    $temp.remove();
}

function get_cron_params() {
    remodal_notification(cron_command_copied, 'success', 'before');
    var remodal_content = get_remodal_cron_config();
    var profile_id = remodal_content.find('input[name="profile_id"]').val();
    copy_cron = cron_params;
    copy_cron = copy_cron.replace("PROFILEID", profile_id);

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(copy_cron).select();
    document.execCommand("copy");
    $temp.remove();
}

function save_cron_configuration() {
    var cron_inputs_area = $('div#tab-cron-jobs').length ? $('div#tab-cron-jobs') : $('div#tab-cron-задания');

    if($('div#tab-cron-jobs').length)
        config_values = $('div#tab-cron-jobs input, div#tab-cron-jobs select');
    else
        config_values = $('div#tab-cron-задания input, div#tab-cron-задания select');

    cron_inputs_area.find('input[type="checkbox"]').each(function(){
        if($(this).is(':checked')) {
            $(this).attr("checked", "checked");
        } else {
            $(this).removeAttr("checked");
        }
    });

    config_values_serialized = config_values.serialize();

    var request = $.ajax({
        url: cron_save_configuration_url,
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

function get_remodal_cron_config() {
    return $('[data-remodal-id=cron_config_remodal]').find('div.remodal_content');

}