function generate_workspace() {
	var id_shop = $('select[name="google_all_stores"]').val();

	if ($('div#tab-gtm-workspace-generator .store_input.store_' + id_shop).length > 0) {
		var data = $('div#tab-general .store_input.store_' + id_shop + ' input[name="google_all_google_version"], div#tab-gtm-workspace-generator .store_input.store_' + id_shop + ' input[type="text"], div#tab-gtm-workspace-generator .store_input.store_' + id_shop + ' input[type="checkbox"]:checked, div#tab-gtm-workspace-generator .store_input.store_' + id_shop + ' select');
	} else {
		var data = $('div#tab-general .store_input.store_' + id_shop + ' input[name="google_all_google_version"], div#tab-gtm-workspace-generator tr.store_input.store_'+id_shop+' input[type="text"], div#tab-gtm-workspace-generator tr.store_input.store_'+id_shop+' input[type="checkbox"]:checked, div#tab-gtm-workspace-generator tr.store_input.store_'+id_shop+' select');
	}

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

function open_tab_gtm_workspace() {
	if( typeof bootstrap === 'undefined' ) {
		console.log('bootstrap js not defined');
		return;
	}

	var gtmWorkspaceTabTrigger = document.querySelector('ul.nav-tabs li a.tab_gtm-workspace-generator');

	bootstrap.Tab.getOrCreateInstance(gtmWorkspaceTabTrigger).show();
}
