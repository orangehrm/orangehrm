$(document).ready(function () {
    $('#btnSave').click(function () {

        if ($("#resetPasswordForm").valid()) {
            $('#resetPasswordForm').submit();
        }

    });

    $('#btnCancel').click(function() {
        var url = location.href;
        var urlSegments = url.toString().split('index.php');
        location.href = urlSegments[0] + 'index.php/auth/login';
    });

    $("#resetPasswordForm").validate({

        rules: {
            'securityAuthentication[newPrimaryPassword]' : {
                required: true,
                minlength: 8,
                maxlength: 64,
                remote: {
                    url: requiredStrengthCheckUrl,
                    data: {
                        password: function(){return $('#securityAuthentication_newPrimaryPassword').val();}
                    }
                }
            },
            'securityAuthentication[primaryPasswordConfirmation]' : {
                required: true,
                minlength: 8,
                equalTo: "#securityAuthentication_newPrimaryPassword"
            }
        },
        messages: {
            'securityAuthentication[newPrimaryPassword]' : {
                required: lang_newPasswordRequired,
                minlength: lang_UserPasswordLength,
                maxlength: lang_maxLengthExceeds,
                remote: lang_passwordStrengthInvalid

            },
            'securityAuthentication[primaryPasswordConfirmation]' : {
                required: lang_confirmNewPasswordRequired,
                minlength: lang_UserPasswordLength,
                equalTo: lang_passwordMissMatch
            },
        }
    });
});
