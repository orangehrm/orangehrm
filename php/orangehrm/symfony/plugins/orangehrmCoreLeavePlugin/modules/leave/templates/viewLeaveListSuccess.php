<?php
$statusFilters = $form->getStatusFilters();

$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
$leaveData = $form->getList();
$searchActionButtons = $form->getSearchActionButtons();
?>

<?php $isDefaultPageView = !empty($isDefaultPage) ? $isDefaultPage : 0; ?>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveListSuccess'); ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<?php if ($messageType == "messageBalloon_notice") {
?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __($form->getTitle()); ?></h2></div>
    <?php if (!$form->isDetailed()) {
 ?>
        <div class="formWrapper">
            <form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">

<?php echo $form['_csrf_token']; ?>
            <label class="mainLabel"><?php echo __("From"); ?></label>
<?php echo $form['calFromDate']->render(); ?>
            <br class="clear" />
            <label class="mainLabel"><?php echo __("To"); ?></label>
<?php echo $form['calToDate']->render(); ?>
            <br class="clear" />

            <label class="mainLabel"><?php echo __("Show Leave with Status"); ?></label>
            <?php foreach ($statusFilters as $filter) {
 ?>
            <?php echo $filter->render($filter->getName()) ?>
            <?php } ?>
<?php if (isset($form['txtEmployee'])) {
?>
                <br class="clear" />
            <?php echo $form['txtEmployee']->renderLabel(__("Employee"), array('class' => 'mainLabel')); ?>
            <?php echo $form['txtEmployee']->render(); ?>
            <?php echo $form['txtEmpID']->render(); ?>
            <?php } ?>
<?php if (isset($form['cmbSubunit'])) {
?>
                <br class="clear" />
            <?php echo $form['cmbSubunit']->renderLabel(__("Sub Unit"), array('class' => 'mainLabel')); ?>
            <?php echo $form['cmbSubunit']->render(); ?>
<?php } ?>
<?php if (isset($form['cmbWithTerminated'])) { ?>
                <br class="clear" />
                <label class="mainLabel"><?php echo __('Include Past Employees'); ?></label><?php echo $form['cmbWithTerminated']->render(); ?>
<?php } ?>
            <br class="clear" />
            <div class="buttonWrapper">
                <?php
                foreach ($searchActionButtons as $id => $button) {
                    echo $button->render($id), "\n";
                }
                ?>
                <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
            </div>
        </form>
    </div>
<?php } ?>
            <input type="hidden" name="pageNoDet" id="pageNoDet" value="<?php echo isset($page) ? $page : ''; ?>" />
            </div> <!-- End of outerbox -->
<?php if ($messageType == "messageBalloon_success") {
?>
                <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>

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

            <div id="overQuotaDialog" title="OrangeHRM - <?php echo __('Confirmation Required'); ?>" style="display: none;">
<?php echo __("Approving this leave will exceed this employee's"); ?><br />
<?php echo __("leave balance for this leave type. Do you want to continue?"); ?>
            <br /><br />
            <div class="dialogButtons">
                <input type="button" id="overQuotaYes" class="savebutton" value="<?php echo __('Yes') ?>" />
                <input type="button" id="overQuotaNo" class="savebutton" value="<?php echo __('No') ?>" />
            </div>
        </div>

        <input type="hidden" id="overQuotaSelectId" value="" />
        <input type="hidden" id="leaveRequestHiddenId" value="" />

        <script type="text/javascript">
            //<![CDATA[

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

                var url = $('#frmList_ohrmListComponent').attr('action');

                /* Suppose if it is the detailed screen */
<?php if (isset($leaveRequestId) && trim($leaveRequestId) != '') { ?>
            url = url + "/id/" + <?php echo $leaveRequestId; ?>;
            $('#frmList_ohrmListComponent').attr('action', url);
<?php } ?>

<?php if (isset($mode) && trim($mode) != '') { ?>
            url = url + "/hdnMode/<?php echo $mode; ?>";
            $('#frmList_ohrmListComponent').attr('action', url);
<?php } ?>
        $('#frmList_ohrmListComponent').submit();
        }

        function handleBackButton() {

        var url = "../../../leave/viewLeaveList/pageNo/" + document.getElementById('pageNoDet').value;

<?php if (isset($mode) && $mode == LeaveListForm::MODE_MY_LEAVE_DETAILED_LIST) { ?>
            url = "../viewMyLeaveList";
<?php } ?>
        window.location = url;
        }

        var mode = '<?php echo ($form->isDetailed()) ? 'detailed' : 'compact'; ?>';
        var quota = new Array();
<?php
            if (isset($quotaArray)) {
                foreach ($quotaArray as $key => $val) {
                    echo "quota[\"" . $key . "\"] = {$val};\n ";
                }
            }
?>

<?php if ($form->isDetailed()): ?>
            var mode = 'detailed';
<?php else: ?>
                var mode = 'compact';
<?php endif; ?>

                var quota = new Array();

<?php

                    if (isset($quotaArray)) {

                        foreach ($quotaArray as $key => $val) {

                            echo "quota[\"" . $key . "\"] = $val;\n ";
                        }

                    }

?>

                function setPage() {

                if (document.frmFilterLeave.pageNo.value) {
                    document.getElementById('frmList_ohrmListComponent').action = document.getElementById('frmList_ohrmListComponent').action + '/currentPage/' + document.frmFilterLeave.pageNo.value;
                }
                }

                function submitPage(pageNo) {

                document.frmFilterLeave.pageNo.value = pageNo;
                document.frmFilterLeave.hdnAction.value = 'paging';
                document.getElementById('frmFilterLeave').submit();

                }

                $(document).ready(function(){

                var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";
                var isInitialPage = "<?php echo $isDefaultPageView; ?>";

                var isMyLeaveListDefaultView = <?php echo $isMyLeaveListDefaultView ? 'true' : 'false'; ?>;

                if(isInitialPage == 1){
                    $('#chkSearchFilter_1').attr('checked', 'checked');
                }

                if(isMyLeaveListDefaultView) {
                    $('.checkbox').attr('checked', 'checked');
                }

                if ($("#txtEmployee").val() == '' || $("#txtEmployee").val() == lang_typeHint) {
                    $("#txtEmployee").addClass("inputFormatHint").val(lang_typeHint);
                }

                $("#txtEmployee").one('focus', function() {

                    if ($(this).hasClass("inputFormatHint")) {
                        $(this).val("");
                        $(this).removeClass("inputFormatHint");
                    }
                });

                var data	= <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()); ?>
                //Auto complete
                $("#txtEmployee").autocomplete(data, {
                    formatItem: function(item) {
                        return item.name;
                    }
                    ,matchContains:true
                }).result(function(event, item) {
                    $('#txtEmpID').val(item.id);
                }
                );

                $("#txtEmployee").change(function(){
                    autoFill('txtEmployee', 'txtEmpID', data);
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

                //dissabling dialog by default
                $("#commentDialog").dialog({
                    autoOpen: false,
                    width: 350,
                    height: 300
                });

                $('input#checkAll').click(function() {
                    $(this).siblings('.checkbox').attr('checked', $(this).attr('checked'));
                });

                $('.checkbox').each(function() {
                    if ($(this).attr('id') != 'checkAll') {
                        $(this).click(function() {
                            var allChecked = true;
                            $(this).siblings('.checkbox').each(function() {
                                if ($(this).attr('id') != 'checkAll') {
                                    allChecked = (allChecked && $(this).attr('checked'));
                                }
                            });
                            allChecked = (allChecked && $(this).attr('checked'));
                            $('input#checkAll').attr('checked', allChecked);
                        });
                    }
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
                    var comment = $('#hdnLeaveComment-' + id).val();
                    var typeOfView = (mode == 'compact') ? 'request' : 'leave';

                    $('#leaveId').val(id);
                    $('#leaveComment').val(comment);
                    $('#leaveOrRequest').val(typeOfView);

                    $('#commentDialog').dialog('open');
                });

                var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
                var lang_dateError = '<?php echo __("To date should be after the From date") ?>';
                var lang_invalidDate = '<?php echo __("Please enter a valid date in %format% format", array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>'

                var validator = $("#frmFilterLeave").validate({

                    rules: {
                        'calFromDate' : {
                            valid_date: function() {
                                return {
                                    format:datepickerDateFormat,
                                    required:false
                                }
                            }
                        },
                        'calToDate' : {
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
                        }
                    },
                    messages: {
                        'calFromDate' : {
                            valid_date: lang_invalidDate
                        },
                        'calToDate' : {
                            valid_date: lang_invalidDate ,
                            date_range: lang_dateError
                        }

                    },
                    errorPlacement: function(error, element) {
                        error.appendTo(element.prev('label'));
                        //                error.appendTo(element.next().next().next('div.errorDiv'));
                    }

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
                                $('#msgPlace').removeAttr('class');
                                $('.messageBalloon_success').remove();
                                $('#msgPlace').html('');
                                if(flag == 1) {
                                    var id = $('#leaveId').val();
                                    $('#commentContainer-' + id).html(commentLabel);
                                    $('#hdnLeaveComment-' + id).val(comment);
                                    $('#msgPlace').attr('class', 'messageBalloon_success');
                                    $('#msgPlace').html('<?php echo __('Comment Successfully Saved'); ?>');
                }
            }
        });

        $("#commentDialog").dialog('close');
        return;
    }
});

$('#btnSearch').click(function() {
    $('#frmFilterLeave').attr('action', $('#frmFilterLeave').attr('action') + '/pageNo/1');
    $('#frmFilterLeave').submit();
});


$('#btnReset').click(function() {
    $('<input/>').attr('type', 'hidden')
    .attr('name', 'reset')
    .attr('value', '1')
    .appendTo('#frmFilterLeave');
                
    $('#frmFilterLeave').submit();
});

$('select.select_action').bind("change",function() {

    var requestId = $(this).attr('name').substring(20);

    if (mode == 'detailed') {
        $('#leave-'+requestId).val($(this).val());
    } else {
        $('#leaveRequest-'+requestId).val($(this).val());
    }

});


/* Checking over quota: Begins */

/* Registering overQuota dialog */
$("#overQuotaDialog").dialog({
    autoOpen: false,
    modal: true,
    width: 350,
    height: 90,
    position: 'middle'
});

/* Calculating to-approve leave sum */
$('.quotaSelect').change(function(){

    if ($(this).val() == 'markedForApproval') {

        var overQuotaSelectId = $(this).attr('id');
        //var leaveRequestHiddenId = $(this).siblings('.requestIdHolder').attr('id');

        /* Tthis problem came on relying on dom structure, so better avoid in future */
        var ids = overQuotaSelectId.split("_");
        var leaveRequestHiddenId = "leaveRequest" + "-" + ids[ids.length - 1];
        var key = $(this).siblings('.quotaHolder').attr('name');
        var sum = 0;

        $('.quotaSelect').each(function(){
            if ($(this).val() == 'markedForApproval' &&
                $(this).siblings('.quotaHolder').attr('name') == key) {
                sum += parseFloat($(this).siblings('.quotaHolder').val());
            }
        });

        if (sum > quota[key]) {
            $("#overQuotaSelectId").val(overQuotaSelectId);
            $("#leaveRequestHiddenId").val(leaveRequestHiddenId);
            $("#overQuotaDialog").dialog('open');
        }
    }

});

/* overQuota dialog actions */
$("#overQuotaYes").click(function(){
    $("#overQuotaDialog").dialog('close');
});

$("#overQuotaNo").click(function(){

    $("#"+$("#overQuotaSelectId").val()).val('');
    $("#"+$("#leaveRequestHiddenId").val()).val('');

    $("#overQuotaDialog").dialog('close');

});

$('.data-table tbody tr.r1').hover(function() {
    $(this).removeClass();
    $(this).attr('class', 'r1 highlightRow');
});

$('.data-table tbody tr.r1').mouseout(function() {
    var i=0;
    $('.data-table tbody tr.r1').each(function(index, item) {
        $(item).attr('class', "r1 even");
        if(i % 2 == 0) {
            $(item).attr('class', "r1 odd");
        }
        i = i + 1;
    });
            
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

