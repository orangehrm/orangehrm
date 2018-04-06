$(document).ready(function () {
    $('#btnSave').click(function () {

        if ($("#frmChangeWeakPassword").valid()) {
            $('#frmChangeWeakPassword').submit();
        }

    });

    $('#btnCancel').click(function() {
        window.history.back();
    });

    $("#frmChangeWeakPassword").validate({

        rules: {
            'changeWeakPassword[currentPassword]' : {
                required: true
            },
            'changeWeakPassword[newPassword]' : {
                required: true,
                minlength: 8,
                maxlength: 64
            },
            'changeWeakPassword[passwordConfirmation]' : {
                required: true,
                equalTo: "#changeWeakPassword_newPassword"
            }
        },
        messages: {
            'changeWeakPassword[currentPassword]' : {
                required: lang_currentPasswordRequired
            },
            'changeWeakPassword[newPassword]' : {
                required: lang_newPasswordRequired,
                maxlength: lang_maxLengthExceeds,
                minlength: lang_UserPasswordLength
            },
            'changeWeakPassword[passwordConfirmation]' : {
                required: lang_confirmNewPasswordRequired,
                equalTo: lang_passwordMissMatch
            }
        }
    });
});