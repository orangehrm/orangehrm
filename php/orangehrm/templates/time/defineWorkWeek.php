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

<script language="JavaScript" type="text/javascript">

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

<h2>
	<?php echo $lang_Time_DefineTimesheetPeriodTitle; ?>
  <hr/>
</h2>

<?php
// For Admin User
if ($_SESSION['isAdmin'] == 'Yes') {

	if (isset($_GET['message'])) {

	if ($_GET['message'] == 'UPDATE_FAILIURE') {
		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
?>
		<div class="<?php echo $col_def?>" >
			<font size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
			</font>
		</div>
<?php
	} elseif ($_GET['message'] == 'UPDATE_SUCCESS') {
		$_SESSION['timePeriodSet'] = 'Yes';
?>
		<h5><?php echo $lang_Time_ContactAdminForTimesheetPeriodSetComplete; ?></h5>
		<a href="../../index.php?module=Home&menu_no=1&submenutop=LeaveModule&menu_no_top=time" target="_parent"><?php echo $lang_Time_ProceedWithTimeModule; ?></a>
<?php
	}
}

if (!isset($_GET['message']) || $_GET['message'] != 'UPDATE_SUCCESS') {
?>
<form id="frmWorkWeek" name="frmWorkWeek" method="post" action="?timecode=Time&action=Work_Week_Save" onSubmit="return validate()">
<input type="hidden" name="txtTimeshetPeriodId" id="txtTimeshetPeriodId" value="<?php echo $submissionPeriod->getTimesheetPeriodId(); ?>"/>

<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_FirstDayOfWeek; ?></td>
			<td></td>
        	<td>
	        	<select id="cmbStartDay" name="cmbStartDay">
	        	<option value="0" selected><?php echo "--".$lang_Common_Select."--"; ?></option>
				<?php foreach ($daysOfTheWeek as $dayNo=>$dayName) { ?>
				<option value="<?php echo $dayNo; ?>" ><?php echo $dayName; ?></option>
				<?php } ?>
				</select>
        	</td>
        	<td class="tableMiddleRight"></td>
  		</tr>
  		<tr>
			<td class="tableMiddleLeft"></td>
			<td><input type="image"
				   name="btnSubmit" id="btnSubmit"
				   height="20" width="65" alt="Save"
				   style="width:65px; height: 20px"
				   onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';"
				   onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
				   src="../../themes/beyondT/pictures/btn_save.gif"/></td>
			<td></td>
        	<td></td>
        	<td class="tableMiddleRight"></td>
  		</tr>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
</form>
<?php } ?>
<?php // For ESS Users and Supervisors
} else {
	echo "<h5>".$lang_Time_ContactAdminForTimesheetPeriodSet."</h5>";
}
?>
