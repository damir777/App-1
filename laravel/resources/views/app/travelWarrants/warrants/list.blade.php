@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.travel_warrants') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddTravelWarrant') }}" class="btn btn-success">{{ trans('main.new_travel_warrant') }}</a>
            </div>
        </div>
    </div>
    @if (!$warrants->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{trans('main.travel_warrant_no')}}</th>
                                    <th data-sort-ignore="true">{{ trans('main.warrant_creator') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.user') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.location') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.copy') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($warrants as $warrant)
                                    <tr>
                                        <td>{{ $warrant->warrant_id }}</td>
                                        <td>{{ $warrant->creator->first_name.' '.$warrant->creator->last_name }}</td>
                                        <td>{{ $warrant->user->first_name.' '.$warrant->user->last_name }}</td>
                                        <td>{{ $warrant->location }}</td>
                                        <td>{{ $warrant->date }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFTravelWarrant', $warrant->id) }}" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('CopyTravelWarrant', $warrant->id) }}">
                                                <i class="fa fa-files-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('EditTravelWarrant', $warrant->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                                data-confirm-link="{{ route('DeleteTravelWarrant', $warrant->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="9">
                                        {{ $warrants->links() }}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection