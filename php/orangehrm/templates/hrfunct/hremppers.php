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

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') {

	if (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') {
	    $editMode = false;
        $disabled = '';
	} else {
	    $editMode = true;
        $disabled = 'disabled="disabled"';
    }

    $editMain = $this->popArr['editMainArr'];
    $employeeLastName = CommonFunctions::escapeHtml($editMain[0][1]);
    $employeeFirstName = CommonFunctions::escapeHtml($editMain[0][2]);
    $employeeMiddleName = CommonFunctions::escapeHtml($editMain[0][3]);
    $employeeNickName = CommonFunctions::escapeHtml($editMain[0][4]);
    $employeeId = CommonFunctions::escapeHtml($editMain[0][5]);

    $edit = $this->popArr['editPersArr'];
    $smoker = CommonFunctions::escapeHtml($edit[0][1]);
    $race = CommonFunctions::escapeHtml($edit[0][2]);
    $birthdate = CommonFunctions::escapeHtml($edit[0][3]);
    $nationality = CommonFunctions::escapeHtml($edit[0][4]);
    $gender = CommonFunctions::escapeHtml($edit[0][5]);
    $maritalStatus = CommonFunctions::escapeHtml($edit[0][6]);
    $ssnNumber = CommonFunctions::escapeHtml($edit[0][7]);
    $sinNumber = CommonFunctions::escapeHtml($edit[0][8]);
    $otherId = CommonFunctions::escapeHtml($edit[0][9]);
    $drivingLicence = CommonFunctions::escapeHtml($edit[0][10]);
    $drivingLicenceExpiry = CommonFunctions::escapeHtml($edit[0][11]);
    $militaryService = CommonFunctions::escapeHtml($edit[0][12]);
?>
	<input type="hidden" name="txtEmpID" value="<?php echo $this->getArr['id']?>"/>

	<label for="txtEmployeeId"><?php echo $lang_Commn_code?><span class="required">*</span></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmployeeId"  id="txtEmployeeId"
		value="<?php echo (isset($this->postArr['txtEmployeeId']))?$this->postArr['txtEmployeeId']:$employeeId;?>" maxlength="50" />
	<label for="txtEmpLastName"><?php echo $lang_hremp_EmpLastName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpLastName" id="txtEmpLastName" maxlength="100"
		value="<?php echo (isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:$employeeLastName;?>"/><br class="clear"/>

	<label for="txtEmpFirstName"><?php echo $lang_hremp_EmpFirstName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpFirstName" id="txtEmpFirstName" maxlength="100"
		value="<?php echo (isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:$employeeFirstName;?>"/>
	<label for="txtEmpMiddleName"><?php echo $lang_hremp_EmpMiddleName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpMiddleName"  id="txtEmpMiddleName" maxlength="100"
		value="<?php echo (isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:$employeeMiddleName;?>"/><br class="clear"/>

	<label for="txtEmpNickName"><?php echo $lang_hremp_nickname?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpNickName" id="txtEmpNickName" maxlength="100"
		value="<?php echo (isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:$employeeNickName;?>"/><br class="clear" />

	<label for="txtNICNo"><?php echo $lang_hremp_ssnno?></label>
	<input type="text" class="formInputText" name="txtNICNo" <?php echo $disabled;?> id="txtNICNo" maxlength="100"
		value="<?php echo (isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$ssnNumber;?>"/>

<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
		<input type="hidden" name="txtNICNo" value="<?php echo (isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$ssnNumber;?>" maxlength="100" />
<?php } ?>

	<label for="cmbNation"><?php echo $lang_hremp_nationality?></label>
	<select class="formSelect"  <?php echo $disabled;?> id="cmbNation" name="cmbNation">
		<option value="0"><?php echo $lang_hremp_selectnatio; ?></option>
<?php
	$nationalities = $this->popArr['nation'];
	if ($nationalities) {
		$currentNation = isset($this->postArr['cmbNation']) ? $this->postArr['cmbNation'] : $nationality;;
		foreach ($nationalities as $nation) {
			$selected = ($currentNation == $nation[0]) ? 'selected="selected"' : '';
			echo "<option {$selected} value='{$nation[0]}'>{$nation[1]}</option>";
		}
	}
?>
	</select><br class="clear"/>

	<label for="txtSINNo"><?php echo $lang_hremp_sinno?></label>
	<input type="text" class="formInputText" name="txtSINNo" <?php echo $disabled;?> id="txtSINNo" maxlength="100"
		value="<?php echo (isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$sinNumber;?>"/>
<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
	<input type="hidden" name="txtSINNo" value="<?php echo (isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$sinNumber;?>" />
<?php } ?>
	<label for="DOB"><?php echo $lang_hremp_dateofbirth?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="DOB" id="DOB"
		value="<?php echo (isset($this->postArr['DOB']))?LocaleUtil::getInstance()->formatDate($this->postArr['DOB']):LocaleUtil::getInstance()->formatDate($birthdate); ?>" size="10" />
	<input type="button" <?php echo $disabled;?>  value="  " class="calendarBtn" name="btnDOB" /><br class="clear" />

	<label for="txtOtherID"><?php echo $lang_hremp_otherid?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtOtherID" id="txtOtherID" maxlength="100"
		value="<?php echo (isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:$otherId;?>"/>
	<label for="cmbMarital"><?php echo $lang_hremp_maritalstatus?></label>
	<select class="formSelect"  <?php echo $disabled;?> name="cmbMarital" id="cmbMarital">
		<option value="0"><?php echo $lang_hremp_selmarital?></option>
<?php
		$currentMarital = isset($this->postArr['cmbMarital']) ? $this->postArr['cmbMarital'] : $maritalStatus;
		foreach ($arrMStat as $mstat) {
			$mstatString = "lang_hremp_MaritalStatus_{$mstat}";
			$selected = ($currentMarital == $$mstatString) ? 'selected="selected"' : '';
			echo "<option {$selected} >{$$mstatString}</option>";
		}
?>
	</select><br class="clear" />

	<label for="chkSmokeFlag"><?php echo $lang_hremp_smoker?></label>
<?php $isSmoker = isset($this->postArr['chkSmokeFlag']) ? $this->postArr['chkSmokeFlag'] : $smoker;
	  $checked = $isSmoker == 1 ? 'checked="checked"' : '';
?>
	<span class="formFieldContainer">
		<input type="checkbox" class="formCheckbox columncheckbox" <?php echo $disabled;?> name="chkSmokeFlag" id="chkSmokeFlag" <?php echo $checked;?> value="1"/>
	</span>
	<label style="margin-left:8px"><?php echo $lang_hremp_gender?></label>
<?php
	$gender = isset($this->postArr['optGender']) ? $this->postArr['optGender'] : $gender;
	$gender = empty($gender) ? '1' : $gender;
?>
	<label for="gender1" class="optionlabel"><?php echo $lang_Common_Male;?></label><input <?php echo $disabled;?> type="radio" class="formRadio" name="optGender"
		value="1" <?php echo ($gender == 1) ? 'checked="checked"' : '';?> id="gender1"/>

	<label for="gender2" class="optionlabel"><?php echo $lang_Common_Female;?></label><input type="radio" class="formRadio" name="optGender" <?php echo $disabled;?>
		value="2" <?php echo ($gender == 2) ? 'checked="checked"' : '';?> id="gender2"/>
	<br class="clear" />

	<label for="txtLicenNo"><?php echo $lang_hremp_dlicenno?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtLicenNo" id="txtLicenNo" maxlength="100"
		value="<?php echo (isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$drivingLicence;?>"/>
					<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
					<input type="hidden" name="txtLicenNo" value="<?php echo (isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$drivingLicence;?>" />
					<?php } ?>

	<label for="txtLicExpDate"><?php echo $lang_hremp_licexpdate?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="txtLicExpDate" id="txtLicExpDate"
		value="<?php echo (isset($this->postArr['txtLicExpDate']))?LocaleUtil::getInstance()->formatDate($this->postArr['txtLicExpDate']):LocaleUtil::getInstance()->formatDate($drivingLicenceExpiry); ?>" size="10" />
	<input type="button" <?php echo $disabled;?> value="  " class="calendarBtn" name="btnLicExpDate"/>
	<br class="clear"/>
	<label for="txtMilitarySer"><?php echo $lang_hremp_militaryservice?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtMilitarySer" id="txtMilitarySer" maxlength="100"
		value="<?php echo (isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:$militaryService;?>"/>
	<label for="cmbEthnicRace"><?php echo $lang_hremp_ethnicrace?></label>
	<select class="formSelect"  <?php echo $disabled;?> name="cmbEthnicRace" id="cmbEthnicRace">
		<option value="0"><?php echo $lang_hremp_selethnicrace?></option>
<?php
	$ethRace = $this->popArr['ethRace'];
	$currentRace = isset($this->postArr['cmbEthnicRace']) ? $this->postArr['cmbEthnicRace'] : $race;
	if (!empty($ethRace)) {
		foreach($ethRace as $race) {
		    $selected = ($currentRace == $race[0]) ? 'selected="selected"' : '';
		    echo "<option {$selected} value='{$race[0]}'>" . CommonFunctions::escapeHtml($race[1]) . "</option>";
		}
	}
?>
	</select>
	<br class="clear"/>
    <div class="formbuttons">
        <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditPers"
        	value="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
        	title="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
        	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        	onclick="editEmpMain(); return false;"/>
		<input type="button" class="clearbutton" id="btnClearPers" tabindex="5"
			onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
			onclick="this.form.reset(); this.form.EditMode.value = '1';" value="<?php echo $lang_Common_Reset;?>" />
    </div>

<?php } ?>
