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
 */

$arrRepType = array ($lang_hrEmpMain_arrRepType_Supervisor, $lang_hrEmpMain_arrRepType_Subordinate);
$arrRepMethod = array ($lang_hrEmpMain_arrRepMethod_Direct => 1, $lang_hrEmpMain_arrRepMethod_Indirect => 2);
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
function editReportTo() {

	if ($('btnEditReportTo').value == '<?php echo $lang_Common_Save; ?>') {
		editEXTReportTo();
		return;
	} else {
		$('btnEditReportTo').value = '<?php echo $lang_Common_Save; ?>';
		$('btnEditReportTo').onClick = editEXTReportTo;
	}

	var frm = document.frmEmp;
	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}
}

function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400,scrollbars=1');
        if(!popup.opener) popup.opener=self;
		popup.focus();
}

function addEXTReportTo() {

	if(document.frmEmp.cmbRepType.value=='0') {
	 	alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
	 	document.frmEmp.cmbRepType.focus();
	 	return;
	 }

	 if(document.frmEmp.txtRepEmpID.value=='') {
	 	alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
	 	document.frmEmp.txtRepEmpID.focus();
	 	return;
	 }

	 if(document.frmEmp.cmbRepMethod.value=='0') {
	 	alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
	 	document.frmEmp.cmbRepMethod.focus();
	 	return;
	 }

	 if(document.frmEmp.cmbRepType.value == 'Supervisor') {

	     document.frmEmp.txtSubEmpID.value = document.frmEmp.txtEmpID.value;
	 	document.frmEmp.txtSupEmpID.value = document.frmEmp.txtRepEmpID.value;

	 }

	 if(document.frmEmp.cmbRepType.value == 'Subordinate') {
	 	document.frmEmp.txtSupEmpID.value = document.frmEmp.txtEmpID.value;
	 	document.frmEmp.txtSubEmpID.value = document.frmEmp.txtRepEmpID.value;

	 }

   document.frmEmp.reporttoSTAT.value="ADD";
   qCombo(15);
}

function editEXTReportTo() {
	 document.frmEmp.reporttoSTAT.value="EDIT";
	 qCombo(15);
}

function delSupEXTReportTo() {

      var check = 0;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chksupdel[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0) {
              alert("<?php echo $lang_Error_SelectAtLeastOneCheckBox; ?>");
              return;
        }

    document.frmEmp.delSupSub.value='sup';
    document.frmEmp.reporttoSTAT.value="DEL";
    qCombo(15);
}

function delSubEXTReportTo() {

      var check = 0;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chksubdel[]') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0) {
              alert("<?php echo $lang_Error_SelectAtLeastOneCheckBox; ?>");
              return;
        }

    document.frmEmp.delSupSub.value='sub';
    document.frmEmp.reporttoSTAT.value="DEL";
    qCombo(15);
}

function viewSub(sub,rep) {

	document.frmEmp.action = document.frmEmp.action + "&editIDSub=" + sub + "&RepMethod=" + rep;
	document.frmEmp.pane.value = 15;
	document.frmEmp.submit();
}

function viewSup(sup,rep) {

	document.frmEmp.action = document.frmEmp.action + "&editIDSup=" + sup + "&RepMethod=" + rep;
	document.frmEmp.pane.value = 15;
	document.frmEmp.submit();
}
	function changeReporter(){
		document.frmEmp.action = document.frmEmp.action + "&reporterChanged=changed";
	}

//--><!]]></script>
<div id="parentPaneReportTo" >
<?php
	$supervisorEMPMode = false;
	if ((isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) && (isset($_GET['reqcode']) && ($_GET['reqcode'] === "EMP")) ) {
		$supervisorEMPMode = true;
	}

	$empInfoObj = new EmpInfo();

	if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
        <input type="hidden" name="reporttoSTAT" value=""/>
<?php	if(isset($this->getArr['editIDSup'])) {	?>
<div id="editPaneReportTo" >
    <input type="hidden" name="txtSupEmpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['editIDSup'])?>"/>
    <input type="hidden" name="txtSubEmpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['id'])?>"/>
    <input type="hidden" name="oldRepMethod" value="<?php echo CommonFunctions::escapeHtml($this->getArr['RepMethod'])?>"/>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator?></td>



    				 <td align="left" valign="top"><select disabled="disabled" name='cmbRep' onChange = "changeReporter();" ><strong>

    				 <option selected="selected" value= " <?php echo $lang_hrEmpMain_arrRepType_Supervisor ?> "> <?php echo $lang_hrEmpMain_arrRepType_Supervisor ?> </option>
    				 <option value= "<?php echo $lang_hrEmpMain_arrRepType_Subordinate ?>"> <?php echo $lang_hrEmpMain_arrRepType_Subordinate ?> </option>
					</select>
    				 <input type="hidden" name="cmbRepType" value="<?php echo $arrRepType[0]?>"/>

					</tr>
					<tr>
						<td valign="top"><?php echo $lang_empview_employeename?></td>
<?php						$empsupid =$this->getArr['editIDSup']; ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['editIDSup'])?>"/><strong>
						<?php echo CommonFunctions::escapeHtml($empInfoObj->getFullName($this->getArr['editIDSup']));?>
					</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select disabled="disabled" name='cmbRepMethod'><strong>
<?php					$keys = array_keys($arrRepMethod);
						$values = array_values($arrRepMethod);
						for($c=0;count($arrRepMethod)>$c;$c++)
							if($this->getArr['RepMethod']==$values[$c]) {
								echo "<option selected=\"selected\" value='". $values[$c] . "'>" . $keys[$c] . "</option>";
							} else {
								echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
							}
?>
					</tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if(!$supervisorEMPMode && $locRights['edit']) { ?>
					        <input type="button" id="btnEditReportTo" class="editbutton" value="<?php echo $lang_Common_Edit; ?>"
					        	onmouseout="moutButton(this);" onmouseover="moverButton(this);"
								onclick="editReportTo();" />
							<input type="reset" class="resetbutton" disabled="disabled" value="<?php echo $lang_Common_Reset; ?>"
									onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
			<?php			}  ?>
						</td>
					  </tr>
 </table>
</div>
<?php } else if (isset($this->getArr['editIDSub'])) { ?>
<div id="editPaneReportTo" >
	<input type="hidden" name="txtSupEmpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['id'])?>"/>
    <input type="hidden" name="txtSubEmpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['editIDSub'])?>"/>
  	<input type="hidden" name="oldRepMethod" value="<?php echo CommonFunctions::escapeHtml($this->getArr['RepMethod'])?>"/>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator ?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?php echo $arrRepType[1]?>"/>
    				 <select disabled="disabled" name='cmbRep' onChange = "changeReporter();"><strong>

    				 <option selected="selected" value= " <?php echo $lang_hrEmpMain_arrRepType_Subordinate ?> "> <?php echo $lang_hrEmpMain_arrRepType_Subordinate ?> </option>
    				 <option value= "<?php echo $lang_hrEmpMain_arrRepType_Supervisor ?>"> <?php echo $lang_hrEmpMain_arrRepType_Supervisor ?> </option>
					</select></td>
					</tr>
					<tr>
						<td valign="top"><?php echo $lang_empview_employeename; ?></td>
						<?php	$empsubid = CommonFunctions::escapeHtml($this->getArr['editIDSub']);  ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?php echo $empsubid?>"/><strong>
						<?php echo  CommonFunctions::escapeHtml($empInfoObj->getFullName($empsubid)); ?>
						</strong></td>
					  </tr>

					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select disabled="disabled" name="cmbRepMethod"><strong>
<?php
						$keys = array_keys($arrRepMethod);
						$values = array_values($arrRepMethod);
						for($c=0;count($arrRepMethod)>$c;$c++)
							if($this->getArr['RepMethod']==$values[$c]) {
								echo "<option selected=\"selected\" value='". $values[$c] . "'>" . $keys[$c] . "</option>";
							} else {
								echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
							}
?>
					</tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if(!$supervisorEMPMode && $locRights['edit']) { ?>
							<input type="button" id="btnEditReportTo" class="editbutton" value="<?php echo $lang_Common_Edit; ?>"
								onmouseout="moutButton(this);" onmouseover="moverButton(this);"
								onclick="editReportTo();" />
							<input type="reset" class="resetbutton" disabled="disabled" value="<?php echo $lang_Common_Reset; ?>"
								onmouseout="moutButton(this);" onmouseover="moverButton(this);" />

		<?php			}  ?>
						</td>
					  </tr>
			</table>
</div>
<?php } else if (!$supervisorEMPMode && $locRights['add']) { ?>
	<div id="addPaneReportTo" class="<?php echo (($this->popArr['suprset'] != null) && ($this->popArr['subrset'] != null))?"addPane":""; ?>" >
		<input type="hidden" name="txtSupEmpID"/>
     	<input type="hidden" name="txtSubEmpID"/>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator?></td>
    				  <td>
					  <select <?php echo (!$supervisorEMPMode && $locRights['add']) ? '':'disabled="disabled"'?> name="cmbRepType">
					  <option value="0"><?php echo $lang_Leave_Common_Select; ?></option>

<?php
							echo "<option value='Supervisor'>" . $arrRepType[0] . "</option>";
							echo "<option value='Subordinate'>" . $arrRepType[1] . "</option>";
?>
					  </select></td>
					</tr>
					<tr><td><?php echo $lang_Leave_Common_EmployeeName; ?></td>
						<td align="left" valign="top"><input type="text" disabled="disabled" name="cmbRepEmpID" value="" readonly="readonly"/><input type="hidden" disabled="disabled" name="txtRepEmpID" value=""/>&nbsp;<input class="button" type="button" value="..." onclick="returnEmpDetail();"/>
						</td></tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select <?php echo (!$supervisorEMPMode && $locRights['add']) ? '':'disabled="disabled"'?> name='cmbRepMethod'>
						   		<option value="0"><?php echo $lang_hrEmpMain_SelectMethod; ?></option>
<?php
									$keys = array_keys($arrRepMethod);
									$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										echo "<option value='" . $values[$c] . "'>" . $keys[$c] . "</option>";
?>						</select></td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						</td>
					  </tr>
                 </table>
<?php	if(!$supervisorEMPMode && $locRights['add']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddRepTo" id="btnAddRepTo"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addEXTReportTo(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>
      </div>
<?php } ?>
<?php if (($this->popArr['suprset'] != null) || ($this->popArr['subrset'] != null)) { ?>
<input type="hidden" name="delSupSub"/>
<table width="100%" cellspacing="0" cellpadding="0" >
  <tr style="vertical-align:top;">
<?php
$rset = $this->popArr['suprset'];
$empname = $this ->popArr['empname'];

$keys = array_keys($arrRepMethod);
$values = array_values($arrRepMethod);

// checking for a records if exsist view the the table and delete btn else no
if ($rset != null && $empname != null){ ?>
<td class="leftList">
	<div class="subHeading"><h3><?php echo $lang_hrEmpMain_supervisorinfomation?></h3></div>
	<div><?php echo $lang_hremp_ie_CurrentSupervisors; ?></div>

	<div class="actionbar">
		<div class="actionbuttons">
<?php if (!$supervisorEMPMode && $locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('ReportTo');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
<?php	if(!$supervisorEMPMode && $locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delSupEXTReportTo();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

		<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
              	<td></td>
				 <td><?php echo $lang_hrEmpMain_ReportToId?></td>
				 <td><?php echo $lang_hrEmpMain_ReportToName?></td>
				 <td><?php echo $lang_hrEmpMain_reportingmethod?></td>
			</tr>
		</thead>
		<tbody>

<?php
    for($c=0;$rset && $c < count($rset); $c++) {

			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';
	    	if ($_SESSION['isAdmin'] == 'No') {
	    		echo "<td>";
               echo "</td>";
	    	} else {
            	echo "<td><input type='checkbox' class='checkbox' name='chksupdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'/></td>";
           }

				   ?><td><a href="javascript:viewSup('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo CommonFunctions::escapeHtml($rset[$c][4])?></a></td><?php
				   for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])
				     echo '<td>' . CommonFunctions::escapeHtml($empname[$a][3]).' '.CommonFunctions::escapeHtml($empname[$a][1]).'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				   	echo '<td>' . $keys[$a] .'</td>';


        echo '</tr>';
        }
?>
		</tbody>
	</table>
</td>
<?php } ?>
<?php
$rset = $this -> popArr['subrset'];
$empname = $this -> popArr['empname'];
?>
<?php
// checking for a records if exsist view the the table and delete btn else no
if ($rset != null && $empname != null){ ?>
<td class="rightList">
	<div class="subHeading"><h3><?php echo $lang_hrEmpMain_subordinateinfomation?></h3></div>
	<div><?php echo $lang_hremp_ie_CurrentSubordinates; ?></div>

	<div class="actionbar">
		<div class="actionbuttons">
<?php if($locRights['add'] && $_SESSION['isAdmin'] == 'Yes') { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('ReportTo');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
<?php	if(!$supervisorEMPMode && $locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delSubEXTReportTo();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

		<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
              	<td></td>
				 <td><?php echo $lang_hrEmpMain_ReportToId?></td>
				 <td><?php echo $lang_hrEmpMain_ReportToName?></td>
				 <td><?php echo $lang_hrEmpMain_reportingmethod?></td>
			</tr>
		</thead>
		<tbody>

<?php
    for($c=0;$rset && $c < count($rset); $c++) {

			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';
	    	if ($_SESSION['isAdmin'] == 'No') {
	    		echo "<td>";
               echo "</td>";
	    	} else {
            	echo "<td><input type='checkbox' class='checkbox' name='chksubdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'/></td>";
           }
				   $subid=$rset[$c][1];
				   ?><td><a href="javascript:viewSub('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo CommonFunctions::escapeHtml($rset[$c][4])?></a></td><?php
				    for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])
				      echo '<td>' . CommonFunctions::escapeHtml($empname[$a][3]).' '.CommonFunctions::escapeHtml($empname[$a][1]).'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				     echo '<td>' . $keys[$a] .'</td>';
        echo '</tr>';
        }

?>
	</tbody>
   </table>
</td>
<?php } ?>
  </tr>
</table>
<?php } ?>
<?php }?>
</div>
