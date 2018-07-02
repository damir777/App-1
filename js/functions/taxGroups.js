function appendTaxElement()
{
    var tax_element = '<div class="tax-element animated fadeInDown">' +
        '<div class="panel panel-default">' +
        '<div class="panel-heading text-right">' +
        '<button type="button" class="delete-button remove-tax"><i class="fa fa-close"></i></button>' +
        '</div>' +
        '<div class="panel-body">' +
        '<div class="row">' +
        '<div class="col-sm-6">' +
        '<div class="form-group">' +
        '<label>' + tax_percentage_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip"' +
        ' data-placement="top" title="" data-original-title="' + tax_percentage_tooltip_trans + '"></i></label>' +
        '<input type="text" name="tax[]" class="form-control tax-percentage">' +
        '</div></div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group">' +
        '<label>' +tax_date_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="tax_date[]" class="form-control tax-date">' +
        '</div></div></div></div></div></div>';

    $('.taxes-div').append(tax_element);

    $('[data-toggle="tooltip"]').tooltip();

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "dd.mm.yyyy."
    });
}

function validateForm()
{
    var date_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/;
    var decimal_test = /^[0-9]+(\.[0-9]+)?$/;

    var name_input = $('.name');

    name_input.removeAttr('style');

    var check_validation = 1;

    if (name_input.val() == '')
    {
        $(name_input).css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    $('.tax-percentage').each(function() {

        var this_element = $(this);

        this_element.removeAttr('style');

        if (!decimal_test.test(this_element.val()))
        {
            this_element.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    });

    $('.tax-date').each(function() {

        var this_element = $(this);

        this_element.removeAttr('style');

        if (!date_test.test(this_element.val()))
        {
            this_element.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    });

    return check_validation;
}

function insertTaxGroup()
{
    $.ajax({
        url: ajax_url + 'settings/taxGroups/insert',
        type: 'post',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: new FormData($('.tax-group-form')[0]),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'settings/taxGroups/list';
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

function updateTaxGroup()
{
    $.ajax({
        url: ajax_url + 'settings/taxGroups/update',
        type: 'post',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: new FormData($('.tax-group-form')[0]),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    location.href = ajax_url + 'settings/taxGroups/list';
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

    $('.add-tax').on('click', function() {

        appendTaxElement();
    });

    $('.taxes-div').on('click', '.remove-tax', function() {

        if ($('.tax-element').length > 1)
        {
            $(this).parents('.tax-element').remove();
        }
    });

    $('.insert-tax-group').on('click', function() {

        var validation = validateForm();

        if (!validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        insertTaxGroup();
    });

    $('.update-tax-group').on('click', function() {

        var validation = validateForm();

        if (!validation)
        {
            toastr.error(validation_error);

            return 0;
        }

        updateTaxGroup();
    });
});