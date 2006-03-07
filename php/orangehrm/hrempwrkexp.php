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

session_start();
if(!isset($_SESSION['fname'])) { 

	header("Location: ./relogin.htm");
	exit();
}

define('OpenSourceEIM', dirname(__FILE__));
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpWorkExp.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$empinfo = new EmpInfo();
	$empdet = $empinfo->filterEmpMain($_GET['id']);

	$empwrkexp= new EmpWorkExp();

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
    $empwrkexp->setEmpId($_GET['id']);
    $empwrkexp->setEmpExpSeqNo($_POST['txtEmpExpID']);
    $empwrkexp->setEmpExpCompany(trim($_POST['txtEmpExpCompany']));
    $empwrkexp->setEmpExpAdd1(trim($_POST['txtEmpExpAdd1']));
    $empwrkexp->setEmpExpAdd2(trim($_POST['txtEmpExpAdd2']));
    $empwrkexp->setEmpExpAdd3(trim($_POST['txtEmpExpAdd3']));
    $empwrkexp->setEmpExpDesOnLev(trim($_POST['txtEmpExpDesOnLev']));
    $empwrkexp->setEmpExpWorkRelFlag((isset($_POST['chkEmpExpWorkRelFlag']))?'1':'0');
    $empwrkexp->setEmpExpFromDat(trim($_POST['txtEmpExpFromDat']));
    $empwrkexp->setEmpExpToDat(trim($_POST['txtEmpExpToDat']));
    $empwrkexp->setEmpExpYears(trim($_POST['txtEmpExpYears']));
    $empwrkexp->setEmpExpMonths(trim($_POST['txtEmpExpMonths']));
    $empwrkexp->setEmpResLev(trim($_POST['txtEmpResLev']));
    $empwrkexp->setEmpExpConPers(trim($_POST['txtEmpExpConPers']));
    $empwrkexp->setEmpExpTelep(trim($_POST['txtEmpExpTelep']));
    $empwrkexp->setEmpExpEmail(trim($_POST['txtEmpExpEmail']));
    $empwrkexp->setEmpExpAcc(trim($_POST['txtEmpExpAcc']));
    $empwrkexp->setEmpExpAchmnt(trim($_POST['txtEmpExpAchmnt']));
    $res=$empwrkexp->addEmpWorkExp();
    
    if($res)
       header("Location: ./hrempwrkexp.php?id=" .$_GET['id']. "&reqcode=" .$_GET['reqcode']. "&pageID=".$_GET['pageID']);
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
   $empwrkexp->setEmpId($_GET['id']);
    $empwrkexp->setEmpExpSeqNo($_POST['txtEmpExpID']);
    $empwrkexp->setEmpExpCompany(trim($_POST['txtEmpExpCompany']));
    $empwrkexp->setEmpExpAdd1(trim($_POST['txtEmpExpAdd1']));
    $empwrkexp->setEmpExpAdd2(trim($_POST['txtEmpExpAdd2']));
    $empwrkexp->setEmpExpAdd3(trim($_POST['txtEmpExpAdd3']));
    $empwrkexp->setEmpExpDesOnLev(trim($_POST['txtEmpExpDesOnLev']));
    $empwrkexp->setEmpExpWorkRelFlag(isset($_POST['chkEmpExpWorkRelFlag'])?'1':'0');
    $empwrkexp->setEmpExpFromDat(trim($_POST['txtEmpExpFromDat']));
    $empwrkexp->setEmpExpToDat(trim($_POST['txtEmpExpToDat']));
    $empwrkexp->setEmpExpYears(trim($_POST['txtEmpExpYears']));
    $empwrkexp->setEmpExpMonths(trim($_POST['txtEmpExpMonths']));
    $empwrkexp->setEmpResLev(trim($_POST['txtEmpResLev']));
    $empwrkexp->setEmpExpConPers(trim($_POST['txtEmpExpConPers']));
    $empwrkexp->setEmpExpTelep(trim($_POST['txtEmpExpTelep']));
    $empwrkexp->setEmpExpEmail(trim($_POST['txtEmpExpEmail']));
    $empwrkexp->setEmpExpAcc(trim($_POST['txtEmpExpAcc']));
    $empwrkexp->setEmpExpAchmnt(trim($_POST['txtEmpExpAchmnt']));
    $res=$empwrkexp->updateEmpWorkExp();

    if($res)
       header("Location: ./hrempwrkexp.php?id=" .$_GET['id']. "&reqcode=" .$_GET['reqcode']. "&pageID=".$_GET['pageID']);
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='DEL')
   {
   $arr[1]=$_POST['chkdel'];
   for($c=0;count($arr[1])>$c;$c++)
       if($arr[1][$c]!=NULL)
	      $arr[0][$c]=$_GET['id'];
		  
   $empwrkexp->delEmpWorkExp($arr);
   }
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

<? if(isset($_GET['WRKEXP'])) { ?>

function reloadEdit() {
	document.frmWrkExp.action="./hrempwrkexp.php?reqcode=<?=$_GET['reqcode']?>&id=<?=$_GET['id']?>&WRKEXP=<?=$_GET['WRKEXP']?>";
	document.frmWrkExp.submit();
}
<? } ?>
function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmWrkExp;
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit_02.jpg'; 
}

function createDate(str) {
		var yy=eval(str.substr(0,4));
		var mm=eval(str.substr(5,2)) - 1;
		var dd=eval(str.substr(8,2));
		
		var tempDate = new Date(yy,mm,dd);
		
		return tempDate;
}


function goBack() {
		location.href = "empview.php?reqcode=<?=$_GET['reqcode']?>";
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

    var txt = document.frmWrkExp.txtEmpExpConPers;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

    var txt = document.frmWrkExp.txtEmpExpTelep;
	if (!numeric(txt)) {
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

    var txt = document.frmWrkExp.txtEmpExpConPers;
	if (!alpha(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

    var txt = document.frmWrkExp.txtEmpExpTelep;
	if (!numeric(txt)) {
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

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2>Employee Work Experience</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmWrkExp" method="post" action="./hrempwrkexp.php?reqcode=<?=$_GET['reqcode']?>&id=<?=$_GET['id']?>">
<input type="hidden" name="pageID" value="">
  <tr>
    <td valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>

      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td>Employee ID</td>
    				  <td width="75"><font color="#204242"><strong><?=$_GET['id']?></strong></font></td>
    				  <td width="50">&nbsp;</td>
					  <td>Surname</td>
						<td width="300"><font color="#204242"><strong><?=$empdet[0][3]?></strong></font></td>
					</tr>
					  <tr> 
						<td>Calling Name</td>
						<td><font color="#204242"><strong><?=$empdet[0][2]?></strong></font></td>
    				  <td width="50">&nbsp;</td>
						<td>Initials</td>
						<td><font color="#204242"><strong><?=$empdet[0][5]?></</font></td>
					  </tr>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>



<?
if(isset($_GET['WRKEXP']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['WRKEXP'];
    $edit=$empwrkexp->filterEmpWorkExp($arr);
?>

         <input type="hidden" name="txtEmpID" value="<?=$_GET['id']?>">
         <input type="hidden" name="txtEmpExpID"  value="<?=$_GET['WRKEXP']?>">
<br><br>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td>Company</td>
    				  <td><input type="text" name="txtEmpExpCompany" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpCompany']) ? $_POST['txtEmpExpCompany'] : $edit[0][2]?>"></td>
    				  <td width="50">&nbsp;</td>
						<td>To Date</td>
						<td> <input type="text" name="txtEmpExpToDat" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?> readonly value="<?=isset($_POST['txtEmpExpToDat']) ? $_POST['txtEmpExpToDat'] : $edit[0][9]?>">&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpToDat);return false;"></td>
					</tr>
					  <tr> 
						<td>Designation on Leave</td>
						<td> <input type="text" name="txtEmpExpDesOnLev" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  value="<?=isset($_POST['txtEmpExpDesOnLev']) ? $_POST['txtEmpExpDesOnLev'] : $edit[0][6]?>"></td>
    				  <td width="50">&nbsp;</td>
					<td>Years</td>
					<td> <input type="text" name="txtEmpExpYears" readonly <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpYears']) ? $_POST['txtEmpExpYears'] : $edit[0][10]?>"></td>
					  </tr>
					  <tr>
						<td>Work Related</td>
						<td> <input type="checkbox" name="chkEmpExpWorkRelFlag" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  value="1" <?=($edit[0][7]=='1'?'checked':'')?>></td>
    				  <td width="50">&nbsp;</td>
						<td>Month</td>
						<td> <input type="text" name="txtEmpExpMonths" readonly <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  value="<?=isset($_POST['txtEmpExpMonths']) ? $_POST['txtEmpExpMonths'] : $edit[0][11]?>"></td>
					  </tr>
					  <tr>
						<td>Address</td>
						<td> <textarea <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  name="txtEmpExpAdd1"><?=isset($_POST['txtEmpExpAdd1']) ? $_POST['txtEmpExpAdd1'] : $edit[0][3]?></textarea></td>
    				  <td width="50">&nbsp;</td>
						<td>Accountabilities</td>
						<td> <textarea <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  name="txtEmpExpAcc"><?=isset($_POST['txtEmpExpAcc']) ? $_POST['txtEmpExpAcc'] : $edit[0][16]?></textarea></td>
					  </tr>
					  <tr>
						<td>Country</td>
						<td> <select <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  onchange="reloadEdit();" name="txtEmpExpAdd2">
								<option>-----Select Country------</option>
<?				$list=$empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['txtEmpExpAdd2'])) {
						if($_POST['txtEmpExpAdd2']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					} elseif($edit[0][4]==$list[$c][0])
						echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";					
?>			 
					</select></td>
    				  <td width="50">&nbsp;</td>
						<td>Contact Person</td>
						<td> <input type="text" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  name="txtEmpExpConPers" value="<?=isset($_POST['txtEmpExpConPers']) ? $_POST['txtEmpExpConPers'] : $edit[0][13]?>"></td>
					  </tr>
					  <tr>
						<td>State</td>
						<td> <select <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  name="txtEmpExpAdd3"> 
								<option>-------Select State-------</option>
<?			if(isset($_POST['txtEmpExpAdd2'])) {
				$plist=$empinfo->getProvinceCodes($_POST['txtEmpExpAdd2']);
				for($c=0;$plist && count($plist)>$c;$c++)
					if(isset($_POST['txtEmpExpAdd3'])) {
						if($_POST['txtEmpExpAdd3']==$plist[$c][1])
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					} elseif ($edit[0][5]==$plist[$c][1]) 
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				} else {
						$plist=$empinfo->getProvinceCodes($edit[0][4]);
						for($c=0;count($plist)>$c;$c++)
						if ($edit[0][5]==$plist[$c][1]) 
						    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
						else
						    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
						</select></td>
    				  <td width="50">&nbsp;</td>
						<td>Telephone</td>
						<td> <input type="text" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?> name="txtEmpExpTelep" value="<?=isset($_POST['txtEmpExpTelep']) ? $_POST['txtEmpExpTelep'] : $edit[0][14]?>"></td>
					  </tr>
					  <tr>
						<td>Reason for Leaving</td>
						<td> <textarea <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  name="txtEmpResLev"><?=isset($_POST['txtEmpResLev']) ? $_POST['txtEmpResLev'] : $edit[0][12]?></textarea></td>
    				  <td width="50">&nbsp;</td>
						<td>Acheivements</td>
						<td> <textarea <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?> name="txtEmpExpAchmnt"><?=isset($_POST['txtEmpExpCompany']) ? $_POST['txtEmpExpCompany'] : $edit[0][17]?></textarea></td>
					  </tr>
					  <tr>
						<td>From Date</td>
						<td> <input type="text" readonly name="txtEmpExpFromDat"  <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  value=<?=isset($_POST['txtEmpExpFromDat']) ? $_POST['txtEmpExpFromDat'] : $edit[0][8]?>>&nbsp;<input disabled type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpFromDat);return false;"></td>
    				  <td width="50">&nbsp;</td>
						<td>Email</td>
						<td> <input type="text" name="txtEmpExpEmail" <?=isset($_POST['txtEmpExpAdd2']) ? '':'disabled'?>  value="<?=isset($_POST['txtEmpExpEmail']) ? $_POST['txtEmpExpEmail'] : $edit[0][15]?>"></td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?		if(isset($_POST['txtEmpExpAdd2'])) { ?>
			        <img border="0" title="Save" onClick="editEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<?			} else {
					if($locRights['edit']) { ?>
						        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
		<?			} else { ?>
						        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			} 
			} ?>
						</td>
					  </tr>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>

<? } else { ?>
&nbsp;
         <input type="hidden" name="txtEmpID" value="<?=$_GET['id']?>">
         <input type="hidden" name="txtEmpExpID"  value="<?=$empwrkexp->getLastRecord($_GET['id'])?>">
<br><br>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td>Company</td>
    				  <td><input type="text" name="txtEmpExpCompany" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpCompany']) ? $_POST['txtEmpExpCompany'] :''?>"></td>
    				  <td width="50">&nbsp;</td>
						<td>To Date</td>
						<td> <input type="text" name="txtEmpExpToDat"  readonly onchange="calcYearMonth();" value="<?=isset($_POST['txtEmpExpToDat']) ? $_POST['txtEmpExpToDat'] :''?>">&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpToDat);return false;"></td>
					</tr>
					  <tr> 
						<td>Designation on Leave</td>
						<td> <input type="text" name="txtEmpExpDesOnLev" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpDesOnLev']) ? $_POST['txtEmpExpDesOnLev'] :''?>"></td>
    				  <td width="50">&nbsp;</td>
					<td>Years</td>
					<td> <input type="text" readonly name="txtEmpExpYears" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpYears']) ? $_POST['txtEmpExpYears'] :''?>"></td>
					  </tr>
					  <tr>
						<td>Work Related</td>
						<td> <input type="checkbox" name="chkEmpExpWorkRelFlag" <?=$locRights['add'] ? '':'disabled'?> value="1" <?=isset($_POST['chkEmpExpWorkRelFlag']) ? 'checked' :''?>></td>
    				  <td width="50">&nbsp;</td>
						<td>Month</td>
						<td> <input type="text" readonly name="txtEmpExpMonths" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpMonths']) ? $_POST['txtEmpExpMonths'] :''?>"></td>
					  </tr>
					  <tr>
						<td>Address</td>
						<td> <textarea name="txtEmpExpAdd1" <?=$locRights['add'] ? '':'disabled'?> cols="25"><?=isset($_POST['txtEmpExpAdd1']) ? $_POST['txtEmpExpAdd1'] :''?></textarea></td>
    				  <td width="50">&nbsp;</td>
						<td>Accountabilities</td>
						<td> <textarea name="txtEmpExpAcc" <?=$locRights['add'] ? '':'disabled'?> cols="25"><?=isset($_POST['txtEmpExpAcc']) ? $_POST['txtEmpExpAcc'] :''?></textarea></td>
					  </tr>
					  <tr>
						<td>Country</td>
						<td> <select onchange="document.frmWrkExp.submit();" <?=$locRights['add'] ? '':'disabled'?> name="txtEmpExpAdd2">
								<option>----Select Country----</option>
<?				$list=$empinfo->getCountryCodes();
				for($c=0;$list && count($list)>$c;$c++)
					if(isset($_POST['txtEmpExpAdd2']) && $_POST['txtEmpExpAdd2']==$list[$c][0])
					    echo "<option selected value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
					else
					    echo "<option value='" . $list[$c][0] . "'>" . $list[$c][1]. "</option>";
?>			 
					</select></td>
    				  <td width="50">&nbsp;</td>
						<td>Contact Person</td>
						<td> <input type="text" name="txtEmpExpConPers" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpConPers']) ? $_POST['txtEmpExpConPers'] :''?>"></td>
					  </tr>
					  <tr>
						<td>State</td>
						<td> <select <?=$locRights['add'] ? '':'disabled'?> name="txtEmpExpAdd3"> 
								<option>------Select State------</option>
<?			if(isset($_POST['txtEmpExpAdd2'])) {
				$plist=$empinfo->getProvinceCodes($_POST['txtEmpExpAdd2']);
				for($c=0;$plist && count($plist)>$c;$c++)
					if(isset($_POST['txtEmpExpAdd3']) && $_POST['txtEmpExpAdd3']==$plist[$c][1])
					    echo "<option selected value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
					else
					    echo "<option value='" . $plist[$c][1]. "'>" . $plist[$c][2]. "</option>";
				}
?>
						</select></td>
    				  <td width="50">&nbsp;</td>
						<td>Telephone</td>
						<td> <input type="text" name="txtEmpExpTelep" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpTelep']) ? $_POST['txtEmpExpTelep'] :''?>"></td>
					  </tr>
					  <tr>
						<td>Reason for Leaving</td>
						<td> <textarea <?=$locRights['add'] ? '':'disabled'?> name="txtEmpResLev"><?=isset($_POST['txtEmpResLev']) ? $_POST['txtEmpResLev'] :''?></textarea></td>
    				  <td width="50">&nbsp;</td>
						<td>Acheivements</td>
						<td> <textarea <?=$locRights['add'] ? '':'disabled'?> name="txtEmpExpAchmnt"><?=isset($_POST['txtEmpExpAchmnt']) ? $_POST['txtEmpExpAchmnt'] :''?></textarea></td>
					  </tr>
					  <tr>
						<td>From Date</td>
						<td> <input type="text" name="txtEmpExpFromDat" readonly value="<?=isset($_POST['txtEmpExpFromDat']) ? $_POST['txtEmpExpFromDat'] :''?>">&nbsp;<input <?=$locRights['add'] ? '':'disabled'?> type="button" class="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmWrkExp.txtEmpExpFromDat);return false;"></td>
    				  <td width="50">&nbsp;</td>
						<td>Email</td>
						<td> <input type="text" name="txtEmpExpEmail" <?=$locRights['add'] ? '':'disabled'?> value="<?=isset($_POST['txtEmpExpEmail']) ? $_POST['txtEmpExpEmail'] :''?>"></td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
					  </tr>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3>Assigned Work Experiance </h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
      <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      	<td></td>
						 <td width="125"><strong>Work Experiance ID</strong></td>
						 <td width="135"><strong>Company</strong></td>
						 <td width="125"><strong>From Date</strong></td>
						 <td width="125"><strong>To Date</strong></td>
					</tr>
<?
$rset = $empwrkexp ->getAssEmpWorkExp($_GET['id']);

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."'></td>";
            echo "<td><a href='./hrempwrkexp.php?reqcode=" . $_GET['reqcode'] . "&id=" . $_GET['id']. "&WRKEXP=" . $rset[$c][1] . "'>" . $rset[$c][1] . "</a></td>";
            echo '<td>' . $rset[$c][2] .'</td>';
            $str = explode(" ",$rset[$c][3]);
            echo '<td>' . $str[0] .'</td>';
            $str = explode(" ",$rset[$c][4]);
            echo '<td>' . $str[0] .'</td>';
        echo '</tr>';
        }

?>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe></body>
</html>
