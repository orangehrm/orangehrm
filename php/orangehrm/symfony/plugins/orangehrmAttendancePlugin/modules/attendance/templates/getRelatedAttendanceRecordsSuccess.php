<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/getRelatedAttendanceRecordsSuccess'); ?>
<?php echo javascript_include_tag('getRelatedAttendanceRecordsSuccess'); ?>



<table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
    <thead id="tableHead">
        <tr><td id="checkBox" style="width: 50px;"></td>
            <td><?php echo __("Punch In"); ?></td>
            <td><?php echo __("Punch In Note"); ?></td>
            <td><?php echo __("Punch Out"); ?></td>
            <td><?php echo __("Punch Out Note"); ?></td>
            <td><?php echo __("Duration(Hours) "); ?></td>
        </tr>
    </thead>     
    <?php $class = 'odd'; ?>
    <?php $i = 0; ?>
    <?php if ($records == null): ?>  <tr>
            <td id="noRecordsColumn"style="text-align:center" colspan="6"><br><?php echo "No attendance records to display!" ?></td>
        </tr> <?php else: ?>  
        <?php foreach ($records as $record): ?>


            <tr class="<?php echo $class; ?>">
                <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>


                <td id="checkBox"><?php if ($r[$i]): ?><input type="checkbox" id="<?php echo $record->getId() ?>" class="toDelete" value="" ><?php endif; ?></td><td><?php echo $record->getPunchInUserTime() ?></td>
                <td><?php echo $record->getPunchInNote() ?></td>
                <td><?php echo $record->getPunchOutUserTime() ?></td>
                <td><?php echo $record->getPunchOutNote() ?></td>
                <?php if ($record->getPunchOutUtcTime() == null): ?>
                    <td><?php echo "0"; ?></td>
                <?php else: ?>
                    <td><?php echo round((strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime())) / 3600, 2) ?></td>
                <?php endif; ?>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
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
                       value="<?php echo __('Punch In'); ?>" />
                   <?php endif; ?>
                   <?php if ($allowedActions['PunchOut']) : ?>
                <input type="button" class="punch" name="button" id="btnPunchOut"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                       value="<?php echo __('Punch Out'); ?>" />
                   <?php endif; ?>
        </div>
    </form>
</div>

<div id="dialogBox" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>">
    <?php echo __("Selected attenadnce records will be deleted?"); ?>

    <div>
        <br class="clear" /><input type="button" id="ok" class="plainbtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="cancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>

</div>


<script type="text/javascript">
    var employeeId='<?php echo $employeeId; ?>';
    var date='<?php echo $date; ?>';
    var linkToEdit='<?php echo url_for('attendance/editAttendanceRecord'); ?>'
    var linkToDeleteRecords='<?php echo url_for('attendance/deleteAttendanceRecords'); ?>'
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>'
    var actionName="view";
</script>