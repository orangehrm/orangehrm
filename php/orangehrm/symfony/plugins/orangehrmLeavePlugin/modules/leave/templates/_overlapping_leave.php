<?php
if (!empty($overlapLeave)) {
    if (count($overlapLeave) == 1) {
        $overlapHeading = __('Overlapping Leave Request Found');
    } else {
        $overlapHeading = __('Overlapping Leave Requests Found');
    }
    ?>
<div class="box single">
    <div class="head"><h1><?php echo $overlapHeading; ?></h1></div>
    <div class="inner">
        <table border="0" cellspacing="0" cellpadding="0" class="table">
            <thead>
                <tr>
                    <th width="100px"><?php echo __("Date") ?></th>
                    <th width="100px"><?php echo __("No of Hours") ?></th>
                    <th width="90px"><?php echo __("Leave Type") ?></th>
                    <th width="200px"><?php echo __("Status") ?></th>
                    <th width="150px"><?php echo __("Comments") ?></th>
                </tr>
            </thead>
            <tbody>

<?php
                $oddRow = true;
                foreach ($overlapLeave as $leave) {
                    $class = $oddRow ? 'odd' : 'even';
                    $oddRow = !$oddRow;
?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo set_datepicker_date_format($leave->getDate()) ?></td>
                        <td><?php echo $leave->getLengthHours() ?></td>
                        <td><?php echo $leave->getLeaveRequest()->getLeaveType()->getName() ?></td>
                        <td><?php echo __(ucwords(strtolower($leave->getTextLeaveStatus()))); ?></td>
                        <td><?php echo $leave->getComments() ?></td>
                    </tr>
<?php } ?>

            </tbody>
        </table>
    </div>
</div>
<?php } ?>