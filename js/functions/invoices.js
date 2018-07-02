function validateForm(client_id, office, register, due_date, delivery_date, model, reference_number, date, partial_paid_sum)
{
    due_date.removeAttr('style');
    delivery_date.removeAttr('style');
    model.removeAttr('style');
    reference_number.removeAttr('style');

    if (!integer_test.test(client_id))
    {
        check_validation = 0;

        return no_client_error;
    }

    if (retail === 'F')
    {
        if (client_id < 1)
        {
            check_validation = 0;

            return no_client_error;
        }
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

    if (register)
    {
        register.removeAttr('style');

        if (register.has('option').length === 0)
        {
            register.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (!date_test.test(due_date.val()))
    {
        due_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (delivery_date.val().trim() != '')
    {
        if (!date_test.test(delivery_date.val()))
        {
            delivery_date.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (!model_test.test(model.val()))
    {
        model.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!reference_number_test.test(reference_number.val()))
    {
        reference_number.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
    else
    {
        if (reference_number.val().length > 22)
        {
            reference_number.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
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

    if (partial_paid_sum)
    {
        partial_paid_sum.removeAttr('style');

        var invoice_status = $('.status').val();

        if (invoice_status == 3)
        {
            if (!decimal_test.test(partial_paid_sum.val()))
            {
                partial_paid_sum.css('border', '1px solid #FF0000');

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
        url: ajax_url + 'docs/invoices/getProducts',
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
                                '" data-ip-id="' + value.ip_id + '">' +
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
                                ip_id: value.ip_id
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

                        $('#invoice-products').show();
                    }
                    else
                    {
                        $('#invoice-products').hide();
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

function checkMerchandise()
{
    var dfd = jQuery.Deferred();

    $.ajax({
        url: ajax_url + 'products/checkMerchandise',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'products': products_object},
        success: function(data) {

            enableButtons(false);

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
                                dfd.resolve('T');
                            }
                            else
                            {
                                dfd.resolve('F');
                            }
                        });
                    }
                    else
                    {
                        dfd.resolve('F');
                    }

                    break;
                case 0:
                    toastr.error(data.error);
                    dfd.resolve(null);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            enableButtons(false);
            toastr.error(error);
            dfd.resolve(null);
        }
    });

    return dfd.promise();
}

function validateAndCheckMerchandise(print, email)
{
    //reset validation variable
    check_validation = 1;

    var office_input = $('.office');
    var register_input = $('.register');
    var client_id = $('#client-id').val();
    var language = $('.language').val();
    var payment_type = $('.payment-type').val();
    var currency = $('.currency').val();
    var input_currency = $('.input-currency').val();
    var due_date_input = $('.due-date');
    var delivery_date_input = $('.delivery-date');
    var note = $('.note').val();
    var int_note = $('.int-note').val();
    var tax = $('.tax').val();
    var advance = $('.advance').val();
    var show_model = $('.show-model').val();
    var model_input = $('.model');
    var reference_number_input = $('.reference-number');

    var validation_error = validateForm(client_id, office_input, register_input, due_date_input, delivery_date_input,
        model_input, reference_number_input, null, null);

    if (!check_validation)
    {
        enableButtons(false);
        toastr.error(validation_error);

        return 0;
    }

    var merchandise = 'F';

    if (retail === 'F')
    {
        checkMerchandise().then(function(create_dispatch) {

            if (create_dispatch)
            {
                merchandise = create_dispatch;

                insertInvoice(office_input.val(), register_input.val(), client_id, language, payment_type, currency, input_currency,
                    due_date_input.val(), delivery_date_input.val(), note, int_note, tax, advance, show_model, model_input.val(),
                    reference_number_input.val(), merchandise, print, email);
            }
        });
    }
    else
    {
        insertInvoice(office_input.val(), register_input.val(), client_id, language, payment_type, currency, input_currency,
            due_date_input.val(), delivery_date_input.val(), note, int_note, tax, advance, show_model, model_input.val(),
            reference_number_input.val(), merchandise, print, email);
    }
}

function insertInvoice(office, register, client, language, payment_type, currency, input_currency, due_date, delivery_date,
    note, int_note, tax, advance, show_model, model, reference_number, merchandise, print, email)
{
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

    $.ajax({
        url: ajax_url + 'docs/invoices/insert',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'retail': retail, 'office': office, 'register': register, 'client': client, 'language': language,
            'payment_type': payment_type, 'currency': currency, 'input_currency': input_currency, 'due_date': due_date,
            'delivery_date': delivery_date, 'note': note, 'int_note': int_note, 'tax': tax, 'advance': advance,
            'show_model': show_model, 'model': model, 'reference_number': reference_number, 'products': products_object,
            'notes': notes_object, 'merchandise': merchandise, 'print': print, 'email': email},
        success: function(data) {

            enableButtons(false);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    if (retail === 'T')
                    {
                        location.href = ajax_url + 'docs/invoices/1/list';
                    }
                    else
                    {
                        location.href = ajax_url + 'docs/invoices/2/list';
                    }

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

function updateInvoice()
{
    //reset validation variable
    check_validation = 1;

    var invoice_id = $('#invoice-id').val();
    var client_id = $('#client-id').val();
    var date_input = $('.invoice-date');
    var language = $('.language').val();
    var payment_type = $('.payment-type').val();
    var currency = $('.currency').val();
    var input_currency = $('.input-currency').val();
    var due_date_input = $('.due-date');
    var delivery_date_input = $('.delivery-date');
    var note = $('.note').val();
    var int_note = $('.int-note').val();
    var tax = $('.tax').val();
    var advance = $('.advance').val();
    var show_model = $('.show-model').val();
    var model_input = $('.model');
    var reference_number_input = $('.reference-number');
    var invoice_status = $('.status').val();
    var partial_paid_sum_input = $('.partial-paid-sum');

    var validation_error = validateForm(client_id, null, null, due_date_input, delivery_date_input, model_input,
        reference_number_input, date_input, partial_paid_sum_input);

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

    $.ajax({
        url: ajax_url + 'docs/invoices/update',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'id': invoice_id, 'date': date_input.val(), 'client': client_id, 'language': language,
                'payment_type': payment_type, 'currency': currency, 'input_currency': input_currency,
                'due_date': due_date_input.val(), 'delivery_date': delivery_date_input.val(), 'note': note, 'int_note': int_note,
                'tax': tax, 'advance': advance, 'show_model': show_model, 'model': model_input.val(),
                'reference_number': reference_number_input.val(), 'status': invoice_status,
                'partial_paid_sum': partial_paid_sum_input.val(), 'products': products_object, 'notes': notes_object},
        success: function(data) {

            enableButtons(true);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    if (retail === 'T')
                    {
                        location.href = ajax_url + 'docs/invoices/1/list';
                    }
                    else
                    {
                        location.href = ajax_url + 'docs/invoices/2/list';
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

function fiscalization(invoice_id, invoice)
{
    $.ajax({
        url: ajax_url + 'docs/invoices/fiscalization',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'invoice_id': invoice_id},
        success: function(data) {

            var responseStatus = data.status;

            invoice.removeClass('animated infinite fadeOut');
            invoice.prop('disabled', false);

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/invoices/1/list';
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
            invoice.removeClass('animated infinite fadeOut');
            invoice.prop('disabled', false);
        }
    });
}

function sendEmail(invoice_id, invoice)
{
    $.ajax({
        url: ajax_url + 'docs/invoices/sendEmail',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'invoice_id': invoice_id},
        success: function(data) {

            var responseStatus = data.status;

            invoice.removeClass('animated infinite rollOut');
            invoice.prop('disabled', false);

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
            invoice.removeClass('animated infinite rollOut');
            invoice.prop('disabled', false);
        }
    });
}

function disableButtons(invoice_id)
{
    if (invoice_id)
    {
        $('.update').prop('disabled', true);
    }
    else
    {
        $('.insert').prop('disabled', true);
    }

    $('.cancel').prop('disabled', true);
}

function enableButtons(invoice_id)
{
    if (invoice_id)
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
var model_test = /^\HR[0-9]{2}$/;
var reference_number_test = /^\d+(\-\d+)?(\-\d+)?$/;

var check_validation = 1;

//create products object
var products_object = {};

//create notes object
var notes_object = {};

$(document).ready(function() {

    if (typeof(retail) !== 'undefined' && retail === 'T')
    {
        $('.retail-client').val('T');
        $('.product-search-string').prop('disabled', false);
    }

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
            ip_id: null
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
        $('.invoice-product-quantity').val(product_quantity);
        $('.invoice-product-brutto').val(product_brutto);
        $('.invoice-product-rebate').val(product_rebate);
        $('.invoice-product-note').val(product_note);
        $('.product-custom-price').val(product_custom_price);
        $('.invoice-product-price').val('');
    });

    $('.update-product').on('click', function() {

        //reset validation variable
        check_validation = 1;

        var product_object_id = $('.product-object-id').val();
        var quantity_input = $('.invoice-product-quantity');
        var price_input = $('.invoice-product-price');
        var brutto = $('.invoice-product-brutto').val();
        var rebate_input = $('.invoice-product-rebate');
        var note = $('.invoice-product-note').val();
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

        if (retail === 'T')
        {
            location.href = ajax_url + 'docs/invoices/1/list';
        }
        else
        {
            location.href = ajax_url + 'docs/invoices/2/list';
        }
    });

    $('.insert').on('click', function() {

        disableButtons(false);

        var insert_type = $(this).attr('data-insert-type');

        var print_invoice = 'F';
        var email_invoice = 'F';

        if (insert_type === 'print')
        {
            print_invoice = 'T';
        }

        if (insert_type === 'email')
        {
            email_invoice = 'T';
        }

        validateAndCheckMerchandise(print_invoice, email_invoice);
    });

    $('.update').on('click', function() {

        disableButtons(true);

        updateInvoice();
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

    $('.status').on('change', function() {

        var invoice_status = $('.status').val();

        if (invoice_status == 3)
        {
            $('.partial-sum-div').show();
        }
        else
        {
            $('.partial-sum-div').hide();
        }
    });

    $('.confirm-reverse').click(function(e) {

        e.preventDefault();

        var confirm_link = $(this).attr('data-reverse-link');

        swal({
            title: reversing,
            text: invoice_reverse,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: alert_confirm,
            cancelButtonText: alert_cancel,
            closeOnConfirm: false,
            closeOnCancel: true
        }, function () {
            location.href = confirm_link;
        });
    });

    $('.invoices-list').on('click', '.fiscalization', function() {

        var this_invoice = $(this);

        this_invoice.addClass('animated infinite fadeOut');
        this_invoice.prop('disabled', true);
        var invoice_id = this_invoice.attr('data-id');

        fiscalization(invoice_id, this_invoice);
    });

    $('.invoices-list').on('click', '.send-email', function() {

        var this_invoice = $(this);

        this_invoice.addClass('animated infinite rollOut');
        this_invoice.prop('disabled', true);
        var invoice_id = this_invoice.attr('data-id');

        sendEmail(invoice_id, this_invoice);
    });
});