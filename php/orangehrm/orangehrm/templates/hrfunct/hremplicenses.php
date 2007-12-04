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
<script language="JavaScript">
function editLicense() {
	if(document.EditLicense.title=='Save') {
		editEXTLicense();
		return;
	}

	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.EditLicense.src="../../themes/beyondT/pictures/btn_save.gif";
	document.EditLicense.title="Save";
}

function moutLicense() {
	if(document.EditLicense.title=='Save')
		document.EditLicense.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		document.EditLicense.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function moverLicense() {
	if(document.EditLicense.title=='Save')
		document.EditLicense.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		document.EditLicense.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function addEXTLicense() {

	var fromDate = strToDate(document.getElementById('atxtEmpLicDat').value, YAHOO.OrangeHRM.calendar.format);
	var toDate = strToDate(document.getElementById('atxtEmpreDat').value, YAHOO.OrangeHRM.calendar.format);

	if(document.frmEmp.cmbLicCode.value == '0') {
		alert("<?php echo $lang_hremplicenses_NoLicenseSelected; ?>");
		return;
	}

	if(toDate <= fromDate){
		alert("<?php echo $lang_hremp_FromDateShouldBeBeforeToDate; ?>");
		return;
	}

	document.frmEmp.licenseSTAT.value="ADD";
	qCombo(12);
}


function editEXTLicense() {

	var fromDate = strToDate(document.getElementById('etxtEmpLicDat').value, YAHOO.OrangeHRM.calendar.format);
	var toDate = strToDate(document.getElementById('etxtEmpreDat').value, YAHOO.OrangeHRM.calendar.format);

	if(fromDate >= toDate){
		alert('<?php echo $lang_hremp_FromDateShouldBeBeforeToDate; ?>');
		return;
	}

  document.frmEmp.licenseSTAT.value="EDIT";
  qCombo(12);
}

function delEXTLicense() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chklicdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>')
		return;
	}

    document.frmEmp.licenseSTAT.value="DEL";
	qCombo(12);
}

function viewLicense(lic) {

	document.frmEmp.action=document.frmEmp.action + "&LIC=" + lic;
	document.frmEmp.pane.value=12;
	document.frmEmp.submit();
}
</script>
<span id="parentPaneLicenses" >
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
    <input type="hidden" name="licenseSTAT" value="">
<?php
if(isset($this->getArr['LIC'])) {
    $edit = $this->popArr['editLicenseArr'];
?>
	<div id="editPaneLicenses" >
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
				 <tr>
                      <td width="200"><?php echo $lang_hremplicenses_licentype?></td>
    				  <td><input type="hidden" name="cmbLicCode" value="<?php echo $edit[0][1]?>"><strong>
<?php						$allLicenlist = $this->popArr['allLicenlist'];
						for($c=0;count($allLicenlist)>$c;$c++)
							if($this->getArr['LIC']==$allLicenlist[$c][0])
							     break;

					  			echo $allLicenlist[$c][1];
?>
					  </strong></td>
					</tr>
					<tr>
                      	<td><?php echo $lang_hrEmpMain_startdate?></td>
						<td>
							<input type="text" name="txtEmpLicDat" id="etxtEmpLicDat" value=<?php echo isset($this->popArr['txtEmpLicDat'])?LocaleUtil::getInstance()->formatDate($this->popArr['txtEmpLicDat']):LocaleUtil::getInstance()->formatDate($edit[0][2]); ?> size="10" />
							<input type="button" name="btnEmpLicDat" value="  " class="calendarBtn" /></td>
    				<tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input type="text" name="txtEmpreDat" id="etxtEmpreDat" value=<?php echo isset($this->popArr['txtEmpreDat'])?LocaleUtil::getInstance()->formatDate($this->popArr['txtEmpreDat']):LocaleUtil::getInstance()->formatDate($edit[0][3]); ?> size="10" />
							<input type="button" name="btnEmpreDat" value="  " class="calendarBtn" /></td>
					</tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						        <img src="../../themes/beyondT/pictures/btn_save.gif" title="Save" onmouseout="moutLicense();" onmouseover="moverLicense();" name="EditLicense" onClick="editEXTLicense();">
						</td>
					  </tr>
			</table>
		</div>
<?php } else { ?>
	<div id="addPaneLicenses" class="<?php echo ($this->popArr['rsetLicense'] != null)?"addPane":""; ?>" >
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
					  <tr>
                      <td width="200"><?php echo $lang_hremplicenses_licentype?></td>
    				  <td><select name="cmbLicCode">
    				  		<option selected value="0">--<?php echo $lang_hremplicenses_SelectLicenseType; ?>--</option>
<?php						$unassLicenlist= $this->popArr['unassLicenlist'];

						for($c=0;$unassLicenlist && count($unassLicenlist)>$c;$c++)
							if(isset($this->popArr['cmbLicCode']) && $this->popArr['cmbLicCode']==$unassLicenlist[$c][0])
							   echo "<option  value=" . $unassLicenlist[$c][0] . ">" . $unassLicenlist[$c][1] . "</option>";
							 else
							   echo "<option value=" . $unassLicenlist[$c][0] . ">" . $unassLicenlist[$c][1] . "</option>";
?>
					  </select></td>
					</tr>
                    <tr>
                    <td><?php echo $lang_hrEmpMain_startdate?></td>
						<td>
							<input type="text" name="txtEmpLicDat" id="atxtEmpLicDat" value="<?php echo isset($this->popArr['txtEmpLicDat'])?LocaleUtil::getInstance()->formatDate($this->popArr['txtEmpLicDat']):''?>" size="10" />
							<input type="button" name="btnEmpLicDat" value="  " class="calendarBtn" /></td>
    				  </tr>
    				  <tr>
                       <td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input type="text" name="txtEmpreDat" id="atxtEmpreDat" value="<?php echo isset($this->popArr['txtEmpreDat'])?LocaleUtil::getInstance()->formatDate($this->popArr['txtEmpreDat']):''?>" size="10" />
							<input type="button" name="btnEmpreDat" value="  " class="calendarBtn" /></td>
					</tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
        <img border="0" title="Save" onClick="addEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
					  </tr>
                  </table>
	</div>
<?php } ?>
<?php
    $rset = $this->popArr['rsetLicense'];

    // check if there are any defined memberships
    if( $rset && count($rset) > 0 ){
        $assignedLicenses = true;
    } else {
        $assignedLicenses = false;
    }
?>
<?php if($assignedLicenses){ ?>
	 <h3><?php echo $lang_hremplicenses_assignlicen?></h3>

	 <img border="0" title="Add" onClick="showAddPane('Licenses');" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.gif';" src="../../themes/beyondT/pictures/btn_add.gif">
     <img title="Delete" onclick="delEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';" src="../../themes/beyondT/pictures/btn_delete.gif">

	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
                    <tr>
                      	 <td ></td>
						 <td ><strong><?php echo $lang_hremplicenses_licentype?></strong></td>
						 <td ><strong><?php echo $lang_hrEmpMain_startdate?></strong></td>
						 <td ><strong><?php echo $lang_hrEmpMain_enddate?></strong></td>

					</tr>
<?php
$allLicenlist = $this -> popArr['allLicenlist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
?>
        <tr>
            <td ><input type='checkbox' class='checkbox' name='chklicdel[]' value='<?php echo $rset[$c][1]?>'></td>
<?php
			for($a=0;count($allLicenlist)>$a;$a++)
				if($rset[$c][1] == $allLicenlist[$a][0])
				   $lname=$allLicenlist[$a][1];
			?><td><a href="javascript:viewLicense('<?php echo $rset[$c][1]?>')"><?php echo $lname?></td><?php
            $str = explode(" ",$rset[$c][2]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($str[0]) .'</td>';
            $str = explode(" ",$rset[$c][3]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($str[0]) .'</td>';
        echo '</tr>';
        }

?>
	</table>
<?php } ?>
<?php } ?>
</span>
