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

require_once ROOT_PATH . '/lib/controllers/TimeController.php';

function populateProjects($cutomerId, $row) {
	ob_clean();

	$timeController = new TimeController();
	$projects = $timeController->fetchCustomersProjects($cutomerId);

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$element="cmbProject[$row]";

	$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projects,0,'frmTimesheet',$element);

	$objResponse->addScript('document.getElementById("'.$element.'").focus();');

	$objResponse->addAssign('status','innerHTML','');

	error_log("{$objResponse->getXML()}\n", 3, ROOT_PATH.'/lib/logs/logDB.txt');

	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateProjects');
$objAjax->processRequests();

$timesheet=$records[0];
$timesheetSubmissionPeriod=$records[1];
$timeExpenses=$records[2];
$customers=$records[3];
$projects=$records[4];
$employee=$records[5];
$self=$records[6];

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
<style type="text/css" >
textarea, input, select {
	margin: 5px;
}
</style>
<script type="text/javascript">
<!--
currFocus = null;
totRows = 0;

function $(id) {
	return document.getElementById(id);
}

function actionSubmit() {
	$("frmTimesheet").action+= "Submit_Timesheet";

	$("frmTimesheet").submit();
}

function looseCurrFocus(row) {
	currFocus = null;
}

function setCurrFocus(label, row) {
	currFocus = $(label+"["+row+"]");
}

function actionInsertTime() {
	if (currFocus) {
		if (currFocus.value == "") {
    		currFocus.value = formatDate(new Date(), "yyyy-MM-dd HH:mm");
  		}
	}
	currFocus.focus();
}

function validate() {
	errors = new Array();
	err = new Array();
	errFlag = false;
	for (i=0; i<=totRows; i++) {
		if (!allEmpty(i)) {
			err[i]=false;

			obj = $("txtDuration["+i+"]");
			if (validateInterval(i) && !((obj.value == '') || (obj.value == 0)) && (obj.value != duration(i))) {
				errors[0] = "<?php echo $lang_Time_Errors_NotAllowedToSpecifyDurationAndInterval; ?>";
				err[i]=true;
				errFlag=true;
				alert(duration(i));
			}

			if ((obj.value == '') || (obj.value == 0)) {
				if (!validateInterval(i)) {
					errors[2] = "<?php echo $lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified; ?>";
					err[i]=true;
					errFlag=true;
				}
			}

			if ($("cmbCustomer["+i+"]").value == 0) {
				errors[3] = "<?php echo $lang_Time_Errors_CustomerNotSpecified; ?>";
				err[i]=true;
				errFlag=true;
			}

			if ($("cmbProject["+i+"]").value == 0) {
				errors[4] = "<?php echo $lang_Time_Errors_ProjectNotSpecified; ?>";
				err[i]=true;
				errFlag=true;
			}

			if ($("txtReportedDate["+i+"]").value == "") {
				errors[5] = "<?php echo $lang_Time_Errors_ReportedDateNotSpecified; ?>";
				err[i]=true;
				errFlag=true;
			}

			if (err[i]) {
				$("row["+i+"]").style.background = "#FFAAAA";
			} else {
				$("row["+i+"]").style.background = "#FFFFFF";
			}
		}
	}

	if (errFlag) {
		errStr="<?php echo $lang_Time_Errors_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr+=" - "+errors[i]+"\n";
		}
		alert(errStr);

		return false;
	}

	return true;
}

function allEmpty(row) {
	unUsed=true;

	if (!(($("txtDuration["+row+"]").value == "") || ($("txtDuration["+row+"]").value == 0))) {
		unUsed=false;
	}

	if ($("cmbCustomer["+row+"]").value != 0) {
		unUsed=false;
	}

	if ($("cmbProject["+row+"]").value != 0) {
		unUsed=false;
	}

	if ($("txtDescription["+row+"]").value != "") {
		unUsed=false;
	}

	if (validateInterval(row)) {
		unUsed=false;
	}

	return unUsed;
}

function validateInterval(row) {
	startTime = strToTime($("txtStartTime["+row+"]").value);
	endTime = strToTime($("txtEndTime["+row+"]").value);

	if (!startTime) return false;

	if (startTime && !endTime) {
		return true;
	}

	if (endTime > startTime) {
		return true;
	} else {
		return false;
	}
}

function duration(row) {
	startTime = strToTime($("txtStartTime["+row+"]").value);
	endTime = strToTime($("txtEndTime["+row+"]").value);

	if (!startTime) return 0;

	if (startTime && !endTime) {
		return 0;
	}

	if (endTime > startTime) {
		return (Math.round((endTime - startTime)/36000)/100);
	}

	return 0;
}

function actionUpdate() {
	if (!validate()) return false;

	$('frmTimesheet').action+='Edit_Timesheet';
	$('frmTimesheet').submit();
}
-->
</script>
<?php $objAjax->printJavascript(); ?>
<h2><?php 	$headingStr = $lang_Time_Timesheet_TimesheetNameForEditTitle;
			if ($self) {
				$headingStr = $lang_Time_Timesheet_TimesheetForEditTitle;
			}
			echo preg_replace(array('/#periodName/', '/#startDate/', '/#name/'),
							array($timesheetSubmissionPeriod->getName(), $timesheet->getStartDate(), "{$employee[2]} {$employee[1]}"),
							$headingStr); ?>
  <hr/>
</h2>
<div id="status"></div>
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
<form id="frmTimesheet" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&id=<?php echo $timesheet->getTimesheetId(); ?>&action=">
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

			$customerObj = new Customer();
			$projectObj = new Projects();
			foreach ($timeExpenses as $timeExpense) {
				$projectId = $timeExpense->getProjectId();

				$projectDet = $projectObj->fetchProject($projectId);

				$customerDet = $customerObj->fetchCustomer($projectDet->getCustomerId());
			?>
			<tr id="row[<?php echo $row; ?>]">
				<td class="tableMiddleLeft"></td>
				<td ><select id="cmbCustomer[<?php echo $row; ?>]" name="cmbCustomer[]" onfocus="looseCurrFocus();" onchange="$('status').innerHTML='Loading...'; xajax_populateProjects(this.value, <?php echo $row; ?>);">
				<?php if (is_array($customers)) { ?>
						<option value="0">- <?php echo $lang_Leave_Common_Select;?> -</option>
				<?php	foreach ($customers as $customer) {
							$selected="";
							if ($customerDet->getCustomerId() == $customer->getCustomerId()) {
								$selected="selected";
							}
				?>
						<option <?php echo $selected; ?> value="<?php echo $customer->getCustomerId(); ?>"><?php echo $customer->getCustomerName(); ?></option>
				<?php 	}
					} else { ?>
						<option value="0">- <?php echo $lang_Time_Timesheet_NoCustomers;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td ><select id="cmbProject[<?php echo $row; ?>]" name="cmbProject[]" onfocus="looseCurrFocus();">
				<?php if (is_array($projects)) { ?>
						<option value="0">- <?php echo $lang_Leave_Common_Select;?> -</option>
				<?php	foreach ($projects as $project) {
							$selected="";
							if ($projectDet->getProjectId() == $project->getProjectId()) {
								$selected="selected";
							}
				?>
						<option <?php echo $selected; ?> value="<?php echo $project->getProjectId(); ?>"><?php echo $project->getProjectName() ?></option>
				<?php 	}
					} else { ?>
						<option value="0">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td><input type="text" id="txtStartTime[<?php echo $row; ?>]" name="txtStartTime[]" value="<?php echo $timeExpense->getStartTime(); ?>" onfocus="setCurrFocus('txtStartTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" id="txtEndTime[<?php echo $row; ?>]" name="txtEndTime[]" value="<?php echo $timeExpense->getEndTime(); ?>" onfocus="setCurrFocus('txtEndTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" id="txtReportedDate[<?php echo $row; ?>]" name="txtReportedDate[]" value="<?php echo $timeExpense->getReportedDate(); ?>" onfocus="looseCurrFocus();" /></td>
				<td><input type="text" id="txtDuration[<?php echo $row; ?>]" name="txtDuration[]" value="<?php echo round($timeExpense->getDuration()/36)/100; ?>" onfocus="looseCurrFocus();" /></td>
				<td><textarea type="text" id="txtDescription[<?php echo $row; ?>]" name="txtDescription[]" onfocus="looseCurrFocus();" ><?php echo $timeExpense->getDescription(); ?></textarea>
					<input type="hidden" id="txtTimeEventId[<?php echo $row; ?>]" name="txtTimeEventId[]" value="<?php echo $timeExpense->getTimeEventId(); ?>" />
				</td>
				<td class="tableMiddleRight"></td>
			</tr>
		<?php
				$row++;
			}
		}?>
			<tr id="row[<?php echo $row; ?>]">
				<td class="tableMiddleLeft"></td>
				<td ><select id="cmbCustomer[<?php echo $row; ?>]" name="cmbCustomer[]" onfocus="looseCurrFocus();" onchange="$('status').innerHTML='Loading...'; xajax_populateProjects(this.value, <?php echo $row; ?>);" >
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
				<td ><select id="cmbProject[<?php echo $row; ?>]" name="cmbProject[]" onfocus="looseCurrFocus();">
				<?php if (is_array($projects)) { ?>
						<option value="0">- <?php echo $lang_Leave_Common_Select;?> -</option>
				<?php	foreach ($projects as $project) { ?>
						<option value="<?php echo $project->getProjectId(); ?>"><?php echo $project->getProjectName() ?></option>
				<?php 	}
					} else { ?>
						<option value="0">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td><input type="text" id="txtStartTime[<?php echo $row; ?>]" name="txtStartTime[]" onfocus="setCurrFocus('txtStartTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" id="txtEndTime[<?php echo $row; ?>]" name="txtEndTime[]" onfocus="setCurrFocus('txtEndTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" id="txtReportedDate[<?php echo $row; ?>]" name="txtReportedDate[]" value="<?php echo date('Y-m-d'); ?>" onfocus="looseCurrFocus();" /></td>
				<td><input type="text" id="txtDuration[<?php echo $row; ?>]" name="txtDuration[]" onfocus="looseCurrFocus();" /></td>
				<td><textarea type="text" id="txtDescription[<?php echo $row; ?>]" name="txtDescription[]" onfocus="looseCurrFocus();" ></textarea></td>
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

<input type="hidden" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input type="hidden" name="txtEmployeeId" value="<?php echo $timesheet->getEmployeeId(); ?>" />

<input type="button" name="btnUpdate" id="btnUpdate" height="20" width="65" value="Update" onclick="actionUpdate();"/>
<input type="reset" name="btnReset" id="btnReset" height="20" width="65" value="Reset"/>
<input type="button" name="btnInsert" id="btnInsert" height="20" width="65" value="Insert Time" onclick="actionInsertTime();"/>
</form>
</p>
<script type="text/javascript">
	totRows = <?php echo $row; ?>;
	currFocus = $("cmbCustomer[<?php echo $row; ?>]");
	currFocus.focus();
</script>
