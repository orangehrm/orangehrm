$(document).ready(function() {
    $("#frmSavejobTitle").validate({

        rules: {
            'jobTitle[jobTitle]' : {
                required:true
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
                required: lang_jobTitleRequired
            },
            'jobTitle[jobDescription]' : {
                maxlength: lang_exceed400Chars
            },
            'jobTitle[note]' : {
                maxlength: lang_exceed400Chars
            }
        },

        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            error.appendTo(element.next('div.errorHolder'));
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

    $("input[name=jobTitle[jobSpecUpdate]]").click(function () {
        if ($('#jobTitle_jobSpecUpdate_3').attr("checked")) {
            $('#fileUploadSection').show();
        } else {
            $('#jobTitle_jobSpec').val("")
            $('#fileUploadSection').hide();
        }
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