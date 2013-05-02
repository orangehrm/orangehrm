$(document).ready(function() {

    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentJobTitle;
        var id = parseInt(jobTitleId,10);
        var vcCount = jobTitleList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == jobTitleList[j].id){
                currentJobTitle = j;
            }
        }
        var i;
        jobTitleName = $.trim($('#jobTitle_jobTitle').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = jobTitleList[i].name.toLowerCase();
            if (jobTitleName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentJobTitle != null){
            if(jobTitleName == jobTitleList[currentJobTitle].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });


    $("#frmSavejobTitle").validate({

        rules: {
            'jobTitle[jobTitle]' : {
                required:true,
                uniqueName:true
            },
            'jobTitle[jobDescription]' : {
                maxlength: 400
            },
            'jobTitle[note]' : {
                maxlength: 400
            }
        },
        messages: {
            'jobTitle[jobTitle]' : {
                required: lang_jobTitleRequired,
                uniqueName: lang_uniqueName
            },
            'jobTitle[jobDescription]' : {
                maxlength: lang_exceed400Chars
            },
            'jobTitle[note]' : {
                maxlength: lang_exceed400Chars
            }
        }
    });

    if(jobTitleId != ""){
        makeViewMode()
    }

    $('#btnSave').click(function(){
        if($(this).attr('value') == lang_edit){
            makeEditMode()
        }else{
            $('#frmSavejobTitle').submit();
        }
    })

    $('#btnCancel').click(function(){
        window.location.replace(viewJobTitleListUrl);
    });

    $("#jobTitle_jobSpecUpdate_3").click(function () {
        $('#fileUploadSection').show();
    });
    
    $("#jobTitle_jobSpecUpdate_2").click(function () {
        $('#jobTitle_jobSpec').val("")
        $('#fileUploadSection').hide();
    });
    
    $("#jobTitle_jobSpecUpdate_1").click(function () {
        $('#jobTitle_jobSpec').val("")
        $('#fileUploadSection').hide();
    });
});

function makeViewMode(){
    $('.formInputText').attr('disabled', 'disabled')
    $('.formInputTextArea').attr('disabled', 'disabled')
    $('#jobTitle_jobSpec').attr('disabled', 'disabled')
    $('#radio').hide()
    $('#fileUploadSection').hide()
    $('#btnSave').attr('value', lang_edit)
}

function makeEditMode(){
    $('.formInputText').removeAttr('disabled')
    $('.formInputTextArea').removeAttr('disabled')
    $('#jobTitle_jobSpec').removeAttr('disabled')
    $('#radio').show()
    $('#jobTitle_jobSpecUpdate_1').attr('checked', 'checked');
    $('#btnSave').attr('value', lang_save)
}