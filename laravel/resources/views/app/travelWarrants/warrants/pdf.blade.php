<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>xx</title>
    <style>
        * {font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;}
        p {font-size: 10px;}
        th {font-size: 10px;}
        td {font-size: 10px;}
        .company {height: 90px; font-size: 11px;}
        .warrant-date {height: 40px; text-align: right; font-size: 11px;}
        .warrant-header {border: 1px solid; margin-bottom: 20px; text-align: center; padding: 3px; font-weight: bold;
            font-size:15px;}
        #warrant-content {font-size: 11px; height: 240px;}
        #warrant-notes {height: 100px; font-size: 11px;}
        #warrant-creator {height: 320px; font-size: 11px; margin-right: 20px; text-align: right;}
        #warrant-content-bill {font-size: 11px; height: 50px;}
        #wages {font-size: 10px;}
        #directions { font-size: 10px;}
        #costs {font-size: 10px;}
        #total-costs {margin-left: 320px; margin-top: 10px; border: 1px solid;}
        #footer1 {margin-top: 35px; font-size: 10px;}
        #footer2 {margin-top: 45px; margin-bottom: 20px; font-size: 10px;}
        #report {font-size: 10px;}
    </style>
</head>
<body>
<div class="company">
    <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
    {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}<br>
    OIB: {{ $data['company']->oib }}
</div>
<div class="warrant-date">{{ $data['company']->city.', '.$data['warrant']['warrant']->start_date }}</div>
<div class="warrant-header">{{ trans('main.travel_warrant').' '.$data['warrant']['warrant']->warrant_id }}</div>
<div id="warrant-content">
    <p>
        {{ trans('main.user').': '.$data['warrant']['warrant']->user->first_name.' '.$data['warrant']['warrant']->user->last_name.
        ', '.$data['warrant']['warrant']->user->job_title }}
    </p>
    <p>{{ trans('main.date').': '.$data['warrant']['warrant']->start_date }}</p>
    <p>{{ trans('main.location').': '.$data['warrant']['warrant']->location }}</p>
    <p>{{ trans('main.purpose').': '.$data['warrant']['warrant']->purpose }}</p>
    <p>{{ trans('main.duration').': '.$data['warrant']['warrant']->duration }}</p>
    <p>
        {{ trans('main.vehicle').': '.$data['warrant']['warrant']->vehicle->vehicle_type.' - '.
        $data['warrant']['warrant']->vehicle->name.' ('.$data['warrant']['warrant']->vehicle->register_number.')' }}
    </p>
    <p>{{ trans('main.start_mileage').': '.$data['warrant']['warrant']->start_mileage }} km</p>
    <p>{{ trans('main.end_mileage').': '.$data['warrant']['warrant']->end_mileage }} km</p>
</div>
<div id="warrant-notes">{{ trans('main.note').': '.$data['warrant']['warrant']->note }}</div>
<div id="warrant-creator">
    {{ $data['warrant']['warrant']->creator->first_name.' '.$data['warrant']['warrant']->creator->last_name }}
    <br><br><br><br><br><br><br><span style="font-size: 9px">({{ strtolower(trans('main.warrant_creator')) }})</span>
</div>
<div style="page-break-after: always;"></div>
<div class="company">
    <span style="font-weight: bold; font-size:18px;">{{ $data['company']->name }}</span><br>
    {{ $data['company']->address }}<br>{{ $data['company']->zip_code.' '.$data['company']->city }}<br>
    OIB: {{ $data['company']->oib }}
</div>
<div class="warrant-date">{{ $data['company']->city.', '.$data['warrant']['warrant']->start_date }}</div>
<div class="warrant-header">{{ trans('main.travel_bill').' '.$data['warrant']['warrant']->warrant_id }}</div>
<div id="warrant-content-bill">
    <p>{{ trans('main.user').': '.$data['warrant']['warrant']->user->first_name.' '.$data['warrant']['warrant']->user->last_name.
        ', '.$data['warrant']['warrant']->user->job_title }}</p>
    <p>{{ trans('main.duration').' '.$data['warrant']['warrant']->start_date.' - '.$data['warrant']['warrant']->end_date }}</p>
</div>
@if (count($data['warrant']['wages']) > 0)
    <div id="wages">
        <h5>{{ strtoupper(trans('main.wages')) }}</h5>
        <table cellspacing="0" cellpadding="3" style="width: 100%">
            <thead style="border: 1px solid">
            <tr align="left">
                <th>{{ trans('main.rb') }}</th>
                <th>{{ trans('main.date') }}</th>
                <th>{{ trans('main.country') }}</th>
                <th>{{ trans('main.departure') }}</th>
                <th>{{ trans('main.arrival') }}</th>
                <th>{{ trans('main.hours') }}</th>
                <th>{{ trans('main.wage') }}</th>
                <th>{{ trans('main.wage_price') }}</th>
                <th>{{ trans('main.sum') }}</th>
            </tr>
            </thead>
            <tbody>

            <?php $i = 1; ?>

            @foreach ($data['warrant']['wages'] as $wage)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $wage->date }}</td>
                    <td>{{ $wage->wageCountry->name }}</td>
                    <td>{{ $wage->departure_date.' '.$wage->departure_time }}</td>
                    <td>{{ $wage->arrival_date.' '.$wage->arrival_time }}</td>
                    <td>{{ number_format($wage->hours, 1, ',', '') }}</td>
                    <td>{{ $wage->wage }}</td>
                    <td style="text-align: right">{{ $wage->wageWage->price }}</td>
                    <td style="text-align: right">{{ number_format($wage->wage * $wage->wageWage->price, 2, ',', '.') }}</td>
                </tr>

                <?php $i++; ?>
            @endforeach

            <tr>
                <td></td><td colspan="2">{{ trans('main.non_costs') }}</td><td></td>
                <td></td><td></td><td></td><td></td>
                <td style="font-weight: bold; text-align: right">
                    {{ number_format($data['warrant']['warrant']->non_costs, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td></td><td style="font-weight: bold">{{ trans('main.total') }}</td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
                <td style="font-weight: bold; text-align: right">
                    {{ number_format($data['warrant']['wage_sum'] - $data['warrant']['warrant']->non_costs, 2, ',', '.') }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endif
@if (count($data['warrant']['directions']) > 0)
    <div id="directions">
        <h5>{{ strtoupper(trans('main.directions')) }}</h5>
        <table cellspacing="0" cellpadding="3" style="width: 100%">
            <thead style="border: 1px solid">
            <tr align="left">
                <th>{{ trans('main.rb') }}</th>
                <th>{{ trans('main.date') }}</th>
                <th>{{ trans('main.transport_type') }}</th>
                <th>{{ trans('main.from') }}</th>
                <th>{{ trans('main.to') }}</th>
                <th>{{ trans('main.kilometers') }}</th>
                <th>{{ trans('main.km_price') }}</th>
                <th>{{ trans('main.sum') }}</th>
            </tr>
            </thead>
            <tbody>

            <?php $i = 1; ?>

            @foreach ($data['warrant']['directions'] as $direction)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $direction->date }}</td>
                    <td>{{ $direction->transport_type }}</td>
                    <td>{{ $direction->start_location }}</td>
                    <td>{{ $direction->end_location }}</td>
                    <td>{{ $direction->distance }}</td>
                    <td>{{ $direction->km_price }}</td>
                    <td style="text-align: right">{{ number_format($direction->distance * $direction->km_price, 2, ',', '.') }}</td>
                </tr>

                <?php $i++; ?>
            @endforeach

            <tr>
                <td></td><td style="font-weight: bold">{{ trans('main.total') }}</td><td></td><td></td>
                <td></td><td></td><td></td>
                <td style="font-weight: bold; text-align: right">
                    {{ number_format($data['warrant']['direction_sum'], 2, ',', '.') }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endif
@if (count($data['warrant']['costs']) > 0)
    <div id="costs">
        <h5>{{ strtoupper(trans('main.costs')) }}</h5>
        <table cellspacing="0" cellpadding="3" style="width: 100%">
            <thead style="border: 1px solid">
            <tr align="left">
                <th>{{ trans('main.rb') }}</th>
                <th>{{ trans('main.date') }}</th>
                <th>{{ trans('main.cost_type') }}</th>
                <th>{{ trans('main.description') }}</th>
                <th>{{ trans('main.sum') }}</th>
                <th>{{ trans('main.non_costs') }}</th>
                <th>{{ trans('main.sum') }}</th>
            </tr>
            </thead>
            <tbody>

            <?php $i = 1; ?>

            @foreach ($data['warrant']['costs'] as $cost)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $cost->date }}</td>
                    <td>{{ $cost->cost_type }}</td>
                    <td>{{ $cost->description }}</td>
                    <td>{{ $cost->sum }}</td>
                    <td>{{ $cost->non_costs }}</td>
                    <td style="text-align: right">{{ number_format($cost->sum - $cost->non_costs, 2, ',', '.') }}</td>
                </tr>

                <?php $i++; ?>
            @endforeach

            <tr>
                <td></td><td style="font-weight: bold">{{ trans('main.total') }}</td><td></td><td></td>
                <td></td><td></td>
                <td style="font-weight: bold; text-align: right">{{ number_format($data['warrant']['cost_sum'], 2, ',', '.') }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endif
<div id="total-costs">
    <table cellspacing="0" cellpadding="2" style="width: 100%">
        <tr>
            <td>Ukupni troškovi:</td>
            <td style="text-align: right;">
                {{ number_format($data['warrant']['wage_sum'] + $data['warrant']['direction_sum'] +
                $data['warrant']['cost_sum'] - $data['warrant']['warrant']->non_costs, 2, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Ukupni predujam:</td>
            <td style="text-align: right;">{{ number_format($data['warrant']['warrant']->advance, 2, ',', '.') }}</td>
        </tr>
        <tr><td>&nbsp;</td><td></td></tr>
        <tr>
            <td style="font-weight: bold">Isplatiti/vratiti:</td>
            <td style="text-align: right;">
                {{ number_format($data['warrant']['wage_sum'] + $data['warrant']['direction_sum'] +
                $data['warrant']['cost_sum'] - $data['warrant']['warrant']->non_costs - $data['warrant']['warrant']->advance, 2,
                ',', '.') }}
            </td>
        </tr>
    </table>
</div>
<div id="footer1">
    <table cellspacing="0" style="width: 100%">
        <tr>
            <td>{{ $data['company']->city.', '.$data['warrant']['warrant']->report_date }}</td>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
        </tr>
        <tr><td></td>
            <td style="text-align: center;">{{ $data['warrant']['warrant']->user->first_name.' '.
                $data['warrant']['warrant']->user->last_name }}</td>
            <td style="text-align: center;">{{ $data['warrant']['warrant']->creator->first_name.' '.
                $data['warrant']['warrant']->creator->last_name }}</td>
        </tr>
    </table>
</div>
<div id="footer2">
    <table cellspacing="0" style="width: 100%">
        <tr>
            <td style="text-align: center;">Podnositelj računa</td>
            <td style="text-align: center;">Isplatio blagajnik</td>
            <td style="text-align: center;">Pregledao likvidator</td>
            <td style="text-align: center;">Isplatiti nalogodavac blagajni</td>
        </tr>
        <tr>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
            <td style="text-align: center;">____________________</td>
        </tr>
    </table>
</div>
<div id="report"><span style="font-weight: bold;">{{ trans('main.report') }}:</span><br>
    {{ $data['warrant']['warrant']->report }}
</div>
</body>
</html>