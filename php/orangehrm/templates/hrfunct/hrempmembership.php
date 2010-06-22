<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
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

 $subown = array($lang_hrEmpMain_subown_Company,	$lang_hrEmpMain_subown_Individual);
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
function editMembership() {
	if($('btnEditMembership').value == '<?php echo $lang_Common_Save; ?>') {
		editEXTMembership();
		return;
	} else {
		$('btnEditMembership').value = '<?php echo $lang_Common_Save; ?>';
		$('btnEditMembership').onClick = editEXTMembership;
	}

	var frm = document.frmEmp;
	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}
}

function addEXTMembership() {

	if(document.frmEmp.cmbMemTypeCode.value=='0') {
		alert('<?php echo $lang_hrempmemberships_NoMembershipTypeSelected; ?>');
		document.frmEmp.cmbMemTypeCode.focus();
		return;
	}

	if(document.frmEmp.cmbMemCode.value=='0') {
		alert('<?php echo $lang_hrempmemberships_NoMembershipSelected; ?>');
		document.frmEmp.cmbMemCode.focus();
		return;
	}

	if(document.frmEmp.cmbMemSubOwn.value=='0') {
		alert('<?php echo $lang_hrempmemberships_NoSubscriptionOwnerSelected; ?>');
		document.frmEmp.cmbMemSubOwn.focus();
		return;
	}

	var txt = document.frmEmp.txtMemSubAmount;
	if ((txt.value != '') && !decimalCurrency(txt)) {
		alert ("<?php echo $lang_hrEmpMain_SubscriptionAmountShouldBeNumeric; ?>!");
		txt.focus();
		return false;
	} else if (txt.value == '') {
		confirmx = confirm('<?php echo $lang_hrEmpMain_MemebershipSubAmountIsEmptyContinue; ?>?');

		if (!confirmx) {
			txt.focus();
			return confirmx;
		}

	}

	var commDate = strToDate(document.getElementById('atxtMemCommDat').value, YAHOO.OrangeHRM.calendar.format);
	var renDate = strToDate(document.getElementById('atxtMemRenDat').value, YAHOO.OrangeHRM.calendar.format);

	if(commDate >= renDate) {
		alert("<?php echo $lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate; ?>");
		return;
	}

  document.frmEmp.membershipSTAT.value="ADD";
    qCombo(13);
}

function editEXTMembership() {

	var txt = document.getElementById('etxtMemSubAmount');
	if ((txt.value != '') && !decimalCurrency(txt)) {
		alert ("<?php echo $lang_hrEmpMain_SubscriptionAmountShouldBeNumeric; ?>!");
		txt.focus();
		return false;
	}

	var commDate = strToDate(document.getElementById('etxtMemCommDat').value, YAHOO.OrangeHRM.calendar.format);
	var renDate = strToDate(document.getElementById('etxtMemRenDat').value, YAHOO.OrangeHRM.calendar.format);

	if(commDate >= renDate) {
		alert("<?php echo $lang_hrEmpMain_CommenceDateShouldBeBeforeRenewalDate; ?>");
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
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

    document.frmEmp.membershipSTAT.value="DEL";
    qCombo(13);
}

function viewMembership(mem,mtp) {

	document.frmEmp.action=document.frmEmp.action + "&MEM=" + mem + "&MTP=" + mtp;
	document.frmEmp.pane.value=13;
	document.frmEmp.submit();
}

//--><!]]></script>
<style type="text/css">
label#membershipLabel,
label#membershipTypeLabel {
	font-weight:bold;
}

div#editPaneMemberships {
	width:100%;
}

div#editPaneMemberships label {
	width: 200px;
}

div#editPaneMemberships br {
	clear:left;
}

div#editPaneMemberships input {
	display:block;
	margin: 2px 2px 2px 2px;
	float:left;
}

div#editPaneMemberships #membershipTypeLabel,
div#editPaneMemberships #membershipLabel, {
	display:inline;
	font-weight:bold;
	padding-left:2px;
}

</style>
<div id="parentPaneMemberships" >
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
     <input type="hidden" name="membershipSTAT" value="" />
<?php
if(isset($this->popArr['editMembershipArr'])) {
    $edit = $this->popArr['editMembershipArr'];
?>
  <input type="hidden" name="cmbMemTypeCode" value="<?php echo $edit[0][2]?>" />
  <input type="hidden" name="cmbMemCode" value="<?php echo $edit[0][1]?>" />
  <div id="editPaneMemberships" >
	  	<label><?php echo $lang_hrEmpMain_membershiptype; ?></label>
		<label id="membershipTypeLabel"><?php
			$typlist = $this->popArr['typlist'];
			for($c=0;count($typlist)>$c;$c++) {
				if($typlist[$c][0]==$edit[0][2]) {
				   echo CommonFunctions::escapeHtml($typlist[$c][1]);
				}
			}
		?>
		</label>
		<br />
		<label><?php echo $lang_hrEmpMain_membership; ?></label>
		<label id="membershipLabel"><?php
			$mship = $this->popArr['mship'];
			for($c=0;count($mship)>$c;$c++) {
			    if($mship[$c][1]==$edit[0][1]) {
					echo CommonFunctions::escapeHtml($mship[$c][2]);
			    }
			}
		?>
		</label>
		<br />
		<label for="cmbMemSubOwn"><?php echo $lang_hrEmpMain_subownership; ?></label>
		<select name="cmbMemSubOwn" id="cmbMemSubOwn" disabled="disabled">
		<?php
			$count = count($subown);
			for($c = 0; $c < $count; $c++) {
				$selected = ($edit[0][3] == $subown[$c]) ? 'selected="selected"' : '';
		?><option <?php echo $selected; ?> value="<?php echo $subown[$c]; ?>"><?php echo $subown[$c]; ?></option>
		<?php
			}
		?>
		</select>
		<br />
		<label for="etxtMemSubAmount"><?php echo $lang_hrEmpMain_subamount?></label>
		<input type="text" name="txtMemSubAmount" id="etxtMemSubAmount" disabled="disabled"
			value="<?php echo CommonFunctions::escapeHtml($edit[0][4])?>" />
		<br />
		<label for="etxtMemCommDat"><?php echo $lang_hrEmpMain_subcomdate?></label>
		<input type="text" name="txtMemCommDat" id="etxtMemCommDat" disabled="disabled"
			value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][5]); ?>" />
		<input class="calendarBtn" type="button" value="   " disabled="disabled" style="float:none" />
		<br />
		<label for="etxtMemRenDat"><?php echo $lang_hrEmpMain_subredate?></label>
		<input type="text" name="txtMemRenDat" id="etxtMemRenDat" disabled="disabled"
			value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][6]); ?>" />
		<input class="calendarBtn" type="button" value="  " disabled="disabled" />
		<br /><br />
		<div class="formbuttons">
			<input type="button" class="editbutton" id="btnEditMembership" value="<?php echo $lang_Common_Edit; ?>"
				onmouseout="moutButton(this);" onmouseover="moverButton(this);"
				onclick="editMembership();" />
			<input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" disabled="disabled"
				onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
		</div>
		&nbsp;
	</div>
<?php } else { ?>
	<div id="addPaneMemberships" class="<?php echo ($this->popArr['rsetMembership'] != null)?"addPane":""; ?>" >
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_membershiptype?></td>
    				  <td>
					  <select class="formSelect" onchange="xajax_getUnAssMemberships(this.value);" name="cmbMemTypeCode">
					  <option value="0">-- <?php echo $lang_hrEmpMain_selmemtype?> --</option>

<?php					  	$typlist= $this->popArr['typlist'];
							for($c=0;$typlist && count($typlist)>$c;$c++)
							if(isset($this->popArr['cmbMemTypeCode']) && $this->popArr['cmbMemTypeCode']==$typlist[$c][0])

							   echo "<option selected=\"selected\" value='" . $typlist[$c][0] . "'>" . CommonFunctions::escapeHtml($typlist[$c][1]) . "</option>";
							else
							   echo "<option value='" . $typlist[$c][0] . "'>" . CommonFunctions::escapeHtml($typlist[$c][1]) . "</option>";

?>
					  </select></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_membership?></td>
						<td align="left" valign="top"><select class="formSelect" name='cmbMemCode'>
						   		<option value="0">-- <?php echo $lang_hrEmpMain_selmemship?> --</option>
<?php
					if(isset($this->popArr['cmbMemTypeCode'])) {

						$mship=$this->popArr['mship'];
						for($c=0;$mship && count($mship)>$c;$c++)
						    echo "<option value='" . $mship[$c][0] . "'>" . CommonFunctions::escapeHtml($mship[$c][1]) . "</option>";
						}
?>
						</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_subownership?></td>
						<td align="left" valign="top"><select class="formSelect" name="cmbMemSubOwn">
						   		<option value="0">-- <?php echo $lang_hrEmpMain_selownership?> --</option>
<?php
						for($c=0;count($subown)>$c;$c++)
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";

?>
						</select></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_subamount?></td>
						<td align="left" valign="top"><input class="formInputText" type="text" name="txtMemSubAmount" id="atxtMemSubAmount" />
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_subcomdate?></td>
						<td align="left" valign="top">
							<input class="formDateInput" type="text" name="txtMemCommDat" id="atxtMemCommDat" value="" size="12" />
							<input class="calendarBtn" type="button" value="   " />
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_subredate?></td>
						<td align="left" valign="top">
							<input class="formDateInput" type="text" name="txtMemRenDat" id="atxtMemRenDat" value="" size="12" />
							<input class="calendarBtn" type="button" value="   " />
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						</td>
					  </tr>
                  </table>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddMembership" id="btnAddMembership"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addEXTMembership(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>

	</div>
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
<?php if($assignedMemberships){ ?>
	<div class="subHeading"><h3><?php echo $lang_hrEmpMain_assignmemship?></h3></div>
	<div class="actionbar">
		<div class="actionbuttons">
			<input type="button" class="addbutton"
				onclick="showAddPane('Memberships');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
			<input type="button" class="delbutton"
				onclick="delEXTMembership();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>
		</div>
	</div>
	<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
		  <tr>
          	<td></td>
			 <td><?php echo $lang_hrEmpMain_membership?></td>
			 <td><?php echo $lang_hrEmpMain_membershiptype?></td>
			 <td><?php echo $lang_hrEmpMain_subownership?></td>
			 <td><?php echo $lang_hrEmpMain_subcomdate?></td>
			 <td><?php echo $lang_hrEmpMain_subredate?></td>
		</tr>
		</thead>
		<tbody>
<?php
    for($c=0;$rset && $c < count($rset); $c++) {
			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkmemdel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'/></td>";
			for($a=0;count($mship)>$a;$a++)
			    if($mship[$a][1]==$rset[$c][1])
				   $fname=$mship[$a][2];

            ?><td><a href="javascript:viewMembership('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo CommonFunctions::escapeHtml($fname)?></a></td><?php

            for($a=0;count($typlist)>$a;$a++)
			    if($typlist[$a][0]==$rset[$c][2])
				   $fname=$typlist[$a][1];
            echo '<td>' . CommonFunctions::escapeHtml($fname) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][3]) .'</td>';
            $disStr = explode(" ",$rset[$c][5]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($disStr[0]) .'</td>';
            $disStr = explode(" ",$rset[$c][6]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($disStr[0]) .'</td>';
        echo '</tr>';
        }
?>
	</tbody>
	</table>
<?php } ?>
<?php } ?>
</div>
