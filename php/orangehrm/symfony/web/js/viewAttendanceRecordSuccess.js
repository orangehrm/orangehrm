$(document).ready(function(){
    
    if(trigger){
       
        $("#recordsTable").hide();
        getRelatedAttendanceRecords(employeeId,dateSelected,actionRecorder);
        $("#employee").removeClass("inputFormatHint");    
    
        $("#employee").autocomplete(employees, {

            formatItem: function(item) {

                return $("<div/>").html(item.name).text();
            }
            ,
            matchContains:true
        }).result(function(event, item) {
            });

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
    
        $("#employee").click(function(){
            if($("#employee").val() == lang_typeForHints){
                this.value = "";
                $(this).removeClass("inputFormatHint");
            }

        });
    
        $('#attendance_date').change(function() {
            $('#validationMsg').removeAttr('class');
            $('#validationMsg').html("");
            
            var isValidEmployee= validateEmployee();
        
            if(!isValidEmployee) {
				
                $('#validationMsg').attr('class', "messageBalloon_failure");
                $('#validationMsg').html(errorMsge);
				
            }
            else{
                
                var isValidDate= validateInputDate();
                
                if(isValidDate){
                    var empId= $('#attendance_employeeId').val();
                    var date=$(".date").val();
                
                    getRelatedAttendanceRecords(empId,date,actionRecorder);   
                }
            }
        });      
    }
    else{

        $("#recordsTable").hide();
        $("#employee").autocomplete(employees, {

            formatItem: function(item) {

                return $("<div/>").html(item.name).text();
            }
            ,
            matchContains:true
        }).result(function(event, item) {
            });

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
    
        $("#employee").click(function(){
            if($("#employee").val() == lang_typeForHints){
                this.value = "";
                $(this).removeClass("inputFormatHint");
            }
        });
    
        $('#attendance_date').change(function() {
            $('#validationMsg').removeAttr('class');
            $('#validationMsg').html("");
            
            var isValidEmployee= validateEmployee();
        
            if(!isValidEmployee) {
				
                $('#validationMsg').attr('class', "messageBalloon_failure");
                $('#validationMsg').html(errorMsge);
				
            }
            else{
                
                var isValidDate= validateInputDate();
                
                if(isValidDate){
                    var empId= $('#attendance_employeeId').val();
                    var date=$(".date").val();
          
                    getRelatedAttendanceRecords(empId,date,actionRecorder);
                    
                }
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
    
        
    if(!date.isValidDate()){

        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(errorForInvalidFormat);
        $("#attendance_date").attr('style', errorStyle);
        errFlag = true;
    }  
    return !errFlag ;
    
}

function validateEmployee(){
		
    var empCount = employeesArray.length;
        
    var temp = false;
    var i;
        
    if(empCount==0){
            
        errorMsge = lang_noEmployees;
        return false;
    }
    for (i=0; i < empCount; i++) {
        empName = $.trim($('#employee').val()).toLowerCase();
        arrayName = employeesArray[i].name.toLowerCase();
        arrayName= $("<div/>").html(arrayName).text();
        if (empName == arrayName) {
            $('#attendance_employeeId').val(employeesArray[i].id);
            temp = true
            break;
        }
    }
    if(temp){
        return true;
    }else if(empName == "" || empName == $.trim("Type for hints...").toLowerCase()){
        errorMsge = lang_selectEmployee;
        return false;
    }else{
        errorMsge = lang_invalidEmployee;
        return false;
    }
}
    
function getRelatedAttendanceRecords(employeeId, date, actionRecorder){
      
    $.post(
        linkForGetRecords,
        {
            employeeId: employeeId,
            date: date,
            actionRecorder: actionRecorder
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