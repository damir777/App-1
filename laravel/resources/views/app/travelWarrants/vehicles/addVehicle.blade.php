@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.new_vehicle') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'InsertVehicle', 'autocomplete' => 'off')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('vehicle_type')) has-error @endif">
                                            <label>{{ trans('main.vehicle_type') }}</label>
                                            {{ Form::text('vehicle_type', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('vehicle_type'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('vehicle_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('year')) has-error @endif">
                                            <label>{{ trans('main.year') }}</label>
                                            {{ Form::text('year', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('year'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('year') }}</strong>
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
                                        <div class="form-group @if ($errors->has('km')) has-error @endif">
                                            <label>{{ trans('main.warrant_km') }}</label>
                                            {{ Form::text('km', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('km'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('km') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('register_number')) has-error @endif">
                                            <label>{{ trans('main.register_number') }}</label>
                                            {{ Form::text('register_number', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('register_number'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('register_number') }}</strong>
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
                                <a href="{{ route('GetVehicles') }}" class="btn btn-warning">
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