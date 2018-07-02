@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-6">
            <h2 class="page-title">{{ trans('main.notes') }}</h2>
        </div>
        <div class="col-xs-6">
            <div class="title-action">
                <a href="{{ route('AddNote') }}" class="btn btn-success">{{ trans('main.add_note') }}</a>
            </div>
        </div>
    </div>
    @if (!$notes->isEmpty())
        <div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content table-responsive">
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">{{ trans('main.text') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.edit') }}</th>
                                    <th class="text-center" data-sort-ignore="true">{{ trans('main.delete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($notes as $note)
                                    <tr>
                                        <td>{{ $note->text }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('EditNote', $note->id) }}"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="confirm-alert"
                                               data-confirm-link="{{ route('DeleteNote', $note->id) }}">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3">
                                        {{ $notes->links() }}
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