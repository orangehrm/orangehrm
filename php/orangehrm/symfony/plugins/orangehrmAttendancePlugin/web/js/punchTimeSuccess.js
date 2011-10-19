$(document).ready(function(){

    dateTimeFormat= 'yyyy-MM-dd HH:mm';

    alert(currentDate)

    $(".date").val(currentDate);
    $(".time").val(currentTime);






    $(".punchInbutton").click(function(){

      //  if((validateForpunchInOverLapping()==1)) {
            $('form#punchTimeForm').attr({

                action:linkForPunchIn+"?timeZone="+gmtHours
            });
            $('form#punchTimeForm').submit();
       // }
    });

    $(".punchOutbutton").click(function(){

                $('form#punchTimeForm').attr({

                    action:linkForPunchOut+"?timeZone="+gmtHours
                });
                $('form#punchTimeForm').submit();

    });


 if(editMode){


//Load default Mask if empty
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
            $('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('.punchOutbutton').removeAttr('disabled');
            $(".time").removeAttr('style');
            $("#attendance_date").removeAttr('style');
        }

        if(flag){
            var flag2= validatePunchOutOverLapping();

            if(flag2==1){
                $('.punchOutbutton').removeAttr('disabled');
                $(".time").removeAttr('style');
                $(".date").removeAttr('style');
            }

            if(flag2==0){

                $('.punchOutbutton').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");

            }
        }
       }

       else{

         var flag3=validateForpunchInOverLapping();

        if(flag3==1){

             $('.punchInbutton').removeAttr('disabled');
                $(".time").removeAttr('style');
                $(".date").removeAttr('style');

        }

        if(flag3==0){

             $('.punchInbutton').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");

        }}

    });



    $(".time").change(function() {
         if(actionPunchOut){

        var flag = validate();

        if(!flag) {
            $('.punchOutbutton').attr('disabled', 'disabled');
            $('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('.punchOutbutton').removeAttr('disabled');
            $(".time").removeAttr('style');
            $("#attendance_date").removeAttr('style');
        }

        if(flag){
            var flag2= validatePunchOutOverLapping();

            if(flag2==1){
                $('.punchOutbutton').removeAttr('disabled');
                $(".time").removeAttr('style');
                $(".date").removeAttr('style');
            }

            if(flag2==0){

                $('.punchOutbutton').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");

            }
        }
         }

         else{

        var flag3=validateForpunchInOverLapping();

        if(flag3==1){

             $('.punchInbutton').removeAttr('disabled');
                $(".time").removeAttr('style');
                $(".date").removeAttr('style');

        }

        if(flag3==0){

             $('.punchInbutton').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");

        }
         }

    });



function validate() {

    errFlag = false;
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var errorStyle = "background-color:#FFDFDF;";

    if (!strToTime($(".date").val()+" "+$(".time").val(), dateTimeFormat)) {



        $('.punchOutbutton').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(erorrForInvalidFormat);
        $(".time").attr('style', errorStyle);
        $("#attendance_date").attr('style', errorStyle);
        errFlag = true;
    }
    else{


        punchedTimestamp = strToTime($(".date").val()+" "+$(".time").val(), dateTimeFormat);
        maxTimestamp = strToTime($(".date").val()+" 24:00", dateTimeFormat);

        if (punchedTimestamp >= maxTimestamp) {
            alert("Invalid Max Time");
            errFlag = true;
        }


        var inTime = strToTime(punchInTime, dateTimeFormat);
        var outTime = strToTime($(".date").val()+" "+$(".time").val(), dateTimeFormat);

        if (inTime > outTime) {

            $('.punchOutbutton').attr('disabled', 'disabled');
            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(erorrForInvalidTime);
            $(".time").attr('style', errorStyle);
            $("#attendance_date").attr('style', errorStyle);
            errFlag = true;
        }

    }
    return !errFlag ;
}

function validatePunchOutOverLapping(){


    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var inTime=punchInTime;

    var outTime =$(".date").val()+" "+$(".time").val();

    var r = $.ajax({
        type: 'POST',
        url: linkForOverLappingValidation,
        data: "punchInTime="+inTime+"&punchOutTime="+outTime+"&employeeId="+employeeId,
        async: false,

        success: function(msg){

            isValid = msg;

        }
    });


    var errorStyle = "background-color:#FFDFDF;";

    if (isValid==0) {

        $('.punchOutbutton').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(errorForOverLappingTime);
        $(".time").attr('style', errorStyle);
        $("#attendance_date").attr('style', errorStyle);

    }

    return isValid;
}



function validateForpunchInOverLapping(){
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");


    var inTime =$(".date").val()+" "+$(".time").val();

    var r = $.ajax({
        type: 'POST',
        url: linkForPunchInOverlappingValidation,
        data: "punchInTime="+inTime+"&employeeId="+employeeId,
        async: false,

        success: function(msg){
            isValid = msg;

        }
    });

     var errorStyle = "background-color:#FFDFDF;";

if (isValid==0) {

        $('.punchInbutton').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "messageBalloon_failure");
        $('#validationMsg').html(errorForOverLappingTime);
        $(".time").attr('style', errorStyle);
        $("#attendance_date").attr('style', errorStyle);

    }

    return isValid;

}
 }

});





