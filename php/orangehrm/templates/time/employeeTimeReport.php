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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

var initialAction = "?timecode=Time&action=";

function goBack() {
	window.location = initialAction+"Employee_Report_Define";
}
//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<style type="text/css">
td {
    vertical-align: top;
    padding: 5px;
    text-align:center;
}
</style>

<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
    <div class="formpage">
        <div class="navigation">
            <a href="#" class="backbutton" title="<?php echo $lang_Common_Back;?>" onclick="goBack();">
                <span><?php echo $lang_Common_Back;?></span>
            </a>
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo "{$lang_Time_EmployeeTimeReportTitle} : {$employee[2]} {$employee[1]}"; ?></h2></div>

<table border="0" cellpadding="0" cellspacing="0" class="simpleList">
	<thead>
		<tr>
			<th></th>
			<th width="150px"><?php echo $lang_Time_Timesheet_Project; ?></th>
			<th width="100px"><?php echo $lang_Time_Timesheet_Activity; ?></th>
			<th width="80px"><?php echo "$lang_Common_Time {$lang_Time_Timesheet_DurationUnits}"; ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($report)) {
				$totalTime = 0;
				foreach ($report as $projectId=>$projectTimeCost) {
					foreach ($projectTimeCost as $activityId=>$activityTimeCost) {
						$projectDet = $projectObj->fetchProject($projectId);
						$customerDet = $customerObj->fetchCustomer($projectDet->getCustomerId(), true);
						$projectActivity = $projectActivityObj->getActivity($activityId);

						$totalTime+=$activityTimeCost;
		?>
		<tr>
			<td></td>
			<td ><?php echo "{$customerDet->getCustomerName()} - {$projectDet->getProjectName()}"; ?></td>
			<td ><?php echo $projectActivity->getName(); ?></td>
			<td ><?php echo round($activityTimeCost/36)/100; ?></td>
			<td></td>
		</tr>
		<?php
					}
				}
		?>
		<tr>
			<td></td>
			<th colspan="2"><?php echo $lang_Time_Timesheet_Total; ?></th>
			<th ><?php echo round($totalTime/36)/100; ?></th>
			<td></td>
		</tr>
		<?php
			  } else { ?>
		<tr>
			<td></td>
			<td colspan="3"><?php echo $lang_Error_NoRecordsFound; ?></td>
			<td></td>
		</tr>
		<?php }?>
	</tbody>
</table>
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
