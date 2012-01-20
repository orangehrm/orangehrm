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
