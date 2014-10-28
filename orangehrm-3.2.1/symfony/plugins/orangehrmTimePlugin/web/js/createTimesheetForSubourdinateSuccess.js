$(document).ready(function(){
    dateTimeFormat= 'yyyy-MM-dd HH:mm';
    $("#createTimesheet").hide();
    $("#time_date").change(function() {
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");
        var startdate=$("#time_date").val();
        if(validateDate(startdate, datepickerDateFormat)){
            var endDate= calculateEndDate(Date_toYMD()); 
            endDateArray=endDate.split('-');
             try{
            var parsedDate = $.datepicker.parseDate(datepickerDateFormat, startdate);
            var startdate1 =  $.datepicker.formatDate('yy-mm-dd', parsedDate);
            }
            catch(error){
            }
            var startDateArray=startdate1.split("-");
            var startDate = new Date(startDateArray[0], startDateArray[1]-1, startDateArray[2]);
            var newEndDate= new Date(endDateArray[0],endDateArray[1]-1,endDateArray[2]);
            if (newEndDate < startDate)
            { 
                $('#validationMsg').attr('class', "message warning");
                $('#validationMsg').html(lang_noFutureTimesheets);
            }else{
                url=createTimesheet+"?startDate="+startdate1+"&employeeId="+employeeId
                $.getJSON(url, function(data) {
                    if(data[0]==1){
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(lang_overlappingTimesheets);
                    }
                    if(data[0]==3){
                        $('#validationMsg').attr('class', "message warning");
                        $('#validationMsg').html(lang_timesheetExists); 
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
            $('#validationMsg').attr('class', "message warning");
            $('#validationMsg').html(lang_invalidDate);
        }
    });
    
    $("#btnAddTimesheet").click(function(){
        $("#createTimesheet").show();
    });
});

String.prototype.isValidDate = function() {
    var IsoDateRe = new RegExp("^([0-9]{4})-([0-9]{2})-([0-9]{2})$");
    var matches = IsoDateRe.exec(this);
    if (!matches) 
        return false;
    var composedDate = new Date(matches[1], (matches[2] - 1), matches[3]);
    return ((composedDate.getMonth() == (matches[2] - 1)) &&
        (composedDate.getDate() == matches[3]) &&
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
