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
$year = $records[0];
$summary = $records[1];

$_SESSION['cellHeight'] = 15;
$_SESSION['colunmWidths'] = array(18, 30, 22, 22, 22, 22, 22, 15, 15);
$_SESSION['recordsPerPage'] = 15;

?>
<h2><?php echo "$lang_Benefits_EmployeeHspSummary : $year"; ?></h2>
<hr />
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Benefits_Errors_' . $expString;
}	?>
<?php if (count($summary) == 0) { ?>
	<?php echo $lang_Error_NoRecordsFound; ?>
<?php } else { ?>

<?php
$count = count($summary);
?>

<table border="0" cellpadding="5" cellspacing="0" width="580" align="center" class="tableMain">
		<thead>
			<tr>
			<th>&nbsp;</th>
		    	<th><?php echo $lang_Benefits_Employee; ?></th>
		    	<th><?php echo $lang_Benefits_HspValue . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_AmountPerDay . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_TotalAcrued . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_TotalDue . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_BalanceAvailable . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_TotalUsed; ?></th>
		    	<th><?php echo $lang_Benefits_TerminationDate; ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($summary) > 0) { ?>
			<?php

				for ($i=0; $i<$count; $i++) {
			?>
				<tr>
					<td><?php echo $summary[$i]['allotment_name']; ?></td>
					<td><?php echo $summary[$i]['employee_name']; ?></td>
					<td><?php if ($summary[$i]['hsp_value'] > 0) {
						echo number_format($summary[$i]['hsp_value'], 2, ".", "");
					 } else {
						echo number_format($summary[$i]['hsp_value'], 2, ".", "");
					 } ?></td>
					<td><?php echo number_format($summary[$i]['amount_per_day'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_acrued'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_due'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['balance_available'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_used'], 2, ".", ""); ?></td>
					<td><?php echo (empty($summary[$i]['termination_date']))?"-":LocaleUtil::getInstance()->formatDate($summary[$i]['termination_date']); ?></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
			</tr>
	  	</tfoot>
	</table>
<?php } ?>

<?php }?>
