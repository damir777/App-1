@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.contracts') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddContract') }}" class="btn btn-success">{{ trans('main.add_contract') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        {{ Form::open(array('route' => 'GetContracts', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$contracts->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny invoices-list">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.contract_number') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.status') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.copy') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td>{{ $contract->contract_number }}</td>
                                        <td>{{ $contract->client->name }}</td>
                                        <td>{{ $contract->date }}</td>
                                        <td>{{ $contract->status }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('CopyContract', $contract->id) }}">
                                                <i class="fa fa-files-o"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('EditContract', $contract->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                                data-confirm-link="{{ route('DeleteContract', $contract->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="7">
                                        @if ($search_string)
                                            {{ $contracts->appends(['search_string' => $search_string]) }}
                                        @else
                                            {{ $contracts->links() }}
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