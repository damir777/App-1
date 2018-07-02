@extends('layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-12">
            <h2 class="page-title">{{ trans('main.statistics') }}</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="widget style1 navy-bg">
                                <div class="text-center">
                                    <span>Ukupno korisnika</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['total_companies'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 navy-bg">
                                <div class="text-center">
                                    <span>Aktivnih korisnika</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['active_companies'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 blue-bg">
                                <div class="text-center">
                                    <span>{{ trans('main.subscribers') }}</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['subscribers'] }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget style1 red-bg">
                                <div class="text-center">
                                    <span>Nepopunjen profil</span>
                                </div>
                                <h2 class="font-bold text-center">{{ $statistics['uncompleted_profile'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection