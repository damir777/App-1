function validateForm(client_id, date)
{
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

    if (date)
    {
        date.removeAttr('style');

        if (!date_time_test.test(date.val()))
        {
            date.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    return validation_error;
}

function validateEditProduct(quantity, price)
{
    quantity.removeAttr('style');
    price.removeAttr('style');

    if (!decimal_test.test(quantity.val()))
    {
        quantity.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!decimal_test.test(price.val()))
    {
        price.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
}

function getProducts()
{
    var client_id = $('#client-id').val();

    $.ajax({
        url: ajax_url + 'docs/dispatches/getProducts',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'client': client_id, 'products': products_object},
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
                                '<td class="text-right">' + value.list_price + '</td><td class="text-center">' + value.tax +
                                '</td><td class="text-right">' + value.sum + '</td><td class="text-center">' +
                                '<a href="#" class="edit-product" data-id="' + counter + '"' +
                                ' data-quantity="' + value.quantity + '" data-price="' + value.price + '" data-note="' +
                                value.note + '">' + '<i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>' +
                                '<td class="text-center"><a href="#" class="delete-product" data-id="' + counter +
                                '" data-dp-id="' + value.dp_id + '">' +
                                '<i class="fa fa-times danger" aria-hidden="true"></i></a></td></tr>';

                            //add product to products object
                            products_object[counter] = {
                                id: value.product_id,
                                quantity: value.quantity,
                                price: value.price,
                                note: value.note,
                                dp_id: value.dp_id
                            };

                            counter++;
                        });

                        $('#products-table').html('').append(append_string);

                        var total_append_string = '<tbody><tr><td>' + sum_trans + ':</td><td>' + data.total + '</td></tr>' +
                            '<tr></tr>';

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

                        $('#dispatch-products').show();
                    }
                    else
                    {
                        $('#dispatch-products').hide();
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

function insertDispatch(client_id, note, show_prices)
{
    $.ajax({
        url: ajax_url + 'docs/dispatches/insert',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'client': client_id, 'note': note, 'show_prices': show_prices, 'products': products_object},
        success: function(data) {

            enableButtons(false);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/dispatches/list';
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

function updateDispatch(dispatch_id, date, client_id, note, show_prices)
{
    $.ajax({
        url: ajax_url + 'docs/dispatches/update',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'id': dispatch_id, 'date': date, 'client': client_id, 'note': note, 'show_prices': show_prices,
            'products': products_object},
        success: function(data) {

            enableButtons(true);

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'docs/dispatches/list';
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

function disableButtons(dispatch_id)
{
    if (dispatch_id)
    {
        $('.update').prop('disabled', true);
    }
    else
    {
        $('.insert').prop('disabled', true);
    }

    $('.cancel').prop('disabled', true);
}

function enableButtons(dispatch_id)
{
    if (dispatch_id)
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
var date_time_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/;
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
            note: '',
            dp_id: null
        };

        $('.products-search-box').hide();
        $('.product-search-string').val('');

        getProducts();
    });

    $('#products-table').on('click', '.edit-product', function(e) {

        e.preventDefault();

        var product_object_id = $(this).attr('data-id');
        var product_quantity = $(this).attr('data-quantity');
        var product_price = $(this).attr('data-price');
        var product_note = $(this).attr('data-note');

        $('#editProduct').modal('show');

        $('.product-object-id').val(product_object_id);
        $('.dispatch-product-quantity').val(product_quantity);
        $('.dispatch-product-price').val(product_price);
        $('.dispatch-product-note').val(product_note);
    });

    $('.update-product').on('click', function() {

        //reset validation variable
        check_validation = 1;

        var product_object_id = $('.product-object-id').val();
        var quantity_input = $('.dispatch-product-quantity');
        var price_input = $('.dispatch-product-price');
        var note = $('.dispatch-product-note').val();

        validateEditProduct(quantity_input, price_input);

        if (!check_validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        if (products_object.hasOwnProperty(product_object_id))
        {
            products_object[product_object_id].quantity = quantity_input.val();
            products_object[product_object_id].price = price_input.val();
            products_object[product_object_id].note = note;

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

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'docs/dispatches/list';
    });

    $('.insert').on('click', function() {

        disableButtons(false);

        //reset validation variable
        check_validation = 1;

        var client_id = $('#client-id').val();
        var note = $('.note').val();
        var show_prices = $('.show-prices').val();

        var validation_error = validateForm(client_id, null);

        if (!check_validation)
        {
            enableButtons(false);
            toastr.error(validation_error);

            return 0;
        }

        insertDispatch(client_id, note, show_prices);
    });

    $('.update').on('click', function() {

        disableButtons(true);

        //reset validation variable
        check_validation = 1;

        var dispatch_id = $('#dispatch-id').val();
        var client_id = $('#client-id').val();
        var date_input = $('.dispatch-date');
        var note = $('.note').val();
        var show_prices = $('.show-prices').val();

        var validation_error = validateForm(client_id, date_input);

        if (!check_validation)
        {
            enableButtons(true);
            toastr.error(validation_error);

            return 0;
        }

        updateDispatch(dispatch_id, date_input.val(), client_id, note, show_prices);
    });
});