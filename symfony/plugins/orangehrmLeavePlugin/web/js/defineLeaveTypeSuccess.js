$(document).ready(function(){

    //$("form#frmLeaveType :input:visible:enabled:first").focus();
    $('#exclude_link').click(function(){
        $("#excludeInfoDialog").modal();
    });
    
    $('#saveButton').click(function(){
        $('#frmLeaveType').submit();
    });
    
    $.validator.addMethod("uniqueLeaveType", function(value) {
        
        var valid = true;        
        var originalName  = $.trim($("#leaveType_hdnOriginalLeaveTypeName").val()).toLowerCase();
        
        value = $.trim(value).toLowerCase();
        
        if (value != originalName) {
            for (var i = 0; i < activeLeaveTypes.length; i++) {
                if (value == activeLeaveTypes[i].toLowerCase()) {
                    valid = false;
                    break;
                }
            }
        }
        
        return valid;
    });
    
    var validator = 
    $("#frmLeaveType").validate({
        rules: {
            'leaveType[txtLeaveTypeName]': {
                required: true, 
                maxlength: 50,
                uniqueLeaveType: true
            }
        },
        messages: {
            'leaveType[txtLeaveTypeName]': {
                required: lang_LeaveTypeNameRequired,
                maxlength: lang_LeaveTypeNameTooLong,
                uniqueLeaveType: lang_LeaveTypeExists
            }
        },
        submitHandler: function(form) {
            
            var deletedId = isDeletedLeaveType();
            if (deletedId) {
                $('#undeleteLeaveType_undeleteId').val(deletedId);               
                $("#undeleteDialog").modal();
            } else {
                form.submit();
            }
        }
    });

    $('#backButton').click(function(){
        window.location.href = backButtonUrl;
    });

    $("#undeleteYes").click(function(){
        $('#frmUndeleteLeaveType').submit();
    });

    $("#undeleteNo").click(function(){
        $(this).attr('disabled', true);
        $('#leaveType_txtLeaveTypeName').attr('disabled', false);
        $('#frmLeaveType').get(0).submit();
    });


    loadActiveLeaveTypes();
    loadDeletedLeaveTypes();
});

/**
 * Checks if current leave type name value matches a deleted leave type.
 * 
 * @return Leave Type ID if it matches a deleted leave type else false.
 */
function isDeletedLeaveType() {

    if ($.trim($("#leaveType_hdnOriginalLeaveTypeName").val()) ==
        $.trim($("#leaveType_txtLeaveTypeName").val())) {
        return false;
    }

    for (var i = 0; i < deletedLeaveTypes.length; i++) {
        if (deletedLeaveTypes[i].name.toLowerCase() == 
            $.trim($('#leaveType_txtLeaveTypeName').val()).toLowerCase()) {
            return deletedLeaveTypes[i].id;
        }
    }
    return false;
}

function loadActiveLeaveTypes() {
    var url = './loadActiveLeaveTypesJson';

    $.getJSON(url, function(data) {
        activeLeaveTypes = data;
    });
}

function loadDeletedLeaveTypes() {
    var url = './loadDeletedLeaveTypesJson';
    
    $.getJSON(url, function(data) {
        deletedLeaveTypes = data;
    });
}