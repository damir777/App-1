@extends('layouts.main')

@section('content')
    <div class="page-heading-wrapper" data-spy="affix" data-offset-top="60">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-xs-8">
                <h2 class="page-title">{{ trans('main.edit_contract') }}</h2>
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
                    {{ Form::hidden('contract_id', $contract->id, array('id' => 'contract-id')) }}
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
                                        {{ Form::hidden('client_id', $contract->client_id, array('id' => 'client-id')) }}
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h2 id="selected-client-name">{{ $contract->client->name }}</h2>
                                            </div>
                                            <div class="col-sm-6" id="selected-client-info">
                                                @if ($contract->client_address != '')
                                                    <p>{{ $contract->client_address }}</p>
                                                @endif
                                                @if ($contract->client_oib != '')
                                                    <p>OIB: {{ $contract->client_oib }}</p>
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
                                        <label>{{ trans('main.contract_number') }}</label>
                                        {{ Form::text('contract_number', $contract->contract_number,
                                            array('class' => 'form-control contract-number')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.due_days') }}</label>
                                        {{ Form::text('due_days', $contract->due_days, array('class' => 'form-control due-days')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.contract_duration') }} <i class="fa fa-info-circle" aria-hidden="true"
                                            data-toggle="tooltip" data-placement="top" title=""
                                            data-original-title="{{ trans('main.tooltip_contract_duration') }}"></i></label>
                                        {{ Form::text('number_of_invoices', $contract->number_of_invoices,
                                            array('class' => 'form-control number-of-invoices')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.active') }}</label>
                                        {{ Form::select('active', array('T' => trans('main.yes'), 'F' => trans('main.no')),
                                            $contract->active, array('class' => 'form-control is-active')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.office') }}</label>
                                        {{ Form::select('office', $offices, $contract->office_id,
                                            array('class' => 'form-control office')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.register_device') }}</label>
                                        {{ Form::select('register', $registers, $contract->register_id,
                                            array('class' => 'form-control register')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.payment_mode') }}</label>
                                        {{ Form::select('previous_month_create', array('T' => trans('main.previous_month'),
                                            'F' => trans('main.current_month')), $contract->previous_month_create,
                                            array('class' => 'form-control previous-month-create')) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>{{ trans('main.note') }}</label>
                                        {{ Form::textarea('note', $contract->note,
                                            array('class' => 'form-control note', 'rows' => 1)) }}
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
                                            <label>{{ trans('main.create_day') }}</label>
                                            {{ Form::select('create_day', array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6,
                                                7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15),
                                                $contract->create_day, array('class' => 'form-control create-day')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.create_after_end') }}</label>
                                            {{ Form::select('create_after_end', array('T' => trans('main.yes'),
                                                'F' => trans('main.no')), $contract->create_after_end,
                                                array('class' => 'form-control create-after-end')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.language') }}</label>
                                            {{ Form::select('language', $languages, $contract->language_id,
                                                array('class' => 'form-control language')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.email_sending') }}</label>
                                            {{ Form::select('email_sending', array('T' => trans('main.yes'),
                                                'F' => trans('main.no')), $contract->email_sending,
                                                array('class' => 'form-control email-sending')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.currency') }}</label>
                                            {{ Form::select('currency', $currencies, $contract->currency_id,
                                                array('class' => 'form-control currency')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.input_currency') }}</label>
                                            {{ Form::select('input_currency', $currencies, $contract->input_currency_id,
                                                array('class' => 'form-control input-currency')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.tax') }}</label>
                                            {{ Form::select('tax',
                                                array('T' => trans('main.yes'), 'F' => trans('main.no')), $contract->tax,
                                                array('class' => 'form-control tax')) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.payment_type') }}</label>
                                            {{ Form::select('payment_type', $payment_types, $contract->payment_type_id,
                                                array('class' => 'form-control payment-type')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>{{ trans('main.int_note') }}</label>
                                            {{ Form::textarea('int_note', $contract->int_note,
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
                                    <div id="contract-products">
                                        <div class="table-responsive m-t">
                                            <table class="table table-striped contract-table">
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
                                                @foreach ($contract->products as $product)
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
                                                                data-cp-id="{{ $product->id }}">
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
                                                    <td>{{ trans('main.sum') }}:</td><td>{{ $contract->total }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('main.rebate_sum') }}:</td><td>{{ $contract->rebate_sum }}</td>
                                                </tr>
                                                @foreach ($contract->tax_array as $tax)
                                                    <tr>
                                                        <td>PDV ({{ $tax['tax'] }}):</td><td>{{ $tax['sum'] }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{ trans('main.total') }}:</strong></td>
                                                    <td><strong>{{ $contract->grand_total }}</strong></td>
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
                                {{ Form::text('quantity', null, array('class' => 'form-control contract-product-quantity')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.rebate') }} (%) <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_rebate') }}"></i></label>
                                {{ Form::text('rebate', null, array('class' => 'form-control contract-product-rebate')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true"
                                    data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                                {{ Form::text('price', null, array('class' => 'form-control contract-product-price',
                                    'placeholder' => trans('main.enter_new_price'))) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.brutto_price') }}</label>
                                {{ Form::select('brutto', array('F' => trans('main.no'), 'T' => trans('main.yes')), 'F',
                                    array('class' => 'form-control contract-product-brutto')) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.note') }}</label>
                                {{ Form::textarea('note', null, array('class' => 'form-control contract-product-note',
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
        var rebate_sum_trans = '{{ trans('main.rebate_sum') }}';
        var tax_trans = '{{ trans('main.tax') }}';
        var total_trans = '{{ trans('main.total') }}';
        var delete_trans = '{{ trans('main.delete') }}';
        var no_client_error = '{{ trans('errors.no_client') }}';
        var no_product_error = '{{ trans('errors.no_product') }}';
    </script>

    {{ HTML::script('js/functions/contracts.js?v='.date('YmdHi')) }}

    <script>
        @foreach ($contract->products as $product)
            var this_counter = '{{ $product->counter }}';
            var this_id = '{{ $product->product_id }}';
            var this_quantity = '{{ $product->quantity }}';
            var this_price = '{{ $product->price }}';
            var this_custom_price = '{{ $product->custom_price }}';
            var this_brutto = '{{ $product->brutto }}';
            var this_rebate = '{{ $product->rebate }}';
            var this_note = '{{ $product->note }}';
            var this_cp_id = '{{ $product->id }}';

            //add product to products object
            products_object[this_counter] = {
                id: this_id,
                quantity: this_quantity,
                price: this_price,
                custom_price: this_custom_price,
                brutto: this_brutto,
                rebate: this_rebate,
                note: this_note,
                cp_id: this_cp_id
            };
        @endforeach
    </script>
@endsection