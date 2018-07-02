@extends('layouts.main')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        @if ($active == 'T')
                                            <div class="alert alert-info">Vaša licenca uskoro ističe.</div>
                                            <h3 class="text-center">xx možete koristiti još {{ $days_remaining }}.</h3>
                                        @else
                                            <div class="alert alert-danger">Vaša licenca je istekla!</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-center m-t">
                                        <p>Da biste i dalje koristili xx kontaktirajte nas.</p>
                                        <h5>
                                            {{ trans('main.phone') }}: 099/444-8488, 091/176-8787;
                                            {{ trans('main.email') }}: info@xx.com
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection