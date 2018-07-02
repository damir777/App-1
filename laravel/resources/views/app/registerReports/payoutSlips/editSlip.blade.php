@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.edit_payout_slip') }}</h2>
            </div>
            <div class="col-xs-4">
                <div class="title-action">
                    <button href="#" class="btn btn-warning btn-circle cancel" data-toggle="tooltip" data-placement="top"
                        title="{{ trans('main.cancel') }}"><i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-circle submit-slip" data-toggle="tooltip" data-placement="top"
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
                        {{ Form::hidden('slip_id', $slip->id, array('id' => 'slip-id')) }}
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('main.employee')}}</label>
                                        {{ Form::select('employee', $employees, $slip->employee->id,
                                            array('class' => 'form-control employee')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.office') }}</label>
                                        {{ Form::select('office', $offices, $slip->office_id,
                                            array('class' => 'form-control office', 'disabled')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.payout_slip_income') }}</label>
                                        {{ Form::select('payout_slip_income',
                                            array('T' => trans('main.yes'), 'F' => trans('main.no')), $slip->income,
                                            array('class' => 'form-control payout-slip-income')) }}
                                    </div>
                                </div>
                                <div class="col-sm-6 location-div" @if ($slip->office_id != 0) {{ 'style=display:none' }} @endif>
                                    <div class="form-group">
                                        <label>{{ trans('main.document_location') }}</label>
                                        {{ Form::text('location', $slip->location, array('class' => 'form-control location')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.note') }}</label>
                                        {{ Form::textarea('note', $slip->note,
                                            array('class' => 'form-control note', 'rows' => 3)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.items') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success add-item">
                                    {{ trans('main.add_item') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content items-div">
                            @foreach ($items as $item)
                                <div class="item-element">
                                    {{ Form::hidden('item_id[]', $item->id, array('class' => 'item-id')) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading text-right">
                                                    <button type="button" class="delete-button remove-item"
                                                        data-item-id="{{ $item['id'] }}"><i class="fa fa-close"></i>
                                                    </button>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.item') }}</label>
                                                                {{ Form::text('item[]', $item->item,
                                                                    array('class' => 'form-control item')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.description') }}</label>
                                                                {{ Form::text('description[]', $item->description,
                                                                    array('class' => 'form-control description')) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>{{ trans('main.sum') }} <i class="fa fa-info-circle"
                                                                    aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                                                    title=""
                                                                    data-original-title="{{ trans('main.tooltip_sum') }}"></i>
                                                                </label>
                                                                {{ Form::text('sum[]', $item->sum,
                                                                    array('class' => 'form-control sum')) }}
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
        var sum_trans = '{{ trans('main.sum') }}';
        var description_trans = '{{ trans('main.description') }}';
        var item_trans = '{{ trans('main.item') }}';
        var delete_item_warning = '{{ trans('errors.delete_item') }}';
        var sum_tooltip_trans = '{{ trans('main.tooltip_sum') }}';
    </script>

    {{ HTML::script('js/functions/payoutSlips.js?v='.date('YmdHi')) }}
@endsection