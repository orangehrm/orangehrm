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
<script language="JavaScript">
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
	if(document.frmEmp.txtEmpConExtStartDat.value == '' || document.frmEmp.txtEmpConExtEndDat.value == '') {
		alert("<?php echo $lang_Error_EnterDate; ?>");
		return;
	}

	startDate = createDate(document.getElementById('atxtEmpConExtStartDat').value);
	endDate = createDate(document.getElementById('atxtEmpConExtEndDat').value);

	if(startDate >= endDate) {
		alert('<?php echo $lang_hremp_StaringDateShouldBeBeforeEnd; ?>');
		return;
	}

  document.frmEmp.conextSTAT.value="ADD";
  qCombo(2);
}

function editConExt() {
	startDate = createDate(document.getElementById('etxtEmpConExtStartDat').value);
	endDate = createDate(document.getElementById('etxtEmpConExtEndDat').value);

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
</script>
<div id="employeeContractLayer" <?php echo (!isset($this->popArr['rsetConExt']) || ($this->popArr['rsetConExt'] == null))?'style="display:none;"':''; ?> >
<script type="text/javascript">
	toggleEmployeeContractsText();
</script>
<span id="parentPaneContracts" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<input type="hidden" name="conextSTAT" value="">
<?php if(isset($this->popArr['editConExtArr'])) {
        $edit = $this -> popArr['editConExtArr'];
?>
	<div id="editPaneContracts" >
	  <h3><?php echo $lang_hremp_EmployeeContracts; ?></h3>
      <table height="80" border="0" cellpadding="0" cellspacing="0">
      <tr>
          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?>
          	<input type="hidden" name="txtEmpConExtID" value="<?php echo $this->getArr['CONEXT']?>">
          </td>
    	  <td>
    	  	<input type="text" readonly name="txtEmpConExtStartDat" id="etxtEmpConExtStartDat" value=<?php echo $edit[0][2]?> size="10" />
    	  	<input type="button" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('etxtEmpConExtStartDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
	  </tr>
	  <tr>
		<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
		<td align="left" valign="top">
			<input type="text" readonly name="txtEmpConExtEndDat" id="etxtEmpConExtEndDat" value=<?php echo $edit[0][3]?> size="10" />
			<input type="button" value="  " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('etxtEmpConExtEndDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
	  </tr>
	  <tr>
		<td valign="top"></td>
		<td align="left" valign="top">
		<?php			if($locRights['edit']) { ?>
					        <img border="0" title="Save" onClick="editConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
		<?php			}  ?>
		</td>
	  </tr>
	 </table>
   </div>
<?php } else { ?>
	<div id="addPaneContracts" class="<?php echo ($this->popArr['rsetConExt'] != null)?"addPane":""; ?>" >
		<h3><?php echo $lang_hremp_EmployeeContracts; ?></h3>
	    <table height="80" border="0" cellpadding="0" cellspacing="0">
	         <tr>
	          <td width="200"><?php echo $lang_hremp_ContractExtensionStartDate; ?>
	          	<input type="hidden" name="txtEmpConExtID"  value="<?php echo $this->popArr['newConExtID']?>">
	          </td>
			  <td>
			  	<input type="text" readonly value="0000-00-00" name="txtEmpConExtStartDat" id="atxtEmpConExtStartDat" size="12" />
			  	<input <?php echo $locRights['add'] ? '':'disabled'?> type="button" value="   " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('atxtEmpConExtStartDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
			</tr>
	  	  <tr>
			<td valign="top"><?php echo $lang_hremp_ContractExtensionEndDate; ?></td>
			<td align="left" valign="top">
				<input type="text" readonly value="0000-00-00" name="txtEmpConExtEndDat" id="atxtEmpConExtEndDat" size="12" />
				<input <?php echo $locRights['add'] ? '':'disabled'?> type="button" value="   " class="calendarBtn" onclick="YAHOO.OrangeHRM.calendar.pop('atxtEmpConExtEndDat', 'cal1Container', 'yyyy-MM-dd'); return false;"></td>
		  </tr>
		  <tr>
			<td valign="top"></td>
			<td align="left" valign="top">
				<?php	if($locRights['add']) { ?>
				        <img border="0" title="Save" onClick="addConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
				<?php	} ?>
			</td>
		  </tr>
		</table>
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
	<h3><?php echo $lang_hremp_AssignedContracts; ?></h3>
<?php if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="showAddPane('Contracts');" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" />
<?php } ?>
<?php	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delConExt();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} ?>
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tabForm">
	<tr>
		<td></td>
		<td><strong><?php echo $lang_hremp_ContractExtensionId; ?></strong></td>
		<td><strong><?php echo $lang_hremp_ContractStartDate; ?></strong></td>
		<td><strong><?php echo $lang_hremp_ContractEndDate; ?></strong></td>
	</tr>
<?php
    for($c=0; $rset && $c < count($rset); $c++) {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkconextdel[]' value='" . $rset[$c][1] ."'></td>";
            ?> <td><a href="#" onmousedown="viewConExt(<?php echo $rset[$c][1]?>)" ><?php echo $rset[$c][1]?></a></td> <?php
            $dtfield = explode(" ",$rset[$c][2]);
            echo '<td>' . $dtfield[0] .'</td>';
            $dtfield = explode(" ",$rset[$c][3]);
            echo '<td>' . $dtfield[0] .'</td>';
                 echo '</tr>';
    }

?>
	</table>
<?php } //if( $assignedContracts ) ?>

<?php } ?>
</span>
</div>
