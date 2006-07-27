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

function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmEmpSkill;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function goBack() {
		location.href = "./CentralController.php?reqcode=<?=$this->getArr['reqcode']?>&VIEW=MAIN";
	}

function addEXT()
{
	if(document.frmEmpSkill.cmbSkilCode.value=='0') {
		alert("Field should be selected");
		document.frmEmpSkill.cmbSkilCode.focus();
		return;
	}
	
	if (document.frmEmpSkill.txtEmpYears.value == '') {
		alert ("Years of Experience Cannot be Blank!");
		document.frmEmpSkill.txtEmpYears.focus();
		return;
	}
	 
	var txt = document.frmEmpSkill.txtEmpYears;
		if (!numeric(txt)) {
			alert ("Years of Experience Error!");
			txt.focus();
			return;
	}
		
	if (document.frmEmpSkill.txtEmpComments.value == '') {
		alert ("Comments Cannot be Blank!");
		document.frmEmpSkill.txtEmpComments.focus();
		return;
	}

	document.frmEmpSkill.STAT.value="ADD";
    document.frmEmpSkill.submit();
}

function editEXT()
{
  document.frmEmpSkill.STAT.value="EDIT";
  document.frmEmpSkill.submit();
}

function delEXT() {
      var check = 0;
		with (document.frmEmpSkill) {
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

    document.frmEmpSkill.STAT.value="DEL";
    document.frmEmpSkill.submit();
}

function addNewEXT(str){
	var EmpID = str;		
	location.href = "./CentralController.php?id="+EmpID+"&capturemode=updatemode&reqcode=<?=$this->getArr['reqcode']?>";
}
</script>

<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

    <input type="hidden" name="STAT" value="">

<?
if(isset($this->popArr['editArr'])) {
    $edit = $this->popArr['editArr'];
?>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$skill?></td>
    				  <td><input type="hidden" name="cmbSkilCode" value="<?=$edit[0][1]?>"><strong>
<?						$allSkilllist = $this->popArr['allSkilllist'];
						for($c=0;count($allSkilllist)>$c;$c++)
							if($this->getArr['editID1']==$allSkilllist[$c][0])
							     break;
							     
					  	echo $allSkilllist[$c][1];
?>
					  </strong></td>
					</tr>
					  <tr>
                      <td><?=$yearofex?></td>
    				  <td><input type="text" name="txtEmpYears" <?=isset($this->popArr['txtEmpYears']) ? '':'disabled'?> value="<?=isset($this->popArr['txtEmpYears']) ? $this->popArr['txtEmpYears'] : $edit[0][2]?>"></td>
    				  <td width="50">&nbsp;</td>
					  </tr>
					 
					  <tr>
						<td><?=$comments?></td>
						<td> <textarea <?=isset($this->popArr['txtEmpComments']) ? '':'disabled'?>  name="txtEmpComments"><?=isset($this->popArr['txtEmpComments']) ? $this->popArr['txtEmpComments'] : $edit[0][3]?></textarea></td>
    				  <td width="50">&nbsp;</td>
					 </tr>

					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
		<?			if($locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
		<?			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
						</td>
					  </tr>
                  </table>

<? } else { ?>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$skill?></td>
    				  <td><select name="cmbSkilCode" <?=$locRights['add'] ? '':'disabled'?>>
    				  		<option selected value="0">-----------Select Skill-------------</option>
<?					  
						$skilllist= $this->popArr['uskilllist'];
						for($c=0;$skilllist && count($skilllist)>$c;$c++)
							if(isset($this->popArr['cmbSkilCode']) && $this->popArr['cmbSkilCode']==$skilllist[$c][0]) 
							   echo "<option  value=" . $skilllist[$c][0] . ">" . $skilllist[$c][1] . "</option>";
							 else
							   echo "<option value=" . $skilllist[$c][0] . ">" . $skilllist[$c][1] . "</option>";
?>					  
					  </select></td>
					</tr>
                    <tr>
                      <td><?=$yearofex?></td>
    				  <td><input type="text" name="txtEmpYears" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($this->popArr['txtEmpYears']) ? $this->popArr['txtEmpYears'] :''?>"></td>
    				  <td width="50">&nbsp;</td>
					</tr>
					 <tr>
					<td><?=$comments?></td>
						<td> <textarea <?=$locRights['add'] ? '':'disabled'?> name="txtEmpComments"><?=isset($this->popArr['txtEmpComments']) ? $this->popArr['txtEmpComments'] :''?></textarea></td>
    				  <td width="50">&nbsp;</td>
						 </tr>
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
								</td>
					  </tr>
                  </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td width='100%'><h3><?=$assignskills?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$skill?></strong></td>
						 <td><strong><?=$yearofex?></strong></td>
						
					</tr>
<?
$rset = $this->popArr['rsets'] ;
$allSkilllist = $this->popArr['allSkilllist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'>";

			for($a=0;count($allSkilllist)>$a;$a++) 
				if($rset[$c][1] == $allSkilllist[$a][0])
				   $lname=$allSkilllist[$a][1];
			 echo "<td><a href='" .$_SERVER['PHP_SELF']. "?reqcode=" . $this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editID1=" . $rset[$c][1] . "&editID2=" . $rset[$c][2] . "'>" . $lname . "</td>";
			echo '<td>'. $rset[$c][2] .'</a></td>';
			
        echo '</tr>';
        }

?>
     </table>
     
<? } ?>