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
	document.EditLicense.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditLicense.title="Save";
}

function moutLicense() {
	if(document.EditLicense.title=='Save')
		document.EditLicense.src='../../themes/beyondT/pictures/btn_save.jpg';
	else
		document.EditLicense.src='../../themes/beyondT/pictures/btn_edit.jpg';
}

function moverLicense() {
	if(document.EditLicense.title=='Save')
		document.EditLicense.src='../../themes/beyondT/pictures/btn_save_02.jpg';
	else
		document.EditLicense.src='../../themes/beyondT/pictures/btn_edit_02.jpg';
}

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));

		var tempDate = new Date(yy,mm,dd);

		return tempDate;
}

function addEXTLicense() {

	var fromDate = createDate(document.getElementById('atxtEmpLicDat').value)
	var toDate = createDate(document.getElementById('atxtEmpreDat').value);

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

	var fromDate = createDate(document.getElementById('etxtEmpLicDat').value)
	var toDate = createDate(document.getElementById('etxtEmpreDat').value);

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
							<input type="text" readonly name="txtEmpLicDat" id="etxtEmpLicDat" value=<?php echo isset($this->popArr['txtEmpLicDat']) ? $this->popArr['txtEmpLicDat'] : $edit[0][2]?> size="10" />
							<input disabled type="button" name="btnEmpLicDat" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('etxtEmpLicDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
    				<tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input type="text" readonly name="txtEmpreDat" id="etxtEmpreDat" value=<?php echo isset($this->popArr['txtEmpreDat']) ? $this->popArr['txtEmpreDat'] : $edit[0][3]?> size="10" />
							<input disabled type="button" name="btnEmpreDat" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('etxtEmpreDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
					</tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutLicense();" onmouseover="moverLicense();" name="EditLicense" onClick="editLicense();">
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
							<input type="text" name="txtEmpLicDat" id="atxtEmpLicDat" readonly value="<?php echo isset($this->popArr['txtEmpLicDat']) ?$this->popArr['txtEmpLicDat'] :'0000-00-00'?>" size="10" />
							<input type="button" name="btnEmpLicDat" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('atxtEmpLicDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
    				  </tr>
    				  <tr>
                       <td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input type="text" name="txtEmpreDat" id="atxtEmpreDat" readonly value="<?php echo isset($this->popArr['txtEmpreDat']) ?$this->popArr['txtEmpreDat'] :'0000-00-00'?>" size="10" />
							<input type="button" name="btnEmpreDat" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('atxtEmpreDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
					</tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
        <img border="0" title="Save" onClick="addEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
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

	 <img border="0" title="Add" onClick="showAddPane('Licenses');" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
     <img title="Delete" onclick="delEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">

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
            echo '<td>' . $str[0] .'</td>';
            $str = explode(" ",$rset[$c][3]);
            echo '<td>' . $str[0] .'</td>';
        echo '</tr>';
        }

?>
	</table>
<?php } ?>
<?php } ?>
</span>
