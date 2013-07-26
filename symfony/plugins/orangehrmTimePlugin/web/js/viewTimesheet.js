$(document).ready(function(){
    $msgDelayTime = 3000; // time that set for msg fading...
    dateTimeFormat= 'yyyy-MM-dd HH:mm';

    var rDate = trim($("#time_date").val());
    if (rDate == '') {
        $("#time_date").val(displayDateFormat);
    }

    $("#addTimesheetBtn").click(function() {
        var startdate=$("#time_date").val();
        if(validateDate(startdate, datepickerDateFormat)){
            var endDate= calculateEndDate(Date_toYMD()); 
            endDateArray=endDate.split("-");
            try{
            var parsedDate = $.datepicker.parseDate(datepickerDateFormat, startdate);
            var startdate1 =  $.datepicker.formatDate('yy-mm-dd', parsedDate);
            }
            catch(error){
                
            }
            endDate = new Date(endDateArray[0],endDateArray[1]-1,endDateArray[2]);
            var startDateArray=startdate1.split("-");
            var startDate = new Date(startDateArray[0], startDateArray[1]-1, startDateArray[2]);
            var newEndDate= new Date(endDate);
            if (newEndDate < startDate) {
                displayMessages('warning', lang_noFutureTimesheets);
            }else{
                url=createTimesheet+"?startDate="+startdate1+"&employeeId="+employeeId
                $.getJSON(url, function(data) {
                    if(data[0]==1){
                        displayMessages('warning', lang_overlappingTimesheets);
                    }
                    if(data[0]==3){
                        displayMessages('warning', lang_timesheetExists);
                    }
                    if(data[0]==2){
                        startDate=data[1].split(' ');
                        $('form#createTimesheetForm').attr({
                            action:linkForViewTimesheet+"?&timesheetStartDateFromDropDown="+startDate[0]+"&employeeId="+employeeId
                        });
                        $('form#createTimesheetForm').submit();
                    }
                })
            }
        }
        else{
            displayMessages('warning', lang_invalidDate);
        }
    });

    $("#btnEdit").click(function(){
        $('form#timesheetFrm').attr({
            action:linkForEditTimesheet+"?employeeId="+employeeId+"&timesheetId="+timesheetId+"&actionName="+actionName
        });
        $('form#timesheetFrm').submit();
    });

    $("#btnSubmit").click(function(){
        $('form#timesheetFrm').attr({
            action:linkForViewTimesheet+"?act="+submitAction+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&submitted="+true+"&updateActionLog="+true
        });
        $('form#timesheetFrm').submit();
    });

    $("#btnReset").click(function(){
        $('form#timesheetFrm').attr({
            action:linkForViewTimesheet+"?act="+resetAction+"&timesheetStartDate="+date+"&employeeId="+employeeId+"&updateActionLog="+true+"&resetAction="+true
        });
        $('form#timesheetFrm').submit();
    });

    $("#btnReject").click(function(){
        $('form#timesheetActionFrm').attr({
            action:linkForViewTimesheet+"?act="+rejectAction+"&timesheetStartDate="+date+"&employeeId="+employeeId+
                "&updateActionLog="+true
        });
        $('form#timesheetActionFrm').submit();
    });

    $("#btnApprove").click(function(){
        $('form#timesheetActionFrm').attr({ 
            action:linkForViewTimesheet+"?act="+approveAction+"&timesheetStartDate="+date+"&employeeId="+employeeId+
                "&updateActionLog="+true
        });
        $('form#timesheetActionFrm').submit();
    });

    $(".icon").click(function(){
        $("#timeComment").attr("disabled","disabled");
        //removing errors message in the comment box
        $("#commentError").html("");
        var elementId = $(this).attr('id');
        var timesheetItemId = elementId.replace('callout_', '');
        var projectName = $(this).closest('tr').find('td:first').text();
        var activityName = $(this).closest('tr').find('td:nth-child(2)').text();
        
        var comment = getComment(timesheetItemId);
        var decoded = $("<div/>").html(comment).text();
        $("#timeComment").val(decoded);
        $("#commentProjectName").text(projectName);
        $("#commentActivityName").text(activityName);
        var parsedDate = $.datepicker.parseDate("yy-mm-dd", comment_date);
        $("#commentDate").text($.datepicker.formatDate(datepickerDateFormat, parsedDate));
    });

    var validator = $("#timesheetActionFrm").validate({
        rules: {
            'Comment' : {
                maxlength: 250
            }
        },
        messages: {
            'Comment' : {
                maxlength: erorrMessageForInvalidComment
            }
        }
    });
    
    $('#btnAddTimesheet').click(function(){
        $('#msgDiv').remove();
    });
    
});

var timesheetItemId;
var question;
var close= "close";

function clicked(dropdown){
    var selectedIndex = document.getElementById('startDates').value;
    var dateString = dateList[selectedIndex];
    var dates = dateString.split(" ");
    location.href = linkForViewTimesheet+"?timesheetStartDateFromDropDown="+dates[0]+"&selectedIndex="+selectedIndex+"&employeeId="+employeeId;
}

function getComment(timesheetItemId){
    var r = $.ajax({
        type: 'POST',
        url: linkToViewComment,
        data: "timesheetItemId="+timesheetItemId,
        async: false,
        success: function(msg){
            var array = msg.split('##');
            question = array[0];
            comment_date = array[1];
        }
    });
    return question;
}

String.prototype.isValidDate = function() {
    var IsoDateRe = new RegExp("^([0-9]{4})-([0-9]{2})-([0-9]{2})$");
    var matches = IsoDateRe.exec(this);
    if (!matches) 
        return false;
    var composedDate = new Date(matches[1], (matches[2] - 1), matches[3]);
    return ((composedDate.getMonth() == (matches[2] - 1)) && (composedDate.getDate() == matches[3]) && 
        (composedDate.getFullYear() == matches[1]));
}

function calculateEndDate(startDate){
    var r = $.ajax({
        type: 'POST',
        url:  returnEndDate,
        data: "startDate="+startDate,
        async: false,
        success: function(msg){
           
            var array = msg.split(' ');
            date1 = array[0];
        }
    });
    return date1;        
}

function Date_toYMD() {
    var dt=new Date();
    var year, month, day;
    year = String(dt.getFullYear());
    month = String(dt.getMonth() + 1);
    if (month.length == 1) {
        month = "0" + month;
    }
    day = String(dt.getDate());
    if (day.length == 1) {
        day = "0" + day;
    }
    return year + "-" + month + "-" + day;
}

function displayMessages(messageType, message) {
    $('#msgDiv').remove();
    if (messageType != 'reset') {
        $divClass = 'message '+messageType;
        $msgDivContent = "<div id='msgDiv' class=' " + $divClass + "' >" + message + 
            "<a class='messageCloseButton' href='#'>"+closeText+"</a>" + "</div>";
        $('#validationMsg').after($msgDivContent);
    }
//    $('#msgDiv').fadeOut($msgDelayTime, function(){
//        $('#msgDiv').remove();
//    });
}
