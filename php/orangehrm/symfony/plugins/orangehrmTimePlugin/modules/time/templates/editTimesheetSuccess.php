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
<?php
$noOfColumns = sizeof($currentWeekDates);
$width = 450 + $noOfColumns * 75;
?>
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/editTimesheetSuccess'); ?>
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/time'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheet'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheetPartial'); ?>
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../../../themes/orange/css/style.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>


<?php if ($noOfColumns == 7): ?>
    <?php if (isset($employeeName)): ?>
<h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for'). " " . $employeeName . " ".__('for Week')." " ?><?php echo set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php else: ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for Week'). " " ?><?php echo " " . set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php endif; ?>
<?php endif; ?>
<?php if ($noOfColumns == 30 || $noOfColumns == 31): ?>
    <?php if (isset($employeeName)): ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for')." " . $employeeName . " ".__('for Month starting on'). " " ?><?php echo set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php else: ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for Month'). " " ?><?php set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php endif; ?>
<?php endif; ?>

<div class="outerbox" style="width: <?php echo $width . 'px' ?>">
    <form class="timesheetForm" method="post" id="timesheetForm" >

        <table  class = "data-table" cellpadding ="0" border="0" cellspacing="0">

            <thead>
                <tr>
                    <td><?php echo ' ' ?></td>
                    <td id="projectName"><?php echo __('Project Name') ?></td>
                    <td id="activityName"><?php echo __('Activity Name') ?></td>
                    <?php foreach ($currentWeekDates as $date): ?>
                        <td align="center" style="padding-right: 15px"><?php echo __(date('D', strtotime($date))); ?> <br/><?php echo date('j', strtotime($date)); ?></td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tr> <td colspan="100"></td></tr>
            <?php $i = 0 ?>

            <?php if ($timesheetItemValuesArray == null): ?>
                <tr>
                    <td ><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                    <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?><td>&nbsp;<?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectName'] ?></td>
                    <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?><td>&nbsp;<?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName'] ?></td>
                    <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                        <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?><td style="text-align:center"><?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?><div style="float: left; padding-left: 20px"><?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5)) ?></div><div id="img" style="float: left; padding-left: 2px"><?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon") ?></div></td>
                    <?php } ?>
                </tr>

                <?php $i++ ?>

            <?php else: ?>
                <?php foreach ($timesheetItemValuesArray as $row): ?>
                <?php $dltClassName = ($row['isProjectDeleted'] == 1 || $row['isActivityDeleted'] == 1) ? "deletedRow" : ""?>
                    <tr>
                        <td id="<?php echo $row['projectId'] . "_" . $row['activityId'] . "_" . $timesheetId . "_" . $employeeId ?>"><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                        <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?><td><?php if ($row['isProjectDeleted'] == 1) { ?><span class="required">*</span><?php } else{?>&nbsp;<?php } ?><?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => $dltClassName." "."project"))?></td>
                        <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?><td><?php if (($row['isActivityDeleted'] == 1)) { ?><span class="required">*</span><?php } else{?>&nbsp;<?php } ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(array("class" => $dltClassName." "."projectActivity")) ?></td>
                        <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                             <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j]?><td style="text-align:center" class="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>"><?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?><div style="float: left; padding-left: 20px"><?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items')) ?></div><div id="img" style="float: left; padding-left: 2px"><?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon ".$dltClassName) ?></div></td>
                        <?php } ?>
                    </tr>
                    <?php $i++ ?>

                <?php endforeach; ?>
            <?php endif; ?>

            <td colspan="100">
                <div id="extraRows"/>
            </td>
        </table>

        <div class="formbuttons">
            <?php echo button_to(__('Cancel'), 'time/' . $backAction . '?timesheetStartDate=' . $startDate . '&employeeId=' . $employeeId, array('class' => 'plainbtn', 'id' => 'btnBack')) ?>
            <?php sfContext::getInstance()->getUser()->setFlash('employeeId', $employeeId); ?>
            <input class="plainbtn" type="submit" value="<?php echo __('Save') ?>" name="btnSave" id="submitSave"/>
            <input type="button" class="plainbtn" id="btnAddRow" value="<?php echo __('Add Row') ?>" name="btnAddRow">
            <input type="button" class="plainbtn" id="submitRemoveRows" value="<?php echo __('Remove Rows') ?>" name="btnRemoveRows">
            <?php echo button_to(__('Reset'), 'time/editTimesheet?timesheetId=' . $timesheetId . '&employeeId=' . $employeeId . '&actionName=' . $backAction, array('class' => 'plainbtn', 'id' => 'btnReset')) ?>
        </div>

    </form>
</div>
  <div class="paddingLeftRequired"><span class="required">*</span><?php echo " ".__('Deleted project activities are not editable') ?> </div>
<!-- comment dialog -->

<div id="commentDialog" title="<?php echo __('Comment'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">
        <div>
            <table>
                <tr><td><?php echo __("Project Name") ?></td><td><span id="commentProjectName"></span></td></tr>
                <tr><td><?php echo __("Activity Name") ?></td><td><span id="commentActivityName"></span></td></tr>
                <tr><td><?php echo __("Date") ?></td><td><span id="commentDate"></span></td></tr>
            </table>
        </div>
        <textarea name="leaveComment" id="timeComment" cols="35" rows="5" class="commentTextArea"></textarea>


        <div class="error" id="commentError"></div>
        <div>
            <br class="clear" /><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Save'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>

<!-- end of comment dialog-->
<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var rows = <?php echo $timesheetForm['initialRows']->count() + 1 ?>;
    var link = "<?php echo url_for('time/addRow') ?>";
    var commentlink = "<?php echo url_for('time/updateTimesheetItemComment') ?>";
    var projectsForAutoComplete=<?php echo $timesheetForm->getProjectListAsJson(); ?>;
    var projects = <?php echo $timesheetForm->getProjectListAsJsonForValidation(); ?>;
    var projectsArray = eval(projects);
    var getActivitiesLink = "<?php echo url_for('time/getRelatedActiviesForAutoCompleteAjax') ?>";
    var timesheetId="<?php echo $timesheetId; ?>"
    var lang_not_numeric = '<?php echo __('Should Be Less Than 24 and in HH:MM or Decimal Format'); ?>';
    var rows_are_duplicate = "<?php echo __('Duplicate Records Found'); ?>";
    var project_name_is_wrong = '<?php echo __('Select a Project and an Activity'); ?>';
    var please_select_an_activity = '<?php echo __('Select a Project and an Activity'); ?>';
    var select_a_row = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    var employeeId = '<?php echo $employeeId; ?>';
    var linkToGetComment = "<?php echo url_for('time/getTimesheetItemComment') ?>";
    var linkToDeleteRow = "<?php echo url_for('time/deleteRows') ?>";
    var editAction = "<?php echo url_for('time/editTimesheet') ?>";
    var currentWeekDates = new Array();
    var startDate='<?php echo $startDate ?>';
    var backAction='<?php echo $backAction ?>';
    var endDate='<?php echo $endDate ?>';
    var erorrMessageForInvalidComment="<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
    var numberOfRows='<?php echo $i ?>';
    var incorrect_total="<?php echo __('Total Should Be Less Than 24 Hours'); ?>";
    var typeForHints='<?php echo __('Type for hints').'...'; ?>';
    var lang_selectProjectAndActivity='<?php echo __('Select a Project and an Activity'); ?>';
    var lang_enterExistingProject='<?php echo __("Select a Project and an Activity"); ?>';
    var lang_noRecords='<?php echo __('Select Records to Remove'); ?>';
    var lang_removeSuccess = '<?php echo __('Successfully removed')?>';
    var lang_noChagesToDelete = '<?php echo __('No Changes to Delete');?>';
<?php
for ($i = 0; $i < count($currentWeekDates); $i++) {
    echo "currentWeekDates[$i]='" . $currentWeekDates[$i] . "';\n";
}
?>
</script>