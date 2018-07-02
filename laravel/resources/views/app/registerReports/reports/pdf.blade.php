<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>xx</title>
    <style>
        * {font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;}
        p {font-size: 10px;}
        th {font-size: 10px;}
        td {font-size: 10px;}
        .company {height: 100px; font-size: 11px;}
        .slip-header {border: 1px solid; width: 100%; text-align: center; padding: 3px; font-weight: bold; font-size:15px;}
        .content {margin-top: 20px; font-size: 11px;}
        .footer {width: 100%; margin-top: 55px; margin-bottom: 20px; font-size: 11px;}
    </style>
</head>
<body>
<div class="company">
    <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
    {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}
    @if ($data['report']['report']->office)
        <br><br>{{ $data['report']['report']->office->name }}
    @endif
</div>
<div style="text-align: center">
    <h4>
        {{ trans('main.register_report_no') }}: {{ $data['report']['report']->report_id }}<br><br>
        {{ trans('main.report_date_period').' '.strtolower(trans('main.from')).' '.$data['report']['start_date'].' '.
            strtolower(trans('main.to')).' '.$data['report']['end_date'] }}
    </h4>
</div>
<div class="content">
    <table cellspacing="0" cellpadding="4" style="width: 100%">
        <thead style="border: 1px solid">
        <tr>
            <th>{{ trans('main.date') }}</th>
            <th>{{ trans('main.document') }}</th>
            <th>{{ trans('main.client') }}</th>
            <th>{{ trans('main.description') }}</th>
            <th>{{ trans('main.payment_type') }}</th>
            <th>{{ trans('main.income') }}</th>
            <th>{{ trans('main.expense') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data['report']['items'] as $item)
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
        <tr><td></td><td></td><td></td><td></td>
            <td style="background-color: #d3e2e4"></td>
            <td style="background-color: #d3e2e4" align="right">{{ $data['report']['income'] }}</td>
            <td style="background-color: #d3e2e4" align="right">{{ $data['report']['expense'] }}</td>
        </tr>
        <tr><td></td><td></td><td></td><td></td>
            <td>Saldo od: {{ $data['report']['start_date'] }}</td><td align="right">{{ $data['report']['old_balance'] }}</td>
            <td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td>
            <td>Ukupan promet blagajne: </td><td align="right">{{ $data['report']['income'] }}</td><td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td>
            <td>Ukupni primitak: </td><td align="right">{{ $data['report']['total'] }}</td><td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td>
            <td>{{ trans('main.expense') }}: </td><td align="right">{{ $data['report']['expense'] }}</td><td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td>
            <td>Saldo od: {{ $data['report']['end_date'] }}</td><td align="right">{{ $data['report']['new_balance'] }}</td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="footer">
    <table cellspacing="0" style="width: 100%">
        <tr>
            <td style="text-align: center;">Blagajnik</td>
            <td style="text-align: center;">Kontrolirao</td>
            <td style="text-align: center;">Likvidator</td>
        </tr>
        <tr>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
        </tr>
    </table>
</div>
@if ($data['show_items'])
    @foreach ($data['report']['payment_slips'] as $slip)
        <div style="page-break-after: always;"></div>
        <div class="company">
            <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
            {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}
            @if ($slip->office_name != '')
                <br><br>{{ $slip->office_name }}
            @elseif ($slip->location != '')
                <br><br>{{ trans('main.document_location').': '.$slip->location }}
            @endif
        </div>
        <div class="slip-header">
            {{ trans('main.payment_slip').' '.$slip->slip_id }}
        </div>
        <div class="content">
            <table cellspacing="0" cellpadding="4" style="width: 100%">
                <thead style="border: 1px solid">
                <tr align="left">
                    <th>{{ trans('main.rb') }}</th>
                    <th>{{ trans('main.item') }}</th>
                    <th>{{ trans('main.description') }}</th>
                    @if ($slip->invoice_id)
                        <th>{{ trans('main.client') }}</th>
                    @else
                        <th>{{ trans('main.payer') }}</th>
                    @endif
                    <th>{{ trans('main.sum') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $slip->item }}</td>
                    <td>{{ $slip->description }}</td>
                    <td>{{ $slip->payer_name }}</td>
                    <td style="text-align: right">{{ number_format($slip->sum, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td><td></td>
                    <td style="font-weight: bold; background-color: #ccc; text-align: right">
                        {{ number_format($slip->sum, 2, ',', '.') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <table cellspacing="0" style="width: 100%">
                <tr>
                    <td style="text-align: center;">Likvidator</td>
                    <td style="text-align: center;">Blagajnik</td>
                    <td style="text-align: center;">Uplatitelj</td>
                </tr>
                <tr>
                    <td style="text-align: center;">____________________</td>
                    <td style="text-align: center;">____________________</td>
                    <td style="text-align: center;">____________________</td>
                </tr>
            </table>
        </div>
    @endforeach
    @foreach ($data['report']['payout_slips'] as $slip)
        <div style="page-break-after: always;"></div>
        <div class="company">
            <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
            {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}
            @if ($slip->office_name != '')
                <br><br>{{ $slip->office_name }}
            @elseif ($slip->location != '')
                <br><br>{{ trans('main.document_location').': '.$slip->location }}
            @endif
        </div>
        <div class="slip-header">
            {{ trans('main.payout_slip').' '.$slip->slip_id }}
        </div>
        <div class="content">
            @if ($slip->note != '')
                <div style="margin-bottom: 22px">{{ $slip->note }}</div>
            @endif
            @if (count($slip->items) > 0)
                <table cellspacing="0" cellpadding="4" style="width: 100%">
                    <thead style="border: 1px solid">
                    <tr align="left">
                        <th>{{ trans('main.rb') }}</th>
                        <th>{{ trans('main.item') }}</th>
                        <th>{{ trans('main.description') }}</th>
                        @if ($slip->income == 'T')
                            <th>Utržak</th>
                        @else
                            <th>{{ trans('main.employee') }}</th>
                        @endif
                        <th>{{ trans('main.sum') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($slip->items as $item)
                        <tr>
                            <td>{{ $item['rb'] }}</td>
                            <td>{{ $item['item'] }}</td>
                            <td>{{ $item['description'] }}</td>
                            @if ($slip->income == 'T')
                                <td>{{ trans('main.payout_slip_income') }}</td>
                            @else
                                <td>{{ $slip->employee_name }}</td>
                            @endif
                            <td style="text-align: right">{{ number_format($item['sum'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td><td></td><td></td>
                            <td style="font-weight: bold; background-color: #ccc; text-align: right">
                                {{ number_format($slip->items_sum, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="footer">
            <table cellspacing="0" style="width: 100%">
                <tr>
                    <td style="text-align: center;">Likvidator</td>
                    <td style="text-align: center;">Blagajnik</td>
                    <td style="text-align: center;">Primatelj</td>
                </tr>
                <tr>
                    <td style="text-align: center;">____________________</td>
                    <td style="text-align: center;">____________________</td>
                    <td style="text-align: center;">____________________</td>
                </tr>
            </table>
        </div>
    @endforeach
@endif
</body>
</html>