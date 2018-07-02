<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xx</title>
    <meta name="_token" content="{{ csrf_token() }}"/>

    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

    {{ HTML::style('css/plugins/datapicker/datepicker3.css') }}
    {{ HTML::style('css/plugins/toastr/toastr.min.css') }}
    {{ HTML::style('css/plugins/footable/footable.core.css') }}
    {{ HTML::style('css/plugins/sweetalert/sweetalert.css') }}
    {{ HTML::style('css/plugins/jasny/jasny-bootstrap.min.css') }}
    {{ HTML::style('css/plugins/clockpicker/clockpicker.css') }}
    {{ HTML::style('css/plugins/bootstrapTour/bootstrap-tour.min.css') }}

    {{ HTML::style('css/animate.min.css') }}
    {{ HTML::style('css/style.min.css') }}
    {{ HTML::style('css/custom.css') }}

    {{ HTML::script('js/jquery-3.1.1.min.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}

    {{ HTML::script('js/plugins/metisMenu/jquery.metisMenu.js') }}
    {{ HTML::script('js/plugins/slimscroll/jquery.slimscroll.min.js') }}
    {{ HTML::script('js/plugins/pace/pace.min.js') }}
    {{ HTML::script('js/plugins/datapicker/bootstrap-datepicker.js') }}
    {{ HTML::script('js/plugins/toastr/toastr.min.js') }}
    {{ HTML::script('js/plugins/footable/footable.all.min.js') }}
    {{ HTML::script('js/plugins/sweetalert/sweetalert.min.js') }}
    {{ HTML::script('js/plugins/jasny/jasny-bootstrap.min.js') }}
    {{ HTML::script('js/plugins/clockpicker/clockpicker.js') }}
    {{ HTML::script('js/plugins/chartJs/Chart.min.js') }}
    {{ HTML::script('js/plugins/bootstrapTour/bootstrap-tour.min.js') }}

    {{ HTML::script('js/inspinia.js') }}
    {{ HTML::script('js/main.js') }}
    {{ HTML::script('js/tour.js') }}

    <script>
        var ajax_url = '<?php echo URL::to('/'); ?>/';
    </script>
</head>
<body>

<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs"><strong class="font-bold">{{ $username }}</strong></span>
                                <span class="text-muted text-xs block">{{ $user_role }}
                                    @if ($user_role_id != 1)
                                        <b class="caret"></b>
                                    @endif
                                </span>
                            </span>
                        </a>
                        @if ($user_role_id != 1)
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                @if ($user_role_id == 2)
                                    <li><a href="{{ route('CompanyInfo') }}">{{ trans('main.company_info') }}</a></li>
                                @endif
                                <li><a href="#" class="start-tour">{{ trans('main.visual_tutorial') }}</a></li>
                                <li><a href="http://podrska.betaware.hr/login.php">{{ trans('main.support') }}</a></li>
                                <li class="divider"></li>
                                <li><a href="{{ route('LogoutUser') }}">{{ trans('main.logout') }}</a></li>
                            </ul>
                        @endif
                    </div>
                    <div class="logo-element">
                        MU
                    </div>
                </li>
                @include($menu)
            </ul>
        </div>
    </nav>
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    @if (session('super_admin'))
                        <li>
                            <a href="{{ route('ReturnToSuperAdmin') }}"><strong style="color: #ed5565">SuperAdmin</strong></a>
                        </li>
                    @endif
                    <li class="dropdown mobile-profile">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><p>{{ $username }}</p></li>
                            @if ($user_role_id == 2)
                                <li><a href="{{ route('CompanyInfo') }}">{{ trans('main.company_info') }}</a></li>
                            @endif
                            <li><a href="http://podrska.betaware.hr/login.php">{{ trans('main.support') }}</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ route('LogoutUser') }}">{{ trans('main.logout') }}</a></li>
                        </ul>
                    </li>
                    <li class="company-name">
                        @if ($user_role_id != 1)
                            <span class="m-r-sm text-muted">{{ $company_name }}</span>
                        @endif
                    </li>
                    <li>
                        <a href="{{ route('LogoutUser') }}">
                            <i class="fa fa-sign-out"></i> {{ trans('main.logout') }}
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        @yield('content')
    </div>
</div>

@if (session('success_message'))
    <script>
        $(document).ready(function() {
            toastr.success("{{ session('success_message') }}");
        });
    </script>
@endif

@if (session('info_message'))
    <script>
        $(document).ready(function() {
            toastr.info("{{ session('info_message') }}");
        });
    </script>
@endif

@if (session('warning_message'))
    <script>
        $(document).ready(function() {
            toastr.warning("{{ session('warning_message') }}");
        });
    </script>
@endif

@if (session('error_message'))
    <script>
        $(document).ready(function() {
            toastr.error("{{ session('error_message') }}");
        });
    </script>
@endif

<script>
    var validation_error = '{{ trans('errors.validation_error') }}';
    var error = '{{ trans('errors.error') }}';

    var alert_title = '{{ trans('main.alert_title') }}';
    var alert_text = '{{ trans('main.alert_text') }}';
    var alert_confirm = '{{ trans('main.alert_confirm') }}';
    var alert_cancel = '{{ trans('main.alert_cancel') }}';
</script>

</body>
</html>