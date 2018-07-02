function disableButtons()
{
    $('.create').prop('disabled', true);
    $('.cancel').prop('disabled', true);
}

function enableButtons()
{
    $('.create').prop('disabled', false);
    $('.cancel').prop('disabled', false);
}

$(document).ready(function() {

    $('.create').on('click', function() {

        disableButtons();

        var check_validation = 1;

        var date_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/;

        var start_date_input = $('.start-date');
        var end_date_input = $('.end-date');
        var office_input = $('.office');

        start_date_input.removeAttr('style');
        end_date_input.removeAttr('style');
        office_input.removeAttr('style');

        if (!date_test.test(start_date_input.val()))
        {
            start_date_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }

        if (!date_test.test(end_date_input.val()))
        {
            end_date_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }

        if (office_input.has('option').length === 0)
        {
            office_input.css('border', '1px solid #FF0000');

            check_validation = 0;
        }

        if (!check_validation)
        {
            enableButtons();
            toastr.error(validation_error);

            return 0;
        }

        $.ajax({
            url: ajax_url + 'registerReports/reports/insert',
            type: 'post',
            dataType: 'json',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'start_date': start_date_input.val(), 'end_date': end_date_input.val(), 'office': office_input.val()},
            success: function(data) {

                enableButtons();

                var responseStatus = data.status;

                switch (responseStatus)
                {
                    case 1:
                        location.href = ajax_url + 'registerReports/reports/list';
                        break;
                    case 2:
                        toastr.warning(data.warning);
                        break;
                    case 0:
                        toastr.error(data.error);
                        break;
                    default:
                        location.href = ajax_url;
                }
            },
            error: function() {
                enableButtons();
                toastr.error(error);
            }
        });
    });

    $('.register-report-delete').on('click', function(e) {

        e.preventDefault();

        var confirm_link = $(this).attr('data-confirm-link');
        var delete_message = $(this).attr('data-delete-message');

        var custom_alert_text = alert_text;

        if (delete_message === 'T')
        {
            custom_alert_text = alert_delete_report;
        }

        swal({
            title: alert_title,
            text: custom_alert_text,
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

    $('.register-report-pdf').on('click', function(e) {

        e.preventDefault();

        var report_id = $(this).attr('data-id');

        swal({
            title: alert_pdf_report_title,
            text: alert_pdf_report,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: alert_confirm,
            cancelButtonText: alert_cancel,
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm)
            {
                window.open(ajax_url + 'registerReports/reports/pdf/' + report_id + '/1', '_blank');
            }
            else
            {
                window.open(ajax_url + 'registerReports/reports/pdf/' + report_id, '_blank');
            }
        });
    });
});