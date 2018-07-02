@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.offers') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddOffer') }}" class="btn btn-success">{{ trans('main.add_offer') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" id="tour-second-step">
                        {{ Form::open(array('route' => 'GetOffers', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('office', $offices, $office, array('class' => 'form-control m-r')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('year', array(0 => trans('main.choose_year'), 2015 => 2015, 2016 => 2016,
                                2017 => 2017, 2018 => 2018, 2019 => 2019, 2020 => 2020), $year,
                                array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$offers->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny offers-list">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.offer_no') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.status') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.sum') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.copy') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td>{{ $offer->offer_id }}</td>
                                        <td>
                                            <i class="fa fa-envelope send-email" aria-hidden="true"
                                               title="{{ trans('main.send_email') }}" data-id="{{ $offer->id }}"></i>
                                            {{ $offer->client->name }}
                                        </td>
                                        <td>{{ $offer->date }}</td>
                                        <td>{{ $offer->status }}</td>
                                        <td class="text-right">{{ $offer->sum }}</td>
                                        <td class="text-center">
                                            @if ($offer->int_pdf == 'T')
                                                <a href="{{ route('PDFOffer', [2, $offer->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                                <a href="{{ route('PDFOffer', [1, $offer->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('PDFOffer', [1, $offer->id]) }}" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('CopyOffer', $offer->id) }}"><i class="fa fa-files-o"></i></a>
                                        </td>
                                        @if ($offer->realized == 'F')
                                            <td class="text-center">
                                                <a href="{{ route('EditOffer', $offer->id) }}"><i class="fa fa-edit"></i></a>
                                            </td>
                                            <td class="text-center">
                                                <a href="#" class="confirm-alert"
                                                   data-confirm-link="{{ route('DeleteOffer', $offer->id) }}">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                        @else
                                            <td></td><td></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="9">
                                        @if ($search_string || $office || $year)
                                            {{ $offers->appends(['search_string' => $search_string, 'office' => $office,
                                                'year' => $year]) }}
                                        @else
                                            {{ $offers->links() }}
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

    {{ HTML::script('js/functions/offers.js?v='.date('YmdHi')) }}
@endsection