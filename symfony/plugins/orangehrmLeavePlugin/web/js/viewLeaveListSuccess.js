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

        }

    });

    //open comment icon is clicked
    $('.dialogInvoker').click(function() {   
        $('div.message').remove();
        //removing errors message in the comment box
        $("#commentError").html("");
        
        /* Extracting the request id */
        var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
        if (!id) {
            id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
        }
        
        $('#leaveId').val(id);
        $('#leaveComment').val('');
        
        fetchComments(id);
      
        $('#commentDialog').modal();
    });    
    
    //on clicking on save button
    $("#commentSave").click(function() {

        $('#commentError').html('').removeClass('validation-error');
        var rawComment = $('#leaveComment').val().trim();
                
        if(rawComment.length > 250) {
            $('#commentError').html(lang_length_exceeded_error).addClass('validation-error');
            return;
        } else if (rawComment.length == 0) {
            $('#commentError').html(lang_Required).addClass('validation-error');
            return;                                
        }
        
        var comment = $('<div/>').text(rawComment).html();

        /* Setting the comment in the label */
         
        var commentLabel = trimComment(comment);

        /* Posting the comment */
        var data = {
            leaveRequestId: $('#leaveId').val(),
            leaveComment: rawComment,
            token : $('#leaveComment__csrf_token').val()
        }

        $.ajax({
            type: 'POST',
            url: commentUpdateUrl,
            data: data,
            success: function(data) {
                $('div.message').remove();
                if(data != 0) {
                    var id = $('#leaveId').val();
                    $('#commentContainer-' + id).html(commentLabel);                        
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

    $('#btnSearch').click(function() {
        $('#frmFilterLeave input.inputFormatHint').val('');
        $('#frmFilterLeave input.ac_loading').val('');
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
    
function fetchComments(leaveRequestId) {

    $('#existingComments').html(lang_Loading);
    params = 'leaveRequestId=' + leaveRequestId;
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