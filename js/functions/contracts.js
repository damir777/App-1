function validateForm(client_id, office, register, contract_number, due_days, number_of_invoices)
{
    office.removeAttr('style');
    register.removeAttr('style');
    contract_number.removeAttr('style');
    due_days.removeAttr('style');
    number_of_invoices.removeAttr('style');

    if (!integer_test.test(client_id) || client_id < 1)
    {
        check_validation = 0;

        return no_client_error;
    }

    if ($.isEmptyObject(products_object))
    {
        check_validation = 0;

        return no_product_error;
    }

    office.removeAttr('style');

    if (office.has('option').length === 0)
    {
        office.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    register.removeAttr('style');

    if (register.has('option').length === 0)
    {
        register.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (contract_number.val().trim() == '')
    {
        contract_number.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(due_days.val()))
    {
        due_days.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(number_of_invoices.val()))
    {
        number_of_invoices.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    return validation_error;
}

function validateEditProduct(quantity, price, rebate)
{
    quantity.removeAttr('style');
    price.removeAttr('style');
    rebate.removeAttr('style');

    if (!decimal_test.test(quantity.val()))
    {
        quantity.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (price.val().trim() != '')
    {
        if (!decimal_test.test(price.val()))
        {
            price.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (!integer_test.test(rebate.val()))
    {
        rebate.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
}

function getProducts()
{
    var client_id = $('#client-id').val();
    var currency = $('.currency').val();
    var tax = $('.tax').val();

    $.ajax({
        url: ajax_url + 'docs/contracts/getProducts',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'client': client_id, 'currency': currency, 'tax': tax, 'products': products_object},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    if (data.products.length > 0)
                    {
                        //clear products object
                        products_object = {};

                        var counter = 1;
                        var append_string = '';

                        $.each(data.products, function(index, value) {

                            append_string += '<tr><td>' + counter + '</td><td>' + value.code + '</td><td>' + value.name +
                                '<span class="product-description text-muted"><small>' + value.note + '</small></span></td>' +
                                '<td>' + value.unit + '</td><td class="text-center">' + value.list_quantity + '</td>' +
                                '<td class="text-right">' + value.list_price + '</td>' +
                                '<td class="text-center">' + value.tax + '</td><td class="text-center">' + value.rebate + '</td>' +
                                '<td class="text-right">' + value.rebate_sum + '</td>' +
                                '<td class="text-right">' + value.sum + '</td>' +
                                '<td class="text-center"><a href="#" class="edit-product" data-id="' + counter + '"' +
                                ' data-quantity="' + value.quantity + '" data-custom-price="' + value.custom_price +
                                '" data-brutto="' + value.brutto + '" data-rebate="' + value.rebate + '" data-note="' +
                                value.note + '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>' +
                                '<td class="text-center"><a href="#" class="delete-product" data-id="' + counter +
                                '" data-cp-id="' + value.cp_id + '">' +
                                '<i class="fa fa-times danger" aria-hidden="true"></i></a></td></tr>';

                            //add product to products object
                            products_object[counter] = {
                                id: value.product_id,
                                quantity: value.quantity,
                                price: value.price,
                                custom_price: value.custom_price,
                                brutto: value.brutto,
                                rebate: value.rebate,
                                note: value.note,
                                cp_id: value.cp_id
                            };

                            counter++;
                        });

                        $('#products-table').html('').append(append_string);

                        var total_append_string = '<tbody><tr><td>' + sum_trans + ':</td><td>' + data.total + '</td></tr>' +
                            '<tr><td>' + rebate_sum_trans + ':</td><td>' + data.rebate_sum + '</td></tr>';

                        if (data.tax_array.length > 0)
                        {
                            $.each(data.tax_array, function(index, value) {

                                total_append_string += '<tr><td>' + tax_trans + ' (' + value.tax + '):</td>' +
                                    '<td>' + value.sum + '</td></tr>';
                            });
                        }

                        total_append_string += '<tr><td><strong>' + total_trans + ':</strong></td>' +
                            '<td><strong>' + data.grand_total + '</strong></td></tr></tbody>';

                        $('.invoice-total').html('').append(total_append_string);

                        $('#contract-products').show();
                    }
                    else
                    {
                        $('#contract-products').hide();
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

function insertContract(office, register, contract_number, client_id, language, payment_type, currency, input_currency,
    due_days, note, int_note, tax, number_of_invoices, create_day, previous_month_create, create_after_end, email_sending, active)
{
    $.ajax({
        url: ajax_url + 'docs/contracts/insert',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'office': office, 'register': register, 'contract_number': contract_number, 'client': client_id,
            'language': language, 'payment_type': payment_type, 'currency': currency, 'input_currency': input_currency,
            'due_days': due_days, 'note': note, 'int_note': int_note, 'tax': tax, 'number_of_invoices': number_of_invoices,
            'create_day': create_day, 'previous_month_create': previous_month_create, 'create_after_end': create_after_end,
            'email_sending': email_sending, 'active': active, 'products': products_object},
        success: function(data) {

            enableButtons(false);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/contracts/list';
                    break;
                case 2:
                    toastr.warning(data.warning);
                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            enableButtons(false);
            toastr.error(error);
        }
    });
}

function updateContract(contract_id, office, register, contract_number, client_id, language, payment_type, currency, input_currency,
    due_days, note, int_note, tax, number_of_invoices, create_day, previous_month_create, create_after_end, email_sending, active)
{
    $.ajax({
        url: ajax_url + 'docs/contracts/update',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'id': contract_id, 'office': office, 'register': register, 'contract_number': contract_number,
                'client': client_id, 'language': language, 'payment_type': payment_type, 'currency': currency,
                'input_currency': input_currency, 'due_days': due_days, 'note': note, 'int_note': int_note, 'tax': tax,
                'number_of_invoices': number_of_invoices, 'create_day': create_day, 'previous_month_create': previous_month_create,
                'create_after_end': create_after_end, 'email_sending': email_sending, 'active': active,
                'products': products_object},
        success: function(data) {

            enableButtons(true);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/contracts/list';
                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            enableButtons(true);
            toastr.error(error);
        }
    });
}

function disableButtons(contract_id)
{
    if (contract_id)
    {
        $('.update').prop('disabled', true);
    }
    else
    {
        $('.insert').prop('disabled', true);
    }

    $('.cancel').prop('disabled', true);
}

function enableButtons(contract_id)
{
    if (contract_id)
    {
        $('.update').prop('disabled', false);
    }
    else
    {
        $('.insert').prop('disabled', false);
    }

    $('.cancel').prop('disabled', false);
}

//define global validation variables
var date_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/;
var decimal_test = /^[0-9]+(\.[0-9]+)?$/;
var integer_test = /^[0-9]+$/;

var check_validation = 1;

//create products object
var products_object = {};

$(document).ready(function() {

    $('#products').on('click', '.add-product', function() {

        var this_product = $(this);
        var product_id = this_product.attr('data-id');

        var products_length = Object.getOwnPropertyNames(products_object).length;

        //add product to products object
        products_object[products_length + 1] = {
            id: product_id,
            quantity: 1,
            price: 0,
            custom_price: 'F',
            brutto: 'F',
            rebate: 0,
            note: '',
            cp_id: null
        };

        $('.products-search-box').hide();
        $('.product-search-string').val('');

        getProducts();
    });

    $('#products-table').on('click', '.edit-product', function(e) {

        e.preventDefault();

        var product_object_id = $(this).attr('data-id');
        var product_quantity = $(this).attr('data-quantity');
        var product_custom_price = $(this).attr('data-custom-price');
        var product_brutto = $(this).attr('data-brutto');
        var product_rebate = $(this).attr('data-rebate');
        var product_note = $(this).attr('data-note');

        $('#editProduct').modal('show');

        $('.product-object-id').val(product_object_id);
        $('.contract-product-quantity').val(product_quantity);
        $('.contract-product-brutto').val(product_brutto);
        $('.contract-product-rebate').val(product_rebate);
        $('.contract-product-note').val(product_note);
        $('.product-custom-price').val(product_custom_price);
        $('.contract-product-price').val('');
    });

    $('.update-product').on('click', function() {

        //reset validation variable
        check_validation = 1;

        var product_object_id = $('.product-object-id').val();
        var quantity_input = $('.contract-product-quantity');
        var price_input = $('.contract-product-price');
        var brutto = $('.contract-product-brutto').val();
        var rebate_input = $('.contract-product-rebate');
        var note = $('.contract-product-note').val();
        var custom_price = $('.product-custom-price').val();

        validateEditProduct(quantity_input, price_input, rebate_input);

        if (!check_validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        if (products_object.hasOwnProperty(product_object_id))
        {
            products_object[product_object_id].quantity = quantity_input.val();
            products_object[product_object_id].brutto = brutto;
            products_object[product_object_id].rebate = rebate_input.val();
            products_object[product_object_id].note = note;

            if (price_input.val())
            {
                products_object[product_object_id].price = price_input.val();
                products_object[product_object_id].custom_price = 'T';
            }

            $('#editProduct').modal('hide');

            getProducts();
        }
        else
        {
            toastr.error(error);
        }
    });

    $('#products-table').on('click', '.delete-product', function(e) {

        e.preventDefault();

        var this_product = $(this);
        var product_object_id = this_product.attr('data-id');

        if (products_object.hasOwnProperty(product_object_id))
        {
            delete products_object[product_object_id];

            getProducts();
        }
        else
        {
            toastr.error(error);
        }
    });

    $('.currency, .tax').on('change', function() {

        getProducts();
    });

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'docs/contracts/list';
    });

    $('.insert').on('click', function() {

        disableButtons(false);

        //reset validation variable
        check_validation = 1;

        var office_input = $('.office');
        var register_input = $('.register');
        var contract_number_input = $('.contract-number');
        var client_id = $('#client-id').val();
        var language = $('.language').val();
        var payment_type = $('.payment-type').val();
        var currency = $('.currency').val();
        var input_currency = $('.input-currency').val();
        var due_days_input = $('.due-days');
        var note = $('.note').val();
        var int_note = $('.int-note').val();
        var tax = $('.tax').val();
        var number_of_invoices_input = $('.number-of-invoices');
        var create_day = $('.create-day').val();
        var previous_month_create = $('.previous-month-create').val();
        var create_after_end = $('.create-after-end').val();
        var email_sending = $('.email-sending').val();
        var active = $('.is-active').val();

        var validation_error = validateForm(client_id, office_input, register_input, contract_number_input, due_days_input,
            number_of_invoices_input);

        if (!check_validation)
        {
            enableButtons(false);
            toastr.error(validation_error);

            return 0;
        }

        insertContract(office_input.val(), register_input.val(), contract_number_input.val(), client_id, language, payment_type,
            currency, input_currency, due_days_input.val(), note, int_note, tax, number_of_invoices_input.val(), create_day,
            previous_month_create, create_after_end, email_sending, active);
    });

    $('.update').on('click', function() {

        disableButtons(true);

        //reset validation variable
        check_validation = 1;

        var contract_id = $('#contract-id').val();
        var office_input = $('.office');
        var register_input = $('.register');
        var contract_number_input = $('.contract-number');
        var client_id = $('#client-id').val();
        var language = $('.language').val();
        var payment_type = $('.payment-type').val();
        var currency = $('.currency').val();
        var input_currency = $('.input-currency').val();
        var due_days_input = $('.due-days');
        var note = $('.note').val();
        var int_note = $('.int-note').val();
        var tax = $('.tax').val();
        var number_of_invoices_input = $('.number-of-invoices');
        var create_day = $('.create-day').val();
        var previous_month_create = $('.previous-month-create').val();
        var create_after_end = $('.create-after-end').val();
        var email_sending = $('.email-sending').val();
        var active = $('.is-active').val();

        var validation_error = validateForm(client_id, office_input, register_input, contract_number_input, due_days_input,
            number_of_invoices_input);

        if (!check_validation)
        {
            enableButtons(true);
            toastr.error(validation_error);

            return 0;
        }

        updateContract(contract_id, office_input.val(), register_input.val(), contract_number_input.val(), client_id, language,
            payment_type, currency, input_currency, due_days_input.val(), note, int_note, tax, number_of_invoices_input.val(),
            create_day, previous_month_create, create_after_end, email_sending, active);
    });
});