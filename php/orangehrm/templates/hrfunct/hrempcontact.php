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
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table style="height:250px;padding-left:5px;padding-right:3px;" border="0" cellpadding="0" cellspacing="2">
<?php
		$edit = $this->popArr['editPermResArr'];
?>
          <tr>
			  <td><?php echo $lang_compstruct_country?></td>
						  <td colspan="4"><select name="cmbCountry" disabled="disabled" onchange="document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....'; xajax_populateStates(this.value);">
						  		<option value="0"><?php echo $lang_districtinformation_selectcounlist?></option>
					<?php
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++)
									if($edit[0][4]==$cntlist[$c][0])
										echo "<option selected=\"selected\" value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
									else
										echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
					?>
						  </select></td>
      </tr>
		<tr>
			<td><?php echo $lang_hremp_street1?></td>
			<td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtStreet1" maxlength="100"
                       value="<?php echo (isset($this->postArr['txtStreet1']))? CommonFunctions::escapeHtml($this->postArr['txtStreet1']):CommonFunctions::escapeHtml($edit[0][1])?>" /></td>
			 <td width="60">&nbsp;</td>
			 <td><?php echo $lang_hremp_street2?></td>
			  <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtStreet2" maxlength="100" 
                         value="<?php echo (isset($this->postArr['txtStreet2']))?CommonFunctions::escapeHtml($this->postArr['txtStreet2']):CommonFunctions::escapeHtml($edit[0][2])?>" /></td>
		</tr>
		 <tr>
			 <td><?php echo $lang_hremp_city?></td>
			 <td><input type="text" name="cmbCity" id="cmbCity" value="<?php echo CommonFunctions::escapeHtml($edit[0][3])?>" disabled="disabled"  maxlength="100"/></td>
			<td width="60">&nbsp;</td>
			<td><?php echo $lang_compstruct_state?></td>
						  <td><div id="lrState" >
					<?php if (isset($edit[0][4]) && ($edit[0][4] == 'US')) { ?>
							<select name="txtState" id="txtState" disabled>
							    	<option value="0">--- <?php echo $lang_districtinformation_selstatelist?> ---</option>
							     	<?php	$provlist = $this->popArr['provlist'];
							    		for($c=0; $provlist && count($provlist)>$c ;$c++)
							    			if($edit[0][5] == $provlist[$c][1])
							    				echo "<option selected=\"selected\" value='" . $provlist[$c][1] . "'>" . $provlist[$c][2] . "</option>";
							    			else
							    				echo "<option value='" . $provlist[$c][1] . "'>" . $provlist[$c][2] . "</option>";
							    	?>
					    	</select>
							    	<?php } else { ?>
							    	<input type="text" disabled="disabled" name="txtState" id="txtState"  maxlength="100"
                                           value="<?php echo isset($edit[0][5]) ? CommonFunctions::escapeHtml($edit[0][5]) : ''?>" />
							    	<?php } ?>
							    	</div>
							    	<input type="hidden" name="cmbProvince" id="cmbProvince"
                                           value="<?php echo isset($edit[0][5]) ? CommonFunctions::escapeHtml($edit[0][5]) : ''?>" /></td>
			</tr>
			<tr>
			 <td><?php echo $lang_compstruct_ZIP_Code?></td>
			 <td><input type="text" name="txtzipCode" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> maxlength="20"
                        value="<?php echo (isset($this->postArr['txtzipCode']))?CommonFunctions::escapeHtml($this->postArr['txtzipCode']):CommonFunctions::escapeHtml($edit[0][6])?>" /></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_hmtele?></td>
			 <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtHmTelep" maxlength="50"
                        value="<?php echo (isset($this->postArr['txtHmTelep']))?CommonFunctions::escapeHtml($this->postArr['txtHmTelep']):CommonFunctions::escapeHtml($edit[0][7])?>" /></td>
			 <td width="60">&nbsp;</td>
			<td><?php echo $lang_hremp_mobile?></td>
			 <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtMobile" maxlength="50"
                        value="<?php echo (isset($this->postArr['txtMobile']))?CommonFunctions::escapeHtml($this->postArr['txtMobile']):CommonFunctions::escapeHtml($edit[0][8])?>" /></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_worktele?></td>
			 <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtWorkTelep" maxlength="50"
                        value="<?php echo (isset($this->postArr['txtWorkTelep']))?CommonFunctions::escapeHtml($this->postArr['txtWorkTelep']):CommonFunctions::escapeHtml($edit[0][9])?>" /></td>
			 <td width="60">&nbsp;</td>
			 <td></td>
			 <td></td>
			</tr>
			<tr>
			 <td><?php echo $lang_hremp_workemail?></td>
			 <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtWorkEmail" maxlength="50"
                        value="<?php echo (isset($this->postArr['txtWorkEmail']))?CommonFunctions::escapeHtml($this->postArr['txtWorkEmail']):CommonFunctions::escapeHtml($edit[0][10])?>" /></td>
			  <td width="60">&nbsp;</td>
			 <td><?php echo $lang_hremp_otheremail?></td>
			 <td><input type="text" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"'?> name="txtOtherEmail" maxlength="50"
                        value="<?php echo (isset($this->postArr['txtOtherEmail']))?CommonFunctions::escapeHtml($this->postArr['txtOtherEmail']):CommonFunctions::escapeHtml($edit[0][11])?>" /></td>
			 </tr>

</table>
    <div class="formbuttons">
        <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditContact"
        	value="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
        	title="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
        	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        	onclick="editEmpMain(); return false;"/>
		<input type="reset" class="clearbutton" id="btnClearContact" tabindex="5"
			onmouseover="moverButton(this);" onmouseout="moutButton(this);"	disabled="disabled"
			value="<?php echo $lang_Common_Reset;?>" />
    </div>
<?php } ?>
