function addDomain(license_id, button_pressed) {
    var input = button_pressed.closest('tr').find('input[name="new_domain"]');
    var domains = button_pressed.closest('tr').find('span.domains');
    jQuery.ajax({
        type: 'post',
        cache: false,
        data: {license_id: license_id, new_domain: input.val()},
        url: path+'sales/sales/ajax_add_domain',
        dataType: 'json',
        beforeSend: function () {
            ajax_loading_open();
        },
        success: function (response) {
            ajax_loading_close();
            if (response.error) {
                alert(response.message);
            }
            else {
                input.val('');
                domains.append((domains.html() ? '<br>' : '')+response.message);
            }
        }
    });
}

function renew(license_id) {
    if (confirm('Renew license?')) {
        jQuery.ajax({
            type: 'post',
            cache: false,
            data: {license_id: license_id},
            url: path+'sales/sales/ajax_renew',
            dataType: 'json',
            beforeSend: function () {
                ajax_loading_open();
            },
            success: function (response) {
                ajax_loading_close();
                if (response.error) {
                    alert(response.message);
                }
                else {
                    jQuery('form#search').submit();
                }
            }
        });
    } else {
    }
}

function clean_domain(button_pressed) {
    var row = button_pressed.closest('tr');
    var input_new_domain = row.find('input[name="new_domain"]');
    var button_add_new_domain = row.find('a.add_domain');
    var domains = row.find('span.domains');

    input_new_domain.val('clean');
    button_add_new_domain.click();
    domains.fadeOut();
}