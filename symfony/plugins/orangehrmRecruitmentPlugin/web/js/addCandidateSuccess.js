var nextId = 0;
var toDisable = new Array();
var item = 0;
var vacancyId;
$(document).ready(function() {
    
    vacancyId = $('#addCandidate_vacancy').val();
    if(candidateStatus != activeStatus) {
        $("#btnSave").attr('disabled', 'disabled');
    }

    $(".addText").live('click', function(){
       
        if($("#btnSave").attr('value') == lang_edit){
        
        }else{
            if((allowedVacancylist.length -1) > nextId){
                buildVacancyDrpDwn("", "show allowed vacancies", true);
                newId = /\d+(?:\.\d+)?/.exec(this.id);
                $("#removeButton"+newId).css("padding-left", "195px");
                $("#addButton"+newId).hide();
                if((allowedVacancylist.length -1) <= nextId){
                    $("#addButton"+(nextId-1)).hide();
                    $("#removeButton"+(nextId-1)).css("padding-left", "195px");
                }
            }
        }
    });

    $('.removeText').live('click', function(){

        result = /\d+(?:\.\d+)?/.exec(this.id);
        if(vacancyString.trim() != "" && result < vacancyList.length){
            if($("#btnSave").attr('value') == lang_edit){
            } else{
                $('#deleteConfirmation').modal();
            }
        }
        else{
            $('#jobDropDown'+result).remove();
            validate();
            $("#addButton"+($('.vacancyDrop').length-1)).show();
            $("#removeButton"+($('.vacancyDrop').length-1)).css("padding-left", "128px");
            if(result == $('.vacancyDrop').length-1){
                $("#addButton"+(nextId-1)).show();
                $("#removeButton"+(nextId-1)).css("padding-left", "128px");
            }
            nextId--;
        }
    });

    $('#btnSave').click(function() {
        var isExistedVacancyGoingToBeDeleted = 0;
        if($("#btnSave").attr('value') == lang_edit) {
            $('#addCandidateHeading').text(lang_editCandidateTitle);
            for(i=0; i < widgetList.length; i++) {
                $(widgetList[i]).removeAttr("disabled");
            }
            $('.actionDrpDown').removeAttr("disabled");
            $(".vacancyDrop").each(function(){
                if($.inArray($(this).attr('id'), toDisable) > -1){
                    $(this).attr('disabled', 'disabled');
                }
            });
            $('#radio').show();
            $('#addCandidate_resumeUpdate_1').attr('checked', 'checked');
            $("#btnSave").attr('value', lang_save);
            $("#btnBack").attr('value', lang_cancel);
            
        } else {
            
            if(isValidForm()) {
                
                $('#addCandidate_keyWords.inputFormatHint').val('');
                getVacancy();
                if(candidateId != "") {
                    if($('#addCandidate_vacancy').val() != vacancyId && vacancyId != "") {
                        $('#deleteConfirmationForSave').modal();
                    } else {
                        $('form#frmAddCandidate').submit();
                    }
                
                } else {
                    $('form#frmAddCandidate').submit();
                }
        }
        }

    });
   
    $("input.fileEditOptions").click(function () {
        if(attachment != "" && !$('#addCandidate_resumeUpdate_3').attr("checked")){
            $('#addCandidate_resume').val("");
        }
        if ($('#addCandidate_resumeUpdate_3').attr("checked")) {
            $('#fileUploadSection').show();
        } else {
            $('#fileUploadSection').hide();
        }
    });

    if ($("#addCandidate_keyWords").val() == '') {
        $("#addCandidate_keyWords").val(lang_commaSeparated)
        .addClass("inputFormatHint");
    }

    $("#addCandidate_keyWords").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
    if(candidateId != ""){
        var widgetList = new Array('.formInputText', '.contactNo', '#addCandidate_keyWords', '#addCandidate_resume', '.vacancyDrop', '#addCandidate_vacancy',
        '#addCandidate_appliedDate', '#frmDateBtn', '#addCandidate_comment', '#addCandidate_resumeUpdate_1', '#addCandidate_resumeUpdate_2','#addCandidate_resumeUpdate_3', '#addCandidate_appliedDate_Button');
        for(i=0; i < widgetList.length; i++) {
            $(widgetList[i]).attr("disabled", "disabled");
        }

        if (candidateStatus != activeStatus) {
            $('.actionDrpDown').attr('disabled', 'disabled');
        }
        $('#fileUploadSection').hide();
        $('#radio').hide();
        $("#btnSave").attr('value', lang_edit);
    } else {
    }

    $('.actionDrpDown').change(function(){
        var id = $(this).attr('id');
        var idList = id.split("_")
        var candidateVacancyId = idList[1];
        var selectedAction = $(this).val();
        var url = changeStatusUrl;
        if(selectedAction == interviewAction || selectedAction == interviewAction2){
            url = interviewUrl;
        }
        if(selectedAction == removeAction){
            url = interviewUrl;
        }
        window.location.replace(url+'?candidateVacancyId='+candidateVacancyId+'&selectedAction='+selectedAction);
    });

    $('#btnBack').click(function(){
        if($("#btnBack").attr('value') == lang_cancel){
            window.location.replace(cancelBtnUrl+'?id='+candidateId);
        }else{
            window.location.replace(backBtnUrl+'?candidateId='+candidateId);
        }
    });

    $('#addCandidate_vacancy').change(function(){
        $('#actionPane').hide();
        if( $('#addCandidate_vacancy').val() == vacancyId){
            $('#actionPane').show();
        }
    });

    $('#dialogSaveButton').click(function() {
        $('form#frmAddCandidate').submit();
    });
    
    $('#dialogCancelButton').click(function() {
        $('#addCandidate_vacancy').val(vacancyId);
        $('#actionPane').show();
    });

    $('.vacancyDrop').change(function(){
        toRemove = /\d+(?:\.\d+)?/.exec(this.id)
        $("#"+toRemove).hide();
    });
});

function buildVacancyDrpDwn(vacancyId, mode, removeBtn) {
    if(nextId < 5){
        var newjobDropDown = $(document.createElement('div')).attr("id", 'jobDropDown' + nextId);
        $('#jobDropDown' + nextId).addClass('jobDropDown');
        htmlTxt =  '<label><?php echo __(Job Vacancy); ?></label>' +
            '<select  id="jobDropDown' + nextId +'"'+' onchange="validate()"'+' class="vacancyDrop"'+'>'+buildVacancyList(vacancyId, mode)+'</select>'+
            '<span '+'class="addText"'+ 'id="addButton'+nextId+'">'+'Add another'+'</span>'
        if(removeBtn){
            htmlTxt += '<span '+'class="removeText"'+ 'id="removeButton'+nextId+'">'+lang_remove+'</span>'
        }else{
            toDisable[item] = "jobDropDown"+nextId;
        }
        newjobDropDown.after().html(htmlTxt);
        nextId++;
        newjobDropDown.appendTo("#textBoxesGroup");
    }

}

function buildVacancyList(vacancyId, mode){

    var listArray = new Array();
    if(mode == "show all vacancies"){
        listArray = list;
    }
    if(mode == "show allowed vacancies"){
        listArray = allowedVacancylist;
    }
    if(mode == "show with closed vacancies"){
        listArray = allowedVacancylistWithClosedVacancies;
    }

    var numOptions = listArray.length;
    var optionHtml = "";
    for (var i = 0; i < numOptions; i++) {

        if(listArray[i].id == vacancyId){
            optionHtml += '<option selected="selected" value="' + listArray[i].id + '">' + listArray[i].name + '</option>';
        }else{
            optionHtml += '<option value="' + listArray[i].id + '">' + listArray[i].name + '</option>';
        }
    }
    return optionHtml;
}

function validate(){
    var flag = validateVacancy();
    if(!flag) {
        $('#btnSave').attr('disabled', 'disabled');
        $('#vacancyError').attr('class', "vacancyErr");
    }
    else{
        $('#btnSave').removeAttr('disabled');
    }

}

function getVacancy() {

    var strID = "";
    
    $('.vacancyDrop').each(function() {
        if(!isEmpty($(this).val())) {
            strID = strID + $(this).val() + "_";
        }
    });
    
    $('#addCandidate_vacancyList').val(strID);

}

function validateVacancy(){

    var flag = true;
    $(".messageBalloon_success").remove();
    $(".messageBalloon_failure").remove()
    $('#vacancyError').removeAttr('class');
    $('#vacancyError').html("");
    
    var errorStyle = "background-color:#FFDFDF;";
    var normalStyle = "background-color:#FFFFFF;";
    var vacancyArray = new Array();
    var errorElements = new Array();
    var index = 0;
    var num = 0;

    $('.vacancyDrop').each(function(){
        element = $(this);
        $(element).attr('style', normalStyle);
        vacancyArray[index] = $(element);
        index++;
    });

    for(var i=0; i<vacancyArray.length; i++){
        var currentElement = vacancyArray[i];
        for(var j=1+i; j<vacancyArray.length; j++){
            if(currentElement.val()!=""){
                if(currentElement.val() == vacancyArray[j].val() ){
                    errorElements[num] = currentElement;
                    errorElements[++num] = vacancyArray[j];
                    num++;
                    $('#vacancyError').html(lang_identical_rows);
                    flag = false;
                }
            }
        }
        for(var k=0; k<errorElements.length; k++){
            errorElements[k].attr('style', errorStyle);
        }
    }
    return flag;
}

function isValidForm(){

    $.validator.addMethod('date_range_comp', function(value, element, params) {

    var valid = false;
    var fromDate = $.trim(value);
    var toDate = $.trim(currentDate);
    var format = datepickerDateFormat;

    if(fromDate == format || toDate == format || fromDate == "" || toDate =="") {
        valid = true;
    }else{
        var parsedFromDate = $.datepicker.parseDate(format, fromDate);
        var parsedToDate = $.datepicker.parseDate(format, toDate);
        if(parsedFromDate <= parsedToDate){
            valid = true;
        }
    }
    return valid;
});

    var validator = $("#frmAddCandidate").validate({

        rules: {
            'addCandidate[firstName]' : {
                required:true,
                maxlength:30
            },

            'addCandidate[middleName]' : {
                maxlength:30
            },

            'addCandidate[lastName]' : {
                required:true,
                maxlength:30
            },
            'addCandidate[email]' : {
                required:true,
                email:true,
                maxlength:30
            },

            'addCandidate[contactNo]': {
                phone: true,
                maxlength:30
            },
            
            'addCandidate[keyWords]': {
                maxlength:250
            },
            'addCandidate[comment]': {
                maxlength:250
            },

            'addCandidate[appliedDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat

                    }
                },
                    date_range_comp: true
                }
            },
        messages: {
            'addCandidate[firstName]' : {
                required: lang_firstNameRequired,
                maxlength: lang_tooLargeInput
            },

            'addCandidate[middleName]' : {
                maxlength: lang_tooLargeInput
            },
            'addCandidate[lastName]' : {
                required: lang_lastNameRequired,
                maxlength: lang_tooLargeInput
            },

            'addCandidate[contactNo]': {
                phone: lang_validPhoneNo,
                maxlength:lang_tooLargeInput
            },

            'addCandidate[email]' : {
                required: lang_emailRequired,
                email: lang_validEmail,
                maxlength: lang_tooLargeInput
            },

            'addCandidate[keyWords]': {
                maxlength:lang_noMoreThan250
            },
            'addCandidate[comment]' :{
                maxlength:lang_noMoreThan250
            },
            'addCandidate[appliedDate]' : {
                valid_date: lang_validDateMsg,
                date_range_comp:lang_dateValidation
            }

        }

    });
    return true;
}
