$(document).ready(function() {

    $("#helpText").append("<span id='helpMessage'></span>");
    if($("#helpMessage").text() == ""){
        $("#helpMessage").text(lang_helpText);
    }
	
    //Auto complete
    $("#candidateSearch_candidateName").autocomplete(candidates, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        formatResult: function(item) {
            return item.name
        },  
        matchContains:true
    }).result(function(event, item) {
        $("#candidateSearch_candidateName").valid();
    });
    var jobTitle = $('#candidateSearch_jobTitle').val();
    var vacancyId = $('#candidateSearch_jobVacancy').val();
    var hiringManagerId = $('#candidateSearch_hiringManager').val();

    var vcUrl = vacancyListUrl + jobTitle;
    getVacancyListJson(vcUrl, vacancyId)

    var url1 = hiringManagerListUrlForVacancyId + vacancyId;
    getHiringManagerListJson(url1, hiringManagerId);

    if(vacancyId == ""){
        var url = hiringManagerListUrlForJobTitle + jobTitle;
        getHiringManagerListJson(url, hiringManagerId);
    }



    $('#candidateSearch_jobTitle').change(function() {

        var jobTitle = $('#candidateSearch_jobTitle').val();
        var vcUrl = vacancyListUrl + jobTitle;
        var url = hiringManagerListUrlForJobTitle + jobTitle;
        
        getVacancyListJson(vcUrl)
        getHiringManagerListJson(url);
        
    });

    $('#candidateSearch_jobVacancy').change(function() {
        var jobTitle = $('#candidateSearch_jobTitle').val();
        var vacancyId = $('#candidateSearch_jobVacancy').val();
        var url = hiringManagerListUrlForVacancyId + vacancyId;
        getHiringManagerListJson(url);
        if(vacancyId == ""){
            url = hiringManagerListUrlForJobTitle + jobTitle;
            getHiringManagerListJson(url);
        }

    });

    $('#btnSrch').click(function() {
        $('#candidateSearch_candidateName.inputFormatHint').val('');
        $('#candidateSearch_keywords.inputFormatHint').val('');
        $('#frmSrchCandidates').submit();
   
    });
    $('#btnRst').click(function() {
        $('#frmSrchCandidates').get(0).reset();
        $('#candidateSearch_jobTitle').val("");
        $('#candidateSearch_jobVacancy').val("");
        $('#candidateSearch_hiringManager').val("");
        $('#candidateSearch_modeOfApplication').val("");
        $('#candidateSearch_fromDate').val("");
        $('#candidateSearch_toDate').val("");
        $('#candidateSearch_keywods').val("");
        $('#candidateSearch_status').val("");
        $('#candidateSearch_candidateName').val("");
        $('#candidateSearch_keywords').val("");
        $('#candidateSearch_selectedCandidate').val("");
        $('#frmSrchCandidates *[name^="additionalParams"]').val("");
        $('#frmSrchCandidates').submit();

    });


    if ($("#candidateSearch_candidateName").val() == '') {
        $("#candidateSearch_candidateName").val(lang_typeForHints)
        .addClass("inputFormatHint");
    }

    $("#candidateSearch_candidateName").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    if ($("#candidateSearch_keywords").val() == '') {
        $("#candidateSearch_keywords").val(lang_enterCommaSeparatedWords)
        .addClass("inputFormatHint");
    }

    $("#candidateSearch_keywords").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    $("#candidateSearch_candidateName").click(function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    $('#btnAdd').click(addCandidate);
    

    $('#btnDelete').attr('disabled', 'disabled');

        
    $("#ohrmList_chkSelectAll").click(function() {
        if($(":checkbox").length == 1) {
            $('#btnDelete').attr('disabled','disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        }
    });
    
    
    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
        }
    });
    
    $('#btnDelete').click(function(){
        $('#frmList_ohrmListComponent').submit(function(){
//            $('#deleteConfirmation').dialog('open');
            return false;
        });
    });
  
//    $("#deleteConfirmation").dialog({
//        autoOpen: false,
//        modal: true,
//        width: 325,
//        height: 50,
//        position: 'middle',
//        open: function() {
//            $('#dialogCancelBtn').focus();
//        }
//    });

    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#dialogDeleteBtn').click(function() {
        document.frmList_ohrmListComponent.submit();
    });
//    $('#dialogCancelBtn').click(function() {
//        $("#deleteConfirmation").dialog("close");
//    });

    $(':checkbox[name*="chkSelectRow[]"]').each(function() {
        var id = $(this).attr('id');
        var temp = id.split("_")
        if(($.inArray(temp[2], allowedCandidateListToDelete)) == -1){
            $(this).attr('disabled', 'disabled');
        }

    });
    var fromdate = $('#candidateSearch_fromDate').val();
    $.validator.addMethod("canNameValidation", function(value, element, params) {
        var temp = false;
        var canCount = candidatesArray.length;
        if ($('#candidateSearch_candidateName').hasClass("inputFormatHint")) {
            temp = true
        }

        else if ($('#candidateSearch_candidateName').val() == "") {
            $('#candidateSearch_selectedCandidate').val("");
            temp = true;
        }
        else{
            var i;
            for (i=0; i < canCount; i++) {
                canName = $.trim($('#candidateSearch_candidateName').val()).toLowerCase();
                arrayName = candidatesArray[i].name.toLowerCase();

                if (canName == arrayName) {
                    $('#candidateSearch_selectedCandidate').val(candidatesArray[i].id);
                    temp = true
                    break;
                }
            }
        }
        return temp;
    });
    
    var validator = $("#frmSrchCandidates").validate({

        rules: {
            'candidateSearch[candidateName]' : {
                canNameValidation: true
            },
            'candidateSearch[dateApplication][from]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                }
            },
            'candidateSearch[dateApplication][to]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                },
                date_range: function() {
                    return {
                        format:datepickerDateFormat,
                        displayFormat:displayDateFormat,
                        fromDate:fromdate
                    }
                }
            }
        },
        messages: {
            'candidateSearch[candidateName]' : {
                canNameValidation: lang_enterValidName
            },
            'candidateSearch[dateApplication][from]' : {
                valid_date: lang_validDateMsg
            },
            'candidateSearch[dateApplication][to]' : {
                valid_date: lang_validDateMsg,
                date_range: lang_dateError
            }

        }
    });

});

function addCandidate(){
    window.location.replace(addCandidateUrl);
}


function getHiringManagerListJson(url, para){

    $.getJSON(url, function(data) {

        var numOptions = data.length;
        var optionHtml = '<option value="">'+lang_all+'</option>';

        for (var i = 0; i < numOptions; i++) {
            // escape name
            var name = $('<div/>').text(data[i].name).html();
            if (data[i].id == para) {
                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + name + '</option>';
            } else {
                optionHtml += '<option value="' + data[i].id + '">' + name + '</option>';
            }
        }

        $("#candidateSearch_hiringManager").html(optionHtml);

    })

}

function getVacancyListJson(vcUrl, para){
    $.getJSON(vcUrl, function(data) {

        var numOptions = 0;
        if(data != null){
            numOptions = data.length;
        }
        var optionHtml = '<option value="">'+lang_all+'</option>';

        for (var i = 0; i < numOptions; i++) {
            // escape name
            var name = $('<div/>').text(data[i].name).html();
            if (data[i].id == para) {
                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + name + '</option>';
            } else {
                optionHtml += '<option value="' + data[i].id + '">' + name + '</option>';
            }
        }

        $("#candidateSearch_jobVacancy").html(optionHtml);

    })
}
