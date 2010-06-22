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
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--

function editWrkExp() {

	if(document.EditWrkExp.title=='Save') {
		editEXTWrkExp();
		return;
	}

	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;

	document.EditWrkExp.src="../../themes/beyondT/pictures/btn_save.gif";
	document.EditWrkExp.title="Save";
}

function moutWrkExp() {
	if(document.EditWrkExp.title=='Save')
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function moverWrkExp() {
	if(document.EditWrkExp.title=='Save')
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));

		var tempDate = new Date(yy,mm,dd);

		return tempDate;
}

function addEXTWrkExp() {

 	var txt = document.frmEmp.txtEmpExpEmployer;
	if (txt.value == '') {
		alert("<?php echo $lang_Common_FieldEmpty; ?>!");
		txt.focus();
		return false;
	}

    var txt = document.frmEmp.txtEmpExpJobTitle;
	if (txt.value == '') {
		alert("<?php echo $lang_Common_FieldEmpty; ?>!");
		txt.focus();
		return false;
	}

    if (document.frmEmp.txtEmpExpComments.value.length > 200 ) {
        alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo200Chars; ?>');
        document.frmEmp.txtEmpExpComments.focus();
        return;
    }

	var fromDate = strToDate(document.getElementById('atxtEmpExpFromDate').value, YAHOO.OrangeHRM.calendar.format);
	var toDate = strToDate(document.getElementById('atxtEmpExpToDate').value, YAHOO.OrangeHRM.calendar.format);
	var currentDate = document.getElementById('atxtEmpExpToDate').value;

	if (toDate && (fromDate > toDate)) {
		alert("<?php echo $lang_hremp_FromDateShouldBeBeforeToDate; ?>");

		return;
	}


  document.frmEmp.wrkexpSTAT.value="ADD";
  qCombo(17);
}

function editEXTWrkExp() {

 	var txt = document.frmEmp.txtEmpExpEmployer;
	if (txt.value == '') {
		alert ("<?php echo $lang_Common_FieldEmpty; ?>!");
		txt.focus();
		return false;
	}

    var txt = document.frmEmp.txtEmpExpJobTitle;
	if (txt.value == '') {
		alert ("<?php echo $lang_Common_FieldEmpty; ?>!");
		txt.focus();
		return false;
	}

    if (document.frmEmp.txtEmpExpComments.value.length > 200 ) {
        alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo200Chars; ?>');
        document.frmEmp.txtEmpExpComments.focus();
        return;
    }
    
	var fromDate = strToDate(document.getElementById('etxtEmpExpFromDate').value, YAHOO.OrangeHRM.calendar.format);
	var toDate = strToDate(document.getElementById('etxtEmpExpToDate').value, YAHOO.OrangeHRM.calendar.format);
	var currentDate = document.getElementById('etxtEmpExpToDate').value;


///////////To validate the date fields

	if (toDate && (fromDate > toDate)) {
		alert("<?php echo $lang_hremp_FromDateShouldBeBeforeToDate; ?>");

		return;
	}
/////////////////////


  document.frmEmp.wrkexpSTAT.value="EDIT";
  qCombo(17);
}

function delEXTWrkExp() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkwrkexpdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

    document.frmEmp.wrkexpSTAT.value="DEL";
    qCombo(17);
}

function viewWrkExp(wrkexp) {

	document.frmEmp.action = document.frmEmp.action + "&WRKEXP=" + wrkexp;
	document.frmEmp.pane.value = 17;
	document.frmEmp.submit();
}

//--><!]]></script>
<div id="parentPaneWorkExperience" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

    <input type="hidden" name="wrkexpSTAT" value=""/>
<?php
if(isset($this->popArr['editWrkExpArr'])) {
    $edit = $this->popArr['editWrkExpArr'];
?>
<div id="editPaneWorkExperience" >
      <input type="hidden" name="txtEmpExpID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['WRKEXP'])?>"/>
      <table border="0" cellpadding="5" cellspacing="0">
                    <tr>
                      <td><?php echo $lang_hrEmpMain_employer?></td>
    				  <td><input type="text" name="txtEmpExpEmployer" maxlength="100" value="<?php echo CommonFunctions::escapeHtml($edit[0][2])?>"/></td>
    				  <td width="50">&nbsp;</td>
					  <td nowrap="nowrap"><?php echo $lang_hrEmpMain_startdate?></td>
					  <td nowrap="nowrap">
					  	<input class="formDateInput" type="text" name="txtEmpExpFromDate" id="etxtEmpExpFromDate" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][4]); ?>" size="10" />
					  	<input type="button" class="calendarBtn" value="   " /></td>
					</tr>
					  <tr>
						<td><?php echo $lang_empview_JobTitle?></td>
						<td> <input type="text" name="txtEmpExpJobTitle" value="<?php echo CommonFunctions::escapeHtml($edit[0][3])?>" maxlength="120"/></td>
    				  <td width="50">&nbsp;</td>
						<td nowrap="nowrap"><?php echo $lang_hrEmpMain_enddate?></td>
						<td nowrap="nowrap">
							<input class="formDateInput" type="text" name="txtEmpExpToDate" id="etxtEmpExpToDate" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][5]); ?>" size="10" />
							<input type="button" class="calendarBtn" value="   " /></td>
					  </tr>
					  <tr valign="top">
						<td><?php echo $lang_Leave_Common_Comments; ?></td>
						<td> <textarea name="txtEmpExpComments"  rows="3" cols="25" ><?php echo CommonFunctions::escapeHtml($edit[0][6])?></textarea></td>
    				  	<td width="50">&nbsp;</td>
						<td width="50"><?php echo $lang_hrEmpMain_internal?></td>
						<td width="50"><input type="checkbox" name="chkEmpExpInternal" value="1" <?php echo (isset($edit[0][7]) && ($edit[0][7] == 1)) ? 'checked="checked"' : '' ?>/></td>
						<td width="50">&nbsp;</td>
					 </tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						</td>
	    </tr>
	</table>
<?php	if($locRights['edit']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnEditWrkExp" id="btnEditWrkExp"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEXTWrkExp(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>

</div>
<?php } else { ?>
<div id="addPaneWorkExperience" class="<?php echo ($this->popArr['rsetWrkExp'] != null)?"addPane":""; ?>" >
    	<input type="hidden" name="txtEmpExpID"  value="<?php echo CommonFunctions::escapeHtml($this->popArr['newWrkExpID'])?>"/>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
          <tr>
            <td><?php echo $lang_hrEmpMain_employer?></td>
            <td><input type="text" name="txtEmpExpEmployer" maxlength="100" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> /></td>
            <td width="20">&nbsp;</td>
            <td nowrap="nowrap"><?php echo $lang_hrEmpMain_startdate?></td>
            <td nowrap="nowrap">
            	<input class="formDateInput" type="text" name="txtEmpExpFromDate" id="atxtEmpExpFromDate" value="" size="10" />
           		<input name="button" type="button" class="calendarBtn" value="   " <?php echo $locRights['add'] ? '':'disabled="disabled"'?> /></td>
          </tr>
          <tr>
            <td><?php echo $lang_empview_JobTitle?></td>
            <td><input type="text" name="txtEmpExpJobTitle" maxlength="120" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> /></td>
            <td width="20">&nbsp;</td>
            <td nowrap="nowrap"><?php echo $lang_hrEmpMain_enddate?></td>
            <td nowrap="nowrap">
            	<input class="formDateInput" type="text" name="txtEmpExpToDate" id="atxtEmpExpToDate" value="" size="10" />
              	<input name="button" type="button" class="calendarBtn" value="   " <?php echo $locRights['add'] ? '':'disabled="disabled"'?> /></td>
          </tr>
          <tr valign="top">
            <td><?php echo $lang_Leave_Common_Comments; ?></td>
            <td><textarea <?php echo $locRights['add'] ? '':'disabled="disabled"'?>  rows="3" cols="25" name="txtEmpExpComments"></textarea></td>
            <td width="20">&nbsp;</td>
			<td><?php echo $lang_hrEmpMain_internal?></td>
			<td><input type="checkbox" name="chkEmpExpInternal" <?php echo $locRights['add'] ? '':'disabled="disabled"'?> value="1"/></td>
          </tr>
        </table>
<?php	if($locRights['add']) { ?>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddWrkExp" id="btnAddWrkExp"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addEXTWrkExp(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php	} ?>
</div>
<?php } ?>
<?php
    $rset = $this->popArr['rsetWrkExp'];

    // check if there are any defined work experiences
    if( $rset && count($rset) > 0 ){
        $assignedExperiences = true;
    } else {
        $assignedExperiences = false;
    }
?>
<?php if($assignedExperiences) { ?>

<div class="subHeading"><h3><?php echo $lang_hrEmpMain_assignworkex?></h3></div>
	<div class="actionbar">
		<div class="actionbuttons">
<?php if ($locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('WorkExperience');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
<?php	if ($locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delEXTWrkExp();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

	<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
	<thead>
	  <tr>
	  	<td></td>
		 <td width="125"><?php echo $lang_hrEmpMain_workexid?></td>
		 <td width="110"><?php echo $lang_hrEmpMain_employer; ?></td>
		 <td width="100"><?php echo $lang_hremp_jobtitle; ?></td>
		 <td width="65"><?php echo $lang_hrEmpMain_startdate; ?></td>
		 <td width="65"><?php echo $lang_hrEmpMain_enddate; ?></td>
		 <td><?php echo $lang_hrEmpMain_internal; ?></td>
	</tr>
	</thead>
	<tbody>
<?php
    for($c=0; $rset && $c < count($rset); $c++) {
			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkwrkexpdel[]' value='" . $rset[$c][1] ."'/></td>";
            ?><td><a href="javascript:viewWrkExp('<?php echo $rset[$c][1]?>')"><?php echo $rset[$c][1]?></a></td><?php
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][2]) .'</td>';
            echo '<td>' . CommonFunctions::escapeHtml($rset[$c][3]) .'</td>';
            $str = explode(" ",$rset[$c][4]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($str[0]) .'</td>';
            $str = explode(" ",$rset[$c][5]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($str[0]) .'</td>';
			$str = (isset($rset[$c][7]) && ($rset[$c][7] == 1))? '<img src="../../themes/beyondT/icons/flag.gif" alt="internal" width="22" height="19" title="Internal"/>' : '';
			echo '<td>' .$str.'</td>';
        echo '</tr>';
        }
?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
</div>
