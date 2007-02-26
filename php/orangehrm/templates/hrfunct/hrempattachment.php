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
</script>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table width="352" height="200" border="0" cellpadding="0" cellspacing="0">

<?php		if(!isset($this->getArr['ATTACH'])) { ?>
          <tr>
				<td valign="top"><?php echo $lang_hremp_path?></td>
				<td><input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
					<input type="file" name="ufile"> [1M Max]</td>
              </tr>
              <tr>
              	<td><?php echo $lang_Commn_description?></td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
			  <tr>
				<td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><h3><?php echo $lang_hrEmpMain_assignattach?></h3></td>
					<td></td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(6);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<?php 	} else { ?>
		<img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php	} ?>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				<td>&nbsp;</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			<table border="0" width="450" align="center" class="tabForm">

			    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_hremp_filename?></strong></td>
						 <td><strong><?php echo $lang_hremp_size?></strong></td>
						 <td><strong><?php echo $lang_hremp_type?></strong></td>
					</tr>
<?php
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?php echo $rset[$c][2]?>" onmousedown="viewAttach('<?php echo $rset[$c][1]?>')" ><?php echo $rset[$c][3]?></a></td> <?php
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>

<?php		} elseif(isset($this->getArr['ATTACH'])) {
		$edit = $this->popArr['editAttForm'];
?>
              <input type="hidden" name="seqNO" value="<?php echo $edit[0][1]?>">
              <tr>
              	<td>Description</td>
              	<td><textarea name="txtAttDesc"><?php echo $edit[0][2]?></textarea></td>
              </tr>
              <tr>
              	<td><input type="button" value="Show File" class="buton" onclick="dwPopup()"></td>
              </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>
<?php	if($locRights['edit']) { ?>
        <img border="0" title="Save" onClick="editAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><h3><?php echo $lang_hrEmpMain_assignattach?></h3></td>
					<td></td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(6);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<?php 	} else { ?>
		<img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php	} ?>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				<td></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<table border="0" width="450" align="center" class="tabForm">

			    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_hremp_filename?></strong></td>
						 <td><strong><?php echo $lang_hremp_size?></strong></td>
						 <td><strong><?php echo $lang_hremp_type?></strong></td>
					</tr>
<?php
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?php echo $rset[$c][2]?>" onmousedown="viewAttach('<?php echo $rset[$c][1]?>')" ><?php echo $rset[$c][3]?></a></td> <?php
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>


<?php } ?>

          </table>
	<?php } ?>