@extends('layouts.auth')

@section('content')
    <div class="middle-box text-center loginscreen">
        <div class="loginscreen-position">
            <div class="ibox-content m-t">
                <h3 class="auth-title m-b-lg">{{ trans('main.login') }}</h3>
                {{ Form::open(['route' => 'LoginUser', 'class' => 'm-t', 'role' => 'form']) }}
                <div class="form-group">
                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('main.email'),
                        'required']) }}
                </div>
                <div class="form-group">
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('main.password'),
                        'required']) }}
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary block full-width m-b-md">{{ trans('main.login') }}</button>
                        <a href="{{ url('/password/reset') }}">
                            <small>{{ trans('main.forgotten_password') }}</small>
                        </a>
                    </div>
                    <div class="col-xs-12">
                        <p class="text-muted text-center m-t">
                            <small>{{ trans('main.do_not_have_account') }}</small>
                        </p>
                        <a class="btn btn-sm btn-white btn-block"
                           href="{{ route('RegisterPage') }}">{{ trans('main.create_account') }}
                        </a>
                    </div>
                </div>
                {{ Form::close() }}
                <div class="row m-t-xl">
                    <div class="col-md-12 text-center">
                        <small>{{ config('app.name') }} &copy; {{ date('Y') }}. |
                            Made by <a href="https://betaware.hr" target="_blank">BetaWare</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection