<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>xx</title>
    <style>
        * {font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;}
        p {font-size: 10px;}
        th {font-size: 10px;}
        td {font-size: 10px;}
        #company {height: 100px; font-size: 11px;}
        #header {border: 1px solid; width: 100%; text-align: center; padding: 3px; font-weight: bold; font-size:15px;}
        #content {margin-top: 20px; font-size: 11px;}
        #footer {width: 100%; margin-top: 55px; margin-bottom: 20px; font-size: 11px;}
    </style>
</head>
<body>
<div id="company">
    <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
    {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}
    @if ($data['payout_slip']->office_name != '')
        <br><br>{{ $data['payout_slip']->office_name }}
    @elseif ($data['payout_slip']->location != '')
        <br><br>{{ trans('main.document_location').': '.$data['payout_slip']->location }}
    @endif
</div>
<div id="header">
    {{ trans('main.payout_slip').' '.$data['payout_slip']->slip_id }}
</div>
<div id="content">
    @if ($data['payout_slip']->note != '')
        <div style="margin-bottom: 22px">{{ $data['payout_slip']->note }}</div>
    @endif
    @if (count($data['items']) > 0)
        <table cellspacing="0" cellpadding="4" style="width: 100%">
            <thead style="border: 1px solid">
            <tr align="left">
                <th>{{ trans('main.rb') }}</th>
                <th>{{ trans('main.item') }}</th>
                <th>{{ trans('main.description') }}</th>
                @if ($data['payout_slip']->income == 'T')
                    <th>Utržak</th>
                @else
                    <th>{{ trans('main.employee') }}</th>
                @endif
                <th>{{ trans('main.sum') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data['items'] as $item)
                <tr>
                    <td>{{ $item['rb'] }}</td>
                    <td>{{ $item['item'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    @if ($data['payout_slip']->income == 'T')
                        <td>{{ trans('main.payout_slip_income') }}</td>
                    @else
                        <td>{{ $data['payout_slip']->employee_name }}</td>
                    @endif
                    <td style="text-align: right">{{ number_format($item['sum'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td><td></td>
                    <td style="font-weight: bold; background-color: #ccc; text-align: right">
                        {{ number_format($data['items_sum'], 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
<div id="footer">
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
</body>
</html>