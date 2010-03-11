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
?>

<?php if(isset($getArr['capturemode']) && $getArr['capturemode'] == 'updatemode') {

	if (isset($postArr['EditMode']) && $postArr['EditMode']=='1') {
	    $editMode = false;
        $disabled = '';
	} else {
	    $editMode = true;
        $disabled = 'disabled="disabled"';
    }

    $employeeLastName = $employee->lastName;
    $employeeFirstName = $employee->firstName;
    $employeeMiddleName = $employee->middleName;
    $employeeNickName = $employee->nickName;
    $employeeId = $employee->employeeId;

    $smoker = $employee->smoker;
    $race = $employee->ethnic_race_code;
    $birthdate = $employee->emp_birthday;
    $nationality = $employee->nation_code;
    $gender = $employee->emp_gender;
    $maritalStatus = $employee->emp_marital_status;
    $ssnNumber =  $employee->ssn;
    $sinNumber = $employee->sin;
    $otherId = $employee->otherId;
    $drivingLicence = $employee->licenseNo;
    $drivingLicenceExpiry = $employee->emp_dri_lice_exp_date;
    $militaryService = $employee->militaryService;
?>
	<input type="hidden" name="txtEmpID" value="<?php echo $employee->empNumber;?>"/>

	<label for="txtEmployeeId"><?php echo __("Code");?><span class="required">*</span></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmployeeId"  id="txtEmployeeId"
		value="<?php echo (isset($postArr['txtEmployeeId']))?$postArr['txtEmployeeId']:$employeeId;?>" maxlength="50" />
	<label for="txtEmpLastName"><?php echo __("Last Name");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpLastName" id="txtEmpLastName"
		value="<?php echo (isset($postArr['txtEmpLastName']))?$postArr['txtEmpLastName']:$employeeLastName;?>"/><br class="clear"/>

	<label for="txtEmpFirstName"><?php echo __("First Name");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpFirstName" id="txtEmpFirstName"
		value="<?php echo (isset($postArr['txtEmpFirstName']))?$postArr['txtEmpFirstName']:$employeeFirstName;?>"/>
	<label for="txtEmpMiddleName"><?php echo __("Middle Name");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpMiddleName"  id="txtEmpMiddleName"
		value="<?php echo (isset($postArr['txtEmpMiddleName']))?$postArr['txtEmpMiddleName']:$employeeMiddleName;?>"/><br class="clear"/>

	<label for="txtEmpNickName"><?php echo __("Nick Name");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpNickName" id="txtEmpNickName"
		value="<?php echo (isset($postArr['txtEmpNickName']))?$postArr['txtEmpNickName']:$employeeNickName;?>"/><br class="clear" />

	<label for="txtNICNo"><?php echo __("SSN No :");?></label>
	<input type="text" class="formInputText" name="txtNICNo" <?php echo $disabled;?> id="txtNICNo"
		value="<?php echo (isset($postArr['txtNICNo']))?$postArr['txtNICNo']:$ssnNumber;?>"/>

<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
		<input type="hidden" name="txtNICNo" value="<?php echo (isset($postArr['txtNICNo']))?$postArr['txtNICNo']:$ssnNumber;?>" />
<?php } ?>

	<label for="cmbNation"><?php echo __("Nationality");?></label>
	<select class="formSelect"  <?php echo $disabled;?> id="cmbNation" name="cmbNation">
		<option value="0"><?php echo __("Select Nationality"); ?></option>
<?php
	if ($nationalities) {
		$currentNation = isset($postArr['cmbNation']) ? $postArr['cmbNation'] : $nationality;;
		foreach ($nationalities as $nation) {
			$selected = ($currentNation == $nation->nat_code) ? 'selected="selected"' : '';
			echo "<option {$selected} value='{$nation->nat_code}'>{$nation->nat_name}</option>";
		}
	}
?>
	</select><br class="clear"/>

	<label for="txtSINNo"><?php echo __("SIN No :");?></label>
	<input type="text" class="formInputText" name="txtSINNo" <?php echo $disabled;?> id="txtSINNo"
		value="<?php echo (isset($postArr['txtSINNo']))?$postArr['txtSINNo']:$sinNumber;?>"/>
<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
	<input type="hidden" name="txtSINNo" value="<?php echo (isset($postArr['txtSINNo']))?$postArr['txtSINNo']:$sinNumber;?>" />
<?php } ?>
	<label for="DOB"><?php echo __("Date of Birth");?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="DOB" id="DOB"
		value="<?php echo (isset($postArr['DOB']))?LocaleUtil::getInstance()->formatDate($postArr['DOB']):LocaleUtil::getInstance()->formatDate($birthdate); ?>" size="10" />
	<input type="button" <?php echo $disabled;?>  value="  " class="calendarBtn" name="btnDOB" /><br class="clear" />

	<label for="txtOtherID"><?php echo __("Other ID");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtOtherID" id="txtOtherID"
		value="<?php echo (isset($postArr['txtOtherID']))?$postArr['txtOtherID']:$otherId;?>"/>
	<label for="cmbMarital"><?php echo __("Marital Status");?></label>
	<select class="formSelect"  <?php echo $disabled;?> name="cmbMarital" id="cmbMarital">
		<option value="0"><?php echo __("--Select--");?></option>
<?php
		$currentMarital = isset($postArr['cmbMarital']) ? $postArr['cmbMarital'] : $maritalStatus;
		$arrMStat = array ('Unmarried','Married','Divorced','Others');
		foreach ($arrMStat as $mstat) {
			$selected = ($currentMarital == $mstat) ? 'selected="selected"' : '';
			echo "<option {$selected} >{$mstat}</option>";
		}
?>
	</select><br class="clear" />

	<label for="chkSmokeFlag"><?php echo __("Smoker");?></label>
<?php $isSmoker = isset($postArr['chkSmokeFlag']) ? $postArr['chkSmokeFlag'] : $smoker;
	  $checked = $isSmoker == 1 ? 'checked="checked"' : '';
?>
	<span class="formFieldContainer">
		<input type="checkbox" class="formCheckbox columncheckbox" <?php echo $disabled;?> name="chkSmokeFlag" id="chkSmokeFlag" <?php echo $checked;?> value="1"/>
	</span>
	<label><?php echo __("Gender");?></label>
<?php
	$gender = isset($postArr['optGender']) ? $postArr['optGender'] : $gender;
	$gender = empty($gender) ? '1' : $gender;
?>
	<label for="gender1" class="optionlabel"><?php echo __("Male");?></label><input <?php echo $disabled;?> type="radio" class="formRadio" name="optGender"
		value="1" <?php echo ($gender == 1) ? 'checked="checked"' : '';?> id="gender1"/>

	<label for="gender2" class="optionlabel"><?php echo __("Female");?></label><input type="radio" class="formRadio" name="optGender" <?php echo $disabled;?>
		value="2" <?php echo ($gender == 2) ? 'checked="checked"' : '';?> id="gender2"/>
	<br class="clear" />

	<label for="txtLicenNo"><?php echo __("Driver's License Number");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtLicenNo" id="txtLicenNo"
		value="<?php echo (isset($postArr['txtLicenNo']))?$postArr['txtLicenNo']:$drivingLicence;?>"/>
					<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
					<input type="hidden" name="txtLicenNo" value="<?php echo (isset($postArr['txtLicenNo']))?$postArr['txtLicenNo']:$drivingLicence;?>" />
					<?php } ?>

	<label for="txtLicExpDate"><?php echo __("License Expiry Date");?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="txtLicExpDate" id="txtLicExpDate"
		value="<?php echo (isset($postArr['txtLicExpDate']))?LocaleUtil::getInstance()->formatDate($postArr['txtLicExpDate']):LocaleUtil::getInstance()->formatDate($drivingLicenceExpiry); ?>" size="10" />
	<input type="button" <?php echo $disabled;?> value="  " class="calendarBtn" name="btnLicExpDate"/>
	<br class="clear"/>
	<label for="txtMilitarySer"><?php echo __("Military Service");?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtMilitarySer" id="txtMilitarySer"
		value="<?php echo (isset($postArr['txtMilitarySer']))?$postArr['txtMilitarySer']:$militaryService;?>"/>
	<label for="cmbEthnicRace"><?php echo __("Ethnic Race");?></label>
	<select class="formSelect"  <?php echo $disabled;?> name="cmbEthnicRace" id="cmbEthnicRace">
		<option value="0"><?php echo __("Select Ethnic Race");?></option>
<?php
	$currentRace = isset($postArr['cmbEthnicRace']) ? $postArr['cmbEthnicRace'] : $race;
	if (!empty($races)) {
		foreach($races as $race) {
		    $selected = ($currentRace == $race->ethnic_race_code) ? 'selected="selected"' : '';
		    echo "<option {$selected} value='{$race->ethnic_race_code}'>{$race->ethnic_race_desc}</option>";
		}
	}
?>
	</select>
	<br class="clear"/>
    <div class="formbuttons">
        <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditPers"
        	value="<?php echo $editMode ? __("Edit") : __("Save");?>"
        	title="<?php echo $editMode ? __("Edit") : __("Save");?>"
        	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        	onclick="editEmpMain(); return false;"/>
		<input type="button" class="clearbutton" id="btnClearPers" tabindex="5"
			onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
			onclick="this.form.reset(); this.form.EditMode.value = '1';" value="<?php echo __("Reset");?>" />
    </div>

<?php } ?>
