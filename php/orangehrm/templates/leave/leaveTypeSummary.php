<?
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
		
		//document.frmSkills.sqlState.value = "NewRecord";
		document.DefineLeaveType.submit();		
	}			
	
	//function clearAll() {
	//	document.frmLeaveApp.txtSkillDesc.value = '';
	//}
</script>
<h3><?php echo $lang_Title?></h3>
<form method="post" name="DefineLeaveType" id="DefineLeaveType" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Type_Define"><div>
  <table width="198" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="65"><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" /></td>
      <td width="65"><img title="Edit" onMouseOut="this.src='../../themes/beyondT/pictures/btn_edit.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_edit_02.jpg';" src="../../themes/beyondT/pictures/btn_edit.jpg" name="EditAss" onClick="editAss();"></td>
      <td width="206"><img onClick="delEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg"></td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
</div> 
  <table width="516" border="0" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <th width="1" class="tableTopLeft"></th>
      <th colspan="6" class="tableTopMiddle"></th>
      <th width="1" class="tableTopRight"></th>
    </tr>
    <tr>
      <th class="tableMiddleLeft"></th>
      <th width="77" class="tableMiddleMiddle">&nbsp;</th>
      <th width="159" align="left" class="tableMiddleMiddle"><?php echo $lang_LeaveTypeId?></th>
      <th colspan="4" align="left" class="tableMiddleMiddle"><?php echo $lang_LeaveType;?></th>
      <th class="tableMiddleRight"></th>
    </tr>
  </thead>
  <tbody>
    <?php
	$j = 0;
	if (is_array($records))
		foreach ($records as $record) {
			if(!($j%2)) { 
				$cssClass = 'odd';
			 } else {
			 	$cssClass = 'even';
			 }
			 $j++;
?>
    <tr>
      <td class="tableMiddleLeft"></td>
      <td align="center" class="<?php echo $cssClass; ?>"><input type="checkbox" name="checkbox" value="checkbox"></td>
      <td class="<?php echo $cssClass; ?>">&nbsp;</td>
      <td colspan="4" class="<?php echo $cssClass; ?>">&nbsp;</td>
      <td class="tableMiddleRight"></td>
    </tr>
    <?php 	
		}
?>
  </tbody>
  <tfoot>
    <tr>
      <td class="tableBottomLeft"></td>
      <td colspan="6" class="tableBottomMiddle"></td>
      <td class="tableBottomRight"></td>
    </tr>
  </tfoot>
</table>
</form>