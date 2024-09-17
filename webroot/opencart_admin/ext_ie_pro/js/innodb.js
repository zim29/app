$(document).on('confirmation', '.innodb', function () {
    var button_confirm_text = remodal_button_confirm_get_text();
    var request = $.ajax({
		url: convert_to_innodb_url,
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
				}  , 4000 );
            }
		},
		error: function (xhr, ajaxOptions, thrownError) {
		    remodal_button_confirm_loading_off(button_confirm_text);
		    remodal_notification(thrownError);
		}
	});
});