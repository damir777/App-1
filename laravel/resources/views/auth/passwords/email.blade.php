@extends('layouts.auth')

@section('content')
    <div class="middle-box text-center loginscreen">
        <div class="loginscreen-position">
            <div class="ibox-content m-t">
                <h3 class="auth-title m-b-lg">{{ trans('main.password_reset') }}</h3>
                {{ Form::open(['url' => '/password/email', 'role' => 'form']) }}
                <div class="form-group @if ($errors->has('email')) has-error @endif">
                    {{ Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('main.email'),
                        'required']) }}
                    @if ($errors->has('email'))
                        <span class="help-block text-left">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary block full-width m-b-md">
                            {{ trans('main.send_password_reset_link') }}
                        </button>
                        <p class="text-muted text-center m-t">
                            <small>{{ trans('main.known_password') }}</small>
                        </p>
                    </div>
                    <div class="col-xs-12">
                        <a class="btn btn-sm btn-white btn-block"
                           href="{{ route('LoginPage') }}">{{ trans('main.login') }}
                        </a>
                    </div>
                </div>
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