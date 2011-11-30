$(document).ready(function() {
    
    $("#searchSystemUser_employeeName").autocomplete(employees, {

            formatItem: function(item) {
                           return item.name;
                    }
                    ,matchContains:true
            }).result(function(event, item) {
                $('#searchSystemUser_employeeId').val(item.id);
            }
	);
            
     if ($("#searchSystemUser_employeeName").val() == '') {
        $("#searchSystemUser_employeeName").val(lang_typeforhint)
        .addClass("inputFormatHint");
    }
    
     $('#searchBtn').click(function() {
         if(isValidForm()){         
            $('#search_form').submit();
         }
    });
    
    $('#resetBtn').click(function() {
                  
        $("#searchSystemUser_employeeName").val(lang_typeforhint).addClass("inputFormatHint");
        $("#searchSystemUser_userName").val('');
        $("#searchSystemUser_userType option[value='']")..attr("selected", "selected");
        $("#searchSystemUser_status option[value='']")..attr("selected", "selected");
    });
    
    $('#searchSystemUser_employeeName').click(function(){
        if($('#searchSystemUser_employeeName').val() == lang_typeforhint){
            $('#searchSystemUser_employeeName').val("")
            $('#searchSystemUser_employeeName').removeClass("inputFormatHint");
        }
        }); 
    
});

$.validator.addMethod("validEmployeeName", function(value, element) {                 
               	 var empName    =   trim($('#searchSystemUser_employeeName').val());
                 var empId      =   $('#searchSystemUser_employeeId').val();  
                 
                 if(empName != lang_typeforhint && empName.length > 0  && empId == ''){
                     return false;
                 }else{
                     return true;
                 }
                
            });
            
    
function isValidForm(){
    
    var validator = $("#search_form").validate({

        rules: {
            'searchSystemUser[employeeName]' : {
                validEmployeeName: true
            }

        },
        messages: {
            'searchSystemUser[employeeName]' : {
                validEmployeeName: user_ValidEmployee
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo('div.errorHolder');
            
        }

    });
    return true;
}

function addSystemUser(){
    window.location.replace(addUserUrl);
}

