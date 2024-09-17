$(function(){
  $.ajaxSetup({ cache: false });
});

$(document).on('confirmation', '.download_libraries', function () {
    var button_confirm_text = remodal_button_confirm_get_text();
    var request = $.ajax({
		url: download_libraries_url,
		dataType: 'json',
		type: "POST",
		beforeSend: function(data) {
            remodal_button_confirm_loading_on();
		},
		success: function(data) {
		    remodal_button_confirm_loading_off(button_confirm_text);
		    if(data.error)
		        remodal_notification(data.message);
		    else {
                remodal_notification(data.message, 'success');
                setTimeout( function(){
					location.reload();
				}  , 2000 );
            }
		},
		error: function (xhr, ajaxOptions, thrownError) {
		    remodal_button_confirm_loading_off(button_confirm_text);
		    remodal_notification(libraries_download_error);
		}
	});
});

function send_conversion(negative) {
    var security_hash = $('#google_all input[name=security_hash]').val();
    var negative = typeof negative != 'undefined' ? 1 : 0;
    var order_id = negative ? $('input.negative_conversion_input:visible').val() : $('input.positive_conversion_input:visible').val();
    var gtm_id = $('input[name*="google_all_container_id_"]:visible').val();

	$.ajax({
		url: link_ajax_generate_conversion,
		data: {order_id:order_id, gtm_id:gtm_id, negative:negative, security_hash:security_hash},
		type: "POST",
		dataType: 'json',
		beforeSend:function()
		{
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();

			if(data.error)
				open_manual_notification(data.message, 'warning', 'exclamation');
			else {
				open_manual_notification(data.message, 'success', 'check');
                $('input.negative_conversion_input:visible').val('');
                $('input.positive_conversion_input:visible').val('');
                $('head').append(data.script.begin_head);
                $('body').append(data.script.begin_body);
            }
		},
		error: function(data) {
			ajax_loading_close();
			alert('Internal error.');
		},
	});
}

function open_tab_gtm_workspace() {
	$('li a.tab_gtm-workspace-generator').click();
	$('li a.tab_Генератор-рабочего-пространства-gtm').click();
}