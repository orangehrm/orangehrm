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

$_SESSION['cellHeight'] = 10;
$_SESSION['colunmWidths'] = array(25, 30, 25, 25, 25, 25, 25);
$_SESSION['recordsPerPage'] = 20;
?>

<h2><?php echo "$lang_Benefits_PersonalHspSummary : $year";?></h2>
<?php
if (isset($summary[0])) {
	echo "<h2>   {$summary[0]['employee_name']}</h2>";
}
?>
<hr/>

<?php if (count($summary) == 0) { ?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php } else { ?>

<?php
$count = count($summary);

?>

<table border="0" cellpadding="5" cellspacing="0" width="580" align="center" class="tableMain">
		<thead>
			<tr>
			<th>&nbsp;</th>
		    	<th><?php echo $lang_Benefits_HspValue; ?></th>
		    	<th><?php echo $lang_Benefits_AmountPerDay; ?></th>
		    	<th><?php echo $lang_Benefits_TotalAcrued; ?></th>
		    	<th><?php echo $lang_Benefits_TotalDue; ?></th>
		    	<th><?php echo $lang_Benefits_BalanceAvailable; ?></th>
		    	<th><?php echo $lang_Benefits_TotalUsed; ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($summary) > 0) { ?>
			<?php
				for ($i=0; $i<count($summary); $i++) {
			?>
				<tr>
					<td><?php echo $summary[$i]['allotment_name']; ?></td>
					<td><?php if ($summary[$i]['hsp_value'] > 0) {
						echo number_format($summary[$i]['hsp_value'], 2, ".", "");
					} else {
						echo number_format(0, 2, ".", "");
					} ?></td>
					<td><?php echo number_format($summary[$i]['amount_per_day'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_acrued'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_due'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['balance_available'], 2, ".", ""); ?></td>
					<td><?php echo number_format($summary[$i]['total_used'], 2, ".", ""); ?></td>
				</tr>
			<?php } ?>
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
				<td class="tableBottomMiddle"></td>
			</tr>
	  	</tfoot>
	</table>

<?php }?>
