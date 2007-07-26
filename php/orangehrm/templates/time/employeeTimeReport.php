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

$role = $records[0];
$employee = $records[1];
$report = $records[2];

$customerObj = new Customer();
$projectObj = new Projects();
$projectActivityObj = new ProjectActivity();
?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";

function goBack() {
	window.location = initialAction+"Employee_Report_Define";
}
</script>
<h2>
<?php echo "{$lang_Time_EmployeeTimeReportTitle} : {$employee[2]} {$employee[1]}"; ?>
<hr/>
</h2>
<p class="navigation">
  	  <input type="image" title="Back"
  	  		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';"
  	  		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"
  	  		 src="../../themes/beyondT/pictures/btn_back.jpg"
  	  		 onClick="goBack(); return false;" />
</p>
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th width="150px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Project; ?></th>
			<th width="100px" class="tableMiddleMiddle"><?php echo $lang_Time_Timesheet_Activity; ?></th>
			<th width="80px" class="tableMiddleMiddle"><?php echo "$lang_Common_Time {$lang_Time_Timesheet_DurationUnits}"; ?></th>
			<th class="tableMiddleRight"></th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($report)) {
				$totalTime = 0;
				foreach ($report as $projectId=>$projectTimeCost) {
					foreach ($projectTimeCost as $activityId=>$activityTimeCost) {
						$projectDet = $projectObj->fetchProject($projectId);
						$customerDet = $customerObj->fetchCustomer($projectId);
						$projectActivities = $projectActivityObj->getActivity($projectId);

						$totalTime+=$activityTimeCost;
		?>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo "{$customerDet->getCustomerName()} - {$projectDet->getProjectName()}"; ?></td>
			<td ><?php echo $projectActivities->getName(); ?></td>
			<td ><?php echo round($activityTimeCost/36)/100; ?></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<?php
					}
				}
		?>
		<tr>
			<td class="tableMiddleLeft"></td>
			<th colspan="2"><?php echo $lang_Time_Timesheet_Total; ?></th>
			<th ><?php echo round($totalTime/36)/100; ?></th>
			<td class="tableMiddleRight"></td>
		</tr>
		<?php
			  } else { ?>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td colspan="3"><?php echo $lang_Error_NoRecordsFound; ?></td>
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
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>