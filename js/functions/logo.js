function cropImage(logo)
{
    $.ajax({
        url: ajax_url + 'uploadLogo',
        type: 'post',
        dataType: 'json',
        beforeSend: function(request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));},
        data: JSON.stringify({'logo': logo}),
        success: function(data) {

            var responseStatus = data.status;

            switch (responseStatus)
            {
                case 1:

                    $('#logo-div').html('').append('<img src="' + ajax_url + 'logo/' + data.logo_name + '">');

                    toastr.success(logo_upload);

                    break;
                case 2:
                    toastr.error(validation_error);
                    break;
                case 0:
                    toastr.error(data.error);
                    break;
                default:
                    location.href = ajax_url;
            }
        },
        error: function() {
            toastr.error(error);
        }
    });
}

$(document).ready(function() {

    var options = $('#croppie').croppie({
        viewport: {
            width: 240,
            height: 100
        },
        boundary: {
            width: 400,
            height: 220
        }
    });

    function applyImage(input)
    {
        if (input.files && input.files[0])
        {
            var reader = new FileReader();
            reader.onload = function(e) {
                options.croppie('bind', {
                    url: e.target.result
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
        else
        {
            alert("Sorry - you're browser doesn't support the FileReader API");
        }
    }

    $('.logo-input').change(function() {

        applyImage(this);

        $('#cropper').fadeIn();
        $('.crop-button-div').show();
    });

    $('.crop').on('click', function() {

        options.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function(resp) {

            $('#cropper').hide();
            $('.crop-button-div').hide();
            $('.logo-input').val('');

            var split_src = resp.split(',');

            cropImage(split_src[1]);
        })
    });
});