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
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--

function delChildren() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkchidel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Common_SelectDelete; ?>')
		return;
	}

	document.frmEmp.childrenSTAT.value="DEL";
	qCombo(3);
}

function addChildren() {

	if(document.frmEmp.txtChiName.value == '') {
		alert('<?php echo $lang_Error_ChildNameEmpty; ?>');
		document.frmEmp.txtChiName.focus();
		return;
	}

	/*if(!YAHOO.OrangeHRM.calendar.parseDate(document.frmEmp.ChiDOB.value)) {
		alert('<?php echo $lang_Error_ChildDobInvalid; ?>');
		document.frmEmp.DOB.focus();
		return;
	}*/

    var cnt = document.frmEmp.ChiDOB;
    if(cnt.value.length > 0 && cnt.value != 'YYYY-mm-DD') {
        var dt = cnt.value.split("-");
        if(!validateDate(dt[2], dt[1], dt[0])) {
            alert("<?php echo $lang_Error_ChildDobInvalid; ?>");
            return;
        }
    }

	document.frmEmp.childrenSTAT.value="ADD";
	qCombo(3);
}

function viewChildren(cSeq) {
	document.frmEmp.action=document.frmEmp.action + "&CHSEQ=" + cSeq ;
	document.frmEmp.pane.value=3;
	document.frmEmp.submit();
}

function editChildren() {
	document.frmEmp.childrenSTAT.value="EDIT";

    var cnt = document.frmEmp.ChiDOB;
    if(cnt.value.length > 0 && cnt.value != 'YYYY-mm-DD') {
        var dt = cnt.value.split("-");
        if(!validateDate(dt[2], dt[1], dt[0])) {
            alert("<?php echo $lang_Error_ChildDobInvalid; ?>");
            return;
        }
    }
	qCombo(3);
}

function resetChildren() {
	$('txtChiName').value = originalChildName;
	$(dobFieldId).value = originalChildDOB;
}

//--><!]]></script>
<style type="text/css">
div#editPaneChildren,
div#addPaneChildren {
	width:100%;
}

div#editPaneChildren label,
div#addPaneChildren label {
	width: 100px;
}

div#editPaneChildren br,
div#addPaneChildren br {
	clear:left;
}

div#editPaneChildren input,
div#addPaneChildren input {
	display:block;
	margin: 2px 2px 2px 2px;
	float:left;
}
</style>
<div id="parentPaneChildren">
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<h3><?php echo  $lang_hremp_children?></h3>

    <input type="hidden" name="childrenSTAT" value="" class="hiddenField" />
<?php if(isset($this->getArr['CHSEQ'])) {
		$edit = $this->popArr['editChiForm'];
?>
	<input type="hidden" name="txtCSeqNo" value="<?php echo $edit[0][1]?>" class="hiddenField" />
	<div id="editPaneChildren" >
		<label for="txtChiName"><?php echo $lang_hremp_name; ?></label>
		<input type="text" name="txtChiName" id="txtChiName" maxlength="100" value="<?php echo CommonFunctions::escapeHtml($edit[0][2])?>" />
		<br />
		<label for="eChiDOB"><?php echo $lang_hremp_dateofbirth; ?></label>
		<input class="formDateInput" type="text" name="ChiDOB" id="eChiDOB" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>" size="10" />
		<input type="button" class="calendarBtn" value="   " />
		<br />

<?php	if($locRights['edit'] || ($_GET['reqcode'] === "ESS")) { ?>
		<div class="formbuttons">
		    <input type="button" class="savebutton" name="btnEditChildren" id="btnEditChildren"
		    	value="<?php echo $lang_Common_Save;?>"
		    	title="<?php echo $lang_Common_Save;?>"
		    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		    	onclick="editChildren(); return false;"/>
		    <input type="button" class="plainbtn" value="<?php echo $lang_Common_Reset; ?>"
		    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		    	onclick="resetChildren()" />
		    <script type="text/javascript">
		    dobFieldId = "eChiDOB";
			originalChildName = "<?php echo CommonFunctions::escapeHtml($edit[0][2]); ?>";
			originalChildDOB = "<?php echo LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>";
			</script>
		</div>
<?php	} ?>
	</div>
<?php } else { ?>
	<input type="hidden" name="txtCSeqNo" value="<?php echo CommonFunctions::escapeHtml($this->popArr['newCID'])?>" class="hiddenField" />
	<div id="addPaneChildren" class="<?php echo ($this->popArr['empChiAss'] != null)?"addPane":""; ?>">
		<label for="txtChiName"><?php echo $lang_hremp_name; ?></label>
		<input name="txtChiName" id="txtChiName" type="text" maxlength="100"/>
		<br />
		<label for="aChiDOB"><?php echo $lang_hremp_dateofbirth; ?></label>
		<input class="formDateInput" type="text" name="ChiDOB" id="aChiDOB" size="10" />
		<input type="button" class="calendarBtn" value="   " />
		<br />
<?php	if($locRights['add'] || ($_GET['reqcode'] === "ESS")) { ?>
		<div class="formbuttons">
		    <input type="button" class="savebutton" name="btnAddChildren" id="btnAddChildren"
		    	value="<?php echo $lang_Common_Save;?>"
		    	title="<?php echo $lang_Common_Save;?>"
		    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		    	onclick="addChildren(); return false;"/>
		    <input type="button" class="plainbtn" value="<?php echo $lang_Common_Reset; ?>"
		    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		    	onclick="resetChildren()" />
		    <script type="text/javascript">
		    dobFieldId = "aChiDOB";
			originalChildName = "";
			originalChildDOB = "";
			</script>
		</div>
<?php	} ?>
	</div>
<?php } ?>
<br />
<?php
//checking for the records if exsists show the children table and the delete btn else hide
	$rset = $this->popArr['empChiAss'];
	if ($rset != null) {?>
	<div class="subHeading"><h3><?php echo $lang_hremp_AssignedChildren; ?></h3></div>

	<div class="actionbar">
		<div class="actionbuttons">
	<?php if($locRights['add'] || ($_GET['reqcode'] === "ESS")) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('Children');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
	<?php	if($locRights['delete'] || ($_GET['reqcode'] === "ESS")) { ?>
					<input type="button" class="delbutton"
						onclick="delChildren();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

	<table style="width:100%;" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
                <td></td>
				<td><?php echo $lang_hremp_name; ?></td>
				<td><?php echo $lang_hremp_dateofbirth; ?></td>
			</tr>
		</thead>
		<tbody>
<?php
    for($c=0;$rset && $c < count($rset); $c++) {
		$cssClass = ($c%2) ? 'even' : 'odd';
?>
        <tr class="<?php $cssClass; ?>">
			<td><input type="checkbox" class="checkbox" name="chkchidel[]" value="<?php echo $rset[$c][1]; ?> " /></td>
			<td><a href="javascript:viewChildren(<?php echo $rset[$c][1]?>)"><?php echo CommonFunctions::escapeHtml($rset[$c][2])?></a></td>
			<td><?php echo LocaleUtil::getInstance()->formatDate($rset[$c][3]); ?></td>
		</tr>
<?php } ?>
		</tbody>
	</table>
	<?php } ?>
<?php } ?>
</div>
