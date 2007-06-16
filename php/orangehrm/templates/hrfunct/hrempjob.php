<script language="javascript">
	function returnLocDet(){
		var popup=window.open('CentralController.php?uniqcode=CST&VIEW=MAIN&esp=1','Locations','height=450,width=400,resizable=1');
        if(!popup.opener) popup.opener=self;
	}

	function toggleEmployeeContracts() {
		oLayer = document.getElementById("employeeContractLayer");
		oLink = document.getElementById("toogleContractLayerLink");

		if (oLayer.style.display == 'none') {
			oLayer.style.display = 'block';
		} else {
			oLayer.style.display = 'none';
		}
		toggleEmployeeContractsText();
	}

	function toggleEmployeeContractsText() {
		oLayer = document.getElementById("employeeContractLayer");
		oLink = document.getElementById("toogleContractLayerLink");

		if (oLayer.style.display == 'none') {
			oLink.innerHTML = "<?php echo $lang_hremp_ShowEmployeeContracts; ?>";
			oLink.className = "show";
		} else {
			oLink.innerHTML = "<?php echo $lang_hremp_HideEmployeeContracts; ?>";
			oLink.className = "hide";
		}
	}
</script>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

	<table height="150" border="0" cellpadding="5" cellspacing="0">
	<tr>
			   <td><?php echo $lang_hremp_jobtitle; ?></td>
			  <td><select name="cmbJobTitle" <?php echo $locRights['add'] ? '':'disabled'?> onchange="document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait; ?>....'; xajax_assEmpStat(this.value);">
			  		<option value="0">-- <?php echo $lang_hremp_SelectJobTitle; ?> --</option>
			  		<?php $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++) {
			  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  			} ?>
			  </select> </td>
			  <td width="50">&nbsp;</td>
			  <td><?php echo $lang_hremp_empstatus; ?></td>
	  <td><select <?php echo $locRights['add'] ? '':'disabled'?> name="cmbType">
			  		<option value="0">-- <?php echo $lang_hremp_selempstat; ?> --</option>
<?php				if(isset($this->postArr['cmbType'])) {
						$arrEmpType = $this->popArr['empstatlist'];
						for($c=0;count($arrEmpType)>$c;$c++)
							if($this->postArr['cmbType']==$arrEmpType[$c][0])
								echo "<option selected value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
							else
								echo "<option value='".$arrEmpType[$c][0]."'>" .$arrEmpType[$c][1]. "</option>";
					}
?>
			  </select></td>
              </tr>
			  <tr>
			  <td><?php echo $lang_hremp_eeocategory; ?> </td>
			  <td><select <?php echo $locRights['add'] ? '':'disabled'?> name="cmbEEOCat">
			  		<option value="0">-- <?php echo $lang_hremp_seleeocat?> --</option>
<?php  			    	$eeojobcat = $this->popArr['eeojobcat'];
						      for($c=0;$eeojobcat && $c < count($eeojobcat);$c++)
						            echo '<option value=' . $eeojobcat[$c][0] . '>' . $eeojobcat[$c][1] .'</option>';
						    ?>
					</select></td>
			   <td width="50">&nbsp;</td>
			  <td nowrap><?php echo $lang_hremp_Subdivision; ?></td>
			  <td nowrap><input type="text"  name="txtLocation" value="" readonly />
			  			 <input type="hidden"  name="cmbLocation" value="" readonly />
			  <input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo $locRights['add'] ? '':'disabled'?> class="button" />
			  </td>
			  </tr>
			  <tr>
			  <td><?php echo $lang_hremp_joindate?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?php echo (isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:''?>>&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>
			  </tr>
			  </table>

<?php } if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(2)" onkeypress="setUpdate(2)" height="150" border="0" cellpadding="5" cellspacing="0">


<?php
		  $edit1 = $this->popArr['editJobInfoArr'];
?>
<tr>
			   <td><?php echo $lang_hremp_jobtitle; ?></td>
			  <td><select name="cmbJobTitle" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> onchange="document.getElementById('status').innerHTML = 'Please Wait....'; xajax_assEmpStat(this.value);">
			  		<option value="0">-- <?php echo $lang_hremp_SelectJobTitle; ?> --</option>
			  		<?php $jobtit = $this->popArr['jobtit'];
			  			for ($c=0; $jobtit && count($jobtit)>$c ; $c++)
			  				if(isset($this->postArr['cmbJobTitle'])) {
			  					if($this->postArr['cmbJobTitle'] == $jobtit[$c][0])
					  				echo "<option selected value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
					  			else
					  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  				} elseif($edit1[0][2] == $jobtit[$c][0])
					  				echo "<option selected value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
					  			else
					  				echo "<option value='" . $jobtit[$c][0] . "'>" .$jobtit[$c][1]. "</option>";
			  		?>


			  <td width="50">&nbsp;</td>
			  <td><?php echo $lang_hremp_EmpStatus; ?></td>
			  <td><select <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbType">
			  		<option value="0">-- <?php echo $lang_hremp_selempstat?> --</option>
<?php						$arrEmpType = $this->popArr['empstatlist'];
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
			  <td><?php echo $lang_hremp_eeocategory; ?></td>
			  <td><select <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="cmbEEOCat">
			  		<option value="0">-- <?php echo $lang_hremp_seleeocat?> --</option>
<?php				  		$eeojobcat = $this->popArr['eeojobcat'];
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
			  <td nowrap><?php echo $lang_hremp_Subdivision; ?></td>
			  <td nowrap><input type="text"  name="txtLocation" value="<?php echo isset($this->postArr['txtLocation']) ? $this->postArr['txtLocation'] : $edit1[0][4]?>" readonly />
			  			 <input type="hidden"  name="cmbLocation" value="<?php echo isset($this->postArr['cmbLocation']) ? $this->postArr['cmbLocation'] : $edit1[0][6]?>" readonly />
			  <input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" /></td>
			  </tr>
			  <tr>
			  <td><?php echo $lang_hremp_joindate; ?></td>
				<td><input type="text" readonly name="txtJoinedDate" value=<?php echo (isset($this->postArr['txtJoinedDate']))?$this->postArr['txtJoinedDate']:$edit1[0][5]?>>&nbsp;<input type="button" <?php echo (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtJoinedDate);return false;"></td>


			  </tr>
			  </table>
<?php } ?>
<hr/>
<a href="javascript:toggleEmployeeContracts();" id="toogleContractLayerLink"><?php echo $lang_hremp_ShowEmployeeContracts; ?></a>
