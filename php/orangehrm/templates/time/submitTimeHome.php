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
<div class="formpage">
    <div class="navigation">
        <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="goBack();">
            <span><?php echo $lang_Common_Back;?></span>
        </a>
    </div>
    <div class="outerbox">
    <div class="mainHeading"><h2><?php echo $lang_Time_Add_NewEvent;?></h2></div>

    <?php if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = "lang_Time_Errors_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>
    <?php } ?>

<?php if (isset($pendingTimeEvents) && is_array($pendingTimeEvents)) { ?>
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
			<th></th>
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
				$customerDet = $customerObj->fetchCustomer($projectDet->getCustomerId(), true);

				$projectActivities = $projectActivityObj->getActivity($pendingTimeEvent->getActivityId());
			?>
		<tr>
			<td></td>
			<td ><?php echo LocaleUtil::getInstance()->formatDateTime($pendingTimeEvent->getStartTime()); ?></td>
			<td ><?php echo "{$customerDet->getCustomerName()} - {$projectDet->getProjectName()}"; ?></td>
			<td ><?php echo "{$projectActivities->getName()}"; ?></td>
			<td ><?php echo $pendingTimeEvent->getDescription(); ?></td>
			<td >
				<span class="linkCompleteTimeEvent"><span class="first-child"><a href="?timecode=Time&action=Update_Event_View&id=<?php echo $pendingTimeEvent->getTimeEventId(); ?>"><?php echo $lang_Time_Complete; ?></a></span></span>
			</td>
			<td></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>
<div class="formbuttons">
    <span id="linkNewTimeEvent"><span class="first-child"><a href="?timecode=Time&action=New_Time_Event_View"><?php echo $lang_Time_NewEvent; ?></a></span></span>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>

