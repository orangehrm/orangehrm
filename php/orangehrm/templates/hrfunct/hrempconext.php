<script language="JavaScript">

function delConExt() {

      var check = false;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkconextdel[]') && (elements[i].checked == true)) {
					check = true;
				}
			}
        }

        if(!check) {
              alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
              return;
        }

    document.frmEmp.conextSTAT.value="DEL";
    qCombo(2);
}


function addConExt() {

	if(document.frmEmp.txtEmpConExtStartDat.value == '' || document.frmEmp.txtEmpConExtEndDat.value == '') {
		alert("<?php echo $lang_Error_EnterDate; ?>");
		return;
	}

	startDate = createDate(document.frmEmp.txtEmpConExtStartDat.value);
	endDate = createDate(document.frmEmp.txtEmpConExtEndDat.value);

	if(startDate >= endDate) {
		alert("Starting Day should be before ending Date");
		return;
	}

  document.frmEmp.conextSTAT.value="ADD";
  qCombo(2);
}

function editConExt() {

	startDate = createDate(document.frmEmp.txtEmpConExtStartDat.value);
	endDate = createDate(document.frmEmp.txtEmpConExtEndDat.value);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>");
		return;
	}

  document.frmEmp.conextSTAT.value="EDIT";
  qCombo(2);
}

function viewConExt(pSeq) {
	document.frmEmp.action = document.frmEmp.action + "&CONEXT=" + pSeq ;
	document.frmEmp.pane.value = 2;
	document.frmEmp.submit();
}
</script>

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<input type="hidden" name="conextSTAT" value="">

    <p><h3><?php echo $lang_hremp_EmployeeContracts; ?></h3></p>
<?php if(isset($this -> popArr['editConExtArr'])) {

        $edit = $this -> popArr['editConExtArr'];
?>
      <input type="hidden" name="txtEmpConExtID" value="<?php echo $this->getArr['CONEXT']?>">

      <table height="80" border="0" cellpadding="0" cellspacing="0">
      <tr>
          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?></td>
    	  <td><input type="text" readonly name="txtEmpConExtStartDat" value=<?php echo $edit[0][2]?>>&nbsp;<input class="button" type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtStartDat);return false;"></td>
	  </tr>
	  <tr>
		<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
		<td align="left" valign="top"> <input type="text" readonly name="txtEmpConExtEndDat" value=<?php echo $edit[0][3]?>>&nbsp;<input type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtEndDat);return false;"></td>
	  </tr>
	  <tr>
		<td valign="top"></td>
		<td align="left" valign="top">
		<?php			if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
		<?php			}  ?>
		</td>
	  </tr>
	</table>
<?php } else { ?>
         <input type="hidden" name="txtEmpConExtID"  value="<?php echo $this->popArr['newConExtID']?>">

      <table height="80" border="0" cellpadding="0" cellspacing="0">
         <tr>
          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?></td>
		  <td><input type="text" readonly value="0000-00-00" name="txtEmpConExtStartDat">&nbsp;<input class="button" <?php echo $locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtStartDat);return false;"></td>
		</tr>
  	  <tr>
		<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
		<td align="left" valign="top"> <input type="text" readonly value="0000-00-00" name="txtEmpConExtEndDat">&nbsp;<input class="button" <?php echo $locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtEndDat);return false;"></td>
	  </tr>
	  <tr>
		<td valign="top"></td>
		<td align="left" valign="top">
			<?php	if($locRights['add']) { ?>
			        <img border="0" title="Save" onClick="addConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
			<?php	} ?>
		</td>
	  </tr>
	  </table>
<?php } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

<?php

    $rset = $this->popArr['rsetConExt'];

    // check if there are any defined memberships
    if( $rset && count($rset) > 0 ){
        $assignedContracts = true;
    } else {
        $assignedContracts = false;
    }
?>
<?php
if ($rset != Null){ ?>
    <td width='100%'><h3><?php echo $lang_hremp_AssignedContracts; ?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
<?php } ?>
<?php if( !$assignedContracts ){ ?>
  <!-- <tr>
    <td width='100%'><h5><?php /*echo $lang_empview_norecorddisplay */?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr> -->

<?php
     } else {
?>

  <tr>
  <td>

  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
<?php
if ($rset != Null){ ?>
                    <tr>
                      	<td></td>
						 <td><strong><?php echo $lang_hremp_ContractExtensionId; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_ContractStartDate; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_ContractEndDate; ?></strong></td>
					</tr>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>

<?php }


    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkconextdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" onmousedown="viewConExt(<?php echo $rset[$c][1]?>)" ><?php echo $rset[$c][1]?></a></td> <?php
            $dtfield = explode(" ",$rset[$c][2]);
            echo '<td>' . $dtfield[0] .'</td>';
            $dtfield = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtfield[0] .'</td>';
                 echo '</tr>';
        }

?>
<?php } //if( $assignedContracts ) ?>
</table>
<?php } ?>
