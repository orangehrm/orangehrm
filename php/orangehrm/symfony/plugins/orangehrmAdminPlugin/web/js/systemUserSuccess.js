$(document).ready(function() {
   
    
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
    
    $("#systemUser_employeeName").autocomplete(employees, {

            formatItem: function(item) {
                           return item.name;
                    }
                    ,matchContains:true
            }).result(function(event, item) {
                $('#systemUser_employeeId').val(item.id);
            }
	);
            
     $("#systemUser_password").password({
	           score: '.score' 
	       });

    $('#systemUser_employeeName').click(function(){
        if($('#systemUser_employeeName').val() == user_typeForHints){
            $('#systemUser_employeeName').val("")
            $('#systemUser_employeeName').removeClass("inputFormatHint");
        }
        }); 
        
     $('#btnCancel').click(function() {
        window.location.replace(viewSystemUserUrl+'?userId='+userId);
    });
});

function disableWidgets(){
    
    $('.formInputText').attr('disabled','disabled');
    $('.formSelect').attr('disabled','disabled');
    $('#btnSave').val(user_edit);  
}

function enableWidgets(){ 
    $('.formInputText').removeAttr('disabled');
    $('.formSelect').removeAttr('disabled');
    $('#btnSave').val(user_save);
}


$.validator.addMethod("validEmployeeName", function(value, element) {                 
               	 var empName    =   trim($('#systemUser_employeeName').val());
                 var empId      =   $('#systemUser_employeeId').val();  
                 
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
                    if(userId > 0)
                        return false;
                    else
                        return true;
                  },
                minlength: 4,
                maxlength: 20
            },
            'systemUser[confirmPassword]' : {
                maxlength: 20,
                equalTo: "#systemUser_password"
            },
            'systemUser[employeeName]' : {
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
            'systemUser[employeeName]' : {
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