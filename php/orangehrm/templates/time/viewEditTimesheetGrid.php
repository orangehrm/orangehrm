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
require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';
require_once ROOT_PATH . '/lib/controllers/TimeController.php';
$GLOBALS['lang_Common_Select'] = $lang_Common_Select;

function populateActivities($projectId, $row) {

	ob_clean();
	require ROOT_PATH . '/language/default/lang_default_full.php';
	$timeController = new TimeController();
	$projectActivities = $timeController->fetchProjectActivities($projectId);
	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
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

$timeExpenses						=	$records[0];
$timesheet							=	$records[1];
$timesheetSubmissionPeriod			=	$records[2];
$employee							=	$records[3];
$evantCountArray					=	$records[4];
$eventsIdArr						=	$records[5];

if (isset($_SESSION['Previous'])) {
	unset($_SESSION['Previous']);
}
if (isset($_SESSION['EventCount'])) {
	unset($_SESSION['EventCount']);
}
if (isset($_SESSION['EventsIdArr'])) {
	unset($_SESSION['EventsIdArr']);
}
if (isset($_SESSION['PreviousProject'])) {
	unset($_SESSION['PreviousProject']);
}
if (isset($_SESSION['PreviousActivity'])) {
	unset($_SESSION['PreviousActivity']);
}
			
$_SESSION['EventsIdArr'] 	= $eventsIdArr	;
$row = 0;
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
$duration = TimeSheet::ONE_WEEK;

$customerObj = new Customer();
$projectObj = new Projects();
$projectObj->setDeleted(Projects::PROJECT_NOT_DELETED);
$projects = $projectObj->fetchProjects();
$projectActivityObj = new ProjectActivity();
?>
<script type="text/javascript">
<!--
var initialAction = "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=";
var OldProjectValue =  null;
var OldProjectIndex = null ;
var OldActivityIndex = null ;
function $(id) {
	return document.getElementById(id);
}

function setCurrentRowColorDefault(row) {
	$("row["+row+"]").style.background = "#FFFFFF";
}

function goBack() {
	window.location=initialAction+"View_Timesheet&id=<?php echo $timesheet->getTimesheetId(); ?>";
}

function validate(rowCount , cellId) {

	errFlag=false;
	errors = new Array();
	var currentRow = rowCount;
	var cellId = cellId;

	if ($("cmbActivity[" + currentRow + "]").value == "-1") {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ActivityNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if ($("cmbProject[" + currentRow + "]").value == "-1") {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if (errFlag) {
		errStr="<?php echo $lang_Time_Errors_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr +=" - "+errors[i]+"\n";
		}
		$("row["+ currentRow +"]").style.background = "#FFAAAA";
		alert(errStr);
		$("txtDuration[" + currentRow + "][" + cellId + "]").value =  0 ;
		return false;
	}else{
 		return true;
 	}
}
function actionAddRow(){

	var rowCount = $("rowCount").value;
	if (rowCount > 0) {
		var temRowCount = rowCount - 1 ;
		var rowAdd = false ;
 	<?php
	for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
	?>
		if ($("txtDuration[" + temRowCount + "][<?php echo $i ?>]").value > 0 ) {
				rowAdd = true ;
		}
	<?php
 	}
	?>
		if (rowAdd == true) {

			var selectBoxProject =  "<select name=\"cmbProject[" + rowCount + "]\" id=\"cmbProject["+ rowCount +"]\"   onchange=\"$('status').innerHTML='<?php echo $lang_Common_Loading;?>...'; xajax_populateActivities(this.value , " + rowCount  + ");\" onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\">" ;
 			selectBoxProject = selectBoxProject + "<option value=\"-1\" >--<?php echo $lang_Leave_Common_Select;?>--</option>" ;
 			<?php
 			foreach($projects as $project){
				 $customer = $customerObj->fetchCustomer($project->getCustomerId() , true);
			?>
 			selectBoxProject = selectBoxProject +  "<option value=\"<?php echo $project->getProjectId() ?>\"><?php echo "{$customer->getCustomerName()} - {$project->getProjectName()}"; ?></option>"  ;
  			<?php
			}
			?>
			selectBoxProject = selectBoxProject +  "</select>"  ;
			var selectBoxActivity =  "<select name=\"cmbActivity[" + rowCount + "]\" id=\"cmbActivity["+ rowCount +"]\"  onchange=\"checkAddEvent("+ rowCount +")\" onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\">" ;
			selectBoxActivity = selectBoxActivity + "<option value=\"-1\" >--<?php echo $lang_Leave_Common_Select;?>--</option>" ;
			selectBoxActivity = selectBoxActivity +  "</select>"  ;
			var mytable			=	document.getElementById("tbody") ;
			var newtfootrow		=	mytable.insertRow(-1)  ;
			newtfootrow.id		=	"row[" + rowCount + "]";
			var newtfootcell1	=	newtfootrow.insertCell(0) ;
			var newtfootcell2	=	newtfootrow.insertCell(1)  ;
			var newtfootcell3	=	newtfootrow.insertCell(2)  ;
			var newtfootcell4	=	newtfootrow.insertCell(3) ;
			var newtfootcell5	=	newtfootrow.insertCell(4) ;
			var newtfootcell6	=	newtfootrow.insertCell(5) ;
			var newtfootcell7	=	newtfootrow.insertCell(6) ;
			var newtfootcell8	=	newtfootrow.insertCell(7) ;
			var newtfootcell9	=	newtfootrow.insertCell(8) ;
			var newtfootcell10	=	newtfootrow.insertCell(9) ;
			var newtfootcell11	=	newtfootrow.insertCell(10) ;
			newtfootcell1.className 	= "tableMiddleLeft";
			newtfootcell2.innerHTML		=  selectBoxProject;
			newtfootcell3.innerHTML		= selectBoxActivity;
		<?php
		$rowDateStart = 4 ;
		for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
			$nameCell = "newtfootcell" . $rowDateStart  . ".innerHTML";
		?>
		 <?php echo  $nameCell  ?>		="<input name=\"txtDuration[" + rowCount + "][<?php  echo $i ?>]\" type=\"text\" id=\"txtDuration[" + rowCount + "][<?php  echo $i ?>]\"  value = \"0\" size=\"3\" onKeyPress=\"javascript:return limitinput(event, '0123456789', true)\" onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\" onchange=\"return validate(" + rowCount + " , <?php  echo $i ?>)\"/>"  ;
		<?php
		$rowDateStart++;
		}
		?>
			newtfootcell11.className 	= "tableMiddleLeft";
			rowCount++;
			$("rowCount").value = rowCount ;
 		} else {
   			for(row = 0 ; row<rowCount ; row++) {
			
				if(row == (rowCount-1)) {
					$("row["+row+"]").style.background = "#FFAAAA";
 				}else {
					$("row["+row+"]").style.background = "#FFFFFF";
 				}
			
			}
			
			errStr="<?php echo $lang_Time_Errors_EncounteredTheFollowingProblems; ?>\n";
			errStr +="<?php echo $lang_Time_Errors_InvalidDuration_Row_Error; ?>\n";
			alert(errStr) ;
	}
	} else {

		 var selectBoxProject =  "<select name=\"cmbProject[0]\" id=\"cmbProject[0]\"   onchange=\"$('status').innerHTML='<?php echo $lang_Common_Loading;?>...'; xajax_populateActivities(this.value , " + 0  + ")\"  onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\">" ;

			selectBoxProject = selectBoxProject + "<option value=\"-1\" >--<?php echo $lang_Leave_Common_Select;?>--</option>" ;
			<?php
			foreach($projects as $project){
				 $customer = $customerObj->fetchCustomer($project->getCustomerId() , true);
			?>
			selectBoxProject = selectBoxProject +  "<option value=\"<?php echo $project->getProjectId() ?>\"><?php echo "{$customer->getCustomerName()} - {$project->getProjectName()}"; ?></option>"  ;
			<?php
			}
			?>
			selectBoxProject = selectBoxProject +  "</select>"  ;
			var selectBoxActivity =  "<select name=\"cmbActivity[0]\" id=\"cmbActivity[0]\"  onchange=\"checkAddEvent("+ rowCount +")\"  onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\">" ;
			selectBoxActivity = selectBoxActivity + "<option value=\"-1\" >--<?php echo $lang_Leave_Common_Select;?>--</option>" ;
 			selectBoxActivity = selectBoxActivity +  "</select>"  ;

			var mytable			=	document.getElementById("tbody") ;
			mytable.deleteRow(0);
			var newtfootrow		=	mytable.insertRow(0);
			newtfootrow.id		=	"row[" + rowCount + "]";
			var newtfootcell1	=	newtfootrow.insertCell(0);
			var newtfootcell2	=	newtfootrow.insertCell(1);
			var newtfootcell3	=	newtfootrow.insertCell(2);
			var newtfootcell4	=	newtfootrow.insertCell(3);
			var newtfootcell5	=	newtfootrow.insertCell(4);
			var newtfootcell6	=	newtfootrow.insertCell(5);
			var newtfootcell7	=	newtfootrow.insertCell(6);
			var newtfootcell8	=	newtfootrow.insertCell(7);
			var newtfootcell9	=	newtfootrow.insertCell(8);
			var newtfootcell10	=	newtfootrow.insertCell(9);
			var newtfootcell11	=	newtfootrow.insertCell(10);
			newtfootcell1.className 	= "tableMiddleLeft"	 ;
			newtfootcell2.innerHTML		=  selectBoxProject  ;
			newtfootcell3.innerHTML		= selectBoxActivity ;
		<?php
		$rowDateStart = 4 ;
		for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
			$nameCell = "newtfootcell" . $rowDateStart  . ".innerHTML";
		?>
		 <?php echo  $nameCell  ?>		="<input name=\"txtDuration[0][<?php  echo $i ?>]\" type=\"text\" id=\"txtDuration[0][<?php  echo $i ?>]\"   value = \"0\" size=\"3\" onKeyPress=\"javascript:return limitinput(event, '0123456789', true);\"   onBlur=\"return validate(" + rowCount + " , <?php  echo $i ?>)\"  onfocus=\"setCurrentRowColorDefault(" + rowCount + ")\"/>"  ;
		<?php
		$rowDateStart++;
		}
		?>
			newtfootcell11.className 	= "tableMiddleLeft"	 ;
			rowCount++;
			$("rowCount").value = rowCount ;
	}
}
function actionUpdate() {

	$('frmTimesheet').action= initialAction+'Edit_Timesheet_Grid';
	$('frmTimesheet').submit();
}
function limitinput(evt, strList, bAllow) {

	var charCode = evt.keyCode;

	if (charCode==0) {
		charCode = evt.which;
	}
	var strChar = String.fromCharCode(charCode);
 	if (bAllow==true) {
		if (charCode==8 || charCode==9 || charCode==37 || charCode==39 || charCode==46 || charCode==116 || (strList.indexOf(strChar)!=-1)) {
		return true;
		}
		else{
			return false;
		}
	}
	else
	{
		if (charCode==8 || charCode==9 || charCode==37 || charCode==39 || charCode==46 || charCode==116 || (strList.indexOf(strChar)==-1)) {
		return true;
		}
		else
		{
		return false;
		}
	}
}
function checkDuraion(txtDurationValue) {

	var  regExp = /^[0-9]+\.*[0-9]*/;
	var  duration = txtDurationValue ;
	if (duration == ""  || !(regExp.test(duration))) {
			  return false ;
	} else {
		 return true ;
	}
}
function checkDuraionChange(txtDurationValue , obj) {

	if (!checkDuraion(txtDurationValue)) {
		alert( "<?php echo $lang_Time_Errors_InvalidTimeOrZeroOrNegativeIntervalSpecified_ERROR; ?>") ;
	}
}
function setCurrentProjectValue(row) {
	if(OldProjectIndex == null){ 
		OldProjectValue = parseFloat($("cmbProject[" + row + "]").value);
		OldProjectIndex = $("cmbProject[" + row + "]").selectedIndex ;
	}
	if(OldActivityIndex == null){
 		OldActivityIndex = parseFloat($("cmbActivity[" + row + "]").selectedIndex) ; 
 	}
}
function setCurrentActivityValue(row) {

	if(OldActivityIndex == null) {
 		OldActivityIndex = parseFloat($("cmbActivity[" + row + "]").selectedIndex) ; 
 	}
 	if(OldProjectIndex == null) { 
		OldProjectValue = parseFloat($("cmbProject[" + row + "]").value);
		OldProjectIndex = $("cmbProject[" + row + "]").selectedIndex ;
	}
}
function checkAddEvent(currentRow , eventType) {

	var rowCount = $("rowCount").value;
 	for(row = 0 ; row <rowCount ; row++) {
	
		if(row != currentRow){
	
			if($("cmbProject[" + row + "]").value == $("cmbProject[" + currentRow + "]").value  && $("cmbActivity[" + row + "]").value == $("cmbActivity[" + currentRow + "]").value) {
						
				 if (!eventType) {	
				 
					$("cmbProject[" + currentRow + "]").selectedIndex   =  0;
					$("cmbActivity[" + currentRow + "]").selectedIndex   = 0;
					
				}else{
 					if(OldProjectValue == null) {
 						OldProjectValue = parseFloat($("cmbProject[" + row + "]").value);
						OldProjectIndex = $("cmbProject[" + row + "]").selectedIndex
					} 
 					$("cmbProject[" + currentRow + "]").selectedIndex   = parseFloat(OldProjectIndex);
 					xajax_populateActivities(parseFloat(OldProjectValue), currentRow);
					 
				}
				$("row["+row+"]").style.background = "#FFAAAA";	
				errStr="<?php echo $lang_Time_Errors_EncounteredTheFollowingProblems; ?>\n";
				errStr +="<?php echo $lang_Time_Errors_ProjectActivityAlreadySelected; ?>\n";
				alert(errStr) ;
				 
				if (eventType) {	
  						$("cmbActivity[" + currentRow + "]").selectedIndex   = parseFloat(OldActivityIndex);
 				}
				OldProjectValue =  null;
				OldProjectIndex = null ;
				OldActivityIndex = null;
  			}else{
 				$("row["+row+"]").style.background = "#FFFFFF";	
			}
		}
 	}
}
-->
</script>
<?php $objAjax->printJavascript(); ?>
<h2><?php 	$headingStr = $lang_Time_Timesheet_TimesheetNameForEditTitle;
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
<form id="frmTimesheet" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
<table border="0" cellpadding="5" cellspacing="0" id="atable">
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
			<th width="100" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Project; ?></th>
			<th width="100" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Activity; ?></th>
		<?php for ($i=$startDate; $i<=$endDate; $i+=3600*24) { ?>
	    	<th width="80" class="tableMiddleMiddle"><?php echo date('l ' . LocaleUtil::getInstance()->getDateFormat(), $i); ?></th>
	    <?php } ?>
	    	<th class="tableMiddleRight"></th>
		</tr>
	</thead>
	<tbody id="tbody">
		<?php
		if (isset($timeExpenses) && is_array($timeExpenses)) {
 			foreach ($timeExpenses as $projectEvent=>$timeExpense) {

				$projectDet = $projectObj->fetchProject($projectEvent);
				$projectActivities = $projectActivityObj->getActivityList($projectEvent);

				foreach ($timeExpense as $activityId=>$activityExpense) {
 		?>
			<tr id="row[<?php echo $row ?>]">
				<td class="tableMiddleLeft"></td>
				<td ><select id="cmbProject[<?php echo $row; ?>]" name="cmbProject[<?php echo $row; ?>]" onfocus="setCurrentRowColorDefault(<?php echo $row  ?>),setCurrentProjectValue(<?php echo $row ?>)" onchange="$('status').innerHTML='<?php echo $lang_Common_Loading;?>...'; xajax_populateActivities(this.value, <?php echo $row; ?>)">
                	<?php if(count($projects)) { ?>
                	<option value="-1">--<?php echo $lang_Leave_Common_Select;?>--</option>
                	<?php
					foreach($projects as $project){
						 $customer = $customerObj->fetchCustomer($project->getCustomerId() , true);
 						if($projectDet->getProjectId() == $project->getProjectId()){
					?>
						<option value="<?php echo $project->getProjectId() ?>" selected="selected"><?php echo "{$customer->getCustomerName()} - {$project->getProjectName()}"; ?></option>
					<?php
						}else{
 					 ?>
                     	<option value="<?php echo $project->getProjectId() ?>"><?php echo "{$customer->getCustomerName()} - {$project->getProjectName()}" ?></option>
                    <?php
						}
 					}
				}else {
				?>
                <option value="-1">--<?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
                <?php
				}
				?>
                  </select>
				</td>
				<td><select id="cmbActivity[<?php echo $row ?>]" name="cmbActivity[<?php echo $row ?>]" onfocus=" setCurrentRowColorDefault(<?php echo $row ?>),setCurrentActivityValue(<?php echo $row ?>)">
                  <?php if (count($projectActivities)) { ?>
                  <option value="-1">--<?php echo $lang_Leave_Common_Select;?>--</option>
                  <?php	foreach ($projectActivities as $projectActivity) {
							if ($projectActivity->getId() == $activityId) {
 								
					?>
                <option value="<?php echo $projectActivity->getId(); ?>" selected="selected"><?php echo $projectActivity->getName();?></option>
                <?php
				
						}else{
				?>
                  <option value="<?php echo $projectActivity->getId(); ?>"><?php echo $projectActivity->getName(); ?></option>
                  <?php }
				  
				  		}
					} else { ?>
                  <option value="-1">--<?php echo $lang_Time_Timesheet_NoCustomers;?> -</option>
                  <?php } ?>
                </select></td>
                <input type="hidden" id="cmbHiddenProject[<?php echo $row ?>]" name="cmbHiddenProject[<?php echo $row ?>]" value="<?php echo $projectDet->getProjectId() ?>" />
                <input type="hidden" id="cmbHiddenActivity[<?php echo $row ?>]" name="cmbHiddenActivity[<?php echo $row ?>]" value="<?php echo $activityId ?>" />
			<?php 	
			 
			for ($i=$startDate; $i<=$endDate; $i+=3600*24) {
						 
						$_SESSION['PreviousProject'][$row] = $projectDet->getProjectId();
						$_SESSION['PreviousActivity'][$row] = $activityId;
						if (!isset($activityExpense[$i])) {
							$activityExpense[$i]=0;
						}
 						$_SESSION['Previous'][$row][$i] =  round($activityExpense[$i]/36)/100 ;
						if(isset($evantCountArray[$projectDet->getProjectId().$projectDet->getProjectId().$i])) {

							$_SESSION['EventCount'][$row][$i] =  $evantCountArray[$projectDet->getProjectId().$projectDet->getProjectId().$i];
						}
    			?>
	    		<td >
    		    <input name="txtDuration[<?php echo $row  ?>][<?php echo $i  ?>]" type="text" id="txtDuration[<?php echo $row  ?>][<?php echo $i  ?>]"   value="<?php echo round($activityExpense[$i]/36)/100; ?>" size="3" onKeyPress="javascript:return limitinput(event, '0123456789', true);" onchange="checkDuraionChange(this.value , this)"  onfocus="setCurrentRowColorDefault(<?php echo $row ?>) , checkAddEvent(<?php echo $row; ?>  , eventType=1)"/></td>
	    	<?php } ?>
	    		<td class="tableMiddleRight"></td>
			</tr>
 		<?php
			
			$row++;
		}
			} ?>
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
<input type="hidden" id="txtAllcount" name="txtAllcount" value="1" />
<input type="hidden" id="rowCount" name="rowCount" value="<?php echo $row ; ?>" />
<input type="hidden" id="txtTimesheetId" name="txtTimesheetId" value="<?php echo $timesheet->getTimesheetId(); ?>" />
<input type="hidden" name="txtEmployeeId" value="<?php echo $timesheet->getEmployeeId(); ?>" />
 <input type="hidden" id="txtTimesheetPeriodId" name="txtTimesheetPeriodId" value="<?php echo $timesheet->getTimesheetPeriodId(); ?>" />
<input type="hidden" id="txtStartDate" name="txtStartDate" value="<?php echo $startDate ?>" />
<input type="hidden" id="txtEndDate" name="txtEndDate" value="<?php echo $endDate ?>" />
<div>
<input src="../../themes/beyondT/pictures/btn_add.gif" onclick="actionAddRow(); return false;"
			onmouseover="this.src='../../themes/beyondT/pictures/btn_add.gif';"
			onmouseout="this.src='../../themes/beyondT/pictures/btn_add.gif';"
			name="btnAdd" id="btnAdd" height="20" type="image" width="65"/>
<input src="../../themes/beyondT/pictures/btn_save.gif"
			onclick="actionUpdate(); return false;"
			onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
			onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';"
			name="btnEdit2" id="btnEdit2" height="20" type="image" width="65"/>
<br/>
</div>
</form>
</p>