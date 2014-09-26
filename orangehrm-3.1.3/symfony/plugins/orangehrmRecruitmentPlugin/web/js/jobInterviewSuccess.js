var countArray = new Array();
$(document).ready(function() {
    counter = 1;
    //Auto complete
    $(".formInputInterviewer").autocomplete(employees, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        matchContains:true
    }).result(function(event, item) {
        //$("#candidateSearch_selectedCandidate").val(item.id);
        //$("label.error").hide();
        validateInterviewerNames();
    });
    
    for(var i = 0; i <= numberOfInterviewers-2; i++){
        $('#interviewer_'+(i+2)).hide();
        countArray[i] = i+2;
    }
    countArray = countArray.reverse();
    
    $("#addButton").live('click', function(){

        if(countArray.length == 1){
            $("#addButton").hide();
        }      
        var index = countArray.pop();
        $('#interviewer_'+index).show();
        if ($('#jobInterview_interviewer_'+index).val() == '' || $('#jobInterview_interviewer_'+index).val() == lang_typeHint) {
            $('#jobInterview_interviewer_'+index).addClass("inputFormatHint").val(lang_typeHint);
        }
    });
    
    $('.removeText').live('click', function(){
        var result = /\d+(?:\.\d+)?/.exec(this.id);
        $('#interviewer_'+result).hide();
        $('#jobInterview_interviewer_'+result).val("");
        countArray.push(result);
        if(countArray.length > 0){
            $("#addButton").show();
        }
        validateInterviewerNames();
        $(this).prev().removeClass('error');
        $(this).next().empty();
        $(this).next().hide();
    });
    
    $('.interviwerErrorContainers').css('display', 'none');

    $('.formInputInterviewer').each(function(){
        if($(this).parent().css('display') != 'none') {
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
        $("#jobInterview_name").attr('disabled', 'disabled');
        $(".calendar").attr('disabled', 'disabled');
        $(".ui-datepicker-trigger").attr('disabled', 'disabled');
        $("#jobInterview_time").attr('disabled', 'disabled');
        $("#jobInterview_note").attr('disabled', 'disabled');
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
            $("#jobInterview_name").removeAttr("disabled");
            $(".calendar").removeAttr("disabled");
            $(".ui-datepicker-trigger").removeAttr("disabled");
            $("#jobInterview_time").removeAttr("disabled");
            $("#jobInterview_note").removeAttr("disabled");
            if (counter < 5){
                $(".addText").show();
            }
            $(".removeText").show();
            if($('.interviewer').filter(function(){
                return ( $(this).css('display') === 'block' );
            }).length == 1) {
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
            if(validateInterviewerNames()){
                if(isValidForm()){
                    removeTypeHints();
                    validateInterviewers();                
                    $('#frmJobInterview').submit();
                }
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

    if($('.interviewer').filter(function(){
        return ( $(this).css('display') === 'block' );
    }).length <=1) {
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

    var errorClass = "validation-error";
    var interviewerNameArray = new Array();
    var errorElements = new Array();
    var index = 0;
    var num = 0;

    $('.formInputInterviewer').each(function(){
        element = $(this);
        $(element).removeClass(errorClass);
        var ParantId = $(element).parent('li').attr('id');
        $("#"+ParantId).find('span.'+errorClass).remove();
        if((element.val() != "") && (element.val() != lang_typeHint)){
            interviewerNameArray[index] = $(element);
            index++;
        }
    });

    if(interviewerNameArray.length > 0) {
        for(var i=0; i<interviewerNameArray.length; i++){        
            var currentElement = interviewerNameArray[i];
        
            for(var j=0; j<interviewerNameArray.length; j++){
                if(currentElement.val() == interviewerNameArray[j].val() && currentElement.attr('id') != interviewerNameArray[j].attr('id')){
                    errorElements[num] = currentElement;
                    errorElements[++num] = interviewerNameArray[j];
                    num++;
                    interviewerNameArray[j].after('<span class="validation-error">'+lang_identical_rows+'</span>');
                    flag = false;
                }
            }
        
            for(var k=0; k<errorElements.length; k++){
                errorElements[k].addClass(errorClass);
            }
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
                hiringManagerNameValidation: true,
                required: $(this).is(":visible")
            },
            
            'jobInterview[interviewer_2]' : {
                hiringManagerNameValidation: true,
                required: $(this).is(":visible")
            },
            
            'jobInterview[interviewer_3]' : {
                hiringManagerNameValidation: true,
                required: $(this).is(":visible")
            },
            
            'jobInterview[interviewer_4]' : {
                hiringManagerNameValidation: true,
                required: $(this).is(":visible")
            },
            
            'jobInterview[interviewer_5]' : {
                hiringManagerNameValidation: true,
                required: $(this).is(":visible")
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
                hiringManagerNameValidation:lang_enterAValidEmployeeName,
                required: lang_interviewerRequired
            },
            
            'jobInterview[interviewer_2]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName,
                required: lang_interviewerRequired
            },
            
            'jobInterview[interviewer_3]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName,
                required: lang_interviewerRequired
            },
            
            'jobInterview[interviewer_4]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName,
                required: lang_interviewerRequired
            },
            
            'jobInterview[interviewer_5]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName,
                required: lang_interviewerRequired
            },

            'jobInterview[date]' : {
                dateRequired: lang_dateRequired,
                valid_date: lang_validDateMsg
            },

            'jobInterview[time]' : {
                timeValidation: lang_validTimeRequired
            }
        }
    });
    
    return validator.valid();   
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