<?
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/
?>

<script language="JavaScript">

function editWrkExp() {
	
	if(document.EditWrkExp.title=='Save') {
		editEXTWrkExp();
		return;
	}
	
	var frm=document.frmEmp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
		
	document.EditWrkExp.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.EditWrkExp.title="Save";
}

function moutWrkExp() {
	if(document.EditWrkExp.title=='Save') 
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function moverWrkExp() {
	if(document.EditWrkExp.title=='Save') 
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.EditWrkExp.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
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
		alert ("Field Empty!");
		txt.focus();
		return false;
	}

    var txt = document.frmEmp.txtEmpExpJobTitle;
	if (txt.value == '') {
		alert ("Field Empty!");
		txt.focus();
		return false;
	}

	var fromDate = createDate(document.frmEmp.txtEmpExpFromDate.value)
	var toDate = createDate(document.frmEmp.txtEmpExpToDate.value);
	
	if(fromDate >= toDate){
		alert("From Date should be before To date");
		return;
	}
	
  document.frmEmp.wrkexpSTAT.value="ADD";
  qCombo(17);
}

function calcYearMonth() {
	
	if(document.frmEmp.txtEmpExpFromDat.value == '') {
		alert("Enter From Date first");
		return;
	}
	var fromDate = createDate(document.frmEmp.txtEmpExpFromDat.value)
	var toDate = createDate(document.frmEmp.txtEmpExpToDat.value);
	
	var diffMs = toDate.getTime() - fromDate.getTime();

	var oneMonth = 1000*60*60*24*30;
	var oneYear = oneMonth * 12;
	
	var eYears = diffMs / oneYear;
	var eMonth = diffMs % oneYear;
	
	eMonth = eMonth / oneMonth;
	
	var str = eMonth.toString();
	document.frmEmp.txtEmpExpMonths.value = str.substr(0,str.indexOf('.'));
	str = eYears.toString();
	document.frmEmp.txtEmpExpYears.value = str.substr(0,str.indexOf('.'));
}

function editEXTWrkExp() {
	
 	var txt = document.frmEmp.txtEmpExpEmployer;
	if (txt.value == '') {
		alert ("Field Empty!");
		txt.focus();
		return false;
	}

    var txt = document.frmEmp.txtEmpExpJobTitle;
	if (txt.value == '') {
		alert ("Field Empty!");
		txt.focus();
		return false;
	}

	var fromDate = createDate(document.frmEmp.txtEmpExpFromDate.value)
	var toDate = createDate(document.frmEmp.txtEmpExpToDate.value);
	
	if(fromDate >= toDate){
		alert("From Date should be before To date");
		return;
	}

  document.frmEmp.wrkexpSTAT.value="EDIT";
  qCombo(17);
}

function delEXTWrkExp() {
	
      var check = 0;
		with (document.frmEmp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0) {
              alert("Select atleast one check box");
              return;
        }


    //alert(cntrl.value);
    document.frmEmp.wrkexpSTAT.value="DEL";
    qCombo(17);
}

function viewWrkExp(wrkexp) {
	
	document.frmEmp.action = document.frmEmp.action + "&WRKEXP=" + wrkexp;
	document.frmEmp.pane.value = 17;
	document.frmEmp.submit();
}

</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

    <input type="hidden" name="wrkexpSTAT" value="">

<?
if(isset($this->popArr['editWrkExpArr'])) {
    $edit = $this->popArr['editWrkExpArr'];
?>
    		 <input type="hidden" name="txtEmpExpID" value="<?=$this->getArr['WRKEXP']?>">

      <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><?=$employer?></td>
    				  <td><input type="text" name="txtEmpExpEmployer" disabled value="<?=$edit[0][2]?>"></td>
    				  <td width="50">&nbsp;</td>
					<td><?=$startdate?></td>
						<td> <input type="text" readonly name="txtEmpExpFromDate" value=<?=$edit[0][4]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpExpFromDate);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$jobtitle?></td>
						<td> <input type="text" disabled name="txtEmpExpJobTitle" value="<?=$edit[0][3]?>"></td>
    				  <td width="50">&nbsp;</td>
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpExpToDate" readonly value=<?=$edit[0][5]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpExpToDate);return false;"></td>
					  </tr>
					  <tr>
						<td><?=$briefdes?></td>
						<td> <textarea disabled name="txtEmpExpComments"><?=$edit[0][6]?></textarea></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?		if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="moutWrkExp();" onmouseover="moverWrkExp();" name="EditWrkExp" onClick="editWrkExp();">
		<?		} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?		} 	 ?>
						</td>
					  </tr>
                  </table>

<? } else { ?>

		<input type="hidden" name="txtEmpExpID"  value="<?=$this->popArr['newID']?>">

			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$employer?></td>
    				  <td><input type="text" name="txtEmpExpEmployer" <?=$locRights['add'] ? '':'disabled'?>></td>
    				  <td width="50">&nbsp;</td>
					<td><?=$startdate?></td>
						<td> <input type="text" name="txtEmpExpFromDate" readonly>&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpExpFromDate);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$jobtitle?></td>
						<td> <input type="text" name="txtEmpExpJobTitle" <?=$locRights['add'] ? '':'disabled'?>></td>
    				  <td width="50">&nbsp;</td>
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpExpToDate" readonly>&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmp.txtEmpExpToDate);return false;"></td>
    				  <td width="50">&nbsp;</td>
    				   </tr>
					  <tr>
					<td><?=$briefdes?></td>
						<td> <textarea <?=$locRights['add'] ? '':'disabled'?> name="txtEmpExpComments"></textarea></td>
    				  <td width="50">&nbsp;</td>
						 </tr>
					  
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXTWrkExp();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
					  </tr>
                  </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=$assignworkex?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
   <td>
<?	if($locRights['add']) { ?>
		<img border="0" title="Add" onClick="resetAdd(17);" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
					<? 	} else { ?>
		<img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg"
<? } ?>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXTWrkExp();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>

	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      	<td></td>
						 <td width="125"><strong><?=$workexid?></strong></td>
						 <td width="135"><strong><?=$employer?></strong></td>
						 <td width="125"><strong><?=$startdate?></strong></td>
						 <td width="125"><strong><?=$enddate?></strong></td>
					</tr>
<?

$rset = $this->popArr['rsetWrkExp'];

    for($c=0; $rset && $c < count($rset); $c++) {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkwrkexpdel[]' value='" . $rset[$c][1] ."'></td>";
            ?><td><a href="javascript:viewWrkExp('<?=$rset[$c][1]?>')"><?=$rset[$c][1]?></a></td><?
            echo '<td>' . $rset[$c][2] .'</td>';
            $str = explode(" ",$rset[$c][4]);
            echo '<td>' . $str[0] .'</td>';
            $str = explode(" ",$rset[$c][5]);
            echo '<td>' . $str[0] .'</td>';
        echo '</tr>';
        }
?>
      </table>
      
<? } ?>