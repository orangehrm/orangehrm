$(document).ready(function()
    {
      
        var d = new Date()
        var gmtHours = -d.getTimezoneOffset()*60;

        dateTimeFormat= 'yyyy-MM-dd HH:mm';

        $(".date").val(currentDate);
        $(".time").val(currentTime);


        $(".punchInbutton").click(function(){
            if((validateNote())){

                if((validate())) {

                    if((validateForpunchInOverLapping()==1)) {
                        var d = new Date()
                        var gmtHours = -d.getTimezoneOffset()*60;
                        $('form#punchTimeForm').attr({

                            action:linkForPunchIn+"?timeZone="+gmtHours
                        });
                        $('form#punchTimeForm').submit();
               

                    }
                }
            }
        });

        $(".punchOutbutton").click(function(){

            if((validateNote())){
                if((validate())) {
   
                    if(validatePunchOutOverLapping()==1){
                        var d = new Date()
                        var gmtHours = -d.getTimezoneOffset()*60;

                        $('form#punchTimeForm').attr({

                            action:linkForPunchOut+"?timeZone="+gmtHours
                        });
                        $('form#punchTimeForm').submit();

                    
                    }
                }
            }

        });

        //Load default Mask if empty
        var rDate = trim($("#attendance_date").val());
        if (rDate == '') {
            $("#attendance_date").val(datepickerDateFormat);
        }

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

            if(actionPunchOut){

                var flag = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                }

                if(flag){
                    var flag2= validatePunchOutOverLapping();

                    if(flag2==1){
                        $('.punchOutbutton').removeAttr('disabled');
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
                }

                if(flag4){
                    var flag3=validateForpunchInOverLapping();

                    if(flag3==1){
                        $('.punchInbutton').removeAttr('disabled');
                    }

                    if(flag3==0){
                        $('.punchInbutton').attr('disabled', 'disabled');
                    }
                }
            }

        });



        $(".time").change(function() {
            if(actionPunchOut){

                var flag = validate();

                if(!flag) {
                    $('.punchOutbutton').attr('disabled', 'disabled');
                }
                else{
                    $('.punchOutbutton').removeAttr('disabled');
                }

                if(flag){
                    var flag2= validatePunchOutOverLapping();

                    if(flag2==1){
                        $('.punchOutbutton').removeAttr('disabled');
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
                }

                if(flag4){
                    var flag3=validateForpunchInOverLapping();

                    if(flag3==1){
                        $('.punchInbutton').removeAttr('disabled');
                    }

                    if(flag3==0){
                        $('.punchInbutton').attr('disabled', 'disabled');
                    }
                }
            }
        });

        $(".note").change(function() {

            var flag7= validateNote();

            if(!flag7) {
                $('.punchOutbutton').attr('disabled', 'disabled');
                $('.punchInbutton').attr('disabled', 'disabled');
            }
            else{
                $('.punchOutbutton').removeAttr('disabled');
                $('.punchInbutton').removeAttr('disabled');
            }


        });

        function validate() {

            var formtHour;
            var formtMin;
            errFlag = false;
            clearErrors()

            var date=$(".date").val();
            var timeArray=$(".time").val().split(':')
    
            //implement the when - is not there this breaks

            if(!validateDate(date, datepickerDateFormat)){
        
                $('.punchOutbutton').attr('disabled', 'disabled');
                $('#attendance_date').addClass('validation-error');
                $('#dateErrorHolder').text(errorForInvalidDateFormat);
                errFlag = true;
       
            }

            else{
                var formtedFullDate=convertDateToYMDFormat(date);
   
            }


            if((timeArray[0]>24)||(timeArray[0]<0)||(timeArray[1]>59)||(timeArray[1]<0)){

                $('.punchOutbutton').attr('disabled', 'disabled');
                $('#attendance_time').addClass('validation-error');
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
                    $('#attendance_time').addClass('validation-error');
                    $('#timeErrorHolder').text(errorForInvalidFormat);

                    errFlag = true;

                }
                else{


                    punchedTimestamp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);
                    maxTimestamp = strToTime(formtedFullDate+" 24:00", dateTimeFormat);

                    if (punchedTimestamp >= maxTimestamp) {
                        $('#attendance_time').addClass('validation-error');
                        $('#timeErrorHolder').text(errorForInvalidFormat);                        
                        errFlag = true;
                    }


                    var inTime = strToTime(punchInUtcTime, dateTimeFormat);
                    var outTimeTemp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);

                    var outTime=outTimeTemp-gmtHours*1000;
      

                    if (inTime > outTime) {

                        $('.punchOutbutton').attr('disabled', 'disabled');
                        $('#attendance_date').addClass('validation-error');
                        $('#attendance_time').addClass('validation-error');
                        $('#timeErrorHolder').text(errorForInvalidTime); 
                        errFlag = true;
                    }

                }
            }

            return !errFlag ;
        }



        function validatePunchOutOverLapping(){

            clearErrors()           

            var inTime=punchInUtcTime;
            var timezone=gmtHours;
        
            var outTime =convertDateToYMDFormat($(".date").val())+" "+$(".time").val();
            var r = $.ajax({
                type: 'POST',
                url: linkForOverLappingValidation,
                data: "punchInTime="+inTime+"&punchOutTime="+outTime+"&employeeId="+employeeId+"&timezone="+timezone+"&recordId="+recordId,
                async: false,

                success: function(msg){

                    isValid = msg;
                   
         
                }
            });

            if (isValid==0) {

                $('.punchOutbutton').attr('disabled', 'disabled');
                $('#attendance_date').addClass('validation-error');
                $('#attendance_time').addClass('validation-error');
                $('#timeErrorHolder').text(errorForOverLappingTime);                

            }

            return isValid;

        }

        function validateForpunchInOverLapping(){

            clearErrors()         

            var inTime =convertDateToYMDFormat($(".date").val())+" "+$(".time").val();
            var timezone=gmtHours;

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
                $('#attendance_date').addClass('validation-error');
                $('#attendance_time').addClass('validation-error');
                $('#timeErrorHolder').text(errorForOverLappingTime);                

            }

            return isValid;
        }
    });
    
    
function validateNote(){

    errFlag1 = false;

    $('.punchOutbutton').removeAttr('disabled');
    $('.punchInbutton').removeAttr('disabled');
    clearErrors()    

    if ($(".note").val().length > 250) {

        $('.punchOutbutton').attr('disabled', 'disabled');
        $('.punchInbutton').attr('disabled', 'disabled');
        $('#attendance_note').addClass('validation-error');
        $('#noteErrorHolder').text(errorForInvalidNote);          
                  
        errFlag1 = true;
    }

    return !errFlag1;

}

function convertDateToYMDFormat(date){
    var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
    return $.datepicker.formatDate("yy-mm-dd", parsedDate);
}

function clearErrors() {
    $('#dateErrorHolder').empty();
    $('#attendance_date').removeClass('validation-error');
    $('#timeErrorHolder').empty();
    $('#attendance_time').removeClass('validation-error');
    $('#noteErrorHolder').empty();
    $('#attendance_note').removeClass('validation-error');    
}





