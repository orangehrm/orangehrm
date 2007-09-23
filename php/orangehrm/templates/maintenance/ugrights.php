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
 */

require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	if ($_SESSION['userGroup'] == $this->popArr['ugDet'][0][0]) {
		$locRights=array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);
	}

?>
<!DOCCIDE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>

<script language="JavaScript">


function addEXT()
{

if(document.frmURights.cmbModuleID.value=='0') {
	alert("<?php echo $lang_Admin_Users_Errors_FieldShouldBeSelected; ?>");
	document.frmURights.cmbModuleID.focus();
	return;
}

	var frm=document.frmURights;
	if((!frm.chkView.checked) && (frm.chkAdd.checked || frm.chkEdit.checked || frm.chkDelete.checked)) {
		alert("<?php echo $lang_Admin_Users_Errors_ViewShouldBeSelected; ?>");
		return
	}

document.frmURights.STAT.value="ADD";
document.frmURights.submit();
}

function editEXT()

{

	var frm=document.frmURights;
	if((!frm.chkView.checked) && (frm.chkAdd.checked || frm.chkEdit.checked || frm.chkDelete.checked)) {
		alert("<?php echo $lang_Admin_Users_Errors_ViewShouldBeSelected; ?>");
		return
	}

  document.frmURights.STAT.value="EDIT";
  document.frmURights.submit();
}

	function goBack() {

		location.href = "./CentralController.php?capturemode=updatemode&uniqcode=USG&id=<?php echo $this->getArr['id']?>";
	}

function delEXT() {

	if(confirm("<?php echo $lang_Admin_Users_Errors_DoYouWantToClearRights; ?>!"))

    document.frmURights.STAT.value="DEL";
    document.frmURights.submit();
}

function mout() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function mover() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function edit()
{
	if(document.Edit.title=='Save') {
		editEXT();
		return;
	}

	var frm=document.frmURights;

	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style1.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2><?php echo $lang_Admin_Users_RightsAssignedToUserGroups; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmURights" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>">
  <tr>
    <td valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
<?php
$ugDet = $this ->popArr['ugDet'];
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
                      <td width="200"><?php echo $lang_Admin_Users_UserGroupId; ?></td>
    				  <td><strong><?php echo $ugDet[0][0]?></strong><input type="hidden" name="txtUserGroupID" value="<?php echo $ugDet[0][0]?>"></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_Admin_Users_UserGroup; ?></td>
						<td align="left" valign="top"><strong><?php echo $ugDet[0][1]?></strong></td>
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



<?php if($ugDet[0][0] == $_SESSION['userGroup']) { ?>

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
    	              	<td height="35"><strong><?php echo $lang_Admin_Users_Errors_SameGroup; ?></strong></td>
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

<?php } elseif(isset($this->popArr['editArr'])) {

    $edit = $this->popArr['editArr'];
?>

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
						<td valign="top"><?php echo $lang_Admin_Users_Modules; ?></td>
						<td align="left" valign="top"><input type="hidden" name="cmbModuleID" value="<?php echo $edit[0][1]?>"><strong>
<?php
						$modlist = $this->popArr['modlist'];
						for($c=0;count($modlist)>$c;$c++)
						    if($modlist[$c][0]==$edit[0][1])
						       echo  $modlist[$c][1] ;
?>
						</strong></td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_Admin_Users_add; ?></td>
						<td align="left" valign="top"><input type="checkbox" disabled <?php echo $edit[0][2]==1 ? 'checked' : ''?> name="chkAdd" value="1">
						</td>
						<td valign="top"><?php echo $lang_Admin_Users_edit; ?></td>
						<td align="left" valign="top"><input type="checkbox" disabled <?php echo $edit[0][3]==1 ? 'checked' : ''?> name="chkEdit" value="1">
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_Admin_Users_delete; ?></td>
						<td align="left" valign="top"><input type="checkbox" disabled <?php echo $edit[0][4]==1 ? 'checked' : ''?> name="chkDelete" value="1">
						</td>
						<td valign="top"><?php echo $lang_Admin_Users_view; ?></td>
						<td align="left" valign="top"><input type="checkbox" disabled <?php echo $edit[0][5]==1 ? 'checked' : ''?> name="chkView" value="1">
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
<?php			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
<?php			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">
<?php			}  ?>
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

<?php } else { ?>
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
						<td valign="top"><?php echo $lang_Admin_Users_Modules; ?></td>
						<td align="left" valign="top">
						<select name="cmbModuleID" <?php echo $locRights['add'] ? '':'disabled'?>>
									<option value="0">--<?php echo $lang_Admin_Users_SelectModule;?>--</option>
<?php
						$modlist = $this->popArr['modlistUnAss'];
						for($c=0;$modlist && count($modlist)>$c;$c++)
						       echo "<option value='". $modlist[$c][0] . "'>". $modlist[$c][1] . "</option>";
?>
						</select>
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_Admin_Users_add; ?></td>
						<td align="left" valign="top"><input type="checkbox" name="chkAdd" <?php echo $locRights['add'] ? '':'disabled'?> value="1">
						</td>
						<td valign="top"><?php echo $lang_Admin_Users_edit; ?></td>
						<td align="left" valign="top"><input type="checkbox" name="chkEdit" <?php echo $locRights['add'] ? '':'disabled'?> value="1">
						</td>
					  </tr>
					  <tr>
						<td valign="top"><?php echo $lang_Admin_Users_delete; ?></td>
						<td align="left" valign="top"><input type="checkbox" name="chkDelete" <?php echo $locRights['add'] ? '':'disabled'?> value="1">
						</td>
						<td valign="top"><?php echo $lang_Admin_Users_view; ?></td>
						<td align="left" valign="top"><input type="checkbox" name="chkView" <?php echo $locRights['add'] ? '':'disabled'?> value="1">
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
<?php					if($locRights['add']) { ?>
						<td align="left" valign="top"><img onClick="addEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php					} else { ?>
						<td align="left" valign="top"><img onClick="alert('<?php echo $lang_Common_AccessDenied;?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?php					}		?>
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
<?php } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?php echo $lang_Admin_Users_AssignedRights; ?></h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?php					if($locRights['delete']) { ?>
						<img onClick="delEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php					} else { ?>
						<img onClick="alert('<?php echo $lang_Common_AccessDenied;?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php					}		?>

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
						 <td><strong><?php echo $lang_Admin_Users_Module; ?></strong></td>
						 <td><strong><?php echo $lang_Admin_Users_add; ?></strong></td>
						 <td><strong><?php echo $lang_Admin_Users_edit; ?></strong></td>
						 <td><strong><?php echo $lang_Admin_Users_delete; ?></strong></td>
						 <td><strong><?php echo $lang_Admin_Users_view; ?></strong></td>
					</tr>
<?php
$rset = $this->popArr['modlistAss'];
$modlist = $this->popArr['modlist'];

    for($c=0; $rset && $c < count($rset); $c++)
        {
        echo '<tr>';
			for($a=0;count($modlist)>$a;$a++)
			    if($modlist[$a][0]==$rset[$c][1])
		            echo "<td><a href='" .$_SERVER['PHP_SELF']. "?id=" . $this->getArr['id']. "&editID=" . $rset[$c][1] . "&uniqcode=" .$this->getArr['uniqcode']. "'>" . $modlist[$a][1] . "</a></td>";
            echo '<td>' . (($rset[$c][2]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][3]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][4]==1) ? 'Yes' : 'No') .'</td>';
            echo '<td>' . (($rset[$c][5]==1) ? 'Yes' : 'No') .'</td>';
        echo '</tr>';
        }

?>
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
</form>
</body>
</html>
