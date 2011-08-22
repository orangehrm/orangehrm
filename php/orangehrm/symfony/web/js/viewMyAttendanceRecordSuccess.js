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
            $("#attendance_date").val(dateDisplayFormat);
        }

        //Bind date picker
        daymarker.bindElement("#attendance_date",
        {
            onSelect: function(date){
              
                $("#attendance_date").trigger('change');            

            },
            dateFormat:jsDateFormat
        });

        $('#DateBtn').click(function(){


            daymarker.show("#attendance_date");


        });
    
        $('#attendance_date').change(function() {
    
            var isValidDate= validateInputDate();
                
            if(isValidDate){
            
                var date=$(".date").val();
                
                getRelatedAttendanceRecords(employeeId,date,actionRecorder);
            //  proxyPunchInOut(empId);
                    
                    
            }  
        
    

        });
    }
    
    else{
        
        $("#recordsTable").hide();
     
        var rDate = trim($("#attendance_date").val());
        if (rDate == '') {
            $("#attendance_date").val(dateDisplayFormat);
        }

        //Bind date picker
        daymarker.bindElement("#attendance_date",
        {
            onSelect: function(date){
              
                $("#attendance_date").trigger('change');            

            },
            dateFormat:jsDateFormat
        });

        $('#DateBtn').click(function(){


            daymarker.show("#attendance_date");


        });
    
        $('#attendance_date').change(function() {
    
            var isValidDate= validateInputDate();
                
            if(isValidDate){
            
                var date=$(".date").val();
                
                getRelatedAttendanceRecords(employeeId,date,actionRecorder);
            //  proxyPunchInOut(empId);
                    
                    
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
        
    var dateArray=$(".date").val().split('-');
    
    if((dateArray[1]<1)||(dateArray[1]>12)||(dateArray[2]>31)||(dateArray[2]<1)){
        
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
