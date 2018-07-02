@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2 class="page-title">{{ trans('main.edit_client') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ Form::open(array('url' => '#', 'autocomplete' => 'off', 'class' => 'client-form')) }}
                    {{ Form::hidden('retail_client', 'F', array('class' => 'retail-client')) }}
                    {{ Form::hidden('id', $client->id, array('class' => 'client-id')) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.client_type') }}</label>
                                                    {{ Form::select('client_type', array(1 => trans('main.private'),
                                                        2 => trans('main.company')), $client->client_type,
                                                        array('class' => 'form-control client-type')) }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.int_client') }}</label>
                                                    {{ Form::select('int_client', array('F' => trans('main.no'),
                                                        'T' => trans('main.yes')), $client->int_client,
                                                        array('class' => 'form-control int-client')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.city') }}</label>
                                            {{ Form::text('city', $client->city, array('class' => 'form-control city')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.name') }}</label>
                                            {{ Form::text('name', $client->name, array('class' => 'form-control client-name')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.country') }}</label>
                                            {{ Form::select('country', $countries, $client->country,
                                                array('class' => 'form-control country')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div id="oib-div"
                                            @if ($client->int_client == 'T') {{ 'style=display:none' }} @endif>
                                            <div class="form-group">
                                                <label>{{ trans('main.oib') }} <i class="fa fa-info-circle" aria-hidden="true"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="{{ trans('main.tooltip_oib') }}"></i></label>
                                                {{ Form::text('oib', $client->oib, array('class' => 'form-control oib')) }}
                                            </div>
                                        </div>
                                        <div id="tax-number-div"
                                            @if ($client->int_client == 'F') {{ 'style=display:none' }} @endif>
                                            <div class="form-group">
                                                <label>{{ trans('main.tax_number') }}</label>
                                                {{ Form::text('tax_number', $client->tax_number,
                                                    array('class' => 'form-control tax-number')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.zip_code') }}</label>
                                            <div id="zip-code-select-div"
                                                @if ($client->country != 'HR') {{ 'style=display:none' }} @endif>
                                                {{ Form::select('zip_code_select', $zip_codes, $client->zip_code_id,
                                                    array('class' => 'form-control zip-code-select')) }}
                                            </div>
                                            <div id="zip-code-text-div"
                                                @if ($client->country == 'HR') {{ 'style=display:none' }} @endif>
                                                {{ Form::text('zip_code_text', $client->zip_code,
                                                    array('class' => 'form-control zip-code')) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.address') }}</label>
                                            {{ Form::text('address', $client->address, array('class' => 'form-control address')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content m-t-md">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.email') }}</label>
                                            {{ Form::text('email', $client->email, array('class' => 'form-control email')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.phone') }}</label>
                                            {{ Form::text('phone', $client->phone, array('class' => 'form-control phone')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ trans('main.rebate') }}</label>
                                            {{ Form::text('rebate', $client->rebate, array('class' => 'form-control rebate')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox collapsed m-t-md">
                                <div class="ibox-title collapse-link">
                                    <h4>{{ trans('main.prices') }}</h4>
                                    <div class="ibox-tools">
                                        <a><i class="fa fa-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.product_search') }}</label>
                                                {{ Form::text('product_search', null,
                                                    array('class' => 'form-control product-search-string',
                                                    'placeholder' => trans('main.search_placeholder'))) }}
                                                <div class="search-box animated fadeInDown products-search-box">
                                                    <p class="exit-searchbox close-product-search" style="display: none">
                                                        {{ trans('main.close_search') }}
                                                        <i class="fa fa-close" aria-hidden="true"></i>
                                                    </p>
                                                    <div id="products"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="table-responsive m-t">
                                                <table class="table client-prices">
                                                    <tbody>
                                                    @foreach ($client->prices as $product)
                                                        <tr>
                                                            <td class="delete-icon">
                                                                <a href="#" class="delete-button delete-client-price"
                                                                    data-id="{{ $product->product->id }}">
                                                                    <i class="fa fa-times danger" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                            <td>{{ $product->product->name.' - '.$product->price }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t">
                        <div class="col-sm-12">
                            <div class="text-center">
                                <a href="{{ route('GetClients') }}" class="btn btn-warning">
                                    <strong>{{ trans('main.cancel') }}</strong>
                                </a>
                                <button type="button" class="btn btn-primary update-client">
                                    <strong>{{ trans('main.save') }}</strong>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    @include('app.modals.clientPrice')

    <script>
        var is_document = 'F';
    </script>

    {{ HTML::script('js/functions/clients.js?v='.date('YmdHi')) }}
    {{ HTML::script('js/functions/products.js?v='.date('YmdHi')) }}
@endsection