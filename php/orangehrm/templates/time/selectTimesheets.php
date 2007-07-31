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

function selectDate() {
	YAHOO.OrangeHRM.calendar.pop(this.id, 'cal1Container', 'yyyy-MM-dd');
}

function init() {
	YAHOO.util.Event.addListener($("btnFromDate"), "click", selectDate, $("txtFromDate"), true);
	YAHOO.util.Event.addListener($("btnToDate"), "click", selectDate, $("txtToDate"), true);
}

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener(window, "load", init);
</script>
<h2>
<?php echo $lang_Time_SelectTimesheetsTitle; ?>
<hr/>
</h2>
<form name="frmEmp" id="frmTimesheet" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?timecode=Time&action=">
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
				<input type="text" name="txtUserEmpID" id="txtUserEmpID" readonly />
				<input type="hidden" name="cmbUserEmpID" id="cmbUserEmpID" />
				<input type="button" id="popEmp" name="popEmp" value="..." onclick="returnEmpDetail();" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_Division; ?></td>
			<td></td>
			<td>
			  <input type="text" id="txtLocation" name="txtLocation" readonly />
			  <input type="hidden" id="cmbLocation" name="cmbLocation" readonly />
			  <input type="button" id="popLoc" name="popLoc" value="..." onclick="returnLocDet();" />
			</td>
			<td class="tableMiddleRight"></td>
		</tr>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><?php echo $lang_Time_Supervisor; ?></td>
			<td></td>
			<td><input type="text" name="cmbRepEmpID" id="cmbRepEmpID" readonly />
				<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" value="">
				<input type="button" id="popEmpRep" name="popEmpRep" value="..." onclick="returnEmpRepDetail();"
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
					<option value="-1">- <?php echo $lang_Common_Select; ?> -</option>
				<?php foreach ($employmentStatuses as $employmentStatus) { ?>
					<option value="<?php echo $employmentStatus[0]; ?>"><?php echo $employmentStatus[1]; ?></option>
				<?php }
				 } else {
			?>
				    <option value="-1">- <?php echo $lang_Time_NoEmploymentStatusDefined; ?> -</option>
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
			<td></td>
			<td></td>
			<td>
				<input type="image" name="btnView" alt="View"
					   onclick="viewTimesheet(); return false;"
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