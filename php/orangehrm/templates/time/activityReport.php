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

$project = $records[0];
$activity = $records[1];
$startDate = LocaleUtil::getInstance()->formatDate($records[2]);
$endDate = LocaleUtil::getInstance()->formatDate($records[3]);
$empTimeArray = $records[4];
$count = $records[5];
$totalTime = $records[6];
$pageNo = $records[7];

$customerObj = new Customer();
$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Time_ActivityReportTitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

var initialAction = "?timecode=Time&action=";

function backToProjectReport() {
	action = "Project_Report";

	$('frmActivity').action = initialAction+action;
	$('frmActivity').submit();
}

function chgPage(pageNo) {
	action = "Activity_Report";

	$('frmActivity').pageNo.value = pageNo;
	$('frmActivity').action = initialAction+action;
	$('frmActivity').submit();
}

function prevPage() {
	chgPage(<?php echo $pageNo - 1;?>);
}

function nextPage() {
	chgPage(<?php echo $pageNo + 1;?>);
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

    .left {
    	width: 100px;
    	float: left;
    }

    .paging {
        margin-top: 0px;
        margin-bottom: 10px;
        width: 450px;
        float: left;
    }

    -->
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
            <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="backToProjectReport();">
                <span><?php echo $lang_Common_Back;?></span>
            </a>
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Time_ActivityReportTitle;?></h2></div>

            <div id="status"></div>
                    
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

	<form name="frmActivity" id="frmActivity" method="post" action="" style="padding-left:5px;">
      <input type="hidden" name="token" value="<?php echo $token;?>" />
    	<input type="hidden" name="cmbProject" value="<?php echo $project->getProjectId(); ?>">
    	<input type="hidden" name="txtFromDate" value="<?php echo $startDate; ?>">
    	<input type="hidden" name="txtToDate" value="<?php echo $endDate; ?>">
    	<input type="hidden" name="activityId" value="<?php echo $activity->getId(); ?>">
    	<input type="hidden" name="time" value="<?php echo $totalTime; ?>">
    	<input type="hidden" name="pageNo" value="<?php echo $pageNo; ?>">

		<div class="left"><?php echo $lang_Time_Timesheet_Project; ?></div><?php echo $customerDet->getCustomerName() . " - " . $project->getProjectName();?><br/>
		<div class="left"><?php echo $lang_Time_Timesheet_Activity; ?></div><?php echo $activity->getName();?><br/>
		<div class="left"><?php echo $lang_Time_Report_To; ?></div><?php echo $startDate; ?><br/>
		<div class="left"><?php echo $lang_Time_Report_From; ?></div><?php echo $endDate; ?><br/>
		<div class="left"><?php echo $lang_Time_Activity_Report_TotalTime; ?></div>
		<?php
			$totalTime = number_format(round($totalTime/3600,1),1);
			echo "$totalTime $lang_Time_Timesheet_DurationUnits"; ?>
		<br/>

        <hr style="width:420px;float:left;margin:15px 0px 15px 0px" align="left" />
  <?php if (empty($empTimeArray)) { ?>
		<br/><div class="notice" style="float:left; padding-top: 5px;"><?php echo $lang_Time_Activity_Report_NoEvents; ?></div>
  <?php } else { ?>
		<div class="paging">
		<?php
		$commonFunc = new CommonFunctions();
		$pageStr = $commonFunc->printPageLinks($count, $pageNo);
		$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);
		echo $pageStr;
		?>
		</div><br />

      <div style="float:left">
		<table width="250" class="simpleList" style="margin:0 0 5px 4px;">
			<thead>
				<tr>
				<th class="listViewThS1"><?php echo $lang_Time_Activity_Report_EmployeeName; ?></th>
				<th class="listViewThS1"><?php echo $lang_Time_TimeInHours; ?></th>
				<th class="listViewThS1"></th>
				</tr>
    		</thead>
			<?php
				$odd = false;
				foreach ($empTimeArray as $empActivityTime) {
	 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
	 	 	 		$odd = !$odd;

	 	 	 		$employeeName = htmlspecialchars($empActivityTime->getFirstName() . " " . $empActivityTime->getLastName());
			 		$time = $empActivityTime->getActivityTime();
	 		?>
    		<tr>
		 		<td class="<?php echo $cssClass?>"><?php echo $employeeName; ?></td>
		 		<td class="<?php echo $cssClass?>"><?php echo number_format(round($time/3600,1),1); ?></td>
			</tr>
		 	<?php
		 		}
 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
		  	?>
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