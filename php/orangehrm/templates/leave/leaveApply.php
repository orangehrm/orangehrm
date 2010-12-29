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

 $token = $records['token'];
?>
<script type="text/javascript" src="../../scripts/jquery/jquery.js"></script>
<link href="../../themes/orange/css/jquery/jquery.autocomplete.css" rel="stylesheet" type="text/css"/>
<link href="../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css"/>


<link href="../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="../../scripts/jquery/ui/ui.core.js"></script>
<script type="text/javascript" src="../../scripts/jquery/ui/ui.datepicker.js"></script>
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){

   //textbox changes
   jQuery("#txtLeaveFromDate").change(function() {
		 var fromDateValue 	= 	trim(jQuery("#txtLeaveFromDate").val());
		 if(fromDateValue == ''){
			 jQuery("#txtLeaveFromDate").val('YYYY-mm-DD');
		 }else{
          var toDateValue	=	trim(jQuery("#txtLeaveToDate").val());
          if(toDateValue == "" || toDateValue == "YYYY-mm-DD") {
             jQuery("#txtLeaveToDate").val(fromDateValue);
             if(currFromDate != jQuery("#txtLeaveToDate").val()) {
                prevFromDate = currFromDate;
                currFromDate = jQuery("#txtLeaveToDate").val();
             }
          }

          if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
   });

   //Bind blur event of From Date
	 jQuery("#txtLeaveFromDate").blur(function() {
		 var fromDateValue 	= 	trim(jQuery("#txtLeaveFromDate").val());
		 if(fromDateValue == ''){
			 jQuery("#txtLeaveFromDate").val('YYYY-mm-DD');
		 }else{
          var toDateValue	=	trim(jQuery("#txtLeaveToDate").val());
          if(toDateValue == "" || toDateValue == "YYYY-mm-DD") {
             jQuery("#txtLeaveToDate").val(fromDateValue);
             if(currFromDate != jQuery("#txtLeaveToDate").val()) {
                prevFromDate = currFromDate;
                currFromDate = jQuery("#txtLeaveToDate").val();
             }
          }

          if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
	 });

    jQuery("#btFromDate").blur(function(){
      jQuery("#txtLeaveFromDate").focus();
    });

    jQuery("#btToDate").focus(function() {
		 var todate 	= 	trim(jQuery("#txtLeaveToDate").val());
		 if(todate != '' && todate != "YYYY-mm-DD"){
         if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
    });

    jQuery("#txtLeaveToDate").focus(function() {
		 var todate 	= 	trim(jQuery("#txtLeaveToDate").val());
		 if(todate != '' && todate != "YYYY-mm-DD"){
         if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
	 });

    jQuery("#txtLeaveToDate").blur(function() {
		 var todate 	= 	trim(jQuery("#txtLeaveToDate").val());
		 if(todate != '' && todate != "YYYY-mm-DD"){
         if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
	 });

    jQuery("#txtLeaveToDate").change(function() {
		 var todate 	= 	trim(jQuery("#txtLeaveToDate").val());
		 if(todate != '' && todate != "YYYY-mm-DD"){
         if(trim(jQuery("#txtLeaveToDate").val()) == trim(jQuery("#txtLeaveFromDate").val())){
             jQuery("#trTime1").show();
          } else {
             jQuery("#trTime1").hide();
          }
	    }
	 });

    jQuery("#btToDate").blur(function() {
      jQuery("#txtLeaveToDate").focus();
	 });
});


</script>

<script type="text/javascript">
//<![CDATA[
	var shiftLength = <?php echo $shiftLength; ?>;
	var empShifts = new Array();
<?php
	if (isset($records['allEmpWorkshits'])) {
		foreach($records['allEmpWorkshits'] as $empId=>$shiftLen) {
			echo "\t" . 'empShifts["' . $empId . '"] = ' . $shiftLen . ";\n";
		}
	}
?>

    var employeeSearchList = new Array();

    function showAutoSuggestTip(obj) {
        if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
            obj.value = '';
            obj.style.color = '#000000';
        }
    }

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

        for (i in employeeSearchList) {
            if ($('txtEmployeeId').value == employeeSearchList[i][0]) {
                $('cmbEmployeeId').value = employeeSearchList[i][2];
                break;
            }
        }

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
				}

			} else if (($('sltLeaveFromTime').value == '' || $('sltLeaveToTime').value == '') && $('txtLeaveTotalTime').value == '') {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod; ?>\n"
			} else if (!numeric($('txtLeaveTotalTime'))) {
				err = true;
				msg += " - <?php echo $lang_Error_NonNumericHours; ?>\n"
			}

			<?php if ($records['isEss']) { ?>
			if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?> (" + "<?php echo $lang_Leave_Common_WorkshiftLengthIs;?> " + shiftLength + " <?php echo $lang_Common_Hours; ?>) \n";
			}
			<?php } ?>

		}

		if ($('txtComments').value.length > <?php echo LeaveRequests::MAX_COMMENT_LENGTH; ?>){
			err = true;
			msg += " - <?php echo sprintf($lang_Leave_LeaveCommentTooLong, LeaveRequests::MAX_COMMENT_LENGTH); ?>\n"
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
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=LEAVE','Employees','height=450,width=400,scrollbars=1');
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
			$('trTime1').className = 'show';
			$('trTime2').className = 'show';
			$('trTime3').className = 'show';
			$('trTime4').className = 'show';
		} else {
			$('trTime1').className = 'hide';
			$('trTime2').className = 'hide';
			$('trTime3').className = 'hide';
			$('trTime4').className = 'hide';
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

//]]>
</script>
<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>
<?php
  if (isset($employees) && is_array($employees)) {
     $heading = $lang_Leave_Title_Assign_Leave;
     $modifier = "Leave_Admin_Apply";
     $btnClass = 'assignbutton';
     $btnTitle = $lang_Common_Assign;
  } else {
     $heading = $lang_Leave_Title_Apply_Leave;
     $modifier = "Leave_Apply";
     $btnClass = 'applybutton';
     $btnTitle = $lang_Common_Apply;
  }

if (isset($exception)) {
	if ($exception->isWarning()) {
		$confirmDate = true;
?>
<div id="duplicateWarning" class="confirmBox" style="margin-left:18px;">
 	<div class="confirmInnerBox">
	<?php echo $lang_Leave_Error_DuplicateLeaveWarning; ?><br />
	<?php echo $lang_Leave_Error_DuplicateLeaveWarningInstructions; ?>
	</div>
</div>
<?php	} else { ?>
<div id="duplicateError" class="confirmBox" style="margin-left:18px;">
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

<table border="0" cellpadding="0" cellspacing="0" class="simpleList" style="margin-left:18px;">
<thead>
	<tr>
		<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Date; ?></th>
		<th width="50px" class="tableMiddleMiddle"><?php echo $lang_Leave_NoOfHours; ?></th>
		<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Period; ?></th>
		<th width="90px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType; ?></th>
		<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Status; ?></th>
		<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_Comments; ?></th>
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
                       Leave::LEAVE_STATUS_LEAVE_TAKEN => $lang_Leave_Common_Taken,
                       Leave::LEAVE_STATUS_LEAVE_WEEKEND => $lang_Leave_Common_Weekend,
                       Leave::LEAVE_STATUS_LEAVE_HOLIDAY => $lang_Leave_Common_Weekend);
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
		<td class="<?php echo $cssClass; ?>"><?php echo LocaleUtil::getInstance()->formatDate($dup->getLeaveDate()); ?></td>
		<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveLengthHours(); ?></td>
		<td class="<?php echo $cssClass; ?>"><?php echo $leaveTime; ?></td>
		<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveTypeName(); ?></td>
		<td class="<?php echo $cssClass; ?>"><?php echo $statusArr[$dup->getLeaveStatus()]; ?></td>
		<td class="<?php echo $cssClass; ?>"><?php echo $dup->getLeaveComments(); ?></td>
	</tr>

<?php } } ?>

</tbody>
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
						"show" : "hide";
?>

<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $heading;?></h2></div>

        <?php if (isset($_GET['message']) && $_GET['message'] != 'xx') {
                $message =  $_GET['message'];
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $messageStr = "lang_Leave_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$messageStr)) ? $$messageStr: ''; ?></span>
            </div>
        <?php } ?>

<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=<?php echo $modifier; ?>">
<input type="hidden" value="<?php echo $token;?>" name="token" />
<?php if (isset($confirmDate)) { ?>
	<input type="hidden" name="confirmDate" value="<?php echo $prevLeaveFromDate; ?>"/>
<?php } ?>

    <?php if (isset($role)) { ?>
      <?php if(($role == authorize::AUTHORIZE_ROLE_ADMIN) || ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR)){ ?>
        <label for="cmbEmployeeId"><?php echo $lang_Leave_Common_EmployeeName; ?><span class="required">*</span></label>
        <div>
        <input type="hidden" name="cmbEmployeeId" id="cmbEmployeeId" value="<?php echo isset($prevEmployeeId) ? $prevEmployeeId : ""; ?>" />
        <div class="yui-ac" id="employeeSearchAC" style="float: left">
        <input name="txtEmployeeId" autocomplete="off" class="yui-ac-input" id="txtEmployeeId" type="text" value="<?php echo isset($empName) ? CommonFunctions::escapeHtml($empName) : ""; ?>" tabindex="2" onfocus="showAutoSuggestTip(this)" style="color: #999999" />
              <div class="yui-ac-container" id="employeeSearchACContainer" style="top: 28px; left: 10px;">
              <div style="display: none; width: 159px; height: 0px; left: 100em" class="yui-ac-content">
              <div style="display: none;" class="yui-ac-hd"></div>
              <div class="yui-ac-bd">
                    <ul>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                          <li style="display: none;"></li>
                        </ul>
                      </div>
                     <div style="display: none;" class="yui-ac-ft"></div>
                    </div>
                   <div style="width: 0pt; height: 0pt;" class="yui-ac-shadow"></div>
              </div>
        </div>
        </div>
        <br class="clear"/>
      <?php } ?>
    <?php } ?>

    <label for="sltLeaveType"><?php echo $lang_Leave_Common_LeaveType; ?></label>
    <select name="sltLeaveType" id="sltLeaveType" class="formSelect">
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

                    $selected = ($record->getLeaveTypeID() == $prevLeaveType) ? 'selected="selected"' : "";
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
    <br class="clear"/>

     <?php
	  	if (!(is_array($records[1])) && ($modifier == 'Leave_Apply')) {  ?>
            <div class="notice"><?php echo $lang_Leave_Common_LeaveQuotaNotAllocated; ?></div>
            <br class="clear"/>
     <?php } ?>

        <label for="txtLeaveFromDate"><?php echo $lang_Leave_Common_FromDate; ?><span class="required">*</span></label>
        <input name="txtLeaveFromDate" type="text" id="txtLeaveFromDate" size="10"
            value="<?php echo $prevLeaveFromDate; ?>" class="formDateInput"/>
          <input type="button" name="Submit" value="  " class="calendarBtn" id="btFromDate"/>
        <br class="clear"/>

        <label for="txtLeaveToDate"><?php echo $lang_Leave_Common_ToDate; ?><span class="required">*</span></label>
        <input name="txtLeaveToDate" type="text" id="txtLeaveToDate" size="10"
        	value="<?php echo $prevLeaveToDate; ?>" class="formDateInput"/>
          <input type="button" name="Submit" value="  " class="calendarBtn" id="btToDate"/>
        <br class="clear"/>

      <div id="trTime1" class="<?php echo $timeElementClass;?>">
        <label for="sltLeaveFromTime"><?php echo $lang_Leave_Common_FromTime; ?></label>
        <select name="sltLeaveFromTime" id="sltLeaveFromTime" onchange="fillTimes();"
                class="formTimeSelect">
            <option value=""></option>
            <?php
                for ($i=$startTime; $i<=$endTime; $i+=$interval) {
                    $timeVal = date('H:i', $i);
                    $selected = ($timeVal == $prevFromTime) ? 'selected="selected"' : "";
            ?>
                    <option <?php echo $selected; ?> value="<?php echo $timeVal; ?>" ><?php echo LocaleUtil::getInstance()->formatTime($timeVal); ?></option>
            <?php } ?>
            </select>
        <label for="sltLeaveToTime"><?php echo $lang_Leave_Common_ToTime; ?></label>
        <select name="sltLeaveToTime" id="sltLeaveToTime" onchange="fillTimes();"
                class="formTimeSelect">
            <option value=""></option>
            <?php
                for ($i=$startTime; $i<=$endTime; $i+=$interval) {
                    $timeVal = date('H:i', $i);
                    $selected = ($timeVal == $prevToTime) ? 'selected="selected"' : "";

                ?>
                    <option <?php echo $selected; ?> value="<?php echo $timeVal; ?>" ><?php echo LocaleUtil::getInstance()->formatTime($timeVal); ?></option>
            <?php } ?>
            </select>
        <br class="clear"/>

        <label for="txtLeaveTotalTime"><?php echo $lang_Leave_Common_TotalHours; ?></label>
        <input name="txtLeaveTotalTime" id="txtLeaveTotalTime" size="4" onchange="fillTimes();"
                value="<?php echo $prevTotalTime; ?>" class="formInputText" style="width:3em;"/>
        <br class="clear"/>
      </div>
      <div id="trTime2" class="<?php echo $timeElementClass;?>">
      </div>

      <div id="trTime3" class="<?php echo $timeElementClass;?>">

      </div>
      <div id="trTime4" class="<?php echo $timeElementClass;?>">
      </div>
      <br class="clear"/>
      <label for="txtComments"><?php echo $lang_Leave_Common_Comment; ?></label>
      <textarea name="txtComments" id="txtComments" class="formTextArea" rows="3" cols="20"
        ><?php echo $prevComments;?></textarea>
      <br class="clear"/>
        <div class="formbuttons">
            <input type="button" class="<?php echo $btnClass;?>" id="saveBtn"
                onclick="addSave();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $btnTitle;?>" title="<?php echo $btnTitle;?>"/>
        </div>
</form>
</div>
<div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }

        <?php
            if($employees){
                $i = 0;
                foreach ($employees as $record) {
                    foreach ($record as $pos => $item) {
                        $record[$pos] = CommonFunctions::escapeForJavascript($item);
                    }
        ?>
                employeeSearchList[<?php echo $i++; ?>] = new Array('<?php echo implode("', '", $record); ?>');
        <?php
                }
            }

        ?>


        YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
            // Instantiate second JS Array DataSource
            this.oACDS = new YAHOO.widget.DS_JSArray(employeeSearchList);
            // Instantiate second AutoComplete
            this.oAutoComp = new YAHOO.widget.AutoComplete('txtEmployeeId','employeeSearchACContainer', this.oACDS);
            this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
            this.oAutoComp.typeAhead = false;
            this.oAutoComp.useShadow = true;
            this.oAutoComp.forceSelection = true;
            this.oAutoComp.formatResult = function(oResultItem, sQuery) {
                var sMarkup = oResultItem[0] + "<br />" + oResultItem[1] .fontsize(-1).fontcolor('#999999')  + "&nbsp;";
                return (sMarkup);
            };
        };
//]]>
</script>
</div>
<div id="cal1Container" style="position:absolute;" ></div>