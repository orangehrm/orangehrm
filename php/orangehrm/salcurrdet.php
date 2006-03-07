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
require_once OpenSourceEIM . '/lib/Models/eimadmin/SalCurDet.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/SalaryGrades.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$salcurdet = new SalCurDet();
	$salgrade = new SalaryGrades();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$salgrdname = $salgrade->filterSalaryGrades($_GET['id']);

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
	$salcurdet->setSalGrdId($_GET['id']);
	$salcurdet->setCurrId($_POST['cmbCurrCode']);
	$salcurdet->setMinSal($_POST['txtMinSal']);
	$salcurdet->setMaxSal($_POST['txtMaxSal']);
    $salcurdet->addSalCurDet();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
	$salcurdet->setSalGrdId($_GET['id']);
	$salcurdet->setCurrId($_POST['cmbCurrCode']);
	$salcurdet->setMinSal($_POST['txtMinSal']);
	$salcurdet->setMaxSal($_POST['txtMaxSal']);
    $salcurdet->updateSalCurDet();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='DEL')
   {
   $arrpass[1]=$_POST['chkdel'];
   
   for($c=0;count($arrpass[1])>$c;$c++)
       if($arrpass[1][$c]!=NULL)
	      $arrpass[0][$c]=$_GET['id'];
		  
   $salcurdet->delSalCurDet($arrpass);
   }

?>
<!DOCCIDE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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


function addEXT()
{

if(document.frmSalCurDet.cmbCurrCode.value=='0') {
	alert("Field should be selected");
	document.frmSalCurDet.cmbCurrCode.focus();
	return;
}

var cnt=document.frmSalCurDet.txtMinSal;
var min=eval(cnt.value);

if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

var cnt=document.frmSalCurDet.txtMaxSal;
var max=eval(cnt.value);

if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

if(min>max) {
	alert("Minmum Salary > Maximum Salary !");
	return;
}

document.frmSalCurDet.STAT.value="ADD";
document.frmSalCurDet.submit();
}

function editEXT()
{
var cnt=document.frmSalCurDet.txtMinSal;
var min=eval(cnt.value);
if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

var cnt=document.frmSalCurDet.txtMaxSal;
var max=eval(cnt.value);

if(!numeric(cnt)) {
	alert("Field should be Numeric");
	cnt.focus();
	return;
}

if(min>max) {
	alert("Minmum Salary < Maximum Salary !");
	return;
}

  document.frmSalCurDet.STAT.value="EDIT";
  document.frmSalCurDet.submit();
}

	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

function delEXT()
{
      var check = 0;
		with (document.frmSalCurDet) {
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
    document.frmSalCurDet.STAT.value="DEL";
    document.frmSalCurDet.submit();
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
	
function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}
	
	var frm=document.frmSalCurDet;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
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
    <td width='100%'><h2>Currency Assignment to Salary Grade</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSalCurDet" method="post" action="./salcurrdet.php?id=<?=$_GET['id']?>&uniqcode=<?=$_GET['uniqcode']?>">
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
                      <td width="200">Salary Grade ID</td>
    				  <td><strong><?=$_GET['id']?></strong></td>
					</tr>
					  <tr> 
						<td valign="top">Salary Grade</td>
						<td align="left" valign="top"><strong><?=$salgrdname[0][1]?></strong></td>
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
if(isset($_GET['CUR']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['CUR'];
    $edit=$salcurdet->filterSalCurDet($arr);
?>

         <input type="hidden" name="txtSalGrd" value="<?=$_GET['id']?>">
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
						<td valign="top"><strong>Currency</strong></td>
						<td align="left" valign="top"><input type="hidden" name="cmbCurrCode" value="<?=$edit[0][1]?>">
<?
						$currlist=$salcurdet->getAllCurrencyCodes();
						for($c=0;count($currlist)>$c;$c++)
						    if($currlist[$c][0]==$edit[0][1])
						       echo  $currlist[$c][1] ;
?>						
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><strong>Minimum Salary</strong></td>
						<td align="left" valign="top"><input type="text" disabled name="txtMinSal" value="<?=$edit[0][2]?>">
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><strong>Maximum Salary</strong></td>
						<td align="left" valign="top"><input type="text" disabled name="txtMaxSal" value="<?=$edit[0][3]?>">
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

<?
}
else
    {
?>
&nbsp;
         <input type="hidden" name="txtSalGrd" value="<?=$_GET['id']?>">
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
						<td valign="top"><strong>Currency</strong></td>
						<td align="left" valign="top">
						<select <?=$locRights['add'] ? '' : 'disabled'?> name="cmbCurrCode">
									<option value="0">--Select--</option>
<?
						$currlist=$salcurdet->getCurrencyCodes($_GET['id']);
						for($c=0;count($currlist)>$c;$c++)
						       echo "<option value='". $currlist[$c][0] . "'>". $currlist[$c][1] . "</option>";
?>						
						</select>
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><strong>Minimum Salary</strong></td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMinSal" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top"><strong>Maximum Salary</strong></td>
						<td align="left" valign="top"><input type="text" <?=$locRights['add'] ? '':'disabled'?> name="txtMaxSal" >
						</td>
					  </tr>
					  <tr> 
						<td valign="top"></td>
<?					if($locRights['add']) { ?>
						<td align="left" valign="top"><img onClick="addEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<?					} else { ?>
						<td align="left" valign="top"><img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_save.jpg">
<?					}		?>						
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

    <td width='100%'><h3>Assigned Currency</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?					if($locRights['delete']) { ?>
						<img onClick="delEXT();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<?					} else { ?>
						<img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<?					}		?>						
  
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
						 <td><strong>Currency</strong></td>
						 <td><strong>Minimum Salary</strong></td>
						 <td><strong>Maximum Salary</strong></td>
					</tr>
<?
$rset = $salcurdet->getAssSalCurDet($_GET['id']);
$currlist=$salcurdet->getAllCurrencyCodes();

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
        if($locRights['delete'])
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] . "'></td>";
			for($a=0;count($currlist)>$a;$a++)
			    if($currlist[$a][0]==$rset[$c][1])
		            echo "<td><a href='./salcurrdet.php?id=" . $_GET['id']. "&CUR=" . $rset[$c][1] . "&uniqcode=" .$_GET['uniqcode']. "'>" . $currlist[$a][1] . "</a></td>";
            echo '<td>' . $rset[$c][2] .'</td>';
            echo '<td>' . $rset[$c][3] .'</td>';
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
