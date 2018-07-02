@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.new_employee') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'InsertEmployee', 'autocomplete' => 'off')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('first_name')) has-error @endif">
                                            <label>{{ trans('main.first_name') }}</label>
                                            {{ Form::text('first_name', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('first_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('last_name')) has-error @endif">
                                            <label>{{ trans('main.last_name') }}</label>
                                            {{ Form::text('last_name', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('last_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                                            <label>{{ trans('main.email') }}</label>
                                            {{ Form::text('email', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.phone') }}</label>
                                            {{ Form::text('phone', null, array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('job_title')) has-error @endif">
                                            <label>{{ trans('main.job_title') }}</label>
                                            {{ Form::text('job_title', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('job_title'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('job_title') }}</strong>
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
                                <a href="{{ route('GetEmployees') }}" class="btn btn-warning">
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