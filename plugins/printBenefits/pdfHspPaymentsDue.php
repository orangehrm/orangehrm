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

$_SESSION['cellHeight'] = 10;
$_SESSION['colunmWidths'] = array(25, 45, 45, 35, 30);
$_SESSION['recordsPerPage'] = 20;

?>
<h2><?php echo $lang_Benefits_HealthSavingsPlanPaymentsDue; ?></h2>
<hr />
<?php
$count = count($requests);
?>

<table border="0" cellpadding="5" cellspacing="0" width="580" align="center" class="tableMain">
		<thead>
		  	<tr>
			<th><?php echo $lang_Benefits_DateIncurred; ?></th>
		    	<th><?php echo $lang_Benefits_NameOfProvider; ?></th>
		    	<th><?php echo $lang_Benefits_ExpenseDescription; ?></th>
		    	<th><?php echo $lang_Benefits_IncurredFor; ?></th>
		    	<th><?php echo $lang_Benefits_Cost . " " . $lang_Benefits_US_Dollars; ?></th>
			</tr>
</thead>
<tbody>

			<?php
				for ($i=0; $i<$count; $i++) {
                        ?>
				<tr>
					<td><?php echo LocaleUtil::getInstance()->formatDate($requests[$i]->getDateIncurred()); ?></td>
					<td><?php echo $requests[$i]->getProviderName(); ?></td>
					<td><?php echo $requests[$i]->getExpenseDescription(); ?></td>
					<td><?php echo $requests[$i]->getPersonIncurringExpense(); ?></td>
					<td><?php echo $requests[$i]->getExpenseAmount(); ?></td>
				</tr>

			<?php
				}
			?>

		</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
			</tr>
	  	</tfoot>
	</table>
