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
function editEducation() {

	if ($('btnEditEducation').value == '<?php echo $lang_Common_Save; ?>') {
		editEXTEducation();
		return;
	} else {
		$('btnEditEducation').value = '<?php echo $lang_Common_Save; ?>';
		$('btnEditEducation').onClick = editEXTEducation;
	}

	var frm = document.frmEmp;
	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}
}

function addEXTEducation() {

	if(document.frmEmp.cmbEduCode.value=='0') {
		alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
		document.frmEmp.cmbEduCode.focus();
		return;
	}

	var txt = document.getElementById('atxtEmpEduYear');
		if (!numeric(txt)) {
			alert ("<?php echo $lang_Error_FieldShouldBeNumeric; ?>!");
			txt.focus();
			return;
	}

	startDate = strToDate(document.getElementById('atxtEmpEduStartDate').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('atxtEmpEduEndDate').value, YAHOO.OrangeHRM.calendar.format);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>");
		return;
	}

	document.frmEmp.educationSTAT.value="ADD";
	qCombo(9);
}

function editEXTEducation() {

	var txt = document.getElementById('etxtEmpEduYear');
		if (!numeric(txt)) {
			alert ("<?php echo $lang_Error_FieldShouldBeNumeric; ?>!");
			txt.focus();
			return;
	}

	startDate = strToDate(document.getElementById('etxtEmpEduStartDate').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('etxtEmpEduEndDate').value, YAHOO.OrangeHRM.calendar.format);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>");
		return;
	}

  document.frmEmp.educationSTAT.value="EDIT";
  qCombo(9);
}

function delEXTEducation() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkedudel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Common_SelectDelete; ?>')
		return;
	}

    document.frmEmp.educationSTAT.value="DEL";
	qCombo(9);
}

function viewEducation(edu) {

	document.frmEmp.action = document.frmEmp.action + "&EDU=" + edu;
	document.frmEmp.pane.value = 9;
	document.frmEmp.submit();
}
//--><!]]></script>
<style type="text/css">
div#editPaneEducation {
	width:100%;
}

div#editPaneEducation label {
	width: 200px;
}

div#editPaneEducation br {
	clear:left;
}

div#editPaneEducation input {
	display:block;
	margin: 2px 2px 2px 2px;
	float:left;
}

div#editPaneEducation #educationLabel {
	display:inline;
	font-weight:bold;
	padding-left:2px;
}

</style>

<div id="parentPaneEducation" >

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
	<input type="hidden" name="educationSTAT" value=""/>
	<?php if (!isset($this->getArr['EDU'])) { ?>
   	<div id="addPaneEducation" class="<?php echo ($this->popArr['rsetEducation'] != null)?"addPane":""; ?>" >
    	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?php echo $lang_hrEmpMain_education?></td>
    				  <td><select class="formSelect" name="cmbEduCode">
    				  		<option selected="selected" value="0">--<?php echo $lang_hrEmpMain_SelectEducation; ?>--</option>
						<?php	$unAssEduCodes = $this->popArr['unAssEduCodes'];
							for($c=0; $unAssEduCodes && count($unAssEduCodes)>$c; $c++)
								echo "<option value='" .$unAssEduCodes[$c][0] . "'>" .CommonFunctions::escapeHtml($unAssEduCodes[$c][1]). ", ".CommonFunctions::escapeHtml($unAssEduCodes[$c][2]). "</option>";
						 ?>
					  </select></td>
					</tr>
                    <tr>
                      <td><?php echo $lang_hrEmpMain_major?></td>
    				  <td><input class="formInputText" type="text" name="txtEmpEduMajor" maxlength="100"/></td>
					</tr>
					 <tr>
					<td><?php echo $lang_Leave_Common_Year?></td>
					   <td><input class="formInputText" type="text" name="txtEmpEduYear" id="atxtEmpEduYear" /></td>
					 </tr>
					 <tr>
					<td><?php echo $lang_hrEmpMain_gpa?></td>
						<td> <input class="formInputText" type="text" name="txtEmpEduGPA" maxlength="25"/></td>
					 </tr>
					<tr>
					<td><?php echo $lang_hrEmpMain_startdate?></td>
						<td>
							<input class="formDateInput" type="text" name="txtEmpEduStartDate" id="atxtEmpEduStartDate" value="" />
							<input type="button" value="  " class="calendarBtn" />
						</td>
					</tr>
					  <tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input class="formDateInput" type="text" name="txtEmpEduEndDate" id="atxtEmpEduEndDate" value="" />
							<input type="button" value="  " class="calendarBtn" />
						</td>
					 </tr>

					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
					    </td>
					  </tr>
			</table>
		<div class="formbuttons">
		    <input type="button" class="savebutton" name="btnAddEducation" id="btnAddEducation"
		    	value="<?php echo $lang_Common_Save;?>"
		    	title="<?php echo $lang_Common_Save;?>"
		    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		    	onclick="addEXTEducation(); return false;"/>
		    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>"
				onmouseover="moverButton(this)" onmouseout="moutButton(this)" />
		</div>
	</div>
	<?php } ?>
<?php
if(isset($this->popArr['editEducationArr'])) {
    $edit = $this->popArr['editEducationArr'];
?>
	<input type="hidden" name="cmbEduCode" value="<?php echo $edit[0][1]?>" style="display:none" />
	<div id="editPaneEducation">
		<label><?php echo $lang_hrEmpMain_education?></label>
		<label id="educationLabel"><?php
				$allEduCodes = $this->popArr['allEduCodes'];
				for($c=0; $allEduCodes && count($allEduCodes)>$c; $c++) {
					if($allEduCodes[$c][0] == $edit[0][1]) {
						 echo CommonFunctions::escapeHtml($allEduCodes[$c][1]) . ", ". CommonFunctions::escapeHtml($allEduCodes[$c][2]);
					}
				}
			?>
		</label>
		<br />
		<label for="txtEmpEduMajor"><?php echo $lang_hrEmpMain_major; ?></label>
		<input type="text" name="txtEmpEduMajor" id="txtEmpEduMajor" maxlength="100"
			value="<?php echo CommonFunctions::escapeHtml($edit[0][2])?>" disabled="disabled" />
		<br />
		<label for="etxtEmpEduYear"><?php echo $lang_Leave_Common_Year?></label>
		<input type="text" name="txtEmpEduYear" id="etxtEmpEduYear"
			value="<?php echo CommonFunctions::escapeHtml($edit[0][3])?>"  disabled="disabled" />
		<br />
		<label for="txtEmpEduGPA"><?php echo $lang_hrEmpMain_gpa; ?></label>
		<input type="text" name="txtEmpEduGPA" id="txtEmpEduGPA" maxlength="25"
			value="<?php echo CommonFunctions::escapeHtml($edit[0][4])?>" disabled="disabled" />
		<br />
		<label for="etxtEmpEduStartDate"><?php echo $lang_hrEmpMain_startdate; ?></label>
		<input type="text" name="txtEmpEduStartDate" id="etxtEmpEduStartDate" disabled="disabled" style="float:left"
			value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][5]); ?>" />
		<input type="button" value="  " class="calendarBtn"  disabled="disabled" style="float:left" />
		<br />
		<label for="etxtEmpEduEndDate"><?php echo $lang_hrEmpMain_enddate?></label>
		<input type="text" name="txtEmpEduEndDate" id="etxtEmpEduEndDate" disabled="disabled" style="float:left"
			value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][6]); ?>" />
		<input type="button" value="  " class="calendarBtn" disabled="disabled" style="float:left" />
		<br /><br />
		<div class="formbuttons">
			<input type="button" class="editbutton" value="<?php echo $lang_Common_Edit; ?>" id="btnEditEducation"
				onmouseout="moutButton(this);" onmouseover="moverButton(this);"
				onclick="editEducation();" />
			<input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" disabled="disabled"
				onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
			<br />
		</div>
	</div>
<?php } ?>
<?php
$rset = $this->popArr['rsetEducation'] ;
$allEduCodes = $this->popArr['allEduCodes'];
?>
<?php
// Handling Table view hide or show depending on the records
if ($rset != null){?>
<div class="subHeading"><h3><?php echo $lang_hrEmpMain_assigneducation?></h3></div>
<div class="actionbar">
	<div class="actionbuttons">
		<input type="button" class="addbutton"
			onclick="showAddPane('Education');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
		<input type="button" class="delbutton"
			onclick="delEXTEducation();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>
	</div>
</div>
<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
	<thead>
	  <tr>
      	<td></td>
		 <td><?php echo $lang_hrEmpMain_education?></td>
		 <td><?php echo $lang_Leave_Common_Year?></td>
		 <td><?php echo $lang_hrEmpMain_gpa?></td>
	</tr>
	</thead>
	<tbody>
<?php
    for($c=0; $rset && $c < count($rset); $c++)
        {
			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';

            echo "<td><input type='checkbox' class='checkbox' name='chkedudel[]' value='" . $rset[$c][1] . "'/></td>";

            for($a=0; $allEduCodes && count($allEduCodes)>$a; $a++)
				if($allEduCodes[$a][0] == $rset[$c][1])
				   $lname = $allEduCodes[$a][1] . ", " .$allEduCodes[$a][2];

			?><td><a href="javascript:viewEducation('<?php echo $rset[$c][1]?>')"><?php echo CommonFunctions::escapeHtml($lname)?></a></td><?php
			echo '<td>'. CommonFunctions::escapeHtml($rset[$c][3]) .'</td>';
			echo '<td>'. CommonFunctions::escapeHtml($rset[$c][4]) .'</td>';

        echo '</tr>';
        }

?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
</div>
