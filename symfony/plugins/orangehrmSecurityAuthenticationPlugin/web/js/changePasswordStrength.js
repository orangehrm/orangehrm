$(document).ready(function () {
    $('#btnSave').click(function () {

        if ($("#resetPasswordForm").valid()) {
            $('#resetPasswordForm').submit();
        }

    });

    $('#btnCancel').click(function() {
        window.history.back();
    });

    $("#resetPasswordForm").validate({

        rules: {
            'securityAuthentication[newPrimaryPassword]' : {
                required: true,
                minlength: 8,
                maxlength: 64
            },
            'securityAuthentication[primaryPasswordConfirmation]' : {
                required: true,
                equalTo: "#securityAuthentication_newPrimaryPassword"
            }
        },
        messages: {
            'securityAuthentication[newPrimaryPassword]]' : {
                required: lang_newPasswordRequired,
                maxlength: lang_maxLengthExceeds,
                minlength: lang_UserPasswordLength
            },
            'securityAuthentication[primaryPasswordConfirmation]' : {
                required: lang_confirmNewPasswordRequired,
                equalTo: lang_passwordMissMatch
            }
        }
    });
});
