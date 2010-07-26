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
 */

$_SESSION['moduleType'] = 'leave';
require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
require_once ROOT_PATH . '/plugins/PlugInFactory.php';
// Check leave-csv plugin available
$PlugInObj = PlugInFactory::factory("LEAVEREPORT");
if(is_object($PlugInObj) && $PlugInObj->checkAuthorizeLoginUser(authorize::AUTHORIZE_ROLE_ADMIN) && $PlugInObj->checkAuthorizeModule( $_SESSION['moduleType'])){
	$csvLeaveExportRepotsPluginAvailable = true;
}

if (isset($modifier[1])) {
	$dispYear = $modifier[1];
}

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
   unset($records['token']);
}

if(isset($modifier['token'])) {
   $token = $modifier['token'];
}

$leaveStatuses = (isset($modifier['leave_statuses'])) ? $modifier['leave_statuses'] : array();
$fromDate = (isset($modifier['from_date'])) ? LocaleUtil::getInstance()->formatDate($modifier['from_date']) : null;
$toDate = (isset($modifier['to_date'])) ? LocaleUtil::getInstance()->formatDate($modifier['to_date']) : null;

$recordsCount = isset($modifier['recordsCount'])?$modifier['recordsCount']:0;
$pageNo = isset($modifier['pageNo'])?$modifier['pageNo']:1;

$modifier = $modifier[0];

if (isset($modifier) && ($modifier == "Taken")) {
	$empInfo = $records[count($records)-1][0];
	$employeeName = $empInfo[2].' '.$empInfo[1];

	array_pop($records);

	$records = $records[0];
}
if ($modifier === "ADMIN") {
	$lang_Title = $lang_Leave_Leave_list_TitleAllEmployees;
} else if ($modifier === "SUP") {
	$lang_Title = $lang_Leave_Leave_list_TitleAllSubordinates;
} else if ($modifier === "Taken") {
	$lang_Title = preg_replace(array('/#employeeName/', '/#dispYear/'), array($employeeName, $dispYear) , $lang_Leave_Leave_list_Title2);
} else if ($modifier === "MY") {
	$lang_Title = $lang_Leave_Leave_list_TitleMyLeaveList;
} else {
	$lang_Title = $lang_Leave_Leave_list_Title3;
}

if ($modifier === "SUP" || $modifier === "ADMIN") {
	$action = "Leave_Request_ChangeStatus";
	$detailAction = "Leave_FetchDetailsSupervisor";

} else {
	$action = "Leave_Request_CancelLeave";
	$detailAction = "Leave_FetchDetailsEmployee";
}
$statusArr = array(Leave::LEAVE_STATUS_LEAVE_REJECTED => $lang_Leave_Common_Rejected,
Leave::LEAVE_STATUS_LEAVE_CANCELLED => $lang_Leave_Common_Cancelled,
Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL => $lang_Leave_Common_PendingApproval,
Leave::LEAVE_STATUS_LEAVE_APPROVED => $lang_Leave_Common_Approved,
Leave::LEAVE_STATUS_LEAVE_TAKEN => $lang_Leave_Common_Taken,
Leave::LEAVE_STATUS_LEAVE_WEEKEND => $lang_Leave_Common_Weekend,
Leave::LEAVE_STATUS_LEAVE_HOLIDAY => $lang_Leave_Common_Holiday,
LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES => $lang_Leave_Common_StatusDiffer
);
?>
<script type="text/javascript">
var exportStatus = false;
function validateLeaveRequestList() {
	for (i = 0; i < noOfLeaveRecords; i++) {
		if ($('txtComment_' + i).value.length > <?php echo LeaveRequests::MAX_COMMENT_LENGTH ?>) {
			alert('<?php echo CommonFunctions::escapeForJavascript(sprintf($lang_Leave_LeaveCommentTooLong, LeaveRequests::MAX_COMMENT_LENGTH)); ?>');
			$('txtComment_' + i).focus();
			return false;
		}
	}
	return true;
}
</script>
<?php

/* Show leave filter form only for admin */
if ($modifier === "ADMIN" || $modifier ==="SUP") {

	$refreshAction = $_GET['action'];
	?>
<script type="text/javascript">
//<![CDATA[
	/**
	 * Reset search form
	 */
	function resetSearchForm() {
		$("txtFromDate").value = "";
		$("txtToDate").value = "";
		$("allCheck").checked = false;
		checkAll(false);
	}

    /**
     * Validate that at least one status check box has been selected before refreshing data
     */
	function validateSearch() {
	
    	if(exportStatus == true) {        	
    		document.frmFilterLeave.action = "../../lib/controllers/CentralController.php?leavecode=Leave&action=<?php echo $refreshAction ?>";    		
    	}
    	
		var errors = new Array();
		var checked = false;

		with (document.frmFilterLeave) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'leaveStatus[]')){
					checked = true;
					break;
				}
			}
		}

		if (!checked){
			errors[errors.length] = '<?php echo $lang_Leave_Leave_list_SelectAtLeastOneStatus; ?>';
		}

		from = $("txtFromDate").value.trim();
		to = $("txtToDate").value.trim();
		startDate = false;
		endDate = false;

		hint = YAHOO.OrangeHRM.calendar.formatHint.format;
		if (from != '' && from != hint) {
			startDate = strToDate(from, YAHOO.OrangeHRM.calendar.format);
			if (!startDate) {
				errors[errors.length] = "<?php echo $lang_Error_PleaseSelectAValidFromDate; ?>";
			}
		}

		if (to != '' && to != hint) {
			endDate = strToDate(to, YAHOO.OrangeHRM.calendar.format);
			if (!endDate) {
				errors[errors.length] = "<?php echo $lang_Error_PleaseSelectAValidToDate; ?>";
			}
		}

		if (startDate && endDate && (startDate > endDate)) {
				errors[errors.length] = "<?php echo $lang_Leave_Common_InvalidDateRange; ?>";
		}

		if (errors.length > 0) {
			errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
			for (i in errors) {
				errStr += " - "+errors[i]+"\n";
			}
			alert(errStr);
			return false;
		}

		return true;
	}

    function toggleAll() {
    	checkAll($('allCheck').checked);
    }

	/*
	 * Check all leave status check boxes
	 */
	function checkAll(check) {
		with (document.frmFilterLeave) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].name == 'leaveStatus[]')) {
					elements[i].checked = check;
				}
			}
		}
	}
	
	/* Functions for paging */
	
	function nextPage() {
		i=document.frmFilterLeave.pageNo.value;
		i++;
		document.frmFilterLeave.pageNo.value=i;
		document.frmFilterLeave.submit();
	}

	function prevPage() {
		var i=document.frmFilterLeave.pageNo.value;
		i--;
		document.frmFilterLeave.pageNo.value=i;
		document.frmFilterLeave.submit();
	}

	function chgPage(pNO) {
		document.frmFilterLeave.pageNo.value=pNO;
		document.frmFilterLeave.submit();
	}

	function exportSummaryData(pdfData) {

		var errors = new Array();
		var checked = false;
		
		with (document.frmFilterLeave) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'leaveStatus[]')){
					checked = true;
					break;
				}
			}
		}

		if (!checked){
			errors[errors.length] = '<?php echo $lang_Leave_Leave_list_SelectAtLeastOneStatus; ?>';
		}

		if (errors.length > 0) {
			errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
			for (i in errors) {
				errStr += " - "+errors[i]+"\n";
			}
			alert(errStr);
			return;
		}
		
		exportStatus = true;
	    var url = "../../plugins/leave-csv/LeaveReportController.php?path=<?php echo addslashes(ROOT_PATH) ?>&repType=leaveListSummaryRep"+"&printPdf="+pdfData+"&pdfName=Leave-List-Summary"+"&moduleType=<?php echo  $_SESSION['moduleType'] ?>&obj=<?php  echo   base64_encode(serialize($PlugInObj))?>";
	
		document.frmFilterLeave.action =  url;		
		document.frmFilterLeave.submit();
	
	}

	function exportDetailedData(pdfData) {

		var errors = new Array();
		var checked = false;
		
		with (document.frmFilterLeave) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'leaveStatus[]')){
					checked = true;
					break;
				}
			}
		}

		if (!checked){
			errors[errors.length] = '<?php echo $lang_Leave_Leave_list_SelectAtLeastOneStatus; ?>';
		}

		if (errors.length > 0) {
			errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
			for (i in errors) {
				errStr += " - "+errors[i]+"\n";
			}
			alert(errStr);
			return;
		}
		
		exportStatus = true;
	    var url = "../../plugins/leave-csv/LeaveReportController.php?path=<?php echo addslashes(ROOT_PATH) ?>&repType=leaveListDetRep"+"&printPdf="+pdfData+"&pdfName=Leave-List-Summary"+"&moduleType=<?php echo  $_SESSION['moduleType'] ?>&obj=<?php  echo   base64_encode(serialize($PlugInObj))?>";
	
		document.frmFilterLeave.action =  url;		
		document.frmFilterLeave.submit();
	
	}

	YAHOO.OrangeHRM.container.init();
//]]>
</script>
<style type="text/css">
input.checkbox {
	vertical-align: middle;
}

input.calendarBtn {
	float: none;
	display: inline;
	width: 500px;
}

input#txtFromDate,input#txtToDate {
	float: none;
	display: inline;
	margin-right: -10px;
	margin-left: 8px;
}

label.mainLabel {
	font-weight: bold;
	float: none;
	display: inline-block;
	width: 150px;
}

label.subLabel {
	float: none;
	display: inline;
	width: auto;
}
</style>
	<?php
	/* Following empty div added to prevent problem in IE, where outerbox margin is not used due to
	 * iframe added by YAHOO.OrangeHRM.container.init()
	 */
	?>
<div></div>
<div id="filterLeavePane" class="outerbox"
	style="min-width: 970px; width: 98%;">
<div class="mainHeading">
<h2><?php echo $lang_Title;?></h2>
</div>
	<?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {
		$message  = $_GET['message'];
		$messageType = CommonFunctions::getCssClassForMessage($message);
		$message = "lang_Leave_" . $message;
		?>
<div class="messagebar"><span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
</div>
		<?php } ?>
<form id="frmFilterLeave" name="frmFilterLeave" method="post"
	onsubmit="return validateSearch();"
	action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $refreshAction; ?>">
<label class="mainLabel"><?php echo $lang_Leave_Leave_list_Period;?></label>
<label class="subLabel" for="txtFromDate"><?php echo $lang_Leave_Leave_list_From;?></label>
<input name="txtFromDate" type="text" id="txtFromDate" size="11"
	value="<?php echo $fromDate;?>" /> <input type="button" value="  "
	class="calendarBtn" style="float: none; display: inline;" /> <label
	class="subLabel" for="txtToDate" style="margin-left: 30px;"><?php echo $lang_Leave_Leave_list_To;?></label>
<input name="txtToDate" type="text" id="txtToDate" size="11"
	value="<?php echo $toDate;?>" /> <input type="button" value="  "
	class="calendarBtn" style="float: none; display: inline;" /><br />
<label class="mainLabel"><?php echo $lang_Leave_Leave_list_ShowLeavesWithStatus;?>:</label>
<label class="subLabel" for="allCheck"><?php echo $lang_Leave_Common_All; ?></label>
<input type="checkbox" class="checkbox" name="allCheck" id="allCheck"
	onclick="toggleAll();" /> <input type="hidden" name="pageNo"
	value="<?php echo (isset($pageNo))?$pageNo:'1'; ?>"> <?php
	foreach ($statusArr as $key=>$value) {
		/* Don't show multiple status as a check box */
		if ($key == LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES) {
			continue;
		}
		$checked = (in_array($key, $leaveStatuses)) ? 'checked="checked"' : '';
		?> <label class="subLabel" for="<?php echo "leaveStatus_{$key}"; ?>"><?php echo $value;?></label>
<input type="checkbox" class="checkbox" name="leaveStatus[]"
	id="<?php echo "leaveStatus_{$key}"; ?>" value="<?php echo $key;?>"
	<?php echo $checked;?> /> <?php
	}
	?> &nbsp;&nbsp; <input type="submit" class="searchbutton" id="Search"
	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	value="<?php echo $lang_Common_Search;?>" /> <input type="button"
	class="clearbutton" onclick="resetSearchForm();return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	title="<?php echo $lang_Common_Reset;?>"
	value="<?php echo $lang_Common_Reset;?>" /></form>
</div>
<hr />
	<?php
}
?>

<div class="outerbox"><?php if ($modifier !== "ADMIN" && $modifier !=="SUP") { ?>
<div class="mainHeading">
<h2><?php echo $lang_Title;?></h2>
</div>
<?php } ?>
<form id="frmCancelLeave" name="frmCancelLeave" method="post"
	action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $action; ?>"
	onsubmit="return validateLeaveRequestList()"><?php   if ($modifier !== "Taken") { ?>
   <input type="hidden" value="<?php echo $token;?>" name="token" />
<div class="actionbar">
<div class="actionbuttons"><?php   if (is_array($records) && (count($records) > 0)) { ?>
<input type="submit" class="plainbtn" name="Save"
	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	value="<?php echo $lang_Common_Save;?>" /> <?php  if(isset($csvLeaveExportRepotsPluginAvailable))  {   ?>

<!--
						The value/label of the following button is hardcoded because it is shown
						only if the plugin is installed and the label should come from the plugin
						and not from the language files
					-->
  <?php if ($modifier === "ADMIN" || $modifier ==="SUP") { ?>
    <input type="button" name="btnExportData"
	value="Summary CSV" class="plainbtn"
	onclick="exportSummaryData(0); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" />
    <input type="button" name="btnExportPDFData"
	value="Summary PDF" class="plainbtn"
	onclick="exportSummaryData(1); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" />
    <input type="button" name="btnExportData" value="Detailed CSV"
	class="plainbtn" onclick="exportDetailedData(0); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" />
     <input type="button" name="btnExportDePDFData" value="Detailed PDF"
	class="plainbtn" onclick="exportDetailedData(1); return false;"
	onmouseover="moverButton(this);" onmouseout="moutButton(this)" /> <?php } } ?>
<?php   } ?></div>
<div class="noresultsbar"><?php echo (!is_array($records)) ? $lang_Error_NoRecordsFound : '';?></div>

<!-- Paging: Begins --> <?php if ($recordsCount > 50) {

	echo '<div class="pagingbar">';

	$commonFunc = new CommonFunctions();
	$pageStr = $commonFunc->printPageLinks($recordsCount, $pageNo, 50);
	$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

	echo $pageStr;

	echo '</div>';

} ?> <!-- Paging: Ends --> <br class="clear" />
</div>
<?php   } ?> <br class="clear" />

<table border="0" cellpadding="0" cellspacing="0" class="data-table">
	<thead>
		<tr>
			<th><?php echo $lang_Leave_Common_Date;?></th>
			<?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
			<th><?php echo $lang_Leave_Common_EmployeeName;?></th>
			<?php } ?>
			<th><?php echo $lang_Leave_NoOfDays;?></th>
			<th><?php echo $lang_Leave_Common_LeaveType;?></th>
			<th><?php echo $lang_Leave_Common_Status;?></th>
			<th><?php echo $lang_Leave_Period;?></th>
			<th><?php echo $lang_Leave_Common_Comments;?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$j = 0;
	$idIndex = 0;
	if (is_array($records))
	foreach ($records as $record) {
		if(!($j%2)) {
			$cssClass = 'odd';
		} else {
			$cssClass = 'even';
		}
		$j++;

		$dateStr = LocaleUtil::getInstance()->formatDate($record->getLeaveFromDate());
		$toDate = LocaleUtil::getInstance()->formatDate($record->getLeaveToDate());

		if (!empty($toDate)) {
			$dateStr .=	" -> ".$toDate;
		}
		?>
		<tr>
			<td class="<?php echo $cssClass; ?>"><a
				href="?leavecode=Leave&amp;action=<?php echo ($modifier == "ADMIN")?"Leave_FetchDetailsAdmin":$detailAction; ?>&amp;id=<?php echo $record->getLeaveRequestId(); ?>&amp;digest=<?php echo md5($record->getLeaveRequestId().SALT); ?>"><?php echo $dateStr; ?></a></td>
				<?php if ($modifier == "SUP" || $modifier == "ADMIN") { ?>
			<td class="<?php echo $cssClass; ?>"><?php echo $record->getEmployeeName(); ?></td>
			<?php } ?>
			<td class="<?php echo $cssClass; ?>"><?php echo round($record->getNoDays(),2); ?></td>
			<td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName(); ?></td>
			<td class="<?php echo $cssClass; ?>"><?php
			$suprevisorRespArr = array($record->statusLeaveRejected => $lang_Leave_Common_Rejected, $record->statusLeaveApproved => $lang_Leave_Common_Approved, $record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);
			$employeeRespArr = array($record->statusLeaveCancelled => $lang_Leave_Common_Cancelled);

			if ($modifier === "MY") {
				$possibleStatusesArr = $employeeRespArr;
			} else if ($modifier == "SUP" || $modifier == "ADMIN") {
				$possibleStatusesArr = $suprevisorRespArr;

				if ($record->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_TAKEN) {
					$possibleStatusesArr = array(Leave::LEAVE_STATUS_LEAVE_CANCELLED => $lang_Leave_Common_Cancelled);
				}
			}

			if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) ||
			($record->getLeaveStatus() ==  $record->statusLeaveApproved) ||
			(($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN")) ||
			(($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier == "ADMIN"))) {
				?> <input type="hidden" name="id[]"
				value="<?php echo $record->getLeaveRequestId(); ?>" /> <?php if (($record->getLeaveLengthHours() != null) || ($record->getLeaveLengthHours() != 0)) { ?>
			<select name="cmbStatus[]">
				<option value="<?php echo $record->getLeaveStatus();?>"
					selected="selected"><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
					<?php foreach($possibleStatusesArr as $key => $value) {
						if ($key != $record->getLeaveStatus()) {
							?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
				<?php 		}
					}
					?>
			</select> <?php } else { ?> <?php echo $lang_Leave_Holiday; ?> <input
				type="hidden" name="cmbStatus[]"
				value="<?php echo $record->getLeaveStatus(); ?>" /> <?php }?> <?php
			} else if ($record->getLeaveStatus() != null) {
				echo $statusArr[$record->getLeaveStatus()];
			}


			?></td>
			<td class="<?php echo $cssClass; ?>"><?php
			$leaveLength = null;
			if ($record->getLeaveLengthHours() == LeaveRequests::LEAVEREQUESTS_LEAVELENGTH_RANGE) {
				$leaveLength = $lang_Leave_Common_Range;
			} else if (($record->getStartTime() != null) && ($record->getEndTime() != null) && ($record->getStartTime() != $record->getEndTime())) {
				$leaveLength = "{$record->getStartTime()} - {$record->getEndTime()}";
			} else if ($record->getLeaveLengthHours() != null) {
				$leaveLength = "{$record->getLeaveLengthHours()} {$lang_Common_Hours}";
			} else {
				$leaveLength = '----';
			}

			echo $leaveLength;
			?></td>
			<td class="<?php echo $cssClass; ?>"><?php
			if ($record->getCommentsDiffer()) {
				$inputType = "readonly";
			} else {
				$inputType = "";
			}

			if (($modifier != null) && ($modifier == "Taken")) {
				echo $record->getLeaveComments(); ?> <input type="hidden"
				<?php echo $inputType; ?> name="txtComment[]"
				id="txtComment_<?php echo $idIndex++; ?>"
				value="<?php echo $record->getLeaveComments(); ?>" /> <?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) ||
				(($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN")) ||
				(($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier == "ADMIN"))) { ?>
				<?php $leaveComments=htmlentities($record->getLeaveComments()); ?> <input
				type="text" <?php echo $inputType; ?> name="txtComment[]"
				id="txtComment_<?php echo $idIndex++; ?>"
				value="<?php echo $leaveComments; ?>" /> <input type="hidden"
				name="txtEmployeeId[]"
				value="<?php echo $record->getEmployeeId(); ?>" /> <?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved)) { ?>
			<input type="text" <?php echo $inputType; ?> name="txtComment[]"
				id="txtComment_<?php echo $idIndex++; ?>"
				value="<?php echo $leaveComments; ?>" /> <?php } else {
					echo $record->getLeaveComments();
				} ?></td>
		</tr>

		<?php
	}
	?>
	</tbody>
</table>
<script type="text/javascript">
noOfLeaveRecords = <?php echo $idIndex; ?>;
</script> <br class="clear" />

</form>
</div>

<div
	id="cal1Container" style="position: absolute;"></div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
