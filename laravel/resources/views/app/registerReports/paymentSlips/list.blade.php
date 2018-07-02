@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.payment_slips') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddPaymentSlip') }}" class="btn btn-success">{{ trans('main.add_payment_slip') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        {{ Form::open(array('route' => 'GetPaymentSlips', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::select('office', $offices, $office, array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$slips->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.payment_slip_no') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.item') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.description') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.print') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($slips as $slip)
                                    <tr>
                                        <td>{{ $slip->slip_id }}</td>
                                        <td>{{ $slip->date }}</td>
                                        <td>{{ $slip->item }}</td>
                                        <td>{{ $slip->description }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFPaymentSlip', $slip->id) }}" target="_blank">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFPaymentSlip', $slip->id) }}" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            @if (!$slip->invoice_id)
                                                <a href="#" class="confirm-alert"
                                                   data-confirm-link="{{ route('DeletePaymentSlip', $slip->id) }}">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="7">
                                        @if ($office)
                                            {{ $slips->appends(['office' => $office]) }}
                                        @else
                                            {{ $slips->links() }}
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
@endsection