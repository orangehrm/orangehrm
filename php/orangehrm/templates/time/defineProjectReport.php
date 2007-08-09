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

$projects=$records[0];
?>

<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";

function viewProjectReport() {
	action = "Project_Report";

	if (validate()) {
		$('frmReport').action = initialAction+action;
		$('frmReport').submit();
	}

	return false;
}

function validate() {
	startTime = strToTime($("txtFromDate").value+" 00:00");
	endTime = strToTime($("txtToDate").value+" 23:59");

	errFlag=false;
	errors = new Array();

	if (-1 > $("cmbProject").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified; ?>";
		errFlag=true;
	}

	if (!startTime || !endTime || (startTime > endTime)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified; ?>";
		errFlag=true;
	}

	if (errFlag) {
		errStr="<?php echo $lang_Time_Errors_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr+=" - "+errors[i]+"\n";
		}
		alert(errStr);

		return false;
	}

	return true;
}

function selectDate() {
	YAHOO.OrangeHRM.calendar.pop(this.id, 'cal1Container', 'yyyy-MM-dd');
}

function init() {
	YAHOO.util.Event.addListener($("btnFromDate"), "click", selectDate, $("txtFromDate"), true);
	YAHOO.util.Event.addListener($("btnToDate"), "click", selectDate, $("txtToDate"), true);
}

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener(window, "load", init);
YAHOO.util.Event.addListener($("frmReport"), "submit", viewProjectReport);

</script>

<h2>
<?php echo $lang_Time_ProjectReportTitle; ?>
<hr/>
</h2>
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
<form name="frmReport" id="frmReport" method="post" action="?timecode=Time&action=" onsubmit="viewProjectReport(); return false;">
<table border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" >
				<?php if (is_array($projects)) {
                          $customerObj = new Customer();

                          foreach ($projects as $project) {
                              $customerDet = $customerObj->fetchCustomer($project->getCustomerId());

                              $selected = "";
                              if (isset($projectId) && ($projectId == $project->getProjectId())) {
							      $selected = "selected";
							  }
				?>
						<option value="<?php echo $project->getProjectId(); ?>" <?php echo $selected; ?> ><?php echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}"; ?></option>
				<?php 	}
					} else { ?>
						<option value="-2">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
				</select>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Common_FromDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtFromDate" name="txtFromDate" value="" size="10"/>
				<input type="button" id="btnFromDate" name="btnFromDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Common_ToDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtToDate" name="txtToDate" value="" size="10"/>
				<input type="button" id="btnToDate" name="btnToDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ></td>
			<td ></td>
			<td >
				<input type="image" name="btnView" alt="View"
					   src="../../themes/beyondT/icons/view.jpg"
					   onmouseover="this.src='../../themes/beyondT/icons/view_o.jpg';"
					   onmouseout="this.src='../../themes/beyondT/icons/view.jpg';" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
<div id="cal1Container" style="position:absolute;" ></div>