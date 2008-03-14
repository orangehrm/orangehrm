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

$year = $records[0];
?>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">

function cancelAddPayPeriod() {
	window.location = '?benefitcode=Benefits&action=List_Benefits_Schedule&year=<?php echo $year; ?>';
}

function addPayPeriod() {

	err = false;
	msg = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

	startDate = strToDate($('txtPayPeriodFromDate').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate($('txtPayPeriodToDate').value, YAHOO.OrangeHRM.calendar.format);

	if (!startDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidPayPeriodStartDate; ?>\n"
	}

	if (!endDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidPayPeriodEndDate; ?>\n"
	}

	if (startDate > endDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidPayPeriod; ?>\n"
	}

	closeDate = strToDate($('txtPayPeriodCloseDate').value, YAHOO.OrangeHRM.calendar.format);
	if (!closeDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidClosingDate; ?>\n"
	}

	dueDate = strToDate($('txtPayPeriodTimesheetDueDate').value, YAHOO.OrangeHRM.calendar.format);
	if (!dueDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidDueDate; ?>\n"
	}

	checkDate = strToDate($('txtPayPeriodCheckDate').value, YAHOO.OrangeHRM.calendar.format);
	if (!checkDate) {
		err = true;
		msg += " - <?php echo $lang_Benefits_Common_InvalidCheckDate; ?>\n"
	}

	if (err) {
		alert(msg);
	} else {
		document.frmAddPayPeriod.submit();
	}
}

YAHOO.OrangeHRM.container.init();
</script>
<h2>
	<?php echo $lang_Benefits_DefinePayDateForPaySchedule; ?>
	<hr/>
</h2>
<form action="?benefitcode=Benefits&action=Add_Pay_Period" method="post" name="frmAddPayPeriod" id="frmAddPayPeriod">
<table border="0" cellpadding="2" cellspacing="0">
	<thead>
	  	<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_PayPeriod; ?></td>
			<td>
				<input name="txtPayPeriodFromDate" type="text" id="txtPayPeriodFromDate"  size="10"/>
          		<input type="button" name="Date" value="  " class="calendarBtn" />
          	</td>
          	<td></td>
          	<td><?php echo $lang_Common_To; ?></td>
			<td>
				<input name="txtPayPeriodToDate" type="text" id="txtPayPeriodToDate"  size="10"/>
          		<input type="button" name="Date" value="  " class="calendarBtn" />
          	</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_PayPeriodCloses; ?></td>
			<td>
				<input name="txtPayPeriodCloseDate" type="text" id="txtPayPeriodCloseDate"  size="10"/>
          		<input type="button" name="Date" value="  " class="calendarBtn" />
          	</td>
          	<td></td>
          	<td></td>
			<td></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_TimesheetAprovalDue; ?></td>
			<td>
				<input name="txtPayPeriodTimesheetDueDate" type="text" id="txtPayPeriodTimesheetDueDate"  size="10"/>
          		<input type="button" name="Date" value="  " class="calendarBtn" />
          	</td>
          	<td></td>
          	<td></td>
			<td></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_CheckDate; ?></td>
			<td>
				<input name="txtPayPeriodCheckDate" type="text" id="txtPayPeriodCheckDate"  size="10"/>
          		<input type="button" name="Date" value="  " class="calendarBtn" />
          	</td>
          	<td></td>
          	<td></td>
			<td>
				<img onClick="addPayPeriod();"
		             style="margin-top:10px;"
		             onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';"
		             onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
		             src="../../themes/beyondT/pictures/btn_save.gif" alt="Save" />
		        <img onClick="cancelAddPayPeriod();"
		             style="margin-top:10px;"
		             onMouseOut="this.src='../../themes/beyondT/icons/cancel.gif';"
		             onMouseOver="this.src='../../themes/beyondT/icons/cancel_o.gif';"
		             src="../../themes/beyondT/icons/cancel.gif" alt="Cancel" />
		    </td>
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
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
</form>