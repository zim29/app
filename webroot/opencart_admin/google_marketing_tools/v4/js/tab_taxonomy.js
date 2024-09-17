/*$(function(){
    $(document).on('keyup', 'input[name^="google_all_feed_taxonomy_cat_"]', function (el) {
        autocomplete_google_category($(this));
    });
});

function autocomplete_google_category(input)
{
    var input_final_result = input.next('input[type="hidden"]');
    var url = taxonomy_url_autocomplete_category+'&country_id=' +  $('select[name="google_all_language"]').val()+ '&text=' +  input.val();
    var id_name = 'id';
    autocomplete_input(input, input_final_result, id_name, url, token);
}*/

$(function() {
    $('input[name^="google_all_feed_taxonomy_cat_"]').autocomplete({
        delay: 2000,
		'source': function (request, response) {
			$.ajax({
				url: taxonomy_url_autocomplete_category+'&country_id=' +  $('select[name="google_all_language"]').val()+ '&text=' +  $(this).val()+'&'+token_name+'='+token+'&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['id']
						}
					}));
				}
			});
		},
		'select': function (item) {
			$(this).val(item['label']);
			$(this).next('input[type="hidden"]').val(item['value']);
			$(this).removeAttr('autocomplete');
		}
	});
});
