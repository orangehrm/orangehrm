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
function delConExt() {
      var check = false;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'chkconextdel[]') && (elements[i].checked == true)) {
					check = true;
				}
			}
        }

        if(!check) {
              alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
              return;
        }

    document.frmEmp.conextSTAT.value="DEL";
    qCombo(2);
}

function addConExt() {

	txtStartDate = document.frmEmp.txtEmpConExtStartDat.value;
	txtEndDate = document.frmEmp.txtEmpConExtEndDat.value;

	if(txtStartDate == '' || txtEndDate == '' || txtStartDate == 'YYYY-mm-DD' || txtEndDate == 'YYYY-mm-DD') {
		alert("<?php echo $lang_Error_EnterDate; ?>");
		return;
	}

	startDate = strToDate(document.getElementById('atxtEmpConExtStartDat').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('atxtEmpConExtEndDat').value, YAHOO.OrangeHRM.calendar.format);

	if(startDate >= endDate) {
		alert('<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>');
		return;
	}

  document.frmEmp.conextSTAT.value="ADD";
  qCombo(2);
}

function editConExt() {
	startDate = strToDate(document.getElementById('etxtEmpConExtStartDat').value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate(document.getElementById('etxtEmpConExtEndDat').value, YAHOO.OrangeHRM.calendar.format);

	if(startDate >= endDate) {
		alert("<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>");
		return;
	}

  document.frmEmp.conextSTAT.value="EDIT";
  qCombo(2);
}

function viewConExt(pSeq) {
	document.frmEmp.action = document.frmEmp.action + "&CONEXT=" + pSeq ;
	document.frmEmp.pane.value = 2;
	document.frmEmp.submit();
}
//--><!]]></script>
<div id="employeeContractLayer" <?php echo (!isset($this->popArr['rsetConExt']) || ($this->popArr['rsetConExt'] == null))?'style="display:none;"':''; ?> >
<script type="text/javaScript"><!--//--><![CDATA[//><!--
	toggleEmployeeContractsText();
//--><!]]></script>
<div id="parentPaneContracts" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<input type="hidden" name="conextSTAT" value=""/>
<?php if(isset($this->popArr['editConExtArr'])) {
        $edit = $this -> popArr['editConExtArr'];
?>
	<div id="editPaneContracts" >
	  <div class="subHeading"><h3><?php echo $lang_hremp_EmployeeContracts; ?></h3></div>
      <table style="height:80px" border="0" cellpadding="0" cellspacing="0">
      <tr>
          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?>
          	<input type="hidden" name="txtEmpConExtID" value="<?php echo CommonFunctions::escapeHtml($this->getArr['CONEXT'])?>"/>
          </td>
    	  <td>
    	  	<input class="formDateInput" type="text" name="txtEmpConExtStartDat" id="etxtEmpConExtStartDat" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][2]); ?>" size="10" />
    	  	<input type="button" value="  " class="calendarBtn" /></td>
	  </tr>
	  <tr>
		<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
		<td align="left" valign="top">
			<input class="formDateInput" type="text" name="txtEmpConExtEndDat" id="etxtEmpConExtEndDat" value="<?php echo LocaleUtil::getInstance()->formatDate($edit[0][3]); ?>" size="10" />
			<input type="button" value="  " class="calendarBtn" /></td>
	  </tr>
	 </table>
<?php	if($locRights['edit']) { ?>		
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnEditContract" id="btnEditContract" 
    	value="<?php echo $lang_Common_Save;?>" 
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
    	onclick="editConExt(); return false;"/>    	
</div>
<?php } ?>		 
   </div>
<?php } else { ?>
	<div id="addPaneContracts" class="<?php echo ($this->popArr['rsetConExt'] != null)?"addPane":""; ?>" >
		<div class="subHeading"><h3><?php echo $lang_hremp_EmployeeContracts; ?></h3></div>
	    <table style="height:80px" border="0" cellpadding="0" cellspacing="0">
	         <tr>
	          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?>
	          	<input type="hidden" name="txtEmpConExtID"  value="<?php echo CommonFunctions::escapeHtml($this->popArr['newConExtID'])?>"/>
	          </td>
			  <td>
			  	<input class="formDateInput" type="text" value="" name="txtEmpConExtStartDat" id="atxtEmpConExtStartDat" size="12" />
			  	<input <?php echo $locRights['add'] ? '':'disabled="disabled"'?> type="button" value="   " class="calendarBtn" /></td>
			</tr>
	  	  <tr>
			<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
			<td align="left" valign="top">
				<input class="formDateInput" type="text" value="" name="txtEmpConExtEndDat" id="atxtEmpConExtEndDat" size="12" />
				<input <?php echo $locRights['add'] ? '':'disabled="disabled"'?> type="button" value="   " class="calendarBtn" /></td>
		  </tr>
		</table>
<?php	if($locRights['add']) { ?>		
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddContract" id="btnAddContract" 
    	value="<?php echo $lang_Common_Save;?>" 
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
    	onclick="addConExt(); return false;"/>    	
</div>
<?php	} ?>		
	</div>
<?php } ?>

<?php
    $rset = $this->popArr['rsetConExt'];

    // check if there are any defined memberships
    if( $rset && count($rset) > 0 ){
        $assignedContracts = true;
    } else {
        $assignedContracts = false;
    }
?>
<?php if($assignedContracts) { ?>
	<div class="subHeading"><h3><?php echo $lang_hremp_AssignedContracts; ?></h3></div>
		<div class="actionbar">
			<div class="actionbuttons">						
<?php if($locRights['add']) { ?>
				<input type="button" class="addbutton"
					onclick="showAddPane('Contracts');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
					value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>			
<?php } ?>
<?php	if($locRights['delete']) { ?>
				<input type="button" class="delbutton"
					onclick="delConExt();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
					value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>			
		
<?php 	} ?>
			</div>
		</div>	
			
<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
	<thead>
	<tr>
		<td></td>
		<td><?php echo $lang_hremp_ContractExtensionId; ?></td>
		<td><?php echo $lang_hremp_ContractStartDate; ?></td>
		<td><?php echo $lang_hremp_ContractEndDate; ?></td>
	</tr>
	</thead>
	<tbody>
<?php
    for($c=0; $rset && $c < count($rset); $c++) {
		$cssClass = ($c%2) ? 'even' : 'odd'; 			
		echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chkconextdel[]' value='" . $rset[$c][1] ."'/></td>";
            ?> <td><a href="#" onmousedown="viewConExt(<?php echo $rset[$c][1]?>)" ><?php echo $rset[$c][1]?></a></td> <?php
            $dtfield = explode(" ",$rset[$c][2]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($dtfield[0]) .'</td>';
            $dtfield = explode(" ",$rset[$c][3]);
            echo '<td>' . LocaleUtil::getInstance()->formatDate($dtfield[0]) .'</td>';
                 echo '</tr>';
    }

?>
	</tbody>
	</table>
<?php } //if( $assignedContracts ) ?>

<?php } ?>
</div>
</div>
