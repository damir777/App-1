@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.users') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddUser') }}" class="btn btn-success">{{ trans('main.add_user') }}</a>
            </div>
        </div>
    </div>
    @if (!$users->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.first_name') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.last_name') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.email') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.phone') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('EditUser', $user->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            @if ($user->role->role_id == 3)
                                                <a href="#" class="confirm-alert"
                                                    data-confirm-link="{{ route('DeleteUser', $user->id) }}">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6">
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
    @endif
@endsection