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

	if(document.EditEducation.title=='Save') {
		editEXTEducation();
		return;
	}

	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;

	document.EditEducation.src="../../themes/beyondT/pictures/btn_save.gif";
	document.EditEducation.title="Save";
}

function moutEducation() {
	if(document.EditEducation.title=='Save')
		document.EditEducation.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		document.EditEducation.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function moverEducation() {
	if(document.EditEducation.title=='Save')
		document.EditEducation.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		document.EditEducation.src='../../themes/beyondT/pictures/btn_edit_02.gif';
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
<div id="parentPaneEducation" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
    <input type="hidden" name="educationSTAT" value=""/>
   	<div id="addPaneEducation" class="<?php echo ($this->popArr['rsetEducation'] != null)?"addPane":""; ?>" >
    	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?php echo $lang_hrEmpMain_education?></td>
    				  <td><select class="formSelect" name="cmbEduCode">
    				  		<option selected="selected" value="0">--<?php echo $lang_hrEmpMain_SelectEducation; ?>--</option>
						<?php	$unAssEduCodes = $this->popArr['unAssEduCodes'];
							for($c=0; $unAssEduCodes && count($unAssEduCodes)>$c; $c++)
								echo "<option value='" .$unAssEduCodes[$c][0] . "'>" .$unAssEduCodes[$c][1]. ", ".$unAssEduCodes[$c][2]. "</option>";
						 ?>
					  </select></td>
					</tr>
                    <tr>
                      <td><?php echo $lang_hrEmpMain_major?></td>
    				  <td><input class="formInputText" type="text" name="txtEmpEduMajor"/></td>
					</tr>
					 <tr>
					<td><?php echo $lang_Leave_Common_Year?></td>
					   <td><input class="formInputText" type="text" name="txtEmpEduYear" id="atxtEmpEduYear" /></td>
					 </tr>
					 <tr>
					<td><?php echo $lang_hrEmpMain_gpa?></td>
						<td> <input class="formInputText" type="text" name="txtEmpEduGPA"/></td>
					 </tr>
					<tr>
					<td><?php echo $lang_hrEmpMain_startdate?></td>
						<td>
							<input class="formDateInput" type="text" name="txtEmpEduStartDate" id="atxtEmpEduStartDate" value="" size="10" />
							<input type="button" value="  " class="calendarBtn" /></td>
					</tr>
					  <tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input class="formDateInput" type="text" name="txtEmpEduEndDate" id="atxtEmpEduEndDate" value="" size="10" />
							<input type="button" value="  " class="calendarBtn" /></td>
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
</div>					
	</div>
<?php
if(isset($this->popArr['editEducationArr'])) {
    $edit = $this->popArr['editEducationArr'];
?>
	<div id="editPaneEducation" >
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?php echo $lang_hrEmpMain_education?></td>
    				  <td><input type="hidden" name="cmbEduCode" value="<?php echo $edit[0][1]?>"/>
						<?php	$allEduCodes = $this->popArr['allEduCodes'];
							for($c=0; $allEduCodes && count($allEduCodes)>$c; $c++)
								if($allEduCodes[$c][0] == $edit[0][1])
									 echo $allEduCodes[$c][1] . ", ". $allEduCodes[$c][2];
									 ?>
					  </select></td>

					</tr>
                    <tr>
                      <td><?php echo $lang_hrEmpMain_major?></td>
    				  <td><input type="text" name="txtEmpEduMajor" value="<?php echo $edit[0][2]?>"/></td>
					</tr>
					 <tr>
					<td><?php echo $lang_Leave_Common_Year?></td>
						<td> <input type="text" name="txtEmpEduYear" id="etxtEmpEduYear" value="<?php echo $edit[0][3]?>"/></td>
					 </tr>
					 <tr>
					<td><?php echo $lang_hrEmpMain_gpa?></td>
						<td> <input type="text" name="txtEmpEduGPA" value="<?php echo $edit[0][4]?>"/></td>
					 </tr>
					<tr>
					<td><?php echo $lang_hrEmpMain_startdate?></td>
						<td>
							<input type="text" name="txtEmpEduStartDate" id="etxtEmpEduStartDate" value=<?php echo LocaleUtil::getInstance()->formatDate($edit[0][5]); ?> size="10" />
							<input type="button" value="  " class="calendarBtn" /></td>
					</tr>
					  <tr>
						<td><?php echo $lang_hrEmpMain_enddate?></td>
						<td>
							<input type="text" name="txtEmpEduEndDate" id="etxtEmpEduEndDate" value=<?php echo LocaleUtil::getInstance()->formatDate($edit[0][6]); ?> size="10" />
							<input type="button" value="  " class="calendarBtn" /></td>
					 </tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						        <img src="../../themes/beyondT/pictures/btn_save.gif" alt="" title="Save" onmouseout="moutEducation();" onmouseover="moverEducation();" name="EditEducation" onclick="editEducation();">
						</td>
					  </tr>
		</table>
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

			?><td><a href="javascript:viewEducation('<?php echo $rset[$c][1]?>')"><?php echo $lname?></a></td><?php
			echo '<td>'. $rset[$c][3] .'</td>';
			echo '<td>'. $rset[$c][4] .'</td>';

        echo '</tr>';
        }

?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
</div>
