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
require_once OpenSourceEIM . '/lib/Models/bugs/UserGroups.php';
require_once OpenSourceEIM . '/lib/Rights.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$urights = new Rights();
	$usergroup = new UserGroups();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$usergroupdet = $usergroup->filterUserGroups($_GET['id']);

if(isset($_POST['STAT']) && $_POST['STAT']=='ADD')
   {
	$urights->setUserGroupID($_GET['id']);
	$urights->setModuleID($_POST['cmbModuleID']);
	$urights->setRightAdd(isset($_POST['chkAdd']) ? 1 : 0 );
	$urights->setRightEdit(isset($_POST['chkEdit']) ? 1 : 0 );
	$urights->setRightDelete(isset($_POST['chkDelete']) ? 1 : 0 );
	$urights->setRightView(isset($_POST['chkView']) ? 1 : 0 );
    $urights->addRights();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='EDIT')
   {
	$urights->setUserGroupID($_GET['id']);
	$urights->setModuleID($_POST['cmbModuleID']);
	$urights->setRightAdd(isset($_POST['chkAdd']) ? 1 : 0 );
	$urights->setRightEdit(isset($_POST['chkEdit']) ? 1 : 0 );
	$urights->setRightDelete(isset($_POST['chkDelete']) ? 1 : 0 );
	$urights->setRightView(isset($_POST['chkView']) ? 1 : 0 );
    $urights->updateRights();
   }

if(isset($_POST['STAT']) && $_POST['STAT']=='DEL')
   {
   $arrpass[1]=$_POST['chkdel'];
   
   for($c=0;count($arrpass[1])>$c;$c++)
       if($arrpass[1][$c]!=NULL)
	      $arrpass[0][$c]=$_GET['id'];
		  
   $urights->delRights($arrpass);
   }

?>
<!DOCCIDE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>

<script language="JavaScript">


function addEXT()
{

if(document.frmURights.cmbModuleID.value=='0') {
	alert("Field should be selected");
	document.frmURights.cmbModuleID.focus();
	return;
}

	var frm=document.frmURights;
	if((!frm.chkView.checked) && (frm.chkAdd.checked || frm.chkEdit.checked || frm.chkDelete.checked)) {
		alert("View should be selected");
		return
	}

document.frmURights.STAT.value="ADD";
document.frmURights.submit();
}

function editEXT()

{

	var frm=document.frmURights;
	if((!frm.chkView.checked) && (frm.chkAdd.checked || frm.chkEdit.checked || frm.chkDelete.checked)) {
		alert("View should be selected");
		return
	}
	
  document.frmURights.STAT.value="EDIT";
  document.frmURights.submit();
}

	function goBack() {
		location.href = "bugview.php?bugcode=<?=$_GET['bugcode']?>";
	}

function delEXT()
{
      var check = 0;
		with (document.frmURights) {
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
    document.frmURights.STAT.value="DEL";
    document.frmURights.submit();
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
	
	var frm=document.frmURights;
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
    <td width='100%'><h2>Rights Assigned to User Groups</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmURights" method="post" action="./ugrights.php?id=<?=$_GET['id']?>&bugcode=<?=$_GET['bugcode']?>">
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
                      <td width="200">User Group ID</td>
    				  <td><strong><?=$_GET['id']?></strong></td>
					</tr>
					  <tr> 
						<td valign="top">User Group</td>
						<td align="left" valign="top"><strong><?=$usergroupdet[0][1]?></strong></td>
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
if(isset($_GET['MOD']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['MOD'];
    $edit=$urights->filterRights($arr);
?>

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
						<td valign="top">Modules</td>
						<td align="left" valign="top"><input type="hidden" name="cmbModuleID" value="<?=$edit[0][1]?>"><strong></strong>
<?
						$modlist=$urights->getAllModules();
						for($c=0;count($modlist)>$c;$c++)
						    if($modlist[$c][0]==$edit[0][1])
						       echo  $modlist[$c][1] ;
?>						
						</strong></td>
					  </tr>
					  <tr> 
						<td valign="top">Add</td>
						<td align="left" valign="top"><input type="checkbox" disabled <?=$edit[0][2]==1 ? 'checked' : ''?> name="chkAdd" value="1">
						</td>
						<td valign="top">Edit</td>
						<td align="left" valign="top"><input type="checkbox" disabled <?=$edit[0][3]==1 ? 'checked' : ''?> name="chkEdit" value="1">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Delete</td>
						<td align="left" valign="top"><input type="checkbox" disabled <?=$edit[0][4]==1 ? 'checked' : ''?> name="chkDelete" value="1">
						</td>
						<td valign="top">View</td>
						<td align="left" valign="top"><input type="checkbox" disabled <?=$edit[0][5]==1 ? 'checked' : ''?> name="chkView" value="1">
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
						<td valign="top">Module</td>
						<td align="left" valign="top">
						<select name="cmbModuleID" <?=$locRights['add'] ? '':'disabled'?>>
									<option value="0">--Select Module--</option>
<?
						$modlist=$urights->getModuleCodes($_GET['id']);
						for($c=0;$modlist && count($modlist)>$c;$c++)
						       echo "<option value='". $modlist[$c][0] . "'>". $modlist[$c][1] . "</option>";
?>						
						</select>
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Add</td>
						<td align="left" valign="top"><input type="checkbox" name="chkAdd" <?=$locRights['add'] ? '':'disabled'?> value="1">
						</td>
						<td valign="top">Edit</td>
						<td align="left" valign="top"><input type="checkbox" name="chkEdit" <?=$locRights['add'] ? '':'disabled'?> value="1">
						</td>
					  </tr>
					  <tr> 
						<td valign="top">Delete</td>
						<td align="left" valign="top"><input type="checkbox" name="chkDelete" <?=$locRights['add'] ? '':'disabled'?> value="1">
						</td>
						<td valign="top">View</td>
						<td align="left" valign="top"><input type="checkbox" name="chkView" <?=$locRights['add'] ? '':'disabled'?> value="1">
						</td>
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

    <td width='100%'><h3>Assigned Rights</h3></td>
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
						 <td><strong>Module</strong></td>
						 <td><strong>Add</strong></td>
						 <td><strong>Edit</strong></td>
						 <td><strong>Delete</strong></td>
						 <td><strong>View</strong></td>
					</tr>
<?
$rset = $urights->getAssRights($_GET['id']);
$modlist=$urights->getAllModules();

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
        if($locRights['delete'])
            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] . "'></td>";
			for($a=0;count($modlist)>$a;$a++)
			    if($modlist[$a][0]==$rset[$c][1])
		            echo "<td><a href='./ugrights.php?id=" . $_GET['id']. "&MOD=" . $rset[$c][1] . "&bugcode=" .$_GET['bugcode']. "'>" . $modlist[$a][1] . "</a></td>";
            echo '<td>' . (($rset[$c][2]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][3]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][4]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][5]==1) ? 'Yes' : 'No') .'</td>';
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
