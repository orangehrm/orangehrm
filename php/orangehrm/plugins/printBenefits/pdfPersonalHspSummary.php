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

$year = $records[2];
$summary = $records[1];

$_SESSION['cellHeight'] = 15;
$_SESSION['colunmWidths'] = array(20, 25, 28, 28, 28, 28, 28);
$_SESSION['recordsPerPage'] = 14;

?>
<h2><?php echo "$lang_Benefits_EmployeeHspSummary : $year";?></h2>
<?php
if (isset($summary[0])) {
        echo "<h2>   {$summary[0]->getEmployeeName()}</h2>";
}
?>
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
			<th><?php echo $lang_Benefits_Summary_Plan; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Status; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Annual_Limit . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Employer . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Employee . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Total_Accrued . "<br />" . $lang_Benefits_US_Dollars; ?></th>
		    	<th><?php echo $lang_Benefits_Summary_Total_Used; ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($summary) > 0) { ?>
			<?php

				for ($i=0; $i<$count; $i++) {
			?>
				<tr>
					<td><?php echo $summary[$i]->getHspPlanName(); ?></td>
					<td><?php echo $summary[$i]->getHspPlanStatusName(); ?></td>
					<td><?php if ($summary[$i]->getAnnualLimit() > 0) {
						echo number_format($summary[$i]->getAnnualLimit(), 2, ".", "");
					 } else {
						echo '0.00';
					 } ?></td>
					<td><?php if ($summary[$i]->getEmployerAmount() > 0) {
						echo number_format($summary[$i]->getEmployerAmount(), 2, ".", "");
					 } else {
						echo '0.00';
					 } ?></td>
					<td><?php if ($summary[$i]->getEmployeeAmount() > 0) {
						echo number_format($summary[$i]->getEmployeeAmount(), 2, ".", "");
					 } else {
						echo '0.00';
					 } ?></td>
					<td><?php if ($summary[$i]->getTotalAccrued() > 0) {
						echo number_format($summary[$i]->getTotalAccrued(), 2, ".", "");
					 } else {
						echo '0.00';
					 } ?></td>
					<td><?php if ($summary[$i]->getTotalUsed() > 0) {
						echo number_format($summary[$i]->getTotalUsed(), 2, ".", "");
					 } else {
						echo '0.00';
					 } ?></td>
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
			</tr>
	  	</tfoot>
	</table>
<?php } ?>

<?php }?>
