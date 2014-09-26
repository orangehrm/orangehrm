$(document).ready(function(){
    
    if(employeeId != '') {
        $('#employeeRecordsForm').append($('.actionbar > .formbuttons').html());
        $('.actionbar > .formbuttons').html('');
        $('.actionbar > .formbuttons').html($('#formbuttons').html());
        $('#formbuttons').html('');
    }
    
    var rDate = trim($("#attendance_date").val());
    if (rDate == '') {
        $("#attendance_date").val(displayDateFormat);
    }

    if(trigger){
        autoFillEmpName(employeeId);
        $("#reportForm").submit();     
    }

    $('#btView').click(function() {
        if(isValidForm()){
            $("#reportForm").submit();                 
        }
    });
    
    $("#attendance_employeeName_empName").change(function(){
        autoFill('attendance_employeeName_empName', 'attendance_employeeName_empId', employees_attendance_employeeName);
    });

    function autoFill(selector, filler, data) {
        $("#" + filler).val("");
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                return true;
            }
        });
    }
        
    function autoFillEmpName(employeeId) {
        $("#attendance_employeeName_empId").val("");
        $.each(employees_attendance_employeeName, function(index, item){
            if(item.id == employeeId) {
                $("#attendance_employeeName_empId").val(item.id);
                $("#attendance_employeeName_empName").val(item.name);
                return true;
            }
        });
    }
}); //ready

function isValidForm(){
    var validator = $("#reportForm").validate({
        rules: {
            'attendance[employeeName][empName]' : {
                required: false,
                validEmployeeName: true
            },
            'attendance[date]' : {
                required: true, 
                valid_date: function() {
                    return {
                        format: datepickerDateFormat, 
                        required: true, 
                        displayFormat: displayDateFormat
                    } 
                } 
            }
        },
        messages: {
            'attendance[employeeName][empName]' : {
                validEmployeeName: invalidEmpName
            },
            'attendance[date]' : {
                required: lang_NameRequired,
                valid_date: errorForInvalidFormat
            }
        }
    });
    return true;
}

$.validator.addMethod("validEmployeeName", function(value, element) {      
    return autoFill('attendance_employeeName_empName', 'attendance_employeeName_empId', employees_attendance_employeeName);
});

function autoFill(selector, filler, data) {
    $("#" + filler).val("");
    var valid = false;
    if($("#" + selector).val() == typeForHints || $("#" + selector).val() == '') {
        valid = true;
    } else {
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
    }
    return valid;
}