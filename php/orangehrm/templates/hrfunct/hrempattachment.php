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

function dwPopup() {
        var popup=window.open('../../templates/hrfunct/download.php?id=<?php echo isset($this->getArr['id']) ? $this->getArr['id'] : ''?>&ATTACH=<?php echo isset($this->getArr['ATTACH']) ? $this->getArr['ATTACH'] : ''?>','Downloads');
        if(!popup.opener) popup.opener=self;
}

function delAttach() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkattdel[]') && (elements[i].checked == true)){
				check = true;
			}
		}
	}

	if(!check){
		alert('<?php echo $lang_hremp_SelectAtLEastOneAttachment; ?>')
		return;
	}

	document.frmEmp.attSTAT.value="DEL";
	qCombo(6);
}

function addAttach() {
	var fileName = document.frmEmp.ufile.value;
	fileName = trim(fileName);
	if (fileName == "") {
		alert("<?php echo $lang_hremp_PleaseSelectFile; ?>");
		return;
	}
	document.frmEmp.attSTAT.value="ADD";
	qCombo(6);
}

function viewAttach(att) {
	document.frmEmp.action=document.frmEmp.action + "&ATTACH=" + att;
	document.frmEmp.pane.value=6;
	document.frmEmp.submit();
}

function editAttach() {
	document.frmEmp.attSTAT.value="EDIT";
	qCombo(6);
}

<?php
	if(isset($_GET['ATT_UPLOAD']) && $_GET['ATT_UPLOAD'] == 'FAILED')
		echo "alert('" .$lang_lang_uploadfailed."');";
?>
</script>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table width="352" height="50" border="0" cellpadding="0" cellspacing="0">

<?php		if(!isset($this->getArr['ATTACH']) && $locRights['add']) { ?>
          <tr>
				<td valign="top"><?php echo $lang_hremp_path?></td>
				<td><input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
					<input type="file" name="ufile"> <br>[<?php echo $lang_hremp_largefileignore?>]</td>
              </tr>
			  <tr><td>&nbsp;</td></tr>
              <tr>
              	<td><?php echo $lang_Commn_description?></td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>

        <img border="0" title="<?php echo $lang_hremp_Save; ?>" onClick="addAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">

				</td>
				</tr>

<?php		} elseif(isset($this->getArr['ATTACH'])) {
				$edit = $this->popArr['editAttForm'];
		 		$disabled = ($locRights['edit']) ? "" : "disabled";
?>
              <input type="hidden" name="seqNO" value="<?php echo $edit[0][1]?>">
              <tr>
              	<td><?php echo $lang_hremp_filename?></td>
              	<td><?php echo $edit[0][3];?></td>
              </tr>
			  <tr><td>&nbsp;</td></tr>
              <tr>
              	<td><?php echo $lang_Commn_description?></td>
              	<td><textarea name="txtAttDesc" <?php echo $disabled; ?> ><?php echo $edit[0][2]?></textarea></td>
              </tr>
              <tr>
              	<td><input type="button" value="<?php echo $lang_hremp_ShowFile; ?>"
              		class="button" onclick="dwPopup()"></td>
              </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>
<?php	if($locRights['edit']) { ?>
        <img border="0" title="<?php echo $lang_hremp_Save; ?>" onClick="editAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				</td>
				</tr>

<?php } ?>

<?php
		$rset = $this->popArr['empAttAss'] ;
		if ($rset != Null){ ?>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td nowrap="nowrap"><h3><?php echo $lang_hrEmpMain_assignattach?></h3></td>
					<td></td>
				</tr>
<?php	if($locRights['add'] || $locRights['delete']) { ?>
			<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="<?php echo $lang_hremp_Delete; ?>" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				<td>&nbsp;</td>
				</tr>
<?php 	} ?>
				<!-- <tr><td>&nbsp;</td></tr> -->
			<table border="0" width="450" align="center" class="tabForm">
			    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_hremp_filename?></strong></td>
						 <td><strong><?php echo $lang_hremp_size?></strong></td>
						 <td><strong><?php echo $lang_hremp_type?></strong></td>
					</tr>
<?php }

	$disabled = ($locRights['delete']) ? "" : "disabled";
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' $disabled class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?php echo $rset[$c][2]?>" onmousedown="viewAttach('<?php echo $rset[$c][1]?>')" ><?php echo $rset[$c][3]?></a></td> <?php
            echo '<td>' . $rset[$c][4] .' byte(s)</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>

          </table>
	<?php } ?>