<?php

$statusFilters = $form->getStatusFilters();

$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
$leaveData = $form->getList();
$searchActionButtons = $form->getSearchActionButtons();
?>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveListSuccess'); ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<?php if($messageType == "messageBalloon_notice") {?>
<div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php }?>
<div class="outerbox">
	<div class="mainHeading"><h2><?php echo $form->getTitle(); ?></h2></div>
	<?php if (!$form->isDetailed()) { ?>
	<div class="formWrapper">
		<form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">

			<label class="mainLabel"><?php echo __("Period");?></label>
			<?php echo $form['calFromDate']->renderLabel(__("From"), array('class' => 'subLabelNew')); ?>
			<?php echo $form['calFromDate']->render(); ?>

			<?php echo $form['calToDate']->renderLabel(__("To"), array('class' => 'subLabelNew')); ?>
			<?php echo $form['calToDate']->render(); ?>
			<br class="clear" />

			<label class="mainLabel"><?php echo __("Show Leave with Status");?></label>
			<?php foreach ($statusFilters as $filter) { ?>
				<?php echo $filter->render($filter->getName()) ?>
			<?php } ?>
			<?php if (isset($form['txtEmployee'])) { ?>
                <br class="clear" />
				<?php echo $form['txtEmployee']->renderLabel(__("Employee"), array('class' => 'mainLabel')); ?>
				<?php echo $form['txtEmployee']->render(); ?>
                <?php echo $form['txtEmpID']->render();?>
			<?php } ?>
			<?php if (isset($form['cmbSubunit'])) { ?>
				<br class="clear" />
				<?php echo $form['cmbSubunit']->renderLabel(__("Sub Unit"), array('class' => 'mainLabel')); ?>
				<?php echo $form['cmbSubunit']->render(); ?>
			<?php } ?>
            <br class="clear" />
                        <div class="buttonWrapper">
			<?php foreach ($searchActionButtons as $id => $button) {
				echo $button->render($id), "\n";
				}
			?>
                        </div>
		</form>
	</div>
	<?php } ?>

</div> <!-- End of outerbox -->
<?php if($messageType == "messageBalloon_success") {?>
<div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php }?>

<!--this is ajax message place -->
<div id="msgPlace"></div>
<!-- end of ajax message place -->

<?php if(count($leaveData) > 0) {?>
<div class="outerbox">

    <?php if ($form->isPaginated()) { ?>
    <div class="navigationHearder">
        <div class="pagingbar">
            <?php echo $form->getPageLinks($baseUrl, $page, $recordCount); ?>
        </div>
        <br class="clear" />
    </div>
    <?php } ?>

	<form id="frmSaveLeave" name="frmSaveLeave" method="post" action="<?php echo url_for('coreLeave/changeLeaveStatus/'); ?>">
		<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
				<?php if (!$form->isDetailed()) { ?>
				<tr>
					<td><?php echo __("Date"); ?></td>
					<td><?php echo __("Employee Name"); ?></td>
                                        <td><?php echo __("Leave Type"); ?></td>
					<td><?php echo __("Number of Days"); ?></td>
                    <td><?php echo __("Status"); ?></td>
                    <td><?php echo __("Comments"); ?></td>
					<td><?php echo __("Actions"); ?></td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><?php echo __("Date"); ?></td>
					<td><?php echo __("Leave Type"); ?></td>
					<td><?php echo __("Duration") . '(' . __("hours") . ')'; ?></td>
                    <td><?php echo __("Status"); ?></td>
                    <td>&nbsp;&nbsp;<?php echo __("Comments"); ?></td>
					<td><?php echo __("Actions"); ?></td>
				</tr>
				<?php } ?>
			</thead>
			<tbody>

                <?php if (count($leaveData) > 0): ?>

				<?php

                                $class = 'odd';

				foreach ($leaveData as $key => $datum) {

					if (!$form->isDetailed()) {
						$url = url_for($baseUrl) . '/id/' . $datum->getLeaveRequestId();

				?>
				<tr class="r1 <?php echo $class; ?>">
                                <?php $class = $class=='odd'?'even':'odd'; ?>
					<td><a href="<?php echo $url; ?>"><?php echo $form->getLeaveDateRange($datum->getLeaveRequestId()); ?></a></td>
					<td><a href="../pim/viewEmployee/empNumber/<?php echo $datum->getEmployee()->getEmpNumber(); ?>"><?php echo $datum->getEmployee()->getFullName(); ?></a></td>
					<td>
						<?php echo $datum->getLeaveType()->getLeaveTypeName(); ?>
						<?php echo ((bool) $datum->getLeaveType()->getAvailableFlag()) ? '' : '(' . __('deleted') . ')'; ?>
					</td>
                    <input type="hidden" name="leaveRequest[<?php echo $datum->getLeaveRequestId(); ?>]" id="leaveRequest-<?php echo $datum->getLeaveRequestId(); ?>" value="" class="requestIdHolder" />
                    <td><div class="numberLabel"><?php echo $datum->getNumberOfDays(); ?></div></td>
                                        <td><a href="<?php echo $url; ?>"><?php echo $datum->getStatus(); ?></a></td>
                                        <td align="left">
                                            <table cellspacing="0" cellpadding="0" border="0">
                                                <tr>
                                                    <td id="commentLabel_<?php echo $datum->getLeaveRequestId(); ?>" align="left" width="200"><?php if(strlen(trim($datum->getLeaveComments())) > 25) {echo substr($datum->getLeaveComments(), 0, 25) . "..."; } else { echo $datum->getLeaveComments(); }?></td>
                                                    <td class="dialogInvoker" id="pen_request_<?php echo $datum->getLeaveRequestId(); ?>"><img src="<?php echo public_path('../../themes/orange/icons/callout.png')?>" title="Click here to edit" /></td>
                                                </tr>
                                            </table>
                                            <input type="hidden" name="leaveComments[<?php echo $datum->getLeaveRequestId(); ?>]" id="leaveComments-<?php echo $datum->getLeaveRequestId(); ?>" value="<?php echo $datum->getLeaveComments(); ?>" />
                                        </td>
					<td class="actions">
                                            <?php if (count($datum->getStatusCounter()) > 1): ?>
                                            <a href="<?php echo $url; ?>"><?php echo __('Go to Detailed View'); ?></a>
                                            <?php else: ?>
                                            <?php

                                                $actions = $form->renderItemActions($datum);
                                                if (count($actions['select_options']) > 1) {
                                                    $selectId = 'select_leave_action_' . $datum->getLeaveRequestId();
                                                    $selectClass = "select_action{$form->getQuotaClass($datum->getLeaveType()->getLeaveTypeId())}";

                                                    $selectOptions = "";

                                                    foreach ($actions['select_options'] as $optionId => $optionValue) {

                                                        $selected = $optionValue == "" ? 'selected="selected"' : '';

                                                        $selectOptions .= '<option '. $selected . ' value="' . $optionId . '" >' . $optionValue . '</option>';
                                                    }
                                            ?>
                                            <select class="<?php echo $selectClass;?>" id="<?php echo $selectId;?>" name="<?php echo $selectId;?>">
                                                <?php echo $selectOptions;?>
                                            </select>
                                            <?php
                                                }
                                            ?>
                                            <?php
                                                $quotaKey = $datum->getEmpNumber().'-';
                                                $quotaKey .= $datum->getLeaveTypeId().'-';
                                                $quotaKey .= $datum->getLeavePeriodId();
                                            ?>

                                            <input type="hidden" name="<?php echo $quotaKey; ?>" class="quotaHolder" value="<?php echo $datum->getNumberOfDays(); ?>" />

                                            <?php endif; ?>
                                        </td>
				</tr>
				<?php } else {  ?>
				<tr class="<?php echo $class; ?>">
                                <?php $class = $class=='odd'?'even':'odd'; ?>
					<td><?php echo $datum->getLeaveDate(); ?></td>
                    <?php if ($datum->getTextLeaveStatus() != ''): ?>
					<td>
						<?php echo $datum->getLeaveRequest()->getLeaveType()->getLeaveTypeName(); ?>
						<?php echo ((bool) $datum->getLeaveRequest()->getLeaveType()->getAvailableFlag()) ? '' : '(' . __('deleted') . ')'; ?>
					</td>
                    <td><div class="numberLabel"><?php echo $datum->getLeaveLengthHours(); ?></div></td>
                    <td><?php echo $datum->getTextLeaveStatus(); ?></td>
                    <td valign="top"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td id="commentLabel_<?php echo $datum->getLeaveId(); ?>" align="left"><?php if(strlen(trim($datum->getLeaveComments())) > 25) {echo substr($datum->getLeaveComments(), 0, 25) . "..."; } else { echo $datum->getLeaveComments(); }?></td>
                                <td class="dialogInvoker" id="pen_leave_<?php echo $datum->getLeaveId(); ?>"><img src="<?php echo public_path('../../themes/orange/icons/callout.png')?>" title="Click here to edit" /></td>
                            </tr>
                        </table>
                        <input type="hidden" name="leaveComments[<?php echo $datum->getLeaveId(); ?>]" value="<?php echo $datum->getLeaveComments(); ?>" id="leaveComments-<?php echo $datum->getLeaveId(); ?>"/></td>
					<td class="actions">
                                             <?php

                                                $actions = $form->renderItemActions($datum);
                                                if (count($actions['select_options']) > 1 && $datum->getTextLeaveStatus() != '') {


                                                    $selectId = 'select_leave_action_' . $datum->getLeaveId();
                                                    $selectClass = "select_action{$form->getQuotaClass($datum->getLeaveTypeId())}";

                                                    $selectOptions = "";

                                                    foreach ($actions['select_options'] as $optionId => $optionValue) {

                                                        $selected = $optionValue == "" ? 'selected="selected"' : '';

                                                        $selectOptions .= '<option '. $selected . ' value="' . $optionId . '" >' . $optionValue . '</option>';
                                                    }
                                            ?>
                                            <select class="<?php echo $selectClass;?>" id="<?php echo $selectId;?>" name="<?php echo $selectId;?>">
                                                <?php echo $selectOptions;?>
                                            </select>
                                            <?php
                                                }
                                            ?>
                                            <?php
                                                $quotaKey = $datum->getEmployeeId().'-';
                                                $quotaKey .= $datum->getLeaveTypeId().'-';
                                                $quotaKey .= $datum->getLeaveRequest()->getLeavePeriodId();
                                            ?>
                                            <input type="hidden" name="<?php echo $quotaKey; ?>" class="quotaHolder" value="1" />
                        <input type="hidden" name="leave[<?php echo $datum->getLeaveId(); ?>]" id="leave-<?php echo $datum->getLeaveId(); ?>" value="" class="requestIdHolder" />
					</td>
                    <?php else: ?>
                    <td></td>
                    <td></td>
                    <td><?php echo __('Non Working Day'); ?></td>
                    <td></td>
                    <td style="height: 32px;">&nbsp;</td>
                    <?php endif; ?>
				</tr>
				<?php
					}
				} // End of foreach
				?>

                <?php endif; ?>
			</tbody>
		</table>

                <div class="formbuttons">
                    <?php
                        $buttons = $form->getActionButtons();
                        foreach ($buttons as $id => $button) {
                                echo $button->render($id), "\n";
                        }
                    ?>

                </div>


		<input type="hidden" name="hdnMode" value="<?php echo $mode; ?>" />

	</form>
</div>
<?php }?>

<!-- comment dialog -->

<div id="commentDialog" title="Leave Comment">
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="10" class="commentTextArea"></textarea>
        <br class="clear" />
        <span class="error" id="commentError"></span>
        <div><input type="button" id="commentSave" class="plainbtn" value="Edit" />
            <input type="button" id="commentCancel" class="plainbtn" value="Cancel" /></div>
    </form>
</div>

<!-- end of comment dialog-->

<div id="overQuotaDialog" title="OrangeHRM - Confirmation Required" style="display: none;">
Approving this leave will exceed this employee's<br />
leave balance for this leave type. Do you want to continue?
<br /><br />
<div class="dialogButtons">
<input type="button" id="overQuotaYes" class="savebutton" value="Yes" />
<input type="button" id="overQuotaNo" class="savebutton" value="No" />
</div>
</div>

<input type="hidden" id="overQuotaSelectId" value="" />
<input type="hidden" id="leaveRequestHiddenId" value="" />

<script type="text/javascript">
//<![CDATA[

        <?php if($form->isDetailed()): ?>
        var mode = 'detailed';
        <?php else: ?>
        var mode = 'compact';
        <?php endif; ?>

        var quota = new Array();

        <?php

            if (isset($quotaArray)) {

                foreach ($quotaArray as $key => $val) {

                    echo "quota[\"".$key."\"] = $val;\n ";

                }

            }

        ?>


	$(document).ready(function(){

        var data	= <?php echo str_replace('&#039;',"'",$form->getEmployeeListAsJson());?>
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
            $("#" + filler).val("");
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    return true;
                }
            });

            if($("#" + filler).val() == "") {
                $("#" + selector).val("");
            }
        }

         //dissabling dialog by default
            $("#commentDialog").dialog({
                autoOpen: false,
                width: 350,
                height: 300
            });
        
            daymarker.bindElement('.ohrm_datepicker', {
                onSelect: function(date){},
                dateFormat : 'yy-mm-dd'
            });

            $('.ohrm_datepicker').each(function() {
                $(this).next().click(function(){
	            daymarker.show('#' + $(this).prev().attr('id'));
	        });
            });

            $('input#checkAll').click(function() {
                $(this).siblings('input:checkbox').attr('checked', $(this).attr('checked'));
            });

            $('input:checkbox').each(function() {
                if ($(this).attr('id') != 'checkAll') {
                    $(this).click(function() {
                        var allChecked = true;
                        $(this).siblings('input:checkbox').each(function() {
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
                
                $("#commentSave").attr("value", "Edit");

                //extracting the request id
                var ids = ($(this).attr("id")).split("_");
                $("#leaveOrRequest").val(ids[ids.length - 2]);
                var comment = $("#leaveComments-" + ids[ids.length - 1]).val();
                $("#leaveId").val(ids[ids.length - 1]);
                $("#leaveComment").val(comment);

                $("#commentDialog").dialog('open');
            });

            //closes the dialog
            $("#commentCancel").click(function() {
                $("#commentDialog").dialog('close');
            });

            //on clicking on save button
            $("#commentSave").click(function() {
                if($("#commentSave").attr("value") == "Edit") {
                    $("#leaveComment").removeAttr("disabled");
                    $("#commentSave").attr("value", "Save");
                    return;
                }

                if($("#commentSave").attr("value") == "Save") {
                    $("#commentError").html("");
                    var comment = $("#leaveComment").val().trim();
                    if(comment.length > 250) {
                        $("#commentError").html("Comment length should be less than 250 characters");
                        return;
                    }

                    //setting the comment in the label
                    var commentLabel = comment.substr(0, 25);
                    if(comment.length > 25) {
                        commentLabel += "...";
                    }

                    //if there is no-change between original and updated comments then don't show success message
                    if($("#leaveComments-" + $("#leaveId").val()).val().trim() == comment) {
                        $("#commentDialog").dialog('close');
                        return;
                    }

                    //we set updated comment for the hidden comment field
                    $("#leaveComments-" + $("#leaveId").val()).val(comment);

                    //posting the comment
                    var url = "updateComment";
                    <?php if (isset($mode) && $mode == LeaveListForm::MODE_MY_LEAVE_LIST) {?>
                        url = "../updateComment";
                    <?php } ?>
                    var data = "leaveRequestId=" + $("#leaveId").val() + "&leaveComment=" + comment;

                    //this is specially for detailed view
                    if($("#leaveOrRequest").val() == "leave") {
                        url = "../../updateComment"
                        data = "leaveId=" + $("#leaveId").val() + "&leaveComment=" + comment;
                    }

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function(flag) {
                            $("#msgPlace").removeAttr("class");
                            $(".messageBalloon_success").remove();
                            $("#msgPlace").html("");
                            if(flag == 1) {
                                $("#commentLabel_" + $("#leaveId").val()).html(commentLabel);
                                $("#msgPlace").attr("class", "messageBalloon_success");
                                $("#msgPlace").html("Comment Successfully Saved");
                            }
                        }
                    });

                    $("#commentDialog").dialog('close');
                    return;
                }
            });

            $('#btnSearch').click(function() {
                $('#frmFilterLeave').submit();
            });


            $('#btnReset').click(function() {
                $('#frmFilterLeave')[0].reset();
            });

            $('#btnBack').click(function() {
                window.location = "coreLeave/viewLeaveList";
            });

            $('#btnSave').click(function() {
                $('td.actions input:hidden').each(function() {
                    if ($(this).val() == '') {
                        $(this).attr('disabled', true);
                    }
                });

                //suppose if it is the detailed screen
                <?php if(isset($leaveRequestId) && trim($leaveRequestId) != "") {?>
                    var url = $('#frmSaveLeave').attr('action') + "/id/" + <?php echo $leaveRequestId;?>;
                    $('#frmSaveLeave').attr('action', url);
                <?php } ?>
                $('#frmSaveLeave').submit();
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
                width: 325,
                height: 90,
                position: 'middle'
            });

            /* Calculating to-approve leave sum */

            $('.quotaSelect').change(function(){

                if ($(this).val() == 'markedForApproval') {

                    var overQuotaSelectId = $(this).attr('id');
                    //var leaveRequestHiddenId = $(this).siblings('.requestIdHolder').attr('id');

                    //this problem came on relying on dom structure, so better avoid in future
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
//]]>
</script>

