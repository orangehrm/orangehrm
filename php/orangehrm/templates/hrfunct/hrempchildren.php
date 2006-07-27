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
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.childrenSTAT.value="DEL";
	qCombo(3);
}

function addChildren() {
	
	if(document.frmEmp.txtChiName.value == '') {
		alert('Field Empty');
		document.frmEmp.txtChiName.focus();
		return;
	}

	if(document.frmEmp.DOB.value == '') {
		alert('Field Empty');
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
<?  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table height="150" border="0" cellpadding="0" cellspacing="0">
           <input type="hidden" name="childrenSTAT" value="">
<?
		if(!isset($this->getArr['CHSEQ'])) {
?>
          
              <input type="hidden" name="txtCSeqNo" value="<?=$this->popArr['newCID']?>">
			   <th><h3><?=$children?></h3></th>          
              <tr>
                <td><?=$name?></td>
                <td><input name="txtChiName" <?=$locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?=$dateofbirth?></td>
				<td><input type="text" readonly name="ChiDOB">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
            </tr>
              				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
<!--<div id="tablePassport">	-->
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$dateofbirth?></strong></td>
				</tr> 
					
					<?
	$rset = $this->popArr['empChiAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewChildren(<?=$rset[$c][1]?>)"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['CHSEQ'])) {
		$edit = $this->popArr['editChiForm'];
?>

          
              <input type="hidden" name="txtCSeqNo" value="<?=$edit[0][1]?>">
			 <th><h3><?=$children?><h3></th>	 
              <tr>
                <td><?=$name?></td>
                <td><input type="text" name="txtChiName" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?=$dateofbirth?></td>
                <td><input type="text" name="ChiDOB" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" <?=$locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
               </tr>
              			  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delChildren();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$dateofbirth?></strong></td>
				</tr>
<?
	$rset = $this->popArr['empChiAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkchidel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewChildren(<?=$rset[$c][1]?>)"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
          </table>
<? } ?>