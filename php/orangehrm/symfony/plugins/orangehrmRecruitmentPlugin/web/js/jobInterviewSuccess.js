var counter;
$(document).ready(function() {
    counter = 1;
    //Auto complete
    $(".formInputInterviewer").autocomplete(employees, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        //$("#candidateSearch_selectedCandidate").val(item.id);
        //$("label.error").hide();
        validateInterviewerNames()
    });

    $("#addButton").live('click', function(){
        counter++;
        if(counter == numberOfInterviewers){
            $("#addButton").hide();
        }        
        $('#interviewer_'+counter).show();
    });
    
    $('.removeText').live('click', function(){
        var result = /\d+(?:\.\d+)?/.exec(this.id);
        $('#interviewer_'+result).hide();
        $('#jobInterview_interviewer_'+result).val("");
        counter--;
        if(counter < numberOfInterviewers){
            $("#addButton").show();
        }
        validateInterviewerNames()
        $(this).prev().removeClass('error');
        $(this).next().empty();
        $(this).next().hide();
    });
    
    $('.interviwerErrorContainers').css('display', 'none');

    $('.formInputInterviewer').each(function(){
        if($(this).parent().css('display') == 'block') {
            if ($(this).val() == '' || $(this).val() == lang_typeHint) {
                $(this).addClass("inputFormatHint").val(lang_typeHint);
            }
        }
    });
   
    $('.formInputInterviewer').one('focus', function() {
        
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }

    });

    if($("#saveBtn").attr('value') == lang_edit) {
        $(".formInputInterviewer").attr('disabled', 'disabled');
        $(".formInputText").attr('disabled', 'disabled');
        $(".calendarBtn").attr('disabled', 'disabled');
        $(".addText").hide();
        $(".removeText").hide();

    }
	
    for(var i = 2; i <= numberOfInterviewers; i++){
        $('#interviewer_'+i).hide();
    }
    $('#saveBtn').click(function(){

        if($("#saveBtn").attr('value') == lang_edit) {
            $('#jobInterviewHeading').text(lang_editInterviewDetails)
            $(".formInputInterviewer").removeAttr("disabled");
            $(".formInputText").removeAttr("disabled");
            $(".calendarBtn").removeAttr("disabled");
            if (counter < 5){
                $(".addText").show();
            }
            $(".removeText").show();
            if($('.interviewer').filter(function(){return ( $(this).css('display') === 'block' );}).length == 1) {
                $('#removeButton1').hide();
            }
            $("#saveBtn").attr('value', lang_save);
            $("#cancelButton").attr('value', lang_cancel);
            if(editHiringManager != 1){
                $(".formInputInterviewer").attr('disabled', 'disabled');
                $(".removeText").hide();
                $("#addButton").hide();
            }
            return;
        }
        if($("#saveBtn").attr('value') == lang_save) {
            if(isValidForm()){
                removeTypeHints();
                validateInterviewers()
                $('#frmJobInterview').submit();
            }
        }
    });

    if(interviewId>0){
        var noOfInterviewers = $('#jobInterview_selectedInterviewerList').val();
        var i;
        for(i=1; i<=noOfInterviewers; i++){
            $('#interviewer_'+(i)).show();
        }
        counter = noOfInterviewers;

        if($("#saveBtn").attr('value') == lang_edit){
            $('#jobInterviewHeading').text(lang_viewInterviewDetails);
        }
    }
    
    $('#cancelButton').click(function(){
        if($("#cancelButton").attr('value') == lang_cancel){
            window.location.replace(cancelUrl+'?id='+historyId);
        }
        if($("#cancelButton").attr('value') == lang_back){
            window.location.replace(addCandidateUrl);
        }
    });

    if($('.interviewer').filter(function(){return ( $(this).css('display') === 'block' );}).length <=1) {
        $('#removeButton1').hide();
    }
    
});

function validateInterviewers(){

    var empCount = employeeList.length;
    var empIdList = new Array();
    var j = 0;
    $('.formInputInterviewer').each(function(){
        element = $(this);
        inputName = $.trim(element.val()).toLowerCase();
        if(inputName != ""){
            var i;
            for (i=0; i < empCount; i++) {
                arrayName = employeeList[i].name.toLowerCase();

                if (inputName == arrayName) {
                    empIdList[j] = employeeList[i].id;
                    j++;
                    break;
                }
            }
        }
    });
    $('#jobInterview_selectedInterviewerList').val(empIdList);
}

function removeTypeHints() {
    
    $('.formInputInterviewer').each(function(){
        if($(this).val() == lang_typeHint) {
            $(this).val("");
        }
    });
    
}

function validateInterviewerNames(){

    var flag = true;
    $(".messageBalloon_success").remove();
    //$(".messageBalloon_failure").remove()
    $('#interviewerNameError').removeAttr('class');
    $('#interviewerNameError').html("");

    var errorStyle = "background-color:#FFDFDF;";
    var normalStyle = "background-color:#FFFFFF;";
    var interviewerNameArray = new Array();
    var errorElements = new Array();
    var index = 0;
    var num = 0;

    $('.formInputInterviewer').each(function(){
        element = $(this);
        $(element).attr('style', normalStyle);
        if((element.val() != "") && (element.val() != lang_typeHint)){
            interviewerNameArray[index] = $(element);
            index++;
        }
    });

    for(var i=0; i<interviewerNameArray.length; i++){
        var currentElement = interviewerNameArray[i];
        for(var j=1+i; j<interviewerNameArray.length; j++){

            if(currentElement.val() == interviewerNameArray[j].val() ){
                errorElements[num] = currentElement;
                errorElements[++num] = interviewerNameArray[j];
                num++;
                $('#interviewerNameError').html(lang_identical_rows);
                flag = false;

            }
        }
        for(var k=0; k<errorElements.length; k++){

            errorElements[k].attr('style', errorStyle);
        }
    }

    return flag;
}

function isValidForm(){

    $.validator.addMethod("hiringManagerNameValidation", function(value, element, params) {
        var temp = false;
        var hmCount = employeeList.length;
        var i;
        for (i=0; i < hmCount; i++) {
            hmName = $.trim($('#'+element.id).val()).toLowerCase();
            arrayName = employeeList[i].name.toLowerCase();
            if (hmName == arrayName) {
                $('#'+element.id).val(employeeList[i].name);
                temp = true;
                break;
            }
        }
        if(($('#'+element.id).parent().css('display') == "none") && (($('#'+element.id).val() == "") || ($('#'+element.id).val() == lang_typeHint))) {
            temp = true;
        }
        
        if((element.id != 'jobInterview_interviewer_1') && (($('#'+element.id).val() == "") || ($('#'+element.id).val() == lang_typeHint))) {
            temp = true;
        }
        
        if(!temp) {
            $('#'+element.id).next().next().css('display', 'block');
        } else {
            $('#'+element.id).next().next().css('display', 'none');
        }
        
        return temp;
    });
    
    $.validator.addMethod("timeValidation", function(value, element) {
        
        var isFormatValid = this.optional(element) || /^[0-9]{1,2}[:]{1}[0-9]{2}$/i.test(value);
        var isTimeRangeValid = false;
        
        var timeArray = value.split(':');
        if(timeArray.length == 2) {
            if((parseInt(timeArray[0]) <= 24) && (parseInt(timeArray[1]) <= 59)) {
                isTimeRangeValid = true;
                if((parseInt(timeArray[0]) == 24) && (parseInt(timeArray[1]) > 0)) {
                    isTimeRangeValid = false;
                }
            }
        }
        if (value == ""){
            isTimeRangeValid = true;
        }
        
        return isFormatValid && isTimeRangeValid;
        
    });
    
    $.validator.addMethod("dateRequired", function(value, element) {
        
        var date = trim(value)
        
        if((date == "") || (date == displayDateFormat)) {
            return false;
        }
        return true;
        
    });

    var validator = $('#frmJobInterview').validate({

        rules: {
            'jobInterview[name]' : {
                required:true,
                maxlength:100
            },
            
            'jobInterview[interviewer_1]' : {
                hiringManagerNameValidation: true
            },
            
            'jobInterview[interviewer_2]' : {
                hiringManagerNameValidation: true
            },
            
            'jobInterview[interviewer_3]' : {
                hiringManagerNameValidation: true
            },
            
            'jobInterview[interviewer_4]' : {
                hiringManagerNameValidation: true
            },
            
            'jobInterview[interviewer_5]' : {
                hiringManagerNameValidation: true
            },

            'jobInterview[date]' : {
                dateRequired: true,
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                }
            },

            'jobInterview[time]' : {
                timeValidation: true
            }
        },
        messages: {
            'jobInterview[name]' : {
                required: lang_interviewHeadingRequired,
                maxlength: lang_noMoreThan98
            },

            'jobInterview[interviewer_1]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },
            
            'jobInterview[interviewer_2]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },
            
            'jobInterview[interviewer_3]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },
            
            'jobInterview[interviewer_4]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },
            
            'jobInterview[interviewer_5]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },

            'jobInterview[date]' : {
                dateRequired: lang_dateRequired,
                valid_date: lang_validDateMsg
            },

            'jobInterview[time]' : {
                timeValidation: lang_validTimeRequired
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            
            if(element.next().hasClass('errorHolder')) {
                error.appendTo(element.next('div.errorHolder'));
            } else if(element.next().next().hasClass('errorHolder')) {
                error.appendTo(element.next().next('div.errorHolder'));
            }
            
        }

    });
    return true;
    
}

function isSheduledTimeFreeJson(shedulingDate, shedulingTime) {
    
    $.getJSON(getInterviewSheduledTimeListActionUrl, function(data) {
        
        //        var numOptions = 0;
        //		if(data != null){
        //			numOptions = data.length;
        //		}
        //        var optionHtml = '<option value="">'+lang_all+'</option>';
        //
        //        for (var i = 0; i < numOptions; i++) {
        //
        //            if(data[i].id == para){
        //                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + data[i].name + '</option>';
        //            }
        //            else{
        //                optionHtml += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        //            }
        //        }
        //
        //        $("#candidateSearch_jobVacancy").html(optionHtml);

        });
    
}