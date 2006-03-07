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
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpLang.php';
require_once OpenSourceEIM . '/lib/Models/hrfunct/EmpInfo.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

$lantype = array ( 'Writing'=> 1 , 'Speaking'=>2 , 'Reading'=>3 );

	$empinfo = new EmpInfo();
	$empdet = $empinfo->filterEmpMain($_GET['id']);

	$emplan= new EmpLanguage();

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
   $emplan->setEmpId($_GET['id']);
   $emplan->setEmpLangCode($_POST['cmbLanCode']);
   $emplan->setEmpLangType($_POST['cmbLanType']);
   $emplan->setEmpLangRatCode($_POST['cmbRat']);
   $emplan->setEmpLangRatGrd($_POST['cmbRatGrd']);
   $emplan->addEmpLang();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
   $emplan->setEmpId($_GET['id']);
   $emplan->setEmpLangCode($_POST['cmbLanCode']);
   $emplan->setEmpLangType($_POST['cmbLanType']);
   $emplan->setEmpLangRatGrd($_POST['cmbRatGrd']);
   $emplan->updateEmpLang();
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
		  
   $emplan->delEmpLang($arrpass);
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

function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmEmpLan;
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

function addCmb() {
	document.frmEmpLan.action=document.frmEmpLan.action+"&CMB="+document.frmEmpLan.cmbLanCode.value;
	document.frmEmpLan.submit();
}

function addEXT()
{
	if(document.frmEmpLan.cmbLanCode.value=='0') {
		alert("Field should be selected");
		document.frmEmpLan.cmbLanCode.focus();
		return;
	}
	
	if(document.frmEmpLan.cmbLanType.value=='0') {
		alert("Field should be selected");
		document.frmEmpLan.cmbLanType.focus();
		return;
	}

	if(document.frmEmpLan.cmbRatGrd.value=='0') {
		alert("Field should be selected");
		document.frmEmpLan.cmbRatGrd.focus();
		return;
	}

  document.frmEmpLan.STAT.value="ADD";
  document.frmEmpLan.submit();
}

function editEXT()
{
  document.frmEmpLan.STAT.value="EDIT";
  document.frmEmpLan.submit();
}

function delEXT()
{
      var check = 0;
		with (document.frmEmpLan) {
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

    document.frmEmpLan.STAT.value="DEL";
    document.frmEmpLan.submit();
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
    <td width='100%'><h2>Employee Language Fluency</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmEmpLan" method="post" action="./hremplan.php?reqcode=<?=$_GET['reqcode']?>&id=<?=$_GET['id']?>">
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
if(isset($_GET['LAN']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['LAN'];
    $arr[2]=$_GET['TYP'];
    $edit=$emplan->filterEmpLang($arr);
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
                      <td width="200">Language</td>
    				  <td><input type="hidden" name="cmbLanCode" value="<?=$_GET['LAN']?>"><strong>
<?						$lanlist=$emplan->getLang();
						for($c=0;count($lanlist)>$c;$c++)
							if($_GET['LAN']==$lanlist[$c][0])
							     break;
							     
					  			echo $lanlist[$c][1];
					  			$ratSel=$lanlist[$c][2];
?>
					  </strong></td>
					</tr>
					  <tr> 
						<td valign="top">Fluency</td>
						<td align="left" valign="top"><input type="hidden" name="cmbLanType" value="<?=$_GET['TYP']?>"><strong>
<?						
						$code=array_values($lantype);
						$name=array_keys($lantype);
						for($a=0;count($lantype)>$a;$a++)
							if($_GET['TYP']==$code[$a])
					  			echo $name[$a];
?>
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Rating Grade</td>
						<td align="left" valign="top"><select disabled name='cmbRatGrd'>
<?
						$grdcodes=$emplan->getRatingGrade($ratSel);
						for($c=0;count($grdcodes)>$c;$c++)
							if($grdcodes[$c][1]==$edit[0][4])
								echo "<option seleted value='" . $grdcodes[$c][1] . "'>" . $grdcodes[$c][2] ."</option>";
							else 
								echo "<option value='" . $grdcodes[$c][1] . "'>" . $grdcodes[$c][2] ."</option>";
?>
						</select></td>
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
                      <td width="200">Language</td>
    				  <td><select name="cmbLanCode" <?=$locRights['add'] ? '':'disabled'?> onchange="addCmb();">
    				  		<option value="0">-Select Language-</option>
<?					  
						$lanlist=$emplan->getLang();
						for($c=0;$lanlist && count($lanlist)>$c;$c++)
							if(isset($_GET['CMB']) && $_GET['CMB']==$lanlist[$c][0]) {
							   echo "<option selected value=" . $lanlist[$c][0] . ">" . $lanlist[$c][1] . "</option>";
							   $ratSel=$lanlist[$c][2];
							} else
							   echo "<option value=" . $lanlist[$c][0] . ">" . $lanlist[$c][1] . "</option>";
?>					  
					  </select></td>
					</tr>
                    <tr>
<?                    if(isset($_GET['CMB'])) 
   							   echo "<input type='hidden' name='cmbRat' value='".$ratSel."'>";
?>
                      <td width="200">Fluency</td>
    				  <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbLanType">
    				  		<option value="0">---Select Fluency---</option>
<?					  
						$code=array_values($lantype);
						$name=array_keys($lantype);
						for($c=0;$lantype && count($lantype)>$c;$c++)
							   echo "<option value=" . $code[$c] . ">" . $name[$c] . "</option>";
?>					  
					  </select></td>
					</tr>
					  <tr> 
						<td valign="top">Rating Grade</td>
						<td align="left" valign="top"><select <?=$locRights['add'] ? '':'disabled'?> name='cmbRatGrd'>
    				  		<option value="0">----Select Rating----</option>
<?
					if(isset($_GET['CMB'])) {
						$grdcodes=$emplan->getRatingGrade($ratSel);
						for($c=0;$grdcodes && count($grdcodes)>$c;$c++)
							   echo "<option value='" . $grdcodes[$c][1] . "'>" . $grdcodes[$c][2] . "</option>";
					}
?>
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

    <td width='100%'><h3>Assigned Languages</h3></td>
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
						 <td><strong>Language</strong></td>
						 <td><strong>Fluency</strong></td>
						 <td><strong>Rating Method</strong></td>
						 <td><strong>Rating Grade</strong></td>
					</tr>
<?
$rset = $emplan ->getAssEmpLang($_GET['id']);
$ratcodes=$emplan->getAllRatingTypes();

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'>";

			for($a=0;count($lanlist)>$a;$a++)
				if($rset[$c][1] == $lanlist[$a][0])
				   $lname=$lanlist[$a][1];
            echo "<td><a href='./hremplan.php?reqcode=" . $_GET['reqcode'] . "&id=" . $_GET['id']. "&LAN=" . $rset[$c][1] . "&TYP=" . $rset[$c][2] . "'>" . $lname . "</td>";
			for($a=0;count($lantype)>$a;$a++)
				if($rset[$c][2] == $code[$a])
				   $lname=$name[$a];
            echo '<td>' . $lname .'</a></td>';
			for($a=0;count($ratcodes)>$a;$a++)
			   if($ratcodes[$a][0]==$rset[$c][3])
			     echo '<td>' . $ratcodes[$a][1] .'</td>';
			$grdcodes=$emplan->getRatingGrade($rset[$c][3]);
			for($a=0;count($grdcodes)>$a;$a++)
				if($grdcodes[$a][1]==$rset[$c][4])
		            echo '<td>' . $grdcodes[$a][2] .'</td>';
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
