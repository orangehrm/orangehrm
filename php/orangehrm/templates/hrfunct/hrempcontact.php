<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

	<table height="250" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><?=$country?></td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCountry" onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0"><?=$selectcountry?></option>
<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++) { 
									echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
								}
					?>	 
			 </select></td>
			   <td width="50">&nbsp;</td>
			   <td><?=$street1?></td>
			 <td><input type="text" name="txtStreet1" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtStreet1']))?$this->postArr['txtStreet1']:''?>"></td>
			 </tr>
			 <tr>
			 <td><?=$state?></td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbProvince" onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateDistrict(this.value);">
						  		<option value="0"><?=$selstate?></option>
						  </select></td>
			  <td width="50">&nbsp;</td>
			  <td><?=$street2?></td>
			 <td><input type="text" name="txtStreet2" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtStreet2']))?$this->postArr['txtStreet2']:''?>"></td>
			  </tr>
			 <tr>
			 <td><?=$city?></td>
			 <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbCity">
					<option value="0"><?=$selcity?></option>
				</select></td>
			<td width="50">&nbsp;</td>
			 <td><?=$zipcode?></td>
			 <td><input type="text" name="txtzipCode" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtzipCode']))?$this->postArr['txtzipCode']:''?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtHmTelep" value="<?=(isset($this->postArr['txtHmTelep']))?$this->postArr['txtHmTelep']:''?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMobile" value="<?=(isset($this->postArr['txtMobile']))?$this->postArr['txtMobile']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$worktele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtWorkTelep" value="<?=(isset($this->postArr['txtWorkTelep']))?$this->postArr['txtWorkTelep']:''?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$workemail?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtWorkEmail" value="<?=(isset($this->postArr['txtWorkEmail']))?$this->postArr['txtWorkEmail']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$otheremail?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtOtherEmail" value="<?=(isset($this->postArr['txtOtherEmail']))?$this->postArr['txtOtherEmail']:''?>"></td>
			 </tr>
			 
			 </table>
			 
<? } if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(5)" onkeypress="setUpdate(5)" height="250" border="0" cellpadding="0" cellspacing="0">
<?
		$edit = $this->popArr['editPermResArr'];
?>
          <tr>
			  <td><?=$country?></td>
						  <td><select name="cmbCountry" disabled onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateStates(this.value);">
						  		<option value="0"><?=$selectcountry?></option>
					<?
								$cntlist = $this->popArr['cntlist'];
								for($c=0;$cntlist && count($cntlist)>$c;$c++)  
									if($edit[0][4]==$cntlist[$c][0])
										echo "<option selected value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
									else
										echo "<option value='" .$cntlist[$c][0] . "'>" . $cntlist[$c][1] . '</option>';
					?>
						  </select></td>
						  <td width="50">&nbsp;</td>
			  <td><?=$street1?></td>
			  <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtStreet1" value="<?=(isset($this->postArr['txtStreet1']))?$this->postArr['txtStreet1']:$edit[0][1]?>"></td>
             </tr>
			 <tr>
			 <td><?=$state?></td>
						  <td><select name="cmbProvince" disabled onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_populateDistrict(this.value);">
						  		<option value="0"><?=$selstate?></option>
					<?
								$provlist = $this->popArr['provlist'];
								for($c=0;$provlist && count($provlist)>$c;$c++)  
									if($edit[0][5]==$provlist[$c][1])
										echo "<option selected value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
									else
										echo "<option value='" .$provlist[$c][1] . "'>" . $provlist[$c][2] . '</option>';
					?>
						  </select></td>
						  <td width="50">&nbsp;</td>
			 <td><?=$street2?></td>
			  <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtStreet2" value="<?=(isset($this->postArr['txtStreet2']))?$this->postArr['txtStreet2']:$edit[0][2]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$city?></td>
			 <td><select name="cmbCity" disabled >
			  <option value="0"><?=$selcity?></option>
<?
				$citylist = $this->popArr['citylist'];
				 for($c=0;$citylist && count($citylist)>$c;$c++)  
					if($edit[0][3]==$citylist[$c][1])
						echo "<option selected value='" .$citylist[$c][1] . "'>" . $citylist[$c][2] . '</option>';
						else
						echo "<option value='" .$citylist[$c][1] . "'>" . $citylist[$c][2] . '</option>';
?>
						  </select></td>			 
			<td width="50">&nbsp;</td>
			<td><?=$zipcode?></td>
			 <td><input type="text" name="txtzipCode" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtzipCode']))?$this->postArr['txtzipCode']:$edit[0][6]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtHmTelep" value="<?=(isset($this->postArr['txtHmTelep']))?$this->postArr['txtHmTelep']:$edit[0][7]?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtMobile" value="<?=(isset($this->postArr['txtMobile']))?$this->postArr['txtMobile']:$edit[0][8]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtWorkTelep" value="<?=(isset($this->postArr['txtWorkTelep']))?$this->postArr['txtWorkTelep']:$edit[0][9]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$workemail?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtWorkEmail" value="<?=(isset($this->postArr['txtWorkEmail']))?$this->postArr['txtWorkEmail']:$edit[0][10]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$otheremail?></td>
			 <td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtOtherEmail" value="<?=(isset($this->postArr['txtOtherEmail']))?$this->postArr['txtOtherEmail']:$edit[0][11]?>"></td>
			 </tr>
			
			 </table>
			 
<? } ?>