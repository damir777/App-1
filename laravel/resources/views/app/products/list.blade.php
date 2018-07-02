@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-5">
            <h2 class="page-title">{{ trans('main.products') }}</h2>
        </div>
        <div class="col-xs-7">
            <div class="title-action">
                <a href="{{ route('AddProduct') }}" class="btn btn-success">{{ trans('main.add_product') }}</a>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content table-responsive">
                        {{ Form::open(array('route' => 'GetProducts', 'class' => 'form-inline custom-search-form',
                            'method' => 'get', 'autocomplete' => 'off')) }}
                        <div class="form-group">
                            {{ Form::text('search_string', $search_string, array('class' => 'form-control m-r',
                                'placeholder' => trans('main.search_placeholder'))) }}
                        </div>
                        <div class="form-group">
                            {{ Form::select('category', $categories, $category, array('class' => 'form-control m-r')) }}
                        </div>
                        <button class="btn btn-primary">{{ trans('main.search') }}</button>
                        {{ Form::close() }}
                    </div>
                    @if (!$products->isEmpty())
                        <div class="ibox-content m-t-md">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th>{{ trans('main.name') }}</th>
                                    <th>{{ trans('main.code') }}</th>
                                    <th>{{ trans('main.category') }}</th>
                                    <th data-sort-ignore="true">{{ trans('main.price') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->price }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('EditProduct', $product->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                               data-confirm-link="{{ route('DeleteProduct', $product->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6">
                                        @if ($search_string || $category)
                                            {{ $products->appends(['search_string' => $search_string, 'category' => $category]) }}
                                        @else
                                            {{ $products->links() }}
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