@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2 class="page-title text-center">
                {{ trans('main.register_report_no') }}: {{ $report['report']->report_id }}<br><br>
                {{ trans('main.report_date_period').' '.strtolower(trans('main.from')).' '.$report['start_date'].' '.
                    strtolower(trans('main.to')).' '.$report['end_date'] }}
            </h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="footable table table-stripped toggle-arrow-tiny">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.document') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.description') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.payment_type') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.income') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.expense') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($report['items'] as $item)
                                <tr>
                                    <td>{{ $item['list_date'] }}</td>
                                    <td>{{ $item['document_id'] }}</td>
                                    <td>{{ $item['client'] }}</td>
                                    <td>{{ $item['description'] }}</td>
                                    <td>{{ $item['payment_type'] }}</td>
                                    @if ($item['payment_slip'] == 'T')
                                        <td align="right">{{ $item['sum'] }}</td><td align="right">0,00</td>
                                    @else
                                        <td align="right">0,00</td><td align="right">{{ $item['sum'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td class="report-calc-first"></td>
                                <td class="report-calc-first" align="right">{{ $report['income'] }}</td>
                                <td class="report-calc-first" align="right">{{ $report['expense'] }}</td>
                            </tr>
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td>Saldo od: {{ $report['start_date'] }}</td><td align="right">{{ $report['old_balance'] }}</td>
                                <td></td>
                            </tr>
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td>Ukupan promet blagajne: </td><td align="right">{{ $report['income'] }}</td><td></td>
                            </tr>
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td>Ukupni primitak: </td><td align="right">{{ $report['total'] }}</td><td></td>
                            </tr>
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td>{{ trans('main.expense') }}: </td><td align="right">{{ $report['expense'] }}</td><td></td>
                            </tr>
                            <tr class="report-calc"><td></td><td></td><td></td><td></td>
                                <td>Saldo od: {{ $report['end_date'] }}</td><td align="right">{{ $report['new_balance'] }}</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection