$(document).ready(function() {

    $('#btnDelete').attr('disabled', 'disabled');


    $("#ohrmList_chkSelectAll").click(function() {
        if($(":checkbox").length == 1) {
            $('#btnDelete').attr('disabled','disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        }
    });

    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
        }
    });

    $('#btnDelete').click(function(){
        $('#frmList_ohrmListComponent').submit(function(){
            $('#deleteConfirmation').dialog('open');
            return false;
        });
    });

    $("#deleteConfirmation").dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle',
        open: function() {
            $('#dialogCancelBtn').focus();
        }
    });

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });

    
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
        $("#searchSystemUser_userType option[value='']").attr("selected", "selected");
        $("#searchSystemUser_status option[value='']").attr("selected", "selected");
        window.location.replace(viewUserUrl);
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
            

function addSystemUser(){
    window.location.replace(addUserUrl);
}

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



