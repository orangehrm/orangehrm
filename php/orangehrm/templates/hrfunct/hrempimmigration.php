<script language="JavaScript">
function delPassport() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkpassportdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

	document.frmEmp.passportSTAT.value="DEL";
	qCombo(10);
}

function addPassport() {

	if(document.frmEmp.txtPPNo.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}

	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPExpDat.focus();
		return;
	}

	startDate = createDate(document.frmEmp.txtPPIssDat.value);
	endDate = createDate(document.frmEmp.txtPPExpDat.value);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_IssedDateShouldBeBeforeExp; ?>");
		return;
	}

	document.frmEmp.passportSTAT.value="ADD";
	qCombo(10);
}

function viewPassport(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&PPSEQ=" + pSeq ;
	document.frmEmp.pane.value = 10;
	document.frmEmp.submit();
}

function editPassport() {

	if(document.frmEmp.txtPPNo.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}

	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPExpDat.focus();
		return;
	}

	startDate = createDate(document.frmEmp.txtPPIssDat.value);
	endDate = createDate(document.frmEmp.txtPPExpDat.value);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_IssedDateShouldBeBeforeExp; ?>");
		return;
	}

	document.frmEmp.passportSTAT.value="EDIT";
	qCombo(10);
}

</script>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>


	<table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="passportSTAT" value="">
<?php
		if(!isset($this->getArr['PPSEQ'])) {
?>
          <tr >
              <input type="hidden" name="txtPPSeqNo" value="<?php echo $this->popArr['newPPID']?>">
			  <td nowrap><?php echo $lang_hremp_passport; ?>&nbsp;&nbsp;<input type="radio" <?php echo $locRights['add'] ? '':'disabled'?> checked name="PPType" value="1">&nbsp;&nbsp;</td>
			  <td nowrap><?php echo $lang_hremp_visa; ?>&nbsp;&nbsp;<input type="radio" <?php echo $locRights['add'] ? '':'disabled'?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td><?php echo $lang_hremp_citizenship; ?>&nbsp;&nbsp;</td>
                <td><select <?php echo $locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0">-- <?php echo $lang_districtinformation_selectcounlist ?> --</option>
<?php				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>
				</td>
		    </tr>
              <tr nowrap>
                <td><?php echo $lang_hremp_passvisano; ?>&nbsp;&nbsp;</td>
                <td><input type="text" <?php echo $locRights['add'] ? '':'disabled'?> name="txtPPNo"></td>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_issueddate; ?>&nbsp;&nbsp;</td>
                <td><input type="text" readonly name="txtPPIssDat" value="0000-00-00">&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr nowrap>
                <td><?php echo $lang_hremp_i9status; ?></td>
                <td><input name="txtI9status" <?php echo $locRights['add'] ? '':'disabled'?> type="text">
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_dateofexp; ?></td>
                <td><input type="text" readonly value="0000-00-00" name="txtPPExpDat">&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr nowrap>
               <td><?php echo $lang_hremp_i9reviewdate; ?></td>
                <td nowrap><input type="text" readonly value="0000-00-00" name="txtI9ReviewDat">&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?php echo $lang_Leave_Common_Comments; ?></td>
				<td><textarea <?php echo $locRights['add'] ? '':'disabled'?> name="txtComments"></textarea></td>
				</tr>

				  <td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>
<div id="tablePassport">
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo "$lang_hremp_passport/$lang_hremp_visa"; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_passvisano?></strong></td>
						 <td><strong><?php echo $lang_hremp_citizenship?></strong></td>
						 <td><strong><?php echo $lang_hremp_issueddate; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_dateofexp; ?></strong></td>
					</tr>

					<?php
	$rset = $this->popArr['empPPAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?php echo $rset[$c][1]?>)" ><?php echo $fname?></a></td> <?php
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }?>
</div>
<?php } elseif(isset($this->getArr['PPSEQ'])) {
		$edit = $this->popArr['editPPForm'];
?>

          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?php echo $edit[0][1]?>">
			  <td><?php echo $lang_hremp_passport; ?> <input type="radio" checked <?php echo $locRights['edit'] ? '':'disabled'?> name="PPType" value="1"></td><td><?php echo $visa?><input type="radio" <?php echo $locRights['edit'] ? '':'disabled'?> name="PPType" <?php echo ($edit[0][6]=='2')?'checked':''?> value="2"></td>
			  <td width="50">&nbsp;</td>
		  	 <td><?php echo $lang_hremp_citizenshipl; ?></td>
                <td><select <?php echo $locRights['edit'] ? '':'disabled'?> name="cmbPPCountry">
                <option value="0">-- <?php echo $lang_districtinformation_selectcounlist ?> --</option>
<?php				$list = $this->popArr['ppcntlist'];
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>
				</td>
			  </tr>
              <tr>
                <td><?php echo $lang_hremp_passvisano; ?></td>
                <td><input type="text" name="txtPPNo" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][2]?>"></td>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_issueddate; ?></td>
                <td><input type="text" name="txtPPIssDat" readonly value=<?php echo $edit[0][3]?>>&nbsp;<input type="button" <?php echo $locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td><?php echo $lang_hremp_i9status; ?></td>
                <td><input name="txtI9status" type="text" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][7]?>">
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_dateofexp; ?></td>
                <td><input type="text" name="txtPPExpDat" readonly value=<?php echo $edit[0][4]?>>&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
               <td><?php echo $lang_hremp_i9reviewdate; ?></td>
                <td><input type="text" name="txtI9ReviewDat" readonly value=<?php echo $edit[0][8]?>>&nbsp;<input type="button" <?php echo $locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?php echo $lang_Leave_Common_Comments; ?></td>
				<td><textarea <?php echo $locRights['edit'] ? '':'disabled'?> name="txtComments"><?php echo $edit[0][5]?></textarea></td>
				</tr>

				  <td>
					<?php	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php 	} else { ?>
					        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>

				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo "$lang_hremp_passport/$lang_hremp_visa"; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_passvisano?></strong></td>
						 <td><strong><?php echo $lang_hremp_citizenship?></strong></td>
						 <td><strong><?php echo $lang_hremp_issueddate; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_dateofexp; ?></strong></td>
					</tr>
<?php
	$rset = $this->popArr['empPPAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?php echo $rset[$c][1]?>)" ><?php echo $fname?></a></td> <?php
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }

 } ?>
		</table>

<?php } ?>

