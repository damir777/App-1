$(document).ready(function() {

    $('.licence-data').on('click', function() {

        var this_button = $(this);

        $('.company-id').val(this_button.attr('data-company-id'));
        $('.licence-end').val(this_button.attr('data-licence-end'));
    });


    $('.update-licence').on('click', function() {

        $('.update-licence').prop('disabled', true);

        var company_id = $('.company-id').val();
        var licence_end_input = $('.licence-end');

        licence_end_input.removeAttr('style');

        var date_test = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/;

        if (!date_test.test(licence_end_input.val()))
        {
            licence_end_input.css('border', '1px solid #FF0000');
            toastr.error(validation_error);

            $('.update-licence').prop('disabled', false);

            return 0;
        }

        $.ajax({
            url: ajax_url + 'superadmin/updateLicence',
            type: 'post',
            dataType: 'json',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
            data: {'company_id': company_id, 'licence_end': licence_end_input.val()},
            success: function(data) {

                $('.update-licence').prop('disabled', false);

                var responseStatus = data.status;

                switch (responseStatus)
                {
                    case 1:
                        location.href = ajax_url + 'superadmin/companies';
                        break;
                    case 2:
                        toastr.warning(validation_error);
                        break;
                    case 0:
                        toastr.error(data.error);
                        break;
                    default:
                        location.href = ajax_url;
                }
            },
            error: function() {
                $('.update-licence').prop('disabled', false);
                toastr.error(error);
            }
        });
    });
});