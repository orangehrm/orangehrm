<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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

$pendingTimeEvents = $records[0];

?>
<h2>
<?php echo $lang_Time_UnfinishedActivitiesTitle; ?>
<hr/>
</h2>
<?php if (isset($pendingTimeEvents) && is_array($pendingTimeEvents)) { ?>
<form id="frmTimeEventList" name="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$customerObj = new Customer();
			$projectObj = new Projects();
			$projectActivityObj = new ProjectActivity();

			foreach ($pendingTimeEvents as $pendingTimeEvent) {
				$projectId = $pendingTimeEvent->getProjectId();

				$projectDet = $projectObj->fetchProject($projectId);
				$customerDet = $customerObj->fetchCustomer($projectDet->getCustomerId());

				$projectActivities = $projectActivityObj->getActivity($pendingTimeEvent->getActivityId());
			?>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $pendingTimeEvent->getStartTime(); ?></td>
			<td ><?php echo "{$customerDet->getCustomerName()} - {$projectDet->getProjectName()}"; ?></td>
			<td ><?php echo "{$projectActivities->getName()}"; ?></td>
			<td ><?php echo $pendingTimeEvent->getDescription(); ?></td>
			<td ><a href="?timecode=Time&action=Update_Event_View&id=<?php echo $pendingTimeEvent->getTimeEventId(); ?>"><?php echo $lang_Time_Complete; ?></a></td>
			<td class="tableMiddleRight"></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
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
<?php } ?>
<a href="?timecode=Time&action=New_Time_Event"><?php echo $lang_Time_NewEvent; ?></a>