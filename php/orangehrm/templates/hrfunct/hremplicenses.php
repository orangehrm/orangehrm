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

	var fromDate = createDate(document.frmEmp.txtEmpLicDat.value)
	var toDate = createDate(document.frmEmp.txtEmpreDat.value);

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

	var fromDate = createDate(document.frmEmp.txtEmpLicDat.value)
	var toDate = createDate(document.frmEmp.txtEmpreDat.value);

	if(fromDate >= toDate){
		alert("From Date should be before To date");
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

    //alert(cntrl.value);
    document.frmEmp.licenseSTAT.value="DEL";
	qCombo(12);
}

function viewLicense(lic) {

	document.frmEmp.action=document.frmEmp.action + "&LIC=" + lic;
	document.frmEmp.pane.value=12;
	document.frmEmp.submit();
}

</script>


<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

        <input type="hidden" name="licenseSTAT" value="">
<?php

if(isset($this->getArr['LIC'])) {

    $edit = $this->popArr['editLicenseArr'];
?>			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
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
						<td> <input type="text" readonly name="txtEmpLicDat" value=<?php echo isset($this->popArr['txtEmpLicDat']) ? $this->popArr['txtEmpLicDat'] : $edit[0][2]?>>&nbsp;<input disabled type="button" name="btnEmpLicDat" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpLicDat);return false;"></td>
    				<tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td><input type="text" readonly name="txtEmpreDat" value=<?php echo isset($this->popArr['txtEmpreDat']) ? $this->popArr['txtEmpreDat'] : $edit[0][3]?>>&nbsp;<input disabled type="button" name="btnEmpreDat" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpreDat);return false;"></td>
					</tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutLicense();" onmouseover="moverLicense();" name="EditLicense" onClick="editLicense();">
						</td>
					  </tr>
                  </table>
<?php } else { ?>

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
						<td> <input type="text" name="txtEmpLicDat" readonly value="<?php echo isset($this->popArr['txtEmpLicDat']) ?$this->popArr['txtEmpLicDat'] :'0000-00-00'?>">&nbsp;<input type="button" name="btnEmpLicDat" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpLicDat);return false;"></td>
    				  </tr>
    				  <tr>
                       <td><?php echo $lang_hrEmpMain_enddate?></td>
						<td> <input type="text" name="txtEmpreDat"  readonly value="<?php echo isset($this->popArr['txtEmpreDat']) ?$this->popArr['txtEmpreDat'] :'0000-00-00'?>">&nbsp;<input type="button" name="btnEmpreDat" class="button" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpreDat);return false;"></td>
					</tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
        <img border="0" title="Save" onClick="addEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
					  </tr>
                  </table>
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

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
 <?php
if ($rset != Null){ ?>
  <tr>

    <td width='100%'><h3><?php echo $lang_hremplicenses_assignlicen?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
<?php } ?>
<?php if( !$assignedLicenses ){ ?>
 <!-- <tr>
    <td width='100%'><h5><?php /*echo $lang_empview_norecorddisplay */ ?> </h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr> -->


<?php
     } else {
?>
<tr><td>&nbsp;</td></tr>
</table>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
<?php
if ($rset != Null){ ?>
                    <tr>
                      	 <td ></td>
						 <td ><strong><?php echo $lang_hremplicenses_licentype?></strong></td>
						 <td ><strong><?php echo $lang_hrEmpMain_startdate?></strong></td>
						 <td ><strong><?php echo $lang_hrEmpMain_enddate?></strong></td>

					</tr>
		<img border="0" title="Add" onClick="resetAdd(12);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
        <img title="Delete" onclick="delEXTLicense();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">

<?php }
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
<?php } //if( $assignedLicenses ) ?>
</table>

<?php } ?>
