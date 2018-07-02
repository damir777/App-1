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
    @if ($data['payment_slip']->office_name != '')
        <br><br>{{ $data['payment_slip']->office_name }}
    @elseif ($data['payment_slip']->location != '')
        <br><br>{{ trans('main.document_location').': '.$data['payment_slip']->location }}
    @endif
</div>
<div id="header">
    {{ trans('main.payment_slip').' '.$data['payment_slip']->slip_id }}
</div>
<div id="content">
    <table cellspacing="0" cellpadding="4" style="width: 100%">
        <thead style="border: 1px solid">
        <tr align="left">
            <th>{{ trans('main.rb') }}</th>
            <th>{{ trans('main.item') }}</th>
            <th>{{ trans('main.description') }}</th>
            @if ($data['payment_slip']->invoice_id)
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
            <td>{{ $data['payment_slip']->item }}</td>
            <td>{{ $data['payment_slip']->description }}</td>
            <td>{{ $data['payment_slip']->payer_name }}</td>
            <td style="text-align: right">{{ number_format($data['payment_slip']->sum, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td></td><td></td><td></td><td></td>
            <td style="font-weight: bold; background-color: #ccc; text-align: right">
                {{ number_format($data['payment_slip']->sum, 2, ',', '.') }}
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div id="footer">
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
</body>
</html>