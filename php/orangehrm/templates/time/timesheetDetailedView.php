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
 *
 */

$timesheet=$records[0];
$timesheetSubmissionPeriod=$records[1];
$timeExpenses=$records[2];
$employee=$records[3];
$self=$records[4];

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

$row=0;
?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";

function goBack() {
	window.location=initialAction+"View_Timesheet&id=<?php echo $timesheet->getTimesheetId(); ?>";
}

function $(id) {
	return document.getElementById(id);
}

function actionEdit() {
	window.location=initialAction+"View_Edit_Timesheet&id=<?php echo $timesheet->getTimesheetId(); ?>&return=View_Detail_Timesheet";
}
</script>
<h2><?php 	$headingStr = $lang_Time_Timesheet_TimesheetNameForEditTitle;
			if ($self) {
				$headingStr = $lang_Time_Timesheet_TimesheetNameForViewTitle;
			}
			echo preg_replace(array('/#periodName/', '/#startDate/', '/#name/'),
							array($timesheetSubmissionPeriod->getName(), LocaleUtil::getInstance()->formatDate($timesheet->getStartDate()), "{$employee[2]} {$employee[1]}"),
							$headingStr); ?>
  <hr/>
</h2>
<div id="status"></div>
<p class="navigation">
  	  <input type="image" title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack(); return false;">
</p>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
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
			<th width="95px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Project; ?></th>
			<th width="80px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Activity; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_StartTime; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_EndTime; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_ReportedDate; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Duration; ?> <?php echo $lang_Time_Timesheet_DurationUnits; ?></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Decription; ?></th>
			<th class="tableMiddleRight"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$customerObj = new Customer();
		$projectObj = new Projects();
		$projectActivityObj = new ProjectActivity();

		if (isset($timeExpenses) && is_array($timeExpenses)) {

			foreach ($timeExpenses as $timeExpense) {
				$projectId = $timeExpense->getProjectId();

				$projectDet = $projectObj->fetchProject($projectId);
				$projectActivity = $projectActivityObj->getActivity($timeExpense->getActivityId());
				$customerDet = $customerObj->fetchCustomer($projectDet->getCustomerId(), true);
			?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td><?php echo "{$customerDet->getCustomerName()} - {$projectDet->getProjectName()}"; ?></td>
				<td><?php echo $projectActivity->getName(); ?></td>
				<td><?php echo LocaleUtil::getInstance()->formatDateTime($timeExpense->getStartTime()); ?></td>
				<td><?php echo LocaleUtil::getInstance()->formatDateTime($timeExpense->getEndTime()); ?></td>
				<td><?php echo LocaleUtil::getInstance()->formatDate($timeExpense->getReportedDate()); ?></td>
				<td><?php echo round($timeExpense->getDuration()/36)/100; ?></td>
				<td><?php echo $timeExpense->getDescription(); ?></td>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php }
		} else { ?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td colspan="7"><?php echo $lang_Error_NoRecordsFound; ?></td>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php }?>
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
<form id="frmTimesheet" name="frmTimesheet" method="post" action="?timecode=Time&action=">

<input type="hidden" id="txtTimesheetId" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input type="hidden" name="txtEmployeeId" value="<?php echo $timesheet->getEmployeeId(); ?>" />

<input type="hidden" id="txtTimesheetPeriodId" name="txtTimesheetPeriodId" value="<?php echo $timesheet->getTimesheetPeriodId(); ?>" />
<input type="hidden" id="txtStartDate" name="txtStartDate" value="<?php echo LocaleUtil::getInstance()->formatDate($timesheet->getStartDate()); ?>" />
<input type="hidden" id="txtEndDate" name="txtEndDate" value="<?php echo LocaleUtil::getInstance()->formatDate($timesheet->getEndDate()); ?>" />

<div>
<?php if ($timesheet->getStatus() != Timesheet::TIMESHEET_STATUS_APPROVED) { ?>
	<input src="../../themes/beyondT/pictures/btn_edit.gif"
			onclick="actionEdit(); return false;"
			onmouseover="this.src='../../themes/beyondT/pictures/btn_edit_02.gif';"
			onmouseout="this.src='../../themes/beyondT/pictures/btn_edit.gif';"
			name="btnEdit" id="btnEdit" height="20" type="image" width="65"/>
<?php } ?>
</div>
</form>
</p>

