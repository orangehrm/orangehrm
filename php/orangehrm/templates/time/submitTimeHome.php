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
<?php include ROOT_PATH."/lib/common/yui.php"; ?>
<script type="text/javascript">

function init() {
  oLinkNewTimeEvent = new YAHOO.widget.Button("linkNewTimeEvent");

  completeEventBtns = YAHOO.util.Dom.getElementsByClassName("linkCompleteTimeEvent");

  oLinkCompleteTimeEvent = new Array();

  for (i=0; completeEventBtns.length > i; i++) {
  	oLinkCompleteTimeEvent[i] = new YAHOO.widget.Button(completeEventBtns[i]);
  }
}

function hideUrl() {
	window.status="";
}

YAHOO.util.Event.addListener(window, "load", init);
</script>

<h2>
<?php echo $lang_Time_UnfinishedActivitiesTitle; ?>
<hr/>
</h2>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
?>
		<div class="<?php echo $col_def?>" >
			<font size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
			</font>
		</div>
<?php }	?>
<?php if (isset($pendingTimeEvents) && is_array($pendingTimeEvents)) { ?>
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
			<td >
				<span class="linkCompleteTimeEvent"><span class="first-child"><a href="?timecode=Time&action=Update_Event_View&id=<?php echo $pendingTimeEvent->getTimeEventId(); ?>"><?php echo $lang_Time_Complete; ?></a></span></span>
			</td>
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
<?php } ?>
<p id="navigation">
	<span id="linkNewTimeEvent"><span class="first-child"><a href="?timecode=Time&action=New_Time_Event_View"><?php echo $lang_Time_NewEvent; ?></a></span></span>
</p>