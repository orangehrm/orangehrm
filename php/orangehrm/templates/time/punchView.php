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

if (isset($records['attRecord'])) {
	$punchIn = false;
	$punchTypeName = $lang_Time_Out;
	$heading = $lang_Time_Heading_PunchOut;
	/* Punch Info: Begins */
	$punchInfo = $lang_Time_LastPunchIn.' '.$records['attRecord'][0]->getInDate().' '.$records['attRecord'][0]->getInTime();
	$timestampDiff = $records['attRecord'][0]->getTimestampDiff();
	
	$punchNote = $records['attRecord'][0]->getInNote();
	if (!empty($punchNote)) {
		$punchInfo .= ' '."($punchNote)";
	}
	/* Punch Info: Ends */
} else {
	$punchIn = true;
	$punchTypeName = $lang_Time_In;
	$heading = $lang_Time_Heading_PunchIn;
	$punchInfo = '';
}

if ($records['message'] == 'save-success') {
	$records['message'] = $lang_Time_PunchSaving_SUCCESS;
} elseif ($records['message'] == 'save-failure') {
	$records['message'] = $lang_Time_PunchSaving_FAILURE;
} elseif ($records['message'] == 'overlapping-failure') {
	$records['message'] = $lang_Time_Attendance_Overlapping;
} else {
	$records['message'] = null;
}

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">

	dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;

	function validate() {

		errFlag = false;
		
		if (!strToTime($("txtDate").value+" "+$("txtTime").value, dateTimeFormat)) {
			alert("<?php echo $lang_Time_Errors_InvalidDateOrTime; ?>");
			errFlag = true;
		}
		
		punchedTimestamp = strToTime($("txtDate").value+" "+$("txtTime").value, dateTimeFormat);
		maxTimestamp = strToTime($("txtDate").value+" 24:00", dateTimeFormat);

		if (punchedTimestamp >= maxTimestamp) {
			alert("<?php echo $lang_Time_Errors_InvalidMaxTime; ?>");
			errFlag = true;
		}

		<?php if (!$punchIn) { ?>

		var inTime = strToTime("<?php echo $records['attRecord'][0]->getInDate(); ?>"+" "+"<?php echo $records['attRecord'][0]->getInTime(); ?>", dateTimeFormat);
		var outTime = strToTime($("txtDate").value+" "+$("txtTime").value, dateTimeFormat);

		if (inTime >= outTime) {
			alert("<?php echo $lang_Time_Attendance_InvalidOutTime; ?>");
			errFlag = true;
		}

		<?php } ?>
		
		if ($('txtNote').value.length > 250) {
			alert("<?php echo $lang_Time_Attendance_NoteTooLong; ?>");
			errFlag = true;
		}

		return !errFlag;
	}

	function punchTime() {
		if (validate()) {
			$("frmPunchTime").submit();
		}
	}

</script>
    <div class="formpage">
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $heading; ?></h2></div>

<?php if (isset($records['message'])) { ?>
    <div class="messagebar">
        <span class="<?php echo $records['messageType']; ?>"><?php echo $records['message']; ?></span>
    </div>
<?php } ?>

<form name="frmPunchTime" id="frmPunchTime" method="post" action="?timecode=Time&action=Save_Punch">
   <input type="text" value="<?php echo $token?>" name="token" />
	<?php if (!$punchIn) { ?>
	<input type="hidden" name="hdnAttendanceId" value="<?php echo $records['attRecord'][0]->getAttendanceId(); ?>" />
	<input type="hidden" name="txtInDate" value="<?php echo $records['attRecord'][0]->getInDate(); ?>" />
	<input type="hidden" name="txtInTime" value="<?php echo $records['attRecord'][0]->getInTime(); ?>" />
	<?php } ?>
	<input type="hidden" name="hdnEmployeeId" value="<?php echo $records['empId']; ?>" />

	<table border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td></td>
			<td><?php echo $lang_Leave_Common_Date; ?></td>
        	<td>
         	<?php if ($records['editMode']) { ?>
        	<input type="text" name="<?php echo ($punchIn?'txtInDate':'txtOutDate'); ?>" id="txtDate" size="10" value="<?php echo $records['currentDate']; ?>" />
        	<input type="button" name="btnSelDate" id="btnSelDate" value="  " class="calendarBtn" style="display: inline;margin:0;float:none;"/>
        	<?php } else {
        		echo $records['currentDate'];
        	?>
        	<input type="hidden" name="<?php echo ($punchIn?'txtInDate':'txtOutDate'); ?>" id="txtDate" value="<?php echo $records['currentDate']; ?>" />
            <?php } ?>
        	</td>
        	<td></td>
  		</tr>
  		<tr>
  			<td></td>
			<td><?php echo $lang_Common_Time; ?></td>
        	<td>
        	<?php if ($records['editMode']) { ?>
        	<input type="text" name="<?php echo ($punchIn?'txtInTime':'txtOutTime'); ?>" id="txtTime" value="<?php echo $records['currentTime']; ?>" size="10" />
        	<span class="timeFormatHint"><?php echo $records['timeInputHint']; ?></span>
        	<?php } else {
        		echo $records['currentTime'];
        	?>
        	<input type="hidden" name="<?php echo ($punchIn?'txtInTime':'txtOutTime'); ?>" id="txtTime" value="<?php echo $records['currentTime']; ?>" />
        	<?php } ?>
        	</td>
        	<td></td>
  		</tr>
  		<tr>
  			<td></td>
        	<td><?php echo $lang_Common_Note; ?></td>
        	<td>
        		<textarea name="<?php echo ($punchIn?'txtInNote':'txtOutNote'); ?>" id="txtNote" rows="5" cols="50"></textarea>
        	</td>
        	<td></td>
  		</tr>
  		<tr>
  			<td></td>
        	<td></td>
        	<td>
        	
        	<?php if (isset($timestampDiff)) { ?>        		
        	<input type="hidden" name="hdnTimestampDiff" id="hdnTimestampDiff" value="<?php echo $timestampDiff; ?>" />        	
        	<?php } ?>        	
        	
            <input type="button" class="punchbutton" name="btnPunch" id="btnPunch"
                    onclick="punchTime()"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $punchTypeName; ?>" />
        	<td></td>
  		</tr>
  	</tbody>
	</table>

</form>
<div id="punchInfo" style="padding:5px">
	<?php echo $punchInfo; ?>
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
