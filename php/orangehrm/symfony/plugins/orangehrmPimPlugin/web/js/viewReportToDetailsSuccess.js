$(document).ready(function() {
    //hide adding part...
    $("#addPaneReportTo").hide();
    
    //Remove check box fields if NO_RECORDS
    if (!haveSupervisors) {
        $('#sup_list .check').hide();
    }
    if (!haveSubordinates) {
        $('#sub_list .check').hide();
    }
    
    $("#reportto_supervisorName_empName").click(function() {
        $("#reportto_supervisorName_empName").val('').removeClass("inputFormatHint");
    });
    $("#reportto_subordinateName_empName").click(function() {
        $("#reportto_subordinateName_empName").val('').removeClass("inputFormatHint");
    });
    
    if (!canCreateSupervisors) {
        $('#frmAddReportTo').find(':radio[id="reportto_type_flag_1"]').attr('disabled', 'disabled');
        $('#frmAddReportTo').find(':radio[id="reportto_type_flag_2"]').attr('checked', true);
    }
    if (!canCreateSubordinates) {
        $('#frmAddReportTo').find(':radio[id="reportto_type_flag_2"]').attr('disabled', 'disabled');
        $('#frmAddReportTo').find(':radio[id="reportto_type_flag_2"]').attr('checked', false);
    }
    hideShowReportingMethodOther();

    hideShowSupervisorSubordinate();
    $('.radio_list input').live('click', function() {
        hideShowSupervisorSubordinate();
        $("#reportto_supervisorName_empName").val(typeForHints);
        $("#reportto_subordinateName_empName").val(typeForHints);
    });
    
    if(essMode){
        $('#supListActions').hide();
        $('#sup_list .check').hide();
        $('#subListActions').hide();
        $('#sub_list .check').hide();
        removeEditLinks();
    }

    $('#reportto_reportingMethodType').change(function() {
        hideShowReportingMethodOther();
    });
    
    $('#btnSaveReportTo').click(function() {
        if(isValidForm($('#frmAddReportTo #nameType').val())){
            $('#frmAddReportTo').submit();
        }
    });

    $("#checkAllSup").click(function(){
        if($("#checkAllSup:checked").attr('value') == 'on') {
            $(".checkboxSup").attr('checked', 'checked');
        } else {
            $(".checkboxSup").removeAttr('checked');
        }
    });

    $(".checkboxSup").click(function() {
        $("#checkAllSup").removeAttr('checked');
        if(($(".checkboxSup").length - 1) == $(".checkboxSup:checked").length) {
            $("#checkAllSup").attr('checked', 'checked');
        }
    });

    $("#checkAllSub").click(function(){
        if($("#checkAllSub:checked").attr('value') == 'on') {
            $(".checkboxSub").attr('checked', 'checked');
        } else {
            $(".checkboxSub").removeAttr('checked');
        }
    });

    $(".checkboxSub").click(function() {
        $("#checkAllSub").removeAttr('checked');
        if(($(".checkboxSub").length - 1) == $(".checkboxSub:checked").length) {
            $("#checkAllSub").attr('checked', 'checked');
        }
    });

    // Add a supervisor 
    $('#btnAddSupervisorDetail').click(function() {

        $('#reportto_subordinateName_empId').val("");
        $('#reportto_supervisorName_empId').val("");
        $("#reportto_previousRecord").val("");
        $('#reportto_type_flag_1').attr('checked', 'checked');
        $("#reportToHeading").text(addSupervisor);
        $('.radio_list').hide();
        $(".paddingLeftRequired").show();
        clearAddForm('supervisor');

        // Hide list action buttons and checkbox
        $('#supListActions').hide();
        $('#sup_list .check').hide();
        $('#subListActions').hide();
        $('#sub_list .check').hide();
        removeEditLinks();
        $('div#messagebar').hide();
        $('#addPaneReportTo').css('display', 'block');
    });

    // Add a subordinate
    $('#btnAddSubordinateDetail').click(function() {

        $('#reportto_subordinateName_empId').val("");
        $('#reportto_supervisorName_empId').val("");
        $("#reportto_previousRecord").val("");
        $('#reportto_type_flag_2').attr('checked', 'checked');
        $("#reportToHeading").text(addSubordinate);
        $('.radio_list').hide();
        $(".paddingLeftRequired").show();
        clearAddForm('subordinate');

        // Hide list action buttons and checkbox
        $('#supListActions').hide();
        $('#sup_list .check').hide();
        $('#subListActions').hide();
        $('#sub_list .check').hide();
        removeEditLinks();
        $('div#messagebar').hide();
        $('#addPaneReportTo').css('display', 'block');
    });

    // Cancel in add pane
    $('#btnCancel').click(function() {
        
        //remove if disabled while edit
        $('#reportto_supervisorName_empName').removeAttr('disabled');
        $('#reportto_subordinateName_empName').removeAttr('disabled');

        clearAddForm('supervisor');
        $('#addPaneReportTo').css('display', 'none');
        $('#supListActions').show();
        $('#sup_list .check').show();
        $('#subListActions').show();
        $('#sub_list .check').show();
        addEditLinks();
        $('div#messagebar').hide();
        $(".paddingLeftRequired").hide();
    });

    $('#delSupBtn').click(function() {
        var checked = $('#frmEmpDelSupervisors input:checked').length;

        if (checked == 0) {
            $("#messagebar").attr("class", "messageBalloon_notice");
            $("#messagebar").text(deleteWarning);
            $('div#messagebar').show();
        } else {
            $('#frmEmpDelSupervisors').submit();
        }
    });

    $('#delSubBtn').click(function() {
        var checked = $('#frmEmpDelSubordinates input:checked').length;

        if (checked == 0) {
            $("#messagebar").attr("class", "messageBalloon_notice");
            $("#messagebar").text(deleteWarning);
            $('div#messagebar').show();
        } else {
            $('#frmEmpDelSubordinates').submit();
        }
    });

    // Edit a supervisor detail in the list
    $('#frmEmpDelSupervisors a').live('click', function() {
        
        if (!($("#frmAddReportTo #nameType").length > 0)) {
            $('<input type="hidden" name="nameType" id="nameType">').insertAfter('#frmAddReportTo #reportto_empNumber');
        }
        $('#frmAddReportTo #nameType').val('supervisor');
        var row = $(this).closest("tr");
        var primarykey = row.find('input.checkboxSup:first').val();
        var tempArray = primarykey.split(" ");
        var name = $(this).text();
        var reportingMethodType = row.find("td:nth-child(3)").text();

        $("#reportto_supervisorName_empId").val(tempArray[0]);
        $("#reportto_previousRecord").val(primarykey);
        $('#reportto_type_flag_1').attr('checked', 'checked');
        $('#reportto_supervisorName_empName').val(name);
        $('#reportto_subordinateName_empName').hide();
        $('label[for="reportto_subordinateName"]').hide();
        $('label[for="reportto_supervisorName"]').show();
        $('#reportto_supervisorName_empName').attr('disabled','disabled');
        $('.radio_list').hide();

        $('#name').text(name);
        $('#name').show();
        $('#reportto_reportingMethodType').val(tempArray[2]);

        $(".paddingLeftRequired").show();
        $("#reportToHeading").text(editSupervisor);
        $('div#messagebar').hide();
        // hide validation error messages

        $('#supListActions').hide();
        $('#sup_list .check').hide();
        $('#subListActions').hide();
        $('#sub_list .check').hide();
        hideShowReportingMethodOther();
        $('#addPaneReportTo').css('display', 'block');
    });


    // Edit a subordinate detail in the list
    $('#frmEmpDelSubordinates a').live('click', function() {

        if (!($("#frmAddReportTo #nameType").length > 0)) {
            $('<input type="hidden" name="nameType" id="nameType">').insertAfter('#frmAddReportTo #reportto_empNumber');
        }
        $('#frmAddReportTo #nameType').val('subordinate');
        var row = $(this).closest("tr");
        var primarykey = row.find('input.checkboxSub:first').val();
        var tempArray = primarykey.split(" ");
        var name1 = $(this).text();
        var reportingMethodType = row.find("td:nth-child(3)").text();

        $("#reportto_subordinateName_empId").val(tempArray[1]);
        $("#reportto_previousRecord").val(primarykey);
        $('#reportto_type_flag_2').attr('checked', 'checked');

        $('#reportto_subordinateName_empName').val(name1);
        $('#reportto_supervisorName_empName').hide();
        $('label[for="reportto_supervisorName"]').hide();
        $('label[for="reportto_subordinateName"]').show();
        $('#reportto_subordinateName_empName').attr('disabled','disabled').show();
        $('.radio_list').hide();

        $('#name').text(name1);
        $('#name').show();
        $('#reportto_reportingMethodType').val(tempArray[2]);

        $(".paddingLeftRequired").show();
        $("#reportToHeading").text(editSubordinate);
        $('div#messagebar').hide();
        // hide validation error messages

        $('#supListActions').hide();
        $('#sup_list .check').hide();
        $('#subListActions').hide();
        $('#sub_list .check').hide();
        hideShowReportingMethodOther();
        $('#addPaneReportTo').css('display', 'block');
    });
});

function hideShowSupervisorSubordinate() {
    
    if (!($("#frmAddReportTo #nameType").length > 0)) {
        $('<input type="hidden" name="nameType" id="nameType">').insertAfter('#frmAddReportTo #reportto_empNumber');
    }
    if ($("#reportto_type_flag_1").is(':checked')) {
        $('#frmAddReportTo #nameType').val('supervisor');
        $('#reportto_supervisorName_empName').show();
        $('label[for="reportto_supervisorName"]').show();
        $('#reportto_subordinateName_empName').val('');
        $('#reportto_subordinateName_empId').val('');
        $('label[for="reportto_subordinateName"]').hide();
        $('#reportto_subordinateName_empName').hide();
    } else if ($("#reportto_type_flag_2").is(':checked')) {
        $('#frmAddReportTo #nameType').val('subordinate');
        $('#reportto_supervisorName_empName').val('');
        $('#reportto_supervisorName_empId').val('');
        $('#reportto_supervisorName_empName').hide();
        $('label[for="reportto_supervisorName"]').hide();
        $('label[for="reportto_subordinateName"]').show();
        $('#reportto_subordinateName_empName').show();
    }
}

function isValidForm(nameType){
    $.validator.addMethod("empNameValidation", function(value, element, params) {
        
        if (params['nameType'] == 'supervisor') {
            var employeesArray = eval(employees_reportto_supervisorName);
            $idElement = $('#reportto_supervisorName_empId');
            $nameElement = $('#reportto_supervisorName_empName');
            if ($('#reportto_supervisorName_empName').val() == '') {
                return false;
            }
        } else if (params['nameType'] == 'subordinate') {
            var employeesArray = eval(employees_reportto_subordinateName);
            $idElement = $('#reportto_subordinateName_empId');
            $nameElement = $('#reportto_subordinateName_empName');
            if ($('#reportto_subordinateName_empName').val() == '') {
                return false;
            }
        }
        
        var temp = false;
        var empDateCount = employeesArray.length;
  
        if ($idElement.val() > 0) {
            temp = true;
        } else {
            var i;
            for (i=0; i < empDateCount; i++) {
                
                empName = $.trim($nameElement.val()).toLowerCase();
                arrayName = employeesArray[i].name.toLowerCase();
                
                if (empName == arrayName) {
                    $idElement.val(employeesArray[i].id);
                    $nameElement.val(employeesArray[i].name);
                    temp = true
                    break;
                }
            }
        }
        
        if (!temp){
            return false;
        }
        else{
            return true;
        }
    });

    $("#frmAddReportTo").validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.prev('label') );
        }
    });
    $("#reportto_reportingMethodType").rules("add", {
        required: true,
        messages: {
            required: reportingMethodTypeIsRequired
        }
    });
    $("#reportto_reportingMethod").rules("add", {
        required: function(element) {
            return $('#reportto_reportingMethodType').val() == -1;
        },
        messages: {
            required: reportingMethodIsRequired
        }
    });
    
    if (nameType == 'supervisor') {
        $("#reportto_subordinateName_empName").rules("remove", "empNameValidation");
        $("#reportto_supervisorName_empName").rules("add", {
                empNameValidation: {value: true, nameType: 'supervisor'},
                messages: {
                    empNameValidation: nameIsRequired
                }
        });
    } else if (nameType == 'subordinate') {
        $("#reportto_supervisorName_empName").rules("remove", "empNameValidation");
        $("#reportto_subordinateName_empName").rules("add", {
            empNameValidation: {value: true, nameType: 'subordinate'},
            messages: {
                empNameValidation: nameIsRequired
            }
        });
    }

    return true;
}



function hideShowReportingMethodOther() {
    
    if ($('#reportto_reportingMethodType').val() != -1 ) {
        $('#pleaseSpecify').hide();
    } else {
        $('#pleaseSpecify').show();
    }
}

function clearAddForm(nameMode) {

    if (!($("#frmAddReportTo #nameType").length > 0)) {
        $('<input type="hidden" name="nameType" id="nameType">').insertAfter('#frmAddReportTo #reportto_empNumber');
    }
    if (nameMode == 'supervisor') {
        $('#frmAddReportTo #nameType').val('supervisor');
        $('#reportto_supervisorName_empName').val('');
        $('#reportto_supervisorName_empName').show();
        $('label[for="reportto_supervisorName"]').show();
        $('label[for="reportto_subordinateName"]').hide();
        $('#reportto_subordinateName_empName').hide();
        if ($("#reportto_supervisorName_empName").val() == '') {
            $("#reportto_supervisorName_empName").val(typeForHints)
            .addClass("inputFormatHint");
        }
    } else if (nameMode == 'subordinate') {
        $('#frmAddReportTo #nameType').val('subordinate');
        $('#reportto_subordinateName_empName').val('');
        $('#reportto_subordinateName_empName').show();
        $('label[for="reportto_subordinateName"]').show();
        $('label[for="reportto_supervisorName"]').hide();
        $('#reportto_supervisorName_empName').hide();
        if ($("#reportto_subordinateName_empName").val() == '') {
            $("#reportto_subordinateName_empName").val(typeForHints)
            .addClass("inputFormatHint");
        }
    }
    
    $('#reportto_reportingMethodType').val('');
    $('div#addPaneReportTo label.error').hide();
    $('#name').hide();
    $('div#messagebar').hide();

    
    hideShowReportingMethodOther();
}

function addEditLinks() {
    // called here to avoid double adding links - When in edit mode and cancel is pressed.
    removeEditLinks();
    if (canUpdateSupervisors){
        $('#sup_list tbody td.supName').wrapInner('<a href="#"/>');
    }
    if (canUpdateSubordinates){
        $('#sub_list tbody td.subName').wrapInner('<a href="#"/>');
    }
}

function removeEditLinks() {
    $('#sup_list tbody td.supName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
    $('#sub_list tbody td.subName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
}
