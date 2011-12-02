$(document).ready(function() {
       
    $('#btnAssignEmployee').click(function() {  
        return !$('#workShift_availableEmp option:selected').remove().appendTo('#workShift_assignedEmp');  
    }); 
    
    $('#btnRemoveEmployee').click(function() {  
        return !$('#workShift_assignedEmp option:selected').remove().appendTo('#workShift_availableEmp');  
    }); 
    
    var validator = $("#frmWorkShift").validate({

        rules: {
            'workShift[name]' : {
                required:true,
                maxlength: 50
            },
            'workShift[hours]' : {
                required:true,
                number: true
            }

        },
        messages: {
            'workShift[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors
            },
            'workShift[hours]' : {
                required:true,
                number: true
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
});