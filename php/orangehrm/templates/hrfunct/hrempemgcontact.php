<script language="JavaScript">
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

function addEContact() {

	if(document.frmEmp.txtEConName.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty?>');
		document.frmEmp.txtEConName.focus();
		return;
	}

	if(document.frmEmp.txtEConRel.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty?>');
		document.frmEmp.txtEConRel.focus();
		return;
	}

	if(document.frmEmp.txtEConHmTel.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty?>');
		document.frmEmp.txtEConHmTel.focus();
		return;
	}

	document.frmEmp.econtactSTAT.value="ADD";
	qCombo(5);
}

function viewEContact(ecSeq) {
	document.frmEmp.action=document.frmEmp.action + "&ECSEQ=" + ecSeq ;
	document.frmEmp.pane.value=5;
	document.frmEmp.submit();
}

function editEContact() {
	document.frmEmp.econtactSTAT.value="EDIT";
	qCombo(5);
}

</script>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>


	<table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="econtactSTAT" value="">
<?php
		if(!isset($this->getArr['ECSEQ'])) {
?>
            <input type="hidden" name="txtECSeqNo" value="<?php echo $this->popArr['newECID']?>">
			 <tr>
			 <td><font color=#ff0000>*</font><?php echo $lang_hremp_name; ?>&nbsp;&nbsp;</td>
			  <td><input name="txtEConName" <?php echo $locRights['add'] ? '':''?> type="text"></td>
			 <td width="50">&nbsp;</td>
			<td><font color=#ff0000>*</font><?php echo $lang_hremp_relationship; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConRel" <?php echo $locRights['add'] ? '':''?> type="text"></td>
			 </tr>
			 <tr>
			 <td><font color=#ff0000>*</font><?php echo $lang_hremp_hmtele; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConHmTel" <?php echo $locRights['add'] ? '':''?> type="text"></td>
			 <td width="50">&nbsp;</td>
			 <td><?php echo $lang_hremp_mobile; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConMobile" <?php echo $locRights['add'] ? '':''?> type="text"></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_worktele; ?>&nbsp;&nbsp;</td>
			 <td><input name="txtEConWorkTel" <?php echo $locRights['add'] ? '':''?> type="text"></td>
			  </tr>
				  <td>
<?php	if (($locRights['add']) || ($_GET['reqcode'] === "ESS")) { ?>
        <img border="0" title="Save" onClick="addEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if (($locRights['delete']) || ($_GET['reqcode'] === "ESS"))  { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>

				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_relationship; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_hmtele; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_mobile; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_worktele; ?></strong></td>
					</tr>

					<?php
	$rset = $this->popArr['empECAss'];

	for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewEContact('<?php echo $rset[$c][1]?>')"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';

        echo '</tr>';
        }?>

	<?php } elseif(isset($this->getArr['ECSEQ'])) {
		$edit = $this->popArr['editECForm'];

?>

          <tr>
              <input type="hidden" name="txtECSeqNo" value="<?php echo $edit[0][1]?>">

			 <td><font color=#ff0000>*</font><?php echo $lang_hremp_name; ?></td>
			 <td><input type="text" name="txtEConName" value="<?php echo $edit[0][2]?>"></td>
			 <td width="50">&nbsp;</td>
			<td><font color=#ff0000>*</font><?php echo $lang_hremp_relationship; ?></td>
			 <td><input type="text" name="txtEConRel" value="<?php echo $edit[0][3]?>"></td>
			 </tr>
			 <tr>
			 <td><font color=#ff0000>*</font><?php echo $lang_hremp_hmtele; ?></td>
			 <td><input type="text"  name="txtEConHmTel" value="<?php echo $edit[0][4]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?php echo $lang_hremp_mobile; ?></td>
			 <td><input type="text" name="txtEConMobile" value="<?php echo $edit[0][5]?>"></td>
			 </tr>
			 <tr>
			 <td><?php echo $lang_hremp_worktele; ?></td>
			 <td><input type="text" name="txtEConWorkTel" value="<?php echo $edit[0][6]?>"></td>
			 </tr>


				  <td>
					<?php	if (($locRights['edit']) || ($_GET['reqcode'] === "ESS")){ ?>
					        <img border="0" title="Save" onClick="editEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php 	} else { ?>
					        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if (($locRights['delete']) || ($_GET['reqcode'] === "ESS"))  { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>

				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_relationship; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_hmtele; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_mobile; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_worktele; ?></strong></td>
					</tr>
<?php
	$rset = $this->popArr['empECAss'];
//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW(count($rset).'hhh');
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewEContact('<?php echo $rset[$c][1]?>')"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';

        echo '</tr>';
        }

 } ?>
		</table>

<?php } ?>