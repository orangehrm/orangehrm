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

<script language="JavaScript">

function editReportTo() {

	if(document.EditReportTo.title=='Save') {
		editEXTReportTo();
		return;
	}

	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;

	document.EditReportTo.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditReportTo.title="Save";
}

function moutReportTo() {
	if(document.EditReportTo.title=='Save')
		document.EditReportTo.src='../../themes/beyondT/pictures/btn_save.jpg';
	else
		document.EditReportTo.src='../../themes/beyondT/pictures/btn_edit.jpg';
}

function moverReportTo() {
	if(document.EditReportTo.title=='Save')
		document.EditReportTo.src='../../themes/beyondT/pictures/btn_save_02.jpg';
	else
		document.EditReportTo.src='../../themes/beyondT/pictures/btn_edit_02.jpg';
}

function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
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

    //alert(cntrl.value);
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

    //alert(cntrl.value);
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
</script>
<?php
	$supervisorEMPMode = false;
	if ((isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) && (isset($_GET['reqcode']) && ($_GET['reqcode'] === "EMP")) ) {
		$supervisorEMPMode = true;
	}

	$empInfoObj = new EmpInfo();

	if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
        <input type="hidden" name="reporttoSTAT" value="">
<?php	if(isset($this->getArr['editIDSup'])) {	?>
     <input type="hidden" name="txtSupEmpID" value="<?php echo $this->getArr['editIDSup']?>">
     <input type="hidden" name="txtSubEmpID" value="<?php echo $this->getArr['id']?>">
     <input type="hidden" name="oldRepMethod" value="<?php echo $this->getArr['RepMethod']?>">
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?php echo $arrRepType[0]?>">
    				 <strong><?php echo $arrRepType[0]?></strong></td>
					</tr>
					<tr>
						<td valign="top"><?php echo $lang_empview_employeeid?></td>
<?php						$empsupid =$this->getArr['editIDSup']; ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?php echo $this->getArr['editIDSup']?>"><strong>
						<?php echo $empInfoObj->fetchEmployeeId($this->getArr['editIDSup']);?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select disabled name='cmbRepMethod'><strong>
<?php					$keys = array_keys($arrRepMethod);
						$values = array_values($arrRepMethod);
						for($c=0;count($arrRepMethod)>$c;$c++)
							if($this->getArr['RepMethod']==$values[$c]) {
								echo "<option selected value=". $values[$c] . ">" . $keys[$c] . "</option>";
							} else {
								echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
							}
?>
					</tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if(!$supervisorEMPMode && $locRights['edit']) { ?>
							        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutReportTo();" onmouseover="moverReportTo();" name="EditReportTo" onClick="editReportTo();">
			<?php			}  ?>
						</td>
					  </tr>
 </table>
<?php } elseif (isset($this->getArr['editIDSub'])) { ?>
	 <input type="hidden" name="txtSupEmpID" value="<?php echo $this->getArr['id']?>">
     <input type="hidden" name="txtSubEmpID" value="<?php echo $this->getArr['editIDSub']?>">
  	 <input type="hidden" name="oldRepMethod" value="<?php echo $this->getArr['RepMethod']?>">
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?php echo $arrRepType[1]?>">
    				 <strong><?php echo $arrRepType[1]?></strong></td>
					</tr>
					<tr>
						<td valign="top"><?php echo $lang_empview_employeeid; ?></td>
						<?php	$empsubid = $this->getArr['editIDSub'];  ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?php echo $empsubid?>"><strong>
						<?php echo  $empInfoObj->fetchEmployeeId($empsubid); ?>
						</strong></td>
					  </tr>

					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select disabled name="cmbRepMethod"><strong>
<?php
						$keys = array_keys($arrRepMethod);
						$values = array_values($arrRepMethod);
						for($c=0;count($arrRepMethod)>$c;$c++)
							if($this->getArr['RepMethod']==$values[$c]) {
								echo "<option selected value=". $values[$c] . ">" . $keys[$c] . "</option>";
							} else {
								echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
							}
?>
					</tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if(!$supervisorEMPMode && $locRights['edit']) { ?>
				        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutReportTo();" onmouseover="moverReportTo();" name="EditReportTo" onClick="editReportTo();">
		<?php			}  ?>
						</td>
					  </tr>
			</table>

<?php } else { ?>
		<input type="hidden" name="txtSupEmpID">
     	<input type="hidden" name="txtSubEmpID">

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_supervisorsubordinator?></td>
    				  <td>
					  <select <?php echo (!$supervisorEMPMode && $locRights['add']) ? '':'disabled'?> name="cmbRepType">
					  <option value="0"><?php echo $lang_Leave_Common_Select; ?></option>

<?php
							echo "<option value=" . $arrRepType[0] . ">" . $arrRepType[0] . "</option>";
							echo "<option value=" . $arrRepType[1] . ">" . $arrRepType[1] . "</option>";
?>
					  </select></td>
					</tr>
					<tr><td><?php echo $lang_Leave_Common_EmployeeName; ?><td align="left" valign="top"><input type="text" disabled name="cmbRepEmpID" value="" readonly><input type="hidden" disabled name="txtRepEmpID" value="">&nbsp;<input class="button" type="button" value="..." onclick="returnEmpDetail();">
						</td></tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_reportingmethod?></td>
						<td align="left" valign="top"><select <?php echo (!$supervisorEMPMode && $locRights['add']) ? '':'disabled'?> name='cmbRepMethod'>
						   		<option value="0"><?php echo $lang_hrEmpMain_SelectMethod; ?></option>
<?php
									$keys = array_keys($arrRepMethod);
									$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
?>						</select></td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?php	if(!$supervisorEMPMode && $locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
						</td>
					  </tr>
                 </table>
<?php } ?>
	<input type="hidden" name="delSupSub">
<table><tr><td>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

<?php
$rset = $this->popArr['suprset'];
$empname = $this ->popArr['empname'];
if ($rset != Null&& $empname != Null){ //To Handle Hide and Viewe Supervisor Label ?>
<tr>

    <td width='100%'><h3><?php echo $lang_hrEmpMain_supervisorinfomation?></h3><?php echo $lang_hremp_ie_CurrentSupervisors; ?></td>
     <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
 <?php } //Finished Handling label?>
  <tr>
  <td>

  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>

<?php

// checking for a records if exsist view the the table and delete btn else no


?><table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">

<?php
if ($rset != Null&& $empname != Null){ ?>
                    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_empview_employeeid?></strong></td>
						 <td><strong><?php echo $lang_empview_employeename?></strong></td>
						 <td><strong><?php echo $lang_hrEmpMain_reportingmethod?></strong></td>
					</tr>
<?php	if(!$supervisorEMPMode && $locRights['delete']) { ?>
        <img title="Delete" onclick="delSupEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>


<?php }// finished Checking

    for($c=0;$rset && $c < count($rset); $c++) {

        echo '<tr>';
             echo "<td><input type='checkbox' class='checkbox' name='chksupdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";


				   ?><td><a href="javascript:viewSup('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo $rset[$c][4]?></a></td><?php
				   for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])
				     echo '<td>' . $empname[$a][3].' '.$empname[$a][1].'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				   	echo '<td>' . $keys[$a] .'</td>';


        echo '</tr>';
        }

?>
                   </table>
            </td><td>

 <table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>


<?php
$rset = $this -> popArr['subrset'];
$empname = $this -> popArr['empname'];
if ($rset != Null && $empname != Null){ //To Handle Subordinate Label?>
	<tr>
 	<td width='100%'><h3><?php echo $lang_hrEmpMain_subordinateinfomation?></h3><?php echo $lang_hremp_ie_CurrentSubordinates; ?></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
<?php } //Finished Handle?>

  <tr>
  <td>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>

<?php



// checking for a records if exsist view the the table and delete btn else no
?>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
<?php
if ($rset != Null && $empname != Null){ ?>
                    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_empview_employeeid?></strong></td>
						 <td><strong><?php echo $lang_empview_employeename?></strong></td>
						 <td><strong><?php echo $lang_hrEmpMain_reportingmethod?></strong></td>
					</tr>
<?php	if(!$supervisorEMPMode && $locRights['delete']) { ?>
        <img title="Delete" onclick="delSubEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>

<?php }// finished checking

    for($c=0;$rset && $c < count($rset); $c++) {

        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chksubdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";

				   $subid=$rset[$c][1];
				   ?><td><a href="javascript:viewSub('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo $rset[$c][4]?></a></td><?php
				    for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])
				      echo '<td>' . $empname[$a][3].' '.$empname[$a][1].'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				     echo '<td>' . $keys[$a] .'</td>';
        echo '</tr>';
        }

?>
                </table></td></tr></table>
<?php } ?>