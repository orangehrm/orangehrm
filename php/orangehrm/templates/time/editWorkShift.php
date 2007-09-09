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

$workshift = $records[0];
$assignedEmployees = $records[1];
$availableEmployees = $records[2];

?>
<style type="text/css">
@import url("../../themes/beyondT/css/octopus.css");

.roundbox {
	margin-top: 10px;
	margin-left: 10px;
	width:300px;
}

label {
	width: 80px;
}

.roundbox_content {
	padding:5px 5px 20px 5px;
}

#txtHoursPerDay {
	width: 2em;
}

#editPanel {
	display: block;
}
</style>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>

<script type="text/javascript">
var baseUrl = '?timecode=Time&action=';

function goBack() {
	location.href = "./CentralController.php?timecode=Time&action=View_Work_Shifts";
}

function upateShift() {
	err=false;
	msg='<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

	if ($('txtShiftName').value.trim() == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyWorkShiftName; ?>\n";
	}

	var hoursPerDay = $('txtHoursPerDay').value.trim();
	if ( hoursPerDay == '') {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_SpecifyHoursPerDay; ?>\n";
	} else if (!numbers($('txtHoursPerDay')) || (0 >= hoursPerDay)) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBePositiveNumber; ?>\n";
	}

	if (err) {
		alert(msg);

		return false;
	}

	selectAllOptions($('cmbAssignedEmployees'));
	$('frmEditWorkShift').action=baseUrl+'Edit_Work_Shift';
	$('frmEditWorkShift').submit();
}

function assignEmployee() {
	moveSelectOptions($('cmbAvailableEmployees'), $('cmbAssignedEmployees'), '<?php echo $lang_Time_Error_NoEmployeeSelected; ?>');
}

function removeEmployee() {
	moveSelectOptions($('cmbAssignedEmployees'), $('cmbAvailableEmployees'), '<?php echo $lang_Time_Error_NoEmployeeSelected; ?>');
}



</script>
<h2><?php echo $lang_Time_AssignEmployeesTitle; ?></h2>
<hr/>
<div class="navigation">
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
<?php
if (isset($_GET['message']) && !empty($_GET['message'])) {

	$expString  = $_GET['message'];
	$col_def = CommonFunctions::getCssClassForMessage($expString);
	$expString = 'lang_Time_Errors_'.$expString;

	$message = isset($$expString) ? $$expString : $_GET['message'];
?>
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $message; ?>
	</font>
<?php }	?>
</div>
<div id="editPanel">
	<form name="frmEditWorkShift" id="frmEditWorkShift" method="post" action="?timecode=Time&action=">
		<div class="roundbox">
			<label for="txtShiftName"><span class="error">*</span> <?php echo $lang_Time_ShiftName; ?></label>
	        <input type="text" id="txtShiftName" name="txtShiftName" tabindex="1" value="<?php echo $workshift->getName(); ?>"/>
			<br/>
	        <label for="txtHoursPerDay"><span class="error">*</span> <?php echo $lang_Time_HoursPerDay; ?></label>
	        <input type="text" id="txtHoursPerDay" name="txtHoursPerDay" tabindex="2" size="3" value="<?php echo $workshift->getHoursPerDay(); ?>"/>
	        <br>
	        <label for="none">&nbsp;</label>
	        <input type="hidden" id="txtShiftId" name="txtShiftId" value="<?php echo $workshift->getWorkshiftId(); ?>"/>
	   	</div><br />
        <img onClick="upateShift();"
             onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';"
             onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';"
             src="../../themes/beyondT/pictures/btn_save.jpg">
		<script type="text/javascript">
		<!--
		    if (document.getElementById && document.createElement) {
		 			initOctopus();
			}
		 -->
		</script>
		<table border="0">
		<tr>
		   	<th width="100" style="align:center;"><?php echo $lang_Time_AvailableEmployees; ?></th>
			<th width="100"/>
		   	<th width="125" style="align:center;"><?php echo $lang_Time_AssignedEmployees; ?></th>
		</tr>
		<tr><td width="100" >
			<select size="10" id="cmbAvailableEmployees" name="cmbAvailableEmployees[]" style="width:125px;"
					multiple="multiple">
       			<?php
       				foreach($availableEmployees as $employee) {
       					$empNum = $employee['emp_number'];
       					$name = $employee['emp_firstname'] . " " . $employee['emp_lastname'];
           				echo "<option value='{$empNum}'>{$name}</option>";
       				}
				?>
			</select></td>
			<td align="center" width="100">
				<input type="button" name="btnAssignEmployee" id="btnAssignEmployee" onClick="assignEmployee();" value=" <?php echo $lang_compstruct_add; ?> >" style="width:80%"><br><br>
				<input type="button" name="btnRemoveEmployee" id="btnRemoveEmployee" onClick="removeEmployee();" value="< <?php echo $lang_Leave_Common_Remove; ?>" style="width:80%">
			</td>
			<td>
			<select size="10" name="cmbAssignedEmployees[]" id="cmbAssignedEmployees" style="width:125px;"
			        multiple="multiple">
       			<?php
       				foreach($assignedEmployees as $employee) {
       					$empNum = $employee['emp_number'];
       					$name = $employee['emp_firstname'] . " " . $employee['emp_lastname'];
           				echo "<option value='{$empNum}'>{$name}</option>";
       				}
				?>
			</select></td>
		</tr>

	</table>
  </form>
</div>
