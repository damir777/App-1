@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.clients') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddClient') }}" class="btn btn-success">{{ trans('main.add_client') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        {{ Form::open(array('route' => 'GetClients', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('type', array(0 => trans('main.choose_type'), 1 => trans('main.private'),
                                2 => trans('main.company')), $type, array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$clients->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th>{{ trans('main.name') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.address') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.city') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.phone') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.email') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.client_invoices') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->address }}</td>
                                        <td>{{ $client->city }}</td>
                                        <td>{{ $client->phone }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('ClientInvoices', $client->id) }}"><i class="fa fa-list-alt"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('EditClient', $client->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                               data-confirm-link="{{ route('DeleteClient', $client->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        @if ($search_string || $type)
                                            {{ $clients->appends(['search_string' => $search_string, 'type' => $type]) }}
                                        @else
                                            {{ $clients->links() }}
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