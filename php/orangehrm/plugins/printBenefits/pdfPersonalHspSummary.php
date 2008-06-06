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
