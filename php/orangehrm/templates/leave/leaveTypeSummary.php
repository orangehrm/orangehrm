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
 require_once($lan->getLangPath("leave/leaveTypeSummary.php")); 

 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<script>			
	function returnEdit() {
			
	}			
	
	function returnDelete() {
		$check = 0;
		with (document.DefineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}
	
		if ( $check == 1 ){
			
			var res = confirm("Do you want to delete ?");
			
			if(!res) return;
			
			document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Delete';
 			document.DefineLeaveType.submit();
		}else{
			alert("Select At Least One Record To Delete");
		}		
	}
	function doHandleAll() {
		with (document.DefineLeaveType) {		
			if(elements['allCheck'].checked == false){
				doUnCheckAll();
			}
			else if(elements['allCheck'].checked == true){
				doCheckAll();
			}
		}	
	}
	function doCheckAll() {
		with (document.DefineLeaveType) {		
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = true;
				}
			}
		}
	}

	function doUnCheckAll() {
		with (document.DefineLeaveType) {		
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = false;
				}
			}
		}
	}
	
	function editRecord(id) {
	
 		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit_View';
 		document.DefineLeaveType.id.value = id; 
 		document.DefineLeaveType.submit();
	}
	//function clearAll() {
	//	document.frmLeaveApp.txtSkillDesc.value = '';
	//}
</script>
<h3><?php echo $lang_Title?></h3>
<form method="post" name="DefineLeaveType" id="DefineLeaveType" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Type_Define"><div>
  <table width="161" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="65"><a href="./CentralController.php?leavecode=Leave&amp;action=Leave_Type_View_Define"><img border="0" title="Add" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" /></a></td>
      <td width="48"><img src="../../themes/beyondT/pictures/btn_edit.jpg" width="65" height="20" onclick="returnEdit();" onmouseover="this.src='../../themes/beyondT/pictures/btn_edit_02.jpg';" onmouseout="this.src='../../themes/beyondT/pictures/btn_edit.jpg';" /></td>
      <td width="48"><img onclick="returnDelete();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg" /></td>
    </tr>
    <tr>
      <td colspan="4">&nbsp;</td>
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
      <th width="77" align="center" class="tableMiddleMiddle"><div align="center">
        <input type='checkbox' class='checkbox' name='allCheck' value='' onclick="doHandleAll();" /><input type="hidden" name="id" value="" />
      </div></th>
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
      <td align="center" class="<?php echo $cssClass; ?>"><input type='checkbox' class='checkbox' name='chkLeaveTypeID[]' value='<?php echo $record->getLeaveTypeId();?>' /></td>
      <td class="<?php echo $cssClass; ?>">
	  <a href="#" onclick="editRecord('<?php echo $record->getLeaveTypeId();?>');" class="listViewThS1"><?php echo $record->getLeaveTypeId();?></a>
	  </td>
      <td colspan="4" class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeName();?></td>
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