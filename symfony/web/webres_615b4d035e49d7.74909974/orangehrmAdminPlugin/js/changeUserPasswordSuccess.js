$(document).ready(function() {
    
    disableWidgets();
    $('#changeUserPassword_secondaryPassword').after('<label class="scoreSec"/>');
    
    $('#btnSave').click(function() {
        
        if ($('#btnSave').val() == lang_edit){
            enableWidgets();
        } else if ($('#btnSave').val() == lang_save){
           
            if($("#frmChangePassword").valid()){
                $('#frmChangePassword').submit();
            }
            
        }
        
    });

    $("#frmChangePassword").validate({

        rules: {
            'changeUserPassword[currentPassword]' : {
                required: true
            },
            'changeUserPassword[newPassword]' : {
                required: true,
                minlength: 8,
                maxlength: 64,
                remote: {
                    url: requiredStrengthCheckUrl,
                    data: {
                        password: function(){return $('#changeUserPassword_newPassword').val();}
                    }
                }
            },
            'changeUserPassword[confirmNewPassword]' : {
                required: true,
                equalTo: "#changeUserPassword_newPassword"
            },
            'changeUserPassword[secondaryPassword]' : {
                required: true,
                minlength: 4,
                maxlength: 20
            },
            'changeUserPassword[confirmation]' : {
                required: true,
                equalTo: "#changeUserPassword_secondaryPassword"
            }
        },
        messages: {
            'changeUserPassword[currentPassword]' : {
                required: lang_currentPasswordRequired
            },
            'changeUserPassword[newPassword]' : {
                required: lang_newPasswordRequired,
                maxlength: lang_maxLengthExceeds,
                minlength: lang_UserPasswordLength,
                remote: lang_passwordStrengthInvalid
            },
            'changeUserPassword[confirmNewPassword]' : {
                required: lang_confirmNewPasswordRequired,
                equalTo: lang_passwordMissMatch
            },
            'changeUserPassword[secondaryPassword]' : {
                required: lang_newPasswordRequired,
                maxlength: lang_maxLengthExceeds,
                minlength: lang_UserPasswordLength
            },
            'changeUserPassword[confirmation]' : {
                required: lang_confirmNewPasswordRequired,
                equalTo: lang_passwordMissMatch
            }
        }
    });
    
    // $("#changeUserPassword_newPassword").password({
    //     score: '.score'
    // });
    //
    // $("#changeUserPassword_secondaryPassword").password({
    //     score: '.scoreSec'
    // });

    $('#btnCancel').click(function() {
        window.history.back();
    });
    
});

function disableWidgets(){
    $('.formInputText').attr('disabled','disabled');
    $('.formSelect').attr('disabled','disabled');
    $('#btnSave').val(lang_edit);  
}

function enableWidgets(){ 
    $('.formInputText').removeAttr('disabled');
    $('.formSelect').removeAttr('disabled');
    $('#btnSave').val(lang_save);
}