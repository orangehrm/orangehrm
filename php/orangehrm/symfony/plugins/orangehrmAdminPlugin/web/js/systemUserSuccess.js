$(document).ready(function() {
    
    if (isEditMode) {
        $('#passwordDiv').hide();
    }
    
    if (ldapInstalled) {
        $('.passwordRequired').hide();
    }
    
    $('#systemUser_chkChangePassword').click(function(){
        if($(this).attr('checked') == true) {
            $('#passwordDiv').show();
        } else {
            $('#passwordDiv').hide();
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
        disableWidgets();
    }
    
     $("#systemUser_password").password({
	           score: '.score' 
	       });
        
     $('#btnCancel').click(function() {
        window.location.replace(viewSystemUserUrl+'?userId='+userId);
    });
    
});

function disableWidgets(){
    
    $('.formInputText').attr('disabled','disabled');
    $('.formSelect').attr('disabled','disabled');
    $('#systemUser_chkChangePassword').attr('disabled','disabled');
    $('#btnSave').val(user_edit);  
}

function enableWidgets(){ 
    $('.formInputText').removeAttr('disabled');
    $('.formSelect').removeAttr('disabled');
    $('#systemUser_chkChangePassword').removeAttr('disabled');
    $('#btnSave').val(user_save);
}


$.validator.addMethod("validEmployeeName", function(value, element) {                 
               	 var empName    =   trim($('#systemUser_employeeName_empName').val());
                 var empId      =   $('#systemUser_employeeName_empId').val();  
                 
                 if(empName.length > 0 && empId == ''){
                     return false;
                 }else{
                     return true;
                 }
                
            });
            
    
function isValidForm(){
    
   
    
    var validator = $("#frmSystemUser").validate({

        rules: {
            'systemUser[userName]' : {
                required:true,
                maxlength: 20,
                minlength: 5,
                remote: {
                   url: isUniqueUserUrl,
                   data: { user_id: userId}
                }
            },
            'systemUser[password]' : {
                required:function(element) {
                    if($('#systemUser_chkChangePassword').attr('checked') == true && !ldapInstalled)
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
            'systemUser[employeeName][empName]' : {
                required: user_EmployeeNameRequired,
                validEmployeeName: user_ValidEmployee
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));
            
        }

    });
    return true;
}
