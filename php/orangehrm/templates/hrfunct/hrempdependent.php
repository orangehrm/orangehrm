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
		alert('<?php echo $lang_Common_SelectDelete; ?>')
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
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<table height="150" border="0" cellpadding="0" cellspacing="0">

            <input type="hidden" name="dependentSTAT">
<?php
		if(!isset($this->getArr['depSEQ'])) {
?>

              <input type="hidden" name="txtDSeqNo" value="<?php echo $this->popArr['newDepID']?>">
			   <th><h3><?php echo $lang_hremp_dependents; ?></h3></th>

              <tr>

                <td><?php echo $lang_hremp_name; ?></td>
                <td><input name="txtDepName" <?php echo $locRights['add'] ? '':'disabled'?> type="text">
                </tr>
                <tr>
                <td><?php echo $lang_hremp_relationship ; ?></td>
                <td><input type="text" <?php echo $locRights['add'] ? '':'disabled'?> name="txtRelShip"></td>
              </tr>

				  <td>
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>
<!--<div id="tablePassport">	-->
				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_relationship; ?></strong></td>
				</tr>

					<?php
	$rset = $this->popArr['empDepAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewDependent(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '</tr>';
        }?>

	<?php } elseif(isset($this->getArr['depSEQ'])) {
		$edit = $this->popArr['editDepForm'];
?>


              <input type="hidden" name="txtDSeqNo" value="<?php echo $edit[0][1]?>">
			 <th><h3><?php echo $lang_hremp_dependents; ?></h3></th>
              <tr>
                <td><?php echo $lang_hremp_name; ?></td>
                <td><input type="text" name="txtDepName" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][2]?>"></td>
               </tr>
              <tr>
                <td><?php echo $lang_hremp_relationship; ?></td>
                <td><input name="txtRelShip" type="text" <?php echo $locRights['edit'] ? '':'disabled'?> value="<?php echo $edit[0][3]?>">
               </tr>


				  <td>
					<?php	if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php 	} else { ?>
					        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
					<?php	} ?>
				  </td>
				</tr>
				<tr>
				<td>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delDependent();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
				</td>
				</tr>

				<table width="275" align="center" border="0" class="tabForm">
				 <tr>
                      	<td width="50">&nbsp;</td>
						 <td><strong><?php echo $lang_hremp_name; ?></strong></td>
						 <td><strong><?php echo $lang_hremp_relationship; ?></strong></td>
				</tr>
<?php
	$rset = $this->popArr['empDepAss'];

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdepdel[]' value='" . $rset[$c][1] ."'></td>";

            ?> <td><a href="javascript:viewDependent(<?php echo $rset[$c][1]?>)"><?php echo $rset[$c][2]?></a></td> <?php
            echo '<td>' . $rset[$c][3] .'</td>';

        echo '</tr>';
        }

 } ?>
</table>
<?php } ?>