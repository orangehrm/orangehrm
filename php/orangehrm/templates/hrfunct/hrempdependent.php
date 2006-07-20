<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') { ?>

	<table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(3)" onkeypress="setUpdate(3)">
         <th><h3><?=$dependents?></h3></th>
			<tr>	  
			 <td><?=$name?></td>
			  <td><input type="text" name="txtDepName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtDepName']))?$this->postArr['txtDepName']:''?>"></td>
			</tr>	
			<tr>	  
			 <td><?=$relationship?></td>
			  <td><input type="text" name="txtRelShip" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtRelShip']))?$this->postArr['txtRelShip']:''?>"></td>
			</tr>			  
			  </table></td>
			   <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td>
      <td><table border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0" onclick="setUpdate(9)" onkeypress="setUpdate(9)">
          <th><h3><?=$children?></h3></th>
		<tr>
	   	<td><?=$name?></td>
			  <td><input type="text" name="txtChiName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtChiName']))?$this->postArr['txtChiName']:''?>"></td>
			  </tr>
		<tr>
		<td><?=$dateofbirth?></td>
			<td><input type="text" readonly name="ChiDOB" value=<?=(isset($this->postArr['ChiDOB']))?$this->postArr['ChiDOB']:''?>>&nbsp;<input type="button" <?=$locRights['add'] ? '':'disabled'?> class="button" value="" onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.ChiDOB);return false;"></td>
				</tr> 
			  </table>
    
<? } elseif(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<table height="150" border="0" cellpadding="0" cellspacing="0">
          
            <input type="hidden" name="depSTAT" value="">
<?
		if(!isset($this->getArr['DSEQ'])) {
?>
          
              <input type="hidden" name="txtDSeqNo" value="<?=$this->popArr['newDID']?>">
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
        <img border="0" title="Save" onClick="addDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
			
            ?> <td><a href="#" onmousedown="viewDependents(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?} elseif(isset($this->getArr['DSEQ'])) {
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
					        <img border="0" title="Save" onClick="editDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<? 	} else { ?>
					        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependents();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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
			
            ?> <td><a href="#" onmousedown="viewDependents(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
				



          </table></td>

          <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
        </tr>
        <tr>
          <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
          <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
        </tr>
      </table></td><td>
     <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
          <td width="514" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
        </tr>
        <tr>
          <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
          <td><table height="150" border="0" cellpadding="0" cellspacing="0">
           <input type="hidden" name="chiSTAT" value="">
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
			
            ?> <td><a href="#" onmousedown="viewChildren(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
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
			
            ?> <td><a href="#" onmousedown="viewChildren(<?=$rset[$c][1]?>)" ><?=$rset[$c][2]?></a></td> <?
            echo '<td>' . $rset[$c][3] .'</td>';
           
        echo '</tr>';
        }

 } ?>
          </table>
          
<? } ?>