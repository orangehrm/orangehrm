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

?>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script src="../../scripts/time.js"></script>
<script>

	var shiftLength = <?php echo $shiftLength; ?>;

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

				if (fromTime > toTime) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_ToTimeBeforeFromTime; ?>\n"
				}
			} else if (($('sltLeaveFromTime').value != '') && extractTimeFromHours($('txtLeaveTotalTime').value)) {
				if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
					err = true;
					msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?>\n"
				}
			} else if (extractTimeFromHours($('txtLeaveTotalTime').value) > shiftLength*60*60*1000) {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_TotalTimeMoreThanADay; ?>\n"
			} else if (($('sltLeaveFromTime').value == '') || ($('sltLeaveToTime').value == '')) {
				err = true;
				msg += " - <?php echo $lang_Leave_Error_PleaseSpecifyEitherTotalTimeOrTheTimePeriod; ?>\n"
			}
		}

		if (err) {
			alert(msg);
		} else {
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

			fromTime = strToTime($('txtLeaveFromDate').value+" "+$('sltLeaveFromTime').value, YAHOO.OrangeHRM.calendar.format);
			toTime = fromTime+extractTimeFromHours($('txtLeaveTotalTime').value, YAHOO.OrangeHRM.calendar.format);

			date = new Date();
			date.setTime(toTime);

			toTimeStr = formatDate(date, "HH:mm");
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

		format = /^\s*[0-9]{0,2}(\.[0-9]{2}){0,1}\s*$/;

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
if (isset($previousLeave) && (($previousLeave->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_TAKEN) || ($previousLeave->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_APPROVED))) {
 ?>
 <div id="revertLeave" class="confirmBox">
 	<span class="confirmInnerBox">
	<?php echo $lang_Leave_DoYouWantToCancelTheLeaveYouJustAssigned; ?> <span id="msgResponseYes" class="selectable"><?php echo $lang_Common_Yes; ?></span> <span id="msgResponseNo" class="selectable" ><?php echo $lang_Common_No; ?></a>
	</span>
</div>
<form id="frmCancelLeave" name="frmCancelLeave" method="post" action="?leavecode=Leave&action=Leave_Request_ChangeStatus">
	<input type="hidden" name="id[]" id="idC" value="<?php echo $previousLeave->getLeaveRequestId(); ?>" />
	<input type="hidden" name="cmbStatus[]" id="cmbStatusC" value="<?php echo Leave::LEAVE_STATUS_LEAVE_CANCELLED; ?>"/>
	<input type="hidden" name="txtComment[]" id="txtCommentC" value="" />
</form>
<?php } ?>
<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=<?php echo $modifier; ?>">
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
			<input type="text" name="txtEmployeeId" id="txtEmployeeId" disabled />
			<input type="hidden" name="cmbEmployeeId" id="cmbEmployeeId" />
			<input type="button" value="..." onclick="returnEmpDetail();" />
		<?php } else if (isset($employees) && is_array($employees)) { ?>
			<select name="cmbEmployeeId">
	        	<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
				<?php
			   		sort($employees);
			   		foreach ($employees as $employee) {
			  	?>
			 		  	<option value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>
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
        <td><select name="sltLeaveType" id="sltLeaveType">
            <?php
	  	if (is_array($records[1])) {
	  	 	foreach ($records[1] as $record) {
	  ?>
            <option value="<?php echo $record->getLeaveTypeID();?>"><?php echo $record->getLeaveTypeName(); ?></option>
            <?php  }
			} else {?>
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
        <td><input name="txtLeaveFromDate" type="text" id="txtLeaveFromDate"  onchange="fillToDate();" onfocus="fillToDate();" size="10"/>
          <input type="button" name="Submit" value="  " class="calendarBtn" />
        </td>
        <td width="25px">&nbsp;</td>
        <td><input name="txtLeaveToDate" type="text" id="txtLeaveToDate"  onchange="fillToDate();" onfocus="fillToDate();" size="10" />
          <input type="button" name="Submit" value="  " class="calendarBtn" />
        </td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime1" class="hidden">
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_FromTime;?></td>
        <td width="25px">&nbsp;</td>
        <td><?php echo $lang_Leave_Common_TotalHours;?></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime2" class="hidden">
        <td class="tableMiddleLeft"></td>
        <td><select name="sltLeaveFromTime" type="text" id="sltLeaveFromTime" onchange="fillTimes();" >
        	<option value="" selected ></option>
        	<?php
        		for ($i=$startTime; $i<=$endTime; $i+=$interval) { ?>
        			<option value="<?php echo date('H:i', $i); ?>" ><?php echo LocaleUtil::getInstance()->formatTime(date('H:i', $i)); ?></option>
        	<?php } ?>
        	</select>
        </td>
        <td width="25px">&nbsp;</td>
        <td><input name="txtLeaveTotalTime" id="txtLeaveTotalTime" size="4" onchange="fillTimes();" /></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime3" class="hidden">
      	<td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_ToTime;?></td>
      	<td width="25px">&nbsp;</td>
      	<td>&nbsp;</td>
      	<td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr id="trTime4" class="hidden">
     	<td class="tableMiddleLeft"></td>
        <td><select name="sltLeaveToTime" type="text" id="sltLeaveToTime" onchange="fillTimes();" >
        	<option value="" selected ></option>
        	<?php
        		for ($i=$startTime; $i<=$endTime; $i+=$interval) { ?>
        			<option value="<?php echo date('H:i', $i); ?>" ><?php echo date('H:i', $i); ?></option>
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
        <td><textarea name="txtComments" id="txtComments"></textarea></td>
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
