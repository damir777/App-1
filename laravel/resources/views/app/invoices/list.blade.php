@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.invoices') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddInvoice', $type) }}" class="btn btn-success">{{ trans('main.add_invoice') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" id="tour-eleven-step">
                        {{ Form::open(array('route' => array('GetInvoices', $type), 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('office', $offices, $office, array('class' => 'form-control m-r')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('register', $registers, $register, array('class' => 'form-control m-r')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('year', array(0 => trans('main.choose_year'), 2015 => 2015, 2016 => 2016,
                                2017 => 2017, 2018 => 2018, 2019 => 2019, 2020 => 2020), $year,
                                array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$invoices->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny invoices-list">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.invoice_no') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.status') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.sum') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.print') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    @if ($type == 2)
                                        <th class="text-center" data-sort-ignore="true">{{ trans('main.copy') }}</th>
                                    @endif
                                    @if ($type == 2)
                                        <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    @endif
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.reverse') }}</th>
                                    @if ($type == 2)
                                        <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        @if ($invoice->retail == 'T' && $invoice->jir == '')
                                            <td>
                                                {{ $invoice->invoice_id }} <i class="fa fa-credit-card fiscalization"
                                                    data-id="{{ $invoice['id'] }}"
                                                    title="{{ trans('main.make_fiscalization') }}"></i>
                                            </td>
                                        @else
                                            <td>{{ $invoice->invoice_id }}</td>
                                        @endif
                                        @if ($invoice->client)
                                            <td>
                                                <i class="fa fa-envelope send-email" aria-hidden="true"
                                                   title="{{ trans('main.send_email') }}" data-id="{{ $invoice->id }}"></i>
                                                {{ $invoice->client->name }}
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>{{ $invoice->date }}</td>
                                        <td>{{ $invoice->status }}</td>
                                        <td class="text-right">{{ $invoice->sum }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFInvoice', [1, $invoice->id]) }}" target="_blank">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            @if ($invoice->int_pdf == 'T')
                                                <a href="{{ route('PDFInvoice', [2, $invoice->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                                <a href="{{ route('PDFInvoice', [1, $invoice->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('PDFInvoice', [1, $invoice->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @endif
                                        </td>
                                        @if ($type == 2)
                                            @if ($invoice->reversed == 'F')
                                                <td class="text-center">
                                                    <a href="{{ route('CopyInvoice', $invoice->id) }}">
                                                        <i class="fa fa-files-o"></i></a>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endif
                                        @if ($type == 2)
                                            @if (!$invoice->reversed_id && ($invoice->paid == 'F' || $invoice->partial_paid_sum))
                                                <td class="text-center">
                                                    <a href="{{ route('EditInvoice', $invoice->id) }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endif
                                        @if (!$invoice->reversed_id)
                                            <td class="text-center">
                                                <a href="#" class="confirm-reverse"
                                                    data-reverse-link="{{ route('ReverseInvoice', array($type, $invoice->id)) }}">
                                                    <i class="fa fa-minus-square-o"></i>
                                                </a>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if ($type == 2)
                                            @if ($invoice->paid == 'F' && !$invoice->reversed_id)
                                                <td class="text-center">
                                                    <a href="#" class="confirm-alert"
                                                       data-confirm-link="{{ route('DeleteInvoice', $invoice->id) }}">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td @if ($type == 2) {{ 'colspan=11' }} @else {{ 'colspan=8' }} @endif>
                                        @if ($search_string || $office || $register || $year)
                                            {{ $invoices->appends(['search_string' => $search_string, 'office' => $office,
                                                'register' => $register, 'year' => $year]) }}
                                        @else
                                            {{ $invoices->links() }}
                                        @endif
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        var reversing = '{{ trans('main.reversing') }}';
        var invoice_reverse = '{{ trans('main.alert_reverse_invoice') }}';

        @if (session('print'))
        window.open(ajax_url + 'docs/invoices/pdf/1/' + '{{ session('print') }}', '_blank');
        @endif
    </script>

    {{ HTML::script('js/functions/invoices.js?v='.date('YmdHi')) }}
@endsection