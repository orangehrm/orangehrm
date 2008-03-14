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

$requests = $records[0];
$empFullName = $records[1][0];

if (count($requests) > 0) {
	$employeeId = $requests[0]->getEmployeeId();
	$year = substr($requests[0]->getDateIncurred(), 0, 4);
}


?>
<script type="text/javascript">

action = '?benefitcode=Benefits&action=';

function cancelAddPayPeriod() {
	window.history.go(-1);
}

</script>
<h2>
	<?php echo $lang_Benefits_HealthSavingsPlanUsedList . " : " . $empFullName; ?>
	<hr/>
</h2>
<div id="controls">

<?php if ($_SESSION['isAdmin'] == "Yes") { ?>
<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="cancelAddPayPeriod();">
<?php } ?>
<?php
if ($_SESSION['printBenefits'] == "enabled" && count($requests) > 0) {
?>
<a href="?benefitcode=Benefits&action=Hsp_Expenditures&year=<?php echo $year; ?>&employeeId=<?php echo $employeeId; ?>&printPdf=1&pdfName=HSP-Expenditures"><img title="Save As PDF" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_01.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_02.gif';" src="../../themes/beyondT/pictures/btn_save_as_pdf_01.gif" border="0"></a>
<?php } ?>

</div>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Benefits_Errors_' . $expString;
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>

<?php if (count($requests) > 0) { ?>

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
				<th class="tableTopRight"></th>
			</tr>
			<tr>
				<th class="tableMiddleLeft"></th>
				<th><?php echo $lang_Benefits_Paid; ?></th>
		    	<th><?php echo $lang_Benefits_DateIncurred; ?></th>
		    	<th><?php echo $lang_Benefits_NameOfProvider; ?></th>
		    	<th><?php echo $lang_Benefits_ExpenseDescription; ?></th>
		    	<th><?php echo $lang_Benefits_IncurredFor; ?></th>
		    	<th><?php echo $lang_Benefits_Cost . " " . $lang_Benefits_US_Dollars; ?></th>
				<th class="tableMiddleRight"></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($requests) > 0) { ?>
			<?php
				$i=0;
				$total=0;
				foreach ($requests as $request) {
					$rowStyle = 'odd';
					if (($i%2) == 0) {
						$rowStyle = 'even';
					}
					$i++;
			?>
				<tr>
					<td class="tableMiddleLeft"></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo ($request->getStatus() == HspPaymentRequest::HSP_PAYMENT_REQUEST_STATUS_PAID)?$lang_Benefits_Yes:$lang_Benefits_No; ?></a></td>
					<td class="<?php echo $rowStyle; ?>">
						<a href="?benefitcode=Benefits&action=View_Hsp_Request&id=<?php echo $request->getId(); ?>"><?php echo LocaleUtil::getInstance()->formatDate($request->getDateIncurred()); ?></a>
					</td>
					<td class="<?php echo $rowStyle; ?>"><?php echo $request->getProviderName(); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo $request->getExpenseDescription(); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo $request->getPersonIncurringExpense(); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo $request->getExpenseAmount(); ?></td>
				    <td class="tableMiddleRight"></td>
				</tr>
			<?php
					$total+=$request->getExpenseAmount();
				}

			if ($rowStyle == "odd") {
				$rowStyle = "even";
			} else {
				$rowStyle = "odd";
			}

			?>
				<tr style="font-weight:bold;">
					<td class="tableMiddleLeft"></td>
					<td class="<?php echo $rowStyle; ?>" colspan="4"></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo $lang_Benefits_TotalUsed; ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo number_format($total, 2); ?></td>
				    <td class="tableMiddleRight"></td>
				</tr>
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
				<td class="tableBottomRight"></td>
			</tr>
	  	</tfoot>
	</table>
<?php } else { ?>

  <h5><?php echo $lang_empview_norecorddisplay; ?></h5>

<?php } ?>
