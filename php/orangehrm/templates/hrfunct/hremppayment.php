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
 *
 */

$common_func = new CommonFunctions();
?>
<script language="JavaScript">
function decimalCurr(txt) {
	regExp = /^[0-9]+(\.[0-9]+)*$/;

	return regExp.test(txt.value);
}

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
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function moverPayment() {
	if(document.EditPayment.title=='Save')
		document.EditPayment.src='../../themes/beyondT/pictures/btn_save_02.jpg';
	else
		document.EditPayment.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function addEXTPayment() {

	if(document.frmEmp.cmbCurrCode.value=='0') {
			alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>!");
			document.frmEmp.cmbCurrCode.focus();
			return;
	}

	var cnt=document.frmEmp.txtBasSal;
	if(!decimalCurr(cnt)) {
		alert("<?php echo $lang_Error_FieldShouldBeNumeric; ?>");
		cnt.focus();
		return;
	}

	var min = eval(document.frmEmp.txtMinCurrency.value);
	var max = eval(document.frmEmp.txtMaxCurrency.value);

	if(min > cnt.value || max < cnt.value) {
		alert("<?php echo $lang_hremp_SalaryShouldBeWithinMinAndMa; ?>");
		cnt.focus();
		return;
	}

document.frmEmp.paymentSTAT.value="ADD";
qCombo(14);
}

function editEXTPayment() {

	var cnt=document.getElementById('etxtBasSal');
	if(!decimalCurr(cnt)) {
		alert("<?php echo $lang_Error_FieldShouldBeNumeric; ?>");
		cnt.focus();
		return;
	}

	var min = eval(document.frmEmp.txtMinCurrency.value);
	var max = eval(document.frmEmp.txtMaxCurrency.value);

	if(min > cnt.value || max < cnt.value) {
		alert("<?php echo $lang_hremp_SalaryShouldBeWithinMinAndMa; ?>");
		cnt.focus();
		return;
	}

  document.frmEmp.paymentSTAT.value="EDIT";
  qCombo(14);
}

function delEXTPayment() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkpaydel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

    document.frmEmp.paymentSTAT.value="DEL";
	qCombo(14);
}

function viewPayment(pay,curr) {

	document.frmEmp.action = document.frmEmp.action + "&PAY=" + pay + "&CUR=" + curr;
	document.frmEmp.pane.value = 14;
	document.frmEmp.submit();
}
</script>
<?php
$supervisorEMPMode = false;
if ((isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) && (isset($_GET['reqcode']) && ($_GET['reqcode'] === "EMP")) ) {
	$supervisorEMPMode = true;
}
if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
<span id="parentPanePayments" >
        <input type="hidden" name="paymentSTAT" value="">
<?php
	$salGrd = $this->popArr['salGrd'];

	if($salGrd === null) {
		$pleaseSelectJobTitle = preg_replace('/\{(.*)\}/', "<a href='javascript:displayLayer(2)'>$1</a>", $lang_hremp_PleaseSelectJobTitle);
		echo "<p align='center'><strong>$pleaseSelectJobTitle</strong></p>";
	}
?>
	<input type="hidden" name="txtSalGrdId" value="<?php echo $this->popArr['salGrd']?>">
<?php
	if(isset($this->popArr['editPaymentArr'])) {
	 	$edit = $this->popArr['editPaymentArr'];
?>
	<div id="editPanePayments">
 			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
        		<tr>
                      <td><?php echo $lang_hrEmpMain_paygrade?></td>
    				  <td><strong>
    				  <?php $salgradelist = $this->popArr['salgradelist'];
    				    for($c=0; $salgradelist && count($salgradelist) > $c; $c++)
    				    	if($this->popArr['salGrd'] == $salgradelist[$c][0])
    				    		echo $salgradelist[$c][1];
    				 ?>
    				  </strong></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_currency?></td>
						<td align="left" valign="top"><input type="hidden" name="cmbCurrCode" value="<?php echo $edit[0][2]?>">
						<strong>
<?php
						$currlist=$this -> popArr['currlist'];
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2])
						       echo $currlist[$c][0];
?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_minpoint?></td>
						<td align="left" valign="top"><strong>
<?php
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2]) {
						    	echo "<input type='hidden' name='txtMinCurrency' value='" .$currlist[$c][3]. "'>";
						    	echo $common_func->formatSciNo($currlist[$c][3]);
						    }
?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_maxpoint?></td>
						<td align="left" valign="top"><strong>
<?php
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][2]==$edit[0][2]) {
						    	echo "<input type='hidden' name='txtMaxCurrency' value='" .$currlist[$c][5]. "'>";
						    	echo $common_func->formatSciNo($currlist[$c][5]);
						    }
?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_bassalary?></td>
						<td align="left" valign="top"><input type="text" disabled name="txtBasSal" id="etxtBasSal" value="<?php echo $common_func->formatSciNo($edit[0][3]);?>">
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if(!$supervisorEMPMode && $locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onmouseout="moutPayment();" onmouseover="moverPayment();" name="EditPayment" onClick="editPayment();">
		<?php			}  ?>
						</td>
					  </tr>
                  </table>
       </div>
<?php } else { ?>
	<div id="addPanePayments" class="<?php echo ($this->popArr['rsetPayment'] != null)?"addPane":""; ?>" >
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_paygrade; ?></td>
    				  <td><strong>
    				 <?php $salgradelist = $this->popArr['salgradelist'];
    				    for($c=0; $salgradelist && count($salgradelist) > $c; $c++)
    				    	if($this->popArr['salGrd'] == $salgradelist[$c][0])
    				    		echo $salgradelist[$c][1];
    				 ?>
    				  </strong></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_currency; ?></td>
						<td align="left" valign="top"><select <?php echo (!$supervisorEMPMode && ($locRights['add'] && $salGrd !== null))? '':'disabled'?> onChange="xajax_getMinMaxCurrency(this.value,'<?php echo $this->popArr['salGrd']?>')" name='cmbCurrCode'>
                       						<option value="0">-- <?php echo $lang_hremp_SelectCurrency; ?> --</option>
<?php
						$curlist= $this->popArr['unAssCurrList'];
						for($c=0;$curlist && count($curlist)>$c;$c++)
								   echo "<option value=" . $curlist[$c][2] . ">" . $curlist[$c][0] . "</option>";
?>
							</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_minpoint?></td>
						<td align="left" valign="top"><strong>
							<input type='hidden' name='txtMinCurrency' id='txtMinCurrency'>
							<div id='divMinCurrency'>-<?php echo $lang_Common_NotApplicable;?>-</div>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_maxpoint?></td>
						<td align="left" valign="top"><strong>
							<input type='hidden' name='txtMaxCurrency' id='txtMaxCurrency'>
							<div id='divMaxCurrency'>-<?php echo $lang_Common_NotApplicable;?>-</div>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_bassalary?></td>
						<td align="left" valign="top"><input type="text" <?php echo (!$supervisorEMPMode && ($locRights['add'] && $salGrd !== null)) ? '':'disabled'?> name="txtBasSal">
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
					<?php	if(!$supervisorEMPMode && $locRights['add']) { ?>
					        <img border="0" title="Save" onClick="<?php echo $salGrd !== null ? 'addEXTPayment()': ''?>;" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php	} ?>
						</td>
					  </tr>
                   </table>
	</div>
<?php } ?>
<?php
$rset = $this->popArr['rsetPayment'];
$currlist=$this->popArr['currAlllist'];

//Handling the table View
if (($rset != null) && ($currlist != null)) { ?>
	<h3><?php echo $lang_hrEmpMain_assignedsalary; ?></h3>
	<?php if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="showAddPane('Payments');" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" />
	<?php } ?>
	<?php	if(!$supervisorEMPMode && $locRights['delete']) { ?>
	     <img title="Delete" onclick="delEXTPayment();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
	<?php 	} ?>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
                    <tr>
                      	 <td></td>
						 <td><strong><?php echo $lang_hrEmpMain_currency?></strong></td>
						 <td><strong><?php echo $lang_hrEmpMain_bassalary?></strong></td>
					</tr>

<?php

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpaydel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($currlist)>$a;$a++)
			    if($currlist[$a][0]==$rset[$c][2])
				   $fname=$currlist[$a][1];
            ?><td><a href="javascript:viewPayment('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo $fname?></a></td><?php
            echo '<td>' . $common_func->formatSciNo($rset[$c][3]) .'</td>';
        echo '</tr>';
        }

?>
</table>
<?php } ?>
<?php } ?>
</span>