$(document).ready(function() { 
    
    /*Validate add performance Tracker Log form*/    
    var validator = $("#frmAddperformanceTrackerLog").validate({
        rules: {
            'addperformanceTrackerLog[log]' : {
                required:true,
                maxlength: 150
            },
            'addperformanceTrackerLog[comment]' : {
                required:true,
                maxlength: 3000
            }
        },
        messages: {
            'addperformanceTrackerLog[log]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed150Charactors
            },
            'addperformanceTrackerLog[comment]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed3000Charactors
            }
            
        }

    });

    
    //open when the pencil mark got clicked
    $('.dialogInvoker').click(function(){
        $('#ajaxCommentSaveMsg').html('').removeAttr('class');      
        
        //removing errors message in the comment box
        $("#commentError").html("");
        
        /* Extracting the request id */
        var id = $(this).parent().siblings('input[id^="hdnTrackLog_"]').val();
        /*if (!id) {
            id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
        }*/
        var comment = $('#hdnTrackLogComment-' + id).val();
        $('#commentSave').show();
        //disable edit comment for ess for pending approval leave
        if(false) {
            $('#commentSave').hide();
        }
        $('#trackLogId').val(id);
        $('#trackLogComment').val(comment);
        
        // If leave comment is empty , enable the edit mode
        if( $('#trackLogComment').val().trim() =="") {
            $("#trackLogComment").removeAttr("disabled");
            $("#commentSave").attr("value", lang_save);
        } else {
            $("#trackLogComment").attr("disabled","disabled");
            $("#commentSave").attr("value", lang_edit);
        }
        $('#commentDialog').modal();
        
    }); 
    
    //on clicking on save button
    $("#commentSave").click(function() {
        if($("#commentSave").attr("value") == lang_edit) {
            $("#trackLogComment").removeAttr("disabled");
            $("#commentSave").attr("value", lang_save);
            return;
        }

        if($('#commentSave').attr('value') == lang_save) {
            $('#commentError').html('');
            var comment = $('#trackLogComment').val().trim();
            if(comment.length > 250) {
                $('#commentError').html(lang_length_exceeded_error);
                return;
            }

            /* Setting the comment in the label */
            var commentLabel = trimComment(comment);

            /* If there is no-change between original and updated comments then don't show success message */
            if($('#hdnTrackLogComment-' + $("#trackLogId").val()).val().trim() == comment) {
                $('#commentDialog').modal('hide');
                return;
            }

            /* We set updated comment for the hidden comment field */
            $('#hdnTrackLogComment-' + $('#trackLogId').val()).val(comment);

            /* Posting the comment */
            var url = commentUpdateUrl;
            var data = 'trackLogId=' + $('#trackLogId').val() + '&trackLogComment=' + encodeURIComponent(comment);
   
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(flag) {
                    $('#ajaxCommentSaveMsg').removeAttr('class').html('');
                    $('.messageBalloon_success').remove(); 
                    if(flag == 1) {
                        var id = $('#trackLogId').val();
                         
                        $('#commentContainer-' + id).html(commentLabel);
                        $('#hdnTrackLogComment-' + id).val(comment);
                        $('#noActionsSelectedWarning').remove();
                        
                        //$('#helpText').before(content);
                        
                        //$('#ajaxCommentSaveMsg')
                        $('#helpText').before('<div class="message success fadable">' + lang_comment_successfully_saved + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
                        setTimeout(function(){
                            $("div.fadable").fadeOut("slow", function () {
                                $("div.fadable").remove();
                            });
                        }, 2000);
                    } 
                }
            });

            $("#commentDialog").modal('hide');
            return;
        }
    });
    
    /*handling model box end*/
    
    if($('#addperformanceTrackerLog_hdnMode').val() != 'edit'){
        $('#performanceTrackerLog').hide();
    }else{         
        $('#btnAdd').hide();
    }
    /*             
    $('#btnAssignEmployee').click(function() {
        return !$('#addPerformanceTracker_availableEmp option:selected').remove().appendTo('#addPerformanceTracker_assignedEmp');  
    }); 
    
    $('#btnRemoveEmployee').click(function() {  
        return !$('#addPerformanceTracker_assignedEmp option:selected').remove().appendTo('#addPerformanceTracker_availableEmp');  
    }); 
    */
    $('#btnAdd').click(function() { 
        
        $('#performanceTrackerLog').show();        
        $('#btnAdd').hide();
    });
  
    $('#saveBtn').click(function() {

        $('#frmAddperformanceTrackerLog').submit();
        
    });
    
    $('#resetBtn').click(function() { 
        $('#btnAdd').show();
        
        //resetting all the form fields except addperformanceTrackerLog_hdnTrckId field.
        $("#addperformanceTrackerLog_hdnLogId").val("");
        $("#addperformanceTrackerLog_hdnMode").val("");
        $("#addperformanceTrackerLog_log").val("");
        $("#addperformanceTrackerLog_comment").val("");
        $("#addperformanceTrackerLog_achievement").find('option:first').attr('selected','selected');

        $('#performanceTrackerLog').hide();
        $('.top').show();
    });
    
});

function resetMultipleSelectBoxes(){ 
    
    $('#addPerformanceTracker_assignedEmp')[0].options.length = 0;
    $('#addPerformanceTracker_availableEmp')[0].options.length = 0;

    for(var i=0; i<employeeList.length; i++){
        $('#addPerformanceTracker_availableEmp').
        append($("<option></option>").
            attr("value",employeeList[i].id).
            text(employeeList[i].name)); 
    }
}


function trimComment(comment) {
    if (comment.length > 35) {
        comment = comment.substr(0, 35) + '...';
    }
    return comment;
}




