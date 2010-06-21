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
$token = $records['token'];

$disabled = ($records['rights']['edit']) ? '' : 'disabled="disabled"';
?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>

<script type="text/javascript">
//<![CDATA[
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
	} else if (isNaN(hoursPerDay)) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBeANumericValue; ?>\n";
	} else if (0 >= hoursPerDay) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBePositiveNumber; ?>\n";
	} else if (hoursPerDay > 24) {
		err=true;
		msg+="\t- <?php echo $lang_Time_Error_HoursPerDayShouldBeLessThan24; ?>\n";
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


    
//]]>
</script>
    <div class="formpage">
        <div class="navigation">
            <input type="button" class="backbutton"
				onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Time_AssignEmployeesTitle;?></h2></div>
        
        <?php
            if (isset($_GET['message']) && !empty($_GET['message'])) {            
                $message  = $_GET['message'];
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Time_Errors_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: CommonFunctions::escapeHtml($_GET['message']); ?></span>
            </div>  
        <?php } ?>

	<form name="frmEditWorkShift" id="frmEditWorkShift" method="post" action="?timecode=Time&action=">
      <input type="hidden" value="<?php echo $token;?>" name="token" />
			<label for="txtShiftName"><?php echo $lang_Time_ShiftName; ?><span class="required">*</span></label>
	        <input type="text" id="txtShiftName" name="txtShiftName" tabindex="1" value="<?php echo $workshift->getName(); ?>"
                class="formInputText"/>
        <br class="clear"/>
	        <label for="txtHoursPerDay"><?php echo $lang_Time_HoursPerDay; ?><span class="required">*</span></label>
	        <input type="text" id="txtHoursPerDay" name="txtHoursPerDay" tabindex="2" size="3" value="<?php echo $workshift->getHoursPerDay(); ?>"
                class="formInputText" style="width:30px;"/>
	        <input type="hidden" id="txtShiftId" name="txtShiftId" value="<?php echo $workshift->getWorkshiftId(); ?>"/>
        <br class="clear"/>
            <div class="formbuttons">           
                <input type="button" class="savebutton" <?php echo $disabled; ?>
                    onclick="upateShift();"onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_Save;?>" />
            </div>
        <br class="clear"/>                        

		<table border="0">
		<tr>
		   	<th width="100" style="align:center;"><?php echo $lang_Time_AvailableEmployees; ?></th>
			<th width="100"/>
		   	<th width="125" style="align:center;"><?php echo $lang_Time_AssignedEmployees; ?></th>
		</tr>
		<tr><td width="100" >
			<select size="10" id="cmbAvailableEmployees" name="cmbAvailableEmployees[]" <?php echo $disabled; ?>
				style="width:125px;" multiple="multiple">
       			<?php
       				foreach($availableEmployees as $employee) {
       					$empNum = $employee['emp_number'];
       					$name = $employee['emp_firstname'] . " " . $employee['emp_lastname'];
           				echo "<option value='{$empNum}'>{$name}</option>";
       				}
				?>
			</select></td>
			<td align="center" width="100">
				<input type="button" <?php echo $disabled; ?> name="btnAssignEmployee" id="btnAssignEmployee" class="plainbtn"
					onmouseover="moverButton(this)" onmouseout="moutButton(this)"
					onclick="assignEmployee();" value=" <?php echo $lang_compstruct_add; ?> >" style="width:80%" /><br /><br />
				<input type="button" <?php echo $disabled; ?> name="btnRemoveEmployee" id="btnRemoveEmployee" class="plainbtn"
					onmouseover="moverButton(this)" onmouseout="moutButton(this)"
					onclick="removeEmployee();" value="< <?php echo $lang_Leave_Common_Remove; ?>" style="width:80%" />
			</td>
			<td>
			<select size="10" name="cmbAssignedEmployees[]" id="cmbAssignedEmployees" <?php echo $disabled; ?>
				style="width:125px;" multiple="multiple">
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
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');                
    }
//]]>
</script>
</div>        
