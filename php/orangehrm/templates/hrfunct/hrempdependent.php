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
function delDependent() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkdepdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Common_SelectDelete; ?>')
		return;
	}

	document.frmEmp.dependentSTAT.value="DEL";
	qCombo(3);
}

function validateDependants() {
	if(document.frmEmp.txtDepName.value == '') {
		alert('<?php echo $lang_Error_DependantNameEmpty; ?>');
		document.frmEmp.txtDepName.focus();
		return false;
	}

	return true;
}

function addDependent() {

	if (! validateDependants()) {
		return false;
	}

	document.frmEmp.dependentSTAT.value="ADD";
	qCombo(3);
	return true;
}

function viewDependent(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&depSEQ=" + pSeq ;
	document.frmEmp.pane.value=3;
	document.frmEmp.submit();
}

function editDependent() {
	if (! validateDependants()) {
		return false;
	}

	document.frmEmp.dependentSTAT.value="EDIT";
	qCombo(3);
	return true;
}

//--><!]]></script>
<div id="parentPaneDependents" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<h3><?php echo $lang_hremp_dependents; ?></h3>
    <input type="hidden" name="dependentSTAT"/>

<?php if(isset($this->getArr['depSEQ'])) {
		$edit = $this->popArr['editDepForm'];
?>
	<div id="editPaneDependents" >
		<table style="height:100px" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                	<?php echo $lang_hremp_name; ?> <span class="error">*</span>
                	<input type="hidden" name="txtDSeqNo" value="<?php echo $edit[0][1]?>" />
                </td>
                <td><input type="text" name="txtDepName" value="<?php echo $edit[0][2]?>"/></td>
               </tr>
              <tr>
                <td><?php echo $lang_hremp_relationship; ?>&nbsp;</td>
                <td><input name="txtRelShip" type="text" value="<?php echo $edit[0][3]?>">
               </tr>
		</table>
<?php	if($locRights['edit'] || ($_GET['reqcode'] === "ESS")) { ?>
	<div class="formbuttons">
	    <input type="button" class="savebutton" name="btnEditDependent" id="btnEditDependent"
	    	value="<?php echo $lang_Common_Save;?>"
	    	title="<?php echo $lang_Common_Save;?>"
	    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	    	onclick="editDependent(); return false;"/>
	    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
	</div>
<?php	} ?>
	</div>
	<?php } else { ?>
	<div id="addPaneDependents" class="<?php echo ($this->popArr['empDepAss'] != null)?"addPane":""; ?>" >
		<table style="height:100px" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php echo $lang_hremp_name; ?> <span class="error">*</span>
                	<input type="hidden" name="txtDSeqNo" value="<?php echo $this->popArr['newDepID']?>"/></td>
                <td><input name="txtDepName" type="text"/></td>
                </tr>
                <tr>
                <td><?php echo $lang_hremp_relationship ; ?>&nbsp;</td>
                <td><input type="text" name="txtRelShip" /></td>
              </tr>
		</table>
<?php	if($locRights['add'] || ($_GET['reqcode'] === "ESS")) { ?>
	<div class="formbuttons">
	    <input type="button" class="savebutton" name="btnAddDependent" id="btnAddDependent"
	    	value="<?php echo $lang_Common_Save;?>"
	    	title="<?php echo $lang_Common_Save;?>"
	    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
	    	onclick="addDependent(); return false;"/>
	    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
	</div>
<?php	} ?>
	</div>
<?php } ?>
<?php
//checking for the records if exsists show the dependents table and the delete btn else hide
$rset = $this->popArr['empDepAss'];
if ($rset != null) { ?>
	<div class="subHeading"><h3><?php echo $lang_hremp_AssignedDependents; ?></h3></div>

	<div class="actionbar">
		<div class="actionbuttons">
	<?php if($locRights['add'] || ($_GET['reqcode'] === "ESS")) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('Dependents');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
	<?php	if($locRights['delete'] || ($_GET['reqcode'] === "ESS")) { ?>
					<input type="button" class="delbutton"
						onclick="delDependent();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>


	<table style="width:100%;" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
                <td></td>
				<td><?php echo $lang_hremp_name; ?></td>
				<td><?php echo $lang_hremp_relationship; ?></td>
			</tr>
		</thead>
		<tbody>
<?php
    for($c=0;$rset && $c < count($rset); $c++)
        {
		$cssClass = ($c%2) ? 'even' : 'odd';
        echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'/></td>";

            ?> <td><a href="javascript:viewDependent(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>
		</tbody>
  	</table>
<?php } ?>
<?php } ?>
</div>