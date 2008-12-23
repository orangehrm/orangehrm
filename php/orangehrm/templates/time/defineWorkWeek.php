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

$daysOfTheWeek = array( 1 => $lang_Common_Monday,
						2 => $lang_Common_Tuesday,
						3 => $lang_Common_Wednesday,
						4 => $lang_Common_Thursday,
						5 => $lang_Common_Friday,
						6 => $lang_Common_Saturday,
						7 => $lang_Common_Sunday );

$submissionPeriod = $records[0];
?>

<script type="text/javascript">

function validate() {

	with (document.frmWorkWeek.cmbStartDay) {
	    if (selectedIndex == 0) {
	    	alert("<?php echo $lang_Time_SelectWeekStartDay; ?>");
	    	focus();
	        return false;
	    }
	}

    return true;
}

</script>

   <div class="formpage">

        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Time_DefineTimesheetPeriodTitle; ?></h2></div>
        
        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
 
        <?php } ?>


<?php
// For Admin User
if ($_SESSION['isAdmin'] == 'Yes') {

	if (isset($_GET['message'])) {

	if ($_GET['message'] == 'UPDATE_FAILIURE') {
		$expString  = $_GET['message'];
		$messageType = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo $$expString; ?></span>
        </div> 
<?php
	} elseif ($_GET['message'] == 'UPDATE_SUCCESS') {
		$_SESSION['timePeriodSet'] = 'Yes';
?>
		<h5><?php echo $lang_Time_ContactAdminForTimesheetPeriodSetComplete; ?></h5>
		<a href="../../index.php?module=Home&amp;menu_no=1&amp;submenutop=LeaveModule&amp;menu_no_top=time" target="_parent"><?php echo $lang_Time_ProceedWithTimeModule; ?></a>
<?php
	}
}

if (!isset($_GET['message']) || $_GET['message'] != 'UPDATE_SUCCESS') {
?>
<form id="frmWorkWeek" name="frmWorkWeek" method="post" action="?timecode=Time&amp;action=Work_Week_Save" onsubmit="return validate()">
<input type="hidden" name="txtTimeshetPeriodId" id="txtTimeshetPeriodId" value="<?php echo $submissionPeriod->getTimesheetPeriodId(); ?>"/>

		<label for="cmbStartDay"><?php echo $lang_Time_FirstDayOfWeek; ?></label>
    	<select id="cmbStartDay" name="cmbStartDay" class="formSelect">
        	<option value="0" selected="selected"><?php echo "--".$lang_Common_Select."--"; ?></option>
			<?php foreach ($daysOfTheWeek as $dayNo=>$dayName) { ?>
			<option value="<?php echo $dayNo; ?>" ><?php echo $dayName; ?></option>
			<?php } ?>
		</select>
        <br class="clear"/>

        <div class="formbuttons">               
            <input type="submit" class="savebutton" 
                name="btnSubmit" id="btnSubmit" 
                onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                value="<?php echo $lang_Common_Save;?>" />
        </div>
        <br class="clear"/>
</form>
<?php } ?>
<?php // For ESS Users and Supervisors
} else {
	echo "<h5>".$lang_Time_ContactAdminForTimesheetPeriodSet."</h5>";
}
?>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');                
    }
//]]>
</script>
        