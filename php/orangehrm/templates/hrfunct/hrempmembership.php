<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>

<script language="JavaScript">
function editMembership() {
	if(document.EditMembership.title=='Save') {
		editEXTMembership();
		return;
	}

	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.EditMembership.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditMembership.title="Save";
}

function moutMembership() {
	if(document.EditMembership.title=='Save')
		document.EditMembership.src='../../themes/beyondT/pictures/btn_save.jpg';
	else
		document.EditMembership.src='../../themes/beyondT/pictures/btn_edit.jpg';
}

function moverMembership() {
	if(document.EditMembership.title=='Save')
		document.EditMembership.src='../../themes/beyondT/pictures/btn_save_02.jpg';
	else
		document.EditMembership.src='../../themes/beyondT/pictures/btn_edit_02.jpg';
}

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));

		var tempDate = new Date(yy,mm,dd);

		return tempDate;
}

function goBack() {
		location.href = "./CentralController.php?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN";
	}

function addEXTMembership() {

	if(document.frmEmp.cmbMemCode.value=='0') {
		alert('Field should be selected');
		document.frmEmp.cmbMemCode.focus();
		return;
	}

	if(document.frmEmp.cmbMemTypeCode.value=='0') {
		alert('Field should be selected');
		document.frmEmp.cmbMemTypeCode.focus();
		return;
	}

	if(document.frmEmp.cmbMemSubOwn.value=='0') {
		alert('Field should be selected');
		document.frmEmp.cmbMemSubOwn.focus();
		return;
	}

	var txt = document.frmEmp.txtMemSubAmount;
	if ((txt.value != '') && !decimalCurr(txt)) {
		alert ("Subscription amount should be numeric!");
		txt.focus();
		return false;
	} else if (txt.value == '') {
		confirmx = confirm('Membership subscription amount is empty. Do you want to continue ?');

		if (!confirmx) {
			txt.focus();
			return confirmx;
		}

	}

	var commDate = createDate(document.frmEmp.txtMemCommDat.value);
	var renDate = createDate(document.frmEmp.txtMemRenDat.value);

	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}

  document.frmEmp.membershipSTAT.value="ADD";
    qCombo(13);
}

function editEXTMembership() {

	var txt = document.frmEmp.txtMemSubAmount;
	if ((txt.value != '') && !decimalCurr(txt)) {
		alert ("Subscription amount should be numeric!");
		txt.focus();
		return false;
	}

	var commDate = createDate(document.frmEmp.txtMemCommDat.value);
	var renDate = createDate(document.frmEmp.txtMemRenDat.value);

	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}

  document.frmEmp.membershipSTAT.value="EDIT";
    qCombo(13);
}

function delEXTMembership() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkmemdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('Select at least one record to Delete')
		return;
	}


    //alert(cntrl.value);
    document.frmEmp.membershipSTAT.value="DEL";
    qCombo(13);
}

function viewMembership(mem,mtp) {

	document.frmEmp.action=document.frmEmp.action + "&MEM=" + mem + "&MTP=" + mtp;
	document.frmEmp.pane.value=13;
	document.frmEmp.submit();
}

</script>
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

        <input type="hidden" name="membershipSTAT" value="">

<?php
if(isset($this->popArr['editMembershipArr'])) {

    $edit = $this->popArr['editMembershipArr'];
?>

<br>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $membershiptype?></td>
    				  <td><strong>
					  <input type="hidden" name="cmbMemTypeCode" value="<?php echo $edit[0][2]?>">
<?php
						$typlist = $this->popArr['typlist'];
						for($c=0;count($typlist)>$c;$c++)
							if($typlist[$c][0]==$edit[0][2])
							   echo $typlist[$c][1];
?>
					  </strong></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $membership?></td>
						<td align="left" valign="top"><strong>
						<input type="hidden" name="cmbMemCode" value="<?php echo $edit[0][1]?>">
<?php
						$mship = $this->popArr['mship'];
						for($c=0;count($mship)>$c;$c++)
						    if($mship[$c][1]==$edit[0][1])
						       echo $mship[$c][2];
?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subownership?></td>
						<td align="left" valign="top"><select disabled name="cmbMemSubOwn">
<?php
						for($c=0;count($subown)>$c;$c++)
						    if($edit[0][3]==$subown[$c])
							    echo "<option selected value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							else
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
?>
						</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subamount?></td>
						<td align="left" valign="top"><input type="text" disabled name="txtMemSubAmount" value="<?php echo $edit[0][4]?>">
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subcomdate?></td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemCommDat" value=<?php echo $edit[0][5]?>>&nbsp;<input class="button" disabled type="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subredate?></td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemRenDat" value=<?php echo $edit[0][6]?>>&nbsp;<input class="button" disabled type="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMemRenDat);return false;">
						</td>
					  </tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?php			if($locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutMembership();" onmouseover="moverMembership();" name="EditMembership" onClick="editMembership();">
		<?php			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?php echo $sysConst->accessDenied?>');">
		<?php			}  ?>
						</td>
					  </tr>
       </table>

<?php } else { ?>

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $membershiptype?></td>
    				  <td>
					  <select onChange="xajax_getUnAssMemberships(this.value);" <?php echo $locRights['add'] ? '':'disabled'?> name="cmbMemTypeCode">
					  <option value=0><?php echo $selmemtype?></option>

<?php					  	$typlist= $this->popArr['typlist'];
							for($c=0;$typlist && count($typlist)>$c;$c++)
							if(isset($this->popArr['cmbMemTypeCode']) && $this->popArr['cmbMemTypeCode']==$typlist[$c][0])

							   echo "<option selected value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";
							else
							   echo "<option value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";

?>
					  </select></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $membership?></td>
						<td align="left" valign="top"><select <?php echo $locRights['add'] ? '':'disabled'?> name='cmbMemCode'>
						   		<option value=0><?php echo $selmemship?></option>
<?php
					if(isset($this->popArr['cmbMemTypeCode'])) {

						$mship=$this->popArr['mship'];
						for($c=0;$mship && count($mship)>$c;$c++)
						    echo "<option value=" . $mship[$c][0] . ">" . $mship[$c][1] . "</option>";
						}
?>
						</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subownership?></td>
						<td align="left" valign="top"><select <?php echo $locRights['add'] ? '':'disabled'?> name="cmbMemSubOwn">
						   		<option value=0><?php echo $selownership?></option>
<?php
						for($c=0;count($subown)>$c;$c++)
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";

?>
						</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subamount?></td>
						<td align="left" valign="top"><input type="text" <?php echo $locRights['add'] ? '':'disabled'?> name="txtMemSubAmount" >
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subcomdate?></td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemCommDat" value="0000-00-00">&nbsp;<input class="button" <?php echo $locRights['add'] ? '':'disabled'?> type="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $subredate?></td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemRenDat" value="0000-00-00">&nbsp;<input class="button" <?php echo $locRights['add'] ? '':'disabled'?> type="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtMemRenDat);return false;">
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?php	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXTMembership();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php	} ?>
						</td>
					  </tr>
                  </table>
<?php } ?>

<?php

    $mship= $this->popArr['mshipAll'];
    $rset = $this->popArr['rsetMembership'];

    // check if there are any defined memberships
    if( $rset && count($rset) > 0 ){
        $assignedMemberships = true;
    } else {
        $assignedMemberships = false;
    }
?>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>
    <td width='100%'><h3><?php echo $assignmemship?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

<?php if( !$assignedMemberships ){ ?>
  <tr>
    <td width='100%'><h5><?php echo $lang_empview_norecorddisplay ?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>


<?php
     } else {
?>

  <tr>
  <td>

<?php	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(13);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<?php 	} else { ?>
		<img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php	} ?>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXTMembership();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
                    <tr>
                      	<td></td>
						 <td><strong><?php echo $membership?></strong></td>
						 <td><strong><?php echo $membershiptype?></strong></td>
						 <td><strong><?php echo $subownership?></strong></td>
						 <td><strong><?php echo $subcomdate?></strong></td>
						 <td><strong><?php echo $subredate?></strong></td>
					</tr>
<?php



    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkmemdel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($mship)>$a;$a++)
			    if($mship[$a][1]==$rset[$c][1])
				   $fname=$mship[$a][2];

            ?><td><a href="javascript:viewMembership('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo $fname?></a></td><?php

            for($a=0;count($typlist)>$a;$a++)
			    if($typlist[$a][0]==$rset[$c][2])
				   $fname=$typlist[$a][1];
            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            $disStr = explode(" ",$rset[$c][5]);
            echo '<td>' . $disStr[0] .'</td>';
            $disStr = explode(" ",$rset[$c][6]);
            echo '<td>' . $disStr[0] .'</td>';
        echo '</tr>';
        }
?>
<?php } //if( $assignedMemberships ) ?>
</table>

<?php } ?>
