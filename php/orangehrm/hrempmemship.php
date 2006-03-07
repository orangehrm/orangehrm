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
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpMembership.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$subown = array ( 'Company' , 'Individual');
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$empinfo = new EmpInfo();
	$empdet = $empinfo->filterEmpMain($_GET['id']);

	$empmemship = new EmpMembership();

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
	$empmemship->setEmpId($_GET['id']);
	$empmemship->setEmpMemCode(trim($_POST['cmbMemCode']));
	$empmemship->setEmpMemTypeCode(trim($_POST['cmbMemTypeCode']));
	$empmemship->setEmpMemSubOwn(trim($_POST['cmbMemSubOwn']));
	$empmemship->setEmpMemSubAmount(trim($_POST['txtMemSubAmount']));
	$empmemship->setEmpMemCommDat(trim($_POST['txtMemCommDat']));
	$empmemship->setEmpMemRenDat(trim($_POST['txtMemRenDat']));
    $empmemship->addEmpMembership();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
    $empmemship->setEmpId($_GET['id']);
	$empmemship->setEmpMemCode(trim($_POST['cmbMemCode']));
	$empmemship->setEmpMemTypeCode(trim($_POST['cmbMemTypeCode']));
	$empmemship->setEmpMemSubOwn(trim($_POST['cmbMemSubOwn']));
	$empmemship->setEmpMemSubAmount(trim($_POST['txtMemSubAmount']));
	$empmemship->setEmpMemCommDat(trim($_POST['txtMemCommDat']));
	$empmemship->setEmpMemRenDat(trim($_POST['txtMemRenDat']));
    $empmemship->updateEmpMembership();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='DEL')
   {
   $arr=$_POST['chkdel'];
   
   for($c=0;count($arr)>$c;$c++) {
   		$frg=explode("|",$arr[$c]);
		$arrpass[1][$c]=$frg[0];
		$arrpass[2][$c]=$frg[1];
   		}

   for($c=0;count($arr)>$c;$c++)
       if($arr[$c]!=NULL)
	      $arrpass[0][$c]=$_GET['id'];
		  
   $empmemship->delEmpMembership($arrpass);
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
    if(code>=48 && code<=57 || code==46)
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
	
	var frm=document.frmEmpMem;
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
	if(document.frmEmpMem.cmbMemCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemCode.focus();
		return;
	}

	if(document.frmEmpMem.cmbMemTypeCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemTypeCode.focus();
		return;
	}

	if(document.frmEmpMem.cmbMemSubOwn.value=='0') {
		alert('Field should be selected');
		document.frmEmpMem.cmbMemSubOwn.focus();
		return;
	}

	var txt = document.frmEmpMem.txtMemSubAmount;
	if (!numeric(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}
	
	var commDate = createDate(document.frmEmpMem.txtMemCommDat.value);
	var renDate = createDate(document.frmEmpMem.txtMemRenDat.value);
	
	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}
	
  document.frmEmpMem.STAT.value="ADD";
  document.frmEmpMem.submit();
}

function addCat()
{
var cntrl=document.frmEmpMem.cmbMemTypeCode;
document.frmEmpMem.action = document.frmEmpMem.action + "&CMB=" + cntrl.value ;
document.frmEmpMem.submit();
}

function editEXT()
{
    var txt = document.frmEmpMem.txtMemSubAmount;
	if (!numeric(txt)) {
		alert ("Description Error!");
		txt.focus();
		return false;
	}

	var commDate = createDate(document.frmEmpMem.txtMemCommDat.value);
	var renDate = createDate(document.frmEmpMem.txtMemRenDat.value);
	
	if(commDate >= renDate) {
		alert("Commence Date should be before renewal date");
		return;
	}

  document.frmEmpMem.STAT.value="EDIT";
  document.frmEmpMem.submit();
}

function delEXT()
{
      var check = 0;
		with (document.frmEmpMem) {
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
    document.frmEmpMem.STAT.value="DEL";
    document.frmEmpMem.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style1.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2>Employee Memberships</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpMem" method="post" action="./hrempmemship.php?reqcode=<?=$_GET['reqcode']?>&id=<?=$_GET['id']?>">
<input type="hidden" name="pageID" value="">
  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
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
if(isset($_GET['MSHIP']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['MSHIP'];
    $arr[2]=$_GET['TYP'];
    $edit=$empmemship->filterEmpMembership($arr);
?>

         <input type="hidden" name="txtEmpID" value="<?=$_GET['id']?>">
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
                      <td>Membership Type</td>
    				  <td><strong>
					  <input type="hidden" name="cmbMemTypeCode" value="<?=$edit[0][2]?>">
<?					  
						$typlist=$empmemship->getMembershipTypeCodes();
						for($c=0;count($typlist)>$c;$c++)
							if($typlist[$c][0]==$edit[0][2])
							   echo $typlist[$c][1];
?>					  
					  </strong></td>
					</tr>
					  <tr> 
						<td valign="top">Membership</td>
						<td align="left" valign="top"><strong>
						<input type="hidden" name="cmbMemCode" value="<?=$edit[0][1]?>">
<?
						$mship=$empmemship->getMembershipCodes($edit[0][2]);
						for($c=0;count($mship)>$c;$c++)
						    if($mship[$c][1]==$edit[0][1])
						       echo $mship[$c][2];
?>						
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Ownership</td>
						<td align="left" valign="top"><select disabled name="cmbMemSubOwn">
<?
						for($c=0;count($subown)>$c;$c++)
						    if($edit[0][3]==$subown[$c])
							    echo "<option selected value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							else
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Amount</td>
						<td align="left" valign="top"><input type="text" disabled name="txtMemSubAmount" value="<?=$edit[0][4]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Commence Date</td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemCommDat" value=<?=$edit[0][5]?>>&nbsp;<input class="button" disabled type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Renwal Date</td>
						<td align="left" valign="top"><input type="text" readonly disabled name="txtMemRenDat" value=<?=$edit[0][6]?>>&nbsp;<input class="button" disabled type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemRenDat);return false;">
						</td>
					  </tr>

					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top"> 
		<?			if($locRights['edit']) { ?>
						        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
		<?			} else { ?>
						        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
		<?			}  ?>
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
                      <td>Membership Type</td>
    				  <td>
					  <select onChange="addCat();" <?=$locRights['add'] ? '':'disabled'?> name="cmbMemTypeCode">
					  <option value=0>-Select M'ship Type-</option>

<?					  
						$typlist=$empmemship->getMembershipTypeCodes();
						for($c=0;$typlist && count($typlist)>$c;$c++)
							if(isset($_GET['CMB']) && $_GET['CMB']==$typlist[$c][0])
							   echo "<option selected value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";
							else
							   echo "<option value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";

?>					  
					  </select></td>
					</tr>
					  <tr> 
						<td valign="top">Membership</td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbMemCode'>
						   		<option value=0>-----Select M'ship----</option>
<?
					if(isset($_GET['CMB'])) {
						$mship=$empmemship->getUnAssMembershipCodes($_GET['id'],$_GET['CMB']);
						for($c=0;$mship && count($mship)>$c;$c++)
						    echo "<option value=" . $mship[$c][0] . ">" . $mship[$c][1] . "</option>";
						}
?>						
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Ownership</td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name="cmbMemSubOwn">
						   		<option value=0>-Select Ownership-</option>
<?
						for($c=0;count($subown)>$c;$c++)
							    echo "<option value='" . $subown[$c] . "'>" . $subown[$c] . "</option>";
							
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Amount</td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMemSubAmount" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Commence Date</td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemCommDat" >&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemCommDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Subscription Renwal Date</td>
						<td align="left" valign="top"><input type="text" readonly name="txtMemRenDat" >&nbsp;<input class="button" <?=$locRights['add'] ? '':'disabled'?> type="button" value=".." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmEmpMem.txtMemRenDat);return false;">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"></td>
						<td align="left" valign="top">
<?	if($locRights['add']) { ?>
        <img border="0" title="Save" onClick="addEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?	} ?>
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
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3>Assigned Memberships</h3></td>
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
						 <td><strong>Membership</strong></td>
						 <td><strong>Membership Type</strong></td>
						 <td><strong>Subscription Ownership</strong></td>
						 <td><strong>Membership Commence Date</strong></td>
						 <td><strong>Membership Renewal Date</strong></td>
					</tr>
<?
$rset = $empmemship->getAssEmpMembership($_GET['id']);
$mship=$empmemship->getAllMembershipCodes();

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."|" . $rset[$c][2] . "'></td>";
			for($a=0;count($mship)>$a;$a++)
			    if($mship[$a][1]==$rset[$c][1])
				   $fname=$mship[$a][2];
            echo "<td><a href='./hrempmemship.php?reqcode=" . $_GET['reqcode'] . "&id=" . $_GET['id']. "&MSHIP=" . $rset[$c][1] . "&TYP=" . $rset[$c][2] . "'>" . $fname . "</a></td>";
			for($a=0;count($typlist)>$a;$a++)
			    if($typlist[$a][0]==$rset[$c][2])
				   $fname=$typlist[$a][1];
            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            $disStr = explode(" ",$rset[$c][5]);
            echo '<td>' . $disStr[0] .'</td>';
            $disStr = explode(" ",$rset[$c][6]);
            echo '<td>' . $disStr[0] .'</td>';
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
