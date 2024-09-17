$(function(){
	if($('select#InvoiceCustomerCountryId').val() != '')
		$('select#InvoiceCustomerCountryId').trigger('change');

	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	});
});
$(document).on('change', 'select#InvoiceCustomerCountryId', function(){
	var select_customer_zone_id = $('select#InvoiceCustomerZoneId');
	var country_id = $(this).val();
	var vat_container = $('#InvoiceCustomerVat').closest('div.col-md-4');
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
				$('input#InvoiceTax').val(data.Country.tax);
				$('#InvoiceIsEu').val(1);
			}
			else
			{
				$('input#InvoiceTax').val(0);
				$('#InvoiceIsEu').val(0);
			}

			calculate_totals();

			var options_html = '<option value=""> - Select zone - </option>';

			$.each(data.Zone, function(i, item) {
				options_html += '<option value="'+i+'">'+item+'</option>';
			});

			select_customer_zone_id.html(options_html);

			if(typeof zone_id == 'undefined' && typeof current_zone_id !== 'undefined') {
				zone_id = current_zone_id;
			}

			if(typeof zone_id != 'undefined')
				select_customer_zone_id.val(zone_id);
		},
		error: function(data) {
			ajax_loading_close();
			alert('Error getting zones.');
		},
	});
});

$(document).on('change', 'input[name="data[Invoice][payment_method]"]', function(){
	calculate_totals();
});

$(document).on('keyup', 'input#InvoiceCustomerVat', function(){
	vat_modified($(this).val());
});

$(document).on('change', 'input#InvoiceCustomerVat', function(){
	vat_modified($(this).val());
});

function vat_modified(vatNumber)
{
	var country_id = $('select#InvoiceCustomerCountryId').val();
	var is_eu = $('#InvoiceIsEu').val() == 1;
	var is_spain = country_id == 195;

	if(country_id == '')
		return false;

	calculate_totals();
}

$(document).ready(function(){
	calculate_totals();
});

$(document).on('change', 'select#InvoiceCurrency', function(){
	calculate_totals();
});

function calculate_totals()
{
	var discount = $('input#InvoiceDiscount').val();

	var temp_base_price = base_price;

	var temp_price = temp_base_price;
	var temp_total = temp_base_price*quantity;
	var tax = parseFloat($('input#InvoiceTax').val());

	var temp_total_with_tax = tax > 0 ? (temp_total * ((tax/100)+1)) : temp_total;

	var country_id = $('select#InvoiceCustomerCountryId').val();
	var is_eu = $('#InvoiceIsEu').val() == 1;
	var is_spain = country_id == 195;
	var temp_tax = '0%';
	var vat_code = $('input#InvoiceCustomerVat').val();

	var temp_price_euros = temp_base_price*eur_currency_value;
	var temp_total_euros = temp_base_price*quantity*eur_currency_value;
	var temp_price = temp_base_price;
	var temp_total = temp_base_price*quantity;

	if(is_spain || (is_eu && vat_code == ''))
	{
		temp_tax = tax+'%';
		temp_total_euros = temp_total_with_tax*eur_currency_value;
		temp_total = temp_total_with_tax;
	}

	if(discount > 0)
	{
		discount = ( (100-discount) / 100);

		temp_total_euros = temp_total_euros * discount;
		temp_total = temp_total * discount;
	}

	//Devman Extensions - info@devmanextensions.com - 2017-10-14 11:55:36 - Paypal fee
	var payment_method = $('input[name="data[Invoice][payment_method]"]:checked').val();

	if(payment_method == 'Paypal')
	{
		temp_total_euros *= paypal_fee;
		temp_total *= paypal_fee;
	}
	if(payment_method == 'Stripe')
	{
		temp_total_euros *= stripe_fee;
		temp_total *= stripe_fee;
	}

	$('input#InvoiceTotal').val(temp_total);
	//END

	temp_price_euros = parseFloat(temp_price_euros).toFixed(2)+"€";
	temp_total_euros = parseFloat(temp_total_euros).toFixed(2)+"€";
	temp_price = "$"+parseFloat(temp_price).toFixed(2)+ ' ('+temp_price_euros+')';
	temp_total = "$"+parseFloat(temp_total).toFixed(2)+ ' ('+temp_total_euros+')';

	$('td.tax').html(temp_tax);
	$('td.price').html(temp_price);
	$('td.total').html(temp_total);
}
