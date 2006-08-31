<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

		<table height="200" border="0" cellpadding="0" cellspacing="0">
		<tr>
					<td><font color=#ff0000>*</font><?=$ssnno?></td>
					<td><input type="text" name="txtNICNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:''?>"></td>
					<td width="50">&nbsp;</td>
					<td><?=$nationality?></td>
					<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbNation">
						<option value="0"><?=$selectnatio?></option>
<?
					$nation = $this->popArr['nation'];
					 for($c=0;$nation && $c < count($nation);$c++)
						            echo '<option value=' . $nation[$c][0] . '>' . $nation[$c][1] .'</option>';
?>					
					</select></td>
				</tr>
				<tr>
				<td><?=$sinno?></td>
					<td><input type="text" name="txtSINNo" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:''?>"></td>
					<td width="50">&nbsp;</td>
				<td><?=$dateofbirth?></td>
				<td><input type="text" name="DOB" readonly value=<?=(isset($this->postArr['DOB']))?$this->postArr['DOB']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				</tr>
				<tr>
				<td><?=$otherid?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtOtherID" value="<?=(isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$maritalstatus?></td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbMarital">
					<option><?=$selmarital?></option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($this->postArr['cmbMarital']) && $this->postArr['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td><?=$smoker?></td>
			  <td><input type="checkbox" <?=$locRights['add'] ? '':'disabled'?> name="chkSmokeFlag" <?=(isset($this->postArr['chkSmokeFlag']) && $this->postArr['chkSmokeFlag']=='1'?'checked':'')?> value="1"></td>
			  <td width="50">&nbsp;</td>
				<td><?=$gender?></td>
				<td valign="middle">Male<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="1" checked>		Female<input <?=$locRights['add'] ? '':'disabled'?> type="radio" name="optGender" value="2" <?=(isset($this->postArr['optGender']) && isset($this->postArr['optGender'])==2)?'checked':''?>></td>
				</tr>
				<tr>
				<td><?=$dlicenno?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtLicenNo" value="<?=(isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$licexpdate?></td>
				<td><input type="text" readonly name="txtLicExpDate" value=<?=(isset($this->postArr['txtLicExpDate']))?$this->postArr['txtLicExpDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtLicExpDate);return false;"></td>
				</tr> 
				<tr>
				<td><?=$militaryservice?></td>
				<td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMilitarySer" value="<?=(isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:''?>"></td>
				<td>&nbsp;</td>
				<td><?=$ethnicrace?></td>
					<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbEthnicRace">
						<option value="0"><?=$selethnicrace?></option>
<?  			    	$ethRace = $this->popArr['ethRace'];
						      for($c=0;$ethRace && $c < count($ethRace);$c++)
						            echo '<option value=' . $ethRace[$c][0] . '>' . $ethRace[$c][1] .'</option>';
						    ?>			
					</select></td>
				</tr>
				</table>

<? } if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(1)" onkeypress="setUpdate(1)" height="200" border="0" cellpadding="0" cellspacing="0">
	<?
			  $edit = $this->popArr['editPersArr'];
	?>

          <tr>
					<td><font color=#ff0000>*</font><?=$ssnno?></td>
					<td><input type="text" name="txtNICNo" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtNICNo']))?$this->postArr['txtNICNo']:$edit[0][7]?>"></td>
					<td width="50">&nbsp;</td>
					<td><?=$nationality?></td>
					<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbNation">
						<option value="0"><?=$selectnatio?></option>
<?
					$nation = $this->popArr['nation'];
					for($c=0;$nation && count($nation)>$c;$c++)
						if(isset($this->postArr['cmbNation'])) {
							if($this->postArr['cmbNation']==$nation[$c][0])
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						} elseif($edit[0][4]==$nation[$c][0]) 
							    echo "<option selected value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
							else
							    echo "<option value='" . $nation[$c][0] . "'>" .$nation[$c][1]. "</option>";
						
?>					
					</select></td>
				</tr>
				<tr>
				<td><?=$sinno?></td>
					<td><input type="text" name="txtSINNo" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="<?=(isset($this->postArr['txtSINNo']))?$this->postArr['txtSINNo']:$edit[0][8]?>"></td>
					<td width="50">&nbsp;</td>
				<td><?=$dateofbirth?></td>
				<td nowrap><input type="text" readonly name="DOB" value=<?=(isset($this->postArr['DOB']))?$this->postArr['DOB']:$edit[0][3]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.DOB);return false;"></td>
				</tr>
				<tr>
				<td><?=$otherid?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtOtherID" value="<?=(isset($this->postArr['txtOtherID']))?$this->postArr['txtOtherID']:$edit[0][9]?>"></td>
				<td>&nbsp;</td>
				<td><?=$maritalstatus?></td>
				<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbMarital">
					<option value="0"><?=$selmarital?></option>
<?					
					for($c=0;count($arrMStat)>$c;$c++)
						if(isset($this->postArr['cmbMarital'])) {
						 	if($this->postArr['cmbMarital']==$arrMStat[$c])
						    echo "<option selected>" .$arrMStat[$c]."</option>";
						else 
						    echo "<option>" .$arrMStat[$c]."</option>";
						} elseif($edit[0][6]==$arrMStat[$c])
								    echo "<option selected>" .$arrMStat[$c]."</option>";
								else 
								    echo "<option>" .$arrMStat[$c]."</option>";
?>
				</select></td>
				</tr>
				<tr>
				<td><?=$smoker?></td>
			  <td> 
<?
			  if(isset($this->postArr['chkSmokeFlag'])) { ?>
			  <input type="checkbox" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="chkSmokeFlag" <?=$this->postArr['chkSmokeFlag']=='1'?'checked':''?> value="1">
<?			 } else { ?> 
			  <input type="checkbox" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="chkSmokeFlag" <?=$edit[0][1]==1?'checked':''?> value="1">
<? } ?>			  
			  </td>
				<td>&nbsp;</td>
				<td><?=$gender?></td>
				<td valign="middle">Male<input <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> type="radio" name="optGender" value="1" checked>		
<?				if(isset($this->postArr['optGender'])) { ?>
				Female<input type="radio" name="optGender" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($this->postArr['optGender']==2)?'checked':''?>></td>
<?				} else {  ?>
				Female<input type="radio" name="optGender" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> value="2" <?=($edit[0][5]==2)?'checked':''?>></td>
<? } ?>				
				</tr>
				<tr>
				<td><?=$dlicenno?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtLicenNo" value="<?=(isset($this->postArr['txtLicenNo']))?$this->postArr['txtLicenNo']:$edit[0][10]?>"></td>
				<td>&nbsp;</td>
				<td><?=$licexpdate?></td>
				<td nowrap><input type="text" name="txtLicExpDate" readonly value=<?=(isset($this->postArr['txtLicExpDate']))?$this->postArr['txtLicExpDate']:$edit[0][11]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtLicExpDate);return false;"></td>
				</tr> 
				<tr>
				<td><?=$militaryservice?></td>
				<td><input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtMilitarySer" value="<?=(isset($this->postArr['txtMilitarySer']))?$this->postArr['txtMilitarySer']:$edit[0][12]?>"></td>
				<td>&nbsp;</td>
				<td><?=$ethnicrace?></td>
					<td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbEthnicRace">
						<option value="0"><?=$selethnicrace?></option>
<?
					$ethRace = $this->popArr['ethRace'];
					for($c=0;$nation && count($ethRace)>$c;$c++)
						if(isset($this->postArr['cmbEthnicRace'])) {
							if($this->postArr['cmbEthnicRace']==$ethRace[$c][0])
							    echo "<option selected value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
							else
							    echo "<option value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
						} elseif($edit[0][2]==$ethRace[$c][0]) 
							    echo "<option selected value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
							else
							    echo "<option value='" . $ethRace[$c][0] . "'>" .$ethRace[$c][1]. "</option>";
						
?>					
					</select></td>
				</tr>
				</table>
<? } ?>    
