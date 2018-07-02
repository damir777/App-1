@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.new_tax_group') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('url' => '#', 'autocomplete' => 'off', 'class' => 'tax-group-form')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content" id="tour-two-step">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.name') }}</label>
                                            {{ Form::text('name', null, array('class' => 'form-control name')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.note') }}</label>
                                            {{ Form::text('note', null, array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-title m-t-md">
                                <h5>{{ trans('main.taxes') }}</h5>
                                <div class="ibox-tools">
                                    <button type="button" class="btn btn-success add-tax">
                                        {{ trans('main.add_tax') }}
                                    </button>
                                </div>
                            </div>
                            <div class="ibox-content" id="tour-three-step">
                                <div class="row">
                                    <div class="col-sm-6 col-sm-offset-3 taxes-div">
                                        <div class="tax-element">
                                            <div class="panel panel-default">
                                                <div class="panel-heading text-right">
                                                    <button type="button" class="delete-button remove-tax">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.tax_percentage') }}
                                                                    <i class="fa fa-info-circle" aria-hidden="true"
                                                                        data-toggle="tooltip" data-placement="top" title=""
                                                                        data-original-title="{{ trans('main.tooltip_tax_percentage') }}"></i></label>
                                                                {{ Form::text('tax[]', null,
                                                                    array('class' => 'form-control tax-percentage')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.tax_date') }}</label>
                                                                <div class="input-group date">
                                                                    <span class="input-group-addon">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </span>
                                                                    {{ Form::text('tax_date[]', null,
                                                                        array('class' => 'form-control tax-date')) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <a href="{{ route('GetTaxGroups') }}" class="btn btn-warning">
                                    <strong>{{ trans('main.cancel') }}</strong>
                                </a>
                                <button type="button" class="btn btn-primary insert-tax-group">
                                    <strong>{{ trans('main.save') }}</strong>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        var tax_percentage_trans = '{{ trans('main.tax_percentage') }}';
        var tax_date_trans = '{{ trans('main.tax_date') }}';
        var tax_percentage_tooltip_trans = '{{ trans('main.tooltip_tax_percentage') }}';
    </script>

    {{ HTML::script('js/functions/taxGroups.js?v='.date('YmdHi')) }}
@endsection