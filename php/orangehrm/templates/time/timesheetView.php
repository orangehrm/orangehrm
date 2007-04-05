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

require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';

$timeExpenses=$records[0];
$timesheet=$records[1];
$timesheetSubmissionPeriod=$records[2];
$dailySum=$records[3];
$employee=$records[4];

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

$duration = 7;
?>
<script type="text/javascript">
<!--
var prev = new Array();
var next = new Array();

next.startDate = '<?php echo date('Y-m-d', $startDate+(3600*24*$duration)); ?>';
next.endDate = '<?php echo date('Y-m-d', $endDate+(3600*24*$duration)); ?>';

prev.startDate = '<?php echo date('Y-m-d', $startDate-(3600*24*$duration)); ?>';
prev.endDate = '<?php echo date('Y-m-d', $endDate-(3600*24*$duration)); ?>';

function $(id) {
	return document.getElementById(id);
}

function actionNav(data) {
	$('txtStartDate').value=data.startDate;
	$('txtEndDate').value=data.endDate;

	$("frmTimesheet").action+= "View_Timesheet";
	$("frmTimesheet").submit();
}


function actionSubmit() {
	$("frmTimesheet").action+= "Submit_Timesheet";
	$("frmTimesheet").submit();
}

function actionEdit() {
	$("frmTimesheet").action+= "View_Edit_Timesheet";
	$("frmTimesheet").submit();
}
-->
</script>
<h2>
	<input src="../../themes/beyondT/icons/resultset_previous.png"
			onclick="actionNav(prev); return false;"
			name="btnPrev" id="btnPrev" type="image"/>
		<?php echo preg_replace(array('/#periodName/', '/#startDate/', '/#name/'),
							array($timesheetSubmissionPeriod->getName(), $timesheet->getStartDate(), "{$employee[2]} {$employee[1]}"),
							$lang_Time_Timesheet_TimesheetNameForViewTitle); ?>
	<input src="../../themes/beyondT/icons/resultset_next.png"
			onclick="actionNav(next); return false;"
			name="btnNext" id="btnNext" type="image"/>
	<hr/>
</h2>

<h3><?php echo preg_replace(array('/#status/'),
							array($statusStr),
							$lang_Time_Timesheet_Status); ?></h3>

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
	    <?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) { ?>
	    	<th class="tableTopMiddle"></th>
	    <?php } ?>
			<th class="tableTopRight"></th>
		</tr>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Customer; ?></th>
			<th width="60px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_ProjectActivity; ?></th>
		<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) { ?>
	    	<th width="70px" class="tableMiddleMiddle"><?php echo date('l Y-m-d', $i); ?></th>
	    <?php } ?>
			<th class="tableMiddleRight"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (isset($timeExpenses) && is_array($timeExpenses)) {

			$customerObj = new Customer();
			$projectObj = new Projects();
			foreach ($timeExpenses as $project=>$timeExpense) {

				$projectDet = $projectObj->fetchProject($project);

				$customer = $customerObj->fetchCustomer($projectDet->getCustomerId());
			?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td ><?php echo $customer->getCustomerName(); ?></td>
				<td ><?php echo $projectDet->getProjectName(); ?></td>
			<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
					if (!isset($timeExpense[$i])) {
						$timeExpense[$i]=0;
					}
			?>
	    		<td ><?php echo round($timeExpense[$i]/36)/100; ?></td>
	    	<?php } ?>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php } ?>
			<tr>
				<th class="tableMiddleLeft"></th>
				<th ><?php echo $lang_Time_Timesheet_Total; ?></th>
				<th ></th>
			<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
					if (!isset($dailySum[$i])) {
						$dailySum[$i]=0;
					}
			?>
		    	<th ><?php echo round($dailySum[$i]/36)/100; ?></th>
		    <?php } ?>
				<th class="tableMiddleRight"></th>
			</tr>
		<?php } else { ?>
			<tr>
				<td class="tableMiddleLeft"></td>
				<td ><?php echo $lang_Error_NoRecordsFound; ?></td>
				<td ></td>
			<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) { ?>
	    		<td ></td>
	    	<?php } ?>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php }?>
	</tbody>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
		<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) { ?>
			<td class="tableBottomMiddle"></td>
		<?php } ?>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
<p id="controls">
<form id="frmTimesheet" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">

<input type="hidden" id="txtTimesheetId" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input type="hidden" name="txtEmployeeId" value="<?php echo $timesheet->getEmployeeId(); ?>" />

<input type="hidden" id="txtTimesheetPeriodId" name="txtTimesheetPeriodId" value="<?php echo $timesheet->getTimesheetPeriodId(); ?>" />
<input type="hidden" id="txtStartDate" name="txtStartDate" value="<?php echo $timesheet->getStartDate(); ?>" />
<input type="hidden" id="txtEndDate" name="txtEndDate" value="<?php echo $timesheet->getEndDate(); ?>" />

<input src="../../themes/beyondT/pictures/btn_edit.jpg"
		onclick="actionEdit(); return false;"
		onmouseover="this.src='../../themes/beyondT/pictures/btn_edit_02.jpg';"
		onmouseout="this.src='../../themes/beyondT/pictures/btn_edit.jpg';"
		name="btnEdit" id="btnEdit" height="20" type="image" width="65"/>
<input src="../../themes/beyondT/pictures/btn_submit.gif"
		onclick="actionSubmit(); return false;"
		onmouseover="this.src='../../themes/beyondT/pictures/btn_submit_02.gif';"
		onmouseout="this.src='../../themes/beyondT/pictures/btn_submit.gif';"
		name="btnSubmit" id="btnSubmit" height="20" type="image" width="65"/>
</form>
</p>
