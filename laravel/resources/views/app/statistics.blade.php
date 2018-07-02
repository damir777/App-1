@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.statistics') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox-content" id="tour-one-step">
                    <div class="row">
                        <div class="col-sm-3" id="tour-fifteen-step">
                            <div class="widget style1 lazur-bg">
                                <div class="text-center">
                                    <span>Broj izdanih ponuda</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['offer_counter'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 lazur-bg">
                                <div class="text-center">
                                    <span>Broj realiziranih ponuda</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['realized_offer_counter'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 navy-bg">
                                <div class="text-center">
                                    <span>Broj izdanih računa</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['invoice_counter'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 navy-bg">
                                <div class="text-center">
                                    <span>Broj plaćenih računa</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['paid_invoice_counter'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content m-t-md" id="tour-fourteen-step">
                    <div class="row">
                        <div class="col-sm-10">
                            <div><canvas id="lineChart" height="114"></canvas></div>
                        </div>
                        <div class="col-sm-2">
                            <ul class="stat-list m-t-lg">
                                <li>
                                    <h2 class="no-margins">{{ $statistics['paid_invoice_sum'] }}</h2>
                                    <small>Ukupno plaćeni</small>
                                </li>
                                <li>
                                    <h2 class="no-margins ">{{ $statistics['unpaid_invoice_sum'] }}</h2>
                                    <small>Ukupno neplaćeni</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row m-t-md">
                        <div class="col-sm-12">
                            <small>
                                <strong>Statistika se odnosi na {{ date('Y.') }} godinu</strong>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="ibox m-t-md">
                    <div class="ibox-title">
                        <h4>{{ trans('main.unpaid').' '.lcfirst(trans('main.invoices')) }}</h4>
                    </div>
                    <div class="ibox-content table-responsive">
                        <table class="footable table table-stripped toggle-arrow-tiny invoices-list">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">{{ trans('main.invoice_no') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.due_date') }}</th>
                                <th data-sort-ignore="true" class="text-center">{{ trans('main.sum') }}</th>
                                <th class="text-center" data-sort-ignore="true">PDF</th>
                                <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($statistics['unpaid_invoices'] as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>
                                        <i class="fa fa-envelope send-email" aria-hidden="true"
                                           title="{{ trans('main.send_email') }}" data-id="{{ $invoice->id }}"></i>
                                        {{ $invoice->client->name }}
                                    </td>
                                    <td>{{ $invoice->date }}</td>
                                    <td>{{ $invoice->due_date }}</td>
                                    <td class="text-right">{{ $invoice->sum }}</td>
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
                                    <td class="text-center">
                                        <a href="{{ route('EditInvoice', $invoice->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7">
                                    {{ $statistics['unpaid_invoices']->links() }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var labels = [];
        var offers_data = [];
        var invoices_data = [];
        var paid_invoices_data = [];

        @foreach ($statistics['labels'] as $label)
            labels.push('{{ $label }}');
        @endforeach

        @foreach ($statistics['offers'] as $offer)
            offers_data.push('{{ $offer }}');
        @endforeach

        @foreach ($statistics['invoices'] as $invoice)
            invoices_data.push('{{ $invoice }}');
        @endforeach

        @foreach ($statistics['paid_invoices'] as $invoice)
            paid_invoices_data.push('{{ $invoice }}');
        @endforeach

        var lineData = {
            labels: labels,
            datasets: [
                {
                    label: "Ponude",
                    backgroundColor: "rgba(26,179,148,0.5)",
                    borderColor: "rgba(26,179,148,0.7)",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#fff",
                    data: offers_data
                },
                {
                    label: "Računi",
                    backgroundColor: "rgba(220,220,220,0.5)",
                    borderColor: "rgba(220,220,220,1)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    pointBorderColor: "#fff",
                    data: invoices_data
                },
                {
                    label: "Plaćeni računi",
                    backgroundColor: "rgba(181,184,207,0.5)",
                    borderColor: "rgba(181,184,207,1)",
                    pointBackgroundColor: "rgba(181,184,207,1)",
                    pointBorderColor: "#fff",
                    data: paid_invoices_data
                }
            ]
        };

        var lineOptions = {
            responsive: true
        };

        var ctx = document.getElementById("lineChart").getContext("2d");
        new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});
    </script>
@endsection