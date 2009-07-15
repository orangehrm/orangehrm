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

 if (isset($modifier[1])) {
 	$dispYear = $modifier[1];
 }

 $leaveStatuses = (isset($modifier['leave_statuses'])) ? $modifier['leave_statuses'] : array();
 $fromDate = (isset($modifier['from_date'])) ? LocaleUtil::getInstance()->formatDate($modifier['from_date']) : null;
 $toDate = (isset($modifier['to_date'])) ? LocaleUtil::getInstance()->formatDate($modifier['to_date']) : null;

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
	$lang_Title = $lang_Leave_Leave_list_Title1;
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
                   Leave::LEAVE_STATUS_LEAVE_HOLIDAY => $lang_Leave_Common_Weekend,
                   LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES => $lang_Leave_Common_StatusDiffer);
?>

<?php

/* Show leave filter form only for admin */
if ($modifier === "ADMIN") {

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
	YAHOO.OrangeHRM.container.init();
//]]>
</script>
<?php
/* Following empty div added to prevent problem in IE, where outerbox margin is not used due to
 * iframe added by YAHOO.OrangeHRM.container.init()
 */
?>
<div></div>
<div id="filterLeavePane" class="outerbox" style="width:800px;">
    <div class="mainHeading"><h2><?php echo $lang_Title;?></h2></div>
    <?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {
            $message  = $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = "lang_Leave_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>
    <?php } ?>

<form id="frmFilterLeave" name="frmFilterLeave" method="post"
      onsubmit="return validateSearch();"
      action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $refreshAction; ?>">
  <table border="0" cellpadding="2" cellspacing="0">
  <tbody>
  <tr>
  <td></td>
  <td><strong><?php echo $lang_Leave_Leave_list_Period;?></strong></td>
  <td><?php echo $lang_Leave_Leave_list_From;?>
  <span><input name="txtFromDate" type="text" id="txtFromDate"  size="11" value="<?php echo $fromDate;?>"/>&nbsp;
  <input type="button" name="Submit" value="  " class="calendarBtn" style="display: inline;margin:0;float:none;"/></span>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lang_Leave_Leave_list_To;?>
  <span><input name="txtToDate" type="text" id="txtToDate" size="11" value="<?php echo $toDate;?>" />&nbsp;
  <input type="button" name="Submit" value="  " class="calendarBtn" style="display: inline;margin:0;float:none;"/></span></td>
  <td>
    <input type="submit" class="searchbutton" id="Search"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        title="<?php echo $lang_Common_Search;?>"
        value="<?php echo $lang_Common_Search;?>" />
  </td>
  <td></td>
  </tr>

  <tr>
  <td></td>
  <td><strong><?php echo $lang_Leave_Leave_list_ShowLeavesWithStatus;?>:</strong></td>
  <td nowrap="nowrap" >
	<?php echo $lang_Leave_Common_All; ?>
	<input type='checkbox' class='checkbox' name='allCheck' id='allCheck' onclick="toggleAll();"/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<?php
	foreach ($statusArr as $key=>$value) {

		// Don't show multiple status as a check box
		if ($key == LeaveRequests::LEAVEREQUESTS_MULTIPLESTATUSES) {
			continue;
		}

		$checked = (in_array($key, $leaveStatuses)) ? "checked='checked'" : "";
?>
		<?php echo "$value";?>
		<input type='checkbox' class='checkbox' name='leaveStatus[]' value='<?php echo $key;?>'
			<?php echo $checked;?> />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
	}
?>
    </td>
    <td>
    <input type="button" class="clearbutton" onclick="resetSearchForm();return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
         title="<?php echo $lang_Common_Reset;?>"
         value="<?php echo $lang_Common_Reset;?>" />

	</td>
	<td></td>
	</tr>
	</tbody>
	</table>
</form>
</div>
<hr/>
<?php
}
?>

<div class="outerbox">
<?php if ($modifier !== "ADMIN") { ?>
<div class="mainHeading"><h2><?php echo $lang_Title;?></h2></div>
<?php } ?>
<form id="frmCancelLeave" name="frmCancelLeave" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $action; ?>">

<?php   if ($modifier !== "Taken") { ?>
    <div class="actionbar">
        <div class="actionbuttons">
<?php   if (is_array($records) && (count($records) > 0)) { ?>
            <input type="submit" class="savebutton" name="Save"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
<?php   } ?>
        </div>
        <div class="noresultsbar"><?php echo (!is_array($records)) ? $lang_Error_NoRecordsFound : '';?></div>
        <div class="pagingbar"></div>
    <br class="clear" />
    </div>
<?php   } ?>
    <br class="clear" />

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
    <td class="<?php echo $cssClass; ?>"><a href="?leavecode=Leave&amp;action=<?php echo ($modifier == "ADMIN")?"Leave_FetchDetailsAdmin":$detailAction; ?>&amp;id=<?php echo $record->getLeaveRequestId(); ?>&amp;digest=<?php echo md5($record->getLeaveRequestId().SALT); ?>"><?php echo $dateStr; ?></a></td>
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
    	?>
    			<input type="hidden" name="id[]" value="<?php echo $record->getLeaveRequestId(); ?>" />
    			<?php if (($record->getLeaveLengthHours() != null) || ($record->getLeaveLengthHours() != 0)) { ?>
    			<select name="cmbStatus[]">
  					<option value="<?php echo $record->getLeaveStatus();?>" selected="selected" ><?php echo $statusArr[$record->getLeaveStatus()]; ?></option>
  					<?php foreach($possibleStatusesArr as $key => $value) {
  								if ($key != $record->getLeaveStatus()) {
  					?>
  							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
  					<?php 		}
  							}
  					?>
  				</select>
  				<?php } else { ?>
  					<?php echo $lang_Leave_Holiday; ?> <input type="hidden" name="cmbStatus[]" value="<?php echo $record->getLeaveStatus(); ?>" />
  				<?php }?>
    	<?php
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
    <td class="<?php echo $cssClass; ?>">
	<?php
		if ($record->getCommentsDiffer()) {
			$inputType = "readonly";
		} else {
			$inputType = "";
		}

		if (($modifier != null) && ($modifier == "Taken")) {
			echo $record->getLeaveComments(); ?>
		<input type="hidden" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $record->getLeaveComments(); ?>" />
	<?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved) ||
	    (($record->getLeaveStatus() ==  $record->statusLeaveRejected) && ($modifier == "SUP" || $modifier == "ADMIN")) ||
	    (($record->getLeaveStatus() ==  Leave::LEAVE_STATUS_LEAVE_TAKEN) && ($modifier == "ADMIN"))) { ?>
		<?php $leaveComments=htmlentities($record->getLeaveComments()); ?> 
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $leaveComments; ?>" />
		<input type="hidden" name="txtEmployeeId[]" value="<?php echo $record->getEmployeeId(); ?>" />
		<?php } else if (($record->getLeaveStatus() == $record->statusLeavePendingApproval) || ($record->getLeaveStatus() ==  $record->statusLeaveApproved)) { ?>
		<input type="text" <?php echo $inputType; ?> name="txtComment[]" value="<?php echo $leaveComments; ?>" />
		<?php } else {
			echo $leaveComments;
			} ?></td>
  </tr>

<?php
		}
?>
  </tbody>
</table>
<br class="clear" />

</form>
</div>

<div id="cal1Container" style="position:absolute;" ></div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
