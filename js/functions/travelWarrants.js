function appendWageElement()
{
    var wage_element = '<div class="wage-element animated fadeInDown">' +
        '<div class="row">' +
        '<div class="col-sm-12">' +
        '<div class="panel panel-default">' +
        '<div class="panel-heading text-right">' +
        '<button type="button" class="delete-button remove-item" data-item-type="wage"><i class="fa fa-close"></i></button>' +
        '</div>' +
        '<div class="panel-body">' +
        '<div class="row">' +
        '<div class="col-sm-3">' +
        '<div class="form-group"><label>' + country_trans + '</label>' + countries_select_element + '</div>' +
        '</div>' +
        '<div class="col-sm-3">' +
        '<div class="form-group">' +
        '<label>' + date_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="wage_date[]" class="form-control wage-date">' +
        '</div></div></div>' +
        '<div class="col-sm-3">' +
        '<div class="form-group">' +
        '<label>' + wage_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"' +
        ' title="" data-original-title="' + wage_tooltip_trans + '"></i></label>' +
        '<input type="text" name="wage[]" class="form-control wage-wage">' +
        '</div></div>' +
        '<div class="col-sm-3"><div class="form-group"><label>' + wage_type_trans + '</label>' + wages_select_element +
        '</div></div></div>' +
        '<div class="row">' +
        '<div class="col-sm-3">' +
        '<div class="form-group">' +
        '<label>' + departure_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="departure_date[]" class="form-control wage-departure-date">' +
        '</div></div></div>' +
        '<div class="col-sm-3">' +
        '<label>' + time_trans + '</label>' +
        '<div class="input-group clockpicker" data-autoclose="true">' +
        '<input type="text" name="departure_time[]" class="form-control wage-departure-time">' +
        '<span class="input-group-addon"><span class="fa fa-clock-o"></span></span>' +
        '</div></div>' +
        '<div class="col-sm-3">' +
        '<div class="form-group">' +
        '<label>' + arrival_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="arrival_date[]" class="form-control wage-arrival-date">' +
        '</div></div></div>' +
        '<div class="col-sm-3">' +
        '<label>' + time_trans + '</label>' +
        '<div class="input-group clockpicker" data-autoclose="true">' +
        '<input type="text" name="arrival_time[]" class="form-control wage-arrival-time">' +
        '<span class="input-group-addon"><span class="fa fa-clock-o"></span></span>' +
        '</div></div></div></div></div></div></div></div>';

    $('.wages-div').append(wage_element).show();

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "dd.mm.yyyy."
    });

    $('.clockpicker').clockpicker();

    $('[data-toggle="tooltip"]').tooltip();
}

function appendDirectionElement()
{
    var direction_element = '<div class="direction-element animated fadeInDown">' +
        '<div class="row">' +
        '<div class="col-sm-12">' +
        '<div class="panel panel-default">' +
        '<div class="panel-heading text-right">' +
        '<button type="button" class="delete-button remove-item" data-item-type="direction"><i class="fa fa-close"></i></button>' +
        '</div>' +
        '<div class="panel-body">' +
        '<div class="row">' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + date_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="direction_date[]" class="form-control direction-date">' +
        '</div></div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + transport_type_trans + '</label>' +
        '<input type="text" name="transport_type[]" class="form-control direction-transport-type">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + from_trans + '</label>' +
        '<input type="text" name="start_location[]" class="form-control direction-start-location">' +
        '</div></div></div>' +
        '<div class="row">' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + to_trans + '</label>' +
        '<input type="text" name="end_location[]" class="form-control direction-end-location">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + km_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"' +
        ' title="" data-original-title="' + kilometers_tooltip_trans + '"></i>' +
        '<i class="fa fa-exchange calculate-distance"></i></label>' +
        '<input type="text" name="distance[]" class="form-control direction-distance">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + km_price_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"' +
        ' title="" data-original-title="' + price_tooltip_trans + '"></i></label>' +
        '<input type="text" name="km_price[]" class="form-control direction-km-price">' +
        '</div></div></div></div></div></div></div>';

    $('.directions-div').append(direction_element).show();

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "dd.mm.yyyy."
    });

    $('[data-toggle="tooltip"]').tooltip();
}

function appendCostElement()
{
    var cost_element = '<div class="cost-element animated fadeInDown">' +
        '<div class="row">' +
        '<div class="col-sm-12">' +
        '<div class="panel panel-default">' +
        '<div class="panel-heading text-right">' +
        '<button type="button" class="delete-button remove-item" data-item-type="cost"><i class="fa fa-close"></i></button>' +
        '</div>' +
        '<div class="panel-body">' +
        '<div class="row">' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + date_trans + '</label>' +
        '<div class="input-group date">' +
        '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' +
        '<input type="text" name="cost_date[]" class="form-control cost-date">' +
        '</div></div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + cost_type_trans + '</label>' +
        '<input type="text" name="cost_type[]" class="form-control cost-cost-type">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + description_trans + '</label>' +
        '<input type="text" name="cost_description[]" class="form-control cost-description">' +
        '</div></div></div>' +
        '<div class="row">' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + sum_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"' +
        ' title="" data-original-title="' + sum_tooltip_trans + '"></i></label>' +
        '<input type="text" name="sum[]" class="form-control cost-sum">' +
        '</div></div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group">' +
        '<label>' + non_costs_trans + ' <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip"' +
        ' data-placement="top" title="" data-original-title="' + non_costs_tooltip_trans + '"></i></label>' +
        '<input type="text" name="cost_non_costs[]" class="form-control cost-non-costs">' +
        '</div></div></div></div></div></div></div></div>';

    $('.costs-div').append(cost_element).show();

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "dd.mm.yyyy."
    });

    $('[data-toggle="tooltip"]').tooltip();
}

function validateForm(creator, user, start_date, end_date, vehicle, start_mileage, end_mileage, duration, location, purpose,
    description, advance, non_costs)
{
    creator.removeAttr('style');
    user.removeAttr('style');
    start_date.removeAttr('style');
    end_date.removeAttr('style');
    vehicle.removeAttr('style');
    start_mileage.removeAttr('style');
    end_mileage.removeAttr('style');
    duration.removeAttr('style');
    location.removeAttr('style');
    purpose.removeAttr('style');
    description.removeAttr('style');
    advance.removeAttr('style');
    non_costs.removeAttr('style');

    if (creator.has('option').length === 0)
    {
        creator.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (user.has('option').length === 0)
    {
        user.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!date_test.test(start_date.val()))
    {
        start_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!date_test.test(end_date.val()))
    {
        end_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (vehicle.has('option').length === 0)
    {
        vehicle.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(start_mileage.val()))
    {
        start_mileage.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(end_mileage.val()))
    {
        end_mileage.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (duration.val().trim() == '')
    {
        duration.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (location.val().trim() == '')
    {
        location.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (purpose.val().trim() == '')
    {
        purpose.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (description.val().trim() == '')
    {
        description.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (advance.val().trim() != '')
    {
        if (!decimal_test.test(advance.val()))
        {
            advance.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }

    if (non_costs.val().trim() != '')
    {
        if (!decimal_test.test(non_costs.val()))
        {
            non_costs.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }
}

function validateWage(date, wage, wage_type, departure_date, departure_time, arrival_date, arrival_time)
{
    date.removeAttr('style');
    wage.removeAttr('style');
    wage_type.removeAttr('style');
    departure_date.removeAttr('style');
    departure_time.removeAttr('style');
    arrival_date.removeAttr('style');
    arrival_time.removeAttr('style');

    if (!date_test.test(date.val()))
    {
        date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(wage.val()))
    {
        wage.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (wage_type.has('option').length === 0)
    {
        wage_type.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!date_test.test(departure_date.val()))
    {
        departure_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!time_test.test(departure_time.val()))
    {
        departure_time.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!date_test.test(arrival_date.val()))
    {
        arrival_date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!time_test.test(arrival_time.val()))
    {
        arrival_time.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
}

function validateDirection(date, transport_type, start_location, end_location, distance, km_price)
{
    date.removeAttr('style');
    transport_type.removeAttr('style');
    start_location.removeAttr('style');
    end_location.removeAttr('style');
    distance.removeAttr('style');
    km_price.removeAttr('style');

    if (!date_test.test(date.val()))
    {
        date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (transport_type.val().trim() == '')
    {
        transport_type.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (start_location.val().trim() == '')
    {
        start_location.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (end_location.val().trim() == '')
    {
        end_location.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!integer_test.test(distance.val()))
    {
        distance.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!decimal_test.test(km_price.val()))
    {
        km_price.css('border', '1px solid #FF0000');

        check_validation = 0;
    }
}

function validateCost(date, cost_type, description, sum, non_costs)
{
    date.removeAttr('style');
    cost_type.removeAttr('style');
    description.removeAttr('style');
    sum.removeAttr('style');
    non_costs.removeAttr('style');

    if (!date_test.test(date.val()))
    {
        date.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (cost_type.val().trim() == '')
    {
        cost_type.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (description.val().trim() == '')
    {
        description.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (!decimal_test.test(sum.val()))
    {
        sum.css('border', '1px solid #FF0000');

        check_validation = 0;
    }

    if (non_costs.val().trim() != '')
    {
        if (!decimal_test.test(non_costs.val()))
        {
            non_costs.css('border', '1px solid #FF0000');

            check_validation = 0;
        }
    }
}

function calculateDistance(start_location, end_location, parent_div)
{
    $.ajax({
        url: ajax_url + 'travelWarrants/calculateDistance',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: {'start_location': start_location, 'end_location': end_location},
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:
                    parent_div.find('.direction-distance').val(data.data);
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

function disableButtons()
{
    $('.submit-warrant').prop('disabled', true);
    $('.cancel').prop('disabled', true);
}

function enableButtons()
{
    $('.submit-warrant').prop('disabled', false);
    $('.cancel').prop('disabled', false);
}

//define global validation variables
var date_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/;
var integer_test = /^[0-9]+$/;
var decimal_test = /^[0-9]+(\.[0-9]+)?$/;
var time_test = /^([01]\d|2[0-3]):([0-5][0-9])$/;

var check_validation = 1;

$(document).ready(function() {

    $('.add-wage').on('click', function() {

        appendWageElement();
    });

    $('.add-direction').on('click', function() {

        appendDirectionElement();
    });

    $('.add-cost').on('click', function() {

        appendCostElement();
    });

    $('.directions-div').on('click', '.calculate-distance', function() {

        var this_direction = $(this);
        var parent_div = this_direction.parents('.direction-element');

        var start_location = parent_div.find('.direction-start-location').val();
        var end_location = parent_div.find('.direction-end-location').val();

        if (start_location == '' || end_location == '')
        {
            return 0;
        }

        calculateDistance(start_location, end_location, parent_div);
    });

    $('.wrapper-content').on('click', '.remove-item', function() {

        var this_item = $(this);
        var item_type = this_item.attr('data-item-type');
        var warrant_id = $('#warrant-id').val();
        var item_id = this_item.attr('data-item-id');

        if (warrant_id && item_id)
        {
            $.ajax({
                url: ajax_url + 'travelWarrants/deleteItem',
                type: 'post',
                dataType: 'json',
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
                data: {'warrant_id': warrant_id, 'item_type': item_type, 'item_id': item_id},
                success: function(data) {

                    var responseStatus = data.status;

                    switch (responseStatus)
                    {
                        case 1:
                            this_item.parents('.' + item_type + '-element').remove();

                            if ($('.' + item_type + '-element').length === 0)
                            {
                                $('.' + item_type + 's-div').hide();
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
        else
        {
            this_item.parents('.' + item_type + '-element').remove();

            if ($('.' + item_type + '-element').length === 0)
            {
                $('.' + item_type + 's-div').hide();
            }
        }
    });

    $('.cancel').on('click', function() {

        location.href = ajax_url + 'travelWarrants/warrants/list';
    });

    $('.submit-warrant').on('click', function() {

        disableButtons();

        //reset validation variable
        check_validation = 1;

        //set wages, directions and costs lists
        var wages_list = [];
        var directions_list = [];
        var costs_list = [];

        //set default warrant id
        var warrant_id = null;

        var warrant_id_input = $('#warrant-id');

        if (warrant_id_input.val())
        {
            warrant_id = warrant_id_input.val();
        }

        var creator_input = $('.creator');
        var user_input = $('.user');
        var start_date_input = $('.start-date');
        var end_date_input = $('.end-date');
        var vehicle_input = $('.vehicle');
        var start_mileage_input = $('.start-mileage');
        var end_mileage_input = $('.end-mileage');
        var duration_input = $('.duration');
        var location_input = $('.location');
        var purpose_input = $('.purpose');
        var description_input = $('.description');
        var advance_input = $('.advance');
        var non_costs_input = $('.non-costs');
        var note = $('.note').val();
        var report = $('.report').val();

        validateForm(creator_input, user_input, start_date_input, end_date_input, vehicle_input, start_mileage_input,
            end_mileage_input, duration_input, location_input, purpose_input, description_input, advance_input, non_costs_input);

        $('.wage-element').each(function() {

            var this_element = $(this);

            //set default wage id
            var wage_id = null;

            var wage_id_input = this_element.find('.wage-id');

            if (wage_id_input.val())
            {
                wage_id = wage_id_input.val();
            }

            var country_input = this_element.find('.wage-country');
            var date_input = this_element.find('.wage-date');
            var wage_input = this_element.find('.wage-wage');
            var wage_type_input = this_element.find('.wage-wage-type');
            var departure_date_input = this_element.find('.wage-departure-date');
            var departure_time_input = this_element.find('.wage-departure-time');
            var arrival_date_input = this_element.find('.wage-arrival-date');
            var arrival_time_input = this_element.find('.wage-arrival-time');

            validateWage(date_input, wage_input, wage_type_input, departure_date_input, departure_time_input, arrival_date_input,
                arrival_time_input);

            //create new wage object
            var wage_object = {
                id: wage_id, country: country_input.val(), date: date_input.val(), wage: wage_input.val(),
                wage_type: wage_type_input.val(), departure_date: departure_date_input.val(),
                departure_time: departure_time_input.val(), arrival_date: arrival_date_input.val(),
                arrival_time: arrival_time_input.val()
            };

            //append wage object to wages list
            wages_list.push(wage_object);
        });

        $('.direction-element').each(function() {

            var this_element = $(this);

            //set default direction id
            var direction_id = null;

            var direction_id_input = this_element.find('.direction-id');

            if (direction_id_input.val())
            {
                direction_id = direction_id_input.val();
            }

            var date_input = this_element.find('.direction-date');
            var transport_type_input = this_element.find('.direction-transport-type');
            var start_location_input = this_element.find('.direction-start-location');
            var end_location_input = this_element.find('.direction-end-location');
            var distance_input = this_element.find('.direction-distance');
            var km_price_input = this_element.find('.direction-km-price');

            validateDirection(date_input, transport_type_input, start_location_input, end_location_input, distance_input,
                km_price_input);

            //create new direction object
            var direction_object = {
                id: direction_id, date: date_input.val(), transport_type: transport_type_input.val(),
                start_location: start_location_input.val(), end_location: end_location_input.val(), distance: distance_input.val(),
                km_price: km_price_input.val()
            };

            //append direction object to directions list
            directions_list.push(direction_object);
        });

        $('.cost-element').each(function() {

            var this_element = $(this);

            //set default cost id
            var cost_id = null;

            var cost_id_input = this_element.find('.cost-id');

            if (cost_id_input.val())
            {
                cost_id = cost_id_input.val();
            }

            var date_input = this_element.find('.cost-date');
            var cost_type_input = this_element.find('.cost-cost-type');
            var description_input = this_element.find('.cost-description');
            var sum_input = this_element.find('.cost-sum');
            var non_costs_input = this_element.find('.cost-non-costs');

            validateCost(date_input, cost_type_input, description_input, sum_input, non_costs_input);

            //create new cost object
            var cost_object = {
                id: cost_id, date: date_input.val(), cost_type: cost_type_input.val(), description: description_input.val(),
                sum: sum_input.val(), non_costs: non_costs_input.val()
            };

            //append cost object to costs list
            costs_list.push(cost_object);
        });

        if (!check_validation)
        {
            enableButtons();
            toastr.error(validation_error);

            return 0;
        }

        //set submit route
        var submit_route = 'travelWarrants/warrants/insert';

        if (warrant_id)
        {
            submit_route = 'travelWarrants/warrants/update';
        }

        $.ajax({
            url: ajax_url + submit_route,
            type: 'post',
            dataType: 'json',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'id': warrant_id, 'creator': creator_input.val(), 'user': user_input.val(),
                'start_date': start_date_input.val(), 'end_date': end_date_input.val(), 'vehicle': vehicle_input.val(),
                'start_mileage': start_mileage_input.val(), 'end_mileage': end_mileage_input.val(),
                'duration': duration_input.val(), 'location': location_input.val(), 'purpose': purpose_input.val(),
                'description': description_input.val(), 'advance': advance_input.val(), 'non_costs': non_costs_input.val(),
                'note': note, 'report': report, 'wages': wages_list, 'directions': directions_list, 'costs': costs_list},
            success: function(data) {

                enableButtons();

                var responseStatus = data.status;

                switch (responseStatus)
                {
                    case 1:
                        location.href = ajax_url + 'travelWarrants/warrants/list';
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