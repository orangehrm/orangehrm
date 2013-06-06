$(document).ready(function() {
    $("label[for='systemUser_chkChangePassword']").parent('li').addClass('checkChangePassword').hide();
    $("label[for='systemUser_chkChangeSecPassword']").parent('li').addClass('checkChangeSecPassword').hide();
    $('.secPassReq').hide();    

    $(':input.password').each(function(){
        $(this).parent('li').addClass('passwordDiv');
    });
    
    $(':input.secPassword').each(function(){
        $(this).parent('li').addClass('secPasswordDiv');
    });
    
    $('#systemUser_password').after('<label class="score"/>');
    $('#systemUser_secondaryPassword').after('<label class="scoreSec"/>');

    if (isEditMode == 'true') {
        $('.checkChangePassword').show();
        $('.checkChangeSecPassword').show();
        $('.passwordDiv').css("display", "none");
        $('.secPasswordDiv').css("display", "none");
        $('.secPassReq').show();
    }
    
    if (ldapInstalled == 'true') {
        $('.passwordRequired').hide();
    }
    
    $('#systemUser_chkChangePassword').click(function(){
        $("#systemUser_password").val('');
        if($(this).is(':checked')) {
            $('.passwordDiv').show();
        } else {
            $('.passwordDiv').hide();
        }
    });
    
    $('#systemUser_chkChangeSecPassword').click(function(){
        $("#systemUser_secondaryPassword").val('');
        if($(this).is(':checked')) {
            $('.secPasswordDiv').show();
        } else {
            $('.secPasswordDiv').hide();
        }
    });
    
    $('#btnSave').click(function() {
        
        if ($('#btnSave').val() == user_edit){
            enableWidgets();
        } else if ($('#btnSave').val() == user_save){
            $('#systemUser_userId').val(userId);
            if(isValidForm()){          
                $('#frmSystemUser').submit();
            }
        }
    });
    
    if(userId > 0){
        $('#UserHeading').text(user_editLocation);
        $("#systemUser_password").val('');
        $("#systemUser_confirmPassword").val('');
        disableWidgets();
    }
    
    $("#systemUser_password").password({
        score: '.score' 
    });
    
    $("#systemUser_secondaryPassword").password({
        score: '.scoreSec' 
    });
        
    $('#btnCancel').click(function() {
        window.location.replace(viewSystemUserUrl+'?userId='+userId);
    });
    
});

function disableWidgets(){
    
    $('.formInputText').attr('disabled','disabled');
    $('.formSelect').attr('disabled','disabled');
    $('#systemUser_chkChangePassword').attr('disabled','disabled');
    $('#systemUser_chkChangeSecPassword').attr('disabled','disabled');
    $('#btnSave').val(user_edit);  
}

function enableWidgets(){ 
    $('.formInputText').removeAttr('disabled');
    $('.formSelect').removeAttr('disabled');
    $('#systemUser_chkChangePassword').removeAttr('disabled');
    $('#systemUser_chkChangeSecPassword').removeAttr('disabled');
    $('#btnSave').val(user_save);
}


$.validator.addMethod("validEmployeeName", function(value, element) {                 

    return autoFill('systemUser_employeeName_empName', 'systemUser_employeeName_empId', employees_systemUser_employeeName);
                 
});
            
    
function isValidForm(){
    validator = $("#frmSystemUser").validate({

        rules: {
            'systemUser[userName]' : {
                required:true,
                maxlength: 20,
                minlength: 5,
                remote: {
                    url: isUniqueUserUrl,
                    data: {
                        user_id: userId
                    }
                }
            },
            'systemUser[password]' : {
                required:function(element) {
                    if(($('#systemUser_chkChangePassword').val() == 'on' || (isEditMode == 'false')) && 
                            (ldapInstalled == 'false'))
                        return true;
                    else
                        return false;
                },
                minlength: 4,
                maxlength: 20
            },
            'systemUser[confirmPassword]' : {
                maxlength: 20,
                equalTo: "#systemUser_password"
            },
            'systemUser[secondaryPassword]' : {
                required:function(element) {
                    if($('#systemUser_chkChangeSecPassword').attr('checked') == true)
                        return true;
                    else
                        return false;
                },
                minlength: 4,
                maxlength: 20
            },
            'systemUser[confirmation]' : {
                maxlength: 20,
                equalTo: "#systemUser_secondaryPassword"
            },
            'systemUser[employeeName][empName]' : {
                required:true,
                maxlength: 200,
                validEmployeeName: true
            }
        },
        messages: {
            'systemUser[userName]' : {
                required: user_UserNameRequired,
                maxlength: user_Max20Chars,
                remote: user_name_alrady_taken,
                minlength: user_UserNameLength
            },
            'systemUser[password]' : {
                required: user_UserPaswordRequired,
                maxlength: user_Max20Chars,
                minlength: user_UserPasswordLength
            },
            'systemUser[confirmPassword]' : {
                required: user_UserConfirmPassword,
                maxlength: user_Max20Chars,
                equalTo: user_samePassword
            },
            'systemUser[secondaryPassword]' : {
                required: user_UserPaswordRequired,
                maxlength: user_Max20Chars,
                minlength: user_UserPasswordLength
            },
            'systemUser[confirmation]' : {
                required: user_UserConfirmPassword,
                maxlength: user_Max20Chars,
                equalTo: user_samePassword
            },
            'systemUser[employeeName][empName]' : {
                required: user_EmployeeNameRequired,
                validEmployeeName: user_ValidEmployee
            }
        }

    });
    return true;
}

function autoFill(selector, filler, data) {
    $("#" + filler).val("");
    var valid = false;
    $.each(data, function(index, item){
        if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
            $("#" + filler).val(item.id);
            valid = true;
        }
    });
    return valid;
}
