<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/
require_once ROOT_PATH . '/lib/confs/sysConf.php';
/*
 *	Including the language pack
 *
 **/
 
 $lan = new Language();
 
 require_once($lan->getLangPath("leave/leaveCommon.php")); 
 require_once($lan->getLangPath("leave/leaveTypeDefine.php")); 

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
			alert ("Leave Date Cannot be a Blank Value!");
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
<h2><?php echo $lang_Title?><hr/></h2>
<form method="post" name="DefineLeaveType" id="DefineLeaveType" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Type_Define">
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="13" height="12"><img src="../../themes/beyondT/pictures/table_r1_c1.gif" alt="" name="table_r1_c1" width="13" height="12" border="0" id="table_r1_c1" /></td>
      <td colspan="2" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r1_c2" width="1" height="1" border="0" id="table_r1_c2" /></td>
      <td width="14"><img src="../../themes/beyondT/pictures/table_r1_c3.gif" alt="" name="table_r1_c3" width="13" height="12" border="0" id="table_r1_c3" /></td>
    </tr>
	<?php if($_REQUEST['action'] == "Leave_Type_Edit_View") {?>
    <tr>
      <td height="24" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td><?php echo $lang_oldLeaveTypeName; ?></td>
      <td><?php //print_r($records); 
	foreach ($records as $record) {
	  echo $record->getLeaveTypeName(); }?></td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
	<?php }?>
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r2_c1" width="1" height="1" border="0" id="table_r2_c1" /></td>
      <td width="182"><?php if($_REQUEST['action'] == "Leave_Type_Edit_View") { echo $lang_newLeaveTypeName; } else { echo $lang_LeaveTypeName;}?></td>
      <td width="391"><input name="txtLeaveTypeName" type="text" id="txtLeaveTypeName"></td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif."><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r2_c3" width="1" height="1" border="0" id="table_r2_c3" /></td>
    </tr>
    <tr></tr>
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    
    
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td colspan="2"><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg" /></td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td><img src="../../themes/beyondT/pictures/table_r3_c1.gif" alt="" name="table_r3_c1" width="13" height="16" border="0" id="table_r3_c1" /></td>
      <td colspan="2" background="../../themes/beyondT/pictures/table_r3_c2.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r3_c2" width="1" height="1" border="0" id="table_r3_c2" /></td>
      <td><img src="../../themes/beyondT/pictures/table_r3_c3.gif" alt="" name="table_r3_c3" width="13" height="16" border="0" id="table_r3_c3" /></td>
    </tr>
  </table>
</form>