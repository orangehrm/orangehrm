$(document).ready(function() {

    $('.btnDrop').hide();
    var vacancyString = $("#addCandidate_vacancyList").val();
    var vacancyList = vacancyString.split("_");
    var initialVacancyIdArray = new Array();

    if(vacancyList.length > 0){
        $("#jobDropDown0").html(buildVacancyList(vacancyList[0]));
        initialVacancyIdArray[0] = $('.vacancyDrop:last').attr('id');
        for(var i=1; i<vacancyList.length; i++){
            buildVacancyDrpDwn(vacancyList[i]);
            initialVacancyIdArray[i] = $('.vacancyDrop:last').attr('id');
        }
    }else{

        if($('.vacancyDrop').length >= list.length-1){
            hideLabel();
        }
        $("#jobDropDown0").html(buildVacancyList());
        $("#btnDropDown0").attr('id', 'btnDropDown'.list[0].id);
    }
    
    $("#addButton").live('click', function(){
        buildVacancyDrpDwn("");
    });

    //Load default Mask if empty
    var date = trim($("#addCandidate_appliedDate").val());
    //Bind date picker
    daymarker.bindElement("#addCandidate_appliedDate",
    {
        onSelect: function(date){
        //$("#candidateSearch_fromDate").valid();
        },
        dateFormat:jsDateFormat
    });

    $('#frmDateBtn').click(function(){
        daymarker.show("#addCandidate_appliedDate");
    });

    $('#btnSave').click(function() {
        var isExistedVacancyGoingToBeDeleted = 0;
        if($("#btnSave").attr('value') == lang_edit) {
            
            $('#textBoxesGroup').css("width", "290px");
            $('#textBoxesGroup').css("float", "left");
            $('#addCandidateHeading').hide();
            $('#addCandidate .mainHeading').append('<h2>' + lang_editCandidateTitle + '</h2>');
            $('.btnDrop').hide();
            for(i=0; i < widgetList.length; i++) {
                $(widgetList[i]).removeAttr("disabled");
            }
            $('.removeText').show();
            if($('.vacancyDrop').length < 5){
                $('#addButton').show();
            }
            $('#radio').show();
            $('#actionPane').hide();
            $('#addCandidate_resumeUpdate_1').attr('checked', 'checked');

            $("#btnSave").attr('value', lang_save);
            
        } else {
            
            if(isValidForm()) {
                
                $('#addCandidate_keyWords.inputFormatHint').val('');
                getVacancy();
                if(candidateId != "") {
                    
                    $('.vacancyDrop').each(function(index, value) {
                        if((jQuery.inArray($(this).attr('id'), initialVacancyIdArray) >= 0) && ($(this).val() == "")) {
                            isExistedVacancyGoingToBeDeleted = 1;                            
                        }                    
                    });
                    
                    if((isExistedVacancyGoingToBeDeleted == 1) && (vacancyList[0] != "")) {
                        $('#deleteConfirmationForSave').dialog('open');
                    } else {
                        $('form#frmAddCandidate').submit();
                    }
                
                } else {
                    $('form#frmAddCandidate').submit();
                }
            
            }
            
        }

    });

    $("input[name=addCandidate[resumeUpdate]]").click(function () {
        if(attachment != "" && !$('#addCandidate_resumeUpdate_3').attr("checked")){
            $('#addCandidate_resume').val("");
        }
        if ($('#addCandidate_resumeUpdate_3').attr("checked")) {
            $('#fileUploadSection').show();
        } else {
            $('#fileUploadSection').hide();
        }
    });

    var result;

    $('.removeText').live('click', function(){
        $('#deleteConfirmation').dialog('open');
        result = /\d+(?:\.\d+)?/.exec(this.id);
        
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
    
    $('#actionPane').hide();
    
    if(candidateId != ""){
        var widgetList = new Array('.formInputText', '.contactNo', '.vacancyDrop', '#addCandidate_keyWords', '#addCandidate_resume',
            '#addCandidate_appliedDate', '#frmDateBtn', '#addCandidate_comment', '#addCandidate_resumeUpdate_1', '#addCandidate_resumeUpdate_2','#addCandidate_resumeUpdate_3');
        for(i=0; i < widgetList.length; i++) {
            $(widgetList[i]).attr("disabled", "disabled");
        }
        $('.removeText').hide();
        $('#addButton').hide();
        $('#fileUploadSection').hide();
        $('#radio').hide();
        $('#actionPane').show();
        $("#btnSave").attr('value', lang_edit);
    } else {
        $('#textBoxesGroup').css("width", "290px");
        $('#textBoxesGroup').css("float", "left");
    }

    $('.actionDrpDown').change(function(){
        var id = $(this).attr('id');
        var idList = id.split("_")
        var candidateVacancyId = idList[1];
        var selectedAction = $(this).val();
        var url = changeStatusUrl;
        if(selectedAction == interviewAction){
            url = interviewUrl;
        }
        window.location.replace(url+'?candidateVacancyId='+candidateVacancyId+'&selectedAction='+selectedAction);
    });

    $('#btnBack').click(function(){
        window.location.replace(backBtnUrl+'?candidateId='+candidateId);
    });
    
    $('#deleteConfirmation').dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle',
        open: function() {
            $('#dialogCancelBtn').focus();
        }
    });

    $('#dialogDeleteBtn').click(function() {
        $('#jobDropDown'+result).remove();
        if($('.vacancyDrop').length < 5){
            showLabel();
        }
        validate();
        $("#deleteConfirmation").dialog("close");
    });
    
    $('#dialogCancelBtn').click(function() {
        $("#deleteConfirmation").dialog("close");
    });
    
    $('#deleteConfirmationForSave').dialog({
        autoOpen: false,
        modal: true,
        width: 325,
        height: 50,
        position: 'middle',
        open: function() {
            $('#dialogCancelButton').focus();
        }
    });

    $('#dialogSaveButton').click(function() {
        $('form#frmAddCandidate').submit();
    });
    
    $('#dialogCancelButton').click(function() {
        $("#deleteConfirmationForSave").dialog("close");
    });

});

function buildVacancyDrpDwn(vacancyId) {

    if($('.vacancyDrop').length < 5 ) {

        if($('.vacancyDrop').length > list.length-3 || $('.vacancyDrop').length > 3){
            hideLabel();
        }

        var nextId = 1 + parseInt($('.vacancyDrop:last').attr('id').substring($('.vacancyDrop:last').attr('id').length - 1, $('.vacancyDrop:last').attr('id').length));

        var newjobDropDown = $(document.createElement('div')).attr("id", 'jobDropDown' + nextId);    

        newjobDropDown.after().html('<label><?php echo __(Job Vacancy); ?></label>' +
            '<select  id="jobDropDown' + nextId +'"'+' onchange="validate()"'+' class="vacancyDrop"'+'>'+buildVacancyList(vacancyId)+'</select>'+
            '<span '+'class="removeText"'+ 'id="removeButton'+nextId+'">'+lang_remove+'</span>'+'<br class="clear" />');

        newjobDropDown.appendTo("#textBoxesGroup");
        
    }

}

function hideLabel(){
    $("#addButton").hide();
}

function showLabel(){
    $("#addButton").show();
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
    //$(".messageBalloon_failure").remove()
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

function buildVacancyList(vacancyId){

    var numOptions = list.length;
    //var optionHtml = '<option value="">'+select+'</option>';
    var optionHtml = "";
    for (var i = 0; i < numOptions; i++) {

        if(list[i].id == vacancyId){
            optionHtml += '<option selected="selected" value="' + list[i].id + '">' + list[i].name + '</option>';
        }else{
            optionHtml += '<option value="' + list[i].id + '">' + list[i].name + '</option>';
        }
    }
    return optionHtml;
}

function isValidForm(){

    $.validator.addMethod("dateComparison", function(value, element, params) {
        var temp = false;

        var fromdate        =        $('#addCandidate_appliedDate').val();
        var todate        =        currentDate;

        if(fromdate.trim() == "" || todate.trim() == "" || fromdate == dateDisplayFormat || todate == dateDisplayFormat){
            temp = true;
        }else{
            fromdate = (fromdate).split("-");
            var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));

            todate = (todate).split("-");
            var todateObj        =        new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));

            if ( fromdate <= todate){
                temp = true;
            }
        }
        return temp;

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
                //validatePhone:true,
                maxlength:30
            //required:false
            },
            
            'addCandidate[keyWords]': {
                maxlength:255
            },

            'addCandidate[appliedDate]' : {
                valid_date: function() {
                    return {
                        format:jsDateFormat,
                        displayFormat:dateDisplayFormat,
                        required:false

                    }
                },
                dateComparison: true
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
                maxlength:lang_noMoreThan255
            },

            'addCandidate[appliedDate]' : {
                valid_date: lang_validDateMsg,
                dateComparison:lang_dateValidation
            }

        },
        //errorElement : 'div',
        errorPlacement: function(error, element) {

            error.appendTo(element.next('div.errorHolder'));
            //these are specially for date boxes
            error.appendTo(element.next().next('div.errorHolder'));

        }

    });
    return true;
}
