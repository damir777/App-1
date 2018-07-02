function validateForm(payer, office, item, sum)
{
    var decimal_test = /^[0-9]+(\.[0-9]+)?$/;

    payer.removeAttr('style');
    office.removeAttr('style');
    item.removeAttr('style');
    sum.removeAttr('style');

    var check_validation = 1;

    if (payer.val().trim() == '')
    {
        payer.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (office.has('option').length === 0)
    {
        office.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (item.val().trim() == '')
    {
        item.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!decimal_test.test(sum.val()))
    {
        sum.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    return check_validation;
}

function disableButtons()
{
    $('.create').prop('disabled', true);
    $('.cancel').prop('disabled', true);
}

function enableButtons()
{
    $('.create').prop('disabled', false);
    $('.cancel').prop('disabled', false);
}

$(document).ready(function() {

    $('.office').on('change', function() {

        var office = $('.office').val();

        if (office == 0)
        {
            $('.location-div').show();
        }
        else
        {
            $('.location-div').hide();
        }
    });

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'registerReports/paymentSlips/list';
    });

    $('.create').on('click', function() {

        disableButtons();

        var payer_input = $('.payer');
        var office_input = $('.office');
        var slip_location = $('.location').val();
        var item_input = $('.item');
        var description = $('.description').val();
        var sum_input = $('.sum');

        var validation = validateForm(payer_input, office_input, item_input, sum_input);

        if (!validation)
        {
            enableButtons();
            toastr.error(validation_error);

            return 0;
        }

        $.ajax({
            url: ajax_url + 'registerReports/paymentSlips/insert',
            type: 'post',
            dataType: 'json',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'payer': payer_input.val(), 'office': office_input.val(), 'location': slip_location, 'item': item_input.val(),
                'description': description, 'sum': sum_input.val()},
            success: function(data) {

                enableButtons();

                var responseStatus = data.status;

                switch (responseStatus)
                {
                    case 1:
                        location.href = ajax_url + 'registerReports/paymentSlips/list';
                        break;
                    case 0:
                        toastr.error(data.error);
                        break;
                    default:
                        location.href = ajax_url;
                }
            },
            error: function() {
                enableButtons();
                toastr.error(error);
            }
        });
    });
});