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
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/editTimesheet')); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/editTimesheetPartial')); ?>

<style type="text/css">
form ol li.largeTextBox textarea {
    width: 365px;
    margin-bottom: 5px;
}
.modal-open form ol li.largeTextBox span.validation-error {
    left: 0;
}    
</style>

<!--<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>-->
<div class="box editTimesheet noHeader" id="edit-timesheet">
    <div class="inner">
        
        <div class="top" style="padding-bottom:5px">
            <h3>
                <?php 
                if ($noOfColumns == 7): 
                    if (isset($employeeName)): 
                        echo __('Edit Timesheet for'). " " . $employeeName . " ".__('for Week')." " . 
                            set_datepicker_date_format($currentWeekDates[0]);
                    else: 
                        echo __('Edit Timesheet for Week'). " " ?><?php echo " " . set_datepicker_date_format($currentWeekDates[0]);
                    endif; 
                endif; 
                if ($noOfColumns == 30 || $noOfColumns == 31): 
                    if (isset($employeeName)): 
                        echo __('Edit Timesheet for')." " . $employeeName . " ".__('for Month starting on') . " " . 
                            set_datepicker_date_format($currentWeekDates[0]);
                    else: 
                        echo __('Edit Timesheet for Month'). " " ?><?php set_datepicker_date_format($currentWeekDates[0]);
                    endif; 
                endif; 
                ?>
            </h3>
        </div>
        
        <div id="validationMsg">
            <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>
        
        <form class="timesheetForm" method="post" id="timesheetForm" >
            <div class="tableWrapper" style="overflow:auto">
            <table style="width:100%" class="table">
                <thead>
                    <tr>
                        <th style="width:2%" class="center"><input type="checkbox" style="display:none"></th>
                        <th style="width:24%" id="projectName"><?php echo __('Project Name') ?></th>
                        <th style="width:18%" id="activityName"><?php echo __('Activity Name') ?></th>
                        <?php foreach ($currentWeekDates as $date): ?>
                            <th class="center" style="width:8%">
                                <?php echo __(date('D', strtotime($date))); ?> 
                                <?php echo date('j', strtotime($date)); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                    echo $timesheetForm['_csrf_token']->render(); ?>
                    <?php if ($timesheetItemValuesArray == null): ?>
                    <tr class="odd">
                        <td id=""><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                        <td>
                            <?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => "project", "size" => 25)) ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?>
                        </td>
                        <td>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(array("class" => "projectActivity")); ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?>
                        </td>
                        <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                            <td class="center comments">
                                <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => 'timeBox')) ?>
<!--                                <span class="" data-toggle="modal" href="#commentDialog">-->
                                    <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon"); ?>
<!--                                </span>-->
                                <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php 
                    $i++; 
                    else: 
                    $x = 0;
                    foreach ($timesheetItemValuesArray as $row): 
                        $dltClassName = ($row['isProjectDeleted'] == 1 || $row['isActivityDeleted'] == 1) ? "deletedRow" : ""; ?>
                        <tr class="<?php echo ($x & 1) ? 'even' : 'odd' ?>">
                            <td id="<?php echo $row['projectId'] . "_" . $row['activityId'] . "_" . $timesheetId . "_" . $employeeId ?>">
                                <?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?>
                            </td>
                            
                            <td>
                                <?php if ($row['isProjectDeleted'] == 1) { ?>
                                    <span class="required">*</span>
                                <?php } ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => $dltClassName." "."project", "size" => 25))?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?>
                            </td>
                            
                            <td>
                                <?php if (($row['isActivityDeleted'] == 1)) { ?>
                                    <span class="required">*</span>
                                <?php }  ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(
                                        array("class" => $dltClassName." "."projectActivity")) ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?>
                            </td>
                            <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                                <td class="center comments"> <!-- title="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>" -->
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->renderError(); ?>
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'timeBox')); ?>
<!--                                    <span class="" data-toggle="modal" href="#commentDialog">-->
                                        <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon ".$dltClassName) ?>
<!--                                    </span>-->
                                    <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j]?>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php 
                        $i++; 
                        $x++;
                    endforeach; 
                    endif; ?>
                        
                        <tr id="extraRows"></tr>
                        
                </tbody>
            </table>
            </div> <!-- tableWrapper -->
        
            <div class="bottom">
                <p class="required">
                    <em>*</em><?php echo " " . __('Deleted project activities are not editable') ?>
                </p>
                <p style="float: right;">
                    <?php sfContext::getInstance()->getUser()->setFlash('employeeId', $employeeId); ?>
                    <input type="submit" class="" value="<?php echo __('Save') ?>" name="btnSave" id="submitSave"/>
                    <input type="button" class="" id="btnAddRow" value="<?php echo __('Add Row') ?>" name="btnAddRow">
                    <input type="button" class="delete" id="submitRemoveRows" value="<?php echo __('Remove Rows') ?>" name="btnRemoveRows">
                    <?php echo button_to(__('Reset'), 'time/editTimesheet?timesheetId=' . $timesheetId . '&employeeId=' . $employeeId . '&actionName=' . $backAction, array('class' => 'reset', 'id' => 'btnReset')) ?>
                    <?php echo button_to(__('Cancel'), 'time/' . $backAction . '?timesheetStartDate=' . $startDate . '&employeeId=' . $employeeId, array('class' => 'reset', 'id' => 'btnBack')) ?>
                </p>
                                
            </div>
        </form>
    </div> <!-- inner -->

</div> <!-- Box -->

<?php echo $listForm ?>
<!-- comment-Dialog -->
<div class="modal hide" id="commentDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Comment'); ?></h3>
    </div>
    <div class="modal-body">
        <form action="updateComment" method="post" id="frmCommentSave" name="frmCommentSave">
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li class="line">
                        <label><?php echo __("Project Name") ?></label>
                        <label id="commentProjectName" class="line"></label>
                    </li>                        
                    <li class="line">                        
                        <label><?php echo __("Activity Name") ?></label>
                        <label id="commentActivityName" class="line"></label>
                    </li>                        
                    <li class="line">                        
                        <label><?php echo __("Date") ?></label>
                        <label id="commentDate" class="line"></label>                        
                    </li>
                    <li class="largeTextBox">
                        <textarea id="timeComment" name="timeComment"></textarea>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <input type="button" id="commentSave" class="" value="<?php echo __('Save'); ?>" />
        <input type="button" id="commentCancel" class="reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- commentDialog -->

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
    var lang_removeSuccess = '<?php echo __('Successfully Removed')?>';
    var lang_noChagesToDelete = '<?php echo __('No Changes to Delete');?>';
    var closeText = '<?php echo __('Close');?>';
    <?php
    for ($i = 0; $i < count($currentWeekDates); $i++) {
        echo "currentWeekDates[$i]='" . $currentWeekDates[$i] . "';\n";
    }
    ?>
</script>