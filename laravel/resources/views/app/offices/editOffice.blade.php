@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.edit_office') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'UpdateOffice', 'autocomplete' => 'off')) }}
                    {{ Form::hidden('id', $office->id) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('label')) has-error @endif">
                                            <label>{{ trans('main.label') }} <i class="fa fa-info-circle" aria-hidden="true"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="{{ trans('main.tooltip_office_label') }}"></i></label>
                                            {{ Form::text('label', $office->label, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('label'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('label') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.name') }}</label>
                                            {{ Form::text('name', $office->name, array('class' => 'form-control', 'required')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.address') }}</label>
                                            {{ Form::text('address', $office->address, array('class' => 'form-control',
                                                'required')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.city') }}</label>
                                            {{ Form::text('city', $office->city, array('class' => 'form-control', 'required')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.phone') }}</label>
                                            {{ Form::text('phone', $office->phone, array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <a href="{{ route('GetOffices') }}" class="btn btn-warning">
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