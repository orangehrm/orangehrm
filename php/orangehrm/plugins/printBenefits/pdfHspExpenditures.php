<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

$requests = $records[0];
$empFullName = $records[1][0];
$total = 0;
$lastRow = false;


$_SESSION['cellHeight'] = 10;
$_SESSION['colunmWidths'] = array(12, 30, 40, 40, 40, 22, 22, 22);
$_SESSION['recordsPerPage'] = 20;

?>

<h2><?php echo $lang_Benefits_HealthSavingsPlanUsedList . " : " . $empFullName; ?></h2>
<hr />

<?php
$count = count($requests);

?>

<table border="0" cellpadding="5" cellspacing="0" width="580" align="center" class="tableMain">
		<thead>
			<tr>
			<th><?php echo $lang_Benefits_Paid; ?></th>
		    	<th><?php echo $lang_Benefits_DateIncurred; ?></th>
		    	<th><?php echo $lang_Benefits_NameOfProvider; ?></th>
		    	<th><?php echo $lang_Benefits_ExpenseDescription; ?></th>
		    	<th><?php echo $lang_Benefits_IncurredFor; ?></th>
		    	<th><?php echo $lang_Benefits_Cost . " " . $lang_Benefits_US_Dollars; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($count > 0) { ?>
                        <?php
                                for ($i=0; $i<$count; $i++) {
			?>
				<tr>
					<td><a><?php echo ($requests[$i]->getStatus() == HspPaymentRequest::HSP_PAYMENT_REQUEST_STATUS_PAID)?$lang_Benefits_Yes:$lang_Benefits_No; ?></a></td>
					<td><?php echo LocaleUtil::getInstance()->formatDate($requests[$i]->getDateIncurred()); ?></td>
					<td><?php echo $requests[$i]->getProviderName(); ?></td>
					<td><?php echo $requests[$i]->getExpenseDescription(); ?></td>
					<td><?php echo $requests[$i]->getPersonIncurringExpense(); ?></td>
					<td><?php echo $requests[$i]->getExpenseAmount(); ?></td>
				</tr>

				<?php $total += $requests[$i]->getExpenseAmount();

					  if ($i == ($count - 1)) {
					  	$lastRow = true;
					  }

				?>

		<?php }?>

		<?php

			if ($lastRow) { ?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b><?php echo $lang_Benefits_TotalUsed; ?></b></td>
					<td><b><?php echo number_format($total, 2); ?></b></td>
				</tr>
			<?php }?>

		</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
			</tr>
	  	</tfoot>
	</table>

<?php }?>
