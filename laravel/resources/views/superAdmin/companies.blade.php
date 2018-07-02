@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.companies') }}</h2>
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
                                <th class="text-center" data-sort-ignore="true" class="text-center">{{ trans('main.active') }}</th>
                                <th class="text-center" data-sort-ignore="true">{{ trans('main.licence_end') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->city }}</td>
                                    <td>{{ $company->phone }}</td>
                                    <td class="text-center">{{ $company->active }}</td>
                                    <td class="text-center licence-data" style="cursor: pointer" data-toggle="modal"
                                        data-target="#updateLicence" data-company-id="{{ $company->id }}"
                                        data-licence-end="{{ $company->licence_end }}">
                                        {{ $company->licence_end }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    {{ $companies->links() }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('app.modals.updateLicence')
@endsection