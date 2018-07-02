<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xx</title>

    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('font-awesome/css/font-awesome.css') }}

    {{ HTML::style('css/plugins/toastr/toastr.min.css') }}
    {{ HTML::style('css/plugins/iCheck/custom.css') }}

    {{ HTML::style('css/animate.min.css') }}
    {{ HTML::style('css/style.min.css') }}
    {{ HTML::style('css/custom.css') }}

    {{ HTML::script('js/jquery-3.1.1.min.js') }}
    {{ HTML::script('js/plugins/toastr/toastr.min.js') }}
    {{ HTML::script('js/plugins/iCheck/icheck.min.js') }}
</head>

<body class="auth-background">
@yield('content')

<script>

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": true,
        "positionClass": "toast-bottom-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "12000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    var checkbox_input = $('.terms-checkbox');

    checkbox_input.on('ifChecked', function() {

        $('.register-button').prop('disabled', false);
    });

    checkbox_input.on('ifUnchecked', function() {

        $('.register-button').prop('disabled', true);
    });

</script>

@if (session('success_message'))
    <script>
        $(document).ready(function(){
            toastr.success("{{ session('success_message') }}");
        });
    </script>
@endif

@if (session('info_message'))
    <script>
        $(document).ready(function(){
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
        $(document).ready(function(){
            toastr.error("{{ session('error_message') }}");
        });
    </script>
@endif

</body>
</html>