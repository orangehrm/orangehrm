<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
 *
 */
?>

<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
 *
 */

$timesheet=$records[0];
$timesheetSubmissionPeriod=$records[1];
$timeExpenses=$records[2];
$customers=$records[3];

$status=$timesheet->getStatus();

switch ($status) {
	case Timesheet::TIMESHEET_STATUS_NOT_SUBMITTED : $statusStr = $lang_Time_Timesheet_Status_NotSubmitted;
												break;
	case Timesheet::TIMESHEET_STATUS_SUBMITTED : $statusStr = $lang_Time_Timesheet_Status_Submitted;
												break;
	case Timesheet::TIMESHEET_STATUS_APPROVED : $statusStr = $lang_Time_Timesheet_Status_Approved;
												break;
	case Timesheet::TIMESHEET_STATUS_REJECTED : $statusStr = $lang_Time_Timesheet_Status_Rejected;
												break;
}

$startDate = strtotime($timesheet->getStartDate());
$endDate = strtotime($timesheet->getEndDate());

?>
<style type="text/css" >
textarea, input, select {
	margin: 5px;
}
</style>
<script type="text/javascript">
<!--
function actionSubmit() {
	document.getElementById("frmTimesheet").action+= "Submit_Timesheet";

	document.getElementById("frmTimesheet").submit();
}

function addRow() {

}
-->
</script>
<h2><?php echo preg_replace(array('/#periodName/', '/#startDate/'),
							array($timesheetSubmissionPeriod->getName(), $timesheet->getStartDate()),
							$lang_Time_Timesheet_TimesheetForEditTitle); ?>
  <hr/>
</h2>

<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$expString = explode ("_",$expString);
		$length = count($expString);

		$col_def=strtolower($expString[$length-1]);

		$expString='lang_Time_Errors_'.$_GET['message'];
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
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
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Customer; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_ProjectActivity; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_StartTime; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_EndTime; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_ReportedDate; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Duration; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Decription; ?></th>
			<th class="tableMiddleRight"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (isset($timeExpenses) && is_array($timeExpenses)) {
			foreach ($timeExpenses as $project=>$timeExpense) { ?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td ><?php echo $project; ?></td>
				<td ><?php echo $project; ?></td>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php }
		} ?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td ><select id="cmbCustomer">
				<?php if (is_array($customers)) { ?>
						<option value="0">- <?php echo $lang_Leave_Common_Select;?> -</option>
				<?php	foreach ($customers as $customer) { ?>
						<option value="<?php echo $customer->getCustomerId(); ?>"><?php echo $customer->getCustomerName(); ?></option>
				<?php 	}
					} else { ?>
						<option value="0">- <?php echo $lang_Time_Timesheet_NoCustomers;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td ><select id="cmbProject">
						<option value="0">- <?php echo $lang_Leave_Common_Select;?> -</option>
					</select>
				</td>
				<td><input type="text" id="txtStartTime" /></td>
				<td><input type="text" id="txtEndTime" /></td>
				<td><input type="text" id="txtReportedDate" /></td>
				<td><input type="text" id="txtDuration" /></td>
				<td><textarea type="text" id="txtDescription" ></textarea></td>
				<td class="tableMiddleRight"></td>
			</tr>
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
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
<p id="controls">
<form id="frmTimesheet" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
<input type="hidden" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input src="../../themes/beyondT/pictures/btn_edit.jpg"
		onclick="actionEdit(); return false;"
		onmouseover="this.src='../../themes/beyondT/pictures/btn_edit_02.jpg';"
		onmouseout="this.src='../../themes/beyondT/pictures/btn_edit.jpg';"
		name="btnEdit" id="btnEdit" height="20" type="image" width="65">
<input src="../../themes/beyondT/pictures/btn_submit.gif"
		onclick="actionSubmit(); return false;"
		onmouseover="this.src='../../themes/beyondT/pictures/btn_submit_02.gif';"
		onmouseout="this.src='../../themes/beyondT/pictures/btn_submit.gif';"
		name="btnEdit" id="btnEdit" height="20" type="image" width="65">
</form>
</p>
