$(document).ready(function()
    {

        dateTimeFormat= 'yyyy-MM-dd HH:mm';

        $(".time").val(currentTime);

        $(".punchInbutton").click(function(){
            
            
            var timezoneArray= getTimezoneArray();
            var  timezone=  timezoneArray[$(".timezone").val()];
            if(timezone == 'GMT'){
                timeZone=0;
            }
            var  timeZone=timezone*3600;
            if((validateNote())){
                if((validate())) {
                    if((validateForpunchInOverLapping()==1)) {
                       
                        $('form#punchTimeForm').attr({
                            action:linkForProxyPunchAction+"?timeZone="+timeZone+"&path="+true+"&employeeId="+employeeId+"&date="+selectedDate
                        });
                        $('form#punchTimeForm').submit();
               

                    }
                }
            }
        });

        $(".punchOutbutton").click(function(){
            var timezoneArray= getTimezoneArray();
            var  timezone=  timezoneArray[$(".timezone").val()];
             if(timeZone == 'GMT'){
                        timeZone=0;
                        
                    }
            var  timeZone= timezone*3600;
            if((validateNote())){
                if((validate())) {
   
                    if(validatePunchOutOverLapping()==1){
  
                        $('form#punchTimeForm').attr({

                            action:linkForProxyPunchAction+"?timeZone="+timeZone+"&path="+true+"&employeeId="+employeeId+"&date="+selectedDate
                        });
                        $('form#punchTimeForm').submit();

                    
                    }
                }
            }

        });

        function strToTime(str, format) {

            yearVal = '';
            monthVal = '';
            dateVal = '';
            hourVal = '';
            minuteVal = '';
            aVal = '';

            if (str.length != format.length) {
                return false;
            }

            j=0;
            for (i=0; i<format.length; i++) {

                ch = format.charAt(j);
                sCh = str.charAt(i);

                if (ch == 'd') {
                    dateVal = dateVal.toString()+sCh;
                } else if (ch == 'M') {
                    monthVal = monthVal.toString()+sCh;
                } else if (ch == 'y') {
                    yearVal = yearVal.toString()+sCh;
                } else if (ch == 'H') {
                    hourVal = hourVal.toString()+sCh;
                } else if (ch == 'h') {
                    hourVal = hourVal.toString()+sCh;
                    if (hourVal > 12) return false;
                } else if (ch == 'm') {
                    minuteVal = minuteVal.toString()+sCh;
                } else if (ch == 'a') {
                    sCh = sCh+str.charAt(i+1);
                    if (sCh == 'PM') {
                        hourVal+=12;
                    } else if (sCh != 'AM') {
                        return false;
                    }
                    i++;
                } else {
                    if (ch != sCh) {
                        return false;
                    }
                }
                j++;
            }

            if ((monthVal < 1) || (monthVal > 12) || (dateVal < 1) || (dateVal > 31) || (hourVal > 24) || (minuteVal > 59)) {
                return false;
            }
            date = new Date(yearVal, monthVal-1, dateVal, hourVal, minuteVal);


            return date.getTime();

        }

        $("#attendance_date").change(function() {

            if(punchOut){

                var flag = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                    $(".time").removeClass('validation-error');
                    $("#attendance_date").removeClass('validation-error');
                }

                if(flag){
                    var flag2= validatePunchOutOverLapping();

                    if(flag2==1){
                        $('.punchOutbutton').removeAttr('disabled');
                        $(".time").removeClass('validation-error');
                        $(".date").removeClass('validation-error');
                    }

                    if(flag2==0){

                        $('.punchOutbutton').attr('disabled', 'disabled');

                    }
                }
            }

            else{

                var flag4 = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                    $(".time").removeClass('validation-error');
                    $("#attendance_date").removeClass('validation-error');
                }

                if(flag4){
                    var flag3=validateForpunchInOverLapping();

                    if(flag3==1){

                        $('.punchInbutton').removeAttr('disabled');
                        $(".time").removeClass('validation-error');
                        $(".date").removeClass('validation-error');

                    }

                    if(flag3==0){

                        $('.punchInbutton').attr('disabled', 'disabled');

                    }
                }
            }

        });



        $(".time").change(function() {
            if(punchOut){

                var flag = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                    $(".time").removeClass('validation-error');
                    $("#attendance_date").removeClass('validation-error');
                }

                if(flag){
                    var flag2= validatePunchOutOverLapping();

                    if(flag2==1){
                        $('.punchOutbutton').removeAttr('disabled');
                        $(".time").removeClass('validation-error');
                        $(".date").removeClass('validation-error');
                    }

                    if(flag2==0){

                        $('.punchOutbutton').attr('disabled', 'disabled');

                    }
                }
            }

            else{

                var flag4 = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                    $(".time").removeClass('validation-error');
                    $("#attendance_date").removeClass('validation-error');
                }

                if(flag4){
                    var flag3=validateForpunchInOverLapping();

                    if(flag3==1){

                        $('.punchInbutton').removeAttr('disabled');
                        $(".time").removeClass('validation-error');
                        $(".date").removeClass('validation-error');

                    }

                    if(flag3==0){

                        $('.punchInbutton').attr('disabled', 'disabled');

                    }
                }
            }
        });

        $(".note").change(function() {
            validateNote();
        });

        function validate() {

            clearErrors();

            var formtHour;
            var formtMin;
            errFlag = false;
 
            var date=$(".date").val();
            var timeArray=$(".time").val().split(':')

            //implement the when - is not there this breaks

            if(!validateDate(date, datepickerDateFormat)){
        
                $('.punchOutbutton').attr('disabled', 'disabled');
                $("#attendance_date").addClass("validation-error");
                $('#dateErrorHolder').text(errorForInvalidDateFormat);
                errFlag = true;
       
            }

            else{
                var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
                var formtedFullDate=$.datepicker.formatDate("yy-mm-dd", parsedDate);

   
            }

            if((timeArray[0]>24)||(timeArray[0]<0)||(timeArray[1]>59)||(timeArray[1]<0)){

                $('.punchOutbutton').attr('disabled', 'disabled');
                $(".time").addClass("validation-error");
                $('#timeErrorHolder').text(errorForInvalidTimeFormat);
                errFlag = true;

            }

            else{

                if(timeArray[0]<10){
                    if(timeArray[0].search([0])== -1){

                        formtHour="0"+timeArray[0];
                    }
                    else{

                        formtHour=timeArray[0];
                    }

                }

                else{
                    formtHour=timeArray[0];
                }

                if(timeArray[1]<10){

                    if(timeArray[1].search([0])== -1){

                        formtMin="0"+timeArray[1];
                    }

                    else{

                        formtMin=timeArray[1];
                    }

                }

                else{

                    formtMin=timeArray[1];  
                }

                var formtdFullTime=formtHour+":"+formtMin;

            }


            if(!errFlag){

                if (!strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat)) {

                    $('.punchOutbutton').attr('disabled', 'disabled');
                    $("#attendance_date").addClass("validation-error");
                    $(".time").addClass("validation-error");
                    $('#timeErrorHolder').text(errorForInvalidFormat);
                    errFlag = true;

                }
                else{


                    punchedTimestamp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);
                    maxTimestamp = strToTime(formtedFullDate+" 24:00", dateTimeFormat);

                    if (punchedTimestamp >= maxTimestamp) {
                        alert("Invalid Max Time");
                        errFlag = true;
                    }


                    var inTime = strToTime(punchInUtcTime, dateTimeFormat);
                    var outTimeTemp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);
                    var timezoneArray= getTimezoneArray();
                
                    var  timeZone=  timezoneArray[$(".timezone").val()];
                    if(timeZone == 'GMT'){
                        timeZone=0;
                    }
                     
           
                    var outTime=outTimeTemp-timeZone*3600*1000;
                   

                    if (inTime > outTime) {

                        $('.punchOutbutton').attr('disabled', 'disabled');
                        $(".time").addClass("validation-error");
                        $("#attendance_date").addClass("validation-error");
                        $('#timeErrorHolder').text(errorForInvalidTime);
                        errFlag = true;
                    }
                }
            }

            return !errFlag ;
        }




        function validatePunchOutOverLapping(){

            clearErrors();

            var timezoneArray= getTimezoneArray();
            var  timeZone=  timezoneArray[$(".timezone").val()];
              if(timeZone == 'GMT'){
                        timeZone=0;
                        
                    }
            var inTime=punchInUtcTime;
            var timezone=timeZone*3600;
 
            var outTime =convertDateToYMDFormat($(".date").val())+" "+$(".time").val();

            var r = $.ajax({
                type: 'POST',
                url: linkForOverLappingValidation,
                data: "punchInTime="+inTime+"&punchOutTime="+outTime+"&employeeId="+employeeId+"&timezone="+timezone,
                async: false,

                success: function(msg){

                    isValid = msg;
         
                }
            });

            if (isValid==0) {

                $('.punchOutbutton').attr('disabled', 'disabled');
                $(".time").addClass("validation-error");
                $("#attendance_date").addClass("validation-error");
                $('#timeErrorHolder').text(errorForOverLappingTime);

            }

            return isValid;
        }



        function validateForpunchInOverLapping(){
            
            clearErrors();
            
            var inTime =convertDateToYMDFormat($(".date").val())+" "+$(".time").val();
            var timezoneArray= getTimezoneArray();
            var  timeZone=  timezoneArray[$(".timezone").val()];
             if(timeZone == 'GMT'){
                        timeZone=0;
                     
                    }
            
            var  timezone=timeZone*3600;
    
            var r = $.ajax({
                type: 'POST',
                url: linkForPunchInOverlappingValidation,
                data: "punchInTime="+inTime+"&employeeId="+employeeId+"&timezone="+timezone,
                async: false,

                success: function(msg){
                    isValid = msg;

                }
            });

            if (isValid==0) {

                $('.punchInbutton').attr('disabled', 'disabled');
                $(".time").addClass("validation-error");
                $("#attendance_date").addClass("validation-error");
                $('#timeErrorHolder').text(errorForOverLappingTime);
                
            }

            return isValid;
        }

        function getCurrentTime(){
          var r = $.ajax({
                type: 'POST',
                url: getCurrentTimeLink,
                async: false,

                success: function(msg){
                    isValid = msg[0];

            

                }
            });

        }
    });

function getTimezoneArray() {

    var timezoneArray=new Array();
    timezoneArray[0] = 'GMT';
    timezoneArray[1] = '1.0';
    timezoneArray[2] = '2.0';
    timezoneArray[3] = '3.0';
    timezoneArray[4] = '4.0';
    timezoneArray[5] = '5.0';
    timezoneArray[6] = '5.5';
    timezoneArray[7] = '6.0';
    timezoneArray[8] = '7.0';
    timezoneArray[9] = '8.0';
    timezoneArray[10] = '9.0';
    timezoneArray[11] = '9.5';
    timezoneArray[12] = '+10.00';
    timezoneArray[13] = '+11.00';
    timezoneArray[14] = '+12.00';
    timezoneArray[15] = '-11.00';
    timezoneArray[16] = '-10.00';
    timezoneArray[17] = '-9.00';
    timezoneArray[18] = '-8.00';
    timezoneArray[19] = '-7.00';
    timezoneArray[20] = '-6.00';
    timezoneArray[21] = '-5.00';
    timezoneArray[22] = '-4.00';
    timezoneArray[23] = '-3.50';
    timezoneArray[24] = '-3.00';
    timezoneArray[25] = '-2.00';
    timezoneArray[26] = '-1.00';

    return timezoneArray;
}

function convertDateToYMDFormat(date){
    var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
    return $.datepicker.formatDate("yy-mm-dd", parsedDate);
}

        function validateNote(){

            clearErrors();
            errFlag1 = false;

            if ($(".note").val().length > 250) {
                $('.punchOutbutton').attr('disabled', 'disabled');
                $('.punchInbutton').attr('disabled', 'disabled');
                $(".note").addClass("validation-error");
                $('#noteErrorHolder').text(errorForInvalidNote);
                  
                errFlag1 = true;
            }

            return !errFlag1;

        }
        
function clearErrors() {
    $('#dateErrorHolder').empty();
    $('#attendance_date').removeClass('validation-error');
    $('#timeErrorHolder').empty();
    $('#attendance_time').removeClass('validation-error');
    $('#timezoneErrorHolder').empty();
    $('#attendance_timezone').removeClass('validation-error');    
    $('#noteErrorHolder').empty();
    $('#attendance_note').removeClass('validation-error');    
}