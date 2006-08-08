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
}

function addEXTReportTo() {
	
	if(document.frmEmp.cmbRepType.value=='0') {
		alert("Field should be selected");
		document.frmEmp.cmbRepType.focus();
		return;
	}
	
	if(document.frmEmp.txtRepEmpID.value=='') {
		alert("Field should be selected");
		document.frmEmp.txtRepEmpID.focus();
		return;
	}

	if(document.frmEmp.cmbRepMethod.value=='0') {
		alert("Field should be selected");
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
              alert("Select atleast one check box");
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
              alert("Select atleast one check box");
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
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
        <input type="hidden" name="reporttoSTAT" value="">
<?	if(isset($this->getArr['editIDSup'])) {	?>
     <input type="hidden" name="txtSupEmpID" value="<?=$this->getArr['editIDSup']?>">
     <input type="hidden" name="txtSubEmpID" value="<?=$this->getArr['id']?>">
     <input type="hidden" name="oldRepMethod" value="<?=$this->getArr['RepMethod']?>">     
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?=$arrRepType[0]?>">
    				 <strong><?=$arrRepType[0]?></strong></td>
					</tr>
					<tr> 
						<td valign="top"><?=$employeeid?></td>
<?						$empsupid =$this->getArr['editIDSup']; ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?=$this->getArr['editIDSup']?>"><strong>
						<?=$this->getArr['editIDSup']?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select disabled name='cmbRepMethod'><strong>
<?						$keys = array_keys($arrRepMethod);
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
		<?			if($locRights['edit']) { ?>
							        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutReportTo();" onmouseover="moverReportTo();" name="EditReportTo" onClick="editReportTo();">
			<?			} else { ?>
							        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
			<?			}  ?>
						</td>
					  </tr>
 </table> 
<? } elseif (isset($this->getArr['editIDSub'])) { ?>
	 <input type="hidden" name="txtSupEmpID" value="<?=$this->getArr['id']?>">
     <input type="hidden" name="txtSubEmpID" value="<?=$this->getArr['editIDSub']?>">
  	 <input type="hidden" name="oldRepMethod" value="<?=$this->getArr['RepMethod']?>">     
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$supervisorsubordinator?></td>
    				 <td align="left" valign="top"><input type="hidden" name="cmbRepType" value="<?=$arrRepType[1]?>">
    				 <strong><?=$arrRepType[1]?></strong></td>
					</tr>
					<tr> 
						<td valign="top"><?=$employeeid?></td>
						<?	$empsubid = $this->getArr['editIDSub'];  ?>
						<td align="left" valign="top"><input type="hidden" name="txtRepEmpID" value="<?=$empsubid?>"><strong>
						<?=$empsubid?>
						</strong></td>
					  </tr>
					  
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select disabled name="cmbRepMethod"><strong>
<?							
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
		<?			if($locRights['edit']) { ?>
				        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutReportTo();" onmouseover="moverReportTo();" name="EditReportTo" onClick="editReportTo();">
		<?			} else { ?>
				        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
						</td>
					  </tr>
			</table>
			
<? } else { ?>
		<input type="hidden" name="txtSupEmpID">
     	<input type="hidden" name="txtSubEmpID">
	
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$supervisorsubordinator?></td>
    				  <td>
					  <select <?=$locRights['add'] ? '':'disabled'?> name="cmbRepType">
					  <option value="0"><?=$selectreporttype?></option>

<?							echo "<option value=" . $arrRepType[0] . ">" . $arrRepType[0] . "</option>";
							echo "<option value=" . $arrRepType[1] . ">" . $arrRepType[1] . "</option>";
?>					  
					  </select></td>
					</tr>
					<tr><td><?=$employeeid?><td align="left" valign="top"><input type="text" disabled name="txtRepEmpID" value="">&nbsp;<input class="button" type="button" value="..." onclick="returnEmpDetail();">
						</td></tr>
					  <tr> 
						<td valign="top"><?=$reportingmethod?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbRepMethod'>
						   		<option value="0"><?=$selecttype?></option>
<?
									$keys = array_keys($arrRepMethod);
									$values = array_values($arrRepMethod);
									for($c=0;count($arrRepMethod)>$c;$c++)
										echo "<option value=" . $values[$c] . ">" . $keys[$c] . "</option>";
?>						</select></td>
					  </tr>
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
						</td>
					  </tr>
                 </table>
<? } ?>
	<input type="hidden" name="delSupSub">
<table><tr><td>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$supervisorinfomation?></h3></td>
     <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delSupEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$employeeid?></strong></td>
						 <td><strong><?=$employeename?></strong></td>
						 <td><strong><?=$reportingmethod?></strong></td>
					</tr>
<?
$rset = $this->popArr['suprset'];
$empname = $this ->popArr['empname'];							

    for($c=0;$rset && $c < count($rset); $c++) {
    	
        echo '<tr>';
             echo "<td><input type='checkbox' class='checkbox' name='chksupdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";
			
				  
				   ?><td><a href="javascript:viewSup('<?=$rset[$c][1]?>','<?=$rset[$c][2]?>')"><?=$rset[$c][1]?></a></td><?
				   for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])  
				     echo '<td>' . $empname[$a][1] .'</td>';
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

  <tr>

    <td width='100%'><h3><?=$subordinateinfomation?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delSubEXTReportTo();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$employeeid?></strong></td>
						 <td><strong><?=$employeename?></strong></td>
						 <td><strong><?=$reportingmethod?></strong></td>
					</tr>
<?

$rset = $this -> popArr['subrset'];
$empname = $this -> popArr['empname'];
							

    for($c=0;$rset && $c < count($rset); $c++) {
    	
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chksubdel[]' value='" . $rset[$c][1] ."|".$rset[$c][2]. "'></td>";
			
				   $subid=$rset[$c][1];
				   ?><td><a href="javascript:viewSub('<?=$rset[$c][1]?>','<?=$rset[$c][2]?>')"><?=$rset[$c][1]?></a></td><?
				    for($a=0; $empname && $a < count($empname); $a++)
				     if($rset[$c][1]==$empname[$a][0])  
				      echo '<td>' . $empname[$a][1] .'</td>';
				   for($a=0;count($arrRepMethod)>$a;$a++)
						if($rset[$c][2] == $values[$a])
				     echo '<td>' . $keys[$a] .'</td>';
        echo '</tr>';
        }

?>
                </table></td></tr></table>
<? } ?>