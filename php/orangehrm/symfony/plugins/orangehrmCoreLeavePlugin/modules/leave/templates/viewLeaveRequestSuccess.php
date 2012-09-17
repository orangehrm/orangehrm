<?php
$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
?>


<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveListSuccess'); ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js') ?>"></script>


<?php if ($messageType == "messageBalloon_notice") {
    ?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo $title; ?></h2></div>

</div> <!-- End of outerbox -->
<?php if ($messageType == "messageBalloon_success") {
    ?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div id="processing"></div>

<!--this is ajax message place -->
<div id="msgPlace"></div>
<!-- end of ajax message place -->

<?php include_component('core', 'ohrmList'); ?>
<input type="hidden" name="hdnMode" value="<?php echo $mode; ?>" />
<!-- comment dialog -->

<div id="commentDialog" title="<?php echo __('Leave Comment'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="10" class="commentTextArea"></textarea>
        <br class="clear" />
        <div class="error" id="commentError"></div>
        <div><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Edit'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>

<!-- end of comment dialog-->


<script type="text/javascript">
    //<![CDATA[

    var leaveRequestId = <?php echo $leaveRequestId; ?>;
    var leave_status_pending = '<?php echo PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL_TEXT; ?>';
    var ess_mode = '<?php echo ($essMode) ? '1' : '0'; ?>';
    
    function handleSaveButton() {
        $('#processing').html('');
        $('.messageBalloon_success').remove();
        $('.messageBalloon_warning').remove();
        $(this).attr('disabled', true);
        $('select[name^="select_leave_action_"]').each(function() {
            var id = $(this).attr('id').replace('select_leave_action_', '');
            if ($(this).val() == '') {
                $('#hdnLeaveRequest_' + id).attr('disabled', true);
            } else {
                $('#hdnLeaveRequest_' + id).val($(this).val());
            }
            
            if ($(this).val() == '') {
                $('#hdnLeave_' + id).attr('disabled', true);
            } else {
                $('#hdnLeave_' + id).val($(this).val());
            }
        });
        
        var action = $('#frmList_ohrmListComponent').attr('action');
        action = action + '/id/' + leaveRequestId;
        
        $('#frmList_ohrmListComponent').attr('action', action);
        $('#processing').html('<div class="messageBalloon_success">'+"<?php echo __('Processing'); ?>"+'...</div>');
        // check the correct url here
        $('#frmList_ohrmListComponent').submit();
    }

    function handleBackButton() {
        window.location = '<?php echo url_for($backUrl); ?>';
        return false;
    }

    var mode = 'detailed';



    $(document).ready(function(){

        //disabling dialog by default
        $("#commentDialog").dialog({
            autoOpen: false,
            width: 350,
            height: 300
        });

        //open when the pencil mark got clicked
        $('.dialogInvoker').click(function() {
            $("#leaveComment").attr("disabled","disabled");
            //removing errors message in the comment box
            $("#commentError").html("");

            $("#commentSave").attr("value", "<?php echo __('Edit'); ?>");

            /* Extracting the request id */
            var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
            if (!id) {
                var id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
            }
            
            /* Extracting the status text */
            var status = $.trim($(this).closest('td').prev('td').text());

            $('#commentSave').show();
            //disable edit comment for ess for pending approval leave
            if(ess_mode == 1 && status != leave_status_pending) {
                $('#commentSave').hide();
            }
            
            var comment = $('#hdnLeaveComment-' + id).val();
            var typeOfView = (mode == 'compact') ? 'request' : 'leave';

            $('#leaveId').val(id);
            $('#leaveComment').val(comment);
            $('#leaveOrRequest').val(typeOfView);

            $('#commentDialog').dialog('open');
        });                

        //closes the dialog
        $("#commentCancel").click(function() {
            $("#commentDialog").dialog('close');
        });

        //on clicking on save button
        $("#commentSave").click(function() {
            if($("#commentSave").attr("value") == "<?php echo __('Edit'); ?>") {
                $("#leaveComment").removeAttr("disabled");
                $("#commentSave").attr("value", "<?php echo __('Save'); ?>");
                return;
            }

            if($('#commentSave').attr('value') == "<?php echo __('Save'); ?>") {
                $('#commentError').html('');
                var comment = $('#leaveComment').val().trim();
                if(comment.length > 250) {
                    $('#commentError').html('<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>');
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
                var url = '<?php echo public_path('index.php/leave/updateComment'); ?>';
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
                        $('#msgPlace').removeAttr('class');
                        $('.messageBalloon_success').remove();
                        $('#msgPlace').html('');
                        if(flag == 1) {
                            var id = $('#leaveId').val();
                            $('#commentContainer-' + id).html(commentLabel);
                            $('#hdnLeaveComment-' + id).val(comment);
                            $('#msgPlace').attr('class', 'messageBalloon_success');
                            $('#msgPlace').html('<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>');
                        }
                    }
                });

                $("#commentDialog").dialog('close');
                return;
            }
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

    //]]>
</script>

