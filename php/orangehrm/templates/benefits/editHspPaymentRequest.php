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

$year = $records[0];
$allotmets = $records[1];
$type = $records[2];
$request = $records[3];
$authorize = $records[4];
$employeeId = $records[5];
$id = $records[6];
$plans = $records[9];
?>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">

action = '?benefitcode=Benefits&action=';

function cancelAddPayPeriod() {
	window.history.go(-1);
}

function decimal(txt) {
	regExp = /^[0-9]+(\.[0-9]+)*$/;
	return regExp.test(txt.value);
}

function addPayPeriod() {

	err = false;
	msg = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

<?php if ($type == 0) { ?>
		dateIncurred = strToDate($('txtDateIncurred').value, YAHOO.OrangeHRM.calendar.format);

		if (!dateIncurred) {
			err = true;
			msg += " - <?php echo $lang_Benefits_Common_InvalidDateIncurred; ?>\n"
		}

		if ($('txtProviderName').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ProviderNameEmpty; ?>\n"
		}

		if ($('txtPersonIncurringExpense').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_PersonIncurringExpenseAmountEmpty; ?>\n"
		}

		if ($('txtExpenseDescription').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ExpenseDescriptionEmpty; ?>\n"
		}

		if ($('txtExpenseAmount').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ExpenseAmountEmpty; ?>\n"
		}

		var txtExpenseAmount = document.frmHspRequest.txtExpenseAmount;

		if (!decimal(txtExpenseAmount)) {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ExpenseAmountInvalid; ?>\n"
		}

		if ($('txtPaymentMadeTo').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_PaymentMadeToEmpty; ?>\n"
		}

		/*if ($('txtThirdPartyAccountNumber').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ThirdPartyAccountNumberEmpty; ?>\n"
		}*/
<?php } else { ?>
		datePaid = strToDate($('txtDatePaid').value, YAHOO.OrangeHRM.calendar.format);

		if (!datePaid) {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_InvalidDatePaid; ?>\n"
		}

		if ($('txtCheckNumber').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_InvalidCheckNumber; ?>\n"
		}
<?php } ?>

	if (err) {
		alert(msg);
	} else {
		document.frmHspRequest.action = action+'Hsp_Request_Add';
		document.frmHspRequest.submit();
	}
}

function mout() {
	var Edit = $("btnEdit");

	if(Edit.title=='Save')
		Edit.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function mover() {
	var Edit = $("btnEdit");

	if(Edit.title=='Save')
		Edit.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function editRequest()
{
	var Edit = $("btnEdit");

	if(Edit.title=='Save') {
		saveRequest();
		return;
	}

	enableFields = ['txtDateIncurred', 'btnDate1', 'txtMailAddress', 'txtProviderName', 'txtPersonIncurringExpense',
					'txtComments', 'txtExpenseDescription', 'txtExpenseAmount', 'txtPaymentMadeTo', 'txtThirdPartyAccountNumber', 'txtDatePaid', 'btnDate', 'txtCheckNumber', 'txtHrNotes'];

	for (i=0; enableFields.length>i; i++) {
		$(enableFields[i]).disabled=false;
	}

	Edit.src="../../themes/beyondT/pictures/btn_save.gif";
	Edit.title="Save";
}

function saveRequest() {
	err = false;
	msg = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

<?php if ($type == 0) { ?>
		dateIncurred = strToDate($('txtDateIncurred').value, YAHOO.OrangeHRM.calendar.format);

		if (!dateIncurred) {
			err = true;
			msg += " - <?php echo $lang_Benefits_Common_InvalidDateIncurred; ?>\n"
		}

		if ($('txtProviderName').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ProviderNameEmpty; ?>\n"
		}

		if ($('txtPersonIncurringExpense').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_PersonIncurringExpenseAmountEmpty; ?>\n"
		}

		if ($('txtExpenseDescription').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ExpenseDescriptionEmpty; ?>\n"
		}

		if ($('txtExpenseAmount').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ExpenseAmountEmpty; ?>\n"
		}

		if ($('txtPaymentMadeTo').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_PaymentMadeToEmpty; ?>\n"
		}

		if ($('txtThirdPartyAccountNumber').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_ThirdPartyAccountNumberEmpty; ?>\n"
		}
<?php } else { ?>
		datePaid = strToDate($('txtDatePaid').value, YAHOO.OrangeHRM.calendar.format);

		if (!datePaid) {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_InvalidDatePaid; ?>\n"
		}

		if ($('txtCheckNumber').value.trim() == "") {
			err = true;
			msg += " - <?php echo $lang_Benefits_Errors_InvalidCheckNumber; ?>\n"
		}
<?php } ?>

	if (err) {
		alert(msg);
	} else {
		document.frmHspRequest.action = action+'Hsp_Request_Save<?php echo "&id=" . $id; ?>';
		document.frmHspRequest.submit();
	}
}

function cancelEditRequest() {
	window.history.go(-1);
}

<?php if ($type == 1) { ?>

function denyRequest() {
	window.location = action+'Deny_Request&id=<?php echo $request->getId(); ?>';
}

function deleteRequest() {
	window.location = action+'Delete_Request&id=<?php echo $request->getId(); ?>';
}

<?php } ?>

function paymentsDue() {
	window.location = '?benefitcode=Benefits&action=List_Hsp_Due';
}

YAHOO.OrangeHRM.container.init();
</script>
<h2>
	<?php echo $lang_Benefits_HspRequestForm; ?>
	<hr/>
</h2>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Benefits_Errors_' . $expString;
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
<?php if (isset($_SESSION['paid']) && $_SESSION['paid'] == "Yes") { ?>
<p>
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="paymentsDue();">
</p>
<?php } elseif ($type > 1) { ?>
<p>
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="cancelEditRequest();">
</p>
<?php } ?>
<form action="?benefitcode=Benefits&action=" method="post" name="frmHspRequest" id="frmHspRequest">
<?php if (isset($request) && ($request->getId() != null)) { ?>
<input type="hidden" name="txtId" value="<?php echo $request->getId(); ?>"/>
<?php } ?>
<input type="hidden" name="txtEmployeeId" value="<?php echo isset($request)?$request->getEmployeeId():$employeeId; ?>" />

<table border="0" cellpadding="2" cellspacing="0">
	<thead>
	  	<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_Plan; ?></td>
			<td><?php if (is_array($plans)) { ?>
			<select name="cmbPlanName">
			<?php
				foreach($plans as $plan) { ?>
				<option value="<?php echo $plan; ?>"><?php echo $plan; ?></option>
				<?php }
			?>
			</select>
			<?php } else { 
				echo $plans;
			?>
			<input type="hidden" name="hidPlanName" value="<?php echo $plans; ?>" />
			<?php
			} ?></td>
			<td><?php echo $lang_Benefits_MailingAddress; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Benefits_DateIncurred; ?></td>
			<td>
				<input name="txtDateIncurred" type="text" id="txtDateIncurred"  size="10"
					value="<?php echo isset($request)?LocaleUtil::getInstance()->formatDate($request->getDateIncurred()):''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?>/>
          		<input type="button" name="Date" id="btnDate1" value="  " class="calendarBtn" <?php echo ($type != 0)?'disabled':''; ?>/>
          	</td>
			<td rowspan="4">
				<textarea name="txtMailAddress" id="txtMailAddress" rows="4" cols="50"
					<?php echo ($type != 0)?'disabled':''; ?>><?php echo isset($request)?$request->getMailAddress():''; ?></textarea>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_ProviderName; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtProviderName" id="txtProviderName"
					value="<?php echo isset($request)?$request->getProviderName():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_PersonIncurringExpense; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">

				<input type="text" name="txtPersonIncurringExpense" id="txtPersonIncurringExpense"
					value="<?php echo isset($request)?$request->getPersonIncurringExpense():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />

			</td>
			<td><?php echo $lang_Benefits_Comments; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_ExpenseDescription; ?></td>
			<td rowspan="5">
				<textarea name="txtComments" id="txtComments" rows="4" cols="50"
					<?php echo ($type != 0)?'disabled':''; ?>><?php echo isset($request)?$request->getComments():''; ?></textarea>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtExpenseDescription" id="txtExpenseDescription"
					value="<?php echo isset($request)?$request->getExpenseDescription():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_ExpenseAmount; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtExpenseAmount" id="txtExpenseAmount"
					value="<?php echo isset($request)?$request->getExpenseAmount():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_PaymentMadeTo; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtPaymentMadeTo" id="txtPaymentMadeTo"
					value="<?php echo isset($request)?$request->getPaymentMadeTo():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />
			</td>
			<td></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_ThirdPartyAccountNumber; ?></td>
			<td class="hrnote" rowspan="3"><?php echo ($type == 0)?$lang_Benefits_HspRequestFormNote[0]:""; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtThirdPartyAccountNumber" id="txtThirdPartyAccountNumber"
					value="<?php echo isset($request)?$request->getThirdPartyAccountNumber():''; ?>"
					<?php echo ($type != 0)?'disabled':''; ?> />
			</td>

			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_DatePaid; ?></td>

			<td class="tableMiddleRight"></td>
		</tr>

		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtDatePaid" id="txtDatePaid"
					value="<?php echo isset($request)?$request->getDatePaid():''; ?>"
					<?php echo ($type != 0 || $_SESSION['isAdmin'] != "Yes")?'disabled':''; ?> />
				<input type="button" name="Date" value="  " class="calendarBtn" id="btnDate"
					<?php echo ($type != 0 || $_SESSION['isAdmin'] != "Yes")?'disabled':''; ?> />
			</td>
			<td>
			<?php if ($type == 0) { ?>
				<img onClick="addPayPeriod();"
		             style="margin-top:10px;"
		             onMouseOut="this.src='../../themes/beyondT/icons/submit.gif';"
		             onMouseOver="this.src='../../themes/beyondT/icons/submit_o.gif';"
		             src="../../themes/beyondT/icons/submit.gif" alt="Save" />
		         <img onClick="cancelAddPayPeriod();"
		             style="margin-top:10px;"
		             onMouseOut="this.src='../../themes/beyondT/icons/cancel.gif';"
		             onMouseOver="this.src='../../themes/beyondT/icons/cancel_o.gif';"
		             src="../../themes/beyondT/icons/cancel.gif" alt="Cancel" />
			<?php }  ?>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_CheckNumber; ?></td>
			<td></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2">
				<input type="text" name="txtCheckNumber" id="txtCheckNumber"
					value="<?php echo isset($request)?$request->getCheckNumber():''; ?>"
					<?php echo ($type != 0 || $_SESSION['isAdmin'] != "Yes")?'disabled':''; ?> />
			</td>
			<td></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2"><?php echo $lang_Benefits_HrNotes; ?></td>
			<td><?php echo ($type == 1)?$lang_Benefits_HspRequestFormNote[1]:""; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="2" rowspan="2">
				<textarea name="txtHrNotes" id="txtHrNotes" rows="2" cols="50" wrap="off" <?php echo ($type != 0 || $_SESSION['isAdmin'] != "Yes")?'disabled':''; ?> ><?php echo isset($request)?$request->getHrNotes():''; ?></textarea>
			</td>
			<td>
				<?php if ($type == 1) { ?>

					<input type="image" class="button1" id="btnEdit" src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="editRequest(); return false;">

			         <img onClick="cancelEditRequest();"
			             style="margin-top:10px;"
			             onMouseOut="this.src='../../themes/beyondT/icons/cancel.gif';"
			             onMouseOver="this.src='../../themes/beyondT/icons/cancel_o.gif';"
			             src="../../themes/beyondT/icons/cancel.gif" alt="Cancel" />

			         <img onClick="denyRequest();"
			             style="margin-top:10px;"
			             onMouseOut="this.src='../../themes/beyondT/icons/reject.gif';"
			             onMouseOver="this.src='../../themes/beyondT/icons/reject_o.gif';"
			             src="../../themes/beyondT/icons/reject.gif" alt="Reject" />

			         <img onClick="deleteRequest();"
			             style="margin-top:10px;"
			             onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.gif';"
			             onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';"
			             src="../../themes/beyondT/pictures/btn_delete.gif" alt="Delete" />
				<?php } ?>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
</form>
