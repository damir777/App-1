@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.edit_dispatch') }}: <strong>{{ $dispatch->dispatch_id }}</strong></h2>
            </div>
            <div class="col-xs-4">
                <div class="title-action">
                    <button href="#" class="btn btn-warning btn-circle cancel" data-toggle="tooltip" data-placement="top"
                        title="{{ trans('main.cancel') }}"><i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-circle update" data-toggle="tooltip" data-placement="top"
                        title="{{ trans('main.save') }}"><i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row affix-padding">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                    {{ Form::hidden('dispatch_id', $dispatch->id, array('id' => 'dispatch-id')) }}
                    <div class="col-sm-12">
                        <div class="ibox-title">
                            <h5>{{ trans('main.client') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addClient">
                                    {{ trans('main.new_client') }}
                                </button>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.client_search') }}</label>
                                        {{ Form::text('client_search', null, array('class' => 'form-control client-search-string',
                                            'placeholder' => trans('main.search_placeholder'))) }}
                                        <div class="search-box client-search-box animated fadeInDown clients-search-box">
                                            <p class="exit-searchbox close-client-search" style="display: none">
                                                {{ trans('main.close_search') }} <i class="fa fa-close" aria-hidden="true"></i>
                                            </p>
                                            <div id="clients"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="selected-client-wrapper" id="selected-client">
                                        {{ Form::hidden('client_id', $dispatch->client_id, array('id' => 'client-id')) }}
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h2 id="selected-client-name">{{ $dispatch->client->name }}</h2>
                                            </div>
                                            <div class="col-sm-6" id="selected-client-info">
                                                @if ($dispatch->client_address != '')
                                                    <p>{{ $dispatch->client_address }}</p>
                                                @endif
                                                @if ($dispatch->client_oib != '')
                                                    <p>OIB: {{ $dispatch->client_oib }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content m-t-md">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>{{ trans('main.date') }}</label>
                                        {{ Form::text('date', $dispatch->dispatch_date,
                                            array('class' => 'form-control dispatch-date')) }}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>{{ trans('main.show_prices') }}</label>
                                        {{ Form::select('show_prices', array('T' => trans('main.yes'), 'F' => trans('main.no')),
                                            $dispatch->show_prices, array('class' => 'form-control show-prices')) }}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>{{ trans('main.note') }}</label>
                                        {{ Form::textarea('note', $dispatch->note, array('class' => 'form-control note',
                                            'rows' => 3)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-title m-t-md">
                            <h5>{{ trans('main.product') }}</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addProduct">
                                    {{ trans('main.new_product') }}
                                </button>
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
                                                {{ trans('main.close_search') }} <i class="fa fa-close" aria-hidden="true"></i>
                                            </p>
                                            <div id="products"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="dispatch-products">
                                        <div class="table-responsive m-t">
                                            <table class="table table-striped invoice-table">
                                                <thead>
                                                <tr>
                                                    <th>{{ trans('main.rb') }}</th>
                                                    <th>{{ trans('main.code') }}</th>
                                                    <th>{{ trans('main.name') }}</th>
                                                    <th>{{ trans('main.unit') }}</th>
                                                    <th class="text-center">{{ trans('main.quantity') }}</th>
                                                    <th class="text-center">{{ trans('main.price') }}</th>
                                                    <th class="text-center">{{ trans('main.tax') }} (%)</th>
                                                    <th class="text-center">{{ trans('main.sum') }}</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody id="products-table">
                                                @foreach ($dispatch->products as $product)
                                                    <tr>
                                                        <td>{{ $product->counter }}</td><td>{{ $product->product->code }}</td>
                                                        <td>
                                                            {{ $product->product->name }}
                                                            <span class="product-description text-muted">
                                                                <small>{{ $product->note }}</small>
                                                            </span>
                                                        </td>
                                                        <td>{{ $product->unit }}</td>
                                                        <td class="text-center">{{ $product->list_quantity }}</td>
                                                        <td class="text-right">{{ $product->list_price }}</td>
                                                        <td class="text-center">{{ $product->tax }}</td>
                                                        <td class="text-right">{{ $product->sum }}</td>
                                                        <td class="text-center">
                                                            <a href="#" class="edit-product" data-id="{{ $product->counter }}"
                                                                data-quantity="{{ $product->quantity }}"
                                                                data-price="{{ $product->price }}"
                                                                data-note="{{ $product->note }}">
                                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" class="delete-product"
                                                                data-id="{{ $product->counter }}"
                                                                data-dp-id="{{ $product->id }}">
                                                                <i class="fa fa-times danger" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <table class="table invoice-total">
                                                <tbody>
                                                <tr>
                                                    <td>{{ trans('main.sum') }}:</td><td>{{ $dispatch->total }}</td>
                                                </tr>
                                                @foreach ($dispatch->tax_array as $tax)
                                                    <tr>
                                                        <td>PDV ({{ $tax['tax'] }}):</td><td>{{ $tax['sum'] }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{ trans('main.total') }}:</strong></td>
                                                    <td><strong>{{ $dispatch->grand_total }}</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="editProduct" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceIn">
                <div class="modal-body">
                    {{ Form::open(array('url' => '#', 'autocomplete' => 'off')) }}
                    {{ Form::hidden('id', null, array('class' => 'product-object-id')) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.quantity') }}</label>
                                {{ Form::text('quantity', null, array('class' => 'form-control dispatch-product-quantity')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                {{ Form::text('price', null, array('class' => 'form-control dispatch-product-price')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.note') }}</label>
                                {{ Form::textarea('note', null, array('class' => 'form-control dispatch-product-note',
                                    'rows' => 1)) }}
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                    <button type="button" class="btn btn-primary update-product">{{ trans('main.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    @include('app.modals.addClient')
    @include('app.modals.addProduct')

    <script>
        var is_document = 'T';
        var sum_trans = '{{ trans('main.sum') }}';
        var tax_trans = '{{ trans('main.tax') }}';
        var total_trans = '{{ trans('main.total') }}';
        var no_client_error = '{{ trans('errors.no_client') }}';
        var no_product_error = '{{ trans('errors.no_product') }}';
    </script>

    {{ HTML::script('js/functions/dispatches.js?v='.date('YmdHi')) }}

    <script>
        @foreach ($dispatch->products as $product)
            var this_counter = '{{ $product->counter }}';
            var this_id = '{{ $product->product_id }}';
            var this_quantity = '{{ $product->quantity }}';
            var this_price = '{{ $product->price }}';
            var this_note = '{{ $product->note }}';
            var this_dp_id = '{{ $product->id }}';

            //add product to products object
            products_object[this_counter] = {
                id: this_id,
                quantity: this_quantity,
                price: this_price,
                note: this_note,
                dp_id: this_dp_id
            };
        @endforeach
    </script>
@endsection