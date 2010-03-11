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
//
//
//
//
//
//
//


$arrTaxStatus = array (EmpUsTax::TAX_STATUS_MARRIED => __("Married"), EmpUsTax::TAX_STATUS_SINGLE => __("Single"),
				EmpUsTax::TAX_STATUS_NONRESIDENTALIEN => __("Non Resident Alien"), EmpUsTax::TAX_STATUS_NOTAPPLICABLE => __("Not Applicable"));
?>

<?php if(isset($getArr['capturemode']) && $getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(18)" onkeypress="setUpdate(18)" style="height:250px;margin-left:5px;margin-right:5px;" border="0" cellpadding="0" cellspacing="2">
<?php
	if (isset($postArr['EditMode']) && $postArr['EditMode']=='1') {
	    $editMode = false;
        $disabled = '';
	} else {
	    $editMode = true;
        $disabled = 'disabled="disabled"';
    }
?>
    <tr>
	    <td colspan="5"><strong><?php echo __("Federal Income Tax");?></strong></td>
    </tr>
    <tr>
		<td><?php echo __("Status");?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxFederalStatus" >
		<option value="0"><?php echo __("--Select--");?></option>
		<?php
			    $prevFederalTaxStatus = isset($postArr['cmbTaxFederalStatus'])?$postArr['cmbTaxFederalStatus']:$employee->usTax->federal_status;
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
		<td><?php echo __("Exemptions"); ?></td>
  		<td><input type="text" size="5" <?php echo $disabled;?> name="taxFederalExceptions" id="taxFederalExceptions"
  				value="<?php echo (isset($postArr['taxFederalExceptions'])) ? $postArr['taxFederalExceptions']:$employee->usTax->federal_exceptions;?>"/>
  		</td>
	</tr>
    <tr>
	    <td colspan="5"><strong><?php echo __("State Income Tax");?></strong></td>
	</tr><tr>
		<td><?php echo __("State"); ?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxState">
			<option value="0">--- <?php echo __("Select State")?>; ---</option>
		<?php
			$prevTaxState = isset($postArr['cmbTaxState'])?$postArr['cmbTaxState']:$employee->usTax->state;
			foreach($states as $state) {
				if($prevTaxState == $state->province_code) {
			    	echo "<option selected=\"selected\" value='" .  $state->province_code . "'>" .  $state->province_name . "</option>";
				} else {
			    	echo "<option value='" .  $state->province_code . "'>" . $state->province_name . "</option>";
				}
			}

		?>
		</select></td>
		<td width="60">&nbsp;</td>
		<td><?php echo __("Status");?></td>
		<td><select <?php echo $disabled;?> name="cmbTaxStateStatus" >
		<option value="0"><?php echo __("--Select");?></option>
		<?php
			    $prevStateTaxStatus = isset($postArr['cmbTaxStateStatus'])?$postArr['cmbTaxStateStatus']:$employee->usTax->state_status;
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
		<td><?php echo __("Exemptions");?></td>
		<td><input type="text" size="5" <?php echo $disabled;?> name="taxStateExceptions" id="taxStateExceptions"
			value="<?php echo (isset($postArr['taxStateExceptions']))?$postArr['taxStateExceptions']:$employee->usTax->state_exceptions;?>"/>
		</td>
		</tr>
		<tr>
			 <td><?php echo __("Unemployment State");?></td>
			 <td><select <?php echo $disabled;?> name="cmbTaxUnemploymentState">
			 <option value="0">--- <?php echo __("Select State");?> ---</option>
		<?php
			$prevUnempState = isset($postArr['cmbTaxUnemploymentState'])?$postArr['cmbTaxUnemploymentState']:$employee->usTax->unemp_state;
			foreach($states as $state) {
				if($prevUnempState == $state->province_code) {
			    	echo "<option selected=\"selected\" value='" .  $state->province_code . "'>" .  $state->province_name . "</option>";
				} else {
			    	echo "<option value='" .  $state->province_code . "'>" . $state->province_name . "</option>";
				}
			}
		?>
		</select></td>
		</tr>
		<tr>
			<td><?php echo __("Work State");?></td>
			<td><select <?php echo $disabled;?> name="cmbTaxWorkState">
			<option value="0">--- <?php echo __("Select State");?> ---</option>
		<?php
			$prevWorkState = isset($postArr['cmbTaxWorkState'])?$postArr['cmbTaxWorkState']:$employee->usTax->work_state;
			foreach($states as $state) {
				if($prevWorkState == $state->province_code) {
			    	echo "<option selected=\"selected\" value='" .  $state->province_code . "'>" .  $state->province_name . "</option>";
				} else {
			    	echo "<option value='" .  $state->province_code . "'>" . $state->province_name . "</option>";
				}
			}

		?>
		</select>
		</td>
		</tr>
</table>

<div class="formbuttons">
    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditTax"
    	value="<?php echo $editMode ? __("Edit") : __("Save");?>"
    	title="<?php echo $editMode ? __("Edit") : __("Save");?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEmpMain(); return false;"/>
	<input type="reset" class="clearbutton" id="btnClearTax" tabindex="5"
		onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
		value="<?php echo __("Reset");?>" />
</div>
<?php } ?>
