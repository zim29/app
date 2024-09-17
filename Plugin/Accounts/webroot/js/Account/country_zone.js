$(function(){
	if($('select#AccountCountryId').val() != '')
		$('select#AccountCountryId').trigger('change');
});
$(document).on('change', 'select#AccountCountryId', function(){
	var select_customer_zone_id = $('select#AccountZoneId');
	var current_select_customer_zone_id = $('select#AccountZoneId').val();
	var country_id = $(this).val();

	select_customer_zone_id.val('');

	$.ajax({
		url: path+'countries/ajax_get_zones',
		data: {country_id:country_id},
		type: "POST",
		dataType: 'json',
		beforeSend:function()
		{
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();

			var options_html = '<option value=""> - Select zone - </option>';

			$.each(data.Zone, function(i, item) {
				options_html += '<option value="'+i+'">'+item+'</option>';
			});

			select_customer_zone_id.html(options_html);

			if(!current_select_customer_zone_id && typeof current_zone_id !== 'undefined') {
				current_select_customer_zone_id = current_zone_id;
			}
			if(current_select_customer_zone_id != '')
				select_customer_zone_id.val(current_select_customer_zone_id);
		},
		error: function(data) {
			ajax_loading_close();
			alert('Error getting zones.');
		},
	});
});
