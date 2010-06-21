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
$payPeriod = $records[1];
$token = $records['token'];
?>
<script type="text/javascript">
//<![CDATA[

function goBack() {
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
//]]>
</script>

<div class="formpage2col">

	<div class="navigation">
		<input type="button" class="backbutton"
			onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Back;?>" />
	</div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Benefits_EditPayDateForPaySchedule;?></h2></div>

<form action="?benefitcode=Benefits&amp;action=Edit_Pay_Period" method="post" name="frmAddPayPeriod" id="frmAddPayPeriod">
    <input name="txtPayPeriodId" type="hidden" id="txtPayPeriodId" value="<?php echo $payPeriod->getId(); ?>"/>
    <input name="token" value="<?php echo $token;?>" type="hidden" />

    <label for="txtPayPeriodFromDate"><?php echo $lang_Benefits_PayPeriod; ?></label>
    <input name="txtPayPeriodFromDate" type="text" id="txtPayPeriodFromDate"  size="10" class="formDateInput"
        value="<?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getStartDate()); ?>"/>
    <input type="button" name="Date" value="  " class="calendarBtn" />

    <label for="txtPayPeriodToDate"><?php echo $lang_Common_To; ?></label>
    <input name="txtPayPeriodToDate" type="text" id="txtPayPeriodToDate" class="formDateInput"
        value="<?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getEndDate()); ?>" size="10"/>
    <input type="button" name="Date" value="  " class="calendarBtn" />
    <br class="clear"/>

    <label for="txtPayPeriodCloseDate"><?php echo $lang_Benefits_PayPeriodCloses; ?></label>
    <input name="txtPayPeriodCloseDate" type="text" id="txtPayPeriodCloseDate" class="formDateInput"
        value="<?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getCloseDate()); ?>" size="10"/>
    <input type="button" name="Date" value="  " class="calendarBtn" />
    <br class="clear"/>

    <label for="txtPayPeriodTimesheetDueDate"><?php echo $lang_Benefits_TimesheetAprovalDue; ?></label>
    <input name="txtPayPeriodTimesheetDueDate" type="text" id="txtPayPeriodTimesheetDueDate" class="formDateInput"
        value="<?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getTimesheetAprovalDueDate()); ?>" size="10"/>
    <input type="button" name="Date" value="  " class="calendarBtn" />
    <br class="clear"/>

    <label for="txtPayPeriodCheckDate"><?php echo $lang_Benefits_CheckDate; ?></label>
    <input name="txtPayPeriodCheckDate" type="text" id="txtPayPeriodCheckDate" class="formDateInput"
        value="<?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getCheckDate()); ?>" size="10"/>
    <input type="button" name="Date" value="  " class="calendarBtn" />
    <br class="clear"/>

    <div class="formbuttons">
        <input type="button" class="savebutton" id="saveBtn"
            onclick="addPayPeriod();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Save;?>" />
        <input type="reset" class="resetbutton" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
             value="<?php echo $lang_Common_Reset;?>" />
    </div>
    <br class="clear"/>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>