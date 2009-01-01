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
	  $edit = $this->popArr['editPersArr'];
	  $editMain = $this->popArr['editMainArr'];	
	  if (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') {
	      $editMode = false;
		  $disabled = '';	      	      
	  } else {
	      $editMode = true;
		  $disabled = 'disabled="disabled"';	      
	  }	  
	  
?>
	<input type="hidden" name="txtEmpID" value="<?php echo $this->getArr['id']?>"/>
	
	<label for="txtEmployeeId"><?php echo $lang_Commn_code?><span class="required">*</span></label>	
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmployeeId"  id="txtEmployeeId"
		value="<?php echo (isset($this->postArr['txtEmployeeId']))?$this->postArr['txtEmployeeId']:$editMain[0][5]?>" maxlength="50" />
	<label for="txtEmpLastName"><?php echo $lang_hremp_EmpLastName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpLastName" id="txtEmpLastName"
		value="<?php echo (isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:$editMain[0][1]?>"/><br class="clear"/>
	
	<label for="txtEmpFirstName"><?php echo $lang_hremp_EmpFirstName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpFirstName" id="txtEmpFirstName"  
		value="<?php echo (isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:$editMain[0][2]?>"/>
	<label for="txtEmpMiddleName"><?php echo $lang_hremp_EmpMiddleName?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpMiddleName"  id="txtEmpMiddleName"  
		value="<?php echo (isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:$editMain[0][3]?>"/><br class="clear"/>
		
	<label for="txtEmpNickName"><?php echo $lang_hremp_nickname?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtEmpNickName" id="txtEmpNickName" 
		value="<?php echo (isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:$edit[0][4]?>"/><br class="clear" />
		
	<label for="txtNICNo"><?php echo $lang_hremp_ssnno?></label>
	<input type="text" class="formInputText" name="txtNICNo" <?php echo $disabled;?> id="txtNICNo"
		value="<?php echo (isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$edit[0][7]?>"/>
		
<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
		<input type="hidden" name="txtNICNo" value="<?php echo (isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$edit[0][7]?>" />
<?php } ?>

	<label for="cmbNation"><?php echo $lang_hremp_nationality?></label>
	<select class="formSelect"  <?php echo $disabled;?> id="cmbNation" name="cmbNation">
		<option value="0"><?php echo $lang_hremp_selectnatio; ?></option>
<?php
	$nationalities = $this->popArr['nation'];
	if ($nationalities) {	
		$currentNation = isset($this->postArr['cmbNation']) ? $this->postArr['cmbNation'] : $edit[0][4];   		 
		foreach ($nationalities as $nation) {
			$selected = ($currentNation == $nation[0]) ? 'selected="selected"' : '';
			echo "<option {$selected} value='{$nation[0]}'>{$nation[1]}</option>";
		}
	}	
?>	
	</select><br class="clear"/>
					
	<label for="txtSINNo"><?php echo $lang_hremp_sinno?></label>
	<input type="text" class="formInputText" name="txtSINNo" <?php echo $disabled;?> id="txtSINNo"
		value="<?php echo (isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$edit[0][8]?>"/>
<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
	<input type="hidden" name="txtSINNo" value="<?php echo (isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$edit[0][8]?>" />
<?php } ?>
	<label for="DOB"><?php echo $lang_hremp_dateofbirth?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="DOB" id="DOB" 
		value="<?php echo (isset($this->postArr['DOB']))?LocaleUtil::getInstance()->formatDate($this->postArr['DOB']):LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>" size="10" />
	<input type="button" <?php echo $disabled;?>  value="  " class="calendarBtn" /><br class="clear" />
	
	<label for="txtOtherID"><?php echo $lang_hremp_otherid?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtOtherID" id="txtOtherID" 
		value="<?php echo (isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:$edit[0][9]?>"/>
	<label for="cmbMarital"><?php echo $lang_hremp_maritalstatus?></label>	
	<select class="formSelect"  <?php echo $disabled;?> name="cmbMarital" id="cmbMarital">
		<option value="0"><?php echo $lang_hremp_selmarital?></option>
<?php
		$currentMarital = isset($this->postArr['cmbMarital']) ? $this->postArr['cmbMarital'] : $edit[0][6];
		foreach ($arrMStat as $mstat) {
			$selected = ($currentMarital == $mstat) ? 'selected="selected"' : '';
			echo "<option {$selected} >{$mstat}</option>";
		}	
?>
	</select><br class="clear" />
	
	<label for="chkSmokeFlag"><?php echo $lang_hremp_smoker?></label>
<?php $isSmoker = isset($this->postArr['chkSmokeFlag']) ? $this->postArr['chkSmokeFlag'] : $edit[0][1];
	  $checked = $isSmoker == 1 ? 'checked="checked"' : '';
?>
	<span class="formFieldContainer">
		<input type="checkbox" class="formCheckbox columncheckbox" <?php echo $disabled;?> name="chkSmokeFlag" id="chkSmokeFlag" <?php echo $checked;?> value="1"/>
	</span>
	<label><?php echo $lang_hremp_gender?></label>
<?php 
	$gender = isset($this->postArr['optGender']) ? $this->postArr['optGender'] : $edit[0][5];
	$gender = empty($gender) ? '1' : $gender;
?>
	<label for="gender1" class="optionlabel"><?php echo $lang_Common_Male;?></label><input <?php echo $disabled;?> type="radio" class="formRadio" name="optGender" 
		value="1" <?php echo ($gender == 1) ? 'checked="checked"' : '';?> id="gender1"/>

	<label for="gender2" class="optionlabel"><?php echo $lang_Common_Female;?></label><input type="radio" class="formRadio" name="optGender" <?php echo $disabled;?>  
		value="2" <?php echo ($gender == 2) ? 'checked="checked"' : '';?> id="gender2"/>
	<br class="clear" />
	
	<label for="txtLicenNo"><?php echo $lang_hremp_dlicenno?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtLicenNo" id="txtLicenNo" 
		value="<?php echo (isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$edit[0][10]?>"/>
					<?php if (isset($_GET['reqcode']) && ($_GET['reqcode'] === "ESS")) { ?>
					<input type="hidden" name="txtLicenNo" value="<?php echo (isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$edit[0][10]?>" />
					<?php } ?>
					
	<label for="txtLicExpDate"><?php echo $lang_hremp_licexpdate?></label>
	<input type="text" class="formDateInput" <?php echo $disabled;?> name="txtLicExpDate" id="txtLicExpDate" 
		value="<?php echo (isset($this->postArr['txtLicExpDate']))?LocaleUtil::getInstance()->formatDate($this->postArr['txtLicExpDate']):LocaleUtil::getInstance()->formatDate($edit[0][11]); ?>" size="10" />
	<input type="button" <?php echo $disabled;?> value="  " class="calendarBtn" name="btnLicExpDate"/>
	<br class="clear"/>
	<label for="txtMilitarySer"><?php echo $lang_hremp_militaryservice?></label>
	<input type="text" class="formInputText" <?php echo $disabled;?> name="txtMilitarySer" id="txtMilitarySer"
		value="<?php echo (isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:$edit[0][12]?>"/>
	<label for="cmbEthnicRace"><?php echo $lang_hremp_ethnicrace?></label>
	<select class="formSelect"  <?php echo $disabled;?> name="cmbEthnicRace" id="cmbEthnicRace">
		<option value="0"><?php echo $lang_hremp_selethnicrace?></option>
<?php
	$ethRace = $this->popArr['ethRace'];
	$currentRace = isset($this->postArr['cmbEthnicRace']) ? $this->postArr['cmbEthnicRace'] : $edit[0][2];
	if (!empty($ethRace)) {
		foreach($ethRace as $race) {
		    $selected = ($currentRace == $race[0]) ? 'selected="selected"' : '';
		    echo "<option {$selected} value='{$race[0]}'>{$race[1]}</option>";
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
		<input type="button" class="clearbutton" id="btnClearPers" onclick="reLoad();  return false;" tabindex="5"
			onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
			 value="<?php echo $lang_Common_Clear;?>" />
    </div>	            	
		
<?php } ?>
