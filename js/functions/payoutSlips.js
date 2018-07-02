function appendItemElement()
{
    var item_element = '<div class="item-element">' +
        '<div class="row">' +
        '<div class="col-sm-12">' +
        '<div class="panel panel-default">' +
        '<div class="panel-heading text-right">' +
        '<button type="button" class="delete-button remove-item"><i class="fa fa-close"></i></button>' +
        '</div>' +
        '<div class="panel-body">' +
        '<div class="row">' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + item_trans + '</label><input type="text" name="item[]" class="form-control item"></div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + description_trans + '</label>' +
        '<input type="type" name="description[]" class="form-control description">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group"><label>' + sum_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip"' +
        ' data-placement="top" title="" data-original-title="' + sum_tooltip_trans + '"></i></label>' +
        '<input type="text" name="sum[]" class="form-control sum"></div></div></div></div></div></div></div></div>';

    $('.items-div').append(item_element).show();

    $('[data-toggle="tooltip"]').tooltip();
}

function validateForm(employee, office)
{
    employee.removeAttr('style');
    office.removeAttr('style');

    if (employee.has('option').length === 0)
    {
        employee.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (office.has('option').length === 0)
    {
        office.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
}

function validateItem(item, sum)
{
    var decimal_test = /^[0-9]+(\.[0-9]+)?$/;

    item.removeAttr('style');
    sum.removeAttr('style');

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

var check_validation = 1;

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

    $('.add-item').on('click', function() {

        appendItemElement();
    });

    $('.items-div').on('click', '.remove-item', function() {

        if ($('.item-element').length === 1)
        {
            toastr.warning(delete_item_warning);
            return 0;
        }

        var this_item = $(this);
        var slip_id = $('#slip-id').val();
        var item_id = this_item.attr('data-item-id');

        if (slip_id && item_id)
        {
            $.ajax({
                url: ajax_url + 'registerReports/payoutSlips/deleteItem',
                type: 'post',
                dataType: 'json',
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
                data: {'slip_id': slip_id, 'item_id': item_id},
                success: function(data) {

                    var responseStatus = data.status;

                    switch (responseStatus)
                    {
                        case 1:
                            this_item.parents('.item-element').remove();
                            break;
                        case 2:
                            toastr.error(data.warning);
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
        else
        {
            this_item.parents('.item-element').remove();
        }
    });

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'registerReports/payoutSlips/list';
    });

    $('.submit-slip').on('click', function() {

        disableButtons();

        //reset validation variable
        check_validation = 1;

        //set items lists
        var items_list = [];

        //set default slip id
        var slip_id = null;

        var slip_id_input = $('#slip-id');

        if (slip_id_input.val())
        {
            slip_id = slip_id_input.val();
        }

        var employee_input = $('.employee');
        var office_input = $('.office');
        var income = $('.payout-slip-income').val();
        var document_location = $('.location').val();
        var note = $('.note').val();

        validateForm(employee_input, office_input);

        $('.item-element').each(function() {

            var this_element = $(this);

            //set default item id
            var item_id = null;

            var item_id_input = this_element.find('.item-id');

            if (item_id_input.val())
            {
                item_id = item_id_input.val();
            }

            var item_input = this_element.find('.item');
            var description = this_element.find('.description').val();
            var sum_input = this_element.find('.sum');

            validateItem(item_input, sum_input);

            //create new item object
            var item_object = {
                id: item_id, item: item_input.val(), description: description, sum: sum_input.val()
            };

            //append item object to items list
            items_list.push(item_object);
        });

        if (!check_validation)
        {
            enableButtons();
            toastr.error(validation_error);

            return 0;
        }

        //set submit route
        var submit_route = 'registerReports/payoutSlips/insert';

        if (slip_id)
        {
            submit_route = 'registerReports/payoutSlips/update';
        }

        $.ajax({
            url: ajax_url + submit_route,
            type: 'post',
            dataType: 'json',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'id': slip_id, 'employee': employee_input.val(), 'office': office_input.val(), 'income': income,
                'location': document_location, 'note': note, 'items': items_list},
            success: function(data) {

                enableButtons();

                var responseStatus = data.status;

                switch (responseStatus)
                {
                    case 1:
                        location.href = ajax_url + 'registerReports/payoutSlips/list';
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