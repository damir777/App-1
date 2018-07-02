@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.subscribers') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content table-responsive">
                        <table class="footable table table-stripped toggle-arrow-tiny">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">{{ trans('main.name') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.city') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.phone') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.email') }}</th>
                                <th data-sort-ignore="true" class="text-center">{{ trans('main.licence_end') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($subscribers as $subscriber)
                                <tr>
                                    <td>{{ $subscriber->name }}</td>
                                    <td>{{ $subscriber->city }}</td>
                                    <td>{{ $subscriber->phone }}</td>
                                    <td>{{ $subscriber->invoice_email }}</td>
                                    <td class="text-center">{{ $subscriber->first_invoice }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    {{ $subscribers->links() }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection