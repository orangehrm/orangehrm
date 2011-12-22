$(document).ready(function() {
    
    disableWidgets();
    
    $('#btnSave').click(function() {
        
        if ($('#btnSave').val() == lang_edit){
            enableWidgets();
        } else if ($('#btnSave').val() == lang_save){
           
            if(isValidForm()){          
                $('#frmChangePassword').submit();
            }
            
        }
        
    });
    
    $("#changeUserPassword_newPassword").password({
        score: '.score' 
	});

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


function isValidForm(){
    
    var validator = $("#frmChangePassword").validate({

        rules: {
            'changeUserPassword[currentPassword]' : {
                required: true
            },            
            'changeUserPassword[newPassword]' : {
                required: true,
                minlength: 4,
                maxlength: 20
            },
            'changeUserPassword[confirmNewPassword]' : {
                required: true,
                equalTo: "#changeUserPassword_newPassword"
            }
        },
        messages: {
            'changeUserPassword[currentPassword]' : {
                required: lang_currentPasswordRequired
            },
            'changeUserPassword[newPassword]' : {
                required: lang_newPasswordRequired,
                maxlength: lang_maxLengthExceeds,
                minlength: lang_UserPasswordLength
            },
            'changeUserPassword[confirmNewPassword]' : {
                required: lang_confirmNewPasswordRequired,
                equalTo: lang_passwordMissMatch
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));
            
        }

    });
    
    return true;
    
}