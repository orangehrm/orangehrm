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

require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/time/Workshift.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php'; 

 $employees = null;

 if (isset($records[0])) {
 	$employees = $records[0];
 }

 if (isset($records[2])) {
 	$role = $records[2];
 }

 if (isset($records[3])) {
 	$previousLeave = $records[3];
 }

 $startTime = strtotime("00:00");
 $endTime = strtotime("23:59");
 $interval = 60*15;

 $shiftLength = Leave::LEAVE_LENGTH_FULL_DAY;
 if (isset($records['shiftLength'])) {
 	$shiftLength = $records['shiftLength'];
 }

 if (isset($records['exception'])) {
 	$exception = $records['exception'];
 }


?>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script src="../../scripts/time.js"></script>
<script>

	var shiftLength = <?php echo $shiftLength; ?>;
	var empShifts = new Array();
<?php
	if (isset($records['allEmpWorkshits'])) {
		foreach($records['allEmpWorkshits'] as $empId=>$shiftLen) {
			echo "\t" . 'empShifts["' . $empId . '"] = ' . $shiftLen . ";\n";
		}
	}
?>

	function resetShiftLength() {

		var empId = document.frmLeaveApp.cmbEmployeeId.value;

		if (empId > 0) {
			empId = trimLeadingZeros(empId);
			workshift = empShifts[empId];

			if (workshift > 0) {
				shiftLength = workshift;
			} else {
				shiftLength = 8;
			}
		}
	}

	function addSave() {
		fillToDate();
		fillTimes();

		err = false;
		msg = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

		obj = document.frmLeaveApp.cmbEmployeeId;
		if (obj && ((obj.value == '') || (obj.value == -1))) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n"
		}

		obj = document.frmLeaveApp.txtLeaveFromDate;
		if ((obj.value == '') || !YAHOO.OrangeHRM.calendar.parseDate(obj.value)) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectAValidFromDate; ?>\n"
		}

		obj = document.frmLeaveApp.txtLeaveToDate;
		if (obj.value == '') {
			fillAuto('txtLeaveFromDate', 'txtLeaveToDate');
		}
		if ((obj.value == '') || !YAHOO.OrangeHRM.calendar.parseDate(obj.value)) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectAValidToDate; ?>\n"
		}

		date1 = document.frmLeaveApp.txtLeaveFromDate.value;
		date2 = document.frmLeaveApp.txtLeaveToDate.value;
		var format = YAHOO.OrangeHRM.calendar.format;
		if (strToDate(date1, format) > strToDate(date2, format)) {
			err = true;
			msg += " - <?php echo $lang_Leave_Common_InvalidDateRange; ?>\n"
		}


		obj = document.frmLeaveApp.sltLeaveType;
		if (obj.value == -1) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectALeaveType; ?>\n"
		}

		obj = document.frmLeaveApp.sltLeaveType;
		if (obj.value == -1) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectALeaveType; ?>\n"
		}

		if (document.frmLeaveApp.cmbEmployeeId) {
			obj = document.frmLeaveApp.cmbEmployeeId;
			if (obj.value == -1) {
				err = true;
				msg += " - <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n"
			}
		}

		if (($('txtLeaveFromDate').value != '') && ($('txtLeaveFromDate').value == $('txtLeaveToDate').value)) {
			if (($('sltLeaveFromTime').value != '') && ($('sltLeaveToTime').value != '')) {
				fromTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveFromTime').value, YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format);
				toTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveToTime').value, YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format);

				if (fromTime == toTime) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_ZeroLengthHours; ?>\n"					
				} else if (fromTime > toTime) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_ToTimeBeforeFromTime; ?>\n"
				} else if (($('txtLeaveTotalTime').value != '') && (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) ) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?> (" + "<?php echo $lang_Leave_Common_WorkshiftLengthIs;?> " + shiftLength + " <?php echo $lang_Common_Hours; ?>) \n";
				}

			} else if (($('sltLeaveFromTime').value != '') && extractTimeFromHours($('txtLeaveTotalTime').value)) {
				if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?> (" + "<?php echo $lang_Leave_Common_WorkshiftLengthIs;?> " + shiftLength + " <?php echo $lang_Common_Hours; ?>) \n";
				}
			} else if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?> (" + "<?php echo $lang_Leave_Common_WorkshiftLengthIs;?> " + shiftLength + " <?php echo $lang_Common_Hours; ?>) \n";
			} else if (($('sltLeaveFromTime').value == '' || $('sltLeaveToTime').value == '') && $('txtLeaveTotalTime').value == '') {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod; ?>\n"
			} else if (!numeric($('txtLeaveTotalTime'))) {
				err = true;
				msg += " - <?php echo $lang_Error_NonNumericHours; ?>\n"
			}
		}

		if (err) {
			alert(msg);
		} else {
			<?php
				$mailConfig 	 = new EmailConfiguration();
				$mailType	 = $mailConfig->getMailType();
				$mailConfigError = false;
				$mailConfigErrorMsg = '';

				if ($mailType == 'sendmail') {

					$sendmailPath = $mailConfig->getSendmailPath();
					$sendmailPath = substr($sendmailPath, 0, strpos($sendmailPath, ' '));
					if (is_file($sendmailPath)) {
						if (!is_executable($sendmailPath)) {
							$mailConfigError = true; 
							$mailConfigErrorMsg = $lang_Error_EmailConfigError_SendmailNotExecutable;
						}
					} elseif (is_link($sendmailPath)) {
						$sendmailPath = readlink($sendmailPath);
						if (is_executable($sendmailPath)) {
							$mailConfigError = true;
							$mailConfigErrorMsg = $lang_Error_EmailConfigError_SendmailNotExecutable;
						}
					} else {
						$mailConfigErrorMsg = $lang_Error_EmailConfigError_SendmailNotFound;
						$mailConfigError = true;
					}

					if ($_SESSION['isAdmin'] == 'Yes') {
						$mailConfigErrorMsg = "$lang_Error_EmailConfigConfirm\\n - $mailConfigErrorMsg\\n   ($sendmailPath)";
					} else {
						$mailConfigErrorMsg = $lang_Error_EmailConfigConfirm;
					}

				} elseif ($mailType == 'smtp') {
					$smtpHost = $mailConfig->getSmtpHost();

					if ($smtpHost == '') {
						$mailConfigError = true;
						$mailConfigErrorMsg = $lang_Error_EmailConfigError_SmtpHostNotDefined;
					}

					/*
					 * TODO: Need to add more SMTP configuration validations here
					 */

					if ($_SESSION['isAdmin'] == 'Yes') {
						$mailConfigErrorMsg = "$lang_Error_EmailConfigConfirm\\n - $mailConfigErrorMsg";
					} else {
						$mailConfigErrorMsg = $lang_Error_EmailConfigConfirm;
					}
				} else {
					$mailConfigErrorMsg = $lang_Error_EmailConfigConfirm;
					$mailConfigError = true;
				}

			?>
			<?php if ($mailConfigError) { ?>
				if (!confirm('<?php echo $mailConfigErrorMsg; ?>')) {
					return;
				}
			<?php } ?>
			document.frmLeaveApp.submit();
		}
	}

	function $(id) {
		return document.getElementById(id);
	}

	function fillAuto(from, to) {
		v1 = YAHOO.OrangeHRM.calendar.parseDate($(from).value);
		v2 = YAHOO.OrangeHRM.calendar.parseDate($(to).value);

		if (!v2 && v1) {
			$(to).value = $(from).value.trim();
		}
	}

	String.prototype.trim = function () {
		regExp = /^\s+|\s+$/g;
		str = this;
		str = str.replace(regExp, "");

		return str;
	}

	function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=LEAVE','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
	}

	/**
	 * Called when a date is selected in the calendar.
	 * Fills the to date if empty.
	 */
	var dateSelectHandler = function() {
	    fillToDate();
	};

	function fillToDate() {
		fillAuto('txtLeaveFromDate', 'txtLeaveToDate');

		if (YAHOO.OrangeHRM.calendar.parseDate($('txtLeaveFromDate').value) && ($('txtLeaveFromDate').value == $('txtLeaveToDate').value)) {
			$('trTime1').className = 'display-table-row';
			$('trTime2').className = 'display-table-row';
			$('trTime3').className = 'display-table-row';
			$('trTime4').className = 'display-table-row';
		} else {
			$('trTime1').className = 'hidden';
			$('trTime2').className = 'hidden';
			$('trTime3').className = 'hidden';
			$('trTime4').className = 'hidden';
		}
	}

	function clearRevertLeave() {
		$("revertLeave").style.display = "none";
	}

	function fillTimes() {
		if (!YAHOO.OrangeHRM.calendar.parseDate($('txtLeaveFromDate').value) || ($('txtLeaveFromDate').value != $('txtLeaveToDate').value)) {
			return false;
		}
		if (($('sltLeaveFromTime').value != '') && ($('sltLeaveToTime').value != '')) {
			fromTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveFromTime').value, YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format);
			toTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveToTime').value, YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format);

			if (fromTime > toTime) {
				return false;
			}

			$('txtLeaveTotalTime').value = (toTime-fromTime)/3600000;
		} else if (($('sltLeaveFromTime').value != '') && extractTimeFromHours($('txtLeaveTotalTime').value)) {
			if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
				return false;
			}

			fromTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveFromTime').value, YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format);
			toTime = fromTime+extractTimeFromHours($('txtLeaveTotalTime').value);

			date = new Date();
			date.setTime(toTime);

			toTimeStr = formatDate(date, YAHOO.OrangeHRM.time.format);
			options = $('sltLeaveToTime').options;

			for (i=0; options.length>i; i++) {
				if (options[i].value == toTimeStr) {
					options[i].selected = true;
					break;
				}
			}
		}

		return true;
	}

	function extractTimeFromHours(str) {
		if (str == '') return false;

		format = /^\s*[0-9]{0,2}(\.[0-9]{1,2}){0,1}\s*$/;

		if (!format.test(str)) return false;

		return str*60*60*1000;
	}

	function doRevertLeave() {
		comment = prompt("<?php echo $lang_Leave_PleaseProvideAReason; ?>...");

		if (!comment) return;

		$("txtCommentC").value = comment;
		$("frmCancelLeave").submit();
	}

	/* Add listener that updates toDate when date is selected */
	function init() {
		YAHOO.OrangeHRM.calendar.cal.selectedEvent.subscribe(dateSelectHandler, YAHOO.OrangeHRM.calendar.cal, true);

		YAHOO.util.Event.addListener($("txtLeaveFromDate"), "change", dateSelectHandler);
		YAHOO.util.Event.addListener($("txtLeaveToDate"), "change", dateSelectHandler);
		YAHOO.util.Event.addListener($("txtLeaveFromDate"), "focus", dateSelectHandler);
		YAHOO.util.Event.addListener($("txtLeaveToDate"), "focus", dateSelectHandler);
		YAHOO.util.Event.addListener($("txtLeaveFromDate"), "blur", dateSelectHandler);
		YAHOO.util.Event.addListener($("txtLeaveToDate"), "blur", dateSelectHandler);

		if ($("revertLeave")) {
			YAHOO.util.Event.addListener($("msgResponseNo"), "click", clearRevertLeave);
			YAHOO.util.Event.addListener($("msgResponseYes"), "click", doRevertLeave);
		}
	}

	YAHOO.OrangeHRM.container.init();
	YAHOO.util.Event.addListener(window, "load", init);

	function numeric(txt) {
		var flag=true;
		var i,code;

		if(txt.value=="") {
   			return false;
		}

		for(i=0;txt.value.length>i;i++) {
			code=txt.value.charCodeAt(i);
   			if(code>=48 && code<=57 || code==46) {
	   			flag=true;
			} else {
	   			flag=false;
	   			break;
	   		}
		}
	return flag;
	}


</script>
<h2>
	<?php
      if (isset($employees) && is_array($employees)) {
		 echo $lang_Leave_Title_Assign_Leave;
		 $modifier = "Leave_Admin_Apply";
		 $btnApply = "assign.gif";
		 $btnApplyMO = "assign_o.gif";
      } else {
      	 echo $lang_Leave_Title_Apply_Leave;
      	 $modifier = "Leave_Apply";
      	 $btnApply = "apply.gif";
		 $btnApplyMO = "apply_o.gif";
      }
     ?>
  <hr/>
</h2>
<?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {

	$expString  = $_GET['message'];
	$col_def = CommonFunctions::getCssClassForMessage($expString);
	$expString = 'lang_Leave_' . $expString;
	if (isset($$expString)) {
?>
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
	</font>
<?php
	}
}

if (isset($exception)) {
	if ($exception->isWarning()) {
		$confirmDate = true;
?>
<div id="duplicateWarning" class="confirmBox">
 	<div class="confirmInnerBox">
	<?php echo $lang_Leave_Error_DuplicateLeaveWarning; ?><br />
	<?php echo $lang_Leave_Error_DuplicateLeaveWarningInstructions; ?>
	</div>
</div>
<?php	} else { ?>
<div id="duplicateError" class="confirmBox">
 	<div class="confirmInnerBox">
	<?php echo $lang_Leave_Error_DuplicateLeaveError; ?><br />
	<?php echo $lang_Leave_Error_DuplicateLeaveErrorInstructions; ?>
	</div>
</div>

<?php
	}
$duplicateLeaves = $exception->getDuplicateLeaveList();
if (!empty($duplicateLeaves) && count($duplicateLeaves) > 0) {
	$empName = $duplicateLeaves[0]->getEmployeeName();
}
?>
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
<th class="tableTopRight"></th>
</tr>

<tr>
<th class="tableMiddleLeft"></th>
<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date; ?></th>
<th width="50px" class="tableMiddleMiddle"><?php echo $lang_Leave_NoOfHours; ?></th>
<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Period; ?></th>
<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType; ?></th>
<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Status; ?></th>
<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Comments; ?></th>
<th class="tableMiddleRight"></th>
</tr>
</thead>
<tbody>

<?php

$j = 0;
if (is_array($duplicateLeaves)) {
	$statusArr = array(Leave::LEAVE_STATUS_LEAVE_REJECTED => $lang_Leave_Common_Rejected,
                       Leave::LEAVE_STATUS_LEAVE_CANCELLED => $lang_Leave_Common_Cancelled,
                       Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL => $lang_Leave_Common_PendingApproval,
                       Leave::LEAVE_STATUS_LEAVE_APPROVED => $lang_Leave_Common_Approved,
                       Leave::LEAVE_STATUS_LEAVE_TAKEN => $lang_Leave_Common_Taken);
	foreach ($duplicateLeaves as $dup) {
		if(!($j%2)) {
			$cssClass = 'odd';
		} else {
			$cssClass = 'even';
		}
		$j++;
		$leaveTime = "";
		$dupStart = $dup->getStartTime();
		$dupEnd = $dup->getEndTime();

		if (!empty($dupStart) && !empty($dupEnd) && ($dupStart != "00:00") && ($dupEnd != "00:00") ) {
			$leaveTime = LocaleUtil::getInstance()->formatTime($dupStart) . ' - ' . LocaleUtil::getInstance()->formatTime($dupEnd);
		}

?>

<tr>
<td class="tableMiddleLeft"></td>
<td class="<?php echo $cssClass; ?>"><?php echo LocaleUtil::getInstance()->formatDate($dup->getLeaveDate()); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveLengthHours(); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $leaveTime; ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveTypeName(); ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $statusArr[$dup->getLeaveStatus()]; ?></td>
<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveComments(); ?></td>
<td class="tableMiddleRight"></td>
</tr>

<?php } } ?>

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
<td class="tableBottomRight"></td>
</tr>
</tfoot>
</table>
<hr />
<?php
}

$prevEmployeeId= (isset($_POST['cmbEmployeeId'])) ? $_POST['cmbEmployeeId'] : "";
$prevLeaveFromDate = (isset($_POST['txtLeaveFromDate'])) ? $_POST['txtLeaveFromDate'] : "";
$prevLeaveToDate = (isset($_POST['txtLeaveToDate'])) ? $_POST['txtLeaveToDate'] : "";
$prevLeaveType = (isset($_POST['sltLeaveType'])) ? $_POST['sltLeaveType'] : "";
$prevToTime = (isset($_POST['sltLeaveToTime'])) ? $_POST['sltLeaveToTime'] : "";
$prevFromTime = (isset($_POST['sltLeaveFromTime'])) ? $_POST['sltLeaveFromTime'] : "";
$prevTotalTime = (isset($_POST['txtLeaveTotalTime'])) ? $_POST['txtLeaveTotalTime'] : "";

$prevComments = (isset($_POST['txtComments'])) ? $_POST['txtComments'] : "";

$timeElementClass = (!empty($prevLeaveFromDate) && ($prevLeaveFromDate == $prevLeaveToDate)) ?
						"display-table-row" : "hidden";
?>
<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=<?php echo $modifier; ?>">

<?php if (isset($confirmDate)) { ?>
	<input type="hidden" name="confirmDate" value="<?php echo $prevLeaveFromDate; ?>"/>
<?php } ?>
  <table border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
    <?php if (isset($role)) { ?>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_EmployeeName; ?></td>
        <td width="25px">&nbsp;</td>
		<td>
		<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
			<input type="text" name="txtEmployeeId" id="txtEmployeeId" disabled value="<?php echo isset($empName) ? $empName : ""; ?>" />
			<input type="hidden" name="cmbEmployeeId" id="cmbEmployeeId" value="<?php echo $prevEmployeeId;?>"/>
			<input type="button" value="..." onclick="returnEmpDetail();" />
		<?php } else if (isset($employees) && is_array($employees)) { ?>
			<select name="cmbEmployeeId" onchange="resetShiftLength();">
	        	<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
				<?php
			   		sort($employees);
			   		foreach ($employees as $employee) {
						$selected = ($prevEmployeeId == $employee[0]) ? "selected" : "";
			  	?>
			 		  	<option <?php echo $selected; ?> value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>
			  <?php } ?>
	  	    </select>
		<?php } ?>
		</td>
	  	<td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
    <?php } ?>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_LeaveType; ?></td>
        <td width="25px">&nbsp;</td>
        <td>
            <select name="sltLeaveType" id="sltLeaveType">
            <?php
                    $skippedLeaveTypesCount = 0;

                    if (is_array($records[1])) {
                        foreach ($records[1] as $record) {
                                $className = get_class($record);

                                if ($className == 'LeaveQuota') {
                                    if ($record->isLeaveQuotaDeleted()) {
                                        $skippedLeaveTypesCount++;
                                        continue;
                                    }
                                }

                                $selected = ($record->getLeaveTypeID() == $prevLeaveType) ? "selected" : "";
          ?>
            <option <?php echo $selected;?> value="<?php echo $record->getLeaveTypeID();?>"><?php echo $record->getLeaveTypeName(); ?></option>
            <?php       }

                        if ($skippedLeaveTypesCount == count($records[1])) { ?>
                                <option value="-1">-- <?php echo $lang_Error_NoLeaveTypes; ?> --</option>
                        <?php }
                } else { ?>
            <option value="-1">-- <?php echo $lang_Error_NoLeaveTypes; ?> --</option>
            <?php } ?>
          </select>

        </td>
        <td width="50px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
     </tr>
     <?php
	  	if (!(is_array($records[1])) && ($modifier == 'Leave_Apply')) {  ?>
	    <tr>
     	<td class="tableMiddleLeft"></td>
     	<td width="75px">&nbsp;</td>
        <td width="25px">&nbsp;</td>
      	<td><?php echo $lang_Leave_Common_LeaveQuotaNotAllocated; ?></td>
    	<td width="25px">&nbsp;</td>
    	<td class="tableMiddleRight"></td>
     </tr> <?php } ?>
     <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_FromDate;?></td>
        <td width="25px">&nbsp;</td>
        <td><?php echo $lang_Leave_Common_ToDate;?></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><input name="txtLeaveFromDate" type="text" id="txtLeaveFromDate"  onchange="fillToDate();" onfocus="fillToDate();" size="10"
        	value="<?php echo $prevLeaveFromDate; ?>"/>
          <input type="button" name="Submit" value="  " class="calendarBtn" />
        </td>
        <td width="25px">&nbsp;</td>
        <td><input name="txtLeaveToDate" type="text" id="txtLeaveToDate"  onchange="fillToDate();" onfocus="fillToDate();" size="10"
        	value="<?php echo $prevLeaveToDate; ?>"/>
          <input type="button" name="Submit" value="  " class="calendarBtn" />
        </td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime1" class="<?php echo $timeElementClass;?>">
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_FromTime;?></td>
        <td width="25px">&nbsp;</td>
        <td><?php echo $lang_Leave_Common_TotalHours;?></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime2" class="<?php echo $timeElementClass;?>">
        <td class="tableMiddleLeft"></td>
        <td><select name="sltLeaveFromTime" type="text" id="sltLeaveFromTime" onchange="fillTimes();" >
        	<option value="" selected ></option>
        	<?php
        		for ($i=$startTime; $i<=$endTime; $i+=$interval) {
        			$timeVal = date('H:i', $i);
        			$selected = ($timeVal == $prevFromTime) ? "selected" : "";
        	?>
        			<option <?php echo $selected; ?> value="<?php echo $timeVal; ?>" ><?php echo LocaleUtil::getInstance()->formatTime($timeVal); ?></option>
        	<?php } ?>
        	</select>
        </td>
        <td width="25px">&nbsp;</td>
        <td><input name="txtLeaveTotalTime" id="txtLeaveTotalTime" size="4" onchange="fillTimes();"
        		value="<?php echo $prevTotalTime; ?>"/></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime3" class="<?php echo $timeElementClass;?>">
      	<td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_ToTime;?></td>
      	<td width="25px">&nbsp;</td>
      	<td>&nbsp;</td>
      	<td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime4" class="<?php echo $timeElementClass;?>">
     	<td class="tableMiddleLeft"></td>
        <td><select name="sltLeaveToTime" type="text" id="sltLeaveToTime" onchange="fillTimes();" >
        	<option value="" selected ></option>
        	<?php
        		for ($i=$startTime; $i<=$endTime; $i+=$interval) {
        			$timeVal = date('H:i', $i);
        			$selected = ($timeVal == $prevToTime) ? "selected" : "";

        		?>
        			<option <?php echo $selected; ?> value="<?php echo $timeVal; ?>" ><?php echo LocaleUtil::getInstance()->formatTime($timeVal); ?></option>
        	<?php } ?>
        	</select>
        </td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
      	<td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_Comment; ?></td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr valign="top">
        <td class="tableMiddleLeft"></td>
        <td><textarea name="txtComments" id="txtComments"><?php echo $prevComments;?></textarea></td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/icons/<?php echo $btnApply; ?>';" onmouseover="this.src='../../themes/beyondT/icons/<?php echo $btnApplyMO; ?>';" src="../../themes/beyondT/icons/<?php echo $btnApply; ?>" /></td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
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
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
</form>
<div id="cal1Container" style="position:absolute;" ></div>
