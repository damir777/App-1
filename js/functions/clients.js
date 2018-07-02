function validateClientForm()
{
    var email_test = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    var retail_client = $('.retail-client').val();
    var client_type = $('.client-type').val();
    var int_client = $('.int-client').val();
    var name_input = $('.client-name');
    var oib_input = $('.oib');
    var tax_number_input = $('.tax-number');
    var address_input = $('.address');
    var city_input = $('.city');
    var country = $('.country').val();
    var zip_code_input = $('.zip-code');
    var email_input = $('.email');
    var rebate_input = $('.rebate');

    name_input.removeAttr('style');
    oib_input.removeAttr('style');
    tax_number_input.removeAttr('style');
    address_input.removeAttr('style');
    city_input.removeAttr('style');
    zip_code_input.removeAttr('style');
    email_input.removeAttr('style');
    rebate_input.removeAttr('style');

    var check_validation = 1;

    if (name_input.val().trim() == '')
    {
        $(name_input).css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (client_type == 2)
    {
        if (int_client === 'F')
        {
            if (oib_input.val().trim() == '')
            {
                oib_input.css('border', '1px solid #FF0000');

                check_validation = 0;
            }
        }
        else
        {
            if (tax_number_input.val().trim() == '')
            {
                tax_number_input.css('border', '1px solid #FF0000');

                check_validation = 0;
            }
        }
    }

    if (retail_client === 'F')
    {
        if (address_input.val().trim() == '')
        {
            address_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }

        if (city_input.val().trim() == '')
        {
            city_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (country !== 'HR')
    {
        if (zip_code_input.val().trim() == '')
        {
            zip_code_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (email_input.val().trim() != '')
    {
        if (!email_test.test(email_input.val()))
        {
            email_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (rebate_input.val().trim() != '')
    {
        if (!decimal_test.test(rebate_input.val()))
        {
            rebate_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    return check_validation;
}

function insertClient()
{
    $.ajax({
        url: ajax_url + 'clients/insert',
        type: 'post',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: new FormData($('.client-form')[0]),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    var document_insert = $('.document-insert').val();

                    if (document_insert === 'F')
                    {
                        location.href = ajax_url + 'clients/list';
                    }
                    else
                    {
                        $('#addClient').modal('hide');
                        toastr.success(client_insert);
                    }

                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

function updateClient()
{
    $.ajax({
        url: ajax_url + 'clients/update',
        type: 'post',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: new FormData($('.client-form')[0]),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'clients/list';
                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

function searchClients(search_string)
{
    $.ajax({
        url: ajax_url + 'clients/search',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'search_string': search_string},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    var clients_div = $('#clients');
                    var append_string = '<div class="results-wrapper">';

                    clients_div.html('');
                    $('.close-client-search').show();

                    if (data.clients.length > 0)
                    {
                        $.each(data.clients, function(index, value)
                        {
                            append_string += '<div class="result add-client" data-id="' + value.id + '" data-name="' + value.name +
                                '" data-address="' + value.address + '" data-city="' + value.city + '" data-oib="' + value.oib +
                                '"><p>' + value.name + '</p></div>';
                        });
                    }
                    else
                    {
                        append_string += '<div class="result alert-danger no-results"><p>' + no_results_trans+ '</p></div>';
                    }

                    append_string += '</div';

                    clients_div.append(append_string);

                    $('.clients-search-box').show();

                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

function insertClientPrice(client_id, product_id, price)
{
    $.ajax({
        url: ajax_url + 'clients/insert/clientPrice',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'client': client_id, 'product': product_id, 'price': price},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    $('#clientPrice').modal('hide');

                    var prices_div = $('.client-prices');
                    var append_string = '<tbody>';

                    prices_div.html('');

                    $.each(data.prices, function(index, value)
                    {
                        append_string += '<tr><td class="delete-icon"><a href="#" class="delete-button delete-client-price"' +
                            ' data-id="' + value.id + '"><i class="fa fa-times danger" aria-hidden="true"></i></a></td>' +
                            '<td>' + value.name + ' - ' + value.price + '</td></tr>';
                    });

                    append_string += '</tbody>';

                    prices_div.append(append_string);

                    toastr.success(data.success);

                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

function deleteClientPrice(client_id, product_id)
{
    $.ajax({
        url: ajax_url + 'clients/delete/clientPrice',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'client': client_id, 'product': product_id},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    var prices_div = $('.client-prices');
                    var append_string = '<tbody>';

                    prices_div.html('');

                    $.each(data.prices, function(index, value)
                    {
                        append_string += '<tr><td class="delete-icon"><a href="#" class="delete-button delete-client-price"' +
                            ' data-id="' + value.id + '"><i class="fa fa-times danger" aria-hidden="true"></i></a></td>' +
                            '<td>' + value.name + ' - ' + value.price + '</td></tr>';
                    });

                    append_string += '</tbody>';

                    prices_div.append(append_string);

                    toastr.success(data.success);

                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

var decimal_test = /^[0-9]+(\.[0-9]+)?$/;
var client_retail = 'F';

$(document).ready(function() {

    $('.int-client').on('change', function() {

        var int_client = $('.int-client').val();

        if (int_client === 'F')
        {
            $('#tax-number-div').hide();
            $('#oib-div').show();
        }
        else
        {
            $('#oib-div').hide();
            $('#tax-number-div').show();
        }
    });

    $('.country').on('change', function() {

        var country = $('.country').val();

        if (country === 'HR')
        {
            $('#zip-code-text-div').hide();
            $('#zip-code-select-div').show();
        }
        else
        {
            $('#zip-code-select-div').hide();
            $('#zip-code-text-div').show();
        }
    });

    $('.insert-client').on('click', function() {

        var validation = validateClientForm();

        if (!validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        insertClient();
    });

    $('.update-client').on('click', function() {

        var validation = validateClientForm();

        if (!validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        updateClient();
    });

    $('.client-search-string').keyup(function() {

        var search_string = $('.client-search-string').val().trim();

        if (search_string.length < 2)
        {
            $('.clients-search-box').hide();

            return 0;
        }

        searchClients(search_string);
    });

    $('.close-client-search').on('click', function() {

        $('.clients-search-box').hide();
        $('.client-search-string').val('');
    });

    $('#clients').on('click', '.add-client', function() {

        var this_client = $(this);
        var client_id = this_client.attr('data-id');
        var client_name = this_client.attr('data-name');
        var client_address = this_client.attr('data-address');
        var client_city = this_client.attr('data-city');
        var client_oib = this_client.attr('data-oib');

        var client_info_string = '';

        if (client_address !== 'null' && client_address !== '')
        {
            client_info_string += '<p>' + client_address;

            if (client_city)
            {
                client_info_string += ', ' + client_city;
            }

            client_info_string += '</p>';
        }

        if (client_oib && client_oib !== 'null')
        {
            client_info_string += '<p>OIB: ' + client_oib + '</p>';
        }

        $('#selected-client-name').html(client_name);

        if (client_info_string !== '')
        {
            $('#selected-client-info').html(client_info_string).show();
        }
        else
        {
            $('#selected-client-info').html('').hide();
        }

        $('#selected-client').show();
        $('.clients-search-box').hide();
        $('.client-search-string').val('');

        $('#client-id').val(client_id);

        $('.product-search-string').prop('disabled', false);
    });

    $('.insert-client-price').on('click', function() {

        var client_id = $('.client-id').val();
        var product_id = $('.product-id').val();
        var price_input = $('.client-price');

        price_input.removeAttr('style');

        if (!decimal_test.test(price_input.val()))
        {
            price_input.css('border', '1px solid #FF0000');

            toastr.error(validation_error);

            return 0;
        }

        insertClientPrice(client_id, product_id, price_input.val());
    });

    $('.client-prices').on('click', '.delete-client-price', function() {

        var this_product = $(this);
        var client_id = $('.client-id').val();
        var product_id = this_product.attr('data-id');

        deleteClientPrice(client_id, product_id);
    });
});