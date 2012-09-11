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

    //Bind date picker
    daymarker.bindElement("#attendance_date",
    {
        dateFormat : datepickerDateFormat,
        onClose: function() {
            $(this).valid();
        }
    });

    $('#attendance_date_Button').click(function(){
        daymarker.show("#attendance_date");
    });
    
    if(trigger){
        autoFillEmpName(employeeId);
        $("#reportForm").submit();     
    }

    $('#btView').click(function() {
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");
        var validEmp = true;
        
        autoFill('attendance_employeeName_empName', 'attendance_employeeName_empId', employees_attendance_employeeName);
        
        if($('#attendance_employeeName_empName').val() != '' && $('#attendance_employeeName_empName').val() != typeForHints && $('#attendance_employeeName_empName').val() != employeeAll && $('#attendance_employeeName_empId').val() == ''){

            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(invalidEmpName);
            $("#attendance_employeeName_empName").attr('style', errorStyle);
            validEmp = false;
        }    

        var isValidDate= validateInputDate();
                
        if(isValidDate && validEmp){
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

function validateInputDate(){

    errFlag = false;
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");
    $(".date").removeAttr('style');

    var errorStyle = "background-color:#FFDFDF;";
          
    if((!validateDate($("#attendance_date").val(), datepickerDateFormat))){

        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(errorForInvalidFormat);
        $("#attendance_date").attr('style', errorStyle);
        errFlag = true;
    }  
    return !errFlag ;
    
}