function validateForm(client_id, office, valid_date, date, register, due_date)
{
    valid_date.removeAttr('style');

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

    if (office)
    {
        office.removeAttr('style');

        if (office.has('option').length === 0)
        {
            office.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (!date_test.test(valid_date.val()))
    {
        valid_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (date)
    {
        date.removeAttr('style');

        if (!date_time_test.test(date.val()))
        {
            date.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (register || due_date)
    {
        register.removeAttr('style');
        due_date.removeAttr('style');

        var create_invoice = $('.create-invoice-select').val();

        if (create_invoice === 'T')
        {
            if (register.has('option').length === 0)
            {
                register.css('border', '1px solid #FF0000');

                check_validation = 0;
            }

            if (!date_test.test(due_date.val()))
            {
                due_date.css('border', '1px solid #FF0000');

                check_validation = 0;
            }
        }
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
        url: ajax_url + 'docs/offers/getProducts',
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
                                '" data-op-id="' + value.op_id + '">' +
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
                                op_id: value.op_id
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

                        $('#offer-products').show();
                    }
                    else
                    {
                        $('#offer-products').hide();
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

function checkMerchandise(offer_id, date, client_id, language, payment_type, currency, input_currency, valid_date, tax, note,
    int_note, notes, create_invoice, register, due_date)
{
    $.ajax({
        url: ajax_url + 'products/checkMerchandise',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'products': products_object},
        success: function(data) {

            enableButtons(true);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    if (data.merchandise === 'T')
                    {
                        swal({
                            title: dispatch,
                            text: dispatch_create,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: alert_confirm,
                            cancelButtonText: alert_cancel,
                            closeOnConfirm: false,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm)
                            {
                                updateOffer(offer_id, date, client_id, language, payment_type, currency, input_currency,
                                    valid_date, tax, note, int_note, notes, create_invoice, register, due_date, 'T');
                            }
                            else
                            {
                                updateOffer(offer_id, date, client_id, language, payment_type, currency, input_currency,
                                    valid_date, tax, note, int_note, notes, create_invoice, register, due_date, 'F');
                            }
                        });
                    }
                    else
                    {
                        updateOffer(offer_id, date, client_id, language, payment_type, currency, input_currency, valid_date, tax,
                            note, int_note, notes, create_invoice, register, due_date, 'F');
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
            enableButtons(true);
            toastr.error(error);
        }
    });
}

function insertOffer(office, client_id, language, payment_type, currency, input_currency, valid_date, tax, note, int_note, notes)
{
    $.ajax({
        url: ajax_url + 'docs/offers/insert',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'office': office, 'client': client_id, 'language': language, 'payment_type': payment_type, 'currency': currency,
            'input_currency': input_currency, 'valid_date': valid_date, 'tax': tax, 'note': note, 'int_note': int_note,
            'products': products_object, 'notes': notes},
        success: function(data) {

            enableButtons(false);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/offers/list';
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

function updateOffer(offer_id, date, client_id, language, payment_type, currency, input_currency, valid_date, tax, note, int_note,
    notes, create_invoice, register, due_date, merchandise)
{
    $.ajax({
        url: ajax_url + 'docs/offers/update',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'id': offer_id, 'date': date, 'client': client_id, 'language': language, 'payment_type': payment_type,
            'currency': currency, 'input_currency': input_currency, 'valid_date': valid_date, 'tax': tax, 'note': note,
            'int_note': int_note, 'products': products_object, 'notes': notes, 'create_invoice': create_invoice,
            'register': register, 'due_date': due_date, 'merchandise': merchandise},
        success: function(data) {

            enableButtons(true);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/offers/list';
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

function sendEmail(offer_id, offer)
{
    $.ajax({
        url: ajax_url + 'docs/offers/sendEmail',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'offer_id': offer_id},
        success: function(data) {

            var responseStatus = data.status;

            offer.removeClass('animated infinite rollOut');
            offer.prop('disabled', false);

            switch (responseStatus)
            {
                case 1:
                    toastr.success(data.success);
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
            toastr.error(error);
            offer.removeClass('animated infinite rollOut');
            offer.prop('disabled', false);
        }
    });
}

function disableButtons(offer_id)
{
    if (offer_id)
    {
        $('.update').prop('disabled', true);
    }
    else
    {
        $('.insert').prop('disabled', true);
    }

    $('.cancel').prop('disabled', true);
}

function enableButtons(offer_id)
{
    if (offer_id)
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
var date_time_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/;
var decimal_test = /^[0-9]+(\.[0-9]+)?$/;
var integer_test = /^[0-9]+$/;

var check_validation = 1;

//create products object
var products_object = {};

//create notes object
var notes_object = {};

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
            op_id: null
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
        $('.offer-product-quantity').val(product_quantity);
        $('.offer-product-brutto').val(product_brutto);
        $('.offer-product-rebate').val(product_rebate);
        $('.offer-product-note').val(product_note);
        $('.product-custom-price').val(product_custom_price);
        $('.offer-product-price').val('');
    });

    $('.update-product').on('click', function() {

        //reset validation variable
        check_validation = 1;

        var product_object_id = $('.product-object-id').val();
        var quantity_input = $('.offer-product-quantity');
        var price_input = $('.offer-product-price');
        var brutto = $('.offer-product-brutto').val();
        var rebate_input = $('.offer-product-rebate');
        var note = $('.offer-product-note').val();
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

    $('.create-invoice-select').on('change', function() {

        var create_invoice = $('.create-invoice-select').val();

        if (create_invoice === 'T')
        {
            $('.invoice-data').show();
        }
        else
        {
            $('.invoice-data').hide();
        }
    });

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'docs/offers/list';
    });

    $('.insert').on('click', function() {

        disableButtons(false);

        //reset validation variable
        check_validation = 1;

        var office_input = $('.office');
        var client_id = $('#client-id').val();
        var language = $('.language').val();
        var payment_type = $('.payment-type').val();
        var currency = $('.currency').val();
        var input_currency = $('.input-currency').val();
        var valid_date_input = $('.valid-date');
        var tax = $('.tax').val();
        var note = $('.note').val();
        var int_note = $('.int-note').val();

        var validation_error = validateForm(client_id, office_input, valid_date_input, null, null, null);

        if (!check_validation)
        {
            enableButtons(false);
            toastr.error(validation_error);

            return 0;
        }

        //clear notes object
        notes_object = {};

        var counter = 1;

        $('.note-text').each(function() {

            var note = $(this).val();

            if (note !== '')
            {
                //add note to notes object
                notes_object[counter] = {
                    note: note,
                    note_id: null
                };

                counter++;
            }
        });

        insertOffer(office_input.val(), client_id, language, payment_type, currency, input_currency, valid_date_input.val(),
            tax, note, int_note, notes_object);
    });

    $('.update').on('click', function() {

        disableButtons(true);

        //reset validation variable
        check_validation = 1;

        var offer_id = $('#offer-id').val();
        var client_id = $('#client-id').val();
        var date_input = $('.offer-date');
        var language = $('.language').val();
        var payment_type = $('.payment-type').val();
        var currency = $('.currency').val();
        var input_currency = $('.input-currency').val();
        var valid_date_input = $('.valid-date');
        var tax = $('.tax').val();
        var note = $('.note').val();
        var int_note = $('.int-note').val();
        var create_invoice = $('.create-invoice-select').val();
        var register_input = $('.register');
        var due_date_input = $('.due-date');

        var validation_error = validateForm(client_id, null, valid_date_input, date_input, register_input, due_date_input);

        if (!check_validation)
        {
            enableButtons(true);
            toastr.error(validation_error);

            return 0;
        }

        //clear notes object
        notes_object = {};

        var counter = 1;

        $('.note-text').each(function() {

            var this_note = $(this);

            var note = this_note.val();
            var note_id = null;

            if (this_note.attr('data-note-id'))
            {
                note_id = this_note.attr('data-note-id');
            }

            if (note !== '')
            {
                //add note to notes object
                notes_object[counter] = {
                    note: note,
                    note_id: note_id
                };

                counter++;
            }
        });

        if (create_invoice === 'T')
        {
            checkMerchandise(offer_id, date_input.val(), client_id, language, payment_type, currency, input_currency,
                valid_date_input.val(), tax, note, int_note, notes_object, create_invoice, register_input.val(),
                due_date_input.val());
        }
        else
        {
            updateOffer(offer_id, date_input.val(), client_id, language, payment_type, currency, input_currency,
                valid_date_input.val(), tax, note, int_note, notes_object, create_invoice, register_input.val(),
                due_date_input.val(), 'F');
        }

    });

    $('.add-note').on('click', function() {

        var note_text = $('.note-select option:selected').text();

        var note_html = '<div class="note-form-element"><div class="col-sm-10"><div class="form-group">' +
            '<textarea name="custom_note" class="form-control note-text">' + note_text + '</textarea></div></div>' +
            '<div class="col-sm-2"><div class="form-group"><button type="button" class="btn btn-danger btn-xs remove-note">' +
            delete_trans + '</button></div></div></div>';

        $('#notes-form').append(note_html);
    });

    $('#notes-form').on('click', '.remove-note', function() {

        var this_button = $(this);

        this_button.parents('.note-form-element').remove();
    });

    $('.offers-list').on('click', '.send-email', function() {

        var this_offer = $(this);

        this_offer.addClass('animated infinite rollOut');
        this_offer.prop('disabled', true);
        var offer_id = this_offer.attr('data-id');

        sendEmail(offer_id, this_offer);
    });
});