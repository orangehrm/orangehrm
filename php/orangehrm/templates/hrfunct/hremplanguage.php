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

	$lantype  = $this->popArr['lantype'];
	$grdcodes = $this->popArr['grdcodes'];
?>

<script language="JavaScript">
function editLang()
{
	if(document.EditLang.title=='Save') {
		editEXTLang();
		return;
	}
	
	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.EditLang.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditLang.title="Save";
}

function moutLang() {
	if(document.EditLang.title=='Save') 
		document.EditLang.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.EditLang.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function moverLang() {
	if(document.EditLang.title=='Save') 
		document.EditLang.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.EditLang.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}


function addEXTLang()
{
	if(document.frmEmp.cmbLanCode.value=='0') {
		alert("Field should be selected");
		document.frmEmp.cmbLanCode.focus();
		return;
	}
	
	if(document.frmEmp.cmbLanType.value=='0') {
		alert("Field should be selected");
		document.frmEmp.cmbLanType.focus();
		return;
	}

	if(document.frmEmp.cmbRatGrd.value=='0') {
		alert("Field should be selected");
		document.frmEmp.cmbRatGrd.focus();
		return;
	}

  document.frmEmp.langSTAT.value="ADD";
  qCombo(11);
}

function editEXTLang() {
  document.frmEmp.langSTAT.value="EDIT";
  qCombo(11);
}

function viewLang(lanSeq,lanFlu) {
	
	document.frmEmp.action=document.frmEmp.action + "&lanSEQ=" + lanSeq + "&lanFLU=" + lanFlu;
	document.frmEmp.pane.value=11;
	document.frmEmp.submit();
}

function delEXTLang() {
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

    document.frmEmp.langSTAT.value="DEL";
   qCombo(11);
}

</script>

<?  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">

  <tr>
    <td height="27" valign='top'> <p>
        <input type="hidden" name="langSTAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>


<?
if(isset($this->getArr['lanSEQ'])) {
    $edit = $this->popArr['editLanArr'];
?>
        
<br>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$language?></td>
    				  <td><input type="hidden" name="cmbLanCode" value="<?=$edit[0][1]?>"><strong>
<?						$lanlist = $this->popArr['lanlist'];
						for($c=0;count($lanlist)>$c;$c++)
							if($edit[0][1]==$lanlist[$c][0])
							     break;
							     
					  			echo $lanlist[$c][1];
?>
					  </strong></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$fluency?></td>
						<td align="left" valign="top"><input type="hidden" name="cmbLanType" value="<?=$this->getArr['lanFLU']?>"><strong>
<?						
						$index=array_values($lantype);
						$value=array_keys($lantype);
						for($a=0;count($lantype)>$a;$a++)
							if($this->getArr['lanFLU']==$index[$a])
					  			echo $value[$a];
?>
						</td>
					  </tr>
					 
					  <tr> 
						<td valign="top"><?=$ratinggarde?></td>
						<td align="left" valign="top"><select disabled name='cmbRatGrd'>
<?
						$code=array_values($grdcodes);
						 $name=array_keys($grdcodes);
						for($c=0;count($grdcodes)>$c;$c++)
							if($code[$c]==$edit[0][4])
								echo "<option selected value='" . $code[$c] . "'>" . $name[$c] ."</option>";
							else 
								echo "<option value='" . $code[$c] . "'>" . $name[$c] ."</option>";
?>
						</select></td>
					  </tr>

					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
		<?			if($locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutLang();" onmouseover="moverLang();" name="EditLang" onClick="editLang();">
		<?			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
						</td>
					  </tr>
                  </table>

<? } else { ?>
&nbsp;
        
<br><br>
      <table border="0" cellpadding="0" cellspacing="0">
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?=$language?></td>
    				  <td><select name="cmbLanCode" <?=$locRights['add'] ? '':'disabled'?>>
    				  		<option selected value="0">--Select Language--</option>
<?					  
						$lanlist= $this->popArr['lanlist'];
						for($c=0;$lanlist && count($lanlist)>$c;$c++)
							if(isset($this->popArr['cmbLanCode']) && $this->popArr['cmbLanCode']==$lanlist[$c][0]) 
							   echo "<option  value=" . $lanlist[$c][0] . ">" . $lanlist[$c][1] . "</option>";
							 else
							   echo "<option value=" . $lanlist[$c][0] . ">" . $lanlist[$c][1] . "</option>";
?>					  
					  </select></td>
					</tr>
                    <tr>
                      <td width="200"><?=$fluency?></td>
    				  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbLanType">
    				  		<option value="0">---Select Fluency---</option>
<?					  
						$index=array_values($lantype);
						$value=array_keys($lantype);
						for($c=0;$lantype && count($lantype)>$c;$c++)
							   echo "<option value=" . $index[$c] . ">" . $value[$c] . "</option>";
?>					  
					  </select></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$ratinggarde?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbRatGrd'>
    				  		<option value="0">----Select Rating----</option>
<?				
				        $code=array_values($grdcodes);
						$name=array_keys($grdcodes);
						for($c=0;$grdcodes && count($grdcodes)>$c;$c++)
							   echo "<option value=" . $code[$c] . ">" . $name[$c] . "</option>";
?>					  

					</select> 
						</td>
					  </tr>
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="addEXTLang();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
								</td>
					  </tr>
                  </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$assignlanguage?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(11);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<? 	} else { ?>
		<img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg"
<? } ?>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXTLang();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$language?></strong></td>
						 <td><strong><?=$fluency?></strong></td>
					</tr>
<?
$rset = $this->popArr['rsetLang'];

    for($c=0; $rset && $c < count($rset); $c++) {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chklangdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'>";

			for($a=0;count($lanlist)>$a;$a++)
				if($rset[$c][1] == $lanlist[$a][0])
				   $lname=$lanlist[$a][1];
            ?> <td><a href="javascript:viewLang('<?=$rset[$c][1]?>','<?=$rset[$c][2]?>')"><?=$lname?></a></td> <?

            for($a=0;count($lantype)>$a;$a++)
				if($rset[$c][2] == $index[$a])
				   $flu=$value[$a];
            echo '<td>' . $flu .'</a></td>';
        echo '</tr>';
        }

?>
</table>

<? } ?>
                  