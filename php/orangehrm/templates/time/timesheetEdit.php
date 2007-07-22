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

require_once ROOT_PATH . '/lib/controllers/TimeController.php';

function populateActivities($projectId, $row) {

	ob_clean();

	require ROOT_PATH . '/language/default/lang_default_full.php';

	$timeController = new TimeController();
	$projectActivities = $timeController->fetchProjectActivities($projectId);

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$element="cmbActivity[$row]";

	if (count($projectActivities) == 0) {
		$projectActivities[0][0] = -1;
		$projectActivities[0][1] = "- $lang_Time_Timesheet_SelectProject -";

		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projectActivities, 0,'frmTimesheet',$element, 0);
	} else {
		$objResponse->addScript("document.getElementById('".$element."').options.length = 0;");
	 	$objResponse->addScript("document.getElementById('".$element."').options[0] = new Option('- $lang_Common_Select -','-1');");
		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projectActivities, 0,'frmTimesheet',$element, 1);
	}

	$objResponse->addScript('document.getElementById("'.$element.'").focus();');

	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateActivities');
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
<script type="text/javascript">
<!--
currFocus = null;
totRows = 0;

var initialAction = "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&id=<?php echo $timesheet->getTimesheetId(); ?>&action=";

function $(id) {
	return document.getElementById(id);
}

function actionSubmit() {
	$("frmTimesheet").action= initialAction+"Submit_Timesheet";

	$("frmTimesheet").submit();
}

function looseCurrFocus(row) {
	currFocus = null;
}

function setCurrFocus(label, row) {
	currFocus = $(label+"["+row+"]");
}

function actionInsertTime() {
	if (!currFocus) {
		currFocus = $("txtStartTime["+totRows+"]");
	}
	if (currFocus.value == "") {
    	currFocus.value = formatDate(new Date(), "yyyy-MM-dd HH:mm");
  	}
  	currFocus.focus();
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

function validateDuration(row) {
	obj = $("txtDuration["+row+"]");

	if (!obj || (obj.value == '') || (obj.value == 0)) {
		return false;
	}

	regExp = /^[0-9]+\.*[0-9]*/;

	if (!regExp.test(obj.value)) {
		return false;
	}

	return true;
}

function validate() {
	errors = new Array();
	err = new Array();
	errFlag = false;
	for (i=0; i<=totRows; i++) {
		if (!allEmpty(i)) {
			err[i]=false;

			obj = $("txtDuration["+i+"]");

			startTime = strToTime($("txtStartTime["+i+"]").value);
			endTime = strToTime($("txtEndTime["+i+"]").value);
			if (validateInterval(i) && validateDuration(i) && (obj.value != duration(i)) && endTime) {
				obj.value = duration(i);
			} else if (validateDuration(i) && startTime && !endTime) {
				endTime = new Date();
				endTime.setTime(startTime+(3600000*obj.value));
				$("txtEndTime["+i+"]").value = formatDate(endTime, "yyyy-MM-dd HH:mm");
			}

			if (!validateDuration(i)) {
				if (!validateInterval(i)) {
					errors[2] = "<?php echo $lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified; ?>";
					err[i]=true;
					errFlag=true;
				}
			}

			if ($("cmbActivity["+i+"]").value == "-1") {
				errors[3] = "<?php echo $lang_Time_Errors_ActivityNotSpecified; ?>";
				err[i]=true;
				errFlag=true;
			}

			if ($("cmbProject["+i+"]").value == "-1") {
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

	if ($("cmbActivity["+row+"]").value != "-1") {
		unUsed=false;
	}

	if ($("cmbProject["+row+"]").value != "-1") {
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

	$('frmTimesheet').action= initialAction+'Edit_Timesheet';
	$('frmTimesheet').submit();
}

function actionReset() {
	$('frmTimesheet').reset();
}

function deleteTimeEvents() {
	$check = 0;
	with (document.frmTimesheet) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'deleteEvent[]')){
				$check = 1;
			}
		}
	}

	if ($check == 1){
		var res = confirm("<?php echo $lang_Common_ConfirmDelete?>");

		if(!res) return;

		$('frmTimesheet').action= initialAction+'Delete_Timesheet';
		$('frmTimesheet').submit();
	}else{
		alert("<?php echo $lang_Common_SelectDelete; ?>");
	}
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
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
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
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th class="tableMiddleMiddle"></th>
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
				$projectActivities = $projectActivityObj->getActivityList($projectId);
			?>
			<tr id="row[<?php echo $row; ?>]">
				<td class="tableMiddleLeft"></td>
				<td ><input type="checkbox" id="deleteEvent[]" name="deleteEvent[]" value="<?php echo $timeExpense->getTimeEventId(); ?>" /></td>
				<td ><select id="cmbProject[<?php echo $row; ?>]" name="cmbProject[]" onfocus="looseCurrFocus();" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value, <?php echo $row; ?>);">
				<?php if (is_array($projects)) { ?>
						<option value="-1">--<?php echo $lang_Leave_Common_Select;?>--</option>
				<?php	foreach ($projects as $project) {
							$selected="";
							$customerDet = $customerObj->fetchCustomer($project->getCustomerId());
							if ($projectId == $project->getProjectId()) {
								$selected="selected";
							}
				?>
						<option <?php echo $selected; ?> value="<?php echo $project->getProjectId(); ?>"><?php echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}"; ?></option>
				<?php 	}
					} else { ?>
						<option value="-1">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td ><select id="cmbActivity[<?php echo $row; ?>]" name="cmbActivity[]" onfocus="looseCurrFocus();">
				<?php if (is_array($projectActivities)) { ?>
						<option value="-1">--<?php echo $lang_Leave_Common_Select;?>--</option>
				<?php	foreach ($projectActivities as $projectActivity) {
							$selected="";
							if ($timeExpense->getActivityId() == $projectActivity->getId()) {
								$selected="selected";
							}
				?>
						<option <?php echo $selected; ?> value="<?php echo $projectActivity->getId(); ?>"><?php echo $projectActivity->getName(); ?></option>
				<?php 	}
					} else { ?>
						<option value="-1">- <?php echo $lang_Time_Timesheet_NoCustomers;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td><input type="text" <?php echo ($timeExpense->getStartTime() == null)?'readonly="readonly"':''; ?> id="txtStartTime[<?php echo $row; ?>]" name="txtStartTime[]" value="<?php echo $timeExpense->getStartTime(); ?>" onfocus="setCurrFocus('txtStartTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" <?php echo ($timeExpense->getStartTime() == null)?'readonly="readonly"':''; ?> id="txtEndTime[<?php echo $row; ?>]" name="txtEndTime[]" value="<?php echo $timeExpense->getEndTime(); ?>" onfocus="setCurrFocus('txtEndTime', <?php echo $row; ?>);" /></td>
				<td><input type="text" id="txtReportedDate[<?php echo $row; ?>]" name="txtReportedDate[]" value="<?php echo $timeExpense->getReportedDate(); ?>" onfocus="looseCurrFocus();" /></td>
				<td><input type="text" <?php echo ($timeExpense->getStartTime() == null)?'':'readonly="readonly"'; ?> id="txtDuration[<?php echo $row; ?>]" name="txtDuration[]" value="<?php echo round($timeExpense->getDuration()/36)/100; ?>" onfocus="looseCurrFocus();" /></td>
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
				<td ><input type="checkbox" id="deleteEvent[]" name="deleteEvent[]" disabled="disabled" /></td>
				<td ><select id="cmbProject[<?php echo $row; ?>]" name="cmbProject[]" onfocus="looseCurrFocus();"  onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value, <?php echo $row; ?>);" >
				<?php if (is_array($projects)) { ?>
						<option value="-1">--<?php echo $lang_Leave_Common_Select;?>--</option>
				<?php	foreach ($projects as $project) {
							$customerDet = $customerObj->fetchCustomer($project->getCustomerId());
				?>
						<option value="<?php echo $project->getProjectId(); ?>"><?php echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}"; ?></option>
				<?php 	}
					} else { ?>
						<option value="-1">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
					</select>
				</td>
				<td ><select id="cmbActivity[<?php echo $row; ?>]" name="cmbActivity[]" onfocus="looseCurrFocus();">
						<option value="-1">- <?php echo $lang_Time_Timesheet_SelectProject; ?> -</option>
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
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
<p id="controls">

<input type="hidden" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input type="hidden" name="txtEmployeeId" value="<?php echo $timesheet->getEmployeeId(); ?>" />

<input src="../../themes/beyondT/icons/update.png"
		onmouseover="this.src='../../themes/beyondT/icons/update_o.png';"
		onmouseout="this.src='../../themes/beyondT/icons/update.png';"
		onclick="actionUpdate(); return false;"
		name="btnUpdate" id="btnUpdate"
		height="20" width="65"  type="image" alt="Update" />
<input src="../../themes/beyondT/icons/reset.gif"
		onmouseover="this.src='../../themes/beyondT/icons/reset_o.gif';"
		onmouseout="this.src='../../themes/beyondT/icons/reset.gif';"
		onclick="actionReset(); return false;"
		name="btnReset" id="btnReset"
		height="20" width="65" type="image" alt="Reset"/>
<input src="../../themes/beyondT/icons/insertTime.png"
		onmouseover="this.src='../../themes/beyondT/icons/insertTime_o.png';"
		onmouseout="this.src='../../themes/beyondT/icons/insertTime.png';"
		onclick="actionInsertTime(); return false;"
		name="btnInsert" id="btnInsert"
		height="20" width="90" type="image" alt="Insert Time" />
<input src="../../themes/beyondT/pictures/btn_delete.jpg"
		onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';"
		onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';"
		onclick="deleteTimeEvents(); return false;"
		name="btnDelete" id="btnDelete"
		type="image" alt="Delete" />
</form>
</p>
<script type="text/javascript">
	totRows = <?php echo $row; ?>;
	currFocus = $("cmbProject[<?php echo $row; ?>]");
	currFocus.focus();
</script>
