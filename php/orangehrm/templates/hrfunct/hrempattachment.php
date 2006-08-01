<script language="JavaScript">

function dwPopup() {
        var popup=window.open('../../templates/hrfunct/download.php?id=<?=isset($this->getArr['id']) ? $this->getArr['id'] : '' ?>&ATTACH=<?=isset($this->getArr['ATTACH']) ? $this->getArr['ATTACH'] : '' ?>','Downloads');
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
		alert('Select at least one Attachment to Delete')
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
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	
	<table width="352" height="200" border="0" cellpadding="0" cellspacing="0">
		
<?		if(!isset($this->getArr['ATTACH'])) { ?>
          <tr>
				<td><?=$path?></td>
				<td><input type="file" name="ufile" ></td>
              </tr>
              <tr>
              	<td><?=$description?></td>
              	<td><textarea name="txtAttDesc"></textarea></td>
              </tr>
			  <tr>
				<td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><h3><?=$assignattach?></h3></td>
					<td></td>
				</tr>
				<tr>
				<td>
<?	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(6);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<? 	} else { ?>
		<img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?	} ?>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td>&nbsp;</td>
				</tr>				
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong><?=$filename?></strong></td>
						 <td><strong><?=$size?></strong></td>
						 <td><strong><?=$type?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>
              
<?		} elseif(isset($this->getArr['ATTACH'])) {
		$edit = $this->popArr['editAttForm'];
?>
              <input type="hidden" name="seqNO" value="<?=$edit[0][1]?>">
              <tr>
              	<td>Description</td>
              	<td><textarea name="txtAttDesc"><?=$edit[0][2]?></textarea></td>
              </tr>
              <tr>
              	<td><input type="button" value="Show File" class="buton" onclick="dwPopup()"></td>
              </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>
<?	if($locRights['edit']) { ?>
        <img border="0" title="Save" onClick="editAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><h3><?=$assignattach?></h3></td>
					<td></td>
				</tr>
				<tr>
				<td>
<?	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(6);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<? 	} else { ?>
		<img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?	} ?>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delAttach();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				<td></td>
			</tr>	
			<table border="0" width="450" align="center" class="tabForm">
			
			    <tr>
                      	<td></td>
						 <td><strong><?=$filename?></strong></td>
						 <td><strong><?=$size?></strong></td>
						 <td><strong><?=$type?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empAttAss'] ;

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkattdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" title="<?=$rset[$c][2]?>" onmousedown="viewAttach('<?=$rset[$c][1]?>')" ><?=$rset[$c][3]?></a></td> <?
            echo '<td>' . $rset[$c][4] .'byte(s)</td>';     
            echo '<td>' . $rset[$c][6] .'</td>';
        echo '</tr>';
        }
?>


<? } ?>
              
          </table>
	<? } ?>