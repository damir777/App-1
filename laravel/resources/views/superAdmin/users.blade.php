@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.users') }}</h2>
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
                                <th data-sort-ignore="true">{{ trans('main.first_name') }}</th>
                                <th data-sort-ignore="true">{{ trans('main.company') }}</th>
                                <th class="text-center" data-sort-ignore="true">{{ trans('main.login') }}</th>
                                <th class="text-center" data-sort-ignore="true">
                                    {{ trans('main.activate') }} / {{ trans('main.deactivate') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name.' '.$user->last_name }}</td>
                                    <td>{{ $user->company }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('SuperAdminLoginAsUser', $user->id) }}">{{ trans('main.login') }}</a>
                                    </td>
                                    <td class="text-center">
                                        @if ($user->active == 'T')
                                            <a href="{{ route('DeactivateUser', $user->id) }}">{{ trans('main.deactivate') }}</a>
                                        @else
                                            <a href="{{ route('ActivateUser', $user->id) }}">{{ trans('main.activate') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4">
                                    {{ $users->links() }}
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