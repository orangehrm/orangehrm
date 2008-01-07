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
?>
<script language="JavaScript">
function delDirectDebit() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkdebitdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

	document.frmEmp.directDebitSTAT.value="DEL";
	qCombo(19);
}

function validateDirectDebit() {

	errors = new Array();
	errorFound = false;

	control = document.frmEmp.DDAccount;
	control.value = trim(control.value);
	if(control.value == '') {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitAccountShouldBeSpecified; ?>";
	}

	control = document.frmEmp.DDRoutingNumber;
	control.value = trim(control.value);
	if(control.value == '') {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitRoutingNumberShouldBeSpecified; ?>";
	} else if(!numbers(control)) {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitRoutingNumberShouldBeNumeric; ?>";
	}

	control = document.frmEmp.DDAmount;
	control.value = trim(control.value);
	if(control.value == '') {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitAmountShouldBeSpecified; ?>";
	} else if (!decimalCurrency(control)) {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitAmountShouldBeNumeric; ?>";
	}

	control = document.frmEmp.cmbTransactionType;
	control.value = trim(control.value);
	if(control.value == '0') {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitTransactionTypeShouldBeSelected; ?>";
	}

	if (errors.length > 0) {
		errStr = "<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr += " - "+errors[i]+"\n";
		}
		alert(errStr);
		return false;
	}

	return true;

}

function addDirectDebit() {
	if (validateDirectDebit()) {
		document.frmEmp.directDebitSTAT.value="ADD";
		qCombo(19);
	}
}

function viewDirectDebit(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&DDSEQ=" + pSeq ;
	document.frmEmp.pane.value = 19;
	document.frmEmp.submit();
}

function editDirectDebit() {
	if (validateDirectDebit()) {
		document.frmEmp.directDebitSTAT.value="EDIT";
		qCombo(19);
	}
}

</script>
<?php
	$transactionTypes = array(EmpDirectDebit::TRANSACTION_TYPE_BLANK => $lang_hrEmpMain_DirectDebitTransactionTypeBlank,
					EmpDirectDebit::TRANSACTION_TYPE_PERCENTAGE => $lang_hrEmpMain_DirectDebitTransactionTypePercentage,
					EmpDirectDebit::TRANSACTION_TYPE_FLAT => $lang_hrEmpMain_DirectDebitTransactionTypeFlat,
					EmpDirectDebit::TRANSACTION_TYPE_FLAT_MINUS => $lang_hrEmpMain_DirectDebitTransactionTypeFlatMinus);
?>
<span id="parentPaneDirectDebit" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<input type="hidden" name="directDebitSTAT" value="">
<?php
if (isset ($this->getArr['DDSEQ'])) {
	$edit = $this->popArr['editDDForm'];
	$disabled = "";
?>
	<div id="editPaneDirectDebit" >
		<table height="170" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAccount"
              		value="<?php echo $edit->getAccount();?>"></td>
          </tr>
          <tr>
			  <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAccountType; ?></td>
			  <td width="30">&nbsp;</td>
			  <td>
			  	<input type="hidden" name="DDSeqNo" value="<?php echo $edit->getDDSeqNo(); ?>">
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeChecking; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		<?php echo $edit->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_CHECKING ? "checked" : "";?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_CHECKING;?>">
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeSavings; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		<?php echo $edit->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_SAVINGS ? "checked" : "";?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_SAVINGS;?>">
			  </td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDRoutingNumber"
              	value="<?php echo $edit->getRoutingNumber(); ?>"></td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAmount"
              	value="<?php echo $edit->getAmount(); ?>"></td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></td>
              <td width="30">&nbsp;</td>
              <td>
              <select <?php echo $disabled;?> name="cmbTransactionType" id="cmbTransactionType">
              	<option value="0"><?php echo $lang_hrEmpMain_DirectDebitSelectTransactionType?></option>
				<?php
					foreach ($transactionTypes as $key=>$type) {
						$selected = ($key == $edit->getTransactionType()) ? "selected": "";
		    			echo "<option " . $selected . " value='" . $key . "'>" . $type . "</option>";
					}
				?>
			  </select>
              </td>
          </tr>

		  <tr>
			  <td>
				<?php	if($locRights['edit']) { ?>
				        <img border="0" title="Save" onClick="editDirectDebit();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
				<?php	} ?>
			  </td>
		  </tr>
		</table>
	</div>
	<?php


} else {
	$disabled = '';
?>
	<div id="addPaneDirectDebit" class="<?php echo ($this->popArr['empDDAss'] != null)?"addPane":""; ?>" >
		<table height="170" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAccount" value=""></td>
          </tr>
          <tr>
			  <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAccountType; ?></td>
			  <td width="30">&nbsp;</td>
			  <td>
			  	<input type="hidden" name="DDSeqNo" value="">
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeChecking; ?>
			  	<input type="radio" <?php echo $disabled;?> checked
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_CHECKING;?>">

			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeSavings; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_SAVINGS;?>">
			  </td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDRoutingNumber" value=""></td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAmount" value=""></td>
          </tr>
          <tr>
              <td nowrap><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></td>
              <td width="30">&nbsp;</td>
              <td>
              <select <?php echo $disabled;?> name="cmbTransactionType" id="cmbTransactionType">
              	<option selected value="0"><?php echo $lang_hrEmpMain_DirectDebitSelectTransactionType?></option>
		<?php

			foreach ($transactionTypes as $key=>$type) {
		    	echo "<option value='" . $key . "'>" . $type . "</option>";
			}
		?>
		</select>
              </td>
          </tr>
          <tr>
				  <td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addDirectDebit();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
<?php	} ?>
				  </td>
				</tr>
		</table>
	</div>
<?php } ?>
<div id="tableDirectDebit">
<?php


	$rset = $this->popArr['empDDAss'];
	if (!empty($rset)) {
?>
	<?php if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="showAddPane('DirectDebit');" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.gif';" src="../../themes/beyondT/pictures/btn_add.gif" />
	<?php } ?>
	<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDirectDebit();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';" src="../../themes/beyondT/pictures/btn_delete.gif">
	<?php 	} ?>
		<table width="550" align="center" border="0" class="tabForm">
			<tr>
            	<td width="50">&nbsp;</td>
				<td><strong><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></strong></td>
				<td><strong><?php echo $lang_hrEmpMain_DirectDebitAccountType;?></strong></td>
				<td><strong><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber;?></strong></td>
				<td><strong><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></strong></td>
				<td><strong><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></strong></td>
			</tr>
<?php


		foreach ($rset as $ddinfo) {
			echo '<tr>';
			echo "<td><input type='checkbox' class='checkbox' name='chkdebitdel[]' value='" . $ddinfo->getDDSeqNo() . "'></td>";
			if ($ddinfo->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_CHECKING) {
				$type =  $lang_hrEmpMain_DirectDebitAccountTypeChecking;
			} else if ($ddinfo->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_SAVINGS) {
				$type = $lang_hrEmpMain_DirectDebitAccountTypeSavings;
			}
?> <td><a href="#" onmousedown="viewDirectDebit(<?php echo  $ddinfo->getDDSeqNo();?>)" ><?php echo $ddinfo->getAccount();?></a></td> <?php

			echo '<td>' . $type . '</td>';
			echo '<td>' . $ddinfo->getRoutingNumber() . '</td>';
			echo '<td>' . $ddinfo->getAmount() . '</td>';
			echo '<td>' . $transactionTypes[$ddinfo->getTransactionType()] . '</td>';
			echo '</tr>';
		}
?>
    </table>
<?php } ?>
</div>
<?php } ?>
</span>
