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
<?php if(isset($getArr['capturemode']) && $getArr['capturemode'] == 'updatemode') { ?>
<?php
	if (isset($postArr['EditMode']) && $postArr['EditMode']=='1') {
	    $editMode = false;
        $disabled = '';
	} else {
	    $editMode = true;
        $disabled = 'disabled="disabled"';
    }
?>
	<table style="height:250px;padding-left:5px;padding-right:3px;" border="0" cellpadding="0" cellspacing="2">
          <tr>
			  <td><?php echo __("Country");?></td>
						  <td colspan="4"><select name="cmbCountry" disabled="disabled" onchange="document.getElementById('status').innerHTML = '<?php echo __("Please wait");?>....'; xajax_populateStates(this.value);">
						  		<option value="0"><?php echo __("Select Country");?></option>
					<?php
								foreach($countries as $country)
									if ($employee->country == $country->cou_code)
										echo "<option selected=\"selected\" value='" .$country->cou_code . "'>" . $country->cou_name . '</option>';
									else
										echo "<option value='" .$country->cou_code . "'>" . $country->cou_name . '</option>';
					?>
						  </select></td>
      </tr>
		<tr>
			<td><?php echo __("Street 1");?></td>
			<td><input type="text" <?php echo $disabled?> name="txtStreet1" value="<?php echo (isset($postArr['txtStreet1']))?$postArr['txtStreet1']:$employee->street1;?>" /></td>
			 <td width="60">&nbsp;</td>
			 <td><?php echo __("Street 2");?></td>
			  <td><input type="text" <?php echo $disabled;?> name="txtStreet2" value="<?php echo (isset($postArr['txtStreet2']))?$postArr['txtStreet2']:$employee->street2;?>" /></td>
		</tr>
		 <tr>
			 <td><?php echo __("City/Town");?></td>
			 <td><input type="text" name="cmbCity" id="cmbCity" value="<?php echo $employee->city;?>" disabled="disabled"/></td>
			<td width="60">&nbsp;</td>
			<td><?php echo __("State / Province");?></td>
						  <td><div id="lrState" >
					<?php if ($employee->country == 'US') { ?>
							<select name="txtState" id="txtState" disabled>
							    	<option value="0">--- <?php echo __("Select State");?> ---</option>
							     	<?php
							    		foreach ($provinces as $province)
							    			if($employee->province == $province->province_code)
							    				echo "<option selected=\"selected\" value='" . $province->province_code . "'>" . $province->province_name . "</option>";
							    			else
							    				echo "<option value='" . $province->province_code . "'>" . $province->province_name . "</option>";
							    	?>
					    	</select>
							    	<?php } else { ?>
							    	<input type="text" disabled="disabled" name="txtState" id="txtState" value="<?php echo $employee->province;?>" />
							    	<?php } ?>
							    	</div>
							    	<input type="hidden" name="cmbProvince" id="cmbProvince" value="<?php echo $employee->province;?>" /></td>
			</tr>
			<tr>
			 <td><?php echo __("ZIP Code");?></td>
			 <td><input type="text" name="txtzipCode" <?php echo $disabled;?> value="<?php echo (isset($postArr['txtzipCode']))?$postArr['txtzipCode']:$employee->emp_zipcode?>" /></td>
			 </tr>
			 <tr>
			 <td><?php echo __("Home Telephone");?></td>
			 <td><input type="text" <?php echo $disabled;?> name="txtHmTelep" value="<?php echo (isset($postArr['txtHmTelep']))?$postArr['txtHmTelep']:$employee->emp_hm_telephone?>" /></td>
			 <td width="60">&nbsp;</td>
			<td><?php echo __("Mobile");?></td>
			 <td><input type="text" <?php echo $disabled;?> name="txtMobile" value="<?php echo (isset($postArr['txtMobile']))?$postArr['txtMobile']:$employee->emp_mobile?>" /></td>
			 </tr>
			 <tr>
			 <td><?php echo __("Work Telephone");?></td>
			 <td><input type="text" <?php echo $disabled;?> name="txtWorkTelep" value="<?php echo (isset($postArr['txtWorkTelep']))?$postArr['txtWorkTelep']:$employee->emp_mobile?>" /></td>
			 <td width="60">&nbsp;</td>
			 <td></td>
			 <td></td>
			</tr>
			<tr>
			 <td><?php echo __("Work Email");?></td>
			 <td><input type="text" <?php echo $disabled;?> name="txtWorkEmail" value="<?php echo (isset($postArr['txtWorkEmail']))?$postArr['txtWorkEmail']:$employee->emp_work_email;?>" /></td>
			  <td width="60">&nbsp;</td>
			 <td><?php echo __("Other Email");?></td>
			 <td><input type="text" <?php echo $disabled;?> name="txtOtherEmail" value="<?php echo (isset($postArr['txtOtherEmail']))?$postArr['txtOtherEmail']:$employee->emp_oth_email;?>" /></td>
			 </tr>

</table>
    <div class="formbuttons">
        <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditContact"
        	value="<?php echo $editMode ? __("Edit") : __("Save");?>"
        	title="<?php echo $editMode ? __("Edit") : __("Save");?>"
        	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        	onclick="editEmpMain(); return false;"/>
		<input type="reset" class="clearbutton" id="btnClearContact" tabindex="5"
			onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
            value="<?php echo __("Reset");?>" />
    </div>
<?php } ?>
