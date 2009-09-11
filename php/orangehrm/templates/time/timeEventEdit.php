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

function populateActivities($projectId, $activityId=-1) {

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
		if ($activityId != -1) {
			$projectActivityObject = new ProjectActivity();
		    if ($projectId == $projectActivityObject->retrieveActivityProjectId($activityId)) {
				$activityExists = false;
				$i = 0;
				foreach ($projectActivities as $activity) {
					if ($activity[$i][0] == $activityId) {
					    $activityExists = true;
					}
					$i++;
				}

				if (!$activityExists) {
					$count = count($projectActivities);
					$projectActivities[$count][0] = $activityId;
					$projectActivities[$count][1] = $projectActivityObject->retrieveActivityName($activityId);
				}
		    }

		}
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

/* For getting current timesheet's start date and end date: Begins */

$currentTimesheet = $records[2];

if (isset($currentTimesheet)) {

	$startDate = strtotime($currentTimesheet->getStartDate() . " 00:00:00");
	$endDate = strtotime($currentTimesheet->getEndDate() . " 23:59:59");
	$startDatePrint = LocaleUtil::getInstance()->formatDateTime(date("Y-m-d H:i", $startDate));
	$endDatePrint = LocaleUtil::getInstance()->formatDateTime(date("Y-m-d H:i", $endDate));

} else {

	$startDatePrint = 0;
	$endDatePrint = 0;

}
/* For getting current timesheet's start date and end date: Ends */

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

function $(id) {
	return document.getElementById(id);
}

function validateDuration(value) {

	if (value != "") {
		regExp = /^[0-9]+\.*[0-9]*/;

		if (!regExp.test(value)) {
			return false;
		}
	}
	return true;
}

function isEmpty(value) {
	value = trim(value);
	return (value == "");
}

function validate() {

	duration = $("txtDuration").value;
	reportedDate = $("txtReportedDate").value;

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

	pattern = /^\d+.?\d*$/;

	if (duration == "" || duration < 0 || duration.match(pattern)==null) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDuration_ERROR; ?>";
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

	<?php if (isset($currentTimesheet)) { ?>
	/* Timesheet period validation conditions begins */

	if (!checkDateAndDuration(reportedDate, duration)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_EVENT_OUTSIDE_PERIOD_FAILURE; ?>";
		errFlag=true;
    }

	/* Timesheet period validation conditions ends */
	<?php } ?>

	if (errFlag) {
		errStr="<?php echo $lang_Time_Errors_EncounteredFollowingProblems; ?>\n";
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

/* Timesheet duration validation Function Begins */

function checkDateAndDuration(dateValue, duration) {

	periodStart = strToTime("<?php echo $startDatePrint; ?>", dateTimeFormat);
	periodEnd = strToTime("<?php echo $endDatePrint; ?>", dateTimeFormat);

	// ignore invalid dates and durations since those are checked separately
	if (dateValue && validateDuration(duration)) {

		if ((dateValue < periodStart) || (dateValue > periodEnd)) {
			return false;
		}

		endTime = new Date();
		endTime.setTime(dateValue + (3600000 * duration));

		if ((endTime < periodStart) || (endTime > periodEnd)) {
			return false;
		}
	}

	return true;
}

/* Timesheet duration validation Function Ends */


</script>
<?php $objAjax->printJavascript(); ?>

<div id="status"></div>
<div class="formpage">
    <div class="navigation">
        <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="goBack(); return false;">
            <span><?php echo $lang_Common_Back;?></span>
        </a>
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Time_SubmitTimeEventTitle;?></h2></div>

<form id="frmTimeEvent" name="frmTimesheet" method="post" action="?timecode=Time&action=" onsubmit="submitTimeEvent(); return false;">
<table border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value, <?php echo (isset($activityId))?$activityId:"-1"; ?>);" >
				<?php

					if (!isset($projectId) && !is_array($projects)) {
					    echo "<option value=\"-1\">- {$lang_Time_Timesheet_NoProjects} -</option>";
					} else {
					    echo "<option value=\"-1\">- {$lang_Leave_Common_Select} -</option>";
					}

					if (isset($projectId)) {
					    $projectName = $projectObj->retrieveProjectName($projectId);
						$customerName = $projectObj->retrieveCustomerName($projectId);
						echo "<option selected value=\"{$projectId}\">{$customerName} - {$projectName}</option>";
					}

					if (is_array($projects)) {

						if (isset($projectId)) {
						    foreach ($projects as $project) {
						        if ($projectId != $project->getProjectId()) {
						        	$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
						        	$customerStatus = $customerDet->getCustomerStatus();
						        	if ($customerStatus == 0) {
										echo "<option value=\"{$project->getProjectId()}\">{$customerDet->getCustomerName()} - {$project->getProjectName()}</option>";
						        	}
						        }
						    }
						} else {
						    foreach ($projects as $project) {
					        	$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
					        	$customerStatus = $customerDet->getCustomerStatus();
					        	if ($customerStatus == 0) {
									echo "<option value=\"{$project->getProjectId()}\">{$customerDet->getCustomerName()} - {$project->getProjectName()}</option>";
					        	}
						    }

						}
					}
				?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Activity; ?></td>
			<td ></td>
			<td >
			<select id="cmbActivity" name="cmbActivity" >
			<?php

				if (!isset($activityId) && !isset($projectId)) {
				    echo "<option value=\"-1\">- {$lang_Time_Timesheet_SelectProjectFirst} -</option>";
				} else {
				    echo "<option value=\"-1\">- {$lang_Leave_Common_Select} -</option>";
				}

				if (isset($activityId)) {
					$activityName = $projectActivityObj->retrieveActivityName($activityId);
				    echo "<option selected value=\"{$activityId}\">{$activityName}</option>";
				}

				if (isset($projectId)) {
				    $projectActivities = $projectActivityObj->getActivityList($projectId);
				    if (isset($activityId)) {
				        foreach ($projectActivities as $projectActivity) {
				            if ($activityId != $projectActivity->getId()) {
				                echo "<option value=\"{$projectActivity->getId()}\">{$projectActivity->getName()}</option>";
				            }
				        }
				    } else {
				        foreach ($projectActivities as $projectActivity) {
			                echo "<option value=\"{$projectActivity->getId()}\">{$projectActivity->getName()}</option>";
				        }

				    }
				}

				?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_DateReportedFor; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtReportedDate" name="txtReportedDate" value="<?php echo LocaleUtil::getInstance()->formatDate($reportedDate); ?>" size="10"/>
				<input type="button" id="btnReportedDateSelect" name="btnReportedDateSelect" value="  " class="calendarBtn"
                    style="display:inline;margin:0;float:none;"/>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Duration; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtDuration" name="txtDuration" size="3" value="<?php echo $duration; ?>" />
				<span class="formHelp"><?php echo $lang_Time_DurationFormat; ?></span>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Decription; ?></td>
			<td ></td>
			<td >
				<textarea type="text" id="txtDescription" name="txtDescription" ><?php echo $description; ?></textarea>
			</td>
			<td></td>
		</tr>
	</tbody>
</table>

<?php if (isset($timeEventId)) { ?>
<input type="hidden" name="txtTimeEventId" id="txtTimeEventId" value="<?php echo $timeEventId; ?>"/>
<?php } ?>
    <div class="formbuttons">
        <input type="button" class="submitbutton" name="btnSubmit" id="btnSubmit"
            onclick="submitTimeEvent(); return false;"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Submit;?>" />
    </div>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
