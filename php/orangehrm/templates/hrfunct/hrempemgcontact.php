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
function delEContact() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkecontactdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete?>')
		return;
	}

	document.frmEmp.econtactSTAT.value="DEL";
	qCombo(5);
}

function validateEContact() {

	if(document.frmEmp.txtEConName.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty?>');
		document.frmEmp.txtEConName.focus();
		return false;
	}

	if(document.frmEmp.txtEConRel.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty?>');
		document.frmEmp.txtEConRel.focus();
		return false;
	}

	if ((document.frmEmp.txtEConHmTel.value == '') &&
		(document.frmEmp.txtEConMobile.value == '') &&
		(document.frmEmp.txtEConWorkTel.value == '')) {
		alert('<?php echo $lang_hremp_ie_PleaseSpecifyAtLeastOnePhoneNo; ?>');
		document.frmEmp.txtEConHmTel.focus();
		return false;
	}

	var cntrl = document.frmEmp.txtEConHmTel;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_hmtele : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.txtEConMobile;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_mobile : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.txtEConWorkTel;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_worktele : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	return true;
}

function addEContact() {
	if(validateEContact()) {
		document.frmEmp.econtactSTAT.value="ADD";
		qCombo(5);
	}
}

function viewEContact(ecSeq) {
	document.frmEmp.action=document.frmEmp.action + "&ECSEQ=" + ecSeq ;
	document.frmEmp.pane.value=5;
	document.frmEmp.submit();
}

function editEContact() {
	if(validateEContact()) {
		document.frmEmp.econtactSTAT.value="EDIT";
		qCombo(5);
	}
}

//--><!]]></script>
<div id="parentPaneEmgContact" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
    <input type="hidden" name="econtactSTAT" value=""/>
<?php if(isset($this->getArr['ECSEQ'])) {
		$edit = $this->popArr['editECForm'];
?>
	<div id="editPaneEmgContact">
		<table id="editPaneEmgContact" style="height:120px;padding:0 5px 0 5px;" border="0" cellpadding="0" cellspacing="0">
          <tr>
			 <td>
			 	<?php echo $lang_hremp_name; ?> <span class="required">*</span>
			 	<input type="hidden" name="txtECSeqNo" value="<?php echo CommonFunctions::escapeHtml($edit[0][1])?>">
			 </td>
			 <td><input type="text" name="txtEConName" maxlength="100"
                        value="<?php echo CommonFunctions::escapeHtml($edit[0][2])?>"/></td>
			 <td width="50">&nbsp;</td>
			<td><?php echo $lang_hremp_relationship; ?> <span class="required">*</span></td>
			 <td><input type="text" name="txtEConRel" maxlength="100"
                        value="<?php echo CommonFunctions::escapeHtml($edit[0][3])?>"/></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_hmtele; ?></td>
			 <td><input type="text"  name="txtEConHmTel" maxlength="100"
                        value="<?php echo CommonFunctions::escapeHtml($edit[0][4])?>"/></td>
			 <td width="50">&nbsp;</td>
			 <td><?php echo $lang_hremp_mobile; ?></td>
			 <td><input type="text" name="txtEConMobile" maxlength="100"
                        value="<?php echo CommonFunctions::escapeHtml($edit[0][5])?>"/></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_worktele; ?></td>
			 <td><input type="text" name="txtEConWorkTel"maxlength="100"
                        value="<?php echo CommonFunctions::escapeHtml($edit[0][6])?>"/></td>
			 </tr>
		</table>
<?php	if (($locRights['edit']) || ($_GET['reqcode'] === "ESS")) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnEditEContact" id="btnEditEContact"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEContact(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>

	</div>
<?php  } else { ?>
	<div id="addPaneEmgContact" class="<?php echo ($this->popArr['empECAss'] != null)?"addPane":""; ?>" >
		<table style="height:120px;padding:0 5px 0 5px;" border="0" cellpadding="0" cellspacing="0">
			 <tr>
			 <td><?php echo $lang_hremp_name; ?> <span class="required">*</span>
			 	<input type="hidden" name="txtECSeqNo" value="<?php echo CommonFunctions::escapeHtml($this->popArr['newECID'])?>" /></td>
			  <td><input name="txtEConName" maxlength="100" <?php echo $locRights['add'] ? '':''?> type="text"/></td>
			 <td width="50">&nbsp;</td>
			 <td><?php echo $lang_hremp_relationship; ?> <span class="required">*</span>&nbsp;&nbsp;</td>
			 <td><input name="txtEConRel" maxlength="100" <?php echo $locRights['add'] ? '':''?> type="text"/></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_hmtele; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConHmTel" maxlength="100" <?php echo $locRights['add'] ? '':''?> type="text"/></td>
			 <td width="50">&nbsp;</td>
			 <td><?php echo $lang_hremp_mobile; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConMobile" maxlength="100" <?php echo $locRights['add'] ? '':''?> type="text"/></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_worktele; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConWorkTel" maxlength="100" <?php echo $locRights['add'] ? '':''?> type="text"/></td>
			 </tr>
		</table>
<?php	if (($locRights['add']) || ($_GET['reqcode'] === "ESS")) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddEContact" id="btnAddEContact"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addEContact(); return false;" />
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>
	</div>
<?php } ?>
<?php
$rset = $this->popArr['empECAss'];
		if (!empty($rset)) {  ?>
		<div class="subHeading"><h3><?php echo $lang_hremp_AssignedEmergencyContacts;?></h3></div>

			<div class="actionbar">
				<div class="actionbuttons">


		<?php if($locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('EmgContact');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
		<?php } ?>
		<?php	if (($locRights['delete']) || ($_GET['reqcode'] === "ESS"))  { ?>
					<input type="button" class="delbutton"
						onclick="delEContact();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

		<?php 	} ?>
			</div>
		</div>
		<table width="550" cellspacing="0" cellpadding="0" class="data-table">
			<thead>
				<tr>
	                <td width="50">&nbsp;</td>
					<td><?php echo $lang_hremp_name; ?></td>
					<td><?php echo $lang_hremp_relationship; ?></td>
					<td><?php echo $lang_hremp_hmtele; ?></td>
					<td><?php echo $lang_hremp_mobile; ?></td>
					<td><?php echo $lang_hremp_worktele; ?></td>
				</tr>
			</thead>
			<tbody>
<?php
		for ($c=0; $c < count($rset); $c++) {
			$cssClass = ($c%2) ? 'even' : 'odd';
        echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'/></td>";

            ?> <td><a href="javascript:viewEContact('<?php echo $rset[$c][1]?>')"><?php echo CommonFunctions::escapeHtml($rset[$c][2])?></a></td> <?php
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][3]) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][4]) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][5]) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][6]) .'</td>';

        echo '</tr>';
   		} ?>
			</tbody>
   	</table>
<?php } ?>
<?php } ?>
</div>
