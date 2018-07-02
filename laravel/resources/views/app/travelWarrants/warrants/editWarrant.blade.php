@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.edit_travel_warrant') }}</h2>
            </div>
            <div class="col-xs-4">
                <div class="title-action">
                    <button href="#" class="btn btn-warning btn-circle cancel" data-toggle="tooltip" data-placement="top"
                        title="{{ trans('main.cancel') }}"><i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-circle submit-warrant" data-toggle="tooltip" data-placement="top"
                        title="{{ trans('main.save') }}"><i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row affix-padding">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::hidden('warrant_id', $warrant['warrant']['id'], array('id' => 'warrant-id')) }}
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('main.warrant_creator')}}</label>
                                        {{ Form::select('creator', $employees, $warrant['warrant']['creator_id'],
                                            array('class' => 'form-control creator')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.employee') }}</label>
                                        {{ Form::select('user', $employees, $warrant['warrant']['user_id'],
                                            array('class' => 'form-control user')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.start') }}</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    {{ Form::text('start_date', $warrant['warrant']['start_date'],
                                                        array('class' => 'form-control start-date')) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.end') }}</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    {{ Form::text('end_date', $warrant['warrant']['end_date'],
                                                        array('class' => 'form-control end-date')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.duration') }}</label>
                                        {{ Form::text('duration', $warrant['warrant']['duration'],
                                            array('class' => 'form-control duration')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.start_mileage') }}</label>
                                        {{ Form::text('start_mileage', $warrant['warrant']['start_mileage'],
                                            array('class' => 'form-control start-mileage')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.location') }}</label>
                                        {{ Form::text('location', $warrant['warrant']['location'],
                                            array('class' => 'form-control location')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.end_mileage') }}</label>
                                        {{ Form::text('end_mileage', $warrant['warrant']['end_mileage'],
                                            array('class' => 'form-control end-mileage')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.purpose') }}</label>
                                        {{ Form::text('purpose', $warrant['warrant']['purpose'],
                                            array('class' => 'form-control purpose')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.description') }}</label>
                                        {{ Form::text('description', $warrant['warrant']['description'],
                                            array('class' => 'form-control description')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.vehicle') }}</label>
                                        {{ Form::select('vehicle', $vehicles, $warrant['warrant']['vehicle'],
                                            array('class' => 'form-control vehicle')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox collapsed m-t-md">
                            <div class="ibox-title collapse-link">
                                <h4>{{ trans('main.other_data') }}</h4>
                                <div class="ibox-tools">
                                    <a><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.non_costs') }} <i class="fa fa-info-circle" aria-hidden="true"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="{{ trans('main.tooltip_non_costs') }}"></i></label>
                                            {{ Form::text('non_costs', $warrant['warrant']['non_costs'],
                                                array('class' => 'form-control non-costs')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.advance') }}</label>
                                            {{ Form::text('advance', $warrant['warrant']['advance'],
                                                array('class' => 'form-control advance')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="comment">{{ trans('main.note') }}</label>
                                            {{ Form::text('note', $warrant['warrant']['note'],
                                                array('class' => 'form-control note', 'rows' => 3)) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="comment">{{ trans('main.report') }}</label>
                                            {{ Form::text('report', $warrant['warrant']['report'],
                                                array('class' => 'form-control report', 'rows' => 3)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.wages') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-wage">
                                    {{ trans('main.add_wage') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content wages-div" @if (count($warrant['wages']) == 0) {{ 'style=display:none' }} @endif>
                            @foreach ($warrant['wages'] as $wage)
                                <div class="wage-element">
                                    {{ Form::hidden('wage_id[]', $wage->id, array('class' => 'wage-id')) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading text-right">
                                                    <button type="button" class="delete-button remove-item" data-item-type="wage"
                                                        data-item-id="{{ $wage['id'] }}">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.country') }}</label>
                                                                {{ Form::select('country[]', $countries, $wage->country,
                                                                    array('class' => 'form-control wage-country')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.date') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('wage_date[]', $wage->date,
                                                                        array('class' => 'form-control wage-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.wage') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="{{ trans('main.tooltip_wage') }}"></i></label>
                                                                {{ Form::text('wage[]', $wage->wage,
                                                                    array('class' => 'form-control wage-wage')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.wage_type') }}</label>
                                                                {{ Form::select('wage_type[]', $wages, $wage->wage_id,
                                                                    array('class' => 'form-control wage-wage-type')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.departure') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('departure_date[]', $wage->departure_date,
                                                                        array('class' => 'form-control wage-departure-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label>{{ trans('main.time') }}</label>
                                                            <div class="input-group clockpicker" data-autoclose="true">
                                                                {{ Form::text('departure_time[]', $wage->departure_time,
                                                                    array('class' => 'form-control wage-departure-time')) }}
                                                                <span class="input-group-addon">
                                                                    <span class="fa fa-clock-o"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.arrival') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('arrival_date[]', $wage->arrival_date,
                                                                        array('class' => 'form-control wage-arrival-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label>{{ trans('main.time') }}</label>
                                                            <div class="input-group clockpicker" data-autoclose="true">
                                                                {{ Form::text('arrival_time[]', $wage->arrival_time,
                                                                    array('class' => 'form-control wage-arrival-time')) }}
                                                                <span class="input-group-addon">
                                                                    <span class="fa fa-clock-o"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.directions') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-direction">
                                    {{ trans('main.add_direction') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content directions-div" @if (count($warrant['directions']) == 0) {{ 'style=display:none' }} @endif>
                            @foreach ($warrant['directions'] as $direction)
                                <div class="direction-element">
                                    {{ Form::hidden('direction_id[]', $direction->id, array('class' => 'direction-id')) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading text-right">
                                                    <button type="button" class="delete-button remove-item" data-item-type="direction"
                                                        data-item-id="{{ $direction['id'] }}">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.date') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('direction_date[]', $direction->date,
                                                                        array('class' => 'form-control direction-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.transport_type') }}</label>
                                                                {{ Form::text('transport_type[]', $direction->transport_type,
                                                                    array('class' => 'form-control direction-transport-type')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.from') }}</label>
                                                                {{ Form::text('start_location[]', $direction->start_location,
                                                                    array('class' => 'form-control direction-start-location')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.to') }}</label>
                                                                {{ Form::text('end_location[]', $direction->end_location,
                                                                    array('class' => 'form-control direction-end-location')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.kilometers') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="{{ trans('main.tooltip_kilometers') }}"></i> <i class="fa fa-exchange calculate-distance"></i></label>
                                                                {{ Form::text('distance[]', $direction->distance,
                                                                    array('class' => 'form-control direction-distance')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.km_price') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                                                {{ Form::text('km_price[]', $direction->km_price,
                                                                    array('class' => 'form-control direction-km-price')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.costs') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-cost">
                                    {{ trans('main.add_cost') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content costs-div" @if (count($warrant['costs']) == 0) {{ 'style=display:none' }} @endif>
                            @foreach ($warrant['costs'] as $cost)
                                <div class="cost-element">
                                    {{ Form::hidden('cost_id[]', $cost->id, array('class' => 'cost-id')) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading text-right">
                                                    <button type="button" class="delete-button remove-item" data-item-type="cost"
                                                        data-item-id="{{ $cost['id'] }}">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.date') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('cost_date[]', $cost->date,
                                                                        array('class' => 'form-control cost-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.cost_type') }}</label>
                                                                {{ Form::text('cost_type[]', $cost->cost_type,
                                                                    array('class' => 'form-control cost-cost-type')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.description') }}</label>
                                                                {{ Form::text('cost_description[]', $cost->description,
                                                                    array('class' => 'form-control cost-description')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.sum') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="{{ trans('main.tooltip_sum') }}"></i></label>
                                                                {{ Form::text('sum[]', $cost->sum,
                                                                    array('class' => 'form-control cost-sum')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.non_costs') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="{{ trans('main.tooltip_non_costs') }}"></i></label>
                                                                {{ Form::text('cost_non_costs[]', $cost->non_costs,
                                                                    array('class' => 'form-control cost-non-costs')) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var countries_select_element = '{{ Form::select('country[]', $countries, null,
            array('class' => 'form-control wage-country')) }}';
        var wages_select_element = '{{ Form::select('wage_type[]', $wages, null,
            array('class' => 'form-control wage-wage-type')) }}';
        var country_trans = '{{ trans('main.country') }}';
        var date_trans = '{{ trans('main.date') }}';
        var wage_trans = '{{ trans('main.wage') }}';
        var wage_type_trans = '{{ trans('main.wage_type') }}';
        var departure_trans = '{{ trans('main.departure') }}';
        var arrival_trans = '{{ trans('main.arrival') }}';
        var time_trans = '{{ trans('main.time') }}';
        var transport_type_trans = '{{ trans('main.transport_type') }}';
        var from_trans = '{{ trans('main.from') }}';
        var to_trans = '{{ trans('main.to') }}';
        var km_trans = '{{ trans('main.kilometers') }}';
        var km_price_trans = '{{ trans('main.km_price') }}';
        var cost_type_trans = '{{ trans('main.cost_type') }}';
        var description_trans = '{{ trans('main.description') }}';
        var sum_trans = '{{ trans('main.sum') }}';
        var non_costs_trans = '{{ trans('main.non_costs') }}';
        var wage_tooltip_trans = '{{ trans('main.tooltip_wage') }}';
        var kilometers_tooltip_trans = '{{ trans('main.tooltip_kilometers') }}';
        var price_tooltip_trans = '{{ trans('main.tooltip_price') }}';
        var sum_tooltip_trans = '{{ trans('main.tooltip_sum') }}';
        var non_costs_tooltip_trans = '{{ trans('main.tooltip_non_costs') }}';
    </script>

    {{ HTML::script('js/functions/travelWarrants.js?v='.date('YmdHi')) }}
@endsection