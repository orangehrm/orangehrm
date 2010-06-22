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
<script type="text/javaScript"><!--//--><![CDATA[//><!--
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
	} else if (parseInt(control.value).toString().length > 9) {
		errors[errors.length] = "<?php echo $lang_hrEmpMain_DirectDebitAmountTooLarge; ?>";
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

//--><!]]></script>
<?php
	$transactionTypes = array(EmpDirectDebit::TRANSACTION_TYPE_BLANK => $lang_hrEmpMain_DirectDebitTransactionTypeBlank,
					EmpDirectDebit::TRANSACTION_TYPE_PERCENTAGE => $lang_hrEmpMain_DirectDebitTransactionTypePercentage,
					EmpDirectDebit::TRANSACTION_TYPE_FLAT => $lang_hrEmpMain_DirectDebitTransactionTypeFlat,
					EmpDirectDebit::TRANSACTION_TYPE_FLAT_MINUS => $lang_hrEmpMain_DirectDebitTransactionTypeFlatMinus);
?>
<div id="parentPaneDirectDebit" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<input type="hidden" name="directDebitSTAT" value=""/>
<?php
if (isset ($this->getArr['DDSEQ'])) {
	$edit = $this->popArr['editDDForm'];
	$disabled = "";
?>
	<div id="editPaneDirectDebit" >
		<table style="height:170px;padding:0 5px 0 5px;" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAccount" maxlength="100"
              		value="<?php echo CommonFunctions::escapeHtml($edit->getAccount());?>"/></td>
          </tr>
          <tr>
			  <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAccountType; ?></td>
			  <td width="30">&nbsp;</td>
			  <td>
			  	<input type="hidden" name="DDSeqNo" value="<?php echo $edit->getDDSeqNo(); ?>"/>
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeChecking; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		<?php echo $edit->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_CHECKING ? 'checked="checked"' : "";?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_CHECKING;?>"/>
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeSavings; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		<?php echo $edit->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_SAVINGS ? 'checked="checked"' : "";?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_SAVINGS;?>"/>
			  </td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDRoutingNumber"
              	value="<?php echo CommonFunctions::escapeHtml($edit->getRoutingNumber()); ?>"/></td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAmount"
              	value="<?php echo $edit->getAmount(); ?>"/></td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></td>
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
			  </td>
		  </tr>
		</table>
<?php	if($locRights['edit']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnEditDirectDebit" id="btnEditDirectDebit"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editDirectDebit(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>

	</div>
	<?php


} else {
	$disabled = '';
?>
	<div id="addPaneDirectDebit" class="<?php echo ($this->popArr['empDDAss'] != null)?"addPane":""; ?>" >
		<table style="height:170px;padding:0 5px 0 5px;" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAccount" value=""/></td>
          </tr>
          <tr>
			  <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAccountType; ?></td>
			  <td width="30">&nbsp;</td>
			  <td>
			  	<input type="hidden" name="DDSeqNo" value=""/>
			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeChecking; ?>
			  	<input type="radio" <?php echo $disabled;?> checked="checked"
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_CHECKING;?>"/>

			  	<?php echo $lang_hrEmpMain_DirectDebitAccountTypeSavings; ?>
			  	<input type="radio" <?php echo $disabled;?>
			  		name="DDAccountType" value="<?php echo EmpDirectDebit::ACCOUNT_TYPE_SAVINGS;?>"/>
			  </td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDRoutingNumber" value=""/></td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></td>
              <td width="30">&nbsp;</td>
              <td><input type="text" <?php echo $disabled;?> name="DDAmount" value=""/></td>
          </tr>
          <tr>
              <td nowrap="nowrap"><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></td>
              <td width="30">&nbsp;</td>
              <td>
              <select <?php echo $disabled;?> name="cmbTransactionType" id="cmbTransactionType">
              	<option selected="selected" value="0"><?php echo $lang_hrEmpMain_DirectDebitSelectTransactionType?></option>
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
				  </td>
				</tr>
		</table>
<?php	if($locRights['add']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddDirectDebit" id="btnAddDirectDebit"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addDirectDebit(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
 <?php	} ?>

	</div>
<?php } ?>
<div id="tableDirectDebit">
<?php


	$rset = $this->popArr['empDDAss'];
	if (!empty($rset)) {
?>
	<div class="subHeading"><h3><?php echo $lang_hrEmpMain_DirectDebitAssigned; ?></h3></div>

	<div class="actionbar">
		<div class="actionbuttons">
<?php if ($locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('DirectDebit');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
<?php	if ($locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delDirectDebit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

		<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
            	<td width="50">&nbsp;</td>
				<td><?php echo $lang_hrEmpMain_DirectDebitAccount; ?></td>
				<td><?php echo $lang_hrEmpMain_DirectDebitAccountType;?></td>
				<td><?php echo $lang_hrEmpMain_DirectDebitRoutingNumber;?></td>
				<td><?php echo $lang_hrEmpMain_DirectDebitAmount; ?></td>
				<td><?php echo $lang_hrEmpMain_DirectDebitTransactionType; ?></td>
			</tr>
		</thead>
		<tbody>
<?php

		$row = 0;
		foreach ($rset as $ddinfo) {
			$cssClass = ($row%2) ? 'even' : 'odd';
			$row++;
        	echo '<tr class="' . $cssClass . '">';
			echo "<td><input type='checkbox' class='checkbox' name='chkdebitdel[]' value='" . $ddinfo->getDDSeqNo() . "'/></td>";
			if ($ddinfo->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_CHECKING) {
				$type =  $lang_hrEmpMain_DirectDebitAccountTypeChecking;
			} else if ($ddinfo->getAccountType() == EmpDirectDebit::ACCOUNT_TYPE_SAVINGS) {
				$type = $lang_hrEmpMain_DirectDebitAccountTypeSavings;
			}
?> <td><a href="#" onmousedown="viewDirectDebit(<?php echo  $ddinfo->getDDSeqNo();?>)" ><?php echo CommonFunctions::escapeHtml($ddinfo->getAccount());?></a></td> <?php

			echo '<td>' . $type . '</td>';
			echo '<td>' . CommonFunctions::escapeHtml($ddinfo->getRoutingNumber()) . '</td>';
			echo '<td>' . $ddinfo->getAmount() . '</td>';
			echo '<td>' . $transactionTypes[$ddinfo->getTransactionType()] . '</td>';
			echo '</tr>';
		}
?>
		</tbody>
    </table>
<?php } ?>
</div>
<?php } ?>
</div>
