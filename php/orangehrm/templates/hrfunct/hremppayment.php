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

function editPayment() {
	
	if(document.EditPayment.title=='Save') {
		editEXTPayment();
		return;
	}
	
	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.EditPayment.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditPayment.title="Save";
}

function mout() {
	if(document.EditPayment.title=='Save') 
		document.EditPayment.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.EditPayment.title=='Save') 
		document.EditPayment.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function addEXTPayment()
{
	if(document.frmEmp.cmbCurrCode.value=='0') {
			alert("Field should be selected!");
			document.frmEmp.cmbCurrCode.focus();
			return;
	}
	
var cnt=document.frmEmp.txtBasSal;
if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

var min = eval(document.frmEmp.Min.value);
var max = eval(document.frmEmp.Max.value);

if(min > cnt.value || max < cnt.value) {
	alert("Salary should be within Min and Max");
	cnt.focus();
	return;
}

document.frmEmp.paymentSTAT.value="ADD";
document.frmEmp.submit();
}

function addCur() {
	document.frmEmp.paymentSTAT.value='OWN';
	document.frmEmp.submit();
}

function editEXTPayment()
{
var cnt=document.frmEmp.txtBasSal;
if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

var min = eval(document.frmEmp.Min.value);
var max = eval(document.frmEmp.Max.value);

if(min > cnt.value || max < cnt.value) {
	alert("Salary should be within Min and Max");
	cnt.focus();
	return;
}

  document.frmEmp.paymentSTAT.value="EDIT";
  document.frmEmp.submit();
}

function delEXTPayment() {
      var check = 0;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
            {
              alert("Select atleast one check box");
              return;
            }


    //alert(cntrl.value);
    document.frmEmp.paymentSTAT.value="DEL";
    document.frmEmp.submit();
}

</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

        <input type="hidden" name="paymentSTAT" value="">

               <input type="hidden" name="txtSalGrdId" value="<?=$empdet[0][5]?>">
<?
	if(isset($this ->popArr['editArr'])) {
	 $edit = $this -> popArr['editArr'];
?>
 	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$paygrade?></td>
    				  <td><strong><?=$empdet[0][5]?></strong></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$currency?></td>
						<td align="left" valign="top"><input type="hidden" name="cmbCurrCode" value="<?=$edit[0][2]?>">
						<strong>
<?
						$currlist=$this -> popArr['currlist'];
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2])
						       echo $currlist[$c][0];
?>						
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$minpoint?></td>
						<td align="left" valign="top"><strong>
<?
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2]) {
						    	echo "<input type='hidden' name='Min' value='" .$currlist[$c][3]. "'>";
						    	echo $currlist[$c][3] ;
						    }
?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$maxpoint?></td>
						<td align="left" valign="top"><strong>
<?
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2]) {
						    	echo "<input type='hidden' name='Max' value='" .$currlist[$c][5]. "'>";
						    	echo $currlist[$c][5];
						    }
?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$bassalary?></td>
						<td align="left" valign="top"><input type="text" disabled name="txtBasSal" value="<?=$edit[0][3]?>">
						</td>
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
                      <td><?=$paygrade?></td>
    				  <td><strong><?=$empdet[0][5]?></strong></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$currency?></td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> onChange='addCur();' name='cmbCurrCode'>
                       						<option value="0">--Select Currency--</option>
<?
 		
						$curlist= $this->popArr['curlist'];
						for($c=0;$curlist && count($curlist)>$c;$c++)
							if(isset($this->popArr['cmbCurrCode']) && $this->popArr['cmbCurrCode']==$curlist[$c][2]) 
								   echo "<option selected value=" . $curlist[$c][2] . ">" . $curlist[$c][0] . "</option>";
								  
								 else
								   echo "<option value=" . $curlist[$c][2] . ">" . $curlist[$c][0] . "</option>";
							   
								echo "</select>";
?>					
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$minpoint?></td>
						<td align="left" valign="top"><strong>
<?						
						if(isset($this->popArr['cmbCurrCode'])) {
							for($c=0;count($curlist)>$c;$c++)
								if($curlist[$c][2]==$this->popArr['cmbCurrCode']) {
									echo "<input type='hidden' name='Min' value='" .$curlist[$c][3]. "'>";
									echo $curlist[$c][3];
								}
							}
?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$maxpoint?></td>
						<td align="left" valign="top"><strong>
<?
						if(isset($this->popArr['cmbCurrCode'])) {
							for($c=0;count($curlist)>$c;$c++)
								if($curlist[$c][2]==$this->popArr['cmbCurrCode']) {
									echo "<input type='hidden' name='Max' value='" .$curlist[$c][5]. "'>";
									echo $curlist[$c][5];
								}
							}
?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$bassalary?></td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtBasSal">
						</td>
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
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$assignedsalary?></h3></td>
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
						 <td><strong><?=$currency?></strong></td>
						 <td><strong><?=$bassalary?></strong></td>
					</tr>
<?
			$rset = $this->popArr['rset'];
			$currlist=$this->popArr['currAlllist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($currlist)>$a;$a++)
			    if($currlist[$a][0]==$rset[$c][2])
				   $fname=$currlist[$a][1];
            echo "<td><a href='" . $_SERVER['PHP_SELF'] . "?reqcode=" . $this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editID1=" . $rset[$c][1] . "&editID2=" . $rset[$c][2] . "'>" . $fname . "</a></td>";
            echo '<td>' . $rset[$c][3] .'</td>';
        echo '</tr>';
        }

?>
</table>

<? } ?>