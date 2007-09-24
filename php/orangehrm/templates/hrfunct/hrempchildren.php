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
<script language="JavaScript">

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
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtChiName.focus();
		return;
	}

	if(!YAHOO.OrangeHRM.calendar.parseDate(document.frmEmp.DOB.value)) {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.DOB.focus();
		return;
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
	qCombo(3);
}

</script>
<span id="parentPaneChildren" >
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<h3><?php echo  $lang_hremp_children?></h3>

    <input type="hidden" name="childrenSTAT" value="">
<?php if(isset($this->getArr['CHSEQ'])) {
		$edit = $this->popArr['editChiForm'];
?>
	<div id="editPaneChildren" >
		<table height="100" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php echo $lang_hremp_name?>
                	<input type="hidden" name="txtCSeqNo" value="<?php echo $edit[0][1]?>"></td>
                <td><input type="text" name="txtChiName" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?php echo $lang_hremp_dateofbirth?></td>
                <td><input type="text" name="ChiDOB" id="eChiDOB" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>" size="10">
                	<input type="button" <?php echo $locRights['edit'] ? '':'disabled'?> class="calendarBtn" value="   " /></td>
               </tr>

				  <td>
					<?php	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
					<?php	} ?>
				</td>
			</tr>
		</table>
	</div>
<?php } else { ?>
	<div id="addPaneChildren" class="<?php echo ($this->popArr['empChiAss'] != null)?"addPane":""; ?>">
		<table height="100" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php echo $lang_hremp_name; ?>
                	<input type="hidden" name="txtCSeqNo" value="<?php echo $this->popArr['newCID']?>"></td>
                <td><input name="txtChiName" <?php echo $locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?php echo $lang_hremp_dateofbirth; ?></td>
				<td><input type="text" name="ChiDOB" id="aChiDOB" size="10">
					<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="calendarBtn" value="   " /></td>
            </tr>
				  <td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
<?php	} ?>
				</td>
			</tr>
		</table>
	</div>
<?php } ?>
<?php
//checking for the records if exsists show the children table and the delete btn else hide
	$rset = $this->popArr['empChiAss'];
	if ($rset != null) {?>
		<?php if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="showAddPane('Children');" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.gif';" src="../../themes/beyondT/pictures/btn_add.gif" />
		<?php } ?>
		<?php	if($locRights['delete']) { ?>
		<img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';" src="../../themes/beyondT/pictures/btn_delete.gif">
		<?php 	}//finish checking ?>
		<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_dateofbirth; ?></strong></td>
				</tr>
	<?php
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewChildren(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . LocaleUtil::getInstance()->formatDate($rset[$c][3]) .'</td>';
            echo '</tr>';
        }?>
        </table>
	<?php } ?>
<?php } ?>
</span>
