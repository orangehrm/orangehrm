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

function populateActivities($projectId) {

	ob_clean();

	require ROOT_PATH . '/language/default/lang_default_full.php';

	$timeController = new TimeController();
	$projectActivities = $timeController->fetchProjectActivities($projectId);

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$element="cmbActivity";

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

$projects=$records[0];

$projectId = null;
$activityId = null;
$startTime = "";
$endTime = "";
$reportedDate = date('Y-m-d');
$duration = "";
$description = "";

if (isset($records[1])) {
	$timeEventId = $records[1]->getTimeEventId();
	$projectId = $records[1]->getProjectId();
	$activityId = $records[1]->getActivityId();
	$startTime = $records[1]->getStartTime();
	$endTime = $records[1]->getEndTime();
	$reportedDate = $records[1]->getReportedDate();
	$duration = $records[1]->getDuration();
	$description = $records[1]->getDescription();
}

$customerObj = new Customer();
$projectObj = new Projects();
$projectActivityObj = new ProjectActivity();

?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";
var dateTimeFormat = YAHOO.OrangeHRM.calendar.format + " " + YAHOO.OrangeHRM.time.format;

function goBack() {
	window.location = initialAction+"Time_Event_Home";
}

function validate() {
	startTime = strToTime($("txtStartTime").value, dateTimeFormat);
	endTime = strToTime($("txtEndTime").value, dateTimeFormat);
	duration = $("txtDuration").value;

	errFlag=false;
	errors = new Array();

	if ($("cmbActivity").value == "-1") {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ActivityNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if ($("cmbProject").value == "-1") {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified_ERROR; ?>";
		errFlag=true;
	}



	if (startTime && (($("txtEndTime").value != "") || ($("txtDuration").value != ""))) {
		if (!startTime || !endTime || (startTime > endTime) || (0 >= duration)) {
			errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR; ?>";
			errFlag=true;
		}

	} else if (!startTime) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR; ?>";
		errFlag=true;
	}

	if ($("txtReportedDate").value == "") {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ReportedDateNotSpecified_ERROR; ?>";
		errFlag=true;
	} else {
		repDate = strToDate($("txtReportedDate").value, YAHOO.OrangeHRM.calendar.format);
		if (!repDate) {
			errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidReportedDate_ERROR; ?>";
			errFlag=true;
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

function submitTimeEvent() {
	action = "Time_Event_Save";

	if (validate()) {

		$('frmTimeEvent').action = initialAction+action;
		$('frmTimeEvent').submit();
	}

	return false;
}

function insertTime() {
	this.value=formatDate(new Date(), dateTimeFormat);
	calculateDuration();
}

function calculateDuration() {
	startTime = strToTime($("txtStartTime").value, dateTimeFormat);
	endTime = strToTime($("txtEndTime").value, dateTimeFormat);

	if (startTime && endTime) {

		$("txtDuration").value = Math.round((endTime-startTime)/36000)/100;
		//$("txtDuration").readOnly = "readonly";
	} else {
		$("txtDuration").readOnly = "";

		if (startTime && endTime && (startTime > endTime)) {
			$("txtDuration").value = "";
		}
	}
}

function calculateEndDate() {

	startTime = strToTime($("txtStartTime").value, dateTimeFormat);
	endTime = strToTime($("txtEndTime").value, dateTimeFormat);
	duration = $("txtDuration").value;

	if (startTime   && (duration > 0)) {
		endTime = new Date();
		endTime.setTime(startTime+(3600000*duration));

		$("txtEndTime").value = formatDate(endTime, dateTimeFormat);
		//$("txtDuration").readOnly = "readonly";
	} else {
		$("txtDuration").readOnly = "";
	}
}

function init() {
	YAHOO.util.Event.addListener($("btnStartTimeInsert"), "click", insertTime, $("txtStartTime"), true);
	YAHOO.util.Event.addListener($("btnEndTimeInsert"), "click", insertTime, $("txtEndTime"), true);

	YAHOO.util.Event.addListener($("txtStartTime"), "focus", calculateDuration);
	YAHOO.util.Event.addListener($("txtStartTime"), "change", calculateDuration);
	YAHOO.util.Event.addListener($("txtEndTime"), "focus", calculateDuration);
	YAHOO.util.Event.addListener($("txtEndTime"), "change", calculateDuration);

	YAHOO.util.Event.addListener($("txtDuration"), "change", calculateEndDate);
	//YAHOO.util.Event.addListener($("txtDuration"), "focus", calculateEndDate);
	YAHOO.util.Event.addListener($("txtDuration"), "blur", calculateEndDate);
}

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener(window, "load", init);

</script>
<?php $objAjax->printJavascript(); ?>
<h2>
<?php echo $lang_Time_SubmitTimeEventTitle; ?>
<hr/>
</h2>
<div id="status"></div>
<p class="navigation">
  	  <input type="image" title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack(); return false;">
</p>
<form id="frmTimeEvent" name="frmTimesheet" method="post" action="?timecode=Time&action=" onsubmit="submitTimeEvent(); return false;">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value);" >
				<?php if (is_array($projects)) { ?>
						<option value="-1">- <?php echo $lang_Leave_Common_Select;?> -</option>
				<?php	foreach ($projects as $project) {
							$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);

							$selected = "";
							if (isset($projectId) && ($projectId == $project->getProjectId())) {
								$selected = "selected";
							}
				?>
						<option value="<?php echo $project->getProjectId(); ?>" <?php echo $selected; ?> ><?php echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}"; ?></option>
				<?php 	}
					} else { ?>
						<option value="-1">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
				</select>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Activity; ?></td>
			<td ></td>
			<td >
				<select id="cmbActivity" name="cmbActivity" >
					<?php
						if (isset($projectId)) {
							$projectActivities = $projectActivityObj->getActivityList($projectId);
					?>
					<option value="-1">- <?php echo $lang_Leave_Common_Select;?> -</option>
					<?php	foreach ($projectActivities as $projectActivity) {
								$selected="";
								if ($activityId == $projectActivity->getId()) {
									$selected="selected";
								}
					?>
					<option <?php echo $selected; ?> value="<?php echo $projectActivity->getId(); ?>"><?php echo $projectActivity->getName(); ?></option>
					<?php
							}
						} else {
					?>
					<option value="-1">- <?php echo $lang_Time_Timesheet_SelectProject; ?> -</option>
					<?php } ?>
				</select>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_StartTime; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtStartTime" name="txtStartTime" size="16" value="<?php echo LocaleUtil::getInstance()->formatDateTime($startTime); ?>" />
				<input src="../../themes/beyondT/icons/insertTime.gif"
					onmouseover="this.src='../../themes/beyondT/icons/insertTime_o.gif';"
					onmouseout="this.src='../../themes/beyondT/icons/insertTime.gif';"
					onclick="return false;"
					name="btnStartTimeInsert" id="btnStartTimeInsert"
					height="20" width="90" type="image" alt="Insert Time" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_EndTime; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtEndTime" name="txtEndTime" size="16" value="<?php echo LocaleUtil::getInstance()->formatDateTime($endTime); ?>" />
				<input src="../../themes/beyondT/icons/insertTime.gif"
					onmouseover="this.src='../../themes/beyondT/icons/insertTime_o.gif';"
					onmouseout="this.src='../../themes/beyondT/icons/insertTime.gif';"
					onclick="return false;"
					name="btnEndTimeInsert" id="btnEndTimeInsert"
					height="20" width="90" type="image" alt="Insert Time" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_DateReportedFor; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtReportedDate" name="txtReportedDate" value="<?php echo LocaleUtil::getInstance()->formatDate($reportedDate); ?>" size="10"/>
				<input type="button" id="btnReportedDateSelect" name="btnReportedDateSelect" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Duration; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtDuration" name="txtDuration" size="3" value="<?php echo $duration; ?>" />
				<span class="formHelp"><?php echo $lang_Time_DurationFormat; ?></span>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Decription; ?></td>
			<td ></td>
			<td >
				<textarea type="text" id="txtDescription" name="txtDescription" ><?php echo $description; ?></textarea>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ></td>
			<td ></td>
			<td >
				<input src="../../themes/beyondT/icons/submit.gif"
					onclick="submitTimeEvent(); return false;"
					onmouseover="this.src='../../themes/beyondT/icons/submit_o.gif';"
					onmouseout="this.src='../../themes/beyondT/icons/submit.gif';"
					alt="Submit" name="btnSubmit" id="btnSubmit"
					height="20" type="image" width="65"/></td>
			<td class="tableMiddleRight"></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>

<?php if (isset($timeEventId)) { ?>
<input type="hidden" name="txtTimeEventId" id="txtTimeEventId" value="<?php echo $timeEventId; ?>"/>
<?php } ?>
</form>
<div id="cal1Container" style="position:absolute;" ></div>
