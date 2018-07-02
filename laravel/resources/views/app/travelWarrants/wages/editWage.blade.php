@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.edit_wage') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'UpdateWage', 'autocomplete' => 'off')) }}
                    {{ Form::hidden('id', $wage->id) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('name')) has-error @endif">
                                            <label>{{ trans('main.name') }}</label>
                                            {{ Form::text('name', $wage->name, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('country')) has-error @endif">
                                            <label>{{ trans('main.country') }}</label>
                                            {{ Form::select('country', $countries, $wage->country,
                                                array('class' => 'form-control')) }}
                                            @if ($errors->has('country'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('country') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('price')) has-error @endif">
                                            <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                            {{ Form::text('price', $wage->price, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('price') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <a href="{{ route('GetWages') }}" class="btn btn-warning">
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