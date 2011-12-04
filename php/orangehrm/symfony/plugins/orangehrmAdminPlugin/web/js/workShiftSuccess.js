$(document).ready(function() {
       
    $('#btnAssignEmployee').click(function() {  
        return !$('#workShift_availableEmp option:selected').remove().appendTo('#workShift_assignedEmp');  
    }); 
    
    $('#btnRemoveEmployee').click(function() {  
        return !$('#workShift_assignedEmp option:selected').remove().appendTo('#workShift_availableEmp');  
    }); 
    
    $('#btnSave').click(function() {
        var selected = $.map( $('#workShift_assignedEmp option'),
                      function(e) { return $(e).val(); } );
        $('#workShift_assignedEmp').val(selected);
        $('#frmWorkShift').submit();

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
                required: lang_hoursRequired,
                number: lang_notNumeric
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
});