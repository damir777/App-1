@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.reports') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="#" class="btn btn-success" data-toggle="modal" data-target="#addRegisterReport">
                    {{ trans('main.add_register_report') }}
                </a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        {{ Form::open(array('route' => 'GetRegisterReports', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::select('office', $offices, $office, array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        @if ($office)
                            <span class="m-l" style="vertical-align: middle">
                                <b>{{ trans('main.current_register_sum') }}: {{ $register_sum }} HRK</b>
                            </span>
                        @endif
                        {{ Form::close() }}
                    </div>
                    @if (!$reports->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.register_report_no') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date_from') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date_to') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.preview') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.print') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>{{ $report->report_id }}</td>
                                        <td>{{ $report->start_date }}</td>
                                        <td>{{ $report->end_date }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('PreviewRegisterReport', $report->id) }}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="register-report-pdf" data-id="{{ $report->id }}">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFRegisterReport', $report->id) }}" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="register-report-delete"
                                                data-delete-message="{{ $report->report_message }}"
                                                data-confirm-link="{{ route('DeleteRegisterReport', $report->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="7">
                                        @if ($office)
                                            {{ $reports->appends(['office' => $office]) }}
                                        @else
                                            {{ $reports->links() }}
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

    <div class="modal inmodal" id="addRegisterReport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceIn">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('main.new_register_report') }}</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('url' => '#', 'autocomplete' => 'off')) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.date_from') }}</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    {{ Form::text('date_from', null, array('class' => 'form-control start-date')) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.date_to') }}</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    {{ Form::text('date_to', null, array('class' => 'form-control end-date')) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ trans('main.office') }}</label>
                                {{ Form::select('office', $report_offices, $office, array('class' => 'form-control office')) }}
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                    <button type="button" class="btn btn-primary create">{{ trans('main.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var alert_delete_report = '{{ trans('main.alert_delete_report') }}';
        var alert_pdf_report_title = '{{ trans('main.print') }}';
        var alert_pdf_report = '{{ trans('main.alert_pdf_report') }}';
    </script>

    {{ HTML::script('js/functions/registerReports.js?v='.date('YmdHi')) }}
@endsection