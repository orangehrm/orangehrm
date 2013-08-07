function handleSaveButton() {
    $('#processing').html('');
    $('.messageBalloon_success').remove();
    $('.messageBalloon_warning').remove();
    $(this).attr('disabled', true);
    
    var selectedActions = 0;
    
    $('select[name^="select_leave_action_"]').each(function() {
        var id = $(this).attr('id').replace('select_leave_action_', '');
        if ($(this).val() == '') {
            $('#hdnLeaveRequest_' + id).attr('disabled', true);
        } else {
            selectedActions++;
            $('#hdnLeaveRequest_' + id).val('WF' + $(this).val());
        }

        if ($(this).val() == '') {
            $('#hdnLeave_' + id).attr('disabled', true);
        } else {
            $('#hdnLeave_' + id).val('WF' + $(this).val());
        }
    });

    if (selectedActions > 0) {
        var action = $('#frmList_ohrmListComponent').attr('action');
        action = action + '/id/' + leaveRequestId;

        $('#frmList_ohrmListComponent').attr('action', action);

        $('#helpText').before('<div class="message success">' + lang_Processing + '</div>');

        // check the correct url here
        $('#frmList_ohrmListComponent').submit();
    } else {
        $('#helpText').before('<div class="message warning fadable">' + lang_selectAction + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
        setTimeout(function(){
            $("div.fadable").fadeOut("slow", function () {
                $("div.fadable").remove();
            });
        }, 2000);
        $(this).attr('disabled', false);      
        return false;
    }
        

}

function handleBackButton() {
    window.location = backUrl;
    return false;
}

var mode = 'detailed';

$(document).ready(function(){

    $('#view_request_comments').click(function() {
        
        //removing errors message in the comment box
        $('#commentError').html('').removeClass('validation-error');         
        $('#leaveId').val(leaveRequestId);
        $('#leaveOrRequest').val('request');        
        fetchComments(leaveRequestId);
        
        $('#leaveComment').val('');
        
        var leaveDate = $(this).parents('tr').find('td:eq(0)').text();        
        $('#commentDialog h3').html(lang_LeaveRequestComments);
        
        $('#commentDialog').modal();        
    });
    
    //open when the pencil mark got clicked
    $('.dialogInvoker').click(function() {

        //removing errors message in the comment box
        $('#commentError').html('').removeClass('validation-error');

        /* Extracting the request id */
        var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
        if (!id) {
            id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
        }            

        $('#leaveId').val(id);
        $('#leaveOrRequest').val('leave');
        fetchComments(id);
        $('#leaveComment').val('');
        
        var leaveDate = $(this).parents('tr').find('td:eq(0)').text();        
        $('#commentDialog h3').html(lang_LeaveComments + ' - ' + leaveDate);
        
        $('#commentDialog').modal();
    });                

    //closes the dialog
    $("#commentCancel").click(function() {
        $("#commentDialog").modal('hide');
    });

    //on clicking on save button
    $("#commentSave").click(function() {

        $('#commentError').html('').removeClass('validation-error');
        var rawComment = $('#leaveComment').val().trim();
        
        if(rawComment.length > 255) {
            $('#commentError').html(lang_LengthExceeded).addClass('validation-error');
            return;
        } else if (rawComment.length == 0) {
            $('#commentError').html(lang_Required).addClass('validation-error');
            return;                                
        }

        var comment = $('<div/>').text(rawComment).html();

        /* Setting the comment in the label */
        var commentLabel = trimComment(comment);

        var leaveOrRequest = $('#leaveOrRequest').val();
        
        // Comment will be encoded by jquery .ajax method
        var data = {
            leaveComment: rawComment,
            token : $('#leaveComment__csrf_token').val()
        };
        
        if (leaveOrRequest == 'leave') {
            data['leaveId'] = $('#leaveId').val();
        } else {
            data['leaveRequestId'] = $('#leaveId').val();
        }        
        
        /* Posting the comment */
        $.ajax({
            type: 'POST',
            url: commentUpdateUrl,
            data: data,
            success: function(data) {
                $('#msgPlace').removeAttr('class');
                $('.messageBalloon_success').remove();
                $('#msgPlace').html('');

                if(data != 0) {
                    var id = $('#leaveId').val();
                    
                    if (leaveOrRequest == 'leave') {                    
                        $('#commentContainer-' + id).html(commentLabel);
                    }
                    $('#noActionsSelectedWarning').remove();

                    $('#helpText').before('<div class="message success fadable">' + lang_comment_successfully_saved + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
                } else {
                    $('#helpText').before('<div class="message warning fadable">' + lang_comment_save_failed + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');                        
                }
                setTimeout(function(){
                    $("div.fadable").fadeOut("slow", function () {
                        $("div.fadable").remove();
                    });
                }, 2000);

            }
        });

        $("#commentDialog").modal('hide');
        return;

    });



    $('select.select_action').bind("change",function() {

        var requestId = $(this).attr('name').substring(20);

        if (mode == 'detailed') {
            $('#leave-'+requestId).val($(this).val());
        } else {
            $('#leaveRequest-'+requestId).val($(this).val());
        }

    });


});

function trimComment(comment) {
    if (comment.length > 35) {
        comment = comment.substr(0, 35) + '...';
    }
    return comment;
}

function fetchComments(id) {

    $('#existingComments').html(lang_Loading);
    
    var leaveOrRequest = $('#leaveOrRequest').val();
    
    if (leaveOrRequest == 'leave') {
        params = 'leaveId=' + id;
    } else {
        params = 'leaveRequestId=' + id;
    }
    
    $.ajax({
        type: 'GET',
        url: getCommentsUrl,
        data: params,
        dataType: 'json',
        success: function(data) {                

            var count = data.length;
            var html = '';
            var rows = 0;

            $('#existingComments').html('');  
            if (count > 0) {
                html = "<table class='table'><tr><th>"+lang_Date+"</th><th>"+lang_Time+"</th><th>"+lang_Author+"</th><th>"+lang_Comment+"</th></tr>";
                for (var i = 0; i < count; i++) {
                    var css = "odd";
                    rows++;
                    if (rows % 2) {
                        css = "even";
                    }
                    var comment = $('<div/>').text(data[i]['comments']).html();
                    html = html + '<tr class="' + css + '"><td>'+data[i]['date']+'</td><td>'+data[i]['time']+'</td><td>'
                        +data[i]['author']+'</td><td>'+comment+'</td></tr>';
                }
                html = html + '</table>';
            } else {

            }
            $('#existingComments').append(html);
        }
    });
}