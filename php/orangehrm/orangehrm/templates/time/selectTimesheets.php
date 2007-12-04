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

$employmentStatuses = $records[0];

?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
var initialAction = "<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=";

function returnLocDet(){
	var popup=window.open('CentralController.php?uniqcode=CST&VIEW=MAIN&esp=1','Locations','height=450,width=400,resizable=1');
	if(!popup.opener) popup.opener=self;
}

function returnEmpRepDetail() {
	var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP','Employees','height=450,width=400');
    if(!popup.opener) popup.opener=self;
	popup.focus();
}

function returnEmpDetail() {
	var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400');
	if(!popup.opener) popup.opener=self;
	popup.focus();
}


function validate() {
	startDate = strToDate($("txtStartDate").value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate($("txtEndDate").value, YAHOO.OrangeHRM.calendar.format);

	errFlag=false;
	errors = new Array();

	if (!startDate || !endDate || (startDate > endDate)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified; ?>";
		errFlag=true;
	}

	if (errFlag) {
		errStr="<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr+=" - "+errors[i]+"\n";
		}
		alert(errStr);

		return false;
	}

	return true;
}

function formReset() {
	document.frmEmp.txtUserEmpID.value = "<?php echo $lang_Time_Common_All; ?>";
	document.frmEmp.cmbUserEmpID.value = "-1";
	document.frmEmp.txtLocation.value = "<?php echo $lang_Time_Common_All; ?>";
	document.frmEmp.cmbLocation.value = "-1";
	document.frmEmp.cmbRepEmpID.value = "<?php echo $lang_Time_Common_All; ?>";
	document.frmEmp.txtRepEmpID.value = "-1";
	document.frmEmp.txtStartDate.value = "";
	document.frmEmp.txtEndDate.value = "";
	var statusDefault = document.getElementById("statusDefault");
	statusDefault.selected = true;
}

YAHOO.OrangeHRM.container.init();
</script>
<h2>
<?php echo $lang_Time_SelectTimesheetsTitle; ?>
<hr/>
</h2>
<form name="frmEmp" id="frmTimesheet" method="post" action="?timecode=Time&action=Timesheet_Print_Preview" onsubmit="return validate();">
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
			<td><?php echo $lang_Leave_Common_EmployeeName; ?></td>
			<td></td>
			<td>
				<input type="text" name="txtUserEmpID" id="txtUserEmpID" value="<?php echo (isset($_SESSION['txtUserEmpID']) && $_SESSION['posted'])?$_SESSION['txtUserEmpID']:$lang_Time_Common_All; ?>" readonly />
				<input type="hidden" name="cmbUserEmpID" id="cmbUserEmpID" value="<?php echo (isset($_SESSION['cmbUserEmpID']) && $_SESSION['posted'])?$_SESSION['cmbUserEmpID']:"-1"; ?>" />
				<input type="button" id="popEmp" name="popEmp" value="..." onclick="returnEmpDetail();" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_Division; ?></td>
			<td></td>
			<td>
			  <input type="text" id="txtLocation" name="txtLocation" value="<?php echo (isset($_SESSION['txtLocation']) && $_SESSION['posted'])?$_SESSION['txtLocation']:$lang_Time_Common_All; ?>" readonly />
			  <input type="hidden" id="cmbLocation" name="cmbLocation" value="<?php echo (isset($_SESSION['cmbLocation']) && $_SESSION['posted'])?$_SESSION['cmbLocation']:"-1"; ?>" />
			  <input type="button" id="popLoc" name="popLoc" value="..." onclick="returnLocDet();" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_Supervisor; ?></td>
			<td></td>
			<td><input type="text" name="cmbRepEmpID" id="cmbRepEmpID" value="<?php echo (isset($_SESSION['cmbRepEmpID']) && $_SESSION['posted'])?$_SESSION['cmbRepEmpID']:$lang_Time_Common_All; ?>" readonly />
				<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" value="<?php echo (isset($_SESSION['txtRepEmpID']) && $_SESSION['posted'])?$_SESSION['txtRepEmpID']:"-1"; ?>" />
				<input type="button" id="popEmpRep" name="popEmpRep" value="..." onclick="returnEmpRepDetail();" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_EmploymentStatus; ?></td>
			<td></td>
			<td>
				<select name="cmbEmploymentStatus">
			<?php if (is_array($employmentStatuses)) { ?>
					<option value="-1" <?php echo (isset($_SESSION['cmbEmploymentStatus']) && $_SESSION['posted'] && $_SESSION['cmbEmploymentStatus'] == "-1")?"selected":""; ?> id="statusDefault"><?php echo $lang_Time_Common_All; ?></option>
				<?php foreach ($employmentStatuses as $employmentStatus) { ?>
					<option value="<?php echo $employmentStatus[0]; ?>" <?php echo (isset($_SESSION['cmbEmploymentStatus']) && $_SESSION['posted'] && $_SESSION['cmbEmploymentStatus'] == $employmentStatus[0])?"selected":""; ?>><?php echo $employmentStatus[1]; ?></option>
				<?php }
				 } else {
			?>
				    <option value="-2" <?php echo (isset($_SESSION['cmbEmploymentStatus']) && $_SESSION['posted'] && $_SESSION['cmbEmploymentStatus'] == "-2")?"selected":""; ?>>- <?php echo $lang_Time_NoEmploymentStatusDefined; ?> -</option>
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
				<input type="text" id="txtStartDate" name="txtStartDate" value="<?php echo (isset($_SESSION['txtStartDate']) && $_SESSION['posted'])?$_SESSION['txtStartDate']:""; ?>" size="10"/>
				<input type="button" id="btnStartDate" name="btnStartDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td ><?php echo $lang_Time_Common_ToDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtEndDate" name="txtEndDate" value="<?php echo (isset($_SESSION['txtEndDate']) && $_SESSION['posted'])?$_SESSION['txtEndDate']:""; ?>" size="10"/>
				<input type="button" id="btnEndDate" name="btnEndDate" value="  " class="calendarBtn"/>
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td></td>
			<td></td>
			<td>
				<input type="image" name="btnView" alt="View"
					   src="../../themes/beyondT/icons/view.gif"
					   onmouseover="this.src='../../themes/beyondT/icons/view_o.gif';"
					   onmouseout="this.src='../../themes/beyondT/icons/view.gif';" />
				<input type="image" name="btnReset" alt="Reset"
					   onclick="formReset(); return false;"
					   src="../../themes/beyondT/icons/reset.gif"
					   onmouseover="this.src='../../themes/beyondT/icons/reset_o.gif';"
					   onmouseout="this.src='../../themes/beyondT/icons/reset.gif';" />
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