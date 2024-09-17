function confirm_license(license_id, button_pressed) {
    var input_sub_total = button_pressed.closest('tr').find('input[name="sub_total"]');
    var input_commission = button_pressed.closest('tr').find('input[name="commission"]');
    var input_total = button_pressed.closest('tr').find('input[name="total"]');

    jQuery.ajax({
        type: 'post',
        cache: false,
        data: {id: license_id, sub_total: input_sub_total.val(), commission: input_commission.val(), total: input_total.val()},
        url: path+'sales/sales_waiting/complete',
        dataType: 'json',
        beforeSend: function () {
            ajax_loading_open();
        },
        success: function (response) {
            ajax_loading_close();
            if (response.error) {
                alert(response.message);
            } else if(response.redirect)
                window.location = response.redirect;
        }
    });
}