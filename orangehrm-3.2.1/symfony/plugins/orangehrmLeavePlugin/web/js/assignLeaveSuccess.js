$(document).ready(function() {
        
    leaveBalanceNegative = false;
    lastLeaveBalance = 0.0;
    
    if (haveLeaveTypes) {
        showTimeControls(false, false);
        
        // Auto complete
        $("#assignleave_txtEmployee_empName").autocomplete(employees_assignleave_txtEmployee, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }              
            ,
            matchContains:true
        }).result(function(event, item) {
            $('#assignleave_txtEmployee_empId').val(item.id);
            setEmployeeWorkshift(item.id);
            updateLeaveBalance();
        }
        );
    
    
//        $("#assignleave_txtEmployee_empName").result(function(event, item) {
//            alert(item.id);
//        });
        
        updateLeaveBalance();
        
        $('#assignleave_txtFromDate').change(function() {
            fromDateBlur($(this).val());
            updateLeaveBalance();
        });
        
        $('#assignleave_txtToDate').change(function() {
            toDateBlur($(this).val());
            updateLeaveBalance();
        });          
        
        $('#assignleave_partialDays').change(function() {
            handlePartialDayChange(true);
        });
        
        //Show From if same date
        if(trim($("#assignleave_txtFromDate").val()) == displayDateFormat || trim($("#assignleave_txtToDate").val()) == displayDateFormat 
            || trim($("#assignleave_txtFromDate").val()) == '' || trim($("#assignleave_txtToDate").val()) == '') {
                showTimeControls(false, false);
        } else if (trim($("#assignleave_txtFromDate").val()) == trim($("#assignleave_txtToDate").val())) {
            showTimeControls(true, false);
        } else {
            showTimeControls(false, true);
        }
        
        // Bind On change event of time elements
        $('select.timepicker').change(function() {
            fillTotalTime($(this));
        });
        
        
        // Fetch and display available leave when leave type is changed
        $('#assignleave_txtLeaveType').change(function() {
            updateLeaveBalance();
        });
        
        $("#assignleave_txtFromTime").datepicker({
            onClose: function() {
                $("#assignleave_txtFromTime").valid();
            }
        });     
        
        
        
        //Validation
        $("#frmLeaveApply").validate({
            rules: {
                'assignleave[txtEmployee][empName]':{
                    required: true,
                    validEmployeeName: true,
                    onkeyup: false
                },
                'assignleave[txtLeaveType]':{
                    required: true
                },
                'assignleave[txtFromDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            required: true,
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'assignleave[txtToDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            required: true,
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$("#assignleave_txtFromDate").val()
                        }
                    }
                },
                'assignleave[txtComment]': {
                    maxlength: 250
                },
                'assignleave[duration][time][from]':{
                    required: false, 
                    validWorkShift : true, 
                    validTotalTime: true, 
                    validToTime: true
                },
                'assignleave[duration][time][to]':{
                    required: false, 
                    validTotalTime: true
                },
                'assignleave[firstDuration][time][from]':{
                    required: false, 
                    validWorkShift : true, 
                    validTotalTime: true, 
                    validToTime: true
                },
                'assignleave[firstDuration][time][to]':{
                    required: false, 
                    validTotalTime: true
                },
                'assignleave[secondDuration][time][from]':{
                    required: false, 
                    validWorkShift : true, 
                    validTotalTime: true, 
                    validToTime: true
                },
                'assignleave[secondDuration][time][to]':{
                    required: false, 
                    validTotalTime: true
                }                        
            },
            messages: {
                'assignleave[txtEmployee][empName]':{
                    required:lang_Required,
                    validEmployeeName: lang_validEmployee
                },
                'assignleave[txtLeaveType]':{
                    required:lang_Required
                },
                'assignleave[txtFromDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate
                },
                'assignleave[txtToDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                },
                'assignleave[txtComment]':{
                    maxlength: lang_CommentLengthExceeded
                },
                'assignleave[duration][time][from]':{
                    validTotalTime : lang_Required,
                    validWorkShift : lang_DurationShouldBeLessThanWorkshift,
                    validToTime: lang_FromTimeLessThanToTime
                },
                'assignleave[duration][time][to]':{
                    validTotalTime : lang_Required
                },
                'assignleave[firstDuration][time][from]':{
                    validTotalTime : lang_Required,
                    validWorkShift : lang_DurationShouldBeLessThanWorkshift,
                    validToTime: lang_FromTimeLessThanToTime
                },
                'assignleave[firstDuration][time][to]':{
                    validTotalTime : lang_Required
                },
                'assignleave[secondDuration][time][from]':{
                    validTotalTime : lang_Required,
                    validWorkShift : lang_DurationShouldBeLessThanWorkshift,
                    validToTime: lang_FromTimeLessThanToTime
                },
                'assignleave[secondDuration][time][to]':{
                    validTotalTime : lang_Required
                }
            },
            submitHandler: function(form) {
                var showConfirm = false;
                if (leaveBalanceNegative) {
                    showConfirm = true;
                } else {
                    var fromdate = $('#assignleave_txtFromDate').val();
                    var todate = $('#assignleave_txtToDate').val();
                    if (fromdate == todate) {
                        var workShift = $('#assignleave_txtEmpWorkShift').val();
                        var totalTime;
                                
                        if ($('#assignleave_duration_duration').val() == 'half_day') {
                            totalTime = parseFloat(workShift) / 2;
                        } else if ($('#assignleave_duration_duration').val() == 'full_day') {
                            totalTime = workShift;
                        } else {
                            totalTime = getTotalTime($('#assignleave_duration_time_from').val(), $('#assignleave_duration_time_to').val());
                        }
                                
                        
                        var leaveInDays = totalTime / parseFloat(workShift);
                        
                        if (!isNaN(leaveInDays)) {
                            // only consider first two decimals
                            leaveInDays = (Math.floor(leaveInDays * 100)) / 100;
                            if (leaveInDays > lastLeaveBalance) {
                                showConfirm = true;
                            }
                        }
                    }
                }
                
                if (showConfirm) {
                    $('#leaveBalanceConfirm').modal();
                } else {
                    form.submit();
                }
            }                    
        });
        
        $.validator.addMethod("validTotalTime", function(value, element) {
            var valid = true;

            if ($(element).is(':visible')) {  
                             
                if (value == '') {
                    valid = false;
                }
            }
            
            return valid;
        });
        
        $.validator.addMethod("validWorkShift", function(value, element) {
            
            var valid = true;
            
            if ($(element).is(':visible')) {            
                var fromElement = $(element).parent('span').children('select.timepicker').first();    
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                var workShift = $('#assignleave_txtEmpWorkShift').val();
                if (parseFloat(totalTime) > parseFloat(workShift)) {
                    valid = false;
                }
            }
            return valid;            
        });
        
        $.validator.addMethod("validToTime", function(value, element) {
            var valid = true;
            
            if ($(element).is(':visible')) {            
                var fromElement = $(element).parent('span').children('select.timepicker').first();    
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                if (parseFloat(totalTime) <= 0) {
                    valid = false;
                }
            }

            return valid;  
        });
        
        $.validator.addMethod("validEmployeeName", function(value, element) { 
            return employeeAutoFill('assignleave_txtEmployee_empName', 'assignleave_txtEmployee_empId', employees_assignleave_txtEmployee);  
            

        });
        
       $("#assignleave_txtEmployee_empName").result(function(event, item) {
            $("#assignleave_txtEmployee_empName").valid();
        });
        
        $('#confirmOkButton').click(function(event) {
            $("#frmLeaveApply").get(0).submit();
        });
        
        //Click Submit button
       $('#assignBtn').click(function(event) {
        	event.preventDefault();
            if($('#assignleave_txtFromDate').val() == displayDateFormat ){
                $('#assignleave_txtFromDate').val("");
            }
            if($('#assignleave_txtToDate').val() == displayDateFormat ){
                $('#assignleave_txtToDate').val("");
            }
            $('#frmLeaveApply').submit();
        });
        
        $("#assignleave_txtEmployee_empName").change(function(){
            autoFill('assignleave_txtEmployee_empName', 'assignleave_txtEmployee_empId', employees_assignleave_txtEmployee);
            updateLeaveBalance();
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
    }
});
    
function updateLeaveBalance() {
    var leaveType = $('#assignleave_txtLeaveType').val();
    var empId = $('#assignleave_txtEmployee_empId').val();
    var startDate = $('#assignleave_txtFromDate').val();
    var endDate =  $('#assignleave_txtToDate').val();
    $('#assignleave_leaveBalance').text('--');
    $('#leaveBalance_details_link').remove();     
    
    $('#popup_emp_name').text($('#assignleave_txtEmployee_empName').val());
    $('#popup_leave_type').text($('#assignleave_txtLeaveType option:selected').text()); 
    $('#multiperiod_emp_name').text($('#assignleave_txtEmployee_empName').val());
    $('#multiperiod_leave_type').text($('#assignleave_txtLeaveType option:selected').text());

    if (leaveType == "" || empId == "") {
    //$('#assignleave_leaveBalance').text('--');
    //$('#leaveBalance_details_link').remove();
    } else {
        $('#assignleave_leaveBalance').text('').addClass('loading_message');   
        $('#assignBtn').attr("disabled", "disabled");
        $.ajax({
            type: 'GET',
            url: leaveBalanceUrl,
            data: '&leaveType=' + leaveType+'&empNumber=' + empId + '&startDate=' + startDate + '&endDate=' + endDate,
            dataType: 'json',
            success: function(data) {                                
                
                if (data.multiperiod == true) {
                    
                    var leavePeriods = data.data;
                    var leavePeriodCount = leavePeriods.length;
                    
                    var linkTxt = data.negative ? lang_BalanceNotSufficient : lang_details;
                    leaveBalanceNegative = data.negative;
                    
                    var balanceTxt = leavePeriodCount == 1 ? leavePeriods[0].balance.balance.toFixed(2) : '';
                    var linkCss = data.negative ? ' class="error" ' : "";
                    
                    $('#assignleave_leaveBalance').text(balanceTxt)
                    .append('<a href="#multiperiod_balance" data-toggle="modal" id="leaveBalance_details_link"' + linkCss + '>' + 
                        linkTxt + '</a>');

                    var html = '';
                    
                    var rows = 0;
                    for (var i = 0; i < leavePeriodCount; i++) {
                        var leavePeriod = leavePeriods[i];
                        var days = leavePeriod['days'];
                        var leavePeriodFirstRow = true;                        

                        for (var leaveDate in days) {
                            if (days.hasOwnProperty(leaveDate)) {
                                var leaveDateDetails = days[leaveDate];
                                
                                rows++;                        
                                var css = rows % 2 ? "even" : "odd";                                
                                
                                var thisLeavePeriod = leavePeriod['period'];
                                var leavePeriodTxt = '';
                                var leavePeriodInitialBalance = '';
                                
                                if (leavePeriodFirstRow) {
                                    leavePeriodTxt = thisLeavePeriod[0] + ' - ' + thisLeavePeriod[1];
                                    leavePeriodInitialBalance = leavePeriod.balance.balance.toFixed(2);
                                    leavePeriodFirstRow = false;                                    
                                }
                                
                                var balanceValue = leaveDateDetails.balance === false ? leaveDateDetails.desc : leaveDateDetails.balance.toFixed(2);
                                
                                html += '<tr class="' + css + '"><td>' + leavePeriodTxt + '</td><td class="right">' + leavePeriodInitialBalance +
                                    '</td><td>' + leaveDate + '</td><td class="right">' + balanceValue + '</td></tr>';                                
                            }
                        }                    
                        
                        $('div#multiperiod_balance table.table tbody').html('').append(html);
                    }
                    
                } else {
                    var balance = data.balance;
                    var asAtDate = data.asAtDate;
                    var balanceDays = balance.balance;
                    $('#assignleave_leaveBalance').text(balanceDays.toFixed(2))
                    .append('<a href="#balance_details" data-toggle="modal" id="leaveBalance_details_link">' + 
                        lang_details + '</a>');

                    $('#balance_as_of').text(asAtDate);                       
                    $('#balance_entitled').text(Number(balance.entitled).toFixed(2));
                    $('#balance_taken').text(Number(balance.taken).toFixed(2));
                    $('#balance_scheduled').text(Number(balance.scheduled).toFixed(2));
                    $('#balance_pending').text(Number(balance.pending).toFixed(2));
                    $('#balance_adjustment').text(Number(balance.adjustment).toFixed(2));        
                    $('#balance_total').text(balanceDays.toFixed(2));

                    if(Number(balance.adjustment) == 0 ){
                        $('#container-adjustment').hide();
                    }
                    
                    leaveBalanceNegative = false;
                    lastLeaveBalance = balanceDays;                    
                }
                
                $('#assignleave_leaveBalance').removeClass('loading_message');   
                $('#assignBtn').removeAttr("disabled");
            }
        });
    }
}
                
function showTimeControls(showOneDay, showMultiDay) {
        
    var oneDayControlIds = ['assignleave_duration_duration'];
        
    $.each(oneDayControlIds, function(index, value) {
            
        if (showOneDay) {
            $('#' + value).parent('li').show();
        } else {
            $('#' + value).parent('li').hide();
        }
    });
    
    var multiDayControlIds = ['assignleave_partialDays'];
    
    
    $.each(multiDayControlIds, function(index, value) {
            
        if (showMultiDay) {
            $('#' + value).parent('li').show();
        } else {
            $('#' + value).parent('li').hide();
        }
    }); 
    
    handlePartialDayChange($('#assignleave_partialDays').is(':visible'));
} 

function handlePartialDayChange(showMultiDay) {
    
    var partialDay = $('#assignleave_partialDays').val();
    var startLabel = false;
    var endLabel = false;
        
    if (!showMultiDay || partialDay === '') {
        $('#assignleave_firstDuration_duration').parent('li').hide();
        $('#assignleave_secondDuration_duration').parent('li').hide();
    } else if (partialDay === 'all' || partialDay === 'start') {
        $('#assignleave_firstDuration_duration').parent('li').show();
        $('#assignleave_secondDuration_duration').parent('li').hide();
        startLabel = partialDay === 'all' ? lang_Duration : lang_StartDay;
    } else if (partialDay === 'end') {
        $('#assignleave_firstDuration_duration').parent('li').show();
        $('#assignleave_secondDuration_duration').parent('li').hide();
        endLabel = lang_EndDay;
    } else if (partialDay === 'start_end') {
        $('#assignleave_firstDuration_duration').parent('li').show();
        $('#assignleave_secondDuration_duration').parent('li').show(); 
        startLabel = lang_StartDay;
        endLabel = lang_EndDay;
    } 
    
    if (startLabel) {
        $('#assignleave_firstDuration_duration').parent('li').children('label:first-child').text(startLabel);
    }
    if (endLabel) {
        $('#assignleave_secondDuration_duration').parent('li').children('label:first-child').text(endLabel);
    }
    
}
    
//Calculate Total time
function fillTotalTime(element) {

    var fromElement = element.parent('span').children('select.timepicker').first();    
    var toElement = fromElement.siblings('select.timepicker').first();
    var durationElement = fromElement.siblings('input.time_range_duration').first();
    
    var total = getTotalTime(fromElement.val(), toElement.val());
    if (isNaN(total)) {
        total = '';
    }

    durationElement.val(total);
    fromElement.valid();
    toElement.valid();
}
    
function getTotalTime(from, to) {
    var total = 0;
    var fromTime = from.split(":");
    var fromdate = new Date();
    fromdate.setHours(fromTime[0],fromTime[1]);
        
    var toTime = to.split(":");
    var todate = new Date();
    todate.setHours(toTime[0],toTime[1]);        
        
    var difference = todate - fromdate;
    var floatDeference	=	parseFloat(difference/3600000) ;
    total = Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2);
        
    return total;        
}
    
function fromDateBlur(date) {

    var fromDateValue = trim(date);
    if (fromDateValue != displayDateFormat && fromDateValue != "") {
        var singleDayLeaveRequest = false;        
        var toDateValue = trim($("#assignleave_txtToDate").val());
        if (validateDate(fromDateValue, datepickerDateFormat)) {
            if (fromDateValue == toDateValue) {
                singleDayLeaveRequest = true;
            }

            if (!validateDate(toDateValue, datepickerDateFormat)) {
                $('#assignleave_txtToDate').val(fromDateValue);
                singleDayLeaveRequest = true;
            }
        }
        showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
    } else {
        showTimeControls(false, false);
    }

    
}
    
function toDateBlur(date) {
    var singleDayLeaveRequest = false;
    var toDateValue = trim(date);
    if (toDateValue != displayDateFormat && toDateValue != "") {
        var fromDateValue = trim($("#assignleave_txtFromDate").val());

        if (validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)) {
            singleDayLeaveRequest = (fromDateValue == toDateValue);
            showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
        } else {
            showTimeControls(false, false);
        }
    } else {
        showTimeControls(false, false);
    }
}
    
function setEmployeeWorkshift(empNumber) {
        
    $.ajax({
        url: "getWorkshiftAjax",
        data: "empNumber="+empNumber,
        dataType: 'json',
        success: function(data){
            $('#assignleave_txtEmpWorkShift').val(data.workshift);
            
            // update start/end time of time range widgets that are not visible
            if (!$('#assignleave_duration_specify_time_content').is(':visible')) {
                $('#assignleave_duration_time_from').val(data.start_time);
                $('#assignleave_duration_time_to').val(data.end_time);
                fillTotalTime($('#assignleave_duration_time_from'));
            }            
            if (!$('#assignleave_firstDuration_specify_time_content').is(':visible')) {
                $('#assignleave_firstDuration_time_from').val(data.start_time);
                $('#assignleave_firstDuration_time_to').val(data.end_time);
                fillTotalTime($('#assignleave_firstDuration_time_from'));
            }
            if (!$('#assignleave_secondDuration_specify_time_content').is(':visible')) {
                $('#assignleave_secondDuration_time_from').val(data.start_time);
                $('#assignleave_secondDuration_time_to').val(data.end_time);
                fillTotalTime($('#assignleave_secondDuration_time_from'));
            }            
        }
    });
        
}    

function employeeAutoFill(selector, filler, data) {
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
        return valid;
    }
