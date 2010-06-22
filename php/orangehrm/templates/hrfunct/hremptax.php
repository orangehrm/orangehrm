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
<?php
require_once ROOT_PATH . '/lib/models/hrfunct/EmpTax.php';

$arrTaxStatus = array (EmpTax::TAX_STATUS_MARRIED => $lang_hrEmpMain_TaxStatusMarried, EmpTax::TAX_STATUS_SINGLE => $lang_hrEmpMain_TaxStatusSingle,
				EmpTax::TAX_STATUS_NONRESIDENTALIEN => $lang_hrEmpMain_TaxStatusNonResidentAlien, EmpTax::TAX_STATUS_NOTAPPLICABLE => $lang_hrEmpMain_TaxStatusNotApplicable);
?>

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(18)" onkeypress="setUpdate(18)" style="height:250px;margin-left:5px;margin-right:5px;" border="0" cellpadding="0" cellspacing="2">
<?php
	$editTaxInfo = $this->popArr['editTaxInfo'];
	$disabled = (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"';
?>
    <tr>
	    <td colspan="5"><strong><?php echo $lang_hrEmpMain_FederalIncomeTax;?></strong></td>
    </tr>
    <tr>
		<td><?php echo $lang_hrEmpMain_TaxStatus?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxFederalStatus" >
		<option value="0"><?php echo $lang_hrEmpMain_TaxStatusSelect;?></option>
		<?php
			    $prevFederalTaxStatus = isset($this->postArr['cmbTaxFederalStatus'])?$this->postArr['cmbTaxFederalStatus']:$editTaxInfo['tax_federal_status'];
				foreach ($arrTaxStatus as $key=>$value) {
					if($prevFederalTaxStatus==$key) {
						echo "<option selected=\"selected\" value='". $key . "'>" . $value . "</option>";
					} else {
						echo "<option value='" . $key . "'>" . $value . "</option>";
					}
				}
		?>
		</select></td>
		<td width="60">&nbsp;</td>
		<td><?php echo $lang_hrEmpMain_TaxExemptions; ?></td>
  		<td><input type="text" size="5" <?php echo $disabled;?> name="taxFederalExceptions" id="taxFederalExceptions"
  				value="<?php echo (isset($this->postArr['taxFederalExceptions'])) ? CommonFunctions::escapeHtml($this->postArr['taxFederalExceptions']):CommonFunctions::escapeHtml($editTaxInfo['tax_federal_exceptions']);?>"/>
  		</td>
	</tr>
    <tr>
	    <td colspan="5"><strong><?php echo $lang_hrEmpMain_StateIncomeTax;?></strong></td>
	</tr><tr>
		<td><?php echo $lang_hrEmpMain_TaxState; ?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxState">
			<option value="0">--- <?php echo $lang_districtinformation_selstatelist?> ---</option>
		<?php
			$usStateList = $this->popArr['usStateList'];
			$prevTaxState = isset($this->postArr['cmbTaxState'])?$this->postArr['cmbTaxState']:$editTaxInfo['tax_state'];
			for($c=0; $usStateList && count($usStateList)>$c ;$c++) {
				if($prevTaxState == $usStateList[$c][1]) {
			    	echo "<option selected=\"selected\" value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				} else {
			    	echo "<option value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				}
			}
		?>
		</select></td>
		<td width="60">&nbsp;</td>
		<td><?php echo $lang_hrEmpMain_TaxStatus?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxStateStatus" >
		<option value="0"><?php echo $lang_hrEmpMain_TaxStatusSelect;?></option>
		<?php
			    $prevStateTaxStatus = isset($this->postArr['cmbTaxStateStatus'])?$this->postArr['cmbTaxStateStatus']:$editTaxInfo['tax_state_status'];
				foreach ($arrTaxStatus as $key=>$value) {
					if($prevStateTaxStatus==$key) {
						echo "<option selected=\"selected\" value='". $key . "'>" . $value . "</option>";
					} else {
						echo "<option value='" . $key . "'>" . $value . "</option>";
					}
				}
		?>
		</select>
		</td>
		</tr>
		<tr>
		<td><?php echo $lang_hrEmpMain_TaxExemptions?></td>
		<td><input type="text" size="5" <?php echo $disabled;?> name="taxStateExceptions" id="taxStateExceptions"
			value="<?php echo (isset($this->postArr['taxStateExceptions']))?CommonFunctions::escapeHtml($this->postArr['taxStateExceptions']):CommonFunctions::escapeHtml($editTaxInfo['tax_state_exceptions']);?>"/>
		</td>
		</tr>
		<tr>
			 <td><?php echo $lang_hrEmpMain_TaxUnemploymentState?></td>
			 <td><select <?php echo $disabled;?> name="cmbTaxUnemploymentState">
			 <option value="0">--- <?php echo $lang_districtinformation_selstatelist?> ---</option>
		<?php
			$usStateList = $this->popArr['usStateList'];
			$prevUnempState = isset($this->postArr['cmbTaxUnemploymentState'])?$this->postArr['cmbTaxUnemploymentState']:$editTaxInfo['tax_unemp_state'];
			for($c=0; $usStateList && count($usStateList)>$c ;$c++) {
				if($prevUnempState == $usStateList[$c][1]) {
			    	echo "<option selected=\"selected\" value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				} else {
			    	echo "<option value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				}
			}
		?>
		</select></td>
		</tr>
		<tr>
			<td><?php echo $lang_hrEmpMain_TaxWorkState;?></td>
			<td><select <?php echo $disabled;?> name="cmbTaxWorkState">
			<option value="0">--- <?php echo $lang_districtinformation_selstatelist?> ---</option>
		<?php
			$usStateList = $this->popArr['usStateList'];
			$prevWorkState = isset($this->postArr['cmbTaxWorkState'])?$this->postArr['cmbTaxWorkState']:$editTaxInfo['tax_work_state'];
			for($c=0; $usStateList && count($usStateList)>$c ;$c++) {
				if($prevWorkState == $usStateList[$c][1]) {
			    	echo "<option selected=\"selected\" value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				} else {
			    	echo "<option value='" . $usStateList[$c][1] . "'>" . $usStateList[$c][2] . "</option>";
				}
			}
		?>
		</select>
		</td>
		</tr>
</table>

<div class="formbuttons">
    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditTax"
    	value="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	title="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEmpMain(); return false;"/>
	<input type="reset" class="clearbutton" id="btnClearTax" tabindex="5"
		onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
		value="<?php echo $lang_Common_Reset;?>" />
</div>
<?php } ?>
