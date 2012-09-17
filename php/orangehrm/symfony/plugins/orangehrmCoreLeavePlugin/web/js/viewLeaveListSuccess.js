$(document).ready(function() {
    
    var validator = $("#frmFilterLeave").validate({

        rules: {
            'leaveList[calFromDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                }
            },
            'leaveList[calToDate]' : {
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
                        fromDate:$('#calFromDate').val()
                    }
                }
            }
        },
        messages: {
            'leaveList[calFromDate]' : {
                valid_date: lang_invalidDate
            },
            'leaveList[calToDate]' : {
                valid_date: lang_invalidDate ,
                date_range: lang_dateError
            }

        },
        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
        }

    });

    // disabling dialog by default
    $("#commentDialog").dialog({
        autoOpen: false,
        width: 350,
        height: 300
    });

    //open when the pencil mark got clicked
    $('.dialogInvoker').click(function() {
        $('#ajaxCommentSaveMsg').html('').removeAttr('class');      
        
        //removing errors message in the comment box
        $("#commentError").html("");
        
        /* Extracting the request id */
        var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
        if (!id) {
            id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
        }
        var comment = $('#hdnLeaveComment-' + id).val();

        /* Extracting the status id */
        var statusId = $(this).closest('td').prev('td').find('input[id^="hdnLeaveRequestStatus_'+id+'"]').val();
        
        $('#commentSave').show();
        //disable edit comment for ess for pending approval leave
        if(ess_mode == 1 && statusId != leave_status_pending) {
            $('#commentSave').hide();
        }
        
        $('#leaveId').val(id);
        $('#leaveComment').val(comment);
        
        // If leave comment is empty , enable the edit mode
        if( $('#leaveComment').val().trim() =="") {
            $("#leaveComment").removeAttr("disabled");
            $("#commentSave").attr("value", lang_save);
        } else {
            $("#leaveComment").attr("disabled","disabled");
            $("#commentSave").attr("value", lang_edit);
        }
        
        $('#leaveOrRequest').val('request');

        $('#commentDialog').dialog('open');
    });
    
    //closes the dialog
    $("#commentCancel").click(function() {
        $("#commentDialog").dialog('close');
    });

    //on clicking on save button
    $("#commentSave").click(function() {
        if($("#commentSave").attr("value") == lang_edit) {
            $("#leaveComment").removeAttr("disabled");
            $("#commentSave").attr("value", lang_save);
            return;
        }

        if($('#commentSave').attr('value') == lang_save) {
            $('#commentError').html('');
            var comment = $('#leaveComment').val().trim();
            if(comment.length > 250) {
                $('#commentError').html(lang_length_exceeded_error);
                return;
            }

            /* Setting the comment in the label */
            var commentLabel = trimComment(comment);

            /* If there is no-change between original and updated comments then don't show success message */
            if($('#hdnLeaveComment-' + $("#leaveId").val()).val().trim() == comment) {
                $('#commentDialog').dialog('close');
                return;
            }

            /* We set updated comment for the hidden comment field */
            $('#hdnLeaveComment-' + $('#leaveId').val()).val(comment);

            /* Posting the comment */
            var url = commentUpdateUrl;
            var data = 'leaveRequestId=' + $('#leaveId').val() + '&leaveComment=' + encodeURIComponent(comment);

            /* This is specially for detailed view */
            if($('#leaveOrRequest').val() == 'leave') {
                data = 'leaveId=' + $('#leaveId').val() + '&leaveComment=' + encodeURIComponent(comment);
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(flag) {
                    $('#ajaxCommentSaveMsg').removeAttr('class').html('');
                    $('.messageBalloon_success').remove();

                    if(flag == 1) {
                        var id = $('#leaveId').val();
                        $('#commentContainer-' + id).html(commentLabel);
                        $('#hdnLeaveComment-' + id).val(comment);
                        $('#noActionsSelectedWarning').remove();
                        $('#ajaxCommentSaveMsg').attr('class', 'messageBalloon_success')
                        .html(lang_comment_successfully_saved);
                    }
                }
            });

            $("#commentDialog").dialog('close');
            return;
        }
    });

    $('#btnSearch').click(function() {
        $('#frmFilterLeave input.inputFormatHint').val('');
        $('#frmFilterLeave').submit();
    });


    $('#btnReset').click(function(event) {        
        window.location = resetUrl;
        event.preventDefault();
        return false;
    });
    
    $('select.select_action').bind("change",function() {
        $('div#noActionsSelectedWarning').remove();
    });
});    

function trimComment(comment) {
    if (comment.length > 35) {
        comment = comment.substr(0, 35) + '...';
    }
    return comment;
}