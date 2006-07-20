<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

	<table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(4)" onkeypress="setUpdate(4)">
         	<tr>	  
			 <td><?=$name?></td>
			  <td><input type="text" name="txtEConName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEConName']))?$this->postArr['txtEConName']:''?>"></td>
			<td width="50">&nbsp;</td>  
			 <td><?=$relationship?></td>
			  <td><input type="text" name="txtEConRel" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEConRel']))?$this->postArr['txtEConRel']:''?>"></td>
			</tr>
			<tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConHmTel" value="<?=(isset($this->postArr['txtEConHmTel']))?$this->postArr['txtEConHmTel']:''?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$mobile?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConMobile" value="<?=(isset($this->postArr['txtEConMobile']))?$this->postArr['txtEConMobile']:''?>"></td>
			 </tr>
			 <tr>
			<td><?=$worktele?></td>
			 <td><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtEConWorkTel" value="<?=(isset($this->postArr['txtEConWorkTel']))?$this->postArr['txtEConWorkTel']:''?>"></td>
			</tr>			  
			  </table>
			  
<? } if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>


	<table height="200" border="0" cellpadding="0" cellspacing="0">

          <input type="hidden" name="econtactSTAT" value="">
<?
		if(!isset($this->getArr['ECSEQ'])) {
?>
            <input type="hidden" name="txtECSeqNo" value="<?=$this->popArr['newECID']?>">
			 <tr>
			 <td><?=$name?></td>
			  <td><input name="txtEConName" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$relationship?></td>
			 <td><input name="txtEConRel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input name="txtEConHmTel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$mobile?></td>
			 <td><input name="txtEConMobile" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input name="txtEConWorkTel" <?=$locRights['add'] ? '':'disabled'?> type="text"></td>
			  </tr>
				  <td>
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
	
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
						 <td><strong><?=$hmtele?></strong></td>
						 <td><strong><?=$mobile?></strong></td>
						 <td><strong><?=$worktele?></strong></td>
					</tr> 
					
					<?
	$rset = $this->popArr['empECAss'];

	for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewEContact('<?=$rset[$c][1]?>')"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
           
        echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['ECSEQ'])) {
		$edit = $this->popArr['editECForm'];
		
?>

          <tr>
              <input type="hidden" name="txtECSeqNo" value="<?=$edit[0][1]?>">
			  
			 <td><?=$name?></td>
			 <td><input type="text" name="txtEConName" value="<?=$edit[0][2]?>"></td>
			 <td width="50">&nbsp;</td>
			<td><?=$relationship?></td>
			 <td><input type="text" name="txtEConRel" value="<?=$edit[0][3]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$hmtele?></td>
			 <td><input type="text"  name="txtEConHmTel" value="<?=$edit[0][4]?>"></td>
			 <td width="50">&nbsp;</td>
			 <td><?=$mobile?></td>
			 <td><input type="text" name="txtEConMobile" value="<?=$edit[0][5]?>"></td>
			 </tr>
			 <tr>
			 <td><?=$worktele?></td>
			 <td><input type="text" name="txtEConWorkTel" value="<?=$edit[0][6]?>"></td>
			 </tr>
			
				  
				  <td>
					<?	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEContact();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
				</td>
				</tr>
				
				<table width="550" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?=$name?></strong></td>
						 <td><strong><?=$relationship?></strong></td>
						 <td><strong><?=$hmtele?></strong></td>
						 <td><strong><?=$mobile?></strong></td>
						 <td><strong><?=$worktele?></strong></td>
					</tr>
<?
	$rset = $this->popArr['empECAss'];
//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW(count($rset).'hhh');
    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $rset[$c][1] ."'></td>";
			
            ?> <td><a href="javascript:viewEContact('<?=$rset[$c][1]?>')"><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
            echo '<td>' . $rset[$c][5] .'</td>';
            echo '<td>' . $rset[$c][6] .'</td>';
            
        echo '</tr>';
        }

 } ?>
		</table>
		
<? } ?>