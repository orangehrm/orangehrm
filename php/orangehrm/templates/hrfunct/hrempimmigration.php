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
		alert('Select at least one record to Delete')
		return;
	}
	
	document.frmEmp.passportSTAT.value="DEL";
	qCombo(10);
}

function addPassport() {
	
	if(document.frmEmp.txtPPNo.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}
	
	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('Field Empty');
		document.frmEmp.txtPPExpDat.focus();
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
	document.frmEmp.passportSTAT.value="EDIT";
	qCombo(10);
}

</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>


	<table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="passportSTAT" value="">
<?
		if(!isset($this->getArr['PPSEQ'])) {
?>
          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$this->popArr['newPPID']?>">
			  <td><?=$passport?> <input type="radio" <?=$locRights['add'] ? '':'disabled'?> checked name="PPType" value="1"></td><td><?=$visa?><input type="radio" <?=$locRights['add'] ? '':'disabled'?> name="PPType" value="2"></td>
			  <td width="50">&nbsp;</td>
		  	  <td><?=$citizenship?></td>
                <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbPPCountry">
                		<option value="0"><?=$selectcountry?></option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td> 
		    </tr>
              <tr>
                <td><?=$passvisano?></td>
                <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtPPNo"></td>
                <td width="50">&nbsp;</td>
                <td><?=$issueddate?></td>
                <td><input type="text" readonly name="txtPPIssDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td><?=$i9status?></td>
                <td><input name="txtI9status" <?=$locRights['add'] ? '':'disabled'?> type="text">
                <td width="50">&nbsp;</td>
                <td><?=$dateofexp?></td>
                <td><input type="text" readonly name="txtPPExpDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
               <td><?=$i9reviewdate?></td>
                <td><input type="text" readonly name="txtI9ReviewDat">&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?=$comments?></td>
				<td><textarea <?=$locRights['add'] ? '':'disabled'?> name="txtComments"></textarea></td>
				</tr>
				
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
<div id="tablePassport">	
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$passport?>/<?=$visa?></strong></td>
						 <td><strong><?=$passvisano?></strong></td>
						 <td><strong><?=$citizenship?></strong></td>
						 <td><strong><?=$issueddate?></strong></td>
						 <td><strong><?=$dateofexp?></strong></td>
					</tr> 
					
					<?
	$rset = $this->popArr['empPPAss'];
		
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][9] .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtPrint[0] .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . $dtPrint[0] .'</td>';
        echo '</tr>';
        }?>
</div>
	<?} elseif(isset($this->getArr['PPSEQ'])) {
		$edit = $this->popArr['editPPForm'];
?>

          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?=$edit[0][1]?>">
			  <td><?=$passport?> <input type="radio" checked <?=$locRights['edit'] ? '':'disabled'?> name="PPType" value="1"></td><td><?=$visa?><input type="radio" <?=$locRights['edit'] ? '':'disabled'?> name="PPType" <?=($edit[0][6]=='2')?'checked':''?> value="2"></td>
			  <td width="50">&nbsp;</td>
		  	 <td><?=$citizenship?></td>
                <td><select <?=$locRights['edit'] ? '':'disabled'?> name="cmbPPCountry">
                <option value="0"><?=$selectcountry?></option>
<?				$list = $this->popArr['ppcntlist'];
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
				</td>            
			  </tr>
              <tr>
                <td><?=$passvisano?></td>
                <td><input type="text" name="txtPPNo" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][2]?>"></td>
                <td width="50">&nbsp;</td>
                <td><?=$issueddate?></td>
                <td><input type="text" name="txtPPIssDat" readonly value=<?=$edit[0][3]?>>&nbsp;<input type="button" <?=$locRights['edit'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPIssDat);return false;"></td>
              </tr>
              <tr>
                <td><?=$i9status?></td>
                <td><input name="txtI9status" type="text" <?=$locRights['edit'] ? '':'disabled'?> value="<?=$edit[0][7]?>">
                <td width="50">&nbsp;</td>
                <td><?=$dateofexp?></td>
                <td><input type="text" name="txtPPExpDat" readonly value=<?=$edit[0][4]?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtPPExpDat);return false;"></td>
              </tr>
              <tr>
               <td><?=$i9reviewdate?></td>
                <td><input type="text" name="txtI9ReviewDat" readonly value=<?=$edit[0][8]?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtI9ReviewDat);return false;"></td>
				<td width="50">&nbsp;</td>
				<td><?=$comments?></td>
				<td><textarea <?=$locRights['edit'] ? '':'disabled'?> name="txtComments"><?=$edit[0][5]?></textarea></td>
				</tr>
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delPassport();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$passport?>/<?=$visa?></strong></td>
						 <td><strong><?=$passvisano?></strong></td>
						 <td><strong><?=$citizenship?></strong></td>
						 <td><strong><?=$issueddate?></strong></td>
						 <td><strong><?=$dateofexp?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empPPAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?=$rset[$c][1]?>)" ><?=$fname?></a></td> <?
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
		
<? } ?>
   
