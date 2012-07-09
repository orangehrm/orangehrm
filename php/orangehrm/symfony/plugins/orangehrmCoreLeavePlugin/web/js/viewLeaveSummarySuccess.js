function initLeaveSummary() {

    /* Making all text boxes non editable by default */
    disableEntitlementTextboxes();
    
    /* Remove edit/save buttons if no editable leave entitlements */
    var editableRows = $('input[name="txtLeaveEntitled[]"][type="text"]').length;
    if (editableRows == 0) {
        $('div[class="actionbar"]').hide();
    }
    
    /* Clearing auto-fill fields */
    $('#leaveSummary_txtEmpName_empName').click(function(){
        $(this).attr('value', '');
        $("#leaveSummary_txtEmpName_empId").attr('value', 0);
    });

    $('#leaveSummary_cmbLeavePeriod').change(function() {
        $('#leaveSummary_hdnSubjectedLeavePeriod').val($('#leaveSummary_cmbLeavePeriod').val());
    });

    /* Search button */
    $('#btnSearch').click(function() {
        // 9706 $("#frmLeaveSummarySearch").validate().resetForm();
        if(userType != 'ESS') {
        recheckEmpId();
        adjustEmpId();
        }
        $('#hdnAction').val('search');
        
        if($("#leaveSummary_txtEmpName_empName").val() == lang_typeHint || $("#leaveSummary_txtEmpName_empName").val() =="") {
            $('#leaveSummary_txtEmpName_empId').val(0);
        }
        
        $('#frmLeaveSummarySearch input.inputFormatHint').val('');
        
        $('#frmLeaveSummarySearch').submit();
    });

    $('input[name^="txtLeaveEntitled"]').change(function() {
        var flag = validateInput();
        if(!flag) {
            $('#validationMsg').attr('class', 'messageBalloon_failure');
        }
    });

    $('#btnReset').click(function(){
        handleResetButton();
    })

}

function handleEditButton() {
    if ($(this).val() == editButtonCaption) {
        enableEntitlementTextboxes();
        $(this).val(saveButtonCaption);
        return;
    }
    
    //9706 if ($(this).val() == saveButtonCaption && $("#frmLeaveSummarySearch").valid()) {
    if ($(this).val() == saveButtonCaption) {
        var flag = validateInput();
        if(flag) {
            
            if ($('#leaveSummary_txtEmpName_empName').val() == lang_typeHint) {
                $('#leaveSummary_txtEmpName_empName').val('');
            }

            $('#hdnAction').val('save');
            $('#frmLeaveSummarySearch').attr('action', '../leave/saveLeaveEntitlements');
            $('#frmLeaveSummarySearch').submit();
        } else {
            $('#validationMsg').attr('class', 'messageBalloon_failure');
        }
    }
}

function handleResetButton() {
    $('.messageBalloon_success').remove();
    $('.messageBalloon_warning').remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html('');

    $('.formInputText').removeAttr('style');  
    
    $("#leaveSummary_txtEmpName_empName").val('');
    $('#leaveSummary_txtEmpName_empId').val('0');
    $('#frmLeaveSummarySearch select').find('option:first').attr('selected','selected');
    $('#leaveSummary_cmbWithTerminated').removeAttr('checked');
    $('#frmLeaveSummarySearch').submit();
    
}
function adjustEmpId() {

    empName = $.trim($('#leaveSummary_txtEmpName_empName').val()).toLowerCase();

    if (empName != 'all' && $('#leaveSummary_txtEmpName_empId').val() == 0) {
        $('#leaveSummary_txtEmpName_empId').val('-1');
    }

}

function recheckEmpId() {

    var empDataArray = eval(employees_leaveSummary_txtEmpName); // TODO: Try to replace eval()
    var empDateCount = empDataArray.length;

    var i;
    for (i=0; i<empDateCount; i++) {

        fieldName = $.trim($('#leaveSummary_txtEmpName_empName').val()).toLowerCase();
        arrayName = empDataArray[i].name.toLowerCase();
        $('#leaveSummary_txtEmpName_empId').val(0);
        if (fieldName == arrayName) {
            $('#leaveSummary_txtEmpName_empId').val(empDataArray[i].id);
            break;
        }
    }
}

function validateInput() {
    var flag = true;

    $('.messageBalloon_success').remove();
    $('.messageBalloon_warning').remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var errorStyle = 'background-color: #FFDFDF;';
    $('.formInputText').each(function(){
        element = $(this);
        $(element).removeAttr('style');

        if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
            $('#validationMsg').html(lang_not_numeric);
            $(element).attr('style', errorStyle);
            flag = false;
        } else {
            if(parseFloat($(element).val()) > 365) {
                $('#validationMsg').html(lang_not_numeric);
                $(element).attr('style', errorStyle);
                flag = false;
            }
        }
    });

    return flag;
}

function disableEntitlementTextboxes() {
    $('input[name^="txtLeaveEntitled"]').attr('disabled', 'disabled');
}

function enableEntitlementTextboxes() {
    $('input[name^="txtLeaveEntitled"]').removeAttr('disabled');
}
