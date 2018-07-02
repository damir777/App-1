<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>xx</title>
    <style>
        * {font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;}
        p {font-size: 10px;}
        th {font-size: 10px;}
        td {font-size: 10px;}
        #logo {position: absolute; left: 380px; top: 5px; right: 0; height: 100px;}
        #company {position: absolute; left: 0; top: -40px; right: 0; height: 90px; width: 720px; font-size: 14px;}
        #company2 {position: absolute; left: 0; top: 40px; right: 0; height: 115px; width: 390px; font-size: 14px;}
        #client {position: absolute; left: 0; top: 120px; right: 0; width: 340px; border: 1px solid #000; padding:3px;}
        #bill {position: absolute; left: 410px; top: 120px; right: 0; width: 310px; font-size: 13px;}
        #content {margin-top: 310px; display: block;}
        #footer {position: absolute; bottom: 0; left: 0; padding: 5px; font-size: 8px !important; width: 100%; display: block;}
        #footer_text{border-top: 1px solid #D3D3D3;}
    </style>
</head>
<body>
<div id="logo">
    <p style="padding: 0 0 0 15px;margin: 0 0 5px 0;">
        <img style="width: 250px; height: auto" src="{{ $data['dispatch']->logo }}">
    </p>
</div>
<div id="company">
    <p>
        <span style="font-weight: bold; font-size:16px;">{{ $data['company']->name }}</span><br>
        {{ $data['company']->address.', '.$data['company']->zipcode.' '.$data['company']->city }}<br>
        {{ trans('main.phone').': '.$data['company']->phone.', '.trans('main.email').': '.$data['company']->email }}<br>
        {{ trans('main.website').': '.$data['company']->website }}
    </p>
</div>
<div id="company2">
    <p style="font-size:10px;">
        OIB: {{ $data['company']->oib }},
        {{ trans('main.tax_id').': HR'.$data['company']->oib }}
        <br>
        {{ trans('main.bank_account').': '.$data['company']->bank_account }}<br>
        {{ 'IBAN: '.$data['company']->iban.', SWIFT: '.$data['company']->swift }}
    </p>
</div>
<div id="client">
    <p style="padding: 0 0 0 10px; margin: 0; font-weight: bold; font-size: 12px;">
        {{ $data['dispatch']->client->name }}
    </p>
    <p style="padding: 4px 0 0 10px; margin: 0; font-size: 12px;">
        @if ($data['dispatch']->client->address)
            {{ $data['dispatch']->client->address }}<br/>
        @endif
        {{ $data['dispatch']->client->zip_code }}
        @if ($data['dispatch']->client->city)
            {{ $data['dispatch']->client->city }}
            <br>
        @endif
    </p>

    @if (($data['dispatch']->client->oib && $data['dispatch']->client->int_client == 'F') ||
        ($data['dispatch']->client->tax_number && $data['dispatch']->client->int_client == 'T'))
        <p style="padding: 4px 0 0 10px; margin: 0; font-size: 12px;">
            @if ($data['dispatch']->client->int_client == 'F')
                {{ 'OIB: '.$data['dispatch']->client->oib }}
            @else
                {{ trans('main.tax_number').': '.$data['dispatch']->client->tax_number }}
            @endif
        </p>
    @endif
</div>
<div id="bill">
    <p style="font-size: 16px;"><b>{{ $data['dispatch']->dispatch_no_text.' &nbsp;'.$data['dispatch']->dispatch_id }}</b></p>
    <p>
        {{ trans('main.document_date').': '.$data['dispatch']->date }}
    </p>
</div>
<div id="content">
    <table cellspacing="0" style="border: 1px solid black; width: 100%;">
        <thead>
        <tr align="left">
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.rb') }}</th>
            <th style="border-bottom: 2px solid black">{{ trans('main.code') }}</th>
            <th style="border-bottom: 2px solid black">{{ trans('main.name') }}</th>
            <th style="border-bottom: 2px solid black">{{ trans('main.pdf_unit') }}</th>
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.quantity') }}</th>
            @if ($data['dispatch']->show_prices == 'T')
                <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.price') }}</th>
                <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.tax') }} %</th>
                <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.sum') }}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($data['products'] as $product)
            <tr>
                <td style="text-align:center">{{ $product['rb'] }}</td><td>{{ $product['code'] }}</td>
                <td>{{ $product['name'] }}</td><td>{{ $product['unit'] }}</td>
                <td style="text-align:center">{{ $product['quantity'] }}</td>
                @if ($data['dispatch']->show_prices == 'T')
                    <td style="text-align:center">{{ $product['price'] }}</td>
                    <td style="text-align:center">{{ $product['tax'] }}</td>
                    <td style="text-align:right">{{ $product['sum'] }}</td>
                @endif
            </tr>
            @if ($product['note'] != '')
                <tr>
                    <td></td><td colspan="2"><small>{{ $product['note'] }}</small></td><td></td>
                    <td></td>
                    @if ($data['dispatch']->show_prices == 'T')
                        <td></td><td></td><td></td>
                    @endif
                </tr>
            @endif

            @if (count($data['products']) != $product['rb'])
                <tr>
                    @if ($data['dispatch']->show_prices == 'T')
                        <td colspan="8" style="border-bottom: 1px solid #cccccc"></td>
                    @else
                        <td colspan="5" style="border-bottom: 1px solid #cccccc"></td>
                    @endif
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    @if ($data['dispatch']->show_prices == 'T')
        <table style="page-break-inside: avoid; width: 100%; margin-top: 12px; margin-bottom: 17px">
            <tr>
                <td width="100px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 11px">
                    {{ trans('main.sum') }}:
                </td>
                <td width="100px" style="text-align:right;font-size: 11px">{{ $data['sum_array']['total'] }}</td>
            </tr>
            @if ($data['company']->pdv_user == 'T')
                @foreach ($data['tax_array'] as $tax)
                    <tr>
                        <td width="100px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 11px">
                            {{ trans('main.tax').' ('.$tax['tax'].'%)' }}:
                        </td>
                        <td width="100px" style="text-align:right;font-size: 11px">{{ $tax['sum'] }}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td width="150px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 13px">
                    <span style="font-weight: bold;">{{ trans('main.total') }} (HRK):</span>
                </td>
                <td width="100px" style="text-align:right;font-size: 13px;font-weight: bold;">
                    {{ $data['sum_array']['grand_total'] }}
                </td>
            </tr>
        </table>
    @endif

    <br>
    @if ($data['dispatch']->note)
        <p>{{ $data['dispatch']->note }}</p>
    @endif

    <table style="page-break-inside: avoid;margin-top: 40px">
        <tr>
            <td width="100px" style="padding-right: 25px;padding-left: 220px;text-align: center">______________________________</td>
            <td width="100px" style="padding-left: 90px;text-align: center">______________________________</td>
        </tr>
        <tr>
            <td width="100px" style="padding-right: 25px;padding-left: 220px;text-align: center">
                {{ trans('main.operator').' - '.$data['dispatch']->user->first_name.' '.$data['dispatch']->user->last_name }}
            </td>
            <td width="100px" style="padding-left: 90px;text-align: center">
                {{ trans('main.recipient') }}
            </td>
        </tr>
    </table>
</div><br/><br/>
<div id="footer">
    <div id="footer_text"><p>{{ $data['company']->document_footer }}</p></div>
</div>
</body>
</html>