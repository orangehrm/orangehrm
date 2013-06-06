
$(document).ready(function() {
    
    $('#btnDeleteAttachment').attr('disabled', 'disabled');
    
    

    $('#addPaneAttachments').hide();
    $("#frmRecAttachment").data('add_mode', true);

    jQuery.validator.addMethod("attachment",
        function() {

            var addMode = $("#frmRecAttachment").data('add_mode');
            if (!addMode) {
                return true;
            } else {
                var file = $('#recruitmentAttachment_ufile').val();
                return file != "";
            }
        }, ""
        );
    var attachmentValidator =
    $("#frmRecAttachment").validate({

        rules: {
            'recruitmentAttachment[ufile]' : {
                attachment:true
            },
            'recruitmentAttachment[comment]': {
                maxlength: 250
            }
        },
        messages: {
            'recruitmentAttachment[ufile]': lang_PleaseSelectAFile,
            'recruitmentAttachment[comment]': {
                maxlength: lang_CommentsMaxLength
            }
        }
            
    });

    //if check all button clicked
    $("#attachmentsCheckAll").click(function() {
        $("table#tblAttachments tbody input.checkboxAtch").removeAttr("checked");
        if($("#attachmentsCheckAll").attr("checked")) {
            $("table#tblAttachments tbody input.checkboxAtch").attr("checked", "checked");
        }
        if($('table#tblAttachments tbody .checkboxAtch:checkbox:checked').length > 0) {
            $('#btnDeleteAttachment').removeAttr('disabled');
        } else {
            $('#btnDeleteAttachment').attr('disabled', 'disabled');
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("table#tblAttachments tbody input.checkboxAtch").click(function() {
        $("#attachmentsCheckAll").removeAttr('checked');
        if($("table#tblAttachments tbody input.checkboxAtch").length == $("table#tblAttachments tbody input.checkboxAtch:checked").length) {
            $("#attachmentsCheckAll").attr('checked', 'checked');
        }
        if($('table#tblAttachments tbody .checkboxAtch:checkbox:checked').length > 0) {
            $('#btnDeleteAttachment').removeAttr('disabled');
        } else {
            $('#btnDeleteAttachment').attr('disabled', 'disabled');
        }
    });
    // Edit an attachment in the list
    $('#attachmentList a.editLink').click(function(event) {
        event.preventDefault();
            
        if (clearAttachmentMessages) {
            $("#attachmentsMessagebar").text("").attr('class', "");
        }
            
        attachmentValidator.resetForm();
            
        var row = $(this).closest("tr");
        var seqNo = row.find('input.checkboxAtch:first').val();
        var fileName = row.find('a.fileLink').text();
        var description = row.find("td:nth-child(5)").text();
        description = jQuery.trim(description); 

        $('#recruitmentAttachment_recruitmentId').val(seqNo);
        $('#recruitmentAttachment_ufile').removeAttr("disabled");
            
        $('#recruitmentAttachment_comment').val(description);

        $("#frmRecAttachment").data('add_mode', false);

        $('#btnCommentOnly').show();

        // hide validation error messages
        $("label.error1col[generated='true']").css('display', 'none');
        $('#attachmentActions').hide();
            
        $("table#tblAttachments input.checkboxAtch").hide();
            
        $('#addPaneAttachments').show();
        $('#saveHeading h1').text(lang_EditAttachmentHeading);
            
        $('#currentFileLi').show();
        $('#currentFileSpan').text(fileName);
        $('#selectFileSpan').text(lang_ReplaceWith);
            
    });

    $('#btnAddAttachment').click(function() {
            
        $('#currentFileLi').hide();
        $('#selectFileSpan').text(lang_SelectFile);
            
        if (clearAttachmentMessages) {
            $("#attachmentsMessagebar").text("").attr('class', "");
        }
        $('#recruitmentAttachment_recruitmentId').val('');
        $('#attachmentEditNote').text('');
        $('#recruitmentAttachment_comment').val('');

        $("#frmRecAttachment").data('add_mode', true);
        $('#btnCommentOnly').hide();

        // hide validation error messages
        $("label.error1col[generated='true']").css('display', 'none');
            
        $('#recruitmentAttachment_ufile').removeAttr("disabled");
        $('#attachmentActions').hide();
        $('#saveHeading h1').text(lang_AddAttachmentHeading);
        $('#addPaneAttachments').show();
            
        $("table#tblAttachments input.checkboxAtch").hide();
        $("table#tblAttachments a.editLink").hide();
            
        if (hideAttachmentListOnAdd) {
            $('#attachmentList').hide();
        }
            
    });
        
    $('#cancelButton').click(function() {
        $("#attachmentsMessagebar").text("").attr('class', "");
            
        attachmentValidator.resetForm();
        $('#addPaneAttachments').hide();
        $('#attachmentActions').show();
        $('#recruitmentAttachment_ufile').val('');
        $('#recruitmentAttachment_comment').val('');
        $('#attachmentList').show();
        $("table#tblAttachments input.checkboxAtch").show();
        $("table#tblAttachments a.editLink").show();            
    });
        
    $('#btnDeleteAttachment').click(function() {

        var checked = $('#attachmentList input:checked').length;

        if (checked > 0) {
            $('#frmRecDelAttachments').submit();
        }
            
    });

    $('#btnSaveAttachment').click(function() {
        $('#recruitmentAttachment_vacancyId').val(id);
        $("#frmRecAttachment").data('add_mode', true);
        $('#frmRecAttachment').submit();
    });
        
    $('#btnCommentOnly').click(function() {
        $('#recruitmentAttachment_commentOnly').val('1');
        $("#frmRecAttachment").data('add_mode', false);
        $('#frmRecAttachment').submit();
    });
});