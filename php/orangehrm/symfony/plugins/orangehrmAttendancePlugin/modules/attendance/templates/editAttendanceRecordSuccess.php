<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/editAttendanceRecordSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmAttendancePlugin/js/editAttendanceRecordSuccess'); ?>

<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>
<div id="validationMsg" style="margin-left: 16px;"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

<div class="outerbox">
    <div class="maincontent">
        <form action="" id="employeeRecordsForm" method="post">
            <table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
                <thead id="tableHead">
                    <tr>
                        <td style="width: 900px;"><?php echo __("Punch In"); ?></td>
                        <td><?php echo __("Punch In Note"); ?></td>
                        <td style="width: 900px;"><?php echo __("Punch Out"); ?></td>
                        <td><?php echo __("Punch Out Note"); ?></td>
                        <td style="width: 100px;"><?php echo __("Duration")."(".__("Hours").")"; ?></td>
                    </tr></thead> 
                <?php $i = 1; ?>
                <?php echo $editAttendanceForm['_csrf_token']; ?>
                <?php echo $editAttendanceForm->renderGlobalErrors(); ?>

                <?php if ($records == null): ?>
                    <tr><td colspan ="5"></tr>

                <?php else: ?>

                    <?php foreach ($records as $record): ?>

                        <tr> <?php if ($editPunchIn[$i]): ?>
                                
                                <td> <?php echo $editAttendanceForm['punchInDate_' . $i]->render((array("class" => "inDate"))); ?> &nbsp;<?php echo $editAttendanceForm['punchInTime_' . $i]->render(array("class" => "inTime")); ?><input type="hidden" id="<?php echo "punchInUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchInUtcTime())); ?>"></td>
                                <td><table cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <?php
                                            $comments = trim($record->getPunchInNote());
                                            if (strlen($comments) > 25) {
                                                $comments = substr($comments, 0, 25) . "...";
                                            }
                                            ?>
                                        <input type="hidden" id="<?php echo "attendanceNote_1_3" . "_" . $record->getId(); ?>" value="<?php echo $record->getPunchInNote(); ?>">
                                        <td id="<?php echo "commentLable_1_3" . "_" . $record->getId(); ?>" align="left" width="200"><?php echo htmlspecialchars($comments); ?></td>
                                        <td class="dialogInvoker" id="pen_request"><?php echo image_tag('callout.png', 'id=' . $record->getId() . "_1" . "_3" . " class=icon") ?></td>
                            </tr>
                        </table>
                        </td>

                    <?php else: ?>
                        <td> <?php echo $editAttendanceForm['punchInDate_' . $i]->render(array("class" => "nonEditable")); ?>&nbsp;<?php echo $editAttendanceForm['punchInTime_' . $i]->render(array("class" => "nonEditable")); ?><input type="hidden" id="<?php echo "punchInUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchInUtcTime())); ?>"></td>
                        <td><table cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <?php
                                    $comments = trim($record->getPunchInNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?>
                                <input type="hidden" id="<?php echo "attendanceNote_2_3" . "_" . $record->getId(); ?>" value="<?php echo $record->getPunchInNote(); ?>">
                                <td id="<?php echo "commentLable_2_3" . "_" . $record->getId(); ?>" align="left" width="200"><?php echo htmlspecialchars($comments); ?></td>
                                <td class="dialogInvoker" id="pen_request"><?php echo image_tag('callout.png', 'id=' . $record->getId() . "_2" . "_3" . " class=icon") ?></td>
                                </tr>
                            </table></td>
                    <?php endif; ?>
                    <?php if ($editPunchOut[$i]): ?>

                        <td><?php echo $editAttendanceForm['punchOutDate_' . $i]->renderError(); ?><?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "outDate")); ?>&nbsp;<?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "outTime")); ?><input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo Date('Y-m-d H:i', strtotime($record->getPunchOutUtcTime())); ?>"></td>
                        <td><table cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <?php
                                    $comments = trim($record->getPunchOutNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?>
                                <input type="hidden" id="<?php echo "attendanceNote_1_4" . "_" . $record->getId(); ?>" value="<?php echo $record->getPunchOutNote(); ?>">
                                <td id="<?php echo "commentLable_1_4" . "_" . $record->getId(); ?>" align="left" width="200"><?php echo htmlspecialchars($comments); ?></td>
                                <td class="dialogInvoker" id="pen_request"><?php echo image_tag('callout.png', 'id=' . $record->getId() . "_1" . "_4" . " class=icon") ?></td>
                                </tr>
                            </table></td>

                    <?php else: ?>


                        <?php if ($record->getPunchOutUtcTime() == null): ?>

                            <td><?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "nonEditable", "width" =>'120px')); ?>&nbsp;<?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "nonEditable")); ?><input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo date("Y-m-d H:i", mktime(0, 0, 0, 7, 1, 2030)); ?>"></td>
                        <?php else: ?>
                            <td><?php echo $editAttendanceForm['punchOutDate_' . $i]->render(array("class" => "nonEditable", "width" =>'120px')); ?>&nbsp;<?php echo $editAttendanceForm['punchOutTime_' . $i]->render(array("class" => "nonEditable")); ?><input type="hidden" id="<?php echo "punchOutUtcTime_" . $i; ?>" value="<?php echo date('Y-m-d H:i', strtotime($record->getPunchOutUtcTime())); ?>"></td>
                        <?php endif; ?>
                        <td><table cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <?php
                                    $comments = trim($record->getPunchOutNote());
                                    if (strlen($comments) > 25) {
                                        $comments = substr($comments, 0, 25) . "...";
                                    }
                                    ?> <input type="hidden" id="<?php echo "attendanceNote_2_4" . "_" . $record->getId(); ?>" value="<?php echo $record->getPunchOutNote(); ?>">
                                <td id="<?php echo "commentLable_2_4" . "_" . $record->getId(); ?>" align="left" width="200"><?php echo htmlspecialchars($comments); ?></td>
                                <td class="dialogInvoker" id="pen_request"><?php echo image_tag('callout.png', 'id=' . $record->getId() . "_2" . "_4" . " class=icon") ?></td>
                                </tr>
                            </table></td>

                    <?php endif; ?>
                    <?php if ($record->getPunchOutUtcTime() == null): ?>
                        <td><?php echo "0"; ?></td>
                    <?php else: ?>
                        <td><?php echo round((strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime())) / 3600, 2); ?></td>
                      
                    <?php endif; ?>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
                      <?php echo $editAttendanceForm->renderHiddenFields(); ?>
            </table>

            <div class="formbuttons">

                &nbsp;&nbsp;&nbsp; <input type="button" class="save" name="button" id="btnSave"
                                          onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                                          value="<?php echo __('Save'); ?>" />
                <input type="button" class="cancel" name="button" id="btnCancel"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Cancel'); ?>" />

            </div>
        </form>
    </div>


</div>
<!-- comment dialog -->

<div id="commentDialog" title="<?php echo __('Punch in/out note'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">

        <textarea name="punchInOutNote" id="punchInOutNote" cols="35" rows="8" class="commentTextArea"></textarea>
        <br class="clear" />
        <div class="error" id="noteError"></div>
        <br class="clear" />
        <div><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Save'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>


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