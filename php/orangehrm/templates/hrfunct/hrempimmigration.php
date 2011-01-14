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
 *
 */
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
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
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

	document.frmEmp.passportSTAT.value="DEL";
	qCombo(10);
}

function addPassport() {

	if(document.frmEmp.txtPPNo.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}

	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPExpDat.focus();
		return;
	}

    if(document.frmEmp.txtComments.value.length > 255 ) {
        alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo255Chars; ?>');
        document.frmEmp.txtComments.focus();
        return;
    }

	startDate = strToDate(document.getElementById('atxtPPIssDat').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('atxtPPExpDat').value, YAHOO.OrangeHRM.calendar.format);

    var cnt = document.frmEmp.atxtI9ReviewDat;
    if(cnt.value.length > 0 && cnt.value != 'YYYY-mm-DD') {
        var dt = cnt.value.split("-");
        if(!validateDate(dt[2], dt[1], dt[0])) {
            alert("Invalid I9 Review Date");
            return;
        }
    }

    var cnt = document.frmEmp.atxtPPIssDat;
    if(cnt.value.length > 0 && cnt.value != 'YYYY-mm-DD') {
        var dt = cnt.value.split("-");
        if(!validateDate(dt[2], dt[1], dt[0])) {
            alert("Invalid Issued Date");
            return;
        }
    }

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_IssedDateShouldBeBeforeExp; ?>");
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

	if(document.frmEmp.txtPPNo.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPNo.focus();
		return;
	}

	if(document.frmEmp.txtPPIssDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPIssDat.focus();
		return;
	}

	if(document.frmEmp.txtPPExpDat.value == '') {
		alert('<?php echo $lang_Common_FieldEmpty; ?>');
		document.frmEmp.txtPPExpDat.focus();
		return;
	}

    if(document.frmEmp.txtComments.value.length > 255 ) {
        alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo255Chars; ?>');
        document.frmEmp.txtComments.focus();
        return;
    }
    
	startDate = strToDate(document.getElementById('etxtPPIssDat').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('etxtPPExpDat').value, YAHOO.OrangeHRM.calendar.format);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_IssedDateShouldBeBeforeExp; ?>");
		return;
	}

	document.frmEmp.passportSTAT.value="EDIT";
	qCombo(10);
}

//--><!]]></script>
<div id="parentPaneImmigration" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<input type="hidden" name="passportSTAT" value=""/>
<?php if(isset($this->getArr['PPSEQ'])) {
		$edit = $this->popArr['editPPForm'];
?>
	<div id="editPaneImmigration" >
		<table style="height:170px;padding: 5px 5px 5px 5px;" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <input type="hidden" name="txtPPSeqNo" value="<?php echo $edit[0][1]?>"/>
			  <td><?php echo $lang_hremp_passport; ?> <input type="radio" checked="checked" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> name="PPType" value="1"/></td>
			  <td><?php echo $lang_hremp_visa?><input type="radio" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> name="PPType" <?php echo ($edit[0][6]=='2')?'checked="checked"':''?> value="2"/></td>
			  <td width="50">&nbsp;</td>
		  	 <td><?php echo $lang_hremp_citizenship; ?></td>
                <td><select class="formSelect" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> name="cmbPPCountry">
                <option value="0">-- <?php echo $lang_districtinformation_selectcounlist ?> --</option>
<?php				$list = $this->popArr['ppcntlist'];
				for($c=0;count($list)>$c;$c++)
					if($edit[0][9]==$list[$c][0])
					    echo "<option selected=\"selected\" value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>
				</select>
				</td>
			  </tr>
              <tr>
                <td><?php echo $lang_hremp_passvisano; ?></td>
                <td><input type="text" name="txtPPNo" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> maxlength="100"
                           value="<?php echo CommonFunctions::escapeHtml($edit[0][2])?>"/></td>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_issueddate; ?></td>
                <td><input class="formDateInput" type="text" name="txtPPIssDat" id="etxtPPIssDat" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?>
                           value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>" size="10"/>
					<input type="button" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> value="   " class="calendarBtn" /></td>
              </tr>
              <tr>
                <td><?php echo $lang_hremp_i9status; ?></td>
                <td><input name="txtI9status" type="text" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?>
                           value="<?php echo CommonFunctions::escapeHtml($edit[0][7])?>" maxlength="100"/>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_dateofexp; ?></td>
                <td><input class="formDateInput" type="text" name="txtPPExpDat" id="etxtPPExpDat" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?>
                           value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][4]); ?>" size="10"/>
                	<input type="button" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> class="calendarBtn" value="   " /></td>
              </tr>
              <tr>
               <td><?php echo $lang_hremp_i9reviewdate; ?></td>
                <td><input class="formDateInput" type="text" name="txtI9ReviewDat" id="etxtI9ReviewDat" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?>
                           value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][8]); ?>" size="10"/>
                	<input type="button" <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> class="calendarBtn" value="   " /></td>
				<td width="50">&nbsp;</td>
				<td><?php echo $lang_Leave_Common_Comments; ?></td>
				<td><textarea <?php echo $locRights['edit'] ? '':'disabled="disabled"'?> rows="3" cols="25" name="txtComments"><?php echo CommonFunctions::escapeHtml($edit[0][5])?></textarea></td>
				</tr>
		</table>
<?php	if($locRights['edit']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnEditPassport" id="btnEditPassport"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editPassport(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php } ?>
	</div>
	<?php } else { ?>
	<div id="addPaneImmigration" class="<?php echo ($this->popArr['empPPAss'] != null)?"addPane":""; ?>" >
		<table style="height:170px;padding: 5px 5px 5px 5px;" border="0" cellpadding="0" cellspacing="0">
          <tr >
			  <td nowrap="nowrap"><?php echo $lang_hremp_passport; ?>
			  	<input type="hidden" name="txtPPSeqNo" value="<?php echo $this->popArr['newPPID']?>"/>
			  	<input type="radio" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> checked="checked" name="PPType" value="1"/>&nbsp;&nbsp;</td>
			  <td nowrap="nowrap"><?php echo $lang_hremp_visa; ?>&nbsp;&nbsp;<input type="radio" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> name="PPType" value="2"/></td>
			  <td width="50">&nbsp;</td>
		  	  <td><?php echo $lang_hremp_citizenship; ?>&nbsp;&nbsp;</td>
                <td><select class="formSelect" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> name="cmbPPCountry">
                		<option value="0">-- <?php echo $lang_districtinformation_selectcounlist ?> --</option>
<?php				$list = $this->popArr['ppcntlist'];
				for($c=0;$list && count($list)>$c;$c++)
				    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>
				</select>
				</td>
		    </tr>
              <tr>
                <td><?php echo $lang_hremp_passvisano; ?>&nbsp;&nbsp;</td>
                <td><input type="text" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> name="txtPPNo" maxlength="100" /></td>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_issueddate; ?>&nbsp;&nbsp;</td>
                <td><input class="formDateInput" type="text" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> name="txtPPIssDat" id="atxtPPIssDat" value="" size="10"/>
                	<input type="button" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> class="calendarBtn" value="   " /></td>
              </tr>
              <tr>
                <td><?php echo $lang_hremp_i9status; ?></td>
                <td><input name="txtI9status" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> type="text" maxlength="100"/></td>
                <td width="50">&nbsp;</td>
                <td><?php echo $lang_hremp_dateofexp; ?></td>
                <td><input class="formDateInput" type="text" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> value="" name="txtPPExpDat" id="atxtPPExpDat" size="10"/>
                	<input type="button" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> class="calendarBtn" value="   " /></td>
              </tr>
              <tr>
               <td><?php echo $lang_hremp_i9reviewdate; ?></td>
                <td nowrap="nowrap"><input class="formDateInput" type="text" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> value="" name="txtI9ReviewDat" id="atxtI9ReviewDat" size="10"/>
                			<input type="button" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> class="calendarBtn" value="   " /></td>
				<td width="50">&nbsp;</td>
				<td><?php echo $lang_Leave_Common_Comments; ?></td>
				<td><textarea <?php echo $locRights['add'] ? '':'disabled="disabled"'?> rows="3" cols="25" name="txtComments"></textarea></td>
				</tr>
		</table>
<?php	if($locRights['add']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddPassport" id="btnAddPassport"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addPassport(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>
	</div>
<?php } ?>
<div id="tablePassport">
<?php
$rset = $this->popArr['empPPAss'];
if ($rset != null){?>
	<div class="subHeading"><h3><?php echo $lang_hremp_AssignedPassportVisas; ?></h3></div>

	<div class="actionbar">
		<div class="actionbuttons">
<?php if($locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('Immigration');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
	<?php	if($locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delPassport();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>


	<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
            	<td></td>
				<td><?php echo "$lang_hremp_passport/$lang_hremp_visa"; ?></td>
				<td><?php echo $lang_hremp_passvisano?></td>
				<td><?php echo $lang_hremp_citizenship?></td>
				<td><?php echo $lang_hremp_issueddate; ?></td>
				<td><?php echo $lang_hremp_dateofexp; ?></td>
			</tr>
		</thead>
		<tbody>
<?php
    for($c=0;$rset && $c < count($rset); $c++) {
		$cssClass = ($c%2) ? 'even' : 'odd';
        echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkpassportdel[]' value='" . $rset[$c][1] ."'/></td>";
			if($rset[$c][6]==1)
            	$fname="Passport";
            else
            	$fname="Visa";

            ?> <td><a href="#" onmousedown="viewPassport(<?php echo $rset[$c][1]?>)" ><?php echo $fname?></a></td> <?php
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][2]) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][9]) .'</td>';
            $dtPrint = explode(" ",$rset[$c][3]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($dtPrint[0]) .'</td>';
            $dtPrint = explode(" ",$rset[$c][4]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($dtPrint[0]) .'</td>';
        echo '</tr>';
    } ?>
		</tbody>
    </table>
<?php } ?>
</div>
<?php } ?>
</div>
