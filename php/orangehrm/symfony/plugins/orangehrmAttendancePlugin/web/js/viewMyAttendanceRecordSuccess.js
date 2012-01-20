/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function()
{
    if(trigger){
        $("#recordsTable").hide();
     
        getRelatedAttendanceRecords(employeeId,dateSelected,actionRecorder);
        var rDate = trim($("#attendance_date").val());
        if (rDate == '') {
            $("#attendance_date").val(datepickerDateFormat);
        }

        //Bind date picker
        daymarker.bindElement("#attendance_date",
        {
            onSelect: function(date){
              
                $("#attendance_date").trigger('change');            

            },
            dateFormat:datepickerDateFormat
        });

        $('#DateBtn').click(function(){
            daymarker.show("#attendance_date");

        });
    
        $('#attendance_date').change(function() {
            var isValidDate= validateInputDate();
                
            if(isValidDate){
                   
                var date=$(".date").val();
                var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
                getRelatedAttendanceRecords(employeeId,$.datepicker.formatDate("yy-mm-dd", parsedDate),actionRecorder);
            }  

        });
    }
    
    else{
        
        $("#recordsTable").hide();
     
        var rDate = trim($("#attendance_date").val());
        if (rDate == '') {
            $("#attendance_date").val(datepickerDateFormat);
        }

        //Bind date picker
        daymarker.bindElement("#attendance_date",
        {
            onSelect: function(date){
              
                $("#attendance_date").trigger('change');            

            },
            dateFormat:datepickerDateFormat
        });

        $('#DateBtn').click(function(){


            daymarker.show("#attendance_date");


        });
    
        $('#attendance_date').change(function() {
    
            var isValidDate= validateInputDate();
                
            if(isValidDate){
                   
                var date=$(".date").val();
               var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
                
                getRelatedAttendanceRecords(employeeId,$.datepicker.formatDate("yy-mm-dd", parsedDate),actionRecorder);
                    
            }  

        });       
        
    }
    
});
function validateInputDate(){
  
    errFlag = false;
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");
    $(".date").removeAttr('style');

    var errorStyle = "background-color:#FFDFDF;";
        
    var date=$(".date").val();
    
        
    if(!validateDate(date, datepickerDateFormat)){
        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(errorForInvalidFormat);
        $("#attendance_date").attr('style', errorStyle);
        errFlag = true;
    }   
    return !errFlag ;
    
}

function getRelatedAttendanceRecords(employeeId,date,actionRecorder){
        
    $.post(
        linkForGetRecords,
        {
            employeeId: employeeId,
            date: date,
            actionRecorder:actionRecorder
        },
        
        function(data, textStatus) {
                      
            if( data != ''){
                $("#recordsTable").show();
                $('#recordsTable1').html(data);    
            }
                    
        });
                    
    return false;
        
}

String.prototype.isValidDate = function() {
    var IsoDateRe = new RegExp("^([0-9]{4})-([0-9]{2})-([0-9]{2})$");
    var matches = IsoDateRe.exec(this);
    if (!matches) return false;
  

    var composedDate = new Date(matches[1], (matches[2] - 1), matches[3]);

    return ((composedDate.getMonth() == (matches[2] - 1)) &&
        (composedDate.getDate() == matches[3]) &&
        (composedDate.getFullYear() == matches[1]));

}