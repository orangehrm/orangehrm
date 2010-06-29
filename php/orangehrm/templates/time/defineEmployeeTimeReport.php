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

function getDeletedProjects($val){

    $timeController = new TimeController();
    $objResponse = new xajaxResponse();
    $xajaxFiller = new xajaxElementFiller();

     $element="cmbProject";

    if ($val==1) {
        $projectList=$timeController->fetchIncludingDeletedProjects(1);
        $Response = $xajaxFiller->cmbFillerById($objResponse,$projectList, 0,'frmReport',$element, 0);
    } else {
        $projectList=$timeController->fetchIncludingDeletedProjects(0);
        $Response = $xajaxFiller->cmbFillerById($objResponse,$projectList, 0,'frmReport',$element, 0);
    }

    return $objResponse->getXML();

}

$objAjax = new xajax();
$objAjax->registerFunction('populateActivities');
$objAjax->registerFunction('getDeletedProjects');
$objAjax->processRequests();

$role=$records[0];
$employees=$records[1];
$projects=$records[2];

$customerObj = new Customer();
$projectObj = new Projects();
$projectActivityObj = new ProjectActivity();

//create a two-dimensional array to sort the project's dropdown list
if(isset($projects)) {
	foreach($projects as $project) {
		
		$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
		
		$projectAndCustomers = array();
		
		$projectAndCustomers['concat'] = $customerDet->getCustomerName()." - ".$project->getProjectName();
		$projectAndCustomers['project'] = $project;
		$projectAndCustomers['customer'] = $customerDet;
		
		$arrayProjectAndCustomers[] = $projectAndCustomers;
	}
	
	//sort the array by customer name - project name
	usort($arrayProjectAndCustomers, "compareConcatenatedName");
}

function compareConcatenatedName($a, $b){
    return strcmp($a["concat"], $b["concat"]);
}

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>

<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
//<![CDATA[
var initialAction = "?timecode=Time&action=";

function getProjectlist(check){
    if (check) {
        xajax_getDeletedProjects(1);
    } else {
        xajax_getDeletedProjects(0);

    }

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
	_matchAutoCompletionFields();

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

function formatAutoCompleteField(obj) {
	if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
		obj.value = '';
		obj.style.color = '#000000';
	}
}

function _matchAutoCompletionFields() {
	employeeName = $('cmbRepEmpID').value;

	for (i = 0; i < employees.length; i++) {
		if (employees[i] == employeeName) {
			$('txtRepEmpID').value = ids[i];
			return true;
		}
	}
	return false;
}

employees = new Array();
ids = new Array();
<?php
$employees = $records['empList'];
for ($i=0;$i<count($employees);$i++) {
	echo "employees[" . $i . "] = '" . CommonFunctions::escapeForJavascript($employees[$i][1] . " " . $employees[$i][2]) . "';\n";
	echo "ids[" . $i . "] = \"" . $employees[$i][0] . "\";\n";
}
?>

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener($("frmEmp"), "submit", viewEmployeeTimeReport);
//]]>
</script>
<?php $objAjax->printJavascript(); ?>
<style type="text/css">
#employeeSearchAC {
    width:20em; /* set width here */
    padding-bottom:2em;
    position:relative;
    top:-10px
}

#employeeSearchAC {
    z-index:9000; /* z-index needed on top instance for ie & sf absolute inside relative issue */
    float:left;
    margin-right:5px;
}

#cmbRepEmpID {
    _position:absolute; /* abs pos needed for ie quirks */
}
</style>
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
<form name="frmEmp" id="frmEmp" method="post" action="?timecode=Time&amp;action=" onsubmit="viewEmployeeTimeReport(); return false;">
   <input type="hidden" name="token" value="<?php echo $token;?>" />
<table border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td></td>
			<td ><?php echo $lang_Leave_Common_EmployeeName; ?></td>
			<td></td>
			<td>
				<div class="yui-skin-sam" style="float:left;margin-right:10px;">
		            <div id="employeeSearchAC" style="width:150px;">
						<input type="text" name="cmbRepEmpID" id="cmbRepEmpID" style="margin:0px 0px 2px 0px; color:#999999" autocomplete="off"
							value="<?php echo $lang_Common_TypeHereForHints; ?>" onfocus="formatAutoCompleteField(this)" />
						<div id="employeeSearchACContainer" style="margin:0px 0px 0px 0px;"></div>
					</div>
				</div>
				<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" />
			</td>

			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value);">
				<?php if (isset($arrayProjectAndCustomers) && is_array($arrayProjectAndCustomers)) { ?>
						<option value="-1"><?php echo $lang_Time_Common_All;?></option>
				<?php	   for($a = 0;$a <count($arrayProjectAndCustomers); $a++) {
							$objProject = $arrayProjectAndCustomers[$a]['project'];
					
							$selected = "";
							if (isset($projectId) && ($projectId == $objProject->getProjectId())) {
								$selected = "selected";
							}
				?>
						<option value="<?php echo $objProject->getProjectId(); ?>" <?php echo $selected; ?> ><?php echo $arrayProjectAndCustomers[$a]['concat']; ?></option>
				<?php 	}
					} else { ?>
						<option value="-2">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
				</select>
			</td>
			<td><label><input type="checkbox" id="cbxDeleted" name="cbxDeleted" onClick="getProjectlist(this.checked)"> Show Deleted</label></td>
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
</table>
<div class="formbuttons">
    <input type="submit" class="viewbutton" id="viewBtn"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_View;?>" />
</div>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }

    YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
	   	// Instantiate first JS Array DataSource
	   	this.oACDS = new YAHOO.widget.DS_JSArray(employees);

	   	// Instantiate AutoComplete for cmbRepEmpID
	   	this.oAutoComp = new YAHOO.widget.AutoComplete('cmbRepEmpID','employeeSearchACContainer', this.oACDS);
	   	this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
	   	this.oAutoComp.typeAhead = false;
	   	this.oAutoComp.useShadow = true;
	   	this.oAutoComp.minQueryLength = 1;
	   	this.oAutoComp.textboxFocusEvent.subscribe(function(){
	   	    var sInputValue = YAHOO.util.Dom.get('cmbRepEmpID').value;
	   	    if(sInputValue.length === 0) {
	   	        var oSelf = this;
	   	        setTimeout(function(){oSelf.sendQuery(sInputValue);},0);
	   	    }
	   	});
	}
//]]>
</script>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
