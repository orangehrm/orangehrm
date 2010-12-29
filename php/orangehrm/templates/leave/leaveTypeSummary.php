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

	/* To check whether active leave types are available: Begins */
	$sum = 0;
   $token = $records['token'];
   unset($records['token']);
	foreach ($records as $record) {
		$flag = $record->getLeaveTypeAvailable();
		if ($flag == 1) {
			$sum++;
		}
	}

	$activeTypesAvailable = false;
	if ($sum > 0) {
		$activeTypesAvailable = true;
	}
	/* To check whether active leave types are available: Ends */

    $duplicateJavascript = '';
    
    $rights = $_SESSION['localRights'];
    $disabled = 'disabled="disabled"';
?>

<script type="text/javascript">
//<![CDATA[

	var deletedLeaveTypes = new Array();

	function actionAdd() {
		document.defineLeaveType.action = '?leavecode=Leave&action=Leave_Type_View_Define';
 		document.defineLeaveType.submit();
	}

	function actionEdit() {

	  with (document.defineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'text') {
					elements[i].disabled = "";
				}
			}
		}
		document.getElementById("btnEdit").className = 'savebutton';
		document.getElementById("btnEdit").onclick = editRecord;
        document.getElementById("btnEdit").value = '<?php echo $lang_Common_Save;?>';
        document.getElementById("btnEdit").title = '<?php echo $lang_Common_Save;?>';

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
		document.defineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
 		document.defineLeaveType.submit();
	}


	function actionDelete() {
		$check = 0;
		with (document.defineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}

		if ( $check == 1 ){

			var res = confirm("<?php echo $lang_Error_DoYouWantToDelete; ?>");

			if(!res) return;

			document.defineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Delete';
 			document.defineLeaveType.submit();
		}else{
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
		}
	}


	function doHandleAll() {
		with (document.defineLeaveType) {
			if(elements['allCheck'].checked == false){
				doUnCheckAll();
			}
			else if(elements['allCheck'].checked == true){
				doCheckAll();
			}
		}
	}


	function doCheckAll() {
		with (document.defineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = true;
				}
			}
		}
	}


	function doUnCheckAll() {
		with (document.defineLeaveType) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = false;
				}
			}
		}
	}
	
	/**
	 * If at least one day is unchecked, main check box would be unchecked
	 */

	function unCheckMain() {
		noOfCheckboxes = 0;
		noOfCheckedCheckboxes = 0;

		with ($('defineLeaveType')) {
			for (i = 0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox' && elements[i].name != 'allCheck') {
					noOfCheckboxes++;
					if (elements[i].checked == true) {
						noOfCheckedCheckboxes++;
					}

				}
			}
		}

		$('allCheck').checked = (noOfCheckboxes == noOfCheckedCheckboxes);
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

		with (document.defineLeaveType) {
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
 		document.defineLeaveType.action = '?leavecode=Leave&action=Leave_Type_Edit';
 		document.defineLeaveType.submit();
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
//]]>
</script>
<div class="outerbox">
<form method="post" name="defineLeaveType" id="defineLeaveType" onsubmit="return false;" action="" onreset="window.setTimeout('checkForDuplicates()', 100);">
   <input type="hidden" value="<?php echo $token;?>" name="token" />
    <div class="mainHeading"><h2><?php echo $lang_Leave_Leave_Type_Summary_Title; ?></h2></div>

    <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
        if (isset($message)) {
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $messageStr = "lang_Leave_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$messageStr)) ? $$messageStr: CommonFunctions::escapeHtml($message); ?></span>
        </div>
    <?php } ?>

    <div class="actionbar">
        <div class="actionbuttons">
            <input type="button" class="addbutton" <?php echo ($rights['add']) ? '' : $disabled; ?>
                name="btnAdd" id="btnAdd" onclick="actionAdd(); return false;"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Add;?>" />

              <?php /* Show edit & delete button only if records are available: Begins */
               if ($activeTypesAvailable) {
              ?>
                <input type="button" class="editbutton" <?php echo ($rights['edit']) ? '' : $disabled; ?>
                    name="btnEdit" id="btnEdit" onclick="actionEdit(); return false;"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Edit;?>" />

                <input type="button" class="delbutton" onclick="actionDelete(); return false;"
                    name="btnDel" id="btnDel" <?php echo ($rights['delete']) ? '' : $disabled; ?>
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Delete;?>" />
              <?php /* Show edit & delete button only if records are available: Ends */
              }
              ?>
              <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
        </div>
        <div class="noresultsbar"><?php echo !$activeTypesAvailable ? $lang_Error_NoRecordsFound : '';?></div>
        <div class="pagingbar"></div>
    <br class="clear" />
    </div>
    <br class="clear" />

<?php /* Show table only if records are available: Begins */
if ($activeTypesAvailable) {
?>
  <table border="0" cellpadding="0" cellspacing="0" class="data-table">
  <thead>
    <tr>
      <td width="50">
        <input type='checkbox' class='checkbox' name='allCheck' id='allCheck' value='' onclick="doHandleAll();" />
      </td>
      <td><?php echo $lang_Leave_Common_LeaveTypeId;?></td>
      <td><?php echo $lang_Leave_Common_LeaveType;?></td>
    </tr>
  </thead>
  <tbody>
    <?php
	$j = 0;
	if (is_array($records))
		foreach ($records as $record) {
			if ($record->getLeaveTypeAvailable() != $record->availableStatusFlag) {
                $leaveTypeName = addslashes($record->getLeaveTypeName());
				$duplicateJavascript .= "deletedLeaveTypes.push(\"{$leaveTypeName}\");\n";
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
      <td class="<?php echo $cssClass; ?>"><input type='checkbox' class='checkbox' name='chkLeaveTypeID[]' value='<?php echo $record->getLeaveTypeId();?>' onclick="unCheckMain();" /></td>
      <td class="<?php echo $cssClass; ?>"><?php echo $record->getLeaveTypeId();?>
	  </td>
      <td class="<?php echo $cssClass; ?>"><input name="txtLeaveTypeName[]" type="text" value="<?php echo $record->getLeaveTypeName();?>" disabled="disabled" onkeyup="checkForDuplicates();"/>
        <input type="hidden" name="id[]" value="<?php echo $record->getLeaveTypeId();?>" /></td>
    </tr>
    <?php
		}
?>
  </tbody>
</table>
<?php /* Show table only if records are available: Ends */
}
?>
<div><span class="error" id="messageLayer1"></span></div>
<div><span class="error" id="messageLayer2"></span></div>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
    <?php echo $duplicateJavascript;?>
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>