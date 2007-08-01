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
<?php echo $timesheetsCount; ?>
</div>
<div id="controls">
</div>
