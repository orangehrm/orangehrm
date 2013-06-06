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

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });
            
    $('#searchBtn').click(function() {
        if(isValidForm()){         
            $('#search_form').submit();
        }
    });
    
    $('#resetBtn').click(function() {
                  
        $("#searchSystemUser_employeeName_empName").val(lang_typeforhint).addClass("inputFormatHint");
        $("#searchSystemUser_userName").val('');
        $("#searchSystemUser_userType option[value='']").attr("selected", "selected");
        $("#searchSystemUser_status option[value='']").attr("selected", "selected");
        window.location.replace(viewUserUrl);
    });
    
    /* Delete confirmation controls: Begin */
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        /* Delete confirmation controls: End */
    
});

$.validator.addMethod("validEmployeeName", function(value, element) {                 
    var empName    =   trim($('#searchSystemUser_employeeName_empName').val());
    var empId      =   $('#searchSystemUser_employeeName_empName_empId').val();  
                 
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



