$(document).ready(function () {
    $('form[id="frmPurgeEmployeeAuthenticate"]').validate({
        rules: {
            confirm_password: {
                required: true,
                maxlength: 250,
            }
        },
        messages: {
            confirm_password: lang_required,
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
