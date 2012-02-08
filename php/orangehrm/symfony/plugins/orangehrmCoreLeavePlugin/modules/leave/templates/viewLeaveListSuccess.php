<?php
/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
$searchActionButtons = $form->getSearchActionButtons();

use_stylesheet('orangehrm.datepicker.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../orangehrmCoreLeavePlugin/css/viewLeaveListSuccess');

use_stylesheets_for_form($form);

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.datepicker.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
use_javascript('orangehrm.datepicker.js');

use_javascripts_for_form($form);
?>


<?php if ($messageType == "messageBalloon_notice") { ?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __($form->getTitle()); ?></h2></div>

    <div class="formWrapper">
        <form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">

            <?php echo $form->render(); ?>
            <br class="clear" />

            <div class="buttonWrapper">
                <?php
                foreach ($searchActionButtons as $id => $button) {
                    echo $button->render($id), "\n";
                }
                ?>
            </div>
        </form>
    </div>

</div> <!-- End of outerbox -->

<?php if ($messageType == "messageBalloon_success") {
    ?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>

<!--this is ajax message place -->
<div id="ajaxCommentSaveMsg"></div>
<!-- end of ajax message place -->

<?php include_component('core', 'ohrmList'); ?>

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
function submitPage(pageNo) {
    location.href = '<?php echo url_for($baseUrl . '?pageNo=');?>' + pageNo;
}

function handleSaveButton() {
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

    $('#frmList_ohrmListComponent').submit();
}


$(document).ready(function() {

    var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";

    if ($("#leaveList_txtEmployee").val() == '' || $("#leaveList_txtEmployee").val() == lang_typeHint) {
        $("#leaveList_txtEmployee").addClass("inputFormatHint").val(lang_typeHint);
    }

    $("#leaveList_txtEmployee").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

    var data = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()); ?>
    
    //Auto complete
    $("#leaveList_txtEmployee").autocomplete(data, {
        formatItem: function(item) {
            return item.name;
        }
        ,matchContains:true
    }).result(function(event, item) {
        $('#leaveList_txtEmpID').val(item.id);
    });

    $("#leaveList_txtEmployee").change(function(){
        autoFill('leaveList_txtEmployee', 'leaveList_txtEmpID', data);
    });

    function autoFill(selector, filler, data) {
        $("#" + filler).val(0);
        if($("#" + selector).val().trim() == "") {
            $("#" + filler).val("");
        }

        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                return true;
            }
        });
    }

    var resetUrl = '<?php echo url_for($baseUrl . '?reset=1');?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_dateError = '<?php echo __("To date should be after the From date") ?>';
    var lang_invalidDate = '<?php echo __("Please enter a valid date in %format% format", array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>'

    var validator = $("#frmFilterLeave").validate({

        rules: {
            'leaveList[calFromDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false
                    }
                }
            },
            'leaveList[calToDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false
                    }
                },
                date_range: function() {
                    return {
                        format:datepickerDateFormat,
                        fromDate:$('#calFromDate').val()
                    }
                }
            },
            'leaveList[chkSearchFilter][]': {
                required: true,
                minlength: 1
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
        $("#leaveComment").attr("disabled","disabled");
        //removing errors message in the comment box
        $("#commentError").html("");

        $("#commentSave").attr("value", "<?php echo __('Edit'); ?>");

        /* Extracting the request id */
        var id = $(this).parent().siblings('input[id^="hdnLeaveRequest_"]').val();
        if (!id) {
            var id = $(this).parent().siblings('input[id^="hdnLeave_"]').val();
        }
        var comment = $('#hdnLeaveComment-' + id).val();

        $('#leaveId').val(id);
        $('#leaveComment').val(comment);
        $('#leaveOrRequest').val('request');

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
                $('#commentError').html('<?php echo __('Comment length should be less than 250 characters'); ?>');
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
                    $('#ajaxCommentSaveMsg').removeAttr('class').html('');
                    $('.messageBalloon_success').remove();

                    if(flag == 1) {
                        var id = $('#leaveId').val();
                        $('#commentContainer-' + id).html(commentLabel);
                        $('#hdnLeaveComment-' + id).val(comment);
                        $('#ajaxCommentSaveMsg').attr('class', 'messageBalloon_success')
                                  .html('<?php echo __('Comment Successfully Saved'); ?>');
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

        var requestId = $(this).attr('name').substring(20);

        if (mode == 'detailed') {
            $('#leave-'+requestId).val($(this).val());
        } else {
            $('#leaveRequest-'+requestId).val($(this).val());
        }

    });

});
    
function setPage() {}

function trimComment(comment) {
    if (comment.length > 35) {
        comment = comment.substr(0, 35) + '...';
    }
    return comment;
}
    
//]]>
</script>

