<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

	<table height="150" border="0" cellpadding="0" cellspacing="0">
	<tr>
			   <td><?=$jobtitle?></td>
			  <td><select name="cmbJobTitle" <?=$locRights['add'] ? '':'disabled'?> onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_assEmpStat(this.value);">
			  		<option value="0">---Select <?=$jobtitle?>---</option>
			  		<? $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++) {
			  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  			} ?>
			  </select> </td>
			  <td width="50">&nbsp;</td>
			  <td><?=$empstatus?></td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbType">
			  		<option value="0"><?=$selempstat?></option>
<?					for($c=0;count($arrEmpType)>$c;$c++)
						if(isset($this->postArr['cmbType']) && $this->postArr['cmbType']==$arrEmpType[$c])
							echo "<option selected>" .$arrEmpType[$c]. "</option>";
						else
							echo "<option>" .$arrEmpType[$c]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td><?=$eeocategory?> </td>
			  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbEEOCat">
			  		<option value="0"><?=$seleeocat?></option>
<?  			    	$eeojobcat = $this->popArr['eeojobcat'];
						      for($c=0;$eeojobcat && $c < count($eeojobcat);$c++)
						            echo '<option value=' . $eeojobcat[$c][0] . '>' . $eeojobcat[$c][1] .'</option>';
						    ?>			
					</select></td>
			   <td width="50">&nbsp;</td>
			  <td><?=$location?></td>
				<td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbLocation">
						<option value="0"><?=$selectlocation?></option>
<?						$loc = $this->popArr['loc'];
						for($c=0;$loc && count($loc)>$c;$c++)
						    echo '<option value=' . $loc[$c][0] . '>' . $loc[$c][1] .'</option>';
							    
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td><?=$joindate?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?=(isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>
			  </tr>
			  </table>

<? } if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(2)" onkeypress="setUpdate(2)" height="150" border="0" cellpadding="0" cellspacing="0">

    
<?
		  $edit1 = $this->popArr['editJobInfoArr'];
?>
<tr>
			   <td><?=$jobtitle?></td>
			  <td><select name="cmbJobTitle" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_assEmpStat(this.value);">
			  		<option value="0">---Select <?=$jobtitle?>---</option>
			  		<? $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++) 
			  				if($edit1[0][2] == $jobtit[$c][0])
				  				echo "<option selected value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
				  			else
				  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  			 ?>

			  			
			  <td width="50">&nbsp;</td>
			  <td><?=$empstatus?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbType">
			  		<option value="0"><?=$selempstat?></option>
<?						$arrEmpType = $this->popArr['empstatlist'];
						for($c=0;count($arrEmpType)>$c;$c++)
							if(isset($this->postArr['cmbType'])) {
								if($this->postArr['cmbType']==$arrEmpType[$c][0])
										echo "<option selected value=".$arrEmpType[$c][0].">" .$arrEmpType[$c][1]. "</option>";
									else
										echo "<option value=".$arrEmpType[$c][0].">" .$arrEmpType[$c][1]. "</option>";
							} elseif($edit1[0][1]==$arrEmpType[$c][0])
										echo "<option selected value=".$arrEmpType[$c][0].">" .$arrEmpType[$c][1]. "</option>";
									else
										echo "<option value=".$arrEmpType[$c][0].">" .$arrEmpType[$c][1]. "</option>";
?>			        
			  </select></td>
              </tr>
			  <tr>
			  <td><?=$eeocategory?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbEEOCat">
			  		<option value="0"><?=$seleeocat?></option>
<?				  		$eeojobcat = $this->popArr['eeojobcat'];
				for($c=0;$eeojobcat && count($eeojobcat)>$c;$c++)
							if(isset($this->postArr['cmbEEOCat'])) {
							   if($this->postArr['cmbEEOCat']==$eeojobcat[$c][0])
								    echo "<option selected value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
								else
								    echo "<option value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
							} elseif($edit1[0][3]==$eeojobcat[$c][0])
								    echo "<option selected value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
								else
								    echo "<option value='".$eeojobcat[$c][0]. "'>" . $eeojobcat[$c][1] ."</option>";
?>			 
			  </select></td>
			  
			  <td width="50">&nbsp;</td>
			  <td><?=$location?></td>
			  <td><select <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbLocation">
			  		<option value="0"><?=$selectlocation?></option>
<?					$loc = $this->popArr['loc'];
						for($c=0;$loc && count($loc)>$c;$c++)
							if(isset($this->postArr['cmbLocation'])) {
							   if($this->postArr['cmbLocation']==$loc[$c][0])
								    echo "<option selected value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
								else
								    echo "<option value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
							} elseif($edit1[0][4]==$loc[$c][0])
								    echo "<option selected value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
								else
								    echo "<option value='".$loc[$c][0]. "'>" . $loc[$c][1] ."</option>";
?>			  
			  </select></td>
			  </tr>
			  <tr>
			  <td><?=$joindate?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?=(isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:$edit1[0][5]?>>&nbsp;<input type="button" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>
		
				
			  </tr>
			  </table>
			  
<? } ?>