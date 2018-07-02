@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.company_info') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        {{ Form::open(array('route' => 'UpdateCompanyInfo', 'autocomplete' => 'off')) }}
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                                <label>{{ trans('main.name') }}</label>
                                {{ Form::text('name', $company->name, array('class' => 'form-control')) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('oib')) has-error @endif">
                                <label>{{ trans('main.oib') }}</label>
                                {{ Form::text('oib', $company->oib, array('class' => 'form-control')) }}
                                @if ($errors->has('oib'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('oib') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('address')) has-error @endif">
                                <label>{{ trans('main.address') }}</label>
                                {{ Form::text('address', $company->address, array('class' => 'form-control')) }}
                                @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('phone')) has-error @endif">
                                <label>{{ trans('main.phone') }}</label>
                                {{ Form::text('phone', $company->phone, array('class' => 'form-control')) }}
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('city')) has-error @endif">
                                <label>{{ trans('main.city') }}</label>
                                {{ Form::text('city', $company->city, array('class' => 'form-control')) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                                <label>{{ trans('main.email') }}</label>
                                {{ Form::text('email', $company->email, array('class' => 'form-control')) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('zip_code')) has-error @endif">
                                <label>{{ trans('main.zip_code') }}</label>
                                {{ Form::text('zip_code', $company->zip_code, array('class' => 'form-control')) }}
                                @if ($errors->has('zip_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('zip_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.website') }}</label>
                                {{ Form::text('website', $company->website, array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content m-t-md">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('legal_form')) has-error @endif">
                                <label>{{ trans('main.legal_form') }}</label>
                                {{ Form::select('legal_form', array(1 => trans('main.company'), 2 => trans('main.craft')),
                                    $company->legal_form, array('class' => 'form-control')) }}
                                @if ($errors->has('legal_form'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('legal_form') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('pdv_user')) has-error @endif">
                                <label>{{ trans('main.pdv_user') }}</label>
                                {{ Form::select('pdv_user', array('T' => trans('main.yes'), 'F' => trans('main.no')),
                                    $company->pdv_user, array('class' => 'form-control')) }}
                                @if ($errors->has('pdv_user'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pdv_user') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('bank_account')) has-error @endif">
                                <label>{{ trans('main.bank_account') }}</label>
                                {{ Form::text('bank_account', $company->bank_account, array('class' => 'form-control')) }}
                                @if ($errors->has('bank_account'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bank_account') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('iban')) has-error @endif">
                                <label>IBAN</label>
                                {{ Form::text('iban', $company->iban, array('class' => 'form-control')) }}
                                @if ($errors->has('iban'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('iban') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>SWIFT</label>
                                {{ Form::text('swift', $company->swift, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group @if ($errors->has('sljednost_prostor')) has-error @endif">
                                <label>{{ trans('main.sljednost') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_sljednost') }}"></i></label>
                                {{ Form::select('sljednost_prostor', array('T' => 'Na nivou poslovnog prostora',
                                    'F' => 'Na nivou naplatnog uređaja'), $company->sljednost_prostor,
                                    array('class' => 'form-control')) }}
                                @if ($errors->has('sljednost_prostor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sljednost_prostor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group @if ($errors->has('document_footer')) has-error @endif">
                                <label>{{ trans('main.document_footer') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_document_footer') }}"></i></label>
                                {{ Form::textarea('document_footer', $company->document_footer, array('class' => 'form-control',
                                    'rows' => 3)) }}
                                @if ($errors->has('document_footer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('document_footer') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox m-t-md">
                    <div class="ibox-title">
                        <h4>{{ trans('main.logo') }}</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            {{ Form::file('logo', array('class' => 'form-control logo-input')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="cropper" style="display:none;">
                                            <div id="croppie"></div>
                                        </div>
                                        <div class="crop-button-div" style="display: none">
                                            <div class="col-sm-12">
                                                <button type="button" class="btn btn-success btn-md pull-right crop">
                                                    {{ trans('main.save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div id="logo-div">
                                        @if ($company->logo)
                                            {{ HTML::image('logo/'.$company->logo) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox collapsed m-t-md">
                    <div class="ibox-title collapse-link">
                        <h4>Uvjeti</h4>
                        <div class="ibox-tools">
                            <a><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ trans('main.general_terms') }}</label>
                                    {{ Form::textarea('general_terms', $company->general_terms, array('class' => 'form-control',
                                        'rows' => 3)) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ trans('main.payment_terms') }}</label>
                                    {{ Form::textarea('payment_terms', $company->payment_terms, array('class' => 'form-control',
                                        'rows' => 3)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="text-center">
                    <button class="btn btn-primary"><strong>{{ trans('main.save') }}</strong></button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <script>
        var logo_upload = '{{ trans('main.logo_upload') }}';
    </script>

    {{ HTML::style('css/croppie.css') }}
    {{ HTML::script('js/croppie.min.js') }}
    {{ HTML::script('js/functions/logo.js?v='.date('YmdHi')) }}
@endsection