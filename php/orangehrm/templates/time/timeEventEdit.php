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
$customers=$records[1];

$customerObj = new Customer();
$projectObj = new Projects();
$projectActivityObj = new ProjectActivity();

?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">

function selectDate() {
	YAHOO.OrangeHRM.calendar.pop('txtReportedDate', 'cal1Container', 'yyyy-MM-dd');
}

function insertTime() {
	this.value=formatDate(new Date(), "yyyy-MM-dd HH:mm");
}

function calculateDuration() {
	startTime = strToTime($("txtStartTime").value);
	endTime = strToTime($("txtEndTime").value);

	if (startTime && endTime) {
		$("txtDuration").value = (endTime-startTime)/3600000;
		$("txtDuration").readonly = "readonly";
	} else {
		$("txtDuration").readonly = "";
	}
}

function init() {
	YAHOO.util.Event.addListener($("btnReportedDateSelect"), "click", selectDate);
	YAHOO.util.Event.addListener($("btnStartTimeInsert"), "click", insertTime, $("txtStartTime"), true);
	YAHOO.util.Event.addListener($("btnEndTimeInsert"), "click", insertTime, $("txtEndTime"), true);

	YAHOO.util.Event.addListener($("txtStartTime"), "focus", calculateDuration);
	YAHOO.util.Event.addListener($("txtStartTime"), "change", calculateDuration);
	YAHOO.util.Event.addListener($("txtEndTime"), "focus", calculateDuration);
	YAHOO.util.Event.addListener($("txtEndTime"), "change", calculateDuration);
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
<form id="frmTimeEvent" name="frmTimesheet" method="post" action="?timecode=Time&action=">
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
			<td >&nbsp;</td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value);" >
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
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Activity; ?></td>
			<td >&nbsp;</td>
			<td >
				<select id="cmbActivity" name="cmbActivity" >
					<option value="-1">- <?php echo $lang_Time_Timesheet_SelectProject; ?> -</option>
				</select>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_StartTime; ?></td>
			<td >&nbsp;</td>
			<td >
				<input type="text" id="txtStartTime" name="txtStartTime" size="16" />
				<input type="button" id="btnStartTimeInsert" name="btnStartTimeInsert" value="<?php echo $lang_Time_InsertTime;?>"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_EndTime; ?></td>
			<td >&nbsp;</td>
			<td >
				<input type="text" id="txtEndTime" name="txtEndTime" size="16" />
				<input type="button" id="btnEndTimeInsert" name="btnEndTimeInsert" value="<?php echo $lang_Time_InsertTime;?>"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_DateReportedFor; ?></td>
			<td >&nbsp;</td>
			<td >
				<input type="text" id="txtReportedDate" name="txtReportedDate" value="<?php echo date('Y-m-d'); ?>" size="10"/>
				<input type="button" id="btnReportedDateSelect" name="btnReportedDateSelect" value="..."/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Duration; ?></td>
			<td >&nbsp;</td>
			<td >
				<input type="text" id="txtDuration" name="txtDuration" size="3" />
				<span class="formHelp"><?php echo $lang_Time_DurationFormat; ?>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Decription; ?></td>
			<td >&nbsp;</td>
			<td >
				<textarea type="text" id="txtDescription" name="txtDescription" >
				</textarea>
			</td>
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
<input type="submit" name="btnSubmit" value="Submit"/>
</form>
<div id="cal1Container" style="position:absolute;" ></div>
