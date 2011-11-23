$(document).ready(function() {
   
    
    $('#btnSave').click(function() {
        
        if ($('#btnSave').val() == user_edit){
            enableWidgets();
        } else if ($('#btnSave').val() == user_save){
           
            $('#sytemUser_userId').val(userId);
            if(isValidForm()){          
                $('#frmSystemUser').submit();
            }
        }
    });
    
    $('#btnCancel').click(function() {
        window.location.replace(viewLocationUrl+'?locationId='+locationId);
    });
    
    
    if(userId > 0){
        $('#UserHeading').text(user_editLocation);
        disableWidgets();
    }
    
    $("#sytemUser_employeeName").autocomplete(employees, {

            formatItem: function(item) {
                           return item.name;
                    }
                    ,matchContains:true
            }).result(function(event, item) {
                $('#sytemUser_employeeId').val(item.id);
            }
	);
    
});

function disableWidgets(){
    $('.formInput').attr('disabled','disabled');
    $('#btnSave').val(user_edit);  
}

function enableWidgets(){ 
    $('.formInput').removeAttr('disabled');
    $('#btnSave').val(user_save);
}


    
function isValidForm(){
    
   
    
    var validator = $("#frmSystemUser").validate({

        rules: {
            'sytemUser[userName]' : {
                required:true,
                maxlength: 20
            },
            'sytemUser[password]' : {
                required:true,
                maxlength: 20
            },
            'sytemUser[confirmPassword]' : {
                required:true,
                maxlength: 20,
                equalTo: "#sytemUser_password"
            },
            'sytemUser[employeeName]' : {
                required:true,
                maxlength: 200
            }

        },
        messages: {
            'sytemUser[userName]' : {
                required: user_UserNameRequired,
                maxlength: user_Max20Chars
            },
            'sytemUser[password]' : {
                required: user_UserPaswordRequired,
                maxlength: user_Max20Chars
            },
            'sytemUser[confirmPassword]' : {
                required: user_UserConfirmPassword,
                maxlength: user_Max20Chars,
                equalTo: user_samePassword
            },
            'sytemUser[employeeName]' : {
                required: user_EmployeeNameRequired
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));
        }

    });
    return true;
}