@extends('layouts.auth')

@section('content')
    <div class="container">
        <div class="text-center loginscreen">
            <div class="loginscreen-position">
                <div class="ibox-content m-t">
                    <h3 class="auth-title m-b-lg">{{ trans('main.register') }}</h3>
                    <div class="row">
                        <div class="col-xs-6 register-form">
                            {{ Form::open(['route' => 'RegisterUser', 'role' => 'form']) }}
                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                                {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('main.email'),
                                    'required']) }}
                                @if ($errors->has('email'))
                                    <span class="help-block text-left">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                                        {{ Form::password('password', ['class' => 'form-control',
                                            'placeholder' => trans('main.password'), 'required']) }}
                                        @if ($errors->has('password'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                                        {{ Form::password('password_confirmation', ['class' => 'form-control',
                                            'placeholder' => trans('main.confirm_password'), 'required']) }}
                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group @if ($errors->has('first_name')) has-error @endif">
                                        {{ Form::text('first_name', null, ['class' => 'form-control',
                                            'placeholder' => trans('main.first_name'), 'required']) }}
                                        @if ($errors->has('first_name'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('first_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group @if ($errors->has('last_name')) has-error @endif">
                                        {{ Form::text('last_name', null, ['class' => 'form-control',
                                            'placeholder' => trans('main.last_name'), 'required']) }}
                                        @if ($errors->has('last_name'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('company_name')) has-error @endif">
                                {{ Form::text('company_name', null, ['class' => 'form-control',
                                    'placeholder' => trans('main.company_name'), 'required']) }}
                                @if ($errors->has('company_name'))
                                    <span class="help-block text-left">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group @if ($errors->has('phone')) has-error @endif">
                                        {{ Form::text('phone', null, ['class' => 'form-control',
                                            'placeholder' => trans('main.phone'), 'required']) }}
                                        @if ($errors->has('phone'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::text('website', null, ['class' => 'form-control',
                                            'placeholder' => trans('main.website')]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group @if ($errors->has('g-recaptcha-response')) has-error @endif">
                                        {!! NoCaptcha::renderJs('hr') !!}
                                        {!! NoCaptcha::display() !!}
                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="help-block text-left">
                                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-md m-b-md">
                                <div class="col-sm-12">
                                    <div>
                                        <label><input type="checkbox" name="terms_checkbox" class="i-checks terms-checkbox">
                                            Registracijom prihvaćam
                                        </label>
                                        <a href="https://xx.com/uvjeti.html" target="_blank">Uvjete i odredbe</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-sm-offset-3 col-xs-12">
                                    <button type="submit" class="btn btn-primary block m-b register-button" disabled>
                                        {{ trans('main.register') }}
                                    </button>
                                    <p class="text-muted text-center m-t-md">
                                        <small>{{ trans('main.already_have_account') }}</small>
                                    </p>
                                </div>
                                <div class="col-sm-6 col-sm-offset-3 col-xs-12">
                                    <a class="btn btn-sm btn-white btn-block"
                                       href="{{ route('LoginPage') }}">{{ trans('main.login') }}
                                    </a>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                        <div class="col-xs-6 text-left register-text">
                            {{ HTML::image('css/img/logo-dark.svg') }}
                            <p>Za korištenje xx potrebno je ispuniti registracijski obrazac te nakon toga aplikaciju možete
                                besplatno koristiti 30 dana. Nakon registracije ćete na email dobiti link za potvrdu Vašeg
                                računa za xx.
                            </p>
                            <br>
                            <p>Cijena xx je 50,00 kn mjesečno (PDV uključen) po korisniku.</p>
                            <br>
                            <p>Za xx nije potrebna nikakva dodatna instalacija. Dovoljan je pristup internetu i moderan
                                browser (preglednik).
                            </p>
                            <br>
                        </div>
                    </div>
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
    </div>
@endsection