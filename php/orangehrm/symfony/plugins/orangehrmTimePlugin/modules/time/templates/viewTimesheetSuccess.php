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
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>


<?php
$noOfColumns = sizeof($sf_data->getRaw('rowDates'));
$width = 350 + $noOfColumns * 75;
?>
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/viewTimesheetSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/viewTimesheet'); ?>
<?php
use_stylesheet('../../../themes/orange/css/style.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
?>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>

<?php $actionName = sfContext::getInstance()->getActionName(); ?>
<?php if (isset($successMessage)) { ?>
    <?php echo templateMessage($successMessage); ?>
<?php } ?>
<?php if (isset($messageData)): ?>
    <?php echo templateMessage($messageData); ?>
<?php else: ?>

    <table id="headingTable">

        <!--    conifigure the heading accoding to the timesheet period using the num of columns-->
        <tr>

            <?php if (isset($employeeName)): ?>
                <td id="headingText"><?php echo __('Timesheet for')." " . $employeeName . " ".__('for') . " " . __($headingText) . " ";
        echo $dateForm['startDates']->render(array('onchange' => 'clicked(event)')); ?>
                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
                        <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Add Timesheet') ?>" />
                           <?php endif; ?>
                </td>

            <?php else: ?>
                <td id="headingText"><?php echo __('Timesheet for') . " " . __($headingText) . " ";
        echo $dateForm['startDates']->render(array('onchange' => 'clicked(event)')); ?>
                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
                        <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Add Timesheet') ?>" />
                           <?php endif; ?>
                </td>


            <?php endif; ?>
        </tr>
    </table>


    <div id="createTimesheet">
        <br class="clear"/>
        <form  id="createTimesheetForm" action=""  method="post">
            <?php echo $createTimesheetForm['_csrf_token']; ?>

            <?php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
            <?php echo $createTimesheetForm['date']->render(); ?><input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;float:none;margin-bottom:-4px;margin-left:4px;"/>
            <?php echo $createTimesheetForm['date']->renderError() ?>
            <br class="clear"/>
        </form>

    </div>

    <div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>
    <div class="outerbox" style="width: <?php echo $width . 'px' ?>;">
        <div class="maincontent">
            <table  border="0" cellpadding="5" cellspacing="0" class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <td id="projectColumn" ><?php echo __("Project Name") ?></td>
                        <td id ="activityColumn" ><?php echo __("Activity Name") ?></td>

                        <?php foreach ($rowDates as $data): ?>
                            <td><?php echo __(date('D', strtotime($data))); ?> <br/><?php echo date('j', strtotime($data)); ?></td><td class="commentIcon"></td>
                        <?php endforeach; ?>

                        <td><?php echo __("Total") ?></td>
                    </tr>
                </thead>
                <tr><td id="noRecordsColumn" colspan="100"></td></tr>
                <?php if (isset($toggleDate)): ?>
                    <?php $selectedTimesheetStartDate = $toggleDate ?>
                <?php else: ?>
                    <?php $selectedTimesheetStartDate = $timesheet->getStartDate() ?>
                <?php endif; ?>
                <?php if ($timesheetRows == null) : ?>
                    <!-- colspan should be based on  the fields in a timesheet-->
                    <tr>
                        <td id="noRecordsColumn" colspan="100"><br><?php echo __("No Records Found") ?></td>
                    </tr>

                <?php else: ?>
                    <?php $class = 'odd'; ?>
                    <?php foreach ($timesheetRows as $timesheetItemRow): ?>
                        <?php if ($format == '1') { ?>
                            <?php $total = '0:00'; ?>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <?php $total = 0; ?>
                        <?php } ?>


                        <tr class="<?php echo $class; ?>">
                            <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>
				<td id="columnName"><?php echo str_replace("##", "", html_entity_decode($timesheetItemRow['projectName'])); ?>
                            <td id="columnName"><?php echo html_entity_decode($timesheetItemRow['activityName']); ?>

                                <?php foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects): ?>
                                    <?php if ($format == '1') { ?>
                                    <td class="duration"><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0:00" : $timesheetItemObjects->getConvertTime(); ?></td>
                                    <td class="commentIcon">
                                    <?php 
                                    if ($timesheetItemObjects->getComment() != null) {
                                       echo image_tag('callout.png', 
                                                 array('id' => 'callout_'. $timesheetItemObjects->getTimesheetItemId(),
                                                       'class' => 'icon')); 
                                    } ?>
                                    </td>
                                <?php } ?>
                                <?php if ($format == '2') { ?>
                                    <td class="duration"><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0.00" : $timesheetItemObjects->getConvertTime(); ?></td>
                                    <td class="commentIcon">
                                    <?php 
                                      if ($timesheetItemObjects->getComment() != null) {
                                          echo image_tag('callout.png', 
                                                 array('id' => 'callout_'. $timesheetItemObjects->getTimesheetItemId(),
                                                       'class' => 'icon')); 

                                      } ?>
                                    </td>
                                <?php } ?>

                                <?php if ($format == '1') { ?>
                                    <?php $total+=$timesheetItemObjects->getDuration(); ?>
                                <?php } ?>
                                <?php if ($format == '2') { ?>
                                    <?php $total+=$timesheetItemObjects->getConvertTime(); ?>
                                <?php } ?>
                            <?php endforeach; ?>

                            <?php if ($format == '1') { ?>
                                <td id= "total"><?php echo $timeService->convertDurationToHours($total) ?><td>
                                <?php } ?>
                                <?php if ($format == '2') { ?>
                                <td id="total"><?php echo number_format($total, 2, '.', ''); ?><td>
                                <?php } ?>


                        </tr>

                    <?php endforeach; ?>
                    <tr><td colspan="100"></tr>
                    <tr class="even">
                        <td id="totalVertical"><?php echo __('Total'); ?></td>
                        <td></td>
                        <?php if ($format == '1') { ?>
                            <?php $weeksTotal = '0:00' ?>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <?php $weeksTotal = 0.00 ?>
                        <?php } ?>   
                        <?php foreach ($rowDates as $data): ?>
                            <?php if ($format == '1') { ?>
                                <?php $verticalTotal = '0:00'; ?>
                            <?php } ?>
                            <?php if ($format == '2') { ?>
                                <?php $verticalTotal = 0.00; ?>
                            <?php } ?>

                            <?php foreach ($timesheetRows as $timesheetItemRow): ?>
                                <?php foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects): ?>
                                    <?php if ($data == $timesheetItemObjects->getDate()): ?>
                                        <?php if ($format == '1') { ?>
                                            <?php $verticalTotal+=$timesheetItemObjects->getDuration(); ?>
                                        <?php } ?>
                                        <?php if ($format == '2') { ?>
                                            <?php $verticalTotal+=$timesheetItemObjects->getConvertTime(); ?>
                                        <?php } ?>
                                        <?php continue; ?>
                                    <?php endif; ?>              
                                <?php endforeach; ?>
                            <?php endforeach; ?>

                            <?php if ($format == '1') { ?>
                                <td id ="totalVerticalValue"><?php echo $timeService->convertDurationToHours($verticalTotal); ?> </td>
                            <?php } ?>
                            <?php if ($format == '2') { ?>
                                <td id ="totalVerticalValue"><?php echo number_format($verticalTotal, 2, '.', ''); ?> </td>
                            <?php } ?>

                            <td></td>

                            <?php $weeksTotal+=$verticalTotal; ?>
                        <?php endforeach; ?>
                        <?php if ($format == '1') { ?>
                            <td id="total"><?php echo $timeService->convertDurationToHours($weeksTotal); ?></td>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <td id="total"><?php echo number_format($weeksTotal, 2, '.', ''); ?></td>
                        <?php } ?>
                        <td></td></tr>
                <?php endif; ?>

            </table>


            <form id="timesheetFrm"  method="post">

                <?php echo $formToImplementCsrfToken['_csrf_token']; ?>

                <div class="formbuttons">

                    <div><h4><?php echo __('Status').': ' ?><?php echo __(ucwords(strtolower($timesheet->getState()))); ?></h4></div>
                    <br class="clear">

                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_MODIFY, $sf_data->getRaw('allowedActions'))) : ?>
                        <input type="submit" class="editbutton" name="button" id="btnEdit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Edit'); ?>" />

                    <?php endif; ?>

                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT, $sf_data->getRaw('allowedActions'))) : ?>
                        <input type="button" class="submitbutton" name="button" id="btnSubmit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Submit'); ?>" />

                    <?php endif; ?>
                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_RESET, $sf_data->getRaw('allowedActions'))) : ?>

                        <input type="button" class="resetButton"  name="button" id="btnReset"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Reset') ?>" />
                        <br class="clear"/>

                    <?php endif; ?>
                    <br class="clear"/>
                    <br class="clear"/>
                    <div> 
                        <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_APPROVE, $sf_data->getRaw('allowedActions')) || (in_array(WorkflowStateMachine::TIMESHEET_ACTION_REJECT, $sf_data->getRaw('allowedActions')))) : ?>

                            <div class="commentHeading">
                                <b><?php echo __("Comment") ?></b>
                            </div>
                            <textarea name="Comment" id="txtComment" rows="3" cols="70" onkeyup="validateComment()"></textarea>

                        <?php endif; ?>
                        <div id="actionBtns" style="padding-top: 3px">
                            <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_APPROVE, $sf_data->getRaw('allowedActions'))): ?>
                                <input type="button" class="approvebutton" name="button" id="btnApprove"
                                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                       value="<?php echo __('Approve') ?>" />


                            <?php endif; ?>


                            <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_REJECT, $sf_data->getRaw('allowedActions'))) : ?>


                                <input type="button" class="rejectbutton"  name="button" id="btnReject"
                                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                       value="<?php echo __('Reject') ?>" />
                                <br class="clear"/>


                            <?php endif; ?>

                        </div>

                    </div>


                </div>          
            </form>
        </div>
    </div>

    <br class="clear">
    <br class="clear">

    <?php if ($actionLogRecords != null): ?>

        <h2 id="actionLogHeading">
            &nbsp;&nbsp;&nbsp;<?php echo __("Actions Performed on the Timesheet"); ?>
        </h2>
        <div class="outerbox" style="width: auto">
            <div class="maincontent" style="width: auto">
                <table border="0" cellpadding="5" cellspacing="0" class="actionLog-table">
                    <thead>
                        <tr>
        <!--                                    <td id="actionlogStatusAlignment"> </td>-->
                            <td id="actionlogStatus"><?php echo __('Action'); ?></td>
                            <td id="actionlogPerform"><?php echo __('Performed By'); ?></td>
                            <td id="actionLogDate"><?php echo __('Date'); ?></td>
                            <td id="actionLogComment"><?php echo __('Comment'); ?></td>
                        </tr>
                    </thead>

                    <?php foreach ($actionLogRecords as $row): ?>
                        <?php

                        $performedBy = $row->getUsers()->getEmployee()->getFullName();
                        
                        if (empty($performedBy) && $row->getUsers()->getIsAdmin() == 'Yes') {
                            $performedBy = __("Admin");
                        }
                        
                        ?>

                        <tr>
            <!--                    <td id="actionlogStatusAlignment"> </td>-->
                            <td id="actionlogStatus"><?php echo __(ucfirst(strtolower($row->getAction()))); ?></td>
                            <td id="actionlogPerform"><?php echo $performedBy; ?></td>
                            <td id="actionLogDate"><?php echo set_datepicker_date_format($row->getDateTime()); ?></td>
                            <td id="actionLogComment"><?php echo $row->getComment(); ?></td>
                        </tr>

                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    <?php endif; ?>
    <div id="commentDialog" title="<?php echo __('Comment'); ?>">
        <form action="updateComment" method="post" id="frmCommentSave">
            <div>
                <table>
                    <tr><td><?php echo __("Project Name ") ?></td><td><span id="commentProjectName"></span></td></tr>
                    <tr><td><?php echo __("Activity Name ") ?></td><td><span id="commentActivityName"></span></td></tr>
                    <tr><td><?php echo __("Date ") ?></td><td><span id="commentDate"></span></td></tr>
                </table>
            </div>
            <textarea name="leaveComment" id="timeComment" cols="35" rows="5" class="commentTextArea" ONKEYUP="adjustRows(this)"  WRAP="hard"></textarea>
            <br class="clear" />
            <div class="error" id="commentError"></div>
            <div><input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Close'); ?>" /></div>
        </form>
    </div>
    <script type="text/javascript">
                                                    
        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
        var submitNextState = "<?php echo $submitNextState; ?>";
        var approveNextState = "<?php echo $approveNextState; ?>";
        var submitNextState = "<?php echo $submitNextState; ?>";
        var rejectNextState = "<?php echo $rejectNextState; ?>";
        var resetNextState = "<?php echo $resetNextState; ?>";
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
	var lang_invalidDate= "<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))); ?>";
        var dateList  = <?php echo json_encode($dateForm->getDateOptions()); ?>;



    </script>

<?php endif; ?>
