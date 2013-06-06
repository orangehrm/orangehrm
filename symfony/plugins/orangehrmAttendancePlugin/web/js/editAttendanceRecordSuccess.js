$(document).ready(function()
    {
       
       var classStr;
        dateTimeFormat= 'yyyy-MM-dd HH:mm';
        
        var validator = $("#frmCommentSave").validate({
            rules: {
                'punchInOutNote' : {
                    maxlength: 250
                }
            },
            messages: {
                'punchInOutNote' : {
                    maxlength: commentError
                }
            }
        });
        
        $(".nonEditable").each(function(){
            $(this).hide();
            $(".nonEditable").attr('readonly', true);
        });

        for(var j=0; j<=nonEditableOutDate.length; j++){
            
            $("#attendance_punchOutDate_"+nonEditableOutDate[j]).attr('readonly', true);
            $("#attendance_punchOutTime_"+nonEditableOutDate[j]).attr('readonly', true)
        }

        if(errorRows.length > 0){
            if(eval(errorRows).length > 0){
                var errorStyle = "background-color:#FFDFDF;";
                for(var j=0; j<eval(errorRows).length; j++){
		
                    $("#attendance_punchInDate_"+eval(errorRows)[j]).addClass('validation-error');
                    $("#attendance_punchInTime_"+eval(errorRows)[j]).addClass('validation-error');
                    $("#attendance_punchOutDate_"+eval(errorRows)[j]).addClass('validation-error');
                    $("#attendance_punchOutTime_"+eval(errorRows)[j]).addClass('validation-error');
                }
                $('#validationMsg').attr('class', "message warning");
                $('#validationMsg').html(errorForOverLappingTime);
            }
        }
           
        $(".inDate").each(function(index, elem){


            // this particular element
            $elem = $(elem),

            InDate = trim($elem.val());

            if (InDate === '') {
                $elem.val(datepickerDateFormat);
            }


            $elem.change(function() {
        
                element = $(this)
                idDate=element.attr('id');
                idArray= idDate.split("_");

                inTimezone=$("#attendance_InOffset_"+idArray[2]).val();
                inTime=   $("#attendance_punchInTime_"+idArray[2]).val();
                inDate=convertToYMDFormat(element.val());
                recordId=$("#attendance_recordId_"+idArray[2]).val();
            
                outDate=convertToYMDFormat($("#attendance_punchOutDate_"+idArray[2]).val());
                outTime=$("#attendance_punchOutTime_"+idArray[2]).val();
        
                outTimezone=$("#attendance_OutOffset_"+idArray[2]).val();
                punchOut= outDate+" "+formatTime(outTime);
            
                punchOut= outDate+" "+formatTime(outTime);
          
                var outTimeTemp = strToTime(punchOut, datepickerDateFormat);
                punchOutUtcTime= outTimeTemp-outTimezone*3600*1000;
      

                if((inDate=="")||(inTime=="")){
                    var errorStyle = "background-color:#FFDFDF;";
                    element.addClass('validation-error');
                    $('#btnSave').attr('disabled', 'disabled');
               
                    $('#validationMsg').attr('class', "message warning");
                    $('#validationMsg').html(errorForInvalidDateFormat);
                
                }
                else{
     
                    var flag4 = validateDateFormat(inDate);

                    if(!flag4) {
                        var errorStyle = "background-color:#FFDFDF;";
                        element.addClass('validation-error');
                        $('#btnSave').attr('disabled', 'disabled');
               
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(errorForInvalidDateFormat);
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        element.removeClass('validation-error');
                    }

                    if(flag4){
                
                        var flag1=validatePunchInTime(punchOutUtcTime,inTimezone,inTime,inDate);
                    
                        if(!flag1){
                            var errorStyle = "background-color:#FFDFDF;";
                            element.addClass('validation-error');
                            $('#btnSave').attr('disabled', 'disabled');
                            $('#validationMsg').attr('class', "message warning");
                            $('#validationMsg').html(errorForInvalidTime);
                        }
                        else{
                
                            $('#btnSave').removeAttr('disabled');
                            element.removeClass('validation-error');
                
                        }
            
                        if(flag1){

                            var punchInOverLappingFlag = validateForpunchInOverLapping(inTimezone,inTime, inDate,recordId,punchOutUtcTime);
                        
                            if(punchInOverLappingFlag==0){
                                var errorStyle = "background-color:#FFDFDF;";
                                element.addClass('validation-error');
                            }
                            else{
                                $('#btnSave').removeAttr('disabled');
                                element.removeClass('validation-error');
                 
                            }
                        }
                    }
   
                }
            });

        });

        $(".outDate").each(function(index, elem){
            // this particular element
            $elem = $(elem),

            OutDate = trim($elem.val());

            if (OutDate === '') {
                $elem.val(datepickerDateFormat);
            }


            $elem.change(function() {
 
                element = $(this)
           
                idDate=element.attr('id');
                idArray= idDate.split("_");
           
                outTimezone=$("#attendance_OutOffset_"+idArray[2]).val();
                inTimezone=$("#attendance_InOffset_"+idArray[2]).val();
                outTime= $("#attendance_punchOutTime_"+idArray[2]).val();
                inTime= $("#attendance_punchInTime_"+idArray[2]).val();
                inDate= convertToYMDFormat($("#attendance_punchInDate_"+idArray[2]).val());
                outDate=convertToYMDFormat(element.val());
                punchInTime=inDate+" "+inTime;
                punchOutTime=outDate+" "+outTime;
                recordId=$("#attendance_recordId_"+idArray[2]).val();
                punchIn= inDate+" "+formatTime(inTime);
            
                var inTimeTemp = strToTime(punchIn, datepickerDateFormat);
                punchInUtcTime=inTimeTemp-inTimezone*3600*1000;
            
            
            
                if((outDate=="")||(outTime=="")){
                    var errorStyle = "background-color:#FFDFDF;";
                    element.addClass('validation-error');
                    $('#btnSave').attr('disabled', 'disabled');
               
                    $('#validationMsg').attr('class', "message warning");
                    $('#validationMsg').html(errorForInvalidDateFormat);
                
                }
                else{
                    $('#btnSave').removeAttr('disabled');
                    element.removeClass('validation-error');
                    $(".messageBalloon_success").remove();
                    $('#validationMsg').removeAttr('class');
                    $('#validationMsg').html("");
            
                    var flag4 = validateDateFormat(outDate);

                    if(!flag4) {
                        var errorStyle = "background-color:#FFDFDF;";
                        element.addClass('validation-error');
                        $('#btnSave').attr('disabled', 'disabled');
               
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(errorForInvalidDateFormat);
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        element.removeClass('validation-error');
                    }
                     
                    if(flag4){
                
                        var flag1=validatePunchOutTime(punchInUtcTime,outTimezone,outTime,outDate);

                        if(!flag1){
                            var errorStyle = "background-color:#FFDFDF;";
                            element.addClass('validation-error');
                            $('#btnSave').attr('disabled', 'disabled');
                            $('#validationMsg').attr('class', "message warning");
                            $('#validationMsg').html(errorForInvalidTime);
                        }
                        else{
                
                            $('#btnSave').removeAttr('disabled');
                            element.removeClass('validation-error');
                
                        }
                    
                        if(flag1){
                        
                            var flag5=validatePunchOutOverLapping(punchInTime,inTimezone,punchOutTime,outTimezone,recordId);
                            if(flag5==0){
                                var errorStyle = "background-color:#FFDFDF;";
                                element.addClass('validation-error');
                            }
                            else{
                                $('#btnSave').removeAttr('disabled');
                                element.removeClass('validation-error');
                            }
                        
                        }
               
                    }
                }
            });

        });
       
        $("#btnCancel").click(function() {
            if(actionRecorder=="viewEmployee"){
                $('form#employeeRecordsForm').attr({
                    action:linkToViewEmployeeRecords+"?employeeId="+employeeId+"&date="+recordDate+"&trigger="+true
                });
            }
        
            if(actionRecorder=="viewMy"){
            
                $('form#employeeRecordsForm').attr({
                    action:linkToViewMyRecords+"?employeeId="+employeeId+"&date="+recordDate+"&trigger="+true
                });
            }
        
            $('form#employeeRecordsForm').submit();
        });

        $("#btnSave").click(function() {
            $('form#employeeRecordsForm').attr({
                action:linkToEdit+"?employeeId="+employeeId+"&date="+recordDate+"&formSubmitAction="+true+"&actionRecorder="+actionRecorder
            });
            $('form#employeeRecordsForm').submit();
        });
        
        $(".icon").click(function() {
            $("#punchInOutNote").val("");
            classStr = $(this).attr("id").split("_");
            if(classStr[1]==2){
                $("#punchInOutNote").attr('readonly', true);
                $("#commentSave").hide();
            }
            else{
                $("#commentSave").show();
                $("#punchInOutNote").attr('readonly', false);
            }
            $("#punchInOutNote").val($("#attendanceNote_"+classStr[1]+"_"+classStr[2]+"_"+classStr[0]).val());
            $('#commentDialog').modal('toggle');
        });
        
        $("#commentSave").click(function() {
            if ($('#frmCommentSave').valid()) {
                var id=classStr[0];
                var punchInOut=classStr[2];
                var comment =  $("#punchInOutNote").val();
                updateComment(comment, id, punchInOut);
                $("#attendanceNote_"+classStr[1]+"_"+classStr[2]+"_"+classStr[0]).val(comment);
                var displayedComment=trim(comment)
                if (displayedComment.length > 25) {
                    displayedComment =displayedComment.substring( 0, 25) +"...";
                }
                $("#commentLable_"+classStr[1]+"_"+classStr[2]+"_"+classStr[0]).html(displayedComment)
                $('#commentDialog').modal('toggle');
            }
        });
        
        $('#commentCancel').click(function () {
            validator.resetForm();
        });
        
        $(".outTime").keyup(function(){
            element = $(this)
            idDate=element.attr('id');
            idArray= idDate.split("_");
            punchInUtcTime=$("#punchInUtcTime_"+idArray[2]).val();
            outTimezone=$("#attendance_OutOffset_"+idArray[2]).val();
            inTimezone=$("#attendance_InOffset_"+idArray[2]).val();
            outDate=   convertToYMDFormat($("#attendance_punchOutDate_"+idArray[2]).val());
            inTime=   $("#attendance_punchInTime_"+idArray[2]).val();
            inDate=   convertToYMDFormat($("#attendance_punchInDate_"+idArray[2]).val());
            recordId=$("#attendance_recordId_"+idArray[2]).val();
            outTime=element.val();
            punchInTime= inDate+" "+inTime;
            punchOutTime= outDate+" "+outTime;
            punchIn= inDate+" "+formatTime(inTime);
            
            var inTimeTemp = strToTime(punchIn, dateTimeFormat);
            punchInUtcTime=inTimeTemp-inTimezone*3600*1000;
                     
            if(!outDate.isValidDate()){
                        
                var errorStyle = "background-color:#FFDFDF;";
                element.addClass('validation-error');
                $('#btnSave').attr('disabled', 'disabled');
               
                $('#validationMsg').attr('class', "message warning");
                $('#validationMsg').html(errorForInvalidTimeFormat);
                
            }else{
                $('#btnSave').removeAttr('disabled');
                element.removeClass('validation-error');
                $(".messageBalloon_success").remove();
                $('#validationMsg').removeAttr('class');
                $('#validationMsg').html("");
            
                var errorTimeFlag= validateTimeFormat(outTime);
            
                if(!errorTimeFlag){
                    var errorStyle = "background-color:#FFDFDF;";
                    element.addClass('validation-error');
                    $('#btnSave').attr('disabled', 'disabled');
               
                    $('#validationMsg').attr('class', "message warning");
                    $('#validationMsg').html(errorForInvalidTimeFormat);
                }
                else{
                   
                    $('#btnSave').removeAttr('disabled');
                    element.removeClass('validation-error');
                }
                if(errorTimeFlag){
                    var isValidPunchOutTime=validatePunchOutTime(punchInUtcTime,outTimezone,outTime,outDate);
                    if(!isValidPunchOutTime){
                        var errorStyle = "background-color:#FFDFDF;";
                        element.addClass('validation-error');
                        $('#btnSave').attr('disabled', 'disabled');
               
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(errorForInvalidTime);
                        
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        element.removeClass('validation-error');
                        $("#attendance_punchInDate_"+idArray[2]).removeClass('validation-error');
                    }
                    
                    if(isValidPunchOutTime){
                        
                        var flag7 = validatePunchOutOverLapping(punchInTime, inTimezone, punchOutTime, outTimezone, recordId);
                    
                        if(flag7 == 0){
                            var errorStyle = "background-color:#FFDFDF;";
                            element.addClass('validation-error');
                        }
                        else{
                            $('#btnSave').removeAttr('disabled');
                            element.removeClass('validation-error');
                        }
                    }
                }
            }
        });
            
        $(".inTime").keyup(function(){
             
            element = $(this)
            idTime=element.attr('id');
            idArray= idTime.split("_");
            recordId=$("#attendance_recordId_"+idArray[2]).val();
      
            inTimezone=$("#attendance_InOffset_"+idArray[2]).val();
            inDate=   convertToYMDFormat($("#attendance_punchInDate_"+idArray[2]).val());
            inTime=element.val();
        
            outDate=convertToYMDFormat($("#attendance_punchOutDate_"+idArray[2]).val());
            outTime=$("#attendance_punchOutTime_"+idArray[2]).val();
            outTimezone=$("#attendance_OutOffset_"+idArray[2]).val();
            punchOut= outDate+" "+formatTime(outTime);
            
            var outTimeTemp = strToTime(punchOut, dateTimeFormat);
            punchOutUtcTime=outTimeTemp-outTimezone*3600*1000;
        
            if((inTime=="") || (inDate=="")){
                        
                var errorStyle = "background-color:#FFDFDF;";
                element.addClass('validation-error');
                $('#btnSave').attr('disabled', 'disabled');
               
                $('#validationMsg').attr('class', "message warning");
                $('#validationMsg').html(errorForInvalidTimeFormat);
                
            }
            else{
            
                $('#btnSave').removeAttr('disabled');
                element.removeClass('validation-error');
                $(".messageBalloon_success").remove();
                $('#validationMsg').removeAttr('class');
                $('#validationMsg').html("");
            

                var errorTimeFlag= validateTimeFormat(inTime);
            
                if(!errorTimeFlag){
                    var errorStyle = "background-color:#FFDFDF;";
                    element.addClass('validation-error');
                    $('#btnSave').attr('disabled', 'disabled');
               
                    $('#validationMsg').attr('class', "message warning");
                    $('#validationMsg').html(errorForInvalidTimeFormat);
                }
                else{
                   
                    $('#btnSave').removeAttr('disabled');
                    element.removeClass('validation-error');
                }
                
                if(errorTimeFlag){
                    var isValidPunchInTime = validatePunchInTime(punchOutUtcTime,inTimezone,inTime,inDate);
                    if(!isValidPunchInTime){
                        var errorStyle = "background-color:#FFDFDF;";
                        element.addClass('validation-error');
                        $('#btnSave').attr('disabled', 'disabled');
               
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(errorForInvalidTime);
                        
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        element.removeClass('validation-error');
                        $("#attendance_punchInDate_"+idArray[2]).removeClass('validation-error');
                    }
                    
                    
                    if(isValidPunchInTime){
                        var punchInOverLappingFlag = validateForpunchInOverLapping(inTimezone,inTime, inDate,recordId, punchOutUtcTime);
                    
                        if(punchInOverLappingFlag==0){
                            var errorStyle = "background-color:#FFDFDF;";
                            element.addClass('validation-error');
                        }
                        else{
                            $('#btnSave').removeAttr('disabled');
                            element.removeClass('validation-error');
                        }
                    }
                }
            }
        });
        
    });
    
function validatePunchOutOverLapping(punchInTime,inTimezone,punchOutTime,outTimezone,recordId){
    var isValid;

    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var inTime=punchInTime;  
    
    var outTime =punchOutTime;
    var inTimezone=inTimezone*3600;
    var outTimezone=outTimezone*3600;
    var r = $.ajax({
        type: 'POST',
        url: linkForOverLappingValidation,
        data: "punchInTime="+inTime+"&punchOutTime="+outTime+"&employeeId="+employeeId+"&inTimezone="+inTimezone+"&outTimezone="+outTimezone+"&recordId="+recordId,
        async: false,

        success: function(msg){

            isValid = msg;
        }
    });
 

    if (isValid==0) {

        $('#btnSave').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "message warning");
        $('#validationMsg').html(errorForOverLappingTime);    

    }

    return isValid;

}
    
 
    
function updateComment(comment,id,punchInOut){

        
    var r=$.ajax({
        type: 'POST',
        url: updateCommentlink,
        data: {
            id: id,
            comment:     comment,
            punchInOut:  punchInOut
        },
        async: false
    }).responseText;
    return r;   
        
}

function validateForpunchInOverLapping(inTimezone,inTime, inDate,recordId, punchOutUtcTime){


    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");

    var inTime = inDate+" "+inTime;
    var timezone=inTimezone;
   
    var r = $.ajax({
        type: 'POST',
        url: linkForPunchInOverlappingValidation,
        data: "punchInTime="+inTime+"&employeeId="+employeeId+"&timezone="+timezone+"&recordId="+recordId+"&punchOutUtcTime="+punchOutUtcTime,
        async: false,

        success: function(msg){
            isValid = msg;

        }
    });

   
    if (isValid==0) {

        $('#btnSave').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "message warning");
        $('#validationMsg').html(errorForOverLappingTime);
    }

    return isValid;
}
function validateDateFormat(date) {
   
    var formtMonth;
    var formtDate;
   
    errFlag = false;
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");
 
    //implement the when - is not there this breaks

    if(!validateDate(date, "yy-mm-dd")){
        
        $('#btnSave').attr('disabled', 'disabled');
            
        errFlag = true;
       
    }

    return !errFlag ;
}

function validateTimeFormat(time){
    
    
    re = /^\d{1,2}:\d{2}([ap]m)?$/;
    errFlag = false;
    $(".messageBalloon_success").remove();
    $('#validationMsg').removeAttr('class');
    $('#validationMsg').html("");
    
    if(time!= '' && !time.match(re)) {
        $('#btnSave').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "message warning");
        $('#validationMsg').html(errorForInvalidTimeFormat);
  
        errFlag = true;
     
    }
    return !errFlag;
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

function validatePunchInTime(punchOutUtcTime, inTimezone, inTime, date){
    errFlag = false;
    var dateArray=date.split('-');
    
    if(dateArray[1].search([0])== -1){
        if((dateArray[1]==11) || (dateArray[1]==12)){
            formtMonth=dateArray[1];
        }
        else{
            formtMonth="0"+dateArray[1];
        }
    }
    else{

        formtMonth=dateArray[1];
    }

    if(dateArray[2]<= 9){
        if(dateArray[2].search([0])== -1){
            formtDate="0"+dateArray[2];
        }

        else{
            formtDate=dateArray[2]
        }

    }
    else{
        formtDate=dateArray[2];
    }


    var formtedFullDate=dateArray[0]+"-"+formtMonth+"-"+formtDate;
  
    var timeArray=inTime.split(':')
    
    if(timeArray[0] < 10){

        if(timeArray[0].search([0]) == -1){
            formtHour = "0" + timeArray[0];
        }
        else{
            formtHour = timeArray[0];
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

    var outTime = punchOutUtcTime
    var inTimeTemp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);
    var inTime=inTimeTemp-inTimezone*3600*1000;

    if (outTime != 0 && inTime > outTime) {
        errFlag = true;
    }
    return !errFlag;
}


function validatePunchOutTime(punchInUtcTime,outTimezone,outTime,outDate){
    errFlag = false;
    var dateArray = outDate.split('-');
    
    if(dateArray[1].search([0])== -1){
        if((dateArray[1]==11) || (dateArray[1]==12)){
            formtMonth=dateArray[1];
        }
        else{
            formtMonth="0"+dateArray[1];
        }

    }

    else{

        formtMonth=dateArray[1];
    }

    if(dateArray[2]<= 9){
        if(dateArray[2].search([0])== -1){
            formtDate="0"+dateArray[2];
        }

        else{
            formtDate=dateArray[2]
        }

    }

    else{
        formtDate=dateArray[2];
    }


    var formtedFullDate=dateArray[0]+"-"+formtMonth+"-"+formtDate;
  
    var timeArray=outTime.split(':')
    
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


    var inTime = punchInUtcTime
    var outTimeTemp = strToTime(formtedFullDate+" "+formtdFullTime, dateTimeFormat);
  
    
    var outTime=outTimeTemp-outTimezone*3600*1000;
  
    if (inTime > outTime) {

        errFlag = true;
    }
    return !errFlag;
        
        
        
}

function formatDate(date){


    var dateArray=date.split('-');

    if(dateArray[1].search([0])== -1){
        if((dateArray[1]==11) || (dateArray[1]==12)){
            formtMonth=dateArray[1];
        }
        else{
            formtMonth="0"+dateArray[1];
        }

    }

    else{

        formtMonth=dateArray[1];
    }

    if(dateArray[2]<= 9){
        if(dateArray[2].search([0])== -1){
            formtDate="0"+dateArray[2];
        }

        else{
            formtDate=dateArray[2]
        }

    }

    else{
        formtDate=dateArray[2];
    }


    var formtedFullDate=dateArray[0]+"-"+formtMonth+"-"+formtDate;
    return formtedFullDate;

}

function formatTime(time){
    
    var timeArray=time.split(':')
    
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

    return formtdFullTime;
    
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

function convertToYMDFormat(date){
    try{
        var parsedDate = $.datepicker.parseDate(datepickerDateFormat, date);
        return $.datepicker.formatDate("yy-mm-dd", parsedDate);
    } catch (error) {
        //alert(error)
    }

}

    
  
 