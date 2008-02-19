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
require_once ROOT_PATH . '/lib/confs/sysConf.php';

if (isset($_GET['message']) && !empty($_GET['message'])) {

	$expString  = $_GET['message'];
	$expString = explode ("_",$expString);
	$length = count($expString);

	$col_def=strtolower($expString[$length-1]);
	$expString='lang_Leave_'.$_GET['message'];

	$message = isset($$expString) ? $$expString : CommonFunctions::escapeHtml($_GET['message']);
?>
	<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $message; ?>
	</font>
<?php }	?>
<script>

	var deletedLeaveTypes = new Array();

	function actionAdd() {

		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_View_Define';
 		document.DefineLeaveType.submit();
	}

	function actionEdit() {

	  with (document.DefineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'text') {
					elements[i].disabled = "";
				}
			}
		}
		document.getElementById("btnEdit").src = '../../themes/beyondT/pictures/btn_save.gif' ;
		document.getElementById("btnEdit").onmouseover = switchToSave2;
		document.getElementById("btnEdit").onmouseout = switchToSave;
		document.getElementById("btnEdit").onclick = editRecord;

		document.getElementById("btnAdd").disabled = 'true';
		document.getElementById("btnDel").disabled = 'true';
	}

	function switchToSave() {
		document.getElementById("btnEdit").src='../../themes/beyondT/pictures/btn_save.gif';
	}

	function switchToSave2() {
		document.getElementById("btnEdit").src='../../themes/beyondT/pictures/btn_save_02.gif';
	}

	function actionEditData()
	{
		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
 		document.DefineLeaveType.submit();
	}


	function actionDelete() {
		$check = 0;
		with (document.DefineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}

		if ( $check == 1 ){

			var res = confirm("<?php echo $lang_Error_DoYouWantToDelete; ?>");

			if(!res) return;

			document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Delete';
 			document.DefineLeaveType.submit();
		}else{
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
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

	function removeMarkFromCell(cell, mark) {

		// See if cell already contains mark
		children = cell.getElementsByTagName('span');
		for (var i=0; i < children.length; i++) {

			spanElement = children[i];
			txtNode = spanElement.firstChild;
			if (txtNode.data == mark) {
				cell.removeChild(spanElement);
				return;
			}
		}
	}

	function addMarkToCell(cell, mark, className) {

		// See if cell already contains mark
		children = cell.getElementsByTagName('span');
		for (var i=0; i < children.length; i++) {

			spanElement = children[i];
			txtNode = spanElement.firstChild;
			if (txtNode.data == mark) {
				return;
			}
		}

		txtNode = document.createTextNode(mark);
		spanNode = document.createElement('span');
		spanNode.setAttribute('class', className);
		spanNode.appendChild(txtNode);
		cell.appendChild(spanNode);
	}

	function checkForDuplicates() {

		var noDuplicates = true;
		var deletedNames = false;
		var typeNames = {};

		with (document.DefineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'text') {

					var txtElement = elements[i];
					var name = txtElement.value;
					var cell = txtElement.parentNode;

					if (name in typeNames) {
						txtElement.className = "error";
						otherTxtElement = typeNames[name];
						otherTxtElement.className = "error";

						addMarkToCell(cell, "*", "error");
						addMarkToCell(otherTxtElement.parentNode, "*", "error");
						noDuplicates = false;
					} else {
						removeMarkFromCell(cell, "*");
						txtElement.className = "";
						typeNames[name] = txtElement;
					}


					if (isDeletedName(name)) {
						deletedNames = true;
						addMarkToCell(cell, "+", "warning");
					} else {
						removeMarkFromCell(cell, "+");
					}
				}
			}
		}

		message = (noDuplicates) ? "" : '* <?php echo $lang_Leave_DUPLICATE_LEAVE_TYPE_ERROR; ?>';
		messageLayer = document.getElementById("messageLayer1");
		messageLayer.className = "error";
		messageLayer.innerHTML = message;

		message = (deletedNames) ? '+ <?php echo $lang_Leave_Define_IsDeletedName; ?>' : "";
		messageLayer = document.getElementById("messageLayer2");
		messageLayer.className = "warning";
		messageLayer.innerHTML = message;

		return noDuplicates;
	}

	function editRecord() {

		if (!checkForDuplicates()) {
			alert('<?php echo $lang_Leave_DUPLICATE_LEAVE_TYPE_ERROR; ?>');
			return false;
		}
 		document.DefineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
 		document.DefineLeaveType.submit();
	}

	function isDeletedName(name) {
		n = deletedLeaveTypes.length;
		for (var i=0; i<n; i++) {
			if (deletedLeaveTypes[i] == name) {
				return true;
			}
		}
		return false;
	}
</script>
<h2><?php echo $lang_Leave_Leave_Type_Summary_Title; ?><hr/></h2>
<form method="post" name="DefineLeaveType" id="DefineLeaveType" onsubmit="return false;">
<p class="navigation">
	<input type="image" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.gif';" src="../../themes/beyondT/pictures/btn_add.gif" name="btnAdd" id="btnAdd" onclick="actionAdd(); return false;"/>
	<input type="image" src="../../themes/beyondT/pictures/btn_edit.gif" width="65" height="20" onclick="actionEdit(); return false;" onmouseover="this.src='../../themes/beyondT/pictures/btn_edit_02.gif';" onmouseout="this.src='../../themes/beyondT/pictures/btn_edit.gif';" name="btnEdit" id="btnEdit"/>
	<input type="image" onclick="actionDelete();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';" src="../../themes/beyondT/pictures/btn_delete.gif" name="btnDel" id="btnDel"/>
</p>

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
        <input type='checkbox' class='checkbox' name='allCheck' value='' onclick="doHandleAll();" />
      </div></th>
      <th width="159" align="left" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveTypeId?></th>
      <th colspan="3" align="left" class="tableMiddleMiddle"><?php echo $lang_Leave_Common_LeaveType;?></th>
      <th width="5" align="left" class="tableMiddleMiddle"></th>
      <th class="tableMiddleRight"></th>
    </tr>
  </thead>
  <tbody>
    <?php
	$j = 0;
	if (is_array($records))
		foreach ($records as $record) {
			if ($record->getLeaveTypeAvailable() != $record->availableStatusFlag) {
				echo "<script>deletedLeaveTypes.push('{$record->getLeaveTypeName()}');</script>";
				continue;
			}
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
      <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeId();?>
	  </td>
      <td colspan="3" class="<?php echo $cssClass; ?>"><input name="txtLeaveTypeName[]" type="text" id="txtLeaveTypeName[]" value="<?php echo $record->getLeaveTypeName();?>" disabled="disabled" onkeyup="checkForDuplicates();"/>
        <input type="hidden" name="id[]" value="<?php echo $record->getLeaveTypeId();?>" /></td>
      <td class="<?php echo $cssClass; ?>" align="left"></td>
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
<div><span class="error" id="messageLayer1"></span></div>
<div><span class="error" id="messageLayer2"></span></div>
</div>
</form>