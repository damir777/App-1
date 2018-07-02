@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2 class="page-title text-center">
                {{ trans('main.client_invoices').': '.$invoices['client'] }}
            </h2>
        </div>
    </div>
    @if (!$invoices['invoices']->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.invoice_no') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.sum') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.paid') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.due_date') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($invoices['invoices'] as $invoice)
                                    <tr>
                                        <td>{{ $invoice->date }}</td>
                                        <td>{{ $invoice->invoice_id }}</td>
                                        <td align="right">{{ $invoice->sum }}</td>
                                        <td align="right">{{ $invoice->paid_sum }}</td>
                                        <td align="center">{{ $invoice->due_date }}</td>
                                    </tr>
                                @endforeach
                                <tr class="report-calc">
                                    <td></td>
                                    <td class="report-calc-first">{{ trans('main.total') }}</td>
                                    <td class="report-calc-first" align="right">{{ $invoices['sum'] }}</td>
                                    <td class="report-calc-first" align="right">{{ $invoices['paid_sum'] }}</td>
                                    <td class="report-calc-first"></td>
                                </tr>
                                <tr class="report-calc">
                                    <td></td>
                                    <td class="report-calc-second">{{ trans('main.debit') }}</td>
                                    <td align="right" class="report-calc-second">{{ $invoices['debit'] }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection