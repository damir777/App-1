function validateProductForm()
{
    var decimal_test = /^[0-9]+(\.[0-9]+)?$/;

    var category_input = $('.category');
    var tax_group_input = $('.tax-group');
    var code_input = $('.code');
    var name_input = $('.form-product-name');
    var price_input = $('.price');

    category_input.removeAttr('style');
    tax_group_input.removeAttr('style');
    code_input.removeAttr('style');
    name_input.removeAttr('style');
    price_input.removeAttr('style');

    var check_validation = 1;

    if (category_input.has('option').length === 0)
    {
        category_input.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (tax_group_input.has('option').length === 0)
    {
        tax_group_input.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (code_input.val().trim() == '')
    {
        code_input.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (name_input.val().trim() == '')
    {
        name_input.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!decimal_test.test(price_input.val()))
    {
        price_input.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    return check_validation;
}

function insertProduct()
{
    $.ajax({
        url: ajax_url + 'products/insert',
        type: 'post',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: new FormData($('.product-form')[0]),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    $('#addProduct').modal('hide');
                    toastr.success(product_insert);
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

function searchProducts(search_string)
{
    $.ajax({
        url: ajax_url + 'products/search',
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

                    var products_div = $('#products');
                    var append_string = '<div class="results-wrapper">';

                    products_div.html('');
                    $('.close-product-search').show();

                    if (data.products.length > 0)
                    {
                        var add_product_class = 'add-product';

                        if (is_document === 'F')
                        {
                            add_product_class = 'client-price-modal';
                        }

                        $.each(data.products, function(index, value)
                        {
                            append_string += '<div class="result ' + add_product_class + '" data-id="' + value.id +
                                '" data-name="' + value.name + '"><p>' + value.name + ' (' + value.code + ')</p></div>';
                        });
                    }
                    else
                    {
                        append_string += '<div class="result alert-danger no-results"><p>' + no_results_trans+ '</p></div>';
                    }

                    append_string += '</div';

                    products_div.append(append_string);

                    $('.products-search-box').show();

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

function insertCategory(name)
{
    $.ajax({
        url: ajax_url + 'categories/insert',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'name': name},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    $('#addCategory').modal('hide');
                    toastr.success(category_insert);
                    getCategories();
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

function getCategories()
{
    $.ajax({
        url: ajax_url + 'categories/selectList',
        type: 'get',
        dataType: 'json',
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    $('.category').html('');

                    var manufacturers_select = document.getElementsByClassName('category');

                    $.each(data.data, function(index, value) {

                        var opt = document.createElement('option');
                        opt.innerHTML = value;
                        opt.value = index;
                        manufacturers_select[0].appendChild(opt);
                    });

                    $('#addCategory').modal('hide');

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

$(document).ready(function() {

    $('.insert-product').on('click', function() {

        var validation = validateProductForm();

        if (!validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        insertProduct();
    });

    $('.product-search-string').keyup(function() {

        var search_string = $('.product-search-string').val().trim();

        if (search_string.length < 2)
        {
            $('.products-search-box').hide();

            return 0;
        }

        searchProducts(search_string);
    });

    $('.close-product-search').on('click', function() {

        $('.products-search-box').hide();
        $('.product-search-string').val('');
    });

    $('.insert-category').on('click', function() {

        var name_input = $('.category-name');

        if (name_input.val().trim() == '')
        {
            toastr.error(data.validation_error);

            return 0;
        }

        insertCategory(name_input.val());
    });

    $('#products').on('click', '.client-price-modal', function() {

        var this_product = $(this);
        var product_id = this_product.attr('data-id');

        $('.product-id').val(product_id);

        $('.client-price').val('');

        $('#clientPrice').modal('show');

        $('.products-search-box').hide();
        $('.product-search-string').val('');
    });

    $(document).on('show.bs.modal', '.modal', function () {

        var zIndex = 1040 + (10 * $('.modal:visible').length);

        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).on('hidden.bs.modal', '.modal', function () {

        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });
});