var previus_tax = $('input#InvoiceTax').val();
$(function(){
   if($('select#InvoiceCustomerCountryId').val() != '')
   		$('select#InvoiceCustomerCountryId').trigger('change');
});
$(document).on('change', 'select#InvoiceCustomerCountryId', function(){
	var select_customer_zone_id = $('select#InvoiceCustomerZoneId');
	var current_select_customer_zone_id = $('select#InvoiceCustomerZoneId').val();
	var country_id = $(this).val();
	var vat_container = $('#InvoiceCustomerVat').closest('div.col-md-12');

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

			if(data.Country.is_eu)
			{
				$('#InvoiceIsEu').val(1);
				var vat_filled = $('#InvoiceCustomerVat').val() != '';
				var is_spain = data.Country.id == 195;
				$('input#InvoiceTax').val(data.Country.tax);
				previus_tax = data.Country.tax;

				if(is_spain || !vat_filled)
					apply_taxes(data.Country.tax);
				else
					remove_taxes();
			}
			else
			{
				remove_taxes();
			}

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

$(document).on('change', 'select#InvoicePaymentMethod', function(){
	calculate_total();
});

$(document).on('keyup', 'input#InvoiceCustomerVat', function(){
	var country_id = $('select#InvoiceCustomerCountryId').val();

	if($(this).val() != '' && country_id != 195)
		remove_taxes();
	else
		apply_taxes();
});

$(document).on('keyup', 'input#InvoicePrice, input#InvoiceDiscount, input#InvoiceQuantity', function(){
	calculate_total();
});

$(document).ready(function(){
	var is_eu = $('#InvoiceIsEu').val();
});

function apply_taxes()
{
	$('input#InvoiceTax').val(previus_tax);
	calculate_total();
}
function remove_taxes()
{
	$('input#InvoiceTax').val(0);
	calculate_total();
}

function calculate_total()
{
	var price = parseFloat($('#InvoicePrice').val());
	var tax = parseInt($('#InvoiceTax').val());
	var discount = parseInt($('#InvoiceDiscount').val());
	var quantity = parseFloat($('#InvoiceQuantity').val());

	if(tax > 0)
		price = price * ( 1 + (tax / 100));

	if(discount > 0)
		price = price * ( (100-discount) / 100);

	total = price*quantity;

	//Devman Extensions - info@devmanextensions.com - 2017-10-14 11:55:36 - Paypal fee
		var payment_method = $('select#InvoicePaymentMethod').val();
		if(payment_method == 'Paypal')
			total *= paypal_fee;
		if(payment_method == 'Stripe')
			total *= stripe_fee;
	//END

	$('#InvoiceTotal').val(total);
}
