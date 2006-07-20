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


require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>

<script language="JavaScript">
function alpha(txt)
{
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

function numeric(txt)
{
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

function edit()
{
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


function goBack() {
		location.href = "./CentralController.php?reqcode=<?=$this->getArr['reqcode']?>&VIEW=MAIN";
	}

function addEXT()
{
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

function editEXT()
{
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

function delEXT()
{
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?=$employerworkex?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmWrkExp" method="post" action="<?=$_SERVER['PHP_SELF']?>?reqcode=<?=$this->getArr['reqcode']?>&id=<?=$this->getArr['id']?><?=isset($this->getArr['editID']) ? '&editID=' .$this->getArr['editID'] : ''?>">

  <tr>
    <td valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>

 <?
$empdet = $this->popArr['empDet'];
?>

      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td><?=$employeeid?></td>
    				  <td width="75"><font color="#204242"><strong><?=$empdet[0][0]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
					  <td><?=$lastname?></td>
						<td width="300"><font color="#204242"><strong><?=$empdet[0][1]?></strong></font></td>
					</tr>
					  <tr> 
						<td><?=$firstname?></td>
						<td><font color="#204242"><strong><?=$empdet[0][2]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
						<td><?=$middlename?></td>
						<td><font color="#204242"><strong><?=$empdet[0][3]?></</font></td>
					  </tr>
                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
             <input type="hidden" name="txtEmpID" value="<?=$empdet[0][0]?>">

<?
if(isset($this->popArr['editArr']))
{
    $edit = $this->popArr['editArr'];
?>
    		 <input type="hidden" name="txtEmpExpID"  value="<?=isset($this->popArr['txtEmpExpID']) ? $this->popArr['txtEmpExpID'] : $edit[0][1]?>">

<table>
		<tr>
      		<td>      			
      			<?	if ($locRights['edit'] ){ ?>
        			<img title="Add" onClick="addNewEXT('<?php echo $empdet[0][0]; ?>');" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
				<? 	} ?>
			</td>
			<td></td>
			<td></td>
			<td></td>
      	</tr>
	  </table>	
<br>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
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
<?		
					if($locRights['edit']) { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
		<?			} else { ?>
						        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
				<?	} 
			 ?>
						</td>
					  </tr>
                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>

<? } else { ?>

 <?
$newid = $this->popArr['newID'];
?>

			 
             <input type="hidden" name="txtEmpExpID"  value="<?=$newid?>">
&nbsp;
         
<br><br>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
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
                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
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
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
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
                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe></body>
</html>