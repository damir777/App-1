$(document).ready(function() {

    $('.footable').footable({ paginate:false });

    $('.confirm-alert').click(function(e) {

        e.preventDefault();

        var confirm_link = $(this).attr('data-confirm-link');

        swal({
            title: alert_title,
            text: alert_text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: alert_confirm,
            cancelButtonText: alert_cancel,
            closeOnConfirm: false,
            closeOnCancel: true
        }, function () {
            location.href = confirm_link;
        });
    });

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "preventDuplicates": true,
        "positionClass": "toast-bottom-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "8000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "dd.mm.yyyy."
    });

    $('.clockpicker').clockpicker();

    $('[data-toggle="tooltip"]').tooltip();
});