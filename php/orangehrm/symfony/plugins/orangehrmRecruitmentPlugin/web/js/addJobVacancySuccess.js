$(document).ready(function() {

    if($("#btnSave").attr('value') == lang_edit) {
        $(".drpDown").attr('disabled', 'disabled');
        $(".formInput").attr('disabled', 'disabled');
        $(".formSelect").attr('disabled', 'disabled');
        $(".formInputText").attr('disabled', 'disabled');
    }
    //Auto complete
    $("#addJobVacancy_hiringManager").autocomplete(hiringManagers, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        //$("#candidateSearch_selectedCandidate").val(item.id);
        //$("label.error").hide();
        });

    $('#btnSave').click(function() {

        $('#addJobVacancy_vacancyId').val(vacancyId);

        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == lang_edit) {
            $(".drpDown").removeAttr("disabled");
            $(".formSelect").removeAttr("disabled");
            $(".formInput").removeAttr("disabled");
            $(".formInputText").removeAttr("disabled");
            $("#btnSave").attr('value', lang_save);
            $("#btnBack").attr('value', lang_cancel);
            return;
        }
        if($("#btnSave").attr('value') == lang_save) {
            if(isValidForm()){
                $('form#frmAddJobVacancy').attr({
                    action:linkForAddJobVacancy+"?Id="+vacancyId
                });
                $('#frmAddJobVacancy').submit();
            }
        }
		
    });

    $('#btnBack').click(function(){
        if($("#btnBack").attr('value') == lang_back) {
            window.location.replace(backBtnUrl+'?vacancyId='+vacancyId);
        }
        if($("#btnBack").attr('value') == lang_cancel) {
            window.location.replace(backCancelUrl+'?Id='+vacancyId);
        }
    });

    if ($("#addJobVacancy_hiringManager").val() == '') {
        $("#addJobVacancy_hiringManager").val(lang_typeForHints)
        .addClass("inputFormatHint");
    }

    $("#addJobVacancy_hiringManager").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
});

function isValidForm(){
    $.validator.addMethod("hiringManagerNameValidation", function(value, element, params) {
        var temp = false;
        var hmCount = hiringManagersArray.length;

        var i;
        for (i=0; i < hmCount; i++) {
            hmName = $.trim($('#addJobVacancy_hiringManager').val()).toLowerCase();
            arrayName = hiringManagersArray[i].name.toLowerCase();
            if (hmName == arrayName) {
                $('#addJobVacancy_hiringManagerId').val(hiringManagersArray[i].id);
                temp = true
                break;
            }
        }
        return temp;
    });

    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentVacancy;
        var id = parseInt(vacancyId,10);
        var vcCount = vacancyNameList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == vacancyNameList[j].id){
                currentVacancy = j;
            }
        }
        var i;
        vcName = $.trim($('#addJobVacancy_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = vacancyNameList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentVacancy != null){
            if(vcName == vacancyNameList[currentVacancy].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });

    $.validator.addMethod("integer", function(value, element, params) {
        value = $('#addJobVacancy_noOfPositions').val();
        return (value =="" ||(value == parseInt(value, 10)));
    });

    var validator = $("#frmAddJobVacancy").validate({

        rules: {
            'addJobVacancy[jobTitle]' : {
                required:true
            },
            'addJobVacancy[name]' : {
                uniqueName:true,
                required:true
            },
            'addJobVacancy[noOfPositions]' : {
                required:false,
                integer: true,
                min: 0,
                max: 99
            },
            'addJobVacancy[hiringManager]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[description]' : {
            	maxlength:40000
            }

        },
        messages: {
            'addJobVacancy[jobTitle]' : {
                required:lang_jobTitleRequired
            },
            'addJobVacancy[name]' : {
                uniqueName:lang_nameExistmsg,
                required:lang_vacancyNameRequired
            },
            'addJobVacancy[noOfPositions]' : {
                integer: lang_negativeAmount,
                min: lang_negativeAmount,
                max: lang_tooLargeAmount
            },
            'addJobVacancy[hiringManager]' : {
                hiringManagerNameValidation:lang_enterAValidEmployeeName
            },
            'addJobVacancy[description]' : {
            	maxlength: lang_descriptionLength
            }

        },
        //        errorElement : 'label',
        errorPlacement: function(error, element) {
            //            error.appendTo( element.prev('label') );
            error.insertBefore(element.next(".clear"));

        //these are specially for date boxes
        //            error.insertBefore(element.next().next(".clear"))
        }

    });
    return true;
}