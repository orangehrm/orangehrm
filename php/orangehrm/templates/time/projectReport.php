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
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

$project = $records[0];
$startDate = LocaleUtil::getInstance()->formatDate($records[1]);
$endDate = LocaleUtil::getInstance()->formatDate($records[2]);
$activityTimeArray = $records[3];
$customerObj = new Customer();
$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
$token = $records['token'];
?>
<script type="text/javascript">
//<![CDATA[
var initialAction = "?timecode=Time&action=";

function viewActivityReport(activityId, time) {

	action = "Activity_Report";

	$('frmActivity').activityId.value = activityId;
	$('frmActivity').time.value = time;
	$('frmActivity').action = initialAction+action;
	$('frmActivity').submit();
}

function backToDefineProjectReport() {
	action = "Project_Report_Define";
	window.location = initialAction+action;
}
//]]>
</script>
<style type="text/css">
span.noActivitiesNotice {
	margin:4px;
	font-style:italic;
}

hr.activitiesSeparator {
	display:block;
	width:100%;
	margin:4px 4px 4px 0px;
}
</style>
    <div class="formpage">
        <div class="navigation">
            <input type="button" class="backbutton" value="<?php echo $lang_Common_Back;?>"
            	onmouseover="moverButton(this)" onmouseout="moutButton(this)" onclick="backToDefineProjectReport();" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Time_ProjectReportTitle;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Time_Errors_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

        <div id="status"></div>

	<form name="frmActivity" id="frmActivity" method="post" action="" style="padding-left:5px;">
      <input type="hidden" name="token" value="<?php echo $token;?>" />
    	<input type="hidden" name="cmbProject" value="<?php echo $project->getProjectId(); ?>"/>
    	<input type="hidden" name="txtFromDate" value="<?php echo $startDate; ?>"/>
    	<input type="hidden" name="txtToDate" value="<?php echo $endDate; ?>"/>
    	<input type="hidden" name="activityId" value=""/>
    	<input type="hidden" name="time" value=""/>

        <span class="formLabel"><?php echo $lang_Time_Timesheet_Project; ?></span>
        <span class="formValue"><?php echo $customerDet->getCustomerName() . " - " . $project->getProjectName();?></span>
        <br class="clear"/>

		<span class="formLabel"><?php echo $lang_Time_Report_From; ?></span>
        <span class="formValue"><?php echo $startDate; ?></span>
        <br class="clear"/>

		<span class="formLabel"><?php echo $lang_Time_Report_To; ?></span>
        <span class="formValue"><?php echo $endDate; ?></span>
        <br class="clear"/>

		<hr class="activitiesSeparator" />
  <?php if (empty($activityTimeArray)) { ?>
		<span class="noActivitiesNotice"><?php echo $lang_Admin_Project_NoActivitiesDefined; ?></span>
  <?php } else { ?>

		<table width="250" class="simpleList">
			<thead>
				<tr>
				<th><?php echo $lang_Time_Timesheet_Activity; ?></th>
				<th class="listViewThS1"><?php echo $lang_Time_TimeInHours; ?></th>
				<th class="listViewThS1"></th>
				</tr>
    		</thead>
			<?php
				$odd = false;
				$totalTime = 0;
				foreach ($activityTimeArray as $activityTime) {
	 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
	 	 	 		$odd = !$odd;
			 		$activityName = htmlspecialchars($activityTime->getActivityName());
			 		$activityId = $activityTime->getActivityId();
			 		$time = $activityTime->getActivityTime();
			 		$totalTime += $time;
	 		?>
    		<tr>
		 		<td class="<?php echo $cssClass?>"><?php echo $activityName; ?></td>
		 		<td class="<?php echo $cssClass?>"><?php echo number_format(round($time/3600,1),1); ?></td>
		 		<td class="<?php echo $cssClass?>">
		 		    <a href="javascript:viewActivityReport(<?php echo "$activityId, $time";?>);">
		 		    <?php echo $lang_Time_Activity_Report_View; ?></a>
		 		</td>
			</tr>
		 	<?php
		 		}
 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
		  	?>
    		<tr style="color: black;font-weight: bold;">
		 		<td class="<?php echo $cssClass?>"><?php echo $lang_Time_Timesheet_Total; ?></td>
		 		<td class="<?php echo $cssClass?>"><?php echo number_format(round($totalTime/3600, 1),1); ?></td>
		 		<td class="<?php echo $cssClass?>"></td>
			</tr>
 		</table>

		<br class="clear"/>
	 	<?php
		 }
	  	?>
  </form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>