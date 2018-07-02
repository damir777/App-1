@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.new_register') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('route' => 'InsertRegister', 'autocomplete' => 'off')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content" id="tour-five-step">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('label')) has-error @endif">
                                            <label>{{ trans('main.label') }}</label>
                                            {{ Form::text('label', null, array('class' => 'form-control', 'required')) }}
                                            @if ($errors->has('label'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('label') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group @if ($errors->has('office')) has-error @endif">
                                            <label>{{ trans('main.office') }}</label>
                                            {{ Form::select('office', $offices, null, array('class' => 'form-control',
                                                'required')) }}
                                            @if ($errors->has('office'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('office') }}</strong>
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
                                <a href="{{ route('GetRegisters') }}" class="btn btn-warning">
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