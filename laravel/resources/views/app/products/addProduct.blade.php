@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.new_product') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'InsertProduct', 'autocomplete' => 'off')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('category')) has-error @endif">
                                            <label>{{ trans('main.category') }}</label>
                                            {{ Form::select('category', $categories, null, array('class' => 'form-control',
                                                'required')) }}
                                            @if ($errors->has('category'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('category') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('price')) has-error @endif">
                                            <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                            {{ Form::text('price', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('price') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('code')) has-error @endif">
                                            <label>{{ trans('main.code') }}</label>
                                            {{ Form::text('code', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('code'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('code') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('tax_group')) has-error @endif">
                                            <label>{{ trans('main.tax_group') }}</label>
                                            {{ Form::select('tax_group', $tax_groups, null, array('class' => 'form-control',
                                                'required')) }}
                                            @if ($errors->has('tax_group'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('tax_group') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('name')) has-error @endif">
                                            <label>{{ trans('main.name') }}</label>
                                            {{ Form::text('name', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('unit')) has-error @endif">
                                            <label>{{ trans('main.unit') }}</label>
                                            {{ Form::select('unit', $units, null, array('class' => 'form-control')) }}
                                            @if ($errors->has('unit'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('unit') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content m-t-md">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('service')) has-error @endif">
                                            <label>{{ trans('main.type') }}</label>
                                            {{ Form::select('service', array('T' => trans('main.service'),
                                                'F' => trans('main.merchandise')), null, array('class' => 'form-control')) }}
                                            @if ($errors->has('service'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('service') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.description') }}</label>
                                            {{ Form::text('description', null, array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <a href="{{ route('GetProducts') }}" class="btn btn-warning">
                                    <strong>{{ trans('main.cancel') }}</strong>
                                </a>
                                <button class="btn btn-primary"><strong>{{ trans('main.save') }}</strong></button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection