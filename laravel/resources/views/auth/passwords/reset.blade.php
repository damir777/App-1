@extends('layouts.auth')

@section('content')
    <div class="middle-box text-center loginscreen">
        <div class="loginscreen-position">
            <div class="ibox-content m-t">
                <h3 class="auth-title m-b-lg">{{ trans('main.password_reset') }}</h3>
                {{ Form::open(['url' => '/password/reset', 'role' => 'form']) }}
                {{ Form::hidden('token', $token) }}
                <div class="form-group @if ($errors->has('email')) has-error @endif">
                    {{ Form::email('email', $email or old('email'), ['class' => 'form-control',
                        'placeholder' => trans('main.email'), 'required']) }}
                    @if ($errors->has('email'))
                        <span class="help-block text-left">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group @if ($errors->has('password')) has-error @endif">
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('main.password'),
                        'required']) }}
                    @if ($errors->has('password'))
                        <span class="help-block text-left">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                    {{ Form::password('password_confirmation', ['class' => 'form-control',
                        'placeholder' => trans('main.confirm_password'), 'required']) }}
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block text-left">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary block full-width">
                    {{ trans('main.reset_password') }}
                </button>
                {{ Form::close() }}
                <div class="row m-t-xl">
                    <div class="col-md-12 text-center">
                        <small>{{ config('app.name') }} &copy; {{ date('Y') }}. |
                            Made by <a href="http://betaware.hr" target="_blank">BetaWare</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection