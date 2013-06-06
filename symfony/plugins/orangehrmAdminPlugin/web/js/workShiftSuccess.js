$(document).ready(function() {
       
    $('#btnAssignEmployee').click(function() {  
        return !$('#workShift_availableEmp option:selected').remove().appendTo('#workShift_assignedEmp');  
    }); 
    
    $('#btnRemoveEmployee').click(function() {  
        return !$('#workShift_assignedEmp option:selected').remove().appendTo('#workShift_availableEmp');  
    }); 
    
    $('#btnSave').click(function() {
        var selected = $.map( $('#workShift_assignedEmp option'),
            function(e) {
                return $(e).val();
            } );
        $('#workShift_assignedEmp').val(selected);
        $('#frmWorkShift').submit();

    });
    
    $('#btnAdd').click(function() {
        resetMultipleSelectBoxes();
        
        $('#workShift_name').val('');
        $('#workShift_workHours_from').val(defaultStartTime);
        $('#workShift_workHours_to').val(defaultEndTime);
        $('#workShift_workShiftId').val('');
        fillTotalTime();
        $('#workShiftHeading').html(lang_addWorkShift);
        $('#workShift').show();
        $('.top').hide();        
        $(".messageBalloon_success").remove();
    });
    
    $('#btnCancel').click(function() {
        $('#workShift').hide();
        $('.top').show();
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

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
    
    // Bind On change event of From Time
    $('#workShift_workHours_from').change(function() {
        fillTotalTime();
    });

    // Bind On change event of To Time
    $('#workShift_workHours_to').change(function() {
        fillTotalTime();
    });    
        
    $.validator.addMethod("uniqueName", function(value, element, params) {
        
        var temp = true;
        var currentShift;
        var id = $('#workShift_workShiftId').val();
        var vcCount = workShiftList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == workShiftList[j].id){
                currentShift = j;
            }
        }
        var i;
        vcName = $.trim($('#workShift_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = workShiftList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentShift != null){
            if(vcName == workShiftList[currentShift].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    $.validator.addMethod("validWorkHours", function(value, element) {
        var valid = true;

        var totalTime = getTotalTime();
        if (parseFloat(totalTime) <= 0) {
            valid = false;
        }

        return valid;  
    });
        
        
    var validator = $("#frmWorkShift").validate({

        rules: {
            'workShift[name]' : {
                required:true,
                uniqueName: true,
                maxlength: 50
            },
            'workShift[workHours][from]':{
                required: true, 
                validWorkHours: true
            },
            'workShift[workHours][to]':{
                required: true
            }
        },
        messages: {
            'workShift[name]' : {
                required: lang_Required,
                uniqueName: lang_nameAlreadyExist,
                maxlength: lang_exceed50Charactors
            },
            'workShift[workHours][from]':{
                required : lang_Required,
                validWorkHours: lang_FromTimeLessThanToTime
            },
            'workShift[workHours][to]':{
                required : lang_Required
            }            
        }

    });
});

function fillTotalTime() {        
    var total = getTotalTime();
    if (isNaN(total)) {
        total = '';
    }

    $('input.time_range_duration').val(total);
    $('#workShift_workHours_from').valid();
    $('#workShift_workHours_to').valid();
}

function getTotalTime() {
    var total = 0;
    var fromTime = ($('#workShift_workHours_from').val()).split(":");
    var fromdate = new Date();
    fromdate.setHours(fromTime[0],fromTime[1]);
        
    var toTime = ($('#workShift_workHours_to').val()).split(":");
    var todate = new Date();
    todate.setHours(toTime[0],toTime[1]);        
        
    var difference = todate - fromdate;
    var floatDeference	=	parseFloat(difference/3600000) ;
    total = Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2);
        
    return total;        
}

function getWorkShiftInfo(url){
    
    $.getJSON(url, function(data) {
        $('#workShift_workShiftId').val(data.id);
        $('#workShift_name').val(data.name);
        $('#workShift_workHours_from').val(data.start_time);
        $('#workShift_workHours_to').val(data.end_time);
        fillTotalTime();
        $('#workShift').show();
        $(".messageBalloon_success").remove();
        $('.top').hide();
    });
}

function getWorkShiftEmpInfo(url){
    
    $.getJSON(url, function(data) {
        
        resetMultipleSelectBoxes();
        $.each(data, function() {
   
            var option = new Option(this.empName, this.empNo);
            // Use Jquery to get select list element
            var dropdownList = $("#workShift_assignedEmp")[0];

            if ($.browser.msie) {
                dropdownList.add(option);
            }
            else {
                dropdownList.add(option, null);
            }
            $("#workShift_availableEmp option[value='"+this.empNo+"']").remove();
        });
    });
}

function resetMultipleSelectBoxes(){
    
    $('#workShift_assignedEmp')[0].options.length = 0;
    $('#workShift_availableEmp')[0].options.length = 0;

    for(var i=0; i<employeeList.length; i++){
        $('#workShift_availableEmp').
        append($("<option></option>").
            attr("value",employeeList[i].id).
            text(employeeList[i].name)); 
    }
} 