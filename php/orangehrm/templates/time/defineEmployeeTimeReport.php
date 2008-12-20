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
	startDate = strToDate($("txtFromDate").value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate($("txtToDate").value, YAHOO.OrangeHRM.calendar.format);

	errFlag=false;
	errors = new Array();

	if (($('txtRepEmpID').value == "") || ($('txtRepEmpID').value == -1)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_EmployeeNotSpecified; ?>";
		errFlag=true;
	}

	if (-1 > $("cmbActivity").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ActivityNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if (-1 > $("cmbProject").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if (!startDate || !endDate || (startDate > endDate)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified; ?>";
		errFlag=true;
	}

	if (errFlag) {
		errStr="<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr+=" - "+errors[i]+"\n";
		}
		alert(errStr);

		return false;
	}

	return true;
}


YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener($("frmEmp"), "submit", viewEmployeeTimeReport);

</script>
<?php $objAjax->printJavascript(); ?>
<div id="status"></div>
<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Time_EmployeeTimeReportTitle;?></h2></div>
    
    <?php if (isset($_GET['message'])) {    
            $message =  $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = 'lang_Time_Errors_' . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>  
    <?php } ?>
<form name="frmEmp" id="frmEmp" method="post" action="?timecode=Time&action=" onsubmit="viewEmployeeTimeReport(); return false;">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
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
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value);">
				<?php if (is_array($projects)) { ?>
						<option value="-1"><?php echo $lang_Time_Common_All;?></option>
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
						<option value="-2">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
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
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Common_FromDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtFromDate" name="txtFromDate" value="" size="10"/>
				<input type="button" id="btnFromDate" name="btnFromDate" value="  " class="calendarBtn"
                    style="display:inline;margin:0;float:none;"/>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Common_ToDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtToDate" name="txtToDate" value="" size="10"/>
				<input type="button" id="btnToDate" name="btnToDate" value="  " class="calendarBtn" 
                    style="display:inline;margin:0;float:none;"/>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ></td>
			<td ></td>
			<td >
			</td>
			<td></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
  	</tfoot>
</table>
<div class="formbuttons">                
    <input type="submit" class="viewbutton" id="viewBtn" 
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
        value="<?php echo $lang_Common_View;?>" />                                  
</div>
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
