$(document).ready(function() {

    hideShowReportingMethodOther()

    if(essMode){
        $('#supListActions').hide();
        $('#sup_list td.check').hide();
        $('#subListActions').hide();
        $('#sub_list td.check').hide();
        removeEditLinks();
    }

    if ($("#reportto_name").val() == '') {
        $("#reportto_name").val(typeForHints)
        .addClass("inputFormatHint");
    }

    $('#reportto_reportingModeType').change(function() {
        hideShowReportingMethodOther();
    });

    $("#reportto_name").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    $("#reportto_name").click(function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
    //Auto complete
    $("#reportto_name").autocomplete(employees, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        $("#reportto_selectedEmployee").val(item.id);
    });

    $('#btnSaveReportTo').click(function() {
        $('#frmAddReportTo').submit();
    });

    $("#checkAllSup").click(function(){
        if($("#checkAllSup:checked").attr('value') == 'on') {
            $(".checkboxSup").attr('checked', 'checked');
        } else {
            $(".checkboxSup").removeAttr('checked');
        }
    });

    if($(".checkboxSup").length > 1 || $(".checkboxSub").length > 1) {
        $(".paddingLeftRequired").hide();
        $("#addPaneReportTo").hide();
    } else {
        $("#btnCancel").hide();
        $(".paddingLeftRequired").show();
        $("#addPaneReportTo").show();
        $("#listReportToDetails").hide();
    }

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


        $('#reportto_type_flag_1').attr('checked', 'checked');
        $("#reportToHeading").text(addSupervisor);
        $('.radio_list').hide();
        $(".paddingLeftRequired").show();
        clearAddForm();

        // Hide list action buttons and checkbox
        $('#supListActions').hide();
        $('#sup_list td.check').hide();
        $('#subListActions').hide();
        $('#sub_list td.check').hide();
        removeEditLinks();
        $('div#messagebar').hide();
        $('#addPaneReportTo').css('display', 'block');
    });

    // Add a subordinate
    $('#btnAddSubordinateDetail').click(function() {

        $('#reportto_type_flag_2').attr('checked', 'checked');
        $("#reportToHeading").text(addSubordinate);
        $('.radio_list').hide();
        $(".paddingLeftRequired").show();
        clearAddForm();

        // Hide list action buttons and checkbox
        $('#supListActions').hide();
        $('#sup_list td.check').hide();
        $('#subListActions').hide();
        $('#sub_list td.check').hide();
        removeEditLinks();
        $('div#messagebar').hide();
        $('#addPaneReportTo').css('display', 'block');
    });

    // Cancel in add pane
    $('#btnCancel').click(function() {
        clearAddForm();
        $('#addPaneReportTo').css('display', 'none');
        $('#supListActions').show();
        $('#sup_list td.check').show();
        $('#subListActions').show();
        $('#sub_list td.check').show();
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

        var row = $(this).closest("tr");
        var primarykey = row.find('input.checkboxSup:first').val();
        var tempArray = primarykey.split(" ");
        var name = $(this).text();
        var reportingMethodType = row.find("td:nth-child(3)").text();

        $("#reportto_selectedEmployee").val(tempArray[0]);
        $("#reportto_previousRecord").val(primarykey);
        $('#reportto_type_flag_1').attr('checked', 'checked');
        $('#reportto_name').val(name);
        $('#reportto_name').hide();
        $('.radio_list').hide();

        $('#name').text(name);
        $('#name').show();
        $('#reportto_reportingModeType').val(reportingMethodType);

        $(".paddingLeftRequired").show();
        $("#reportToHeading").text(editSupervisor);
        $('div#messagebar').hide();
        // hide validation error messages

        $('#supListActions').hide();
        $('#sup_list td.check').hide();
        $('#subListActions').hide();
        $('#sub_list td.check').hide();
        hideShowReportingMethodOther()
        $('#addPaneReportTo').css('display', 'block');
    });

    // Edit a subordinate detail in the list
    $('#frmEmpDelSubordinates a').live('click', function() {

        var row = $(this).closest("tr");
        var primarykey = row.find('input.checkboxSub:first').val();
        var tempArray = primarykey.split(" ");
        var name1 = $(this).text();
        var reportingMethodType = row.find("td:nth-child(3)").text();

        $("#reportto_selectedEmployee").val(tempArray[1]);
        $("#reportto_previousRecord").val(primarykey);
        $('#reportto_type_flag_2').attr('checked', 'checked');

        $('#reportto_name').val(name1);
        $('#reportto_name').hide();
        $('.radio_list').hide();

        $('#name').text(name1);
        $('#name').show();
        $('#reportto_reportingModeType').val(reportingMethodType);

        $(".paddingLeftRequired").show();
        $("#reportToHeading").text(editSubordinate);
        $('div#messagebar').hide();
        // hide validation error messages

        $('#supListActions').hide();
        $('#sup_list td.check').hide();
        $('#subListActions').hide();
        $('#sub_list td.check').hide();
        hideShowReportingMethodOther()
        $('#addPaneReportTo').css('display', 'block');
    });

    $.validator.addMethod("empNameValidation", function(value, element, params) {
        var temp = false;
        if($('#reportto_selectedEmployee').val() > 0){
            temp = true;
        }

        else{

            var empDateCount = employeesArray.length;
      
            var i;
            for (i=0; i < empDateCount; i++) {
                empName = $.trim($('#reportto_name').val()).toLowerCase();
                arrayName = employeesArray[i].name.toLowerCase();

                if (empName == arrayName) {
                    $('#reportto_selectedEmployee').val(employeesArray[i].id);
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

        rules: {
            'reportto[name]' : {
                empNameValidation: true
            },
            'reportto[reportingModeType]' : {
                required: true
            },
            'reportto[reportingMethod]':{
                required: function(element) {
                    return $('#reportto_reportingModeType').val() == -1;
                }
            }

        },
        messages: {
            'reportto[name]' : {
                empNameValidation: nameIsRequired

            },
            'reportto[reportingModeType]' :{        
                required: reportingMethodTypeIsRequired
            },
            'reportto[reportingMethod]':{
                required: reportingMethodIsRequired
            }

        },
        errorPlacement: function(error, element) {
            error.appendTo( element.prev('label') );
        }

    });


});

function hideShowReportingMethodOther() {
    
    if ($('#reportto_reportingModeType').val() != -1 ) {
        $('#pleaseSpecify').hide();
    } else {
        $('#pleaseSpecify').show();
    }
}

function clearAddForm() {

    $('#reportto_name').val('');
    $('#reportto_name').show();
    $('#reportto_reportingModeType').val('');
    $('div#addPaneReportTo label.error').hide();
    $('#name').hide();
    $('div#messagebar').hide();

    if ($("#reportto_name").val() == '') {
        $("#reportto_name").val(typeForHints)
        .addClass("inputFormatHint");
    }
    hideShowReportingMethodOther()
}

function addEditLinks() {
    // called here to avoid double adding links - When in edit mode and cancel is pressed.
    removeEditLinks();
    $('#sup_list tbody td.supName').wrapInner('<a href="#"/>');
    $('#sub_list tbody td.subName').wrapInner('<a href="#"/>');
}

function removeEditLinks() {
    $('#sup_list tbody td.supName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
    $('#sub_list tbody td.subName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
}
