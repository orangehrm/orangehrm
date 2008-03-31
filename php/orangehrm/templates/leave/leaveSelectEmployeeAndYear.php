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
 */

 if (isset($modifier)) {
 	switch ($modifier) {
 		case "summary" : $action = "?leavecode=Leave&action=Leave_Summary";
 						 break;
 		default 		: $action = "";
 						  break;
 	}
 }

 $years = $records[0];
 $employees = $records[1];
 if (isset($records[2])) {
 	$leaveTypes = $records[2];
 }

 if (isset($records[3])) {
 	$role = $records[3];
 }

	if (isset($_GET['message'])) {
?>
<var><?php echo CommonFunctions::escapeHtml($_GET['message']); ?></var>
<?php } ?>
<h2><?php echo $lang_Leave_Select_Employee_Title; ?><hr/></h2>
<script type="text/javascript">
	function validate() {
		err = false;
		errors = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

		if (document.frmSelectEmployee.year.value == -1) {
			errors += "-  <?php echo $lang_Error_PleaseSelectAYear; ?>\n";
			err = true;
		}
		if (document.frmSelectEmployee.id.value == -1) {
			errors += "-  <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n";
			err = true;
		}

		if (err) {
			errors = errors+"\n";
			alert(errors);
		} else {
			document.frmSelectEmployee.submit();
		}
	}

	function view() {

		if(undefined===window.cmbEmpID){
			document.frmSelectEmployee.searchBy.value = "employee";
		} else {
		    document.frmSelectEmployee.searchBy.value = "leaveType";
		}

	}

	function changeEmployeeSelection() {

		objCmbId = document.frmSelectEmployee.cmbId;
		objRow = document.getElementById("idSelectRow");
		objId = document.frmSelectEmployee.id;

		switch (objCmbId.value) {
			case '0' : objRow.className = 'hide';
					 objId.value = 0;
					 break;
			case '1' : objRow.className = 'show';
					 objId.value = -1;
					 break;
		}
	}

	function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=SUMMARY','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
	}
</script>
<form method="post" name="frmSelectEmployee" action="<?php echo $action; ?>" onsubmit="validate(); return false;">
<input type="hidden" name="searchBy" value="leaveType"/>
<table border="0" cellpadding="2" cellspacing="0">
  <tbody>
  	<tr>
		<th class="tableTopLeft"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>
    	<th width="70px" class="odd"><?php echo $lang_Leave_Common_Year;?></th>

    	<th width="130px" class="odd">
		    	  <select name="year">
		    	    <option value="-1"> - <?php echo $lang_Leave_Common_Select;?> - </option>
		   <?php
		   		if (is_array($years)) {
		   			foreach ($years as $year) {
		  ?>
		 		  	<option value="<?php echo $year ?>"><?php echo $year ?></option>
		  <?php 	}
		   		}
		 ?>
  	    		  </select>
   	    </th>

    	<th width="180px" class="odd"><?php echo $lang_Leave_Common_EmployeeName;?></th>

    	<th width="180px" class="odd">
    	<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
    		<select name="cmbId" onchange="changeEmployeeSelection();">
				<option value="0"><?php echo $lang_Leave_Common_AllEmployees;?></option>
				<option value="1"><?php echo $lang_Leave_Common_Select;?></option>
			</select>
		<?php } else if ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) { ?>
			<select name="id">
				<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
				<?php
		   			if (is_array($employees)) {
		   				sort($employees);
		   				foreach ($employees as $employee) {
		  ?>
		 		<option value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>
		  <?php 		}
		   			}
    		}
		 ?>
  	    		</select>
		</th>
    	<th width="100px" class="odd"><input type="image" name="btnView" onclick="view();" src="../../themes/beyondT/icons/view.gif" onmouseover="this.src='../../themes/beyondT/icons/view_o.gif';" onmouseout="this.src='../../themes/beyondT/icons/view.gif';" /></th>
		<th class="tableMiddleRight"></th>
	</tr>
	<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
	<tr class="hide" id="idSelectRow">
		<th class="tableMiddleLeft"></th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
		<th>
			<input type="text" name="cmbEmpID" id="cmbEmpID" value="" disabled />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="button" value="..." onclick="returnEmpDetail();" />
		</th>
		<th>&nbsp;</th>
		<th class="tableMiddleRight"></th>
	</tr>
	<tr>
		<th class="tableMiddleLeft"></th>
    	<th width="70px" class="odd"></th>

    	<th width="130px" class="odd">
   	    </th>

    	<th width="180px" class="odd"><?php echo $lang_Leave_Common_LeaveType;?></th>

    	<th width="150px" class="odd">
				<select name="leaveTypeId">
					<option value="0"><?php echo $lang_Leave_Common_All;?></option>
					<?php
		   				if (isset($leaveTypes) && is_array($leaveTypes)) {
		   					foreach ($leaveTypes as $leaveType) {
		  ?>
		 		  	<option value="<?php echo $leaveType->getLeaveTypeId(); ?>"><?php echo $leaveType->getLeaveTypeName(); ?></option>
		  <?php 			}
		   				}
		 ?>
  	    		</select>
		</th>
    	<th width="100px" class="odd"></th>
		<th class="tableMiddleRight"></th>
	</tr>
	<?php } ?>
	<tr>
		<th class="tableMiddleLeft"></th>
    	<th width="70px" class="odd"></th>

    	<th width="130px" class="odd">
   	    </th>

    	<th width="180px" class="odd"></th>

    	<th width="150px" class="odd">
		</th>
    	<th width="100px" class="odd"></th>
		<th class="tableMiddleRight"></th>
	</tr>
  </tbody>
  <tfoot>
  	<tr>
		<td class="tableBottomLeft"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
</form>