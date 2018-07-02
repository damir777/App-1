@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.order_forms') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddOrderForm') }}" class="btn btn-success">{{ trans('main.add_order_form') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        {{ Form::open(array('route' => 'GetOrderForms', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$order_forms->isEmpty())
                        <div class="ibox-content m-t-md table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.dispatch_no') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.client') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.date') }}</th>
                                    <th data-sort-ignore="true" class="text-center">{{ trans('main.sum') }}</th>
                                    <th class="text-center" data-sort-ignore="true">PDF</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order_forms as $order_form)
                                    <tr>
                                        <td>{{ $order_form->order_form_id }}</td>
                                        <td>{{ $order_form->client->name }}</td>
                                        <td>{{ $order_form->date }}</td>
                                        <td class="text-right">{{ $order_form->sum }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('PDFOrderForm', $order_form->id) }}" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('EditOrderForm', $order_form->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                               data-confirm-link="{{ route('DeleteOrderForm', $order_form->id) }}">
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
                                            {{ $order_forms->appends(['search_string' => $search_string]) }}
                                        @else
                                            {{ $order_forms->links() }}
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