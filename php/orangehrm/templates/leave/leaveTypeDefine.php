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

 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<script>
	//function goBack() {
	//	location.href = "./CentralController.php?uniqcode=&VIEW=MAIN";
	//}

	function addSave() {

		if (document.DefineLeaveType.txtLeaveTypeName.value == '') {
			alert ("<?php echo $lang_Error_LeaveDateCannotBeABlankValue; ?>");
			return false;
		}

		<?php if ($_REQUEST['action'] == "Leave_Type_Edit_View") {?>
		//alert("test");
		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
		<?php } else {?>
		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Define';
		<?php }?>
		document.DefineLeaveType.submit();
	}

	//function clearAll() {
	//	document.frmLeaveApp.txtSkillDesc.value = '';
	//}
</script>
<h2><?php echo $lang_Leave_Define_leave_Type_Title; ?><hr/></h2>
<form method="post" name="DefineLeaveType" id="DefineLeaveType" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Type_Define">
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <th class="tableTopLeft"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopMiddle"></th>
        <th class="tableTopRight"></th>
      </tr>
    </thead>
    <tbody>
		<?php if($_REQUEST['action'] == "Leave_Type_Edit_View") {?>
	    <tr>
	      <td class="tableMiddleLeft"></td>
	      <td><?php echo $lang_oldLeaveTypeName; ?></td>
	      <td><?php //print_r($records);
		foreach ($records as $record) {
		  echo $record->getLeaveTypeName(); }?></td>
	      <td class="tableMiddleRight"></td>
	    </tr>
		<?php }?>
	    <tr>
	      <td class="tableMiddleLeft"></td>
	      <td width="182"><?php if($_REQUEST['action'] == "Leave_Type_Edit_View") { echo $lang_newLeaveTypeName; } else { echo $lang_Leave_Common_LeaveTypeName;}?></td>
	      <td width="391"><input name="txtLeaveTypeName" type="text" id="txtLeaveTypeName"></td>
	      <td class="tableMiddleRight"></td>
	    </tr>
	    <tr>
	      <td class="tableMiddleLeft"></td>
	      <td colspan="2">&nbsp;</td>
	      <td class="tableMiddleRight"></td>
	    </tr>
	    <tr>
	      <td class="tableMiddleLeft"></td>
	      <td colspan="2"><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg" /></td>
	      <td class="tableMiddleRight"></td>
	    </tr>
	</tbody>
    <tfoot>
      <tr>
        <td class="tableBottomLeft"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomMiddle"></td>
        <td class="tableBottomRight"></td>
      </tr>
    </tfoot>
  </table>
</form>