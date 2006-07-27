<?
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/
?>

<script language="JavaScript">

function editEducation() {
	
	if(document.EditEducation.title=='Save') {
		editEXTEducation();
		return;
	}
	
	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
		
	document.EditEducation.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditEducation.title="Save";
}

function moutEducation() {
	if(document.EditEducation.title=='Save') 
		document.EditEducation.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.EditEducation.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function moverEducation() {
	if(document.EditEducation.title=='Save') 
		document.EditEducation.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.EditEducation.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function addEXTEducation() {
	
	if(document.frmEmp.cmbEduCode.value=='0') {
		alert("Field should be selected");
		document.frmEmp.cmbEduCode.focus();
		return;
	}
	
	var txt = document.frmEmp.txtEmpEduYear;
		if (!numeric(txt)) {
			alert ("Field should be numeric!");
			txt.focus();
			return;
	}
	
	document.frmEmp.educationSTAT.value="ADD";
	qCombo(9);
}

function editEXTEducation() {
	
	var txt = document.frmEmp.txtEmpEduYear;
		if (!numeric(txt)) {
			alert ("Field should be numeric!");
			txt.focus();
			return;
	}
	
  document.frmEmp.educationSTAT.value="EDIT";
  qCombo(9);
}

function delEXTEducation() {
      var check = 0;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0) {
           alert("Select atleast one check box");
           return;
        }

    document.frmEmp.educationSTAT.value="DEL";
	qCombo(9);
}

function viewEducation(edu) {
	
	document.frmEmp.action = document.frmEmp.action + "&EDU=" + edu;
	document.frmEmp.pane.value = 9;
	document.frmEmp.submit();
}

</script>

<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

    <input type="hidden" name="educationSTAT" value="">

<?
if(isset($this->popArr['editEducationArr'])) {
    $edit = $this->popArr['editEducationArr'];
?>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$education?></td>
    				  <td><input type="hidden" name="cmbEduCode" value="<?=$edit[0][1]?>">
						<?	$allEduCodes = $this->popArr['allEduCodes'];
							for($c=0; $allEduCodes && count($allEduCodes)>$c; $c++) 
								if($allEduCodes[$c][0] == $edit[0][1])
									 echo $allEduCodes[$c][1] . ", ". $allEduCodes[$c][2];
									 ?>					  
					  </select></td>
					  
					</tr>
                    <tr>
                      <td><?=$major?></td>
    				  <td><input type="text" name="txtEmpEduMajor" disabled value="<?=$edit[0][2]?>"></td>
    				  <td width="50">&nbsp;</td>
					</tr>
					 <tr>
					<td><?=$year?></td>
						<td> <input type="text" disabled name="txtEmpEduYear" value="<?=$edit[0][3]?>"></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					 <tr>
					<td><?=$gpa?></td>
						<td> <input type="text" disabled name="txtEmpEduGPA" value="<?=$edit[0][4]?>"></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					<tr>
					<td><?=$startdate?></td>
						<td> <input type="text" name="txtEmpEduStartDate" readonly value=<?=$edit[0][5]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpEduStartDate);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpEduEndDate" readonly value=<?=$edit[0][6]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpEduEndDate);return false;"></td>
					 </tr>

					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
		<?			if($locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutEducation();" onmouseover="moverEducation();" name="EditEducation" onClick="editEducation();">
		<?			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
						</td>
					  </tr>
                  </table>

<? } else { ?>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$education?></td>
    				  <td><select name="cmbEduCode" <?=$locRights['add'] ? '':'disabled'?>>
    				  		<option selected value="0">--Select Education--</option>
						<?	$unAssEduCodes = $this->popArr['unAssEduCodes'];
							for($c=0; $unAssEduCodes && count($unAssEduCodes)>$c; $c++) 
								echo "<option value='" .$unAssEduCodes[$c][0] . "'>" .$unAssEduCodes[$c][1]. ", ".$unAssEduCodes[$c][2]. "</option>";
						 ?>					  
					  </select></td>
					</tr>
                    <tr>
                      <td><?=$major?></td>
    				  <td><input type="text" name="txtEmpEduMajor" <?=$locRights['add'] ? '':'disabled'?>></td>
    				  <td width="50">&nbsp;</td>
					</tr>
					 <tr>
					<td><?=$year?></td>
						<td> <input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEmpEduYear"></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					 <tr>
					<td><?=$gpa?></td>
						<td> <input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEmpEduGPA"></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					<tr>
					<td><?=$startdate?></td>
						<td> <input type="text" name="txtEmpEduStartDate" readonly>&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpEduStartDate);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpEduEndDate" readonly>&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpEduEndDate);return false;"></td>
					 </tr>
					 
					 <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addEXTEducation();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
								</td>
					  </tr>
                  </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td width='100%'><h3><?=$assigneducation?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXTEducation();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
                    <tr>
                      	<td></td>
						 <td><strong><?=$education?></strong></td>
						 <td><strong><?=$year?></strong></td>
						 <td><strong><?=$gpa?></strong></td>
						
					</tr>
<?
$rset = $this->popArr['rsetEducation'] ;
$allEduCodes = $this->popArr['allEduCodes'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkedudel[]' value='" . $rset[$c][1] . "'>";

            for($a=0; $allEduCodes && count($allEduCodes)>$a; $a++) 
				if($allEduCodes[$a][0] == $rset[$c][1])
				   $lname = $allEduCodes[$a][1] . ", " .$allEduCodes[$a][2];
				   
			?><td><a href="javascript:viewEducation('<?=$rset[$c][1]?>')"><?=$lname?></td><?
			echo '<td>'. $rset[$c][3] .'</a></td>';
			echo '<td>'. $rset[$c][4] .'</a></td>';
			
        echo '</tr>';
        }

?>
     </table>
     
<? } ?>