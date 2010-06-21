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

if ($records['messageType'] == 'SUCCESS') {
	$records['message'] = $lang_Time_AttendanceConfigSaving_SUCCESS;
} elseif ($records['messageType'] == 'FAILURE') {
	$records['message'] = $lang_Time_AttendanceConfigSaving_FAILURE;
} else {
	$records['message'] = null;
}

$token = "";
if(isset($records['token'])) {
 $token = $records['token'];
}
?>

<div class="formpage">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Time_AttendanceConfiguration; ?></h2></div>
        <?php if (isset($records['message'])) { ?>
            <div class="messagebar">
                <span class="<?php echo $records['messageType']; ?>"><?php echo $records['message']; ?></span>
            </div>
        <?php } ?>

<form action="?timecode=Time&action=Save_Attendance_Config" method="post" id="attendanceConfig" name="attendanceConfig">
   <input type="hidden" value="<?php echo $token;?>" name="token" />
<table width="480" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td width="470">&nbsp;<?php echo $lang_Time_EmpChangeTime; ?></td>
      <td width="10">
        <input name="chkEmpChangeTime" id="chkEmpChangeTime" type="checkbox" <?php echo ($records['empChangeTime']?'checked':''); ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Time_EmpEditSubmitted; ?></td>
      <td>
        <input name="chkEmpEditSubmitted" id="chkEmpEditSubmitted" type="checkbox" <?php echo ($records['empEditSubmitted']?'checked':''); ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Time_SupEditSubmitted; ?></td>
      <td>
        <input name="chkSupEditSubmitted" id="chkSupEditSubmitted" type="checkbox" <?php echo ($records['supEditSubmitted']?'checked':''); ?> />
      </td>
    </tr>
  </table>


    <div class="formbuttons">
        <input type="submit" class="savebutton" id="saveBtn"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Save;?>" />
    </div>
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
