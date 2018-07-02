@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.edit_invoice') }}: <strong>{{ $invoice->invoice_id }}</strong></h2>
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
                    {{ Form::hidden('invoice_id', $invoice->id, array('id' => 'invoice-id')) }}
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
                                        {{ Form::hidden('client_id', $invoice->client_id, array('id' => 'client-id')) }}
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h2 id="selected-client-name">{{ $invoice->client->name }}</h2>
                                            </div>
                                            <div class="col-sm-6" id="selected-client-info">
                                                @if ($invoice->client_address != '')
                                                    <p>{{ $invoice->client_address }}</p>
                                                @endif
                                                @if ($invoice->client_oib != '')
                                                    <p>OIB: {{ $invoice->client_oib }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content m-t-md">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.date') }}</label>
                                        {{ Form::text('date', $invoice->invoice_date,
                                            array('class' => 'form-control invoice-date')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.due_date') }}</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            {{ Form::text('due_date', $invoice->due_date,
                                                array('class' => 'form-control due-date')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.payment_type') }}</label>
                                        {{ Form::select('payment_type', $payment_types, $invoice->payment_type_id,
                                            array('class' => 'form-control payment-type')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.note') }}</label>
                                        <p class="label-add-btn" data-toggle="modal" data-target="#notesModal">
                                            {{ trans('main.add_note') }} <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        </p>
                                        {{ Form::textarea('note', $invoice->note, array('class' => 'form-control note',
                                            'rows' => 1)) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.status') }}</label>
                                        {{ Form::select('status', array(1 => trans('main.unpaid'), 2 => trans('main.paid'),
						                    3 => trans('main.partial_paid')), $invoice->status,
                                            array('class' => 'form-control status')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3 partial-sum-div"
                                    @if (!$invoice->partial_paid_sum) {{ 'style=display:none' }} @endif>
                                    <div class="form-group">
                                        <label>{{ trans('main.paid_sum') }}</label>
                                        {{ Form::text('register', $invoice->partial_paid_sum,
                                            array('class' => 'form-control partial-paid-sum')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox collapsed m-t-md">
                            <div class="ibox-title collapse-link">
                                <h4>{{ trans('main.other_options') }}</h4>
                                <div class="ibox-tools">
                                    <a><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.delivery_date') }}</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                {{ Form::text('delivery_date', $invoice->delivery_date,
                                                    array('class' => 'form-control delivery-date')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.language') }}</label>
                                            {{ Form::select('language', $languages, $invoice->language_id,
                                                array('class' => 'form-control language')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.currency') }}</label>
                                            {{ Form::select('currency', $currencies, $invoice->currency_id,
                                                array('class' => 'form-control currency')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.input_currency') }}</label>
                                            {{ Form::select('input_currency', $currencies, $invoice->input_currency_id,
                                                array('class' => 'form-control input-currency')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.show_model') }}</label>
                                            {{ Form::select('show_model',
                                                array('F' => trans('main.no'), 'T' => trans('main.yes')), $invoice->show_model,
                                                array('class' => 'form-control show-model')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>{{ trans('main.model') }}</label>
                                                    {{ Form::text('model', $invoice->model,
                                                        array('class' => 'form-control model')) }}
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label>{{ trans('main.reference_number') }}</label>
                                                    {{ Form::text('reference_number', $invoice->reference_number,
                                                        array('class' => 'form-control reference-number')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.tax') }}</label>
                                                    {{ Form::select('tax', array('T' => trans('main.yes'), 'F' => trans('main.no')),
                                                        $invoice->tax, array('class' => 'form-control tax')) }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.advance') }}</label>
                                                    {{ Form::select('advance',
                                                        array('F' => trans('main.no'), 'T' => trans('main.yes')),
                                                        $invoice->advance, array('class' => 'form-control advance')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.int_note') }}</label>
                                            {{ Form::textarea('int_note', $invoice->int_note,
                                                array('class' => 'form-control int-note', 'rows' => 1)) }}
                                        </div>
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
                                    <div id="invoice-products">
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
                                                    <th class="text-center">{{ trans('main.rebate') }} (%)</th>
                                                    <th class="text-center">{{ trans('main.rebate_sum') }}</th>
                                                    <th class="text-center">{{ trans('main.sum') }}</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody id="products-table">
                                                @foreach ($invoice->products as $product)
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
                                                        <td class="text-center">{{ $product->rebate }}</td>
                                                        <td class="text-right">{{ $product->rebate_sum }}</td>
                                                        <td class="text-right">{{ $product->sum }}</td>
                                                        <td class="text-center">
                                                            <a href="#" class="edit-product"
                                                                data-id="{{ $product->counter }}"
                                                                data-quantity="{{ $product->quantity }}"
                                                                data-custom-price="{{ $product->custom_price }}"
                                                                data-brutto="{{ $product->brutto }}"
                                                                data-rebate="{{ $product->rebate }}"
                                                                data-note="{{ $product->note }}">
                                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" class="delete-product"
                                                                data-id="{{ $product->counter }}"
                                                                data-ip-id="{{ $product->id }}">
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
                                                    <td>{{ trans('main.sum') }}:</td><td>{{ $invoice->total }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('main.rebate_sum') }}:</td><td>{{ $invoice->rebate_sum }}</td>
                                                </tr>
                                                @foreach ($invoice->tax_array as $tax)
                                                    <tr>
                                                        <td>PDV ({{ $tax['tax'] }}):</td><td>{{ $tax['sum'] }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{ trans('main.total') }}:</strong></td>
                                                    <td><strong>{{ $invoice->grand_total }}</strong></td>
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
                    {{ Form::hidden('custom_price', null, array('class' => 'product-custom-price')) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.quantity') }}</label>
                                {{ Form::text('quantity', null, array('class' => 'form-control invoice-product-quantity')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.rebate') }} (%) <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_rebate') }}"></i></label>
                                {{ Form::text('rebate', null, array('class' => 'form-control invoice-product-rebate')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                {{ Form::text('price', null, array('class' => 'form-control invoice-product-price',
                                    'placeholder' => trans('main.enter_new_price'))) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.brutto_price') }}</label>
                                {{ Form::select('brutto', array('F' => trans('main.no'), 'T' => trans('main.yes')), 'F',
                                    array('class' => 'form-control invoice-product-brutto')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.note') }}</label>
                                {{ Form::textarea('note', null, array('class' => 'form-control invoice-product-note',
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
    @include('app.modals.notes')

    <script>
        var is_document = 'T';
        var retail = '{{ $invoice->retail }}';
        var sum_trans = '{{ trans('main.sum') }}';
        var rebate_sum_trans = '{{ trans('main.rebate_sum') }}';
        var tax_trans = '{{ trans('main.tax') }}';
        var total_trans = '{{ trans('main.total') }}';
        var delete_trans = '{{ trans('main.delete') }}';
        var no_client_error = '{{ trans('errors.no_client') }}';
        var no_product_error = '{{ trans('errors.no_product') }}';
        var dispatch = '{{ trans('main.dispatch') }}';
        var dispatch_create = '{{ trans('main.alert_create_dispatch') }}';
    </script>

    {{ HTML::script('js/functions/invoices.js?v='.date('YmdHi')) }}

    <script>
        @foreach ($invoice->products as $product)
            var this_counter = '{{ $product->counter }}';
            var this_id = '{{ $product->product_id }}';
            var this_quantity = '{{ $product->quantity }}';
            var this_price = '{{ $product->price }}';
            var this_custom_price = '{{ $product->custom_price }}';
            var this_brutto = '{{ $product->brutto }}';
            var this_rebate = '{{ $product->rebate }}';
            var this_note = '{{ $product->note }}';
            var this_ip_id = '{{ $product->id }}';

            //add product to products object
            products_object[this_counter] = {
                id: this_id,
                quantity: this_quantity,
                price: this_price,
                custom_price: this_custom_price,
                brutto: this_brutto,
                rebate: this_rebate,
                note: this_note,
                ip_id: this_ip_id
            };
        @endforeach
    </script>
@endsection