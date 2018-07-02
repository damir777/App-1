@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.edit_user') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'UpdateUser', 'autocomplete' => 'off')) }}
                    {{ Form::hidden('id', $user->id) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('first_name')) has-error @endif">
                                            <label>{{ trans('main.first_name') }}</label>
                                            {{ Form::text('first_name', $user->first_name, array('class' => 'form-control',
                                                'required')) }}
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
                                            {{ Form::text('last_name', $user->last_name, array('class' => 'form-control',
                                                'required')) }}
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
                                            {{ Form::text('email', $user->email, array('class' => 'form-control', 'required')) }}
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
                                            {{ Form::text('phone', $user->phone, array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('password')) has-error @endif">
                                            <label>{{ trans('main.password') }}</label>
                                            {{ Form::password('password', ['class' => 'form-control']) }}
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                                            <label>{{ trans('main.confirm_password') }}</label>
                                            {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content m-t-md">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('office')) has-error @endif">
                                            <label>{{ trans('main.office') }}</label>
                                            {{ Form::select('office', $offices, $user->office_id,
                                                array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('office'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('office') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('register')) has-error @endif">
                                            <label>{{ trans('main.register_device') }}</label>
                                            {{ Form::select('register', $registers, $user->register_id,
                                                array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('register'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('register') }}</strong>
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
                                <a href="{{ route('GetUsers') }}" class="btn btn-warning">
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