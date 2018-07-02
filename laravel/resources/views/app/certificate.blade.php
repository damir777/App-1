@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.fiscalization') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'UpdateCertificateInfo', 'autocomplete' => 'off', 'files' => true)) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content" id="tour-thirteen-step">
                                @if ($certificate->certificate)
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="alert alert-info">
                                                Fiskalni certifikat već postoji!
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('certificate')) has-error @endif">
                                            <label>{{ trans('main.fiscal_certificate') }}</label>
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                <div class="form-control" data-trigger="fileinput">
                                                    <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                    <span class="fileinput-filename"></span>
                                                </div>
                                                <span class="input-group-addon btn btn-default btn-file">
                                                    <span class="fileinput-new">{{ trans('main.add') }}</span>
                                                    <span class="fileinput-exists">{{ trans('main.change') }}</span>
                                                    {{ Form::file('certificate') }}
                                                </span>
                                                <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                                   data-dismiss="fileinput">{{ trans('main.delete') }}
                                                </a>
                                            </div>
                                            @if ($errors->has('certificate'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('certificate') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('password')) has-error @endif">
                                            <label>{{ trans('main.password') }}</label>
                                            <div class="input-group">
                                                {{ Form::password('password', array('class' => 'form-control',
                                                    'id' => 'certificate-password', 'required')) }}
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default" id="toggle-password">Go!</button>
                                                </span>
                                            </div>
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
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
                                <button class="btn btn-primary"><strong>{{ trans('main.save') }}</strong></button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#toggle-password').on('click', function() {

            var this_button = $(this);
            var password = document.getElementById('certificate-password');

            if (password.type === 'password')
            {
                this_button.html('Sakrij');
                password.type = 'text';
            }
            else
            {
                this_button.html('Prikaži');
                password.type = 'password';
            }
        });
    </script>
@endsection