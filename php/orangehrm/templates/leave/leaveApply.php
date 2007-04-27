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
 */

require_once ROOT_PATH . '/lib/confs/sysConf.php';

 $employees = null;

 if (isset($records[0])) {
 	$employees = $records[0];
 }

 if (isset($records[2])) {
 	$role = $records[2];
 }

 if (isset($_GET['message'])) {
?>

<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<script>

	function addSave() {
		err = false;
		msg = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

		obj = document.frmLeaveApp.txtLeaveFromDate;
		if ((obj.value == '') || !validDate(obj.value)) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectAValidFromDate; ?>\n"
		}

		obj = document.frmLeaveApp.txtLeaveToDate;
		if (obj.value == '') {
			fillAuto('txtLeaveFromDate', 'txtLeaveToDate');
		}
		if ((obj.value == '') || !validDate(obj.value)) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectAValidFromDate; ?>\n"
		}

		obj = document.frmLeaveApp.sltLeaveType;
		if (obj.value == -1) {
			err = true;
			msg += " - <?php echo $lang_Error_PleaseSelectALeaveType; ?>\n"
		}

		if (document.frmLeaveApp.cmbEmployeeId) {
			obj = document.frmLeaveApp.cmbEmployeeId;
			if (obj.value == -1) {
				err = true;
				msg += " - <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n"
			}
		}

		if (err) {
			alert(msg);
		} else {
			document.frmLeaveApp.submit();
		}
	}

	function validDate(txt) {
		dateExpression = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/

		if (!dateExpression.test(txt)) {
			return false;
		}

		return true;
	}

	function $(id) {
		return document.getElementById(id);
	}

	function fillAuto(from, to) {
		v1 = $(from).value.trim();
		v2 = $(to).value.trim();

		if (v2 == "") {
			$(to).value = v1;
		}
	}

	String.prototype.trim = function () {
		regExp = /^\s+|\s+$/g;
		str = this;
		str = str.replace(regExp, "");

		return str;
	}

	function returnEmpDetail(){
		var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=LEAVE','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
		popup.focus();
	}

	function fillToDate() {
		obj = document.frmLeaveApp.txtLeaveToDate;
		if (obj.value == '') {
			fillAuto('txtLeaveFromDate', 'txtLeaveToDate');
		}
	}

</script>
<h2>
	<?php
      if (isset($employees) && is_array($employees)) {
		 echo $lang_Leave_Title_Assign_Leave;
		 $modifier = "Leave_Admin_Apply";
		 $btnApply = "assign.gif";
		 $btnApplyMO = "assign_o.gif";
      } else {
      	 echo $lang_Leave_Title_Apply_Leave;
      	 $modifier = "Leave_Apply";
      	 $btnApply = "apply.gif";
		 $btnApplyMO = "apply_o.gif";
      }
     ?>
  <hr/>
</h2>
<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=<?php echo $modifier; ?>">
  <table border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
    <?php if (isset($role)) { ?>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_EmployeeName; ?></td>
        <td width="25px">&nbsp;</td>
		<td>
		<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
			<input type="text" name="txtEmployeeId" id="txtEmployeeId" disabled />
			<input type="hidden" name="cmbEmployeeId" id="cmbEmployeeId" />
			<input type="button" value="..." onclick="returnEmpDetail();" />
		<?php } else if (isset($employees) && is_array($employees)) { ?>
			<select name="cmbEmployeeId">
	        	<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
				<?php
			   		sort($employees);
			   		foreach ($employees as $employee) {
			  	?>
			 		  	<option value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>
			  <?php } ?>
	  	    </select>
		<?php } ?>
		</td>
	  	<td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
    <?php } ?>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_LeaveType; ?></td>
        <td width="25px">&nbsp;</td>
        <td><select name="sltLeaveType" id="sltLeaveType">
            <?php
	  	if (is_array($records[1])) {
	  	 	foreach ($records[1] as $record) {
	  ?>
            <option value="<?php echo $record->getLeaveTypeID();?>"><?php echo $record->getLeaveTypeName(); ?></option>
            <?php  }
			} else {?>
            <option value="-1">-- <?php echo $lang_Error_NoLeaveTypes; ?> --</option>
            <?php } ?>
          </select>
        </td>
        <td width="50px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
     </tr>
     <?php
	  	if (!(is_array($records[1]))) {  ?>
	    <tr>
     	<td class="tableMiddleLeft"></td>
     	<td width="75px">&nbsp;</td>
        <td width="25px">&nbsp;</td>
      	<td><?php echo $lang_Leave_Common_LeaveQuotaNotAllocated;?></td>
    	<td width="25px">&nbsp;</td>
    	<td class="tableMiddleRight"></td>
     </tr> <?php } ?>
     <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_FromDate;?></td>
        <td width="25px">&nbsp;</td>
        <td><?php echo $lang_Leave_Common_ToDate;?></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><input name="txtLeaveFromDate" type="text" id="txtLeaveFromDate" onfocus="fillAuto('txtLeaveFromDate', 'txtLeaveToDate');"/>
          <input type="button" name="Submit" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmLeaveApp.txtLeaveFromDate); fillToDate(); return false;"/>
        </td>
        <td width="25px">&nbsp;</td>
        <td><input name="txtLeaveToDate" type="text" id="txtLeaveToDate" onfocus="fillAuto('txtLeaveFromDate', 'txtLeaveToDate');" />
          <input type="button" name="Submit" value="..." onclick="fillAuto('txtLeaveFromDate', 'txtLeaveToDate'); if(self.gfPop)gfPop.fPopCalendar(document.frmLeaveApp.txtLeaveToDate);return false;"/>
        </td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><?php echo $lang_Leave_Common_Length; ?></td>
        <td width="25px">&nbsp;</td>
        <td><?php echo $lang_Leave_Common_Comment; ?></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr valign="top">
        <td class="tableMiddleLeft"></td>
        <td><select name="sltLeaveLength" id="sltLeaveLength">
            <option value="<?php echo (Leave::LEAVE_LENGTH_FULL_DAY);?>"><?php echo $lang_Leave_Common_FullDay;?></option>
            <option value="<?php echo (Leave::LEAVE_LENGTH_HALF_DAY_MORNING);?>"><?php echo $lang_Leave_Common_HalfDayMorning;?></option>
            <option value="<?php echo (Leave::LEAVE_LENGTH_HALF_DAY_AFTERNOON);?>"><?php echo $lang_Leave_Common_HalfDayAfternoon;?></option>
          </select>
        </td>
        <td width="25px">&nbsp;</td>
        <td><textarea name="txtComments" id="txtComments"></textarea></td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
      <tr>
        <td class="tableMiddleLeft"></td>
        <td><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/icons/<?php echo $btnApply; ?>';" onmouseover="this.src='../../themes/beyondT/icons/<?php echo $btnApplyMO; ?>';" src="../../themes/beyondT/icons/<?php echo $btnApply; ?>" /></td>
        <td width="25px">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25px">&nbsp;</td>
        <td class="tableMiddleRight"></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td class="tableBottomLeft"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
