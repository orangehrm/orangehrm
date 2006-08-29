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
              alert("Select atleast one check box");
              return;
        }

    document.frmEmp.conextSTAT.value="DEL";
    qCombo(2);
}


function addConExt() {
	
	if(document.frmEmp.txtEmpConExtStartDat.value == '' || document.frmEmp.txtEmpConExtEndDat.value == '') {
		alert("Enter Date");
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
		alert("Starting Day should be before ending Date");
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

<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<input type="hidden" name="conextSTAT" value="">

    <p><h3>Employee Contracts</h3></p>
<? if(isset($this -> popArr['editConExtArr'])) {
	
        $edit = $this -> popArr['editConExtArr']; 
?>
      <input type="hidden" name="txtEmpConExtID" value="<?=$this->getArr['CONEXT']?>">

      <table height="80" border="0" cellpadding="0" cellspacing="0">
      <tr>
          <td width="200">Contract Extension Start Date</td>
    	  <td><input type="text" readonly name="txtEmpConExtStartDat" value=<?=$edit[0][2]?>>&nbsp;<input class="button" type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtStartDat);return false;"></td>
	  </tr>
	  <tr> 
		<td valign="top">Contract Extension End Date</td>
		<td align="left" valign="top"> <input type="text" readonly name="txtEmpConExtEndDat" value=<?=$edit[0][3]?>>&nbsp;<input type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtEndDat);return false;"></td>
	  </tr>
	  <tr> 
		<td valign="top"></td>
		<td align="left" valign="top"> 
		<?			if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
		<?			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_save.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
		</td>
	  </tr>
	</table>	  
<? } else { ?>
         <input type="hidden" name="txtEmpConExtID"  value="<?=$this->popArr['newConExtID']?>">

      <table height="80" border="0" cellpadding="0" cellspacing="0">
         <tr>
          <td width="200">Contract Extension Start Date</td>
		  <td><input type="text" readonly name="txtEmpConExtStartDat">&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtStartDat);return false;"></td>
		</tr>
  	  <tr> 
		<td valign="top">Contract Extension End Date</td>
		<td align="left" valign="top"> <input type="text" readonly name="txtEmpConExtEndDat">&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpConExtEndDat);return false;"></td>
	  </tr>
	  <tr> 
		<td valign="top"></td>
		<td align="left" valign="top">
			<?	if($locRights['add']) { ?>
			        <img border="0" title="Save" onClick="addConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
			<? 	} else { ?>
			        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
			<?	} ?>
		</td>
	  </tr>
	  </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h4>Assigned Contracts</h4></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
                    <tr>
                      	<td></td>
						 <td><strong>Contract Extension ID</strong></td>
						 <td><strong>Contract Start Date</strong></td>
						 <td><strong>Contract End Date</strong></td>
					</tr>
<?
$rset = $this->popArr['rsetConExt'];


    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkconextdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" onmousedown="viewConExt(<?=$rset[$c][1]?>)" ><?=$rset[$c][1]?></a></td> <?
            $dtfield = explode(" ",$rset[$c][2]);
            echo '<td>' . $dtfield[0] .'</td>';
            $dtfield = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtfield[0] .'</td>';
                 echo '</tr>';
        }

?>
</table>
<? } ?>
