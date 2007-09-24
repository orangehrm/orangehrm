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

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $lang_Time_ProjectReportTitle; ?></title>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script type="text/javascript">
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
</script>

<style type="text/css">
    <!--
    @import url("../../themes/<?php echo $styleSheet;?>/css/style.css");
    @import url("../../themes/beyondT/css/octopus.css");

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width:500px;
    }

    .roundbox_content {
        padding:15px 15px 25px 35px;
    }

    .notice {
    	font-family: Verdana, Arial, Helvetica, sans-serif;
    	font-size: -1;
    }

    .total {
        border-width: 1px 0px 0px 0px;
        color: black;
        font-weight: bold;
    }

    .left {
    	width: 100px;
    	float: left;
    }

    -->
</style>

</head>
<body>
<h2>
<?php echo $lang_Time_ProjectReportTitle; ?>
<hr/>
</h2>
<div id="navigation" style="float:left;width:100px">
	<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';"
	     onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"
	     src="../../themes/beyondT/pictures/btn_back.jpg" onClick="backToDefineProjectReport();">
</div><br/>
<div id="status"></div>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Time_Errors_' . $expString;
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
<div class="roundbox">
	<form name="frmActivity" id="frmActivity" method="post" action="">
    	<input type="hidden" name="cmbProject" value="<?php echo $project->getProjectId(); ?>">
    	<input type="hidden" name="txtFromDate" value="<?php echo $startDate; ?>">
    	<input type="hidden" name="txtToDate" value="<?php echo $endDate; ?>">
    	<input type="hidden" name="activityId" value="">
    	<input type="hidden" name="time" value="">
		<div class="left"><?php echo $lang_Time_Timesheet_Project; ?></div><?php echo $customerDet->getCustomerName() . " - " . $project->getProjectName();?><br/>
		<div class="left"><?php echo $lang_Time_Report_To; ?></div><?php echo $startDate; ?><br/>
		<div class="left"><?php echo $lang_Time_Report_From; ?></div><?php echo $endDate; ?><br/>

		<hr style="width:420px;float:left;margin:15px 0px 15px 0px"/></br>
  <?php if (empty($activityTimeArray)) { ?>
		<div class="notice"><?php echo $lang_Admin_Project_NoActivitiesDefined; ?></div>
  <?php } else { ?>
      <div style="float:left">
		<table width="250" class="simpleList" >
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
    		<tr class="total">
		 		<td class="<?php echo $cssClass?>"><?php echo $lang_Time_Timesheet_Total; ?></td>
		 		<td class="<?php echo $cssClass?>"><?php echo number_format(round($totalTime/3600, 1),1); ?></td>
		 		<td class="<?php echo $cssClass?>"></td>
			</tr>
 		</table>
		</div>
		<br/>
	 	<?php
		 }
	  	?>
  </form>
</div>
<script type="text/javascript">
<!--
	if (document.getElementById && document.createElement) {
		initOctopus();
	}
-->
</script>

</body>
</html>