$(document).ready(function() {

    $('#leaveperiod_cmbStartMonth').change(function(){

        $("#endDateDiv").show();

        if($(this).val() == 0) {
            $("#endDateDiv").hide();
        }
            
        if($(this).val() != 0) {
            eraseErrorMessages();
        }

        url = url_date_of_months +'/month/' + $(this).val();
        $.getJSON(url, function(dates) {
            populateDateSelector($('#leaveperiod_cmbStartDate'), dates);
            loadEndDate_Regular();
            checkCurrentStartDate();
        });
    });
		
    url = url_date_of_months+'/month/2/isLeapYear/false';
    $.getJSON(url, function(dates) {
        populateDateSelector($('#leaveperiod_cmbStartDateForNonLeapYears'), dates);
        loadEndDate_NonLeapYears();
    });

    $("#leaveperiod_cmbStartMonthForNonLeapYears").change(function() {
        url = url_date_of_months+'/month/' + $("#leaveperiod_cmbStartMonthForNonLeapYears").val() + '/isLeapYear/false';
        $.getJSON(url, function(dates) {
            populateDateSelector($('#leaveperiod_cmbStartDateForNonLeapYears'), dates);
            loadEndDate_NonLeapYears();
        });
        loadEndDate_NonLeapYears();
    });
        
    $('#leaveperiod_cmbStartDate').change(function() {
        loadEndDate_Regular();
        checkCurrentStartDate();
    });

    $('#leaveperiod_cmbStartDateForNonLeapYears').change(loadEndDate_NonLeapYears);

    $('.datesForNonLeapYears').hide();

    $('#btnReset').click(function() {
        $('#leaveperiod_cmbStartMonth').val(initValues.startMonth);
        $('#leaveperiod_cmbStartDate').html(initValues.startDateHTML);
        $('#leaveperiod_cmbStartDate').val(initValues.startDate);
        $('#lblEndDate').html(initValues.endDate);
        var followingYearText = "";
        if(initValues.startMonth > 1 || initValues.startDate > 1) {
            followingYearText = "(" + lang_following_year + ")";
        }
        $('#lblEndDateFollowingYear').html(followingYearText);
        $('.datesForNonLeapYears').hide();
        $('#messagebar').hide();
    });

    $('#btnEdit').click(function() {
            
        if (isEditMode) {
            if (validateForm()) {
                
                $('#frmLeavePeriod').submit();
                $("#btnReset").hide();
            }
        } else {
            isEditMode = true;
            $("#btnReset").show();
            $('#frmLeavePeriod select').attr('disabled', false);
            $(this).val(lang_save);
        }
    });

    if (isLeavePeriodDefined) {
        $('#frmLeavePeriod select').attr('disabled', true);
    }

    $('#leaveperiod_cmbStartMonth').val(start_month_value);
    $('#leaveperiod_cmbStartDate').val(start_date_value);

    initStartMonth = $('#leaveperiod_cmbStartMonth').val();
    initStartDateHTML = $('#leaveperiod_cmbStartDate').html();
    initStartDate = $('#leaveperiod_cmbStartDate').val();
    initEndDate = $('#lblEndDate').html();

    initValues = {
        startMonth : initStartMonth,
        startDateHTML : initStartDateHTML,
        startDate: initStartDate,
        endDate: initEndDate
    };

/*
    daymarker.bindElement("#leaveperiod_calCurrentPeriodStartDate", {
        onSelect: function(date) {
            // TODO: Make this part format independant
            dateValues = date.split('-');
            var selectedDate = new Date(dateValues[0], dateValues[1] - 1, dateValues[2]);
            	

            url = url_leave_period_end_date+'/month/' + $('#leaveperiod_cmbStartMonth').val() + '/date/' + $('#leaveperiod_cmbStartDate').val() + '/format/Y-m-d';
            $.ajax({
                type: 'get',
                url: url,
                success: function(endDateString) {

                    dateValues = endDateString.split('-');
                    maximumAllowedDate = new Date(dateValues[0], dateValues[1] - 1, dateValues[2]);
                    maximumAllowedDate.setTime(maximumAllowedDate.getTime() - 86400000); // Deducting one day

                    if (selectedDate.getTime() > maximumAllowedDate.getTime()) {
                        maximumAllowedDateString = maximumAllowedDate.getFullYear() + '-' + (maximumAllowedDate.getMonth() + 1) + '-' + maximumAllowedDate.getDate();
                        $('#leaveperiod_calCurrentPeriodStartDate').val(maximumAllowedDateString);
	                    	
                        $('#messagebar').addClass('messageBalloon_warning'); // Extract error display to a new function
                        errorMessage = lang_error_date_grater_than;
                        $('#messagebar').css('height', 30);
                        $('#messagebar').html(errorMessage);
                        $('#messagebar').show();
                    } else {
                        $('#messagebar').html('');
                        $('#messagebar').hide();
                    }

                }
            });
        	    
            	
        },
        dateFormat : 'yy-mm-dd'
    });
*/
    $('#calCurrentPeriodStartDate_Button').click(function(){
        daymarker.show("#leaveperiod_calCurrentPeriodStartDate");
    });

    $('#divChooseCurrentStartDate').hide();
    $("#endDateDiv").show();
    if($("#leaveperiod_cmbStartMonth").val() == 0) {
        $("#endDateDiv").hide();
    }

    /* removing error messages if reset button got clicked */
    $("#btnReset").click(function() {
        $(".errorHolder").empty();
    });

    /* these are validation messages on any dropdown changes */
    $("#leaveperiod_cmbStartMonthForNonLeapYears").change(function() {
        $("#inlineErrorHolder2").empty();
        if($("#leaveperiod_cmbStartMonthForNonLeapYears").val() == 0) {
            $("#inlineErrorHolder2").append(lang_StartMonthForNonLeapYearIsRequired);
        }
    });

    $("#leaveperiod_cmbStartMonth").change(function() {
        $("#inlineErrorHolder").empty();
        if($("#leaveperiod_cmbStartMonth").val() == 0) {
            $("#inlineErrorHolder").append(lang_StartMonthIsRequired);
        }
    });
});
	
function loadEndDate_Regular() {
    if ($('#leaveperiod_cmbStartMonth').val() == '2' && $('#leaveperiod_cmbStartDate').val() == '29') {
        $('.datesForNonLeapYears').show();
    } else {
        $('.datesForNonLeapYears').hide();
    }
		
    loadEndDate($('#leaveperiod_cmbStartMonth').val(), $('#leaveperiod_cmbStartDate').val(), $('#lblEndDate'));
}
	
function loadEndDate_NonLeapYears() {
    var startMonth = 2;
    if($("#leaveperiod_cmbStartMonthForNonLeapYears").val() > 0) {
        startMonth = $("#leaveperiod_cmbStartMonthForNonLeapYears").val();
    }
    loadEndDate(startMonth, $('#leaveperiod_cmbStartDateForNonLeapYears').val(), $('#lblEndDateForNonLeapYears'));
}
	
function loadEndDate(startMonth, startDate, displayLabel) {
    url = url_leave_period_end_date+'/month/' + startMonth + '/date/' + startDate;
    $.ajax({
        type: 'get',
        url: url,
        success: function(html) {
            //this is for end date on leap year
            var followingYearText = "";
            if($("#leaveperiod_cmbStartMonth").val() > 1 || $('#leaveperiod_cmbStartDate').val() > 1) {
                followingYearText = "(" + lang_following_year + ")";
            }
            $("#lblEndDateFollowingYear").html(followingYearText);

            //this is for end date on non leap year
            followingYearText = "";
            if($("#leaveperiod_cmbStartMonthForNonLeapYears").val() > 1 || $('#leaveperiod_cmbStartDateForNonLeapYears').val() > 1) {
                followingYearText = "(" + lang_following_year + ")";
            }
            $("#lblNonLeapYearFollowingYear").html(followingYearText);
            displayLabel.html(html);
        }
    });	    	    	    
}
	
function populateDateSelector(dateSelector, dates) {
    var html = '';
    $.each (dates, function (index, date) {
        var option = '<option value="' + date + '">' + date + '</option>';
        if(dateSelector.attr("id") == "leaveperiod_cmbStartDateForNonLeapYears" && date == 28) {
            option = '<option value="' + date + '" selected="selected">' + date + '</option>';
        }
        html += option;
    });
    dateSelector.html(html);
}

function checkCurrentStartDate() {
    if (!isLeavePeriodDefined) {
        startMonth = $('#leaveperiod_cmbStartMonth').val();
        startDate = $('#leaveperiod_cmbStartDate').val();
        url = url_current_start_date+'/month/' + startMonth + '/date/' + startDate;
        $.ajax({
            type: 'get',
            url: url,
            success: function(html) {
                $('#spanDateHolder').html(html);
                $('#leaveperiod_calCurrentPeriodStartDate').val(html);
                $('#divChooseCurrentStartDate').show();
            }
        });  
    }
}

function validateForm() {
    $.validator.addMethod("positiveNumber", function(value, element, params) {
        return Number(value) > 0;
    });
    
    var validator = $("#frmLeavePeriod").validate({


        rules: {
            'leaveperiod[cmbStartMonth]' : {
                positiveNumber:true
            },
            'leaveperiod[cmbStartDate]' : {
                positiveNumber:true
            }
        },
        messages: {
            'leaveperiod[cmbStartMonth]' : {
                positiveNumber: lang_required
            },
            'leaveperiod[cmbStartDate]' : {
                positiveNumber: lang_required
            }
        }

    });
    return true;

    /*
        eraseErrorMessages();

        if ($('#leaveperiod_cmbStartMonth').val() == '0') {
            placeError('<?php echo __(ValidationMessages::REQUIRED); ?>');
            return false;
        }

        if ($('#leaveperiod_cmbStartDate').val() == '0') {
            placeError('<?php echo __(ValidationMessages::REQUIRED); ?>');
            return false;
        }

        if($('#leaveperiod_cmbStartMonth').val() == 2 && $("#leaveperiod_cmbStartMonthForNonLeapYears").val() == 0) {
            $("#inlineErrorHolder2").append('<?php echo __(ValidationMessages::REQUIRED); ?>');
            return false;
        }
     */
    return true;

}

function placeError(message) {
    $('#inlineErrorHolder').append(message);
}

function eraseErrorMessages() {
    $('#inlineErrorHolder').empty();
    $('#inlineErrorHolder2').empty();

}

