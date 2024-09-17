function generate_workspace()
{
	var id_shop = $('select[name="google_all_stores"]').val();

	var tab_name = $('div#tab-gtm-workspace-generator').length > 0 ? 'tab-gtm-workspace-generator' : 'tab-Генератор-рабочего-пространства-gtm';

	$('div#'+tab_name).find('input[name="domain_workspace"]').remove();
	$('div#'+tab_name).append('<input type="hidden" name="domain_workspace" value="'+document.location.hostname+'">');

	if($('div#'+tab_name+' .store_input.store_'+id_shop).length > 0)
		var data = $('div#'+tab_name+' .store_input.store_'+id_shop+' input[type="text"], input[name="google_all_google_version"], input[name="domain_workspace"], div#'+tab_name+' .store_input.store_'+id_shop+' input[type="checkbox"]:checked, div#'+tab_name+' .store_input.store_'+id_shop+' select');
	else
		var data = $('div#'+tab_name+' tr.store_input.store_'+id_shop+' input[type="text"], input[name="google_all_google_version"], input[name="domain_workspace"], div#'+tab_name+' tr.store_input.store_'+id_shop+' input[type="checkbox"]:checked, div#'+tab_name+' tr.store_input.store_'+id_shop+' select');
	$.ajax({
		url: link_get_workspace,
		data: data,
		type: "POST",
		dataType: 'json',
		beforeSend:function()
		{
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			if(!data.error)
				open_manual_notification(data.message, 'success', 'check');
			else
				open_manual_notification(data.message, 'warning', 'exclamation');
		},
		error: function(data) {
			ajax_loading_close();
			alert('Error getting Workspace.');
		},
	});
}
