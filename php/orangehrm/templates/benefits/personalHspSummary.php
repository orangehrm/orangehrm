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

if ($_SESSION['isAdmin'] == 'Yes') {
	echo "<pre>"; print_r($records); echo "</pre>"; exit();
	$year = $records[2];
	$summary = $records[1];
} else {
	$year = $records[0];
	$summary = $records[1];
}
?>
<h2>
	<?php echo "$lang_Benefits_PersonalHspSummary : $year";
		  if (isset($summary[0])) {
		  	echo " : {$summary[0]['employee_name']}";
		  }
	?>
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
<script type="text/javascript">
var commonAction = '?benefitcode=Benefits&year=<?php echo $year; ?>&employeeId=<?php echo $summary[0]['emp_number']; ?>&action=';

function edit() {
	with (document.frmMain) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'txtHspValue[]') || (elements[i].name == 'txtAmountPerDay[]') || (elements[i].name == 'txtTotalAcrued[]')) {
				elements[i].disabled = '';
			}
		}
	}
	document.getElementById('btnAdd').style.display = 'none';
	document.getElementById('btnSave').style.display = 'inline';
}

function save() {
	with (document.frmMain) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'txtHspValue[]') && (elements[i].value == '')) {
				alert('<?php echo $lang_Benefits_Error_HspValueCannotBeEmpty; ?>');
				elements[i].focus();
				return false;
			}
		}
	}

	document.frmMain.action = commonAction+'Save_Hsp_Values_Employee';
	document.frmMain.submit();
}

function haltPlan(param) {
	<?php if($_SESSION['isAdmin']!='Yes') {?>
	halt = confirm("<?php echo $lang_Benefits_Employee_Halts_Plan[0] ."". date('Y/m/d', time()) ."". $lang_Benefits_Employee_Halts_Plan[1]; ?>");
	if(halt == true) {
		window.location = param;
	}
	<?php }else {?>
		window.location = param;
		<?php }?>
}

function goBack() {
	window.history.go(-1);
}
</script>
<?php if (count($summary) == 0) { ?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php } else { ?>
<div id="controls">
 	<input type="button" value="<?php echo $lang_Common_Back; ?>" class="backbutton"
 		onmouseout="moutButton(this);" onmouseover="moverButton(this)"
		onclick="goBack();" />

 	<?php if($_SESSION['isAdmin']=='Yes') {?>
 	<img id="btnAdd" title="Add" onClick="edit();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_edit.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_edit_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_edit.gif"
 		 style="display:inline;" />

 	<img id="btnSave" title="Save" onClick="save();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_save.gif"
 		 style="display:none;"/>
<?php } ?>
<?php
if ($_SESSION['printBenefits'] == "enabled") {
?>
<a href="?benefitcode=Benefits&action=Hsp_Summary_Employee&year=<?php echo $year; ?>&employeeId=<?php echo $summary[0]['emp_number']; ?>&printPdf=1&pdfName=Personal-HSP-Summary"><img title="Save As PDF" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_01.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_02.gif';" src="../../themes/beyondT/pictures/btn_save_as_pdf_01.gif" border="0"></a>
<?php } ?>


</div>
<form id="frmMain" name="frmMain" method="post">
	<table border="0" cellpadding="5" cellspacing="0">
		<thead>
		  	<tr>
				<th class="tableTopLeft"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
				<th class="tableTopRight"></th>
			</tr>
			<tr>
				<th class="tableMiddleLeft"></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
		    	<th><?php echo $lang_Benefits_HspValue; ?></th>
		    	<th><?php echo $lang_Benefits_AmountPerDay; ?></th>
		    	<th><?php echo $lang_Benefits_TotalAcrued; ?></th>
		    	<th><?php echo $lang_Benefits_TotalDue; ?></th>
		    	<th><?php echo $lang_Benefits_BalanceAvailable; ?></th>
		    	<th><?php echo $lang_Benefits_TotalUsed; ?></th>
				<th class="tableMiddleRight"></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($summary) > 0) { ?>
			<?php
				for ($i=0; $i<count($summary); $i++) {
					$rowStyle = 'odd';
					if (($i%2) == 0) {
						$rowStyle = 'even';
					}
			?>
				<tr>
					<td class="tableMiddleLeft"></td>
					<td class="<?php echo $rowStyle; ?>">
						<input  type="hidden" value="<?php echo $summary[$i]['hsp_id']; ?>"
								id="txtHspId[<?php echo $i; ?>]" name="txtHspId[]" />

						<input  type="hidden" value="<?php echo $summary[$i]['emp_number']; ?>"
								id="txtEmployeeId[<?php echo $i; ?>]" name="txtEmployeeId[]" />
					</td>
					<td class="<?php echo $rowStyle; ?>">
					<?php if (($summary[$i]['hsp_id'] == null) || ($summary[$i]['halted'] == 1) || ($summary[$i]['terminated'] == 1)) { ?>
						<?php echo $lang_Benefits_HaltPlan; ?>
					<?php } else { ?>
						<a href="javascript:haltPlan('?benefitcode=Benefits&year=<?php echo $year; ?>&action=Halt_Hsp_Plan_Employee&id=<?php echo $summary[$i]['hsp_id']; ?>')"><?php echo $lang_Benefits_HaltPlan; ?></a>
					</td>
					<?php }?>
					<td class="<?php echo $rowStyle; ?>">
					<?php if ($summary[$i]['hsp_value'] > 0 || $summary[$i]['terminated'] == 1) {
						echo number_format($summary[$i]['hsp_value'], 2, ".", "");
					?>
					<input type="hidden" value="<?php echo number_format($summary[$i]['hsp_value'], 2, ".", ""); ?>" name="txtHspValue[]" />
					<?php } else { ?>
						<input  type="input" value="<?php echo number_format($summary[$i]['hsp_value'], 2, ".", ""); ?>"
								disabled id="txtHspValue[<?php echo $i; ?>]" name="txtHspValue[]" size="10" />
					<?php } ?>
					<td class="<?php echo $rowStyle; ?>">
					<?php if ($summary[$i]['terminated'] == 1) {
						echo number_format($summary[$i]['amount_per_day'], 2, ".", "");
					?>
					<?php }else {?>
					<input type="input" value="<?php echo number_format($summary[$i]['amount_per_day'], 2, ".", ""); ?>"
								disabled id="txtAmountPerDay[<?php echo $i; ?>]" name="txtAmountPerDay[]" size="10" />
					<?php }?>
					<input type="hidden" value="<?php echo number_format($summary[$i]['amount_per_day'], 2, ".", ""); ?>" name="initialAmountPerDay[<?php echo $i; ?>]" />
					<input type="hidden" value="<?php echo number_format($summary[$i]['edited_status'], 2, ".", ""); ?>" name="editedStatus[<?php echo $i; ?>]" />
					<?php if (isset($summary[$i]['pay_days'])) { ?>
					<input type="hidden" value="<?php echo $summary[$i]['pay_days']; ?>" name="payDays[]" />
					<?php } ?>
					</td>

					<td class="<?php echo $rowStyle; ?>">
					<?php if ($summary[$i]['terminated'] == 1) {
						echo number_format($summary[$i]['total_acrued'], 2, ".", "");
					?>
						<input  type="hidden" value="<?php echo number_format($summary[$i]['total_acrued'], 2, ".", ""); ?>"
								disabled id="txtTotalAcrued[<?php echo $i; ?>]" name="txtTotalAcrued[]" size="10" />
					<?php } else {?>
						<input  type="input" value="<?php echo number_format($summary[$i]['total_acrued'], 2, ".", ""); ?>"
								disabled id="txtTotalAcrued[<?php echo $i; ?>]" name="txtTotalAcrued[]" size="10" />

								<?php }?>						</td>

					<td class="<?php echo $rowStyle; ?>"><?php echo number_format($summary[$i]['total_due'], 2, ".", ""); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo number_format($summary[$i]['balance_available'], 2, ".", ""); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo number_format($summary[$i]['total_used'], 2, ".", ""); ?></td>
				    <td class="tableMiddleRight"></td>
				</tr>
			<?php } ?>
		<?php }?>
		</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomLeft"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomRight"></td>
			</tr>
	  	</tfoot>
	</table>
</form>
<?php }?>
