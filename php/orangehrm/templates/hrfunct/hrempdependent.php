<script language="JavaScript">
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
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.dependentSTAT.value="DEL";
	qCombo(3);
}

function addDependent() {
	document.frmEmp.dependentSTAT.value="ADD";
	qCombo(3);
}

function viewDependent(pSeq) {
	document.frmEmp.action=document.frmEmp.action + "&depSEQ=" + pSeq ;
	document.frmEmp.pane.value=3;
	document.frmEmp.submit();
}

function editDependent() {
	document.frmEmp.dependentSTAT.value="EDIT";
	qCombo(3);
}

</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<table height="150" border="0" cellpadding="0" cellspacing="0">
          
            <input type="hidden" name="dependentSTAT">
<?
		if(!isset($this->getArr['depSEQ'])) {
?>
          
              <input type="hidden" name="txtDSeqNo" value="<?=$this->popArr['newDepID']?>">
			   <th><h3><?=$dependents?></h3></th>          

              <tr>

                <td><?=$name?></td>
                <td><input name="txtDepName" <?=$locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?=$relationship?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtRelShip"></td>
              </tr>
              				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
						 <td><strong><?=$relationship?></strong></td>
				</tr> 
					
					<?
	$rset = $this->popArr['empDepAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewDependent(<?=$rset[$c][1]?>)"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['depSEQ'])) {
		$edit = $this->popArr['editDepForm'];
?>

          
              <input type="hidden" name="txtDSeqNo" value="<?=$edit[0][1]?>">
			 <th><h3><?=$dependents?></h3></th>	 
              <tr>
                <td><?=$name?></td>
                <td><input type="text" name="txtDepName" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?=$relationship?></td>
                <td><input name="txtRelShip" type="text" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][3]?>">
               </tr>
              
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
				</tr>
<?
	$rset = $this->popArr['empDepAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewDependent(<?=$rset[$c][1]?>)"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
</table>
<? } ?>