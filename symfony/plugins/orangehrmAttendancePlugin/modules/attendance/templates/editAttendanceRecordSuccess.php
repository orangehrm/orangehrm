
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/editAttendanceRecordSuccess')); ?>
<?php if($attendancePermissions->canRead()){?>
<div class="box miniList ">
    
    <div class="head">
        <h1><?Php echo __('Edit Attendance Records'); ?></h1>
    </div>
    
    <div class="inner">
        
        <div id="validationMsg">
            <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>
        
        <form action="" id="employeeRecordsForm" method="post">
            <div class="top">
                <input type="button" class="" name="button" id="btnSave" value="<?php echo __('Save'); ?>" />
                <input type="button" class="reset" name="button" id="btnCancel" value="<?php echo __('Cancel'); ?>" />
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 25%;"><?php echo __("Punch In"); ?></th>
                        <th style="width: 20%;"><?php echo __("Punch In Note"); ?></th>
                        <th style="width: 25%;"><?php echo __("Punch Out"); ?></th>
                        <th style="width: 20%;"><?php echo __("Punch Out Note"); ?></th>
                        <th style="width: 10%;" class="center"><?php echo __("Duration") . "(" . __("Hours") . ")"; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php echo $editAttendanceForm['_csrf_token']; ?>
                    <?php echo $editAttendanceForm->renderGlobalErrors(); ?>
                    <?php if ($records == null): ?>
                        <tr><td colspan ="5"></tr>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                        <tr class="<?php echo ($i & 1) ? 'odd' : 'even' ?>"> 
                            <?php if ($editPunchIn[$i]): ?>
                                <td>
                                    <?php echo $editAttendanceForm['punchInDate_' . $i]->render((array("class" => "inDate"))); ?>
                                    <?php echo $editAttendanceForm['punchInTime_' . $i]->render(array("class" => "timeBox inTime")); ?>
                                    <input type="hidden" id="<?php echo "punchInUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchInUtcTime())); ?>">
                                </td>
                                <td>
                                    <?php
                                    $comments = trim($record->getPunchInNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?>
                                    <span id="<?php echo "commentLable_1_3" . "_" . $record->getId(); ?>">
                                        <?php echo htmlspecialchars($comments); ?>
                                    </span>
                                    <?php echo image_tag(theme_path('images/comment.png'), 'id=' . $record->getId() . "_1" . "_3" . " class=icon"); ?>
                                    <input type="hidden" id="<?php echo "attendanceNote_1_3" . "_" . $record->getId(); ?>" 
                                           value="<?php echo $record->getPunchInNote(); ?>">
                                </td>
                            <?php else: ?>
                                <td>
                                    <?php echo $editAttendanceForm['punchInDate_' . $i]->render(array("class" => "nonEditable")); ?>
                                    <?php echo $editAttendanceForm['punchInTime_' . $i]->render(array("class" => "timeBox nonEditable")); ?>
                                    <input type="hidden" id="<?php echo "punchInUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchInUtcTime())); ?>">
                                </td>
                                <td>
                                    <?php
                                    $comments = trim($record->getPunchInNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?>
                                    <span id="<?php echo "commentLable_2_3" . "_" . $record->getId(); ?>">
                                        <?php echo htmlspecialchars($comments); ?>
                                    </span>
                                    <?php // echo image_tag(theme_path('images/comment.png'), 'id=' . $record->getId() . "_2" . "_3" . " class=icon"); ?>
                                    <input type="hidden" id="<?php echo "attendanceNote_2_3" . "_" . $record->getId(); ?>" 
                                           value="<?php echo $record->getPunchInNote(); ?>">

                                </td>
                            <?php endif; ?>
                            <?php if ($editPunchOut[$i]): ?>
                                <td>
                                    <?php echo $editAttendanceForm['punchOutDate_' . $i]->renderError(); ?>
                                    <?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "outDate")); ?>
                                    <?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "timeBox outTime")); ?>
                                    <input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo Date('Y-m-d H:i', strtotime($record->getPunchOutUtcTime())); ?>">
                                </td>
                                <td>
                                    <?php
                                    $comments = trim($record->getPunchOutNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?>
                                    <input type="hidden" id="<?php echo "attendanceNote_1_4" . "_" . $record->getId(); ?>" value="<?php echo $record->getPunchOutNote(); ?>">
                                    <span id="<?php echo "commentLable_1_4" . "_" . $record->getId(); ?>">
                                        <?php echo htmlspecialchars($comments); ?>
                                    </span> 
                                    <?php echo image_tag(theme_path('images/comment.png'), 'id=' . $record->getId() . "_1" . "_4" . " class=icon"); ?>
                                </td>
                            <?php else: ?>
                                <?php if ($record->getPunchOutUtcTime() == null): ?>
                                    <td>
                                        <?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "nonEditable")); ?>
                                        <?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "timeBox nonEditable")); ?>
                                        <input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo date("Y-m-d H:i", mktime(0, 0, 0, 7, 1, 2030)); ?>">
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "nonEditable")); ?>
                                        <?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "timeBox nonEditable")); ?>
                                        <input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchOutUtcTime())); ?>">
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php
                                    $comments = trim($record->getPunchOutNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?> 
                                    <input type="hidden" id="<?php echo "attendanceNote_2_4" . "_" . $record->getId(); ?>" 
                                        value="<?php echo $record->getPunchOutNote(); ?>">
                                    <span id="<?php echo "commentLable_2_4" . "_" . $record->getId(); ?>">
                                        <?php echo htmlspecialchars($comments); ?>
                                    </span>
                                    <?php // echo image_tag(theme_path('images/comment.png'), 'id=' . $record->getId() . "_2" . "_4" . " class=icon"); ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($record->getPunchOutUtcTime() == null): ?>
                                <td><?php echo "0"; ?></td>
                            <?php else: ?>
                                <td>
                                    <?php echo round((strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime())) / 3600, 2); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php echo $editAttendanceForm->renderHiddenFields(); ?>
                </tbody>
            </table>
            
        </form>
    </div><!-- inner -->
</div><!-- box -->

<!-- commentDialog-Dialog -->
<div class="modal hide" id="commentDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Punch in/out note'); ?></h3>
    </div>
    <div class="modal-body">
        <form action="updateComment" method="post" id="frmCommentSave">
            <?php // echo $['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li class ="largeTextBox">
                        <label for="punchInOutNote"><?php echo __('Comment') ?></label>
                        <textarea name="punchInOutNote" id="punchInOutNote" class="largeTextBox"></textarea>
                    </li>
                </ol>
            </fieldset>
        </form> 
    </div>
    <div class="modal-footer">
        <input type="button" id="commentSave" class="" value="<?php echo __('Save'); ?>" />
        <input type="button" id="commentCancel" data-dismiss="modal" class="reset" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- commentDialog -->
<?php }?>

<script type="text/javascript">
  
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var recordDate='<?php echo $date; ?>';
    var linkToViewEmployeeRecords='<?php echo url_for('attendance/viewAttendanceRecord'); ?>'
    var linkToViewMyRecords='<?php echo url_for('attendance/viewMyAttendanceRecord'); ?>'
    var linkToEdit='<?php echo url_for('attendance/editAttendanceRecord'); ?>'
    var linkForOverLappingValidation='<?php echo url_for('attendance/validatePunchOutOverLappingWhenEditing') ?>';
    var linkForPunchInOverlappingValidation='<?php echo url_for('attendance/validatePunchInOverLappingWhenEditing') ?>';
    var nonEditableOutDate = <?php echo json_encode($editAttendanceForm->nonEditableOutDate); ?>;
    var updateCommentlink='<?php echo url_for('attendance/updatePunchInOutNote'); ?>'
    var errorForInvalidTime='<?php echo __('Punch out Time Should Be Higher Than Punch in Time'); ?>';
    var errorForInvalidFormat = '<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>';
    var errorForInvalidTimeFormat='<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>';
    var getCurrentTimeLink='<?php echo url_for('attendance/getCurrentTime') ?>';
    var errorForInvalidDateFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
    var errorForOverLappingTime='<?php echo __('Overlapping Records Found'); ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>'
    var commentError='<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var errorRows = '<?php echo $errorRows; ?>';
     
</script>