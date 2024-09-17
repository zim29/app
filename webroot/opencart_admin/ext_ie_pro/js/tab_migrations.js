function migration_select_all_categories(checked) {
    var container = _get_tab_migration();
    container.find('div.profile_export_category input[type="checkbox"]').prop('checked', checked);
}

function _get_tab_migration() {
	if($('#tab-migrations-or-backups').length)
		return $('#tab-migrations-or-backups');
	else if($('#tab-Перенос-и-резервирование').length)
		return $('#tab-Перенос-и-резервирование');
	else if($('#tab-Перенесення-та-резервування').length)
    	return $('#tab-Перенесення-та-резервування');
}

function migration_export() {
    var container = _get_tab_migration();
    var formData = container.find('input[type="checkbox"]:checked, select');
    var button_confirm_text = remodal_button_confirm_get_text();

    $.ajax({
        url: clean_progress_url,
        dataType: 'json',
        data: {},
        type: "POST",
        beforeSend: function (data) { ajax_loading_open(); clean_remodal_progress(); },
        success: function (data) {
            $.ajax({
                url: migration_export_url,
                dataType: 'json',
                data: formData,
                type: "POST",
                beforeSend: function (data) {
                    ajax_loading_close();
                    var inst = get_remodal_instance();
                    inst.open();
                    ajax_check_import_process();
                },
                success: function (data) {},
                error: function (xhr, ajaxOptions, thrownError) {
                    cancel_process(xhr.responseText);
                }
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Internal error clearing previous progress.')
        }
    });
}

function migration_import() {
    var container = _get_tab_migration();

    var formData = new FormData();
    formData.append('file', container.find('input[name="migration_file"]')[0].files[0]);

    $.ajax({
        url: clean_progress_url,
        dataType: 'json',
        data: {},
        type: "POST",
        beforeSend: function (data) { ajax_loading_open(); clean_remodal_progress(); },
        success: function (data) {
            $.ajax({
                url: migration_import_url,
                dataType: 'json',
                data: formData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    ajax_loading_close();
                    var inst = get_remodal_instance();
                    inst.open();
                    ajax_check_import_process();
                },
                success: function (data) {},
                error: function (xhr, ajaxOptions, thrownError) {
                    cancel_process(xhr.responseText);
                }
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Internal error clearing previous progress.')
        }
    });
}
