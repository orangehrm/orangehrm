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
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpQual.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$statlist = array('First Class','Second Class Upr.','Second Class Lwr.');
	$empinfo = new EmpInfo();
	$empdet = $empinfo->filterEmpMain($_GET['id']);

	$empqual = new EmpQualification();

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
	$empqual->setEmpQualId($_POST['cmbQualCode']);
	$empqual->setEmpId($_GET['id']);
	$empqual->setEmpQualInst(trim($_POST['txtQualInst']));
	$empqual->setEmpQualYear(trim($_POST['txtQualYear']));
	$empqual->setEmpQualStat($_POST['cmbQualStat']);
	$empqual->setEmpQualComment(trim($_POST['txtQualComment']));
	$empqual->addEmpQual();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
	$empqual->setEmpQualId($_POST['cmbQualCode']);
	$empqual->setEmpId($_GET['id']);
	$empqual->setEmpQualInst(trim($_POST['txtQualInst']));
	$empqual->setEmpQualYear(trim($_POST['txtQualYear']));
	$empqual->setEmpQualStat($_POST['cmbQualStat']);
	$empqual->setEmpQualComment(trim($_POST['txtQualComment']));
	$empqual->updateEmpQual();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='DEL')
   {
   $arr[1]=$_POST['chkdel'];

   for($c=0;count($arr[1])>$c;$c++) 
       if($arr[1][$c]!=null)
          $arr[0][$c]=$_GET['id'];
       
   $empqual->delEmpQual($arr);
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
	
	var frm=document.frmEmpQual;
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

function goBack() {
		location.href = "empview.php?reqcode=<?=$_GET['reqcode']?>";
	}

function addEXT()
{
	if(document.frmEmpQual.TypeCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpQual.TypeCode.focus();
		return;
	}

	if(document.frmEmpQual.cmbQualCode.value=='0') {
		alert('Field should be selected');
		document.frmEmpQual.cmbQualCode.focus();
		return;
	}

	var cnt=document.frmEmpQual.txtQualInst;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt=document.frmEmpQual.txtQualYear;
	if(!numeric(cnt)) {
		alert("Field should be Numeric");
		cnt.focus();
		return;
	}

	if(document.frmEmpQual.cmbQualStat.value=='0') {
		alert('Field should be selected');
		document.frmEmpQual.cmbQualStat.focus();
		return;
	}

  document.frmEmpQual.STAT.value="ADD";
  document.frmEmpQual.submit();
}

function addCat()
{
var cntrl=document.frmEmpQual.TypeCode;
document.frmEmpQual.action = document.frmEmpQual.action + "&CMB=" + cntrl.value ;
document.frmEmpQual.submit();
}

function editEXT()
{
	var cnt=document.frmEmpQual.txtQualInst;
	if(!alpha(cnt)) {
		alert("Field should be Alphabetic");
		cnt.focus();
		return;
	}
	
	var cnt=document.frmEmpQual.txtQualYear;
	if(!numeric(cnt)) {
		alert("Field should be Numeric");
		cnt.focus();
		return;
	}

  document.frmEmpQual.STAT.value="EDIT";
  document.frmEmpQual.submit();
}

function delEXT()
{
      var check = 0;
		with (document.frmEmpQual) {
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
    document.frmEmpQual.STAT.value="DEL";
    document.frmEmpQual.submit();
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
    <td width='100%'><h2>Employee Qualifications</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpQual" method="post" action="./hrempqual.php?reqcode=<?=$_GET['reqcode']?>&id=<?=$_GET['id']?>">
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
if(isset($_GET['QUAL']))
{
    $arr[0]=$_GET['QUAL'];
    $arr[1]=$_GET['id'];
    $edit=$empqual->filterEmpQual($arr);
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
                      <td>Qualification Type</td>
    				  <td><strong>
<?					    $quallist=$empqual->getAllQualifications();
						for($c=0;count($quallist)>$c;$c++)
						    if($quallist[$c][0]==$edit[0][0])
						       break;
						       $qualDesc=$quallist[$c][1];
						       $qualType=$quallist[$c][2];
						       
						$typlist=$empqual->getQualificationTypeCodes();
						for($c=0;count($typlist)>$c;$c++)
							if($typlist[$c][0]==$qualType)
							   echo $typlist[$c][1];
?>					  
					  </strong></td>
					</tr>
					  <tr> 
						<td valign="top">Qualification</td>
						<td align="left" valign="top"><strong>
						<input type="hidden" name="cmbQualCode" value="<?=$edit[0][0]?>">
						<?=$qualDesc?>
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top">Institute</td>
						<td align="left" valign="top"><input type="text" disabled name="txtQualInst" value="<?=$edit[0][2]?>"></td>
					  </tr>
					  <tr> 
						<td valign="top">Year</td>
						<td align="left" valign="top"><input type="text" disabled name="txtQualYear" value="<?=$edit[0][3]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Status</td>
						<td align="left" valign="top"><select disabled name="cmbQualStat">
<?						for($c=0;count($statlist)>$c;$c++)
							if($edit[0][4]==$statlist[$c])
							   echo "<option selected>" .$statlist[$c]."</option>";
							else 
							   echo "<option>" .$statlist[$c]."</option>";
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Comment</td>
						<td align="left" valign="top"><textarea name="txtQualComment" disabled><?=$edit[0][5]?></textarea></td>
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

<?
} else {
?>
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
                      <td>Qualification Type</td>
    				  <td>
					  <select onChange="addCat();" <?=$locRights['add'] ? '':'disabled'?> name="TypeCode">
					  <option value=0>-Select Qual. Type-</option>
<?					  
						$typlist=$empqual->getQualificationTypeCodes();
						for($c=0;$typlist && count($typlist)>$c;$c++)
							if(isset($_GET['CMB']) && $_GET['CMB']==$typlist[$c][0])
							   echo "<option selected value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";
							else
							   echo "<option value=" . $typlist[$c][0] . ">" . $typlist[$c][1] . "</option>";

?>					  
					  </select></td>
					</tr>
					  <tr> 
						<td valign="top">Qualification</td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbQualCode'>
						   		<option value=0>------Select Qual------</option>
<?
					if(isset($_GET['CMB'])) {
						$mship=$empqual->getUnAssQualifications($_GET['id'],$_GET['CMB']);
						for($c=0;$mship && count($mship)>$c;$c++)
						    echo "<option value=" . $mship[$c][0] . ">" . $mship[$c][1] . "</option>";
						}
?>						
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Institute</td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtQualInst" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Year</td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtQualYear" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Status</td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name="cmbQualStat">
														<option value="0">---Select Status---</option>
<?						for($c=0;count($statlist)>$c;$c++)
							   echo "<option>" .$statlist[$c]."</option>";
?>
						</select></td>
					  </tr>
					  <tr> 
						<td valign="top">Comment</td>
						<td align="left" valign="top"><textarea <?=$locRights['add'] ? '':'disabled'?> name="txtQualComment"></textarea></td>
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

    <td width='100%'><h3>Assigned Qualifications</h3></td>
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
						 <td><strong>Qualification</strong></td>
						 <td><strong>Qualification Type</strong></td>
						 <td><strong>Institute</strong></td>
						 <td><strong>Year</strong></td>
						 <td><strong>Status</strong></td>
					</tr>
<?
$rset = $empqual->getAssEmpQual($_GET['id']);
$quallist=$empqual->getAllQualifications();

    for($c=0;$rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] . "'></td>";
			for($a=0;count($quallist)>$a;$a++)
			    if($quallist[$a][0]==$rset[$c][1]) {
				   $fname=$quallist[$a][1];
				   $type=$quallist[$a][2]; 
			    }
            echo "<td><a href='./hrempqual.php?reqcode=" . $_GET['reqcode'] . "&id=" . $_GET['id']. "&QUAL=" . $rset[$c][1] . "'>" . $fname . "</a></td>";
			for($a=0;count($typlist)>$a;$a++)
			    if($typlist[$a][0]==$type)
				   $fname=$typlist[$a][1];
            echo '<td>' . $fname .'</td>';
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
            echo '<td>' . $rset[$c][4] .'</td>';
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
</body>
</html>
