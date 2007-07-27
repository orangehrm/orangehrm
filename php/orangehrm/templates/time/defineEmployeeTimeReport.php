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

function populateActivities($projectId) {

	ob_clean();

	require ROOT_PATH . '/language/default/lang_default_full.php';

	$timeController = new TimeController();
	$projectActivities = $timeController->fetchProjectActivities($projectId);

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$element="cmbActivity";

	$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projectActivities, 0,'frmTimesheet',$element, 1);

	$objResponse->addScript('document.getElementById("'.$element.'").focus();');

	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateActivities');
$objAjax->processRequests();

$role=$records[0];
$employees=$records[1];
$projects=$records[2];

$customerObj = new Customer();
$projectObj = new Projects();
$projectActivityObj = new ProjectActivity();

?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";

function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
}

function viewEmployeeTimeReport() {
	action = "Employee_Report";

	if (validate()) {
		$('frmEmp').action = initialAction+action;
		$('frmEmp').submit();
	}

	return false;
}

function validate() {
	startTime = strToTime($("txtFromDate").value+" 00:00");
	endTime = strToTime($("txtToDate").value+" 23:59");

	errFlag=false;
	errors = new Array();

	if (($('txtRepEmpID').value == "") || ($('txtRepEmpID').value == -1)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_EmployeeNotSpecified; ?>";
		errFlag=true;
	}

	if (-1 > $("cmbActivity").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ActivityNotSpecified; ?>";
		errFlag=true;
	}

	if (-1 > $("cmbProject").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified; ?>";
		errFlag=true;
	}

	if (!startTime || !endTime || (startTime > endTime)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified; ?>";
		errFlag=true;
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

function selectDate() {
	YAHOO.OrangeHRM.calendar.pop(this.id, 'cal1Container', 'yyyy-MM-dd');
}

function init() {
	YAHOO.util.Event.addListener($("btnFromDate"), "click", selectDate, $("txtFromDate"), true);
	YAHOO.util.Event.addListener($("btnToDate"), "click", selectDate, $("txtToDate"), true);
}

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener(window, "load", init);
YAHOO.util.Event.addListener($("frmEmp"), "submit", viewEmployeeTimeReport);

</script>
<?php $objAjax->printJavascript(); ?>
<h2>
<?php echo $lang_Time_EmployeeTimeReportTitle; ?>
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
<form name="frmEmp" id="frmEmp" method="post" action="?timecode=Time&action=" onsubmit="viewEmployeeTimeReport(); return false;">
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
			<td ><?php echo $lang_Leave_Common_EmployeeName; ?></td>
			<td></td>
		<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
			<td ><input type="text" name="cmbRepEmpID" id="cmbRepEmpID" disabled />
				<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" />
				<input type="button" value="..." onclick="returnEmpDetail();" />
			</td>
		<?php } else if ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) { ?>
			<td >
				<select name="txtRepEmpID" id="txtRepEmpID">
					<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
					<?php if (is_array($employees)) {
		   					foreach ($employees as $employee) {
		  			?>
		 		  	<option value="<?php echo $employee[0] ?>"><?php echo $employee[1]; ?></option>
		  			<?php 	}
		   				} ?>
				</select>
			</td>
		<?php } ?>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value);">
				<?php if (is_array($projects)) { ?>
						<option value="-1"><?php echo $lang_Time_Common_All;?></option>
				<?php	foreach ($projects as $project) {
							$customerDet = $customerObj->fetchCustomer($project->getCustomerId());

							$selected = "";
							if (isset($projectId) && ($projectId == $project->getProjectId())) {
								$selected = "selected";
							}
				?>
						<option value="<?php echo $project->getProjectId(); ?>" <?php echo $selected; ?> ><?php echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}"; ?></option>
				<?php 	}
					} else { ?>
						<option value="-2">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
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
				<?php
					 $disabled="";
					 if (!is_array($projects)) {
						$disabled="disabled";
					 }
				?>
				<select id="cmbActivity" name="cmbActivity" <?php echo $disabled; ?>>
					<option value="-1"><?php echo $lang_Time_Common_All; ?></option>
				</select>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Common_FromDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtFromDate" name="txtFromDate" value="" size="10"/>
				<input type="button" id="btnFromDate" name="btnFromDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Common_ToDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtToDate" name="txtToDate" value="" size="10"/>
				<input type="button" id="btnToDate" name="btnToDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ></td>
			<td ></td>
			<td >
				<input type="image" name="btnView" alt="View"
					   src="../../themes/beyondT/icons/view.jpg"
					   onmouseover="this.src='../../themes/beyondT/icons/view_o.jpg';"
					   onmouseout="this.src='../../themes/beyondT/icons/view.jpg';" />
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
<div id="cal1Container" style="position:absolute;" ></div>