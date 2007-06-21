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

	if(document.frmEmp.DOB.value == '') {
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
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table height="150" border="0" cellpadding="0" cellspacing="0">
           <input type="hidden" name="childrenSTAT" value="">
<?php
		if(!isset($this->getArr['CHSEQ'])) {
?>

              <input type="hidden" name="txtCSeqNo" value="<?php echo $this->popArr['newCID']?>">
			   <th><h3><?php echo $lang_hremp_children; ?></h3></th>
              <tr>
                <td><?php echo $lang_hremp_name; ?></td>
                <td><input name="txtChiName" <?php echo $locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?php echo $lang_hremp_dateofbirth; ?></td>
				<td><input type="text" readonly value="0000-00-00" name="ChiDOB">&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
            </tr>

				  <td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
				</td>
				</tr>
<!--<div id="tablePassport">	-->




<?php
//checking for the records if exsists show the children table and the delete btn else hide

	$rset = $this->popArr['empChiAss'];

	if ($rset !=Null){?>

		<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_dateofbirth; ?></strong></td>
				</tr>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	}//finish checking ?>



	<?php }

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewChildren(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?php } elseif(isset($this->getArr['CHSEQ'])) {
		$edit = $this->popArr['editChiForm'];
?>


              <input type="hidden" name="txtCSeqNo" value="<?php echo $edit[0][1]?>">
			 <th><h3><?php echo  $lang_hremp_children?><h3></th>
              <tr>
                <td><?php echo $lang_hremp_name?></td>
                <td><input type="text" name="txtChiName" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?php echo $lang_hremp_dateofbirth?></td>
                <td><input type="text" name="ChiDOB" readonly value=<?php echo $edit[0][3]?>>&nbsp;<input type="button" <?php echo $locRights['edit'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
               </tr>

				  <td>
					<?php	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>

				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_Commn_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_dateofbirth; ?></strong></td>
				</tr>
<?php
	$rset = $this->popArr['empChiAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewChildren(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';

        echo '</tr>';
        }

 } ?>
          </table>
<?php } ?>