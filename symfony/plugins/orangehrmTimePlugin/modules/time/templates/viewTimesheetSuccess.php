<?php /**
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
 */ ?>

<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/viewTimesheet')); ?>

<?php
$noOfColumns = sizeof($sf_data->getRaw('rowDates'));
$width = 350 + $noOfColumns * 75;

$actionName = sfContext::getInstance()->getActionName();
?>

<style type="text/css">
    #timeComment {
        width: 365px;
        margin-bottom: 5px;
    }
</style>
<?php if ($timesheetPermissions->canRead()) { ?>
    <?php if (isset($messageData[0])): ?>
        <div class="box timesheet">
            <div class="inner">
                <div class="message <?php echo $messageData[0]; ?>">
                    <?php echo $messageData[1]; ?>
                    <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <div class="box timesheet noHeader" id="timesheet">

            <div class="inner">

                <?php echo (isset($successMessage[0])) ? displayMainMessage($successMessage[0], $successMessage[1]) : '' ?>

                <div class="top">
                    <h3>
                        <?php
                        echo (isset($employeeName)) ? __('Timesheet for') . " " . $employeeName . " " . __('for') . " " . __($headingText) . " " : __('Timesheet for') . " " . __($headingText) . " ";
                        ?>
                    </h3>
                    <form>
                        <ol class="normal" style="padding-bottom:0">
                            <li style="margin-bottom:5px">
                                <?php echo $dateForm['startDates']->render(array('onchange' => 'clicked(event)')); ?>
                                <?php if ($allowedToCreateTimesheets) : ?>
                                    <a id="btnAddTimesheet" data-toggle="modal" href="#createTimesheet" class="fieldHelpRight"><?php echo __("Add Timesheet"); ?></a>
                                <?php endif; ?>
                            </li>
                        </ol>
                    </form>

                </div>

                <div id="validationMsg"></div>
                <div class="tableWrapper" style="overflow:auto">
                    <table style="width:100%" class="table">
                        <thead>
                            <tr>
                                <th id="projectColumn" style="width:20%"><?php echo __("Project Name") ?></th>
                                <th id ="activityColumn" style="width:15%"><?php echo __("Activity Name") ?></th>
                                <?php foreach ($rowDates as $data): ?>
                                    <th style="width:5%" class="center">
                                        <?php echo __(date('D', strtotime($data))); ?> 
                                        <?php echo date('j', strtotime($data)); ?>
                                    </th>
            <!--                        <th class="commentIcon"></th>-->
                                <?php endforeach; ?>
                                <th style="width:6%" class="center"><?php echo __("Total") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($toggleDate)):
                                $selectedTimesheetStartDate = $toggleDate;
                            else :
                                $selectedTimesheetStartDate = $timesheet->getStartDate();
                            endif;

                            if ($timesheetRows == null) :
                                ?>
                                <!-- colspan should be based on  the fields in a timesheet-->
                                <tr>
                                    <td id="noRecordsColumn" colspan="100"><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                </tr>
                            <?php
                            else:
                                // timesheet available 
                                $class = 'odd';
                                foreach ($timesheetRows as $timesheetItemRow):
                                    if ($format == '1')
                                        $total = '0:00';
                                    if ($format == '2')
                                        $total = 0;
                                    ?>
                                    <tr class="<?php echo $class; ?>">
                                        <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>
                                        <td id="columnName">
                                            <?php echo str_replace("##", "", html_entity_decode($timesheetItemRow['projectName'])); ?>
                                        </td>
                                        <td id="columnName">
                                            <?php echo html_entity_decode($timesheetItemRow['activityName']); ?>
                                        </td>
                                        <?php
                                        foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects):

                                            if ($format == '1') {
                                                ?>
                                                <td class="right comments">
                                                    <?php echo ($timesheetItemObjects->getDuration() == null ) ? "0:00" :
                                                            $timesheetItemObjects->getConvertTime();
                                                    ?><span class="commentIcon" data-toggle="modal" href="#commentDialog">
                                                    <?php
                                                    if ($timesheetItemObjects->getComment() != null)
                                                        echo image_tag(theme_path('images/comment.png'), array('id' => 'callout_' .
                                                            $timesheetItemObjects->getTimesheetItemId(), 'class' => 'icon'));
                                                    // comment -- callout
                                                    ?>
                                                    </span>                                
                                                </td>
                                            <?php } ?>

                                                <?php if ($format == '2') { ?>
                                                <td class="right"><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0.00" :
                                    $timesheetItemObjects->getConvertTime();
                                                    ?><span class="commentIcon" data-toggle="modal" href="#commentDialog">
                                                    <?php
                                                    if ($timesheetItemObjects->getComment() != null)
                                                        echo image_tag(theme_path('images/comment.png'), array('id' => 'callout_' .
                                                            $timesheetItemObjects->getTimesheetItemId(), 'class' => 'icon'));
                                                    ?>
                                                    </span>                                    
                                                </td>
                                            <?php } ?>

                                            <?php
                                            if ($format == '1')
                                                $total+=$timesheetItemObjects->getDuration();
                                            if ($format == '2')
                                                $total+=$timesheetItemObjects->getConvertTime();
                                        endforeach;
                                        ?>

                                        <?php if ($format == '1') { ?>
                                            <td class="right total">
                                                <strong><?php echo $timeService->convertDurationToHours($total) ?></strong>
                                            </td>
                                        <?php } ?>
                                        <?php if ($format == '2') { ?>
                                            <td class="right total">
                                                <strong><?php echo number_format($total, 2, '.', ''); ?></strong>
                                            </td>
                                    <?php } ?>
                                    </tr>
                <?php
            endforeach;
            ?>

                                <tr class="total">
                                    <td id="totalVertical"><?php echo __('Total'); ?></td>
                                    <td></td>
                                    <?php
                                    if ($format == '1') {
                                        $weeksTotal = '0:00';
                                    }
                                    if ($format == '2') {
                                        $weeksTotal = 0.00;
                                    }
                                    foreach ($rowDates as $data):
                                        if ($format == '1') {
                                            $verticalTotal = '0:00';
                                        }
                                        if ($format == '2') {
                                            $verticalTotal = 0.00;
                                        }
                                        foreach ($timesheetRows as $timesheetItemRow):
                                            foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects):
                                                if ($data == $timesheetItemObjects->getDate()):
                                                    if ($format == '1')
                                                        $verticalTotal+=$timesheetItemObjects->getDuration();
                                                    if ($format == '2')
                                                        $verticalTotal+=$timesheetItemObjects->getConvertTime();
                                                    continue;
                                                endif;
                                            endforeach;
                                        endforeach;
                                        ?>
                                        <?php if ($format == '1') { ?>
                                            <td class="right"><?php echo $timeService->convertDurationToHours($verticalTotal); ?></td>
                                        <?php } ?>
                                        <?php if ($format == '2') { ?>
                                            <td class="right"><?php echo number_format($verticalTotal, 2, '.', ''); ?></td>
                                        <?php } ?>
                <?php
                $weeksTotal+=$verticalTotal;
            endforeach;
            ?>
                                    <?php if ($format == '1') { ?>
                                        <td class="right total">
                                            <strong><?php echo $timeService->convertDurationToHours($weeksTotal); ?></strong>
                                        </td>
                                    <?php } ?>
                                <?php if ($format == '2') { ?>
                                        <td class="right total">
                                            <strong><?php echo number_format($weeksTotal, 2, '.', ''); ?></strong>
                                        </td>
            <?php } ?>
                                </tr>
        <?php endif; ?>
                        </tbody>
                    </table>
                </div> <!-- tableWrapper -->
                <div class="bottom">
                    <em><h2><?php echo __('Status') . ': ' ?><?php echo __(ucwords(strtolower($timesheet->getState()))); ?></h2></em>
                    <form id="timesheetFrm" name="timesheetFrm"  method="post">
                            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                        <p>
                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_MODIFY])) : ?>
                                    <input type="button" class="edit" name="button" id="btnEdit" value="<?php echo __('Edit'); ?>" />
                                <?php endif; ?>

                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT])) : ?>
                                    <input type="button" class="" name="button" id="btnSubmit" value="<?php echo __('Submit'); ?>" />
                                <?php endif; ?>

                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_RESET])) : ?>
                                    <input type="button" class="reset"  name="button" id="btnReset" value="<?php echo __('Reset') ?>" />
                                <?php endif; ?>
                        </p>         
                    </form>
                </div>
            </div> <!-- inner -->

        </div> <!-- Box -->

        <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_APPROVE]) ||
                isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_REJECT])) :
            ?>
            <div class="box">
                <div class="head">
                    <h1 id=""><?php echo __("Timesheet Action"); ?></h1>
                </div>
                <div class="inner">
                    <form id="timesheetActionFrm" name="timesheetActionFrm"  method="post">
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                        <fieldset>
                            <ol>
                                <li class="largeTextBox">
                                    <label><?php echo __("Comment") ?></label>
                                    <textarea name="Comment" id="txtComment"></textarea>
                                </li>
                            </ol>
                            <p>
            <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_APPROVE])): ?>
                                    <input type="button" class="" name="button" id="btnApprove" value="<?php echo __('Approve') ?>" />
            <?php endif; ?>
            <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_REJECT])) : ?>
                                    <input type="button" class="delete"  name="button" id="btnReject" value="<?php echo __('Reject') ?>" />
            <?php endif; ?>
                            </p>
                        </fieldset>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($actionLogRecords != null): ?>
            <div class="box miniList">

                <div class="head">
                    <h1 id="actionLogHeading"><?php echo __("Actions Performed on the Timesheet"); ?></h1>
                </div>

                <div class="inner">
                    <table border="0" cellpadding="5" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <th id="actionlogStatus" width="15%"><?php echo __('Action'); ?></th>
                                <th id="actionlogPerform" width="25%"><?php echo __('Performed By'); ?></th>
                                <th id="actionLogDate" width="15%"><?php echo __('Date'); ?></th>
                                <th id="actionLogComment" width="45%"><?php echo __('Comment'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($actionLogRecords as $row):
                                $performedBy = $row->getUsers()->getEmployee()->getFullName();
                                if (empty($performedBy) && $row->getUsers()->getIsAdmin() == 'Yes') {
                                    $performedBy = __("Admin");
                                }
                                ?>
                                <tr class="<?php echo ($i & 1) ? 'even' : 'odd'; ?>">
                                    <td id="actionlogStatus"><?php echo __(ucfirst(strtolower($row->getAction()))); ?></td>
                                    <td id="actionlogPerform"><?php echo $performedBy; ?></td>
                                    <td id="actionLogDate"><?php echo set_datepicker_date_format($row->getDateTime()); ?></td>
                                    <td id="actionLogComment"><?php echo $row->getComment(); ?></td>
                                </tr>
                <?php
                $i++;
            endforeach;
            ?>
                        </tbody>
                    </table>
                </div> <!-- inner -->

            </div> <!-- Box-miniList -->
        <?php endif; ?>

        <!-- Comment-Dialog -->
        <div class="modal hide" id="commentDialog">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3><?php echo __('Comment'); ?></h3>
            </div>
            <div class="modal-body">

                <form action="updateComment" method="post" id="frmCommentSave">
                    <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                    <fieldset>
                        <ol>
                            <li class="line">
                                <label><?php echo __("Project Name ") ?></label>
                                <label id="commentProjectName" class="line"></label>
                            </li>
                            <li class="line">
                                <label><?php echo __("Activity Name ") ?></label>
                                <label id="commentActivityName" class="line"></label>
                            </li>
                            <li class="line">
                                <label><?php echo __("Date ") ?></label>
                                <label id="commentDate" class="line"></label>
                            </li>                    
                            <li>
                                <textarea name="leaveComment" id="timeComment"></textarea>
                            </li>
                        </ol>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" id="commentCancel" class="reset" data-dismiss="modal" value="<?php echo __('Close'); ?>"/>
            </div>
        </div> <!-- commentDialog -->

        <!-- createTimesheet-Dialog -->
        <div class="modal hide" id="createTimesheet">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3><?php echo __('Add Timesheet'); ?></h3>
            </div>
            <div class="modal-body">
                <form  id="createTimesheetForm" action=""  method="post">
        <?php echo $createTimesheetForm['_csrf_token']; ?>
                    <fieldset>
                        <ol>
                            <li class ="line">
        <?php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
        <?php echo $createTimesheetForm['date']->render(); ?>
        <?php echo $createTimesheetForm['date']->renderError() ?>
                            </li>
                        </ol>
                    </fieldset>
                </form> 
            </div>
            <div class="modal-footer">
                <input type="button" id="addTimesheetBtn" class="" data-dismiss="modal" value="<?php echo __('Ok'); ?>"/>
                <input type="button" id="addCancel" class="reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
            </div>
        </div> <!-- createTimesheet -->

    <?php endif; ?>
<?php } ?>
<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var submitAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT; ?>";
    var approveAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_APPROVE; ?>";
    var rejectAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_REJECT; ?>";
    var resetAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_RESET; ?>";
    var employeeId = "<?php echo $timesheet->getEmployeeId(); ?>";
    var timesheetId = "<?php echo $timesheet->getTimesheetId(); ?>";
    var linkForViewTimesheet="<?php echo url_for('time/' . $actionName) ?>";
    var linkForEditTimesheet="<?php echo url_for('time/editTimesheet') ?>";
    var linkToViewComment="<?php echo url_for('time/showTimesheetItemComment') ?>";
    var date = "<?php echo $selectedTimesheetStartDate ?>";
    var actionName = "<?php echo $actionName; ?>";
    var erorrMessageForInvalidComment="<?php echo __("Comment should be less than 250 characters"); ?>";
    var validateStartDate="<?php echo url_for('time/validateStartDate'); ?>";
    var createTimesheet="<?php echo url_for('time/createTimesheet'); ?>";
    var returnEndDate="<?php echo url_for('time/returnEndDate'); ?>";
    var currentDate= "<?php echo $currentDate; ?>";
    var lang_noFutureTimesheets= "<?php echo __("Failed to Create: Future Timesheets Not Allowed"); ?>";
    var lang_overlappingTimesheets= "<?php echo __("Timesheet Overlaps with Existing Timesheets"); ?>";
    var lang_timesheetExists= "<?php echo __("Timesheet Already Exists"); ?>";
    var lang_invalidDate= "<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat()))));
?>";
                var dateList  = <?php echo json_encode($dateForm->getDateOptions()); ?>;
                var closeText = '<?php echo __('Close'); ?>';
    
</script>
