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

function moutPayment() {
	if(document.EditPayment.title=='Save') 
		document.EditPayment.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function moverPayment() {
	if(document.EditPayment.title=='Save') 
		document.EditPayment.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function addEXTPayment() {
	
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

	var min = eval(document.frmEmp.txtMinCurrency.value);
	var max = eval(document.frmEmp.txtMaxCurrency.value);
	
	if(min > cnt.value || max < cnt.value) {
		alert("Salary should be within Min and Max");
		cnt.focus();
		return;
	}

document.frmEmp.paymentSTAT.value="ADD";
qCombo(14);
}

function editEXTPayment() {
	
	var cnt=document.frmEmp.txtBasSal;
	if(!numeric(cnt)) {
		alert("Field should be Numeric");
		cnt.focus();
		return;
	}
	
	var min = eval(document.frmEmp.txtMinCurrency.value);
	var max = eval(document.frmEmp.txtMaxCurrency.value);
	
	if(min > cnt.value || max < cnt.value) {
		alert("Salary should be within Min and Max");
		cnt.focus();
		return;
	}
	
  document.frmEmp.paymentSTAT.value="EDIT";
  qCombo(14);
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
	qCombo(14);
}

function viewPayment(pay,curr) {
	
	document.frmEmp.action = document.frmEmp.action + "&PAY=" + pay + "&CUR=" + curr;
	document.frmEmp.pane.value = 14;
	document.frmEmp.submit();
}

</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

        <input type="hidden" name="paymentSTAT" value="">
        <input type="hidden" name="txtSalGrdId" value="<?=$this->popArr['salGrd']?>">
   	<?
			$salGrd = $this->popArr['salGrd'];
			
			if($salGrd === null) {
				echo "<p align='center'><strong>Please Select a Job Title for this Employee <a href='javascript:displayLayer(2)'>here</a></strong></p>";
			}
 	?>

<?
	if(isset($this ->popArr['editPaymentArr'])) {
	 $edit = $this -> popArr['editPaymentArr'];
?>
 	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$paygrade?></td>
    				  <td><strong>
    				 <? $salgradelist = $this->popArr['salgradelist'];
    				    for($c=0; $salgradelist && count($salgradelist) > $c; $c++)
    				    	if($this->popArr['salGrd'] == $salgradelist[$c][0])
    				    		echo $salgradelist[$c][1];
    				 ?>
    				  </strong></td>
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
						    	echo "<input type='hidden' name='txtMinCurrency' value='" .$currlist[$c][3]. "'>";
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
						    	echo "<input type='hidden' name='txtMaxCurrency' value='" .$currlist[$c][5]. "'>";
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
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutPayment();" onmouseover="moverPayment();" name="EditPayment" onClick="editPayment();">
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
    				  <td><strong>
    				 <? $salgradelist = $this->popArr['salgradelist'];
    				    for($c=0; $salgradelist && count($salgradelist) > $c; $c++)
    				    	if($this->popArr['salGrd'] == $salgradelist[$c][0])
    				    		echo $salgradelist[$c][1];
    				 ?>
    				  </strong></td>
					</tr>
					  <tr> 
						<td valign="top"><?=$currency?></td>
						<td align="left" valign="top"><select <?=($locRights['add'] && $salGrd !== null)? '':'disabled'?> onChange="xajax_getMinMaxCurrency(this.value,'<?=$this->popArr['salGrd']?>')" name='cmbCurrCode'>
                       						<option value="0">--Select Currency--</option>
<?
						$curlist= $this->popArr['currlist'];
						for($c=0;$curlist && count($curlist)>$c;$c++)
								   echo "<option value=" . $curlist[$c][2] . ">" . $curlist[$c][0] . "</option>";
?>							   
							</select></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$minpoint?></td>
						<td align="left" valign="top"><strong>
							<input type='hidden' name='txtMinCurrency' id='txtMinCurrency'>
							<div id='divMinCurrency'>-N/A-</div>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$maxpoint?></td>
						<td align="left" valign="top"><strong>
							<input type='hidden' name='txtMaxCurrency' id='txtMaxCurrency'>
							<div id='divMaxCurrency'>-N/A-</div>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top"><?=$bassalary?></td>
						<td align="left" valign="top"><input type="text" <?=($locRights['add'] && $salGrd !== null) ? '':'disabled'?> name="txtBasSal">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
					<?	if($locRights['add']) { ?>
					        <img border="0" title="Save" onClick="<?=$salGrd !== null ? 'addEXTPayment()': ''?>;" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
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
        <img title="Delete" onclick="delEXTPayment();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
			$rset = $this->popArr['rsetPayment'];
			$currlist=$this->popArr['currAlllist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpaydel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($currlist)>$a;$a++)
			    if($currlist[$a][0]==$rset[$c][2])
				   $fname=$currlist[$a][1];
            ?><td><a href="javascript:viewPayment('<?=$rset[$c][1]?>','<?=$rset[$c][2]?>')"><?=$fname?></a></td><?
            echo '<td>' . $rset[$c][3] .'</td>';
        echo '</tr>';
        }

?>
</table>

<? } ?>