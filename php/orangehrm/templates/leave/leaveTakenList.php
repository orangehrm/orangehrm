<?php
/**
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
 */
?>

<h2><?php echo $lang_Leave_Leave_list_Title5; ?><hr/></h2>
<?php if (count($records) == 0) {?>
<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php } else {?>

<?php
if (isset($_GET['message']) && $_GET['message'] == "Success") {
?>
<font color="#009933" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php
	echo $lang_Leave_CANCEL_SUCCESS;
} else if (isset($_GET['message']) && $_GET['message'] == "Failiure") {
?>
<font color="#ff6600" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php
	echo $lang_Leave_CANCEL_FAILURE;
}
?>
</font>

<form id="frmCancelTakenLeave" name="frmCancelTakenLeave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_CancelTakenLeaves">

<table border="0" cellpadding="0" cellspacing="0">
<thead>
<tr>
<th class="tableTopLeft"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopMiddle"></th>
<th class="tableTopRight"></th>
</tr>

<tr>
<th class="tableMiddleLeft"></th>
<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date; ?></th>
<th width="200px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_EmployeeName; ?></th>
<th width="50px" class="tableMiddleMiddle"><?php echo $lang_Leave_NoOfHours; ?></th>
<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType; ?></th>
<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Status; ?></th>
<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Comments; ?></th>
<th class="tableMiddleRight"></th>
</tr>
</thead>
<tbody>

<?php

$j = 0;
if (is_array($records)) {
	foreach ($records as $record) {
		if(!($j%2)) {
			$cssClass = 'odd';
		} else {
			$cssClass = 'even';
		}
	$j++;

?>

<input type="hidden" name="leaveId[]" value="<?php echo $record->getLeaveId(); ?>" />
<input type="hidden" name="leaveTypeId[]" value="<?php echo $record->getLeaveTypeId(); ?>" />
<input type="hidden" name="employeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
<input type="hidden" name="leaveYear[]" value="<?php echo substr($record->getLeaveDate(), 0, 4); ?>" />
<input type="hidden" name="noHours[]" value="<?php echo $record->getNoHours(); ?>" />
<tr>
<td class="tableMiddleLeft"></td>
<td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveDate(); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $record->getEmployeeName(); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $record->getNoHours(); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
<td class="<?php echo $cssClass; ?>">
<select name="leaveStatus[]">
<option value="3" selected="selected" ><?php echo $lang_Leave_Common_Taken; ?></option>
<option value="0"><?php echo $lang_Leave_Common_Cancel; ?></option>
</select>
</td>
<td class="<?php echo $cssClass; ?>">
<input type="text"  name="leaveComments[]" value="<?php echo $record->getLeaveComments(); ?>" />
</td>
<td class="tableMiddleRight"></td>
</tr>

<?php } } ?>

</tbody>
<tfoot>
<tr>
<td class="tableBottomLeft"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomMiddle"></td>
<td class="tableBottomRight"></td>
</tr>
</tfoot>

</table>
<p id="controls">
<input type="image" name="Save" class="save" src="../../themes/beyondT/pictures/btn_save.gif"/>
</p>
<?php } ?>
</form>

