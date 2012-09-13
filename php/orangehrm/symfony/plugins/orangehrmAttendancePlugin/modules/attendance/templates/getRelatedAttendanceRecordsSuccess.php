<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/getRelatedAttendanceRecordsSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmAttendancePlugin/js/getMyRelatedAttendanceRecordsSuccess'); ?>

<table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
    <thead id="tableHead" >
        <tr><td id="checkBox" style="width: 50px;"></td>
            <td><?php echo __("Punch In"); ?></td>
            <td><?php echo __("Punch In Note"); ?></td>
            <td><?php echo __("Punch Out"); ?></td>
            <td><?php echo __("Punch Out Note"); ?></td>
            <td><?php echo __("Duration")."(".__("Hours").")"; ?></td>
        </tr>
    </thead>     
    <?php $class = 'odd'; ?>
    <?php $i = 0; ?>
    <?php $total = 0; ?>
    <?php if ($records == null): ?>  <tr>
            <td id="noRecordsColumn"style="text-align:center" colspan="6"><br><?php echo __("No attendance records to display") ?></td>
        </tr> <?php else: ?> 

        <?php foreach ($records as $record): ?>


            <tr class="<?php echo $class; ?>">
                <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>

                <?php $inUserTimeArray = explode(" ", $record->getPunchInUserTime())?>
                <td id="checkBox" style="vertical-align: text-top"><?php if ($allowedToDelete[$i]): ?><input type="checkbox" id="<?php echo $record->getId() ?>" class="toDelete" value="" ><?php endif; ?></td>
                <td style="vertical-align: text-top"><?php echo set_datepicker_date_format($inUserTimeArray[0])." ".$inUserTimeArray[1] ?><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#98a09f"><?php echo " GMT " . $record->getPunchInTimeOffset(); ?></span></td>
                <td style="vertical-align: text-top"><?php echo $record->getPunchInNote() ?></td>


                <?php if ($record->getPunchOutUserTime() == null): ?>
                    <td></td>
                    <td></td>
                <?php elseif (date('Y-m-d', strtotime($record->getPunchOutUserTime())) != $date): ?>

                    <td style="vertical-align: text-top"><span style="color:#98a09f"><?php echo $record->getPunchOutUserTime() ?></span><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#98a09f"><?php echo " GMT " . $record->getPunchOutTimeOffset(); ?></span></td>
                    <td style="vertical-align: text-top"><?php echo $record->getPunchOutNote() ?></td>
                <?php else: ?>
                    <?php $outUserTimeArray = explode(" ", $record->getPunchOutUserTime())?>
                    <td style="vertical-align: text-top"><?php echo set_datepicker_date_format($outUserTimeArray[0])." ".$outUserTimeArray[1] ?><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#98a09f"><?php echo " GMT " . $record->getPunchOutTimeOffset(); ?></span></td>
                    <td style="vertical-align: text-top"><?php echo $record->getPunchOutNote() ?></td>
                <?php endif; ?>


                <?php if ($record->getPunchOutUtcTime() == null): ?>

                    <td style="vertical-align: text-top"><?php echo "0"; ?></td>

                <?php else: ?>
                    <td style="vertical-align: text-top"><?php echo round((strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime())) / 3600, 2) ?></td>
                    <?php $total = $total + round((strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime())) / 3600, 2) ?>
                <?php endif; ?>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($records != null): ?> 
        <tr class="<?php echo $class; ?>"><td colspan="6"></tr>
        <tr class="<?php echo $class; ?>"><td></td><td id="totalVerticalValue"><?php echo __("Total"); ?></td><td colspan="3"><td id="totalVerticalValue"><?php echo $total; ?></td></tr>
    <?php endif; ?>

</table>


<br>
<div class="formbuttons">
    <form action="" id="employeeRecordsForm" method="post">
        <div>

            <?php if ($allowedActions['Edit']) : ?>
                <input type="button" class="edit" name="button" id="btnEdit"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Edit'); ?>" />
                   <?php endif; ?>

            <?php if ($allowedActions['Delete']) : ?>
                <input type="button" class="delete" name="button" id="btnDelete"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Delete'); ?>" />
                   <?php endif; ?>
                   <?php if ($allowedActions['PunchIn']) : ?>
                <input type="button" class="punch" name="button" id="btnPunchIn"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Add Attendance Records'); ?>" />
                   <?php endif; ?>
                   <?php if ($allowedActions['PunchOut']) : ?>
                <input type="button" class="punch" name="button" id="btnPunchOut"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Add Attendance Records'); ?>" />
                   <?php endif; ?>
        </div>
    </form>
</div>

<div id="dialogBox" class="dialogBox" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>">
    <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>

    <div>
        <br class="clear" />&nbsp;&nbsp;&nbsp;<input type="button" id="dialogOk" class="plainbtn okBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancel" class="plainbtn cancelBtn" value="<?php echo __('Cancel'); ?>" /></div>

</div>


<script type="text/javascript">
    var employeeId='<?php echo $employeeId; ?>';
    var date='<?php echo $date; ?>';
    var linkToEdit='<?php echo url_for('attendance/editAttendanceRecord'); ?>'
    var linkToDeleteRecords='<?php echo url_for('attendance/deleteAttendanceRecords'); ?>'
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>'
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var lang_noRowsSelected='<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
</script>