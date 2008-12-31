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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Time_ProjectReportTitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
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
</style>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
    <div class="formpage">
        <div class="navigation">
            <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="backToDefineProjectReport();">
                <span><?php echo $lang_Common_Back;?></span>
            </a>
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
    	<input type="hidden" name="cmbProject" value="<?php echo $project->getProjectId(); ?>">
    	<input type="hidden" name="txtFromDate" value="<?php echo $startDate; ?>">
    	<input type="hidden" name="txtToDate" value="<?php echo $endDate; ?>">
    	<input type="hidden" name="activityId" value="">
    	<input type="hidden" name="time" value="">
		<div class="left"><?php echo $lang_Time_Timesheet_Project; ?></div><?php echo $customerDet->getCustomerName() . " - " . $project->getProjectName();?><br/>
		<div class="left"><?php echo $lang_Time_Report_From; ?></div><?php echo $startDate; ?><br/>
		<div class="left"><?php echo $lang_Time_Report_To; ?></div><?php echo $endDate; ?><br/>

		<hr style="width:420px;float:left;margin:15px 0px 15px 0px"/></br>
  <?php if (empty($activityTimeArray)) { ?>
		<div class="notice"><?php echo $lang_Admin_Project_NoActivitiesDefined; ?></div>
  <?php } else { ?>
      <div style="float:left">
		<table width="250" class="simpleList" style="margin:0 0 5px 4px;">
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
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');                
    }
//]]>
</script>
</div>        
</body>
</html>