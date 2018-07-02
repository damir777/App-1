@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.offices') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddOffice') }}" class="btn btn-success">{{ trans('main.add_office') }}</a>
            </div>
        </div>
    </div>
    @if (!$offices->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.label') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.name') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.address') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.city') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.phone') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($offices as $office)
                                    <tr>
                                        <td>{{ $office->label }}</td>
                                        <td>{{ $office->name }}</td>
                                        <td>{{ $office->address }}</td>
                                        <td>{{ $office->city }}</td>
                                        <td>{{ $office->phone }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('EditOffice', $office->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                               data-confirm-link="{{ route('DeleteOffice', $office->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="7">
                                        {{ $offices->links() }}
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