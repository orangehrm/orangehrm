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

$timeEvent = $records[1];
$employee = $records[2];

$employeeName = "{$employee[2]} {$employee[1]}";
$punchTypeNames = array(TimeEvent::TIME_EVENT_PUNCH_IN=>$lang_Time_PunchIn, TimeEvent::TIME_EVENT_PUNCH_OUT=>$lang_Time_PunchOut);

$punchTypeName = $punchTypeNames[$records[0]];
$timeEventId = null;
$lastPunch = null;

$note = "";

if ($timeEvent != null) {
	switch ($records[0]) {
		case TimeEvent::TIME_EVENT_PUNCH_IN  : $lastPunch = LocaleUtil::getInstance()->formatDateTime($timeEvent->getEndTime());
											   $puchInfo = "$lang_Time_LastPunchOut {$lastPunch}";
											   break;
		case TimeEvent::TIME_EVENT_PUNCH_OUT : $lastPunch = LocaleUtil::getInstance()->formatDateTime($timeEvent->getStartTime());
											   $puchInfo = "$lang_Time_LastPunchIn {$lastPunch}";
											   $timeEventId = $timeEvent->getTimeEventId();
											   $note = $timeEvent->getDescription();
											   break;
	}
} else {
	$puchInfo = "";
}

?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
	dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;

	commonAction = "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=";
	<?php if (isset($timeEventId)) { ?>
	submitAction = "Punch_Out";
	<?php } else { ?>
	submitAction = "Punch_In";
	<?php } ?>

	<?php if (isset($lastPunch)) { ?>
	lastPunch = strToTime("<?php echo $lastPunch; ?>", dateTimeFormat);
	<?php } else { ?>
	lastPunch = false;
	<?php }?>

	function validate() {
		startTime = false;

		err = false;

		if (!strToTime($("txtDate").value+" "+$("txtTime").value, dateTimeFormat)) {
			alert("<?php echo $lang_Time_Errors_InvalidDateOrTime; ?>");
			err = true;
		}

		if ($("startTime")) {
			startTime = strToTime($("startTime").value, dateTimeFormat);
			endTime = strToTime($("txtDate").value+" "+$("txtTime").value, dateTimeFormat);

			if (0 >= (endTime-startTime)) {
				alert("<?php echo $lang_Time_Errors_ZeroOrNegativeDurationTimeEventsAreNotAllowed; ?>");
				err = true;
			}
		}

		return !err;
	}

	function punchTime() {
		if (validate()) {
			$("frmPunchTime").action=commonAction+submitAction;
			$("frmPunchTime").submit();
		}
	}

	function init() {

		$("txtDate").value = formatDate(new Date(), YAHOO.OrangeHRM.calendar.format);
		$("txtTime").value = formatDate(new Date(), YAHOO.OrangeHRM.time.format);

		oButtonPunch = new YAHOO.widget.Button("btnPunch", {onclick: {fn:punchTime}});

	}

	YAHOO.OrangeHRM.container.init();
	YAHOO.util.Event.addListener(window, "load", init);
</script>
<h2>
<?php echo $lang_Time_PunchInPunchOutTitle; ?>
<hr/>
</h2>

<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
<form name="frmPunchTime" id="frmPunchTime" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
	<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Leave_Common_Date; ?></td>
        	<td>
	        	<input type="text" name="txtDate" id="txtDate" size="10"/>
	        	<input type="button" name="btnSelDate" id="btnSelDate" value="  " class="calendarBtn"/>
        	</td>
        	<td class="tableMiddleRight"></td>
  		</tr>
  		<tr>
  			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Common_Time; ?></td>
        	<td><input type="text" name="txtTime" id="txtTime" /></td>
        	<td class="tableMiddleRight"></td>
  		</tr>
  		<tr>
  			<td class="tableMiddleLeft"></td>
        	<td><?php echo $lang_Common_Note; ?></td>
        	<td>
        		<textarea name="txtNote" id="txtNote" rows="5" cols="50"><?php echo $note; ?></textarea>
        	</td>
        	<td class="tableMiddleRight"></td>
  		</tr>
  		<tr>
  			<td class="tableMiddleLeft"></td>
        	<td></td>
        	<td><input type="button" name="btnPunch" id="btnPunch" value="<?php echo $punchTypeName; ?>" /></td>
        	<td class="tableMiddleRight"></td>
  		</tr>
  	</tbody>
  	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
	</table>

	<?php if (isset($timeEventId)) { ?>
	<input type="hidden" name="startTime" id="startTime" value="<?php echo LocaleUtil::getInstance()->formatDateTime($timeEvent->getStartTime()); ?>" />
	<input type="hidden" name="timeEventId" id="timeEventId" value="<?php echo $timeEventId; ?>" />
	<?php } ?>
</form>
<div id="punchInfo">
	<?php echo $puchInfo; ?>
</div>
<div id="cal1Container" style="position:absolute;" ></div>