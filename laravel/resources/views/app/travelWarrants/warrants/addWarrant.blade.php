@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.new_travel_warrant') }}</h2>
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
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('main.warrant_creator')}}</label>
                                        {{ Form::select('creator', $employees, null, array('class' => 'form-control creator')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.employee') }}</label>
                                        {{ Form::select('user', $employees, null, array('class' => 'form-control user')) }}
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
                                                    {{ Form::text('start_date', null,
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
                                                    {{ Form::text('end_date', null, array('class' => 'form-control end-date')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.duration') }}</label>
                                        {{ Form::text('duration', null, array('class' => 'form-control duration')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.start_mileage') }}</label>
                                        {{ Form::text('start_mileage', null, array('class' => 'form-control start-mileage')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.location') }}</label>
                                        {{ Form::text('location', null, array('class' => 'form-control location')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.end_mileage') }}</label>
                                        {{ Form::text('end_mileage', null, array('class' => 'form-control end-mileage')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.purpose') }}</label>
                                        {{ Form::text('purpose', null, array('class' => 'form-control purpose')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.description') }}</label>
                                        {{ Form::text('description', null, array('class' => 'form-control description')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.vehicle') }}</label>
                                        {{ Form::select('vehicle', $vehicles, null, array('class' => 'form-control vehicle')) }}
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
                                            {{ Form::text('non_costs', null, array('class' => 'form-control non-costs')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.advance') }}</label>
                                            {{ Form::text('advance', null, array('class' => 'form-control advance')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="comment">{{ trans('main.note') }}</label>
                                            {{ Form::text('note', null, array('class' => 'form-control note', 'rows' => 3)) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="comment">{{ trans('main.report') }}</label>
                                            {{ Form::text('report', null, array('class' => 'form-control report', 'rows' => 3)) }}
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
                        <div class="ibox-content wages-div" style="display: none"></div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.directions') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-direction">
                                    {{ trans('main.add_direction') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content directions-div" style="display: none"></div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.costs') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-cost">
                                    {{ trans('main.add_cost') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content costs-div" style="display: none"></div>
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