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
    
    $('#workShift').hide();
    
    $('#btnAdd').click(function() {
        $('#workShift').show();
        $('#btnAdd').hide();
        $('#workShift_name').val('');
        $('#workShift_hours').val('');
        $('#workShift_workShiftId').val('');
        $('#workShiftHeading').html(lang_addWorkShift);
        $(".messageBalloon_success").remove();
    });
    
    $('#btnCancel').click(function() {
        $('#workShift').hide();
        $('#btnAdd').show();
        validator.resetForm();
    });
    
    $('a[href="javascript:"]').click(function(){
        var row = $(this).closest("tr");
        var shiftId = row.find('input').val();
        var url = workShiftInfoUrl+shiftId;
        $('#workShiftHeading').html(lang_editWorkShift);
        getWorkShiftInfo(url);
        var empUrl = workShiftEmpInfoUrl+shiftId;
        getWorkShiftEmpInfo(empUrl);

    });
    
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

function getWorkShiftInfo(url){
    
    $.getJSON(url, function(data) {
        $('#workShift_workShiftId').val(data.id);
        $('#workShift_name').val(data.name);
        $('#workShift_hours').val(data.hoursPerDay);
        $('#empStatus').show();
        $(".messageBalloon_success").remove();
        $('#btnAdd').hide();
        return data.id;
    });
}