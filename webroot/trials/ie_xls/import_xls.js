$(document).ready(function() {
  $.ajaxSetup({ cache: false });
});

function convert_to_innodb()
{
	var request = $.ajax({
		url: convert_to_innodb_url,
		dataType: 'json',
		type: "POST",
		beforeSend: function(data) {
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			alert(data.message);
		},      
		error: function (xhr, ajaxOptions, thrownError) {     
			ajax_loading_close();
			alert(data.message);
		}
	});
}
function readURL(input) {
	$('a.button.button_upload_xls span').html('');
	$('a.button.button_upload_xls span').html(' <b>('+input.val()+')</b>');
}

function export_start(format) {
	var datas = {};
	datas['format'] = format;
	$('div.products_export_process input, div.products_export_process select, tr.products_export_process input, tr.products_export_process select').each(function(){
		var is_input = $(this).is('input');
		var name = $(this).attr('name');
		var val = is_input ? ($(this).attr('type') == 'checkbox' ? ($(this).is(':checked') ? 'true' : '') : $(this).val()) : $(this).val();
		datas[name] = val;
	});

    $.ajax({
		url: url_ajax_clean_progress,
		type: "POST",
		dataType: 'json',
		processData: false,
       	contentType: false,
		beforeSend: function() {
			ajax_loading_open_with_text(progress_export_start_process, progress_export_start_process);
		},
		success: function(data) {
			if(data.error != '')
			{
				abort_import_export_modal_progress(data.message);
			}
			else
			{
				$.ajax({
					url: url_export,
					data: datas,
					type: "POST",
					dataType: 'json',
					beforeSend: function(data) {
						ajax_check_import_process();
					},
					success: function(data) {
					},
					error: function (xhr, ajaxOptions, thrownError) {
						stop_import_process_internal_error(xhr.responseText);
					}
				});
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			stop_import_process_internal_error(xhr.responseText);
		}
	});
}

function save_configuration() {
	var request = $.ajax({
		url: save_configuration_url,
		data: $('form').serialize(),
		type: "POST",
		dataType: 'json',
		beforeSend: function(data) {
			ajax_loading_open();
		},
		success: function(data) {
			if(!data.error)
				location.reload();
			else
			{
				open_manual_notification(data.message, 'danger', 'exclamation');
				ajax_loading_close();
			}
		},      
		error: function (xhr, ajaxOptions, thrownError) {     
			ajax_loading_close();
			alert(data.message);
		}
	});
}

$(document).on('ready', function(){
	check_category_tree();

	$('input[name="import_xls_categories_tree"]').on('change', function(){check_category_tree()});
	$('input[name="import_xls_select_unselect_all"]').on('change', function(){
		$('input[type="checkbox"].checkbox_column').prop('checked', $(this).is(':checked'));
	});
	$('input[name="import_xls_select_unselect_all_export_datas"]').on('change', function(){
		$('input[type="checkbox"].backup_export_element').prop('checked', $(this).is(':checked'));
	});
});

function check_category_tree()
{
	var category_tree = $('input[name="import_xls_categories_tree"]').is(':checked');
	var input_last_child = $('input[name="import_xls_categories_last_tree"]');
	var label = input_last_child.parent('label');

	if(category_tree)
	{
		label.removeClass('disabled');
		input_last_child.removeAttr('disabled');
	}
	else
	{
		label.addClass('disabled');
		input_last_child.attr('disabled', 'disabled');
		input_last_child.prop('checked', false);
	}
}

function ajax_loading_open_with_text(title, content, add_title_loading_icon) {
  	jQuery('body').prepend('<div class="ajax_loading"><div class="panel_loading"><span class="title"><i class="fa fa-refresh fa-spin"></i>'+title+'</span><div style="clear:both;"></div><div class="content"></div><div class="container_button_close"><a style="display:none;" class="button close_button" href="javascript:{}" onclick="ajax_loading_close()">Close</a></div></div></div>');
}
function ajax_loading_change_title(title, add_title_loading_icon) {
	var add_title_loading_icon = typeof add_title_loading_icon == 'undefined' ? true : add_title_loading_icon;
	$('div.ajax_loading div.panel_loading').find('span.title').html((add_title_loading_icon ? '<i class="fa fa-refresh fa-spin"></i>' : '')+title);
}
function ajax_loading_put_progress(content) {
	var panel_content = $('div.ajax_loading div.panel_loading').find('div.content')
	panel_content.html('');
	$.each(content, function( index, value ) {
        panel_content.append(value + '<br>');
        panel_content.scrollTop = panel_content.scrollHeight;
        panel_content.animate({
            scrollTop: panel_content.get(0).scrollHeight
        }, 0);
	});
}

function ajax_start_import(type)  {
    var formData = new FormData();
    formData.append('type', type);

    if(type == 'products') {
        formData.append('file', $('input[name="upload"]')[0].files[0]);
        formData.append('spreadsheet', $('input[name="import_xls_google_spread_sheets_products_filename"]').val());
	} else if(type == 'backups') {
    	formData.append('file', $('input[name="upload_backups"]')[0].files[0]);
	} else {
    	formData.append('file', $('input[name="upload_extra"]')[0].files[0]);
	}

	$.ajax({
		url: url_ajax_clean_progress,
		type: "POST",
		dataType: 'json',
		processData: false,
       	contentType: false,
		beforeSend: function(data) {
			ajax_loading_open_with_text(ajax_checking_file_starting_import_process, ajax_checking_file_starting_import_process);
		},
		success: function(data) {
			if(data.error != '')
			{
				abort_import_export_modal_progress(data.message);
			}
			else
			{
				$.ajax({
					url: url_ajax_start_import,
					data: formData,
					type: "POST",
					dataType: 'json',
					processData: false,
					contentType: false,
					beforeSend: function(data) {
						ajax_check_import_process();
					},
					success: function(data) {

					},
					error: function (xhr, ajaxOptions, thrownError) {
						stop_import_process_internal_error(xhr.responseText);
					}
				});
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			stop_import_process_internal_error(xhr.responseText);
		}
	});
}

function ajax_check_import_process() {
	$.getJSON(progress_route, function( data ) {
		if(data != null && data.length != 0) {
			if(data.message.length != 0)
				ajax_loading_put_progress(data.message);

            if (data.continue) {
                setTimeout(function () {
					ajax_check_import_process();
				}, 250);
            } else if(typeof data.continue != 'undefined' && !data.continue) {
				$('div.ajax_loading div.panel_loading').find('a.button.close_button').show();
            	if(data.title != '')
					ajax_loading_change_title(data.title, false);
            	if(data.redirect)
            		window.location = data.redirect;
			} else {
            	setTimeout(function () {
					ajax_check_import_process();
				}, 250);
			}
        } else {
			setTimeout(function () {
				ajax_check_import_process();
			}, 250);
		}
	}).error(function() {
		setTimeout(function () {
			ajax_check_import_process();
		}, 250);
	});
}

function stop_import_process_internal_error(error) {
	$.ajax({
		url: url_ajax_stop_import,
		data : {error: error},
		type: "POST",
		dataType: 'json',
		success: function(data) {
			ajax_check_import_process();
		},
	});
}

function abort_import_export_modal_progress(message) {
	ajax_loading_change_title('ERROR', false);
	$('div.panel_loading').find('div.content').html(message);
	$('div.panel_loading').find('a.close_button').show();
}