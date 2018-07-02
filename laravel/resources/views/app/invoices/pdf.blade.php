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
        #company_adhoc {position: absolute; left: 0; top: -40px; right: 0; height: 90px; width: 720px; font-size: 14px;}
        #company2 {position: absolute; left: 0; top: 40px; right: 0; height: 115px; width: 390px; font-size: 14px;}
        #company2_adhoc {position: absolute; left: 0; top: 95px; right: 0; height: 115px; width: 390px; font-size: 14px;}
        #client {position: absolute; left: 0; top: 120px; right: 0; width: 340px; border: 1px solid #000; padding:3px;}
        #client_adhoc {position: absolute; left: 0; top: 160px; right: 0; width: 340px; border: 1px solid #000; padding:3px;}
        #bill {position: absolute; left: 410px; top: 120px; right: 0; width: 310px; font-size: 13px;}
        #content {margin-top: 310px; display: block;}
        #logo2 {position: absolute; bottom: 190px; left: 0;}
        #footer {position: absolute; bottom: 0; left: 0; padding: 5px; font-size: 8px !important; width: 100%; display: block;}
        #footer_text{border-top: 1px solid #D3D3D3;}
    </style>
</head>
<body>
<div id="logo">
    <p style="padding: 0 0 0 15px; margin: 0 0 5px 0;">
        <img style="width: 250px; height: auto" src="{{ $data['invoice']->logo }}">
    </p>
</div>
@if ($data['invoice']->company_id == 65 && ($data['invoice']->retail == 'F' || ($data['invoice']->retail == 'T' &&
    $data['invoice']->office_id == 48)))
    <div id="company_adhoc">
        <p>
            <span style="font-weight: bold; font-size:16px;">{{ $data['company']->name }}</span><br>
            {{ $data['company']->address.', '.$data['company']->zipcode.' '.$data['company']->city }}<br>
            {{ trans('main.phone').': '.$data['company']->phone.', '.trans('main.email').': '.$data['company']->email }}<br />
            {{ trans('main.website').': '.$data['company']->website }}
        </p>
        <p style="font-size:10px;">
            Poslovnica Osijek, Stjepana Radića 12, 31000 Osijek<br>
            {{ trans('main.phone').': 031/215-500, '.trans('main.email').': adhoc@adhoc.hr' }}<br>
            {{ trans('main.website').': www.adhoc.hr' }}
        </p>
    </div>
    <div id="company2_adhoc">
        <p style="font-size:10px;">
            OIB: {{ $data['company']->oib }},
            {{ trans('main.tax_id').': HR'.$data['company']->oib }}
            <br>
            {{ trans('main.bank_account').': '.$data['company']->bank_account }}<br>
            {{ 'IBAN: '.$data['company']->iban.', SWIFT: '.$data['company']->swift }}
        </p>
    </div>
@else
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
@endif

@if ($data['invoice']->client)
    <div id="@if ($data['invoice']->company_id == 65) client_adhoc @else client @endif">
        <p style="padding: 0 0 0 10px; margin: 0; font-weight: bold; font-size: 12px;">
            {{ $data['invoice']->client->name }}
        </p>
        <p style="padding: 4px 0 0 10px; margin: 0; font-size: 12px;">
            @if ($data['invoice']->client->address)
                {{ $data['invoice']->client->address }}<br/>
            @endif
            {{ $data['invoice']->client->zip_code }}
            @if ($data['invoice']->client->city)
                {{ $data['invoice']->client->city }}
                <br>
            @endif
        </p>

        @if (($data['invoice']->client->oib && $data['invoice']->client->int_client == 'F') ||
            ($data['invoice']->client->tax_number && $data['invoice']->client->int_client == 'T'))
            <p style="padding: 4px 0 0 10px; margin: 0; font-size: 12px;">
                @if ($data['invoice']->client->int_client == 'F')
                    {{ 'OIB: '.$data['invoice']->client->oib }}
                @else
                    {{ trans('main.tax_number').': '.$data['invoice']->client->tax_number }}
                @endif
            </p>
        @endif
    </div>
@endif

<div id="bill">
    <p style="font-size: 16px;"><b>{{ $data['invoice']->invoice_no_text.' &nbsp;'.$data['invoice']->invoice_id }}</b></p>

    @if ($data['invoice']->reversed == 'T')
        <p style="font-size: 13px;">{{ trans('main.reversed_invoice_no').': &nbsp;'.$data['invoice']->reversed_id }}</p>
    @endif

    <p>
        {{ trans('main.document_date').': '.$data['invoice']->date }}<br>
        @if ($data['invoice']->retail == 'F')
            {{ trans('main.due_date').': '.$data['invoice']->due_date }}<br>
        @endif
        @if ($data['invoice']->delivery_date)
            {{ trans('main.delivery_date').': '.$data['invoice']->delivery_date }}<br>
        @endif
        {{ trans('main.document_location').': '.$data['invoice']->office->city }}<br>
        {{ trans('main.payment_type').': '.trans('main.'.$data['invoice']->paymentType->code) }}
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
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.price') }}</th>
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.tax') }} %</th>
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.rebate') }} %</th>
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.rebate_sum') }}</th>
            <th style="border-bottom: 2px solid black; text-align:center">{{ trans('main.sum') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data['products'] as $product)
            <tr>
                <td style="text-align:center">{{ $product['rb'] }}</td><td>{{ $product['code'] }}</td>
                <td>{{ $product['name'] }}</td><td>{{ $product['unit'] }}</td>
                <td style="text-align:center">{{ $product['quantity'] }}</td>
                <td style="text-align:center">{{ $product['price'] }}</td>
                <td style="text-align:center">{{ $product['tax'] }}</td>
                <td style="text-align:center">{{ $product['rebate'] }}</td>
                <td style="text-align:center">{{ $product['rebate_sum'] }}</td>
                <td style="text-align:right">{{ $product['sum'] }}</td>
            </tr>
            @if ($product['note'] != '')
                <tr>
                    <td></td><td colspan="2"><small>{{ $product['note'] }}</small></td><td></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endif

            @if (count($data['products']) != $product['rb'])
                <tr><td colspan="10" style="border-bottom: 1px solid #cccccc"></td></tr>
            @endif
        @endforeach
        </tbody>
    </table>

    <table style="page-break-inside: avoid; width: 100%; margin-top: 12px; margin-bottom: 17px">
        <tr>
            <td width="100px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 11px">
                {{ trans('main.sum') }}:
            </td>
            <td width="100px" style="text-align:right;font-size: 11px">{{ $data['sum_array']['total'] }}</td>
        </tr>
        <tr>
            <td width="100px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 11px">
                {{ trans('main.rebate_sum') }}:
            </td>
            <td width="100px" style="text-align:right;font-size: 11px">{{ $data['sum_array']['rebate_sum'] }}</td>
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
        <!-- type 2 - different currencies -->
        @if ($data['type'] == 2 && ($data['invoice']->currency_id != 1))
            <tr>
                <td width="150px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 13px">
                    <span style="font-weight: bold;">{{ trans('main.total') }} (HRK):</span>
                </td>
                <td width="100px" style="text-align:right;font-size: 13px;font-weight: bold;">
                    {{ $data['sum_array']['dom_grand_total'] }}
                </td>
            </tr>
            <tr>
                <td width="150px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 13px">
                    <span style="font-weight: bold;">{{ trans('main.total').' ('.$data['invoice']->currency->code.')' }}:</span>
                </td>
                <td width="100px" style="text-align:right;font-size: 13px;font-weight: bold;">
                    {{ $data['sum_array']['grand_total'] }}
                </td>
            </tr>
        <!-- type 1 - different currencies -->
        @elseif ($data['type'] == 1 && ($data['invoice']->currency_id != 1))
            <tr>
                <td width="150px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 13px">
                    <span style="font-weight: bold;">{{ trans('main.total').' ('.$data['invoice']->currency->code.')' }}:</span>
                </td>
                <td width="100px" style="text-align:right;font-size: 13px;font-weight: bold;">
                    {{ $data['sum_array']['grand_total'] }}
                </td>
            </tr>
        @else
            <tr>
                <td width="150px" style="text-align:right;padding-right: 15px;padding-left: 420px;font-size: 13px">
                    <span style="font-weight: bold;">{{ trans('main.total') }} (HRK):</span>
                </td>
                <td width="100px" style="text-align:right;font-size: 13px;font-weight: bold;">
                    {{ $data['sum_array']['grand_total'] }}
                </td>
            </tr>
        @endif
    </table>

    @if ($data['invoice']->retail == 'F' && $data['invoice']->show_model == 'T' && $data['invoice']->model != '')
        <p>{{ trans('main.model').': '.$data['invoice']->model.', '.trans('main.reference_number').': '.
            $data['invoice']->reference_number }}
        </p>
    @endif

    @if ($data['invoice']->contract_id && $data['invoice']->create_after_end == 'F')
        @if ($data['invoice']->current_contract_invoice <= $data['invoice']->number_of_invoices)
            <p>{{ $data['invoice']->current_contract_invoice.'/'.$data['invoice']->number_of_invoices }}</p>
        @endif
    @endif
    <!--
    @if ($data['invoice']->client && $data['invoice']->client->int_client == 'T' && $data['invoice']->retail == 'F')
        @if ($data['type'] == 1 && $data['invoice']->language_id != 1)
            <p>VAT does not apply by the article 17 point 1. VAT law</p>
        @else
            <p>Ne podliježe obračunu PDV-a prema Čl.17. st. 1. Zakona o PDV-u</p>
        @endif
    @endif
    -->
    @if ($data['company']->pdv_user == 'F')
        <p>Obveznik nije u sustavu PDV-a, PDV nije obračunat temeljem čl. 90. stavka 2. Zakona o PDV-u.</p>
    @else
        @foreach ($data['tax_notes_array'] as $note)
            <p>{{ $note }}</p>
        @endforeach
    @endif

    <!-- type 2 -->
    @if ($data['type'] == 2 || ($data['type'] == 1 && $data['invoice']->language_id == 1))
        @if ($data['invoice']->note)
            <p>{{ $data['invoice']->note }}</p>
        @endif

        @foreach ($data['invoice']->notes as $note)
            <p>{{ $note->note }}</p>
        @endforeach

        @if ($data['company']->payment_terms)
            <p><strong>{{ trans('main.payment_terms') }}: </strong>{{ $data['company']->payment_terms }}</p>
        @endif

        @if ($data['company']->general_terms)
            <p><strong>{{ trans('main.general_terms') }}: </strong>{{ $data['company']->general_terms }}</p>
        @endif
    <!-- type 1 - different languages -->
    @else
        @if ($data['invoice']->int_note)
            <p>{{ $data['invoice']->int_note }}</p>
        @endif
    @endif

    @if ($data['invoice']->zki)
        <p>ZKI: {{ $data['invoice']->zki }}</p>
    @endif

    @if ($data['invoice']->jir)
       <p>JIR: {{ $data['invoice']->jir }}</p>
    @endif

    @if ($data['company']->logo2)
        <!--<div id="logo2"><img src="{{ $data['invoice']->logo2_url }}"></div>-->
    @endif

    <br>
    <table style="page-break-inside: avoid;">
        @if ($data['invoice']->retail == 'F')
            <tr>
                <td width="100px" style="padding-right: 25px;padding-left: 220px;">
                    {{ trans('main.for').' '.$data['company']->name }}:
                </td>
                <td width="100px" style="padding-left: 90px">
                    {{ trans('main.operator').' - '.trans('main.reviewer') }}
                </td>
            </tr>
            <tr>
                @if ($data['company']->legal_form == 1)
                    <td width="100px" style="padding-right: 25px;padding-left: 220px;">
                        {{ trans('main.director').': '.$data['admin'] }}
                    </td>
                @else
                    <td width="100px" style="padding-right: 25px;padding-left: 220px;">
                        {{ trans('main.owner').': '.$data['admin'] }}
                    </td>
                @endif

                <td width="100px" style="padding-left: 90px">
                    {{ $data['invoice']->user->first_name.' '.$data['invoice']->user->last_name }}
                </td>
            </tr>
        @else
            <tr>
                <td width="100px" style="padding-left: 463px">
                    {{ trans('main.operator').' - '.trans('main.reviewer') }}
                </td>
            </tr>
            <tr>
                <td width="100px" style="padding-left: 463px">
                    {{ $data['invoice']->user->first_name.' '.$data['invoice']->user->last_name }}
                </td>
            </tr>
        @endif
    </table>
</div><br/><br/>
<div id="footer">
    <div id="footer_text"><p>{{ $data['company']->document_footer }}</p></div>
</div>
</body>
</html>