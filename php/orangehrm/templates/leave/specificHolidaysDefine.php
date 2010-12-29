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

	if (isset($records[0])) {
		$id = $records[0]->getHolidayId();
		$description = $records[0]->getDescription();
		$date = $records[0]->getDate();
		$recurring = ($records[0]->getRecurring() == Holidays::HOLIDAYS_RECURRING)?"checked":"";
		$length = $records[0]->getLength();
	} else {
		$id = "";
		$description = "";
		$date = "";
		$recurring = "";
		$length = "";
	}

	$rights = $records['rights'];

	if (isset($modifier) && $modifier) {
		$action = "Holiday_Specific_Edit";
	} else {
		$action = "Holiday_Specific_Add";
	}
?>
<script type="text/javascript">
//<![CDATA[

	holidayDates = new Array();

	<?php if(isset($records['holidayList'])) {
			foreach(($records['holidayList']) as $holidaydate) {
				if ($modifier) {
					if ($holidaydate->getDate()!=$date){
						echo "\tholidayDates.push(\"{$holidaydate->getDate()}\");\n";
					}
				}
				else{
					echo "\tholidayDates.push(\"{$holidaydate->getDate()}\");\n";
				}
        	}
		}



	?>
	function goBack() {
		location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Holiday_Specific_List';
	}

	function addSave() {

	date = document.getElementById('txtDate').value;
		if (isTypeName(date)) {
            alert("<?php echo $lang_Leave_HOLIDAY_IN_USE_ERROR; ?>");
            return false;
        }

		if (validate()) {
			document.frmDefineHolidays.submit();
		}
	}

	 function isTypeName(date) {
		n = holidayDates.length;
        for (var i=0; i<n; i++) {
            if (holidayDates[i] == date) {
                return true;
            }
        }
        return false;
    }

	function validate() {
		errMes = "";

		obj = $('txtDescription');
		if (obj.value.trim() == "") {
			errMes += "\t- <?php echo $lang_Error_NameOfHolidayCannotBeBlank; ?>\r\n";
		}
		obj = $('txtDate');
		if (!YAHOO.OrangeHRM.calendar.parseDate(obj.value.trim())) {
			errMes += "\t- <?php echo $lang_Error_InvalidDate; ?>\r\n";
		}


		if (errMes != "") {
			errMes = "Please correct the following errors to continue\r\n\r\n"+errMes;
			alert(errMes);
			return false;
		} else {
			return true;
		}
	}

	function $(id) {
		return document.getElementById(id);
	}

	String.prototype.trim = function () {
		regExp = /^\s+|\s+$/g;
		str = this;
		str = str.replace(regExp, "");

		return str;
	}

	YAHOO.OrangeHRM.container.init();
//]]>
</script>
<div class="formpage">
    <div class="navigation">
		<input type="button" class="savebutton"
		onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		value="<?php echo $lang_Common_Back;?>" />
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Leave_Leave_Holiday_Specific_Title;?></h2></div>
<?php
  if (isset($_GET['message']) && !empty($_GET['message'])) {
?>
    <div class="messagebar">
        <span class="<?php echo $messageType; ?>"><?php echo CommonFunctions::escapeHtml($_GET['message']); ?></span>
    </div>
<?php } ?>

    <form id="frmDefineHolidays" name="frmDefineHolidays" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=<?php echo $action; ?>">
        <input type="hidden" value="<?php echo $records['token'];?>" name="token" />
        <input type="hidden" value="<?php echo $id; ?>" name="txtId" />
      	<label for="txtDescription"><?php echo $lang_Leave_Common_NameOfHoliday;?><span class="required">*</span></label>
        <input type="text" id="txtDescription" name="txtDescription" size="30" class="formInputText"
            value="<?php echo $description; ?>"/>
        <br class="clear"/>

        <label for="txtDate"><?php echo $lang_Leave_Common_Date;?><span class="required">*</span></label>
        <input name="txtDate" id="txtDate" type="text" value="<?php echo LocaleUtil::getInstance()->formatDate($date); ?>"
            class="formDateInput" />
        <input type="button" name="Submit" value="  " class="calendarBtn" /></td>
        <br class="clear"/>

        <label for="chkRecurring"><?php echo $lang_Leave_Common_Recurring;?></label>
        <input name="chkRecurring" id="chkRecurring" type="checkbox" class="formCheckbox"
            value="<?php echo Holidays::HOLIDAYS_RECURRING; ?>" <?php echo $recurring; ?> /></td>
        <br class="clear"/>

        <label for="sltLeaveLength"><?php echo $lang_Leave_Common_Length;?></label>
        <select name="sltLeaveLength" id="sltLeaveLength" class="formSelect">
                <option value="<?php echo Leave::LEAVE_LENGTH_FULL_DAY; ?>" <?php echo ($length == Leave::LEAVE_LENGTH_FULL_DAY)?'selected="selected"':""; ?>><?php echo $lang_Leave_Common_FullDay; ?></option>
                <option value="<?php echo Leave::LEAVE_LENGTH_HALF_DAY;?>" <?php echo ($length == Leave::LEAVE_LENGTH_HALF_DAY)?'selected="selected"':""; ?>><?php echo $lang_Leave_Common_HalfDay; ?></option>
        </select>
        <br class="clear"/>
        <div class="formbuttons">
        	<?php $disabled = ($rights['edit']) ? '' : 'disabled="disabled"'; ?>
            <input type="button" class="savebutton" id="saveBtn" <?php echo $disabled; ?>
                onclick="addSave();"onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
            <input type="button" class="clearbutton" onclick="reset();" tabindex="3" <?php echo $disabled; ?>
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Reset;?>" />
        </div>
    </form>
    </div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    //]]>
    </script>
    <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
