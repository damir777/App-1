@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.vehicles') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddVehicle') }}" class="btn btn-success">{{ trans('main.add_vehicle') }}</a>
            </div>
        </div>
    </div>
    @if (!$vehicles->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{trans('main.vehicle_type')}}</th>
                                    <th data-sort-ignore="true">{{ trans('main.name') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.register_number') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.warrant_km') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->vehicle_type }}</td>
                                        <td>{{ $vehicle->name }}</td>
                                        <td>{{ $vehicle->register_number }}</td>
                                        <td>{{ $vehicle->km }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('EditVehicle', $vehicle->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                                data-confirm-link="{{ route('DeleteVehicle', $vehicle->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6">
                                        {{ $vehicles->links() }}
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