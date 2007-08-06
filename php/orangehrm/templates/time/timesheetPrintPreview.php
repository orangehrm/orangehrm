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

$filterValues = $records[0];
$timesheetsCount = $records[1];
?>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/connection/connection-min.js"></script>
<script type="text/javascript">
currPage=1;
commonAction="?timecode=Time&action=Print_Timesheet_Get_Page";
connections=new Array(<?php echo $timesheetsCount; ?>);

for (i=0; connections.length>i; i++) {
	connections[i]=false;
}

function nextPage() {
	currPage++;
	$('filterTimesheets').action=commonAction+"page="+currPage;
}
</script>
<h2><?php echo $lang_Time_PrintTimesheetsTitle; ?></h2>

<form id="filterTimesheets" name="filterTimesheets" method="post" action="?timecode=Time&action=Print_Timesheet_Get_Page">
	<input type="hidden" name="txtEmpID" id="txtEmpID" value="<?php echo $filterValues[0]; ?>" />
	<input type="hidden" name="txtLocation" id="txtLocation" value="<?php echo $filterValues[1]; ?>" />
	<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" value="<?php echo $filterValues[2]; ?>" />
	<input type="hidden" name="txtEmploymentStatus" id="txtEmploymentStatus" value="<?php echo $filterValues[3]; ?>" />
	<input type="hidden" name="txtStartDate" id="txtStartDate" value="<?php echo $filterValues[4]; ?>" />
	<input type="hidden" name="txtEndDate" id="txtEndDate" value="<?php echo $filterValues[5]; ?>" />
</form>

<div id="printPanel">
</div>
<div id="pagePanel">
<?php
$temp = $timesheetsCount;
$currentPage = 1;
$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;
?>
</div>
<div id="controls">
</div>
