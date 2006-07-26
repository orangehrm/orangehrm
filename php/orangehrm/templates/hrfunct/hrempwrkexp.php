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
function alpha(txt) {
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)
	{
	code=txt.value.charCodeAt(i);
    if((code>=65 && code<=122) || code==32 || code==46)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function numeric(txt) {
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)
	{
	code=txt.value.charCodeAt(i);
    if(code>=48 && code<=57)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function edit() {
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmWrkExp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));
		
		var tempDate = new Date(yy,mm,dd);
		
		return tempDate;
}

function addEXT() {
 	var txt = document.frmWrkExp.txtEmpExpDesOnLev;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

    var txt = document.frmWrkExp.txtEmpResLev;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

 
	var fromDate = createDate(document.frmWrkExp.txtEmpExpFromDat.value)
	var toDate = createDate(document.frmWrkExp.txtEmpExpToDat.value);
	
	if(fromDate >= toDate){
		alert("From Date should be before To date");
		return;
	}
	
  document.frmWrkExp.STAT.value="ADD";
  document.frmWrkExp.submit();
}

function calcYearMonth() {
	
	if(document.frmWrkExp.txtEmpExpFromDat.value == '') {
		alert("Enter From Date first");
		return;
	}
	var fromDate = createDate(document.frmWrkExp.txtEmpExpFromDat.value)
	var toDate = createDate(document.frmWrkExp.txtEmpExpToDat.value);
	
	var diffMs = toDate.getTime() - fromDate.getTime();

	var oneMonth = 1000*60*60*24*30;
	var oneYear = oneMonth * 12;
	
	var eYears = diffMs / oneYear;
	var eMonth = diffMs % oneYear;
	
	eMonth = eMonth / oneMonth;
	
	var str = eMonth.toString();
	document.frmWrkExp.txtEmpExpMonths.value = str.substr(0,str.indexOf('.'));
	str = eYears.toString();
	document.frmWrkExp.txtEmpExpYears.value = str.substr(0,str.indexOf('.'));
}

function editEXT() {
 	var txt = document.frmWrkExp.txtEmpExpDesOnLev;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

    var txt = document.frmWrkExp.txtEmpResLev;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}


	var fromDate = createDate(document.frmWrkExp.txtEmpExpFromDat.value)
	var toDate = createDate(document.frmWrkExp.txtEmpExpToDat.value);
	
	if(fromDate >= toDate){
		alert("From Date should be before To date");
		return;
	}

  document.frmWrkExp.STAT.value="EDIT";
  document.frmWrkExp.submit();
}

function delEXT() {
      var check = 0;
		with (document.frmWrkExp) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
            {
              alert("Select atleast one check box");
              return;
            }


    //alert(cntrl.value);
    document.frmWrkExp.STAT.value="DEL";
    document.frmWrkExp.submit();
}

function addNewEXT(str){
	var EmpID = str;		
	location.href = "./CentralController.php?id="+EmpID+"&capturemode=updatemode&reqcode=<?=$this->getArr['reqcode']?>";
}
</script>
<? if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

    <input type="hidden" name="STAT" value="">

<?
if(isset($this->popArr['editArr'])) {
    $edit = $this->popArr['editArr'];
?>
    		 <input type="hidden" name="txtEmpExpID"  value="<?=isset($this->popArr['txtEmpExpID']) ? $this->popArr['txtEmpExpID'] : $edit[0][1]?>">

      <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><?=$employer?></td>
    				  <td><input type="text" name="txtEmpExpCompany" <?=isset($this->popArr['txtEmpExpCompany']) ? '':'disabled'?> value="<?=isset($this->popArr['txtEmpExpCompany']) ? $this->popArr['txtEmpExpCompany'] : $edit[0][2]?>"></td>
    				  <td width="50">&nbsp;</td>
					<td><?=$startdate?></td>
						<td> <input type="text" readonly name="txtEmpExpFromDat"  <?=isset($this->popArr['txtEmpExpFromDat']) ? '':'disabled'?>  value=<?=isset($this->popArr['txtEmpExpFromDat']) ? $this->popArr['txtEmpExpFromDat'] : $edit[0][8]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpFromDat);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$jobtitle?></td>
						<td> <input type="text" name="txtEmpExpDesOnLev" <?=isset($this->popArr['txtEmpExpDesOnLev']) ? '':'disabled'?>  value="<?=isset($this->popArr['txtEmpExpDesOnLev']) ? $this->popArr['txtEmpExpDesOnLev'] : $edit[0][6]?>"></td>
    				  <td width="50">&nbsp;</td>
					  </tr>
					  <tr>
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpExpToDat" <?=isset($this->popArr['txtEmpExpToDat']) ? '':'disabled'?> readonly value="<?=isset($this->popArr['txtEmpExpToDat']) ? $this->popArr['txtEmpExpToDat'] : $edit[0][9]?>">&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpToDat);return false;"></td>
						<td><?=$briefdes?></td>
						<td> <textarea <?=isset($this->popArr['txtEmpResLev']) ? '':'disabled'?>  name="txtEmpResLev"><?=isset($this->popArr['txtEmpResLev']) ? $this->popArr['txtEmpResLev'] : $edit[0][12]?></textarea></td>
    				  <td width="50">&nbsp;</td>
					 </tr>
					 <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
		<?		if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
		<?		} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?		} 	 ?>
						</td>
					  </tr>
                  </table>

<? } else { ?>

<? $newid = $this->popArr['newID']; ?>
<input type="hidden" name="txtEmpExpID"  value="<?=$newid?>">

			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$employer?></td>
    				  <td><input type="text" name="txtEmpExpCompany" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($this->popArr['txtEmpExpCompany']) ? $this->popArr['txtEmpExpCompany'] :''?>"></td>
    				  <td width="50">&nbsp;</td>
					<td><?=$startdate?></td>
						<td> <input type="text" name="txtEmpExpFromDat" readonly value="<?=isset($this->popArr['txtEmpExpFromDat']) ?$this->popArr['txtEmpExpFromDat'] :''?>">&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpFromDat);return false;"></td>
					</tr>
					  <tr> 
						<td><?=$jobtitle?></td>
						<td> <input type="text" name="txtEmpExpDesOnLev" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($this->popArr['txtEmpExpDesOnLev']) ? $this->popArr['txtEmpExpDesOnLev'] :''?>"></td>
    				  <td width="50">&nbsp;</td>
						<td><?=$enddate?></td>
						<td> <input type="text" name="txtEmpExpToDat"  readonly onchange="calcYearMonth();" value="<?=isset($this->popArr['txtEmpExpToDat']) ?$this->popArr['txtEmpExpToDat'] :''?>">&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpToDat);return false;"></td>
    				  <td width="50">&nbsp;</td>
    				   </tr>
					  <tr>
					<td><?=$briefdes?></td>
						<td> <textarea <?=$locRights['add'] ? '':'disabled'?> name="txtEmpResLev"><?=isset($this->popArr['txtEmpResLev']) ? $this->popArr['txtEmpResLev'] :''?></textarea></td>
    				  <td width="50">&nbsp;</td>
						 </tr>
					  
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
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
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXT();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
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

$rset = $this->popArr['rset'];


    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."'></td>";
            echo "<td><a href='".$_SERVER['PHP_SELF']. "?reqcode=" .$this->getArr['reqcode'] . "&id=" . $this->getArr['id']. "&editID=" . $rset[$c][1] . "'>Exp " . $rset[$c][1] . "</a></td>";
            echo '<td>' . $rset[$c][2] .'</td>';
            $str = explode(" ",$rset[$c][3]);
            echo '<td>' . $str[0] .'</td>';
            $str = explode(" ",$rset[$c][4]);
            echo '<td>' . $str[0] .'</td>';
        echo '</tr>';
        }

?>
      </table>
      
<? } ?>