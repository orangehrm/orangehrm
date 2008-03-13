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
$lan = new Language();

require_once($lan->getLangPath("full.php"));

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {
	$_GET['isAdmin'] = isset($_GET['isAdmin'])?$_GET['isAdmin']:'No';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script>

function name(txt)
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

function popEmpList() {
	var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400');
    if(!popup.opener) popup.opener=self;
	popup.focus();
}

function goBack() {
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&isAdmin=<?php echo isset($_GET['isAdmin'])?$_GET['isAdmin']:'No'; ?>";
	}

	function addSave() {
		var frm=document.frmUsers;
		if (frm.txtUserName.value.length < 5 ) {
			alert ("<?php echo $lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong; ?>!");
			frm.txtUserName.focus();
			return false;
		}

		<?php if ($_SESSION['ldap'] == "enabled") {} else {?>

		if(frm.txtUserPassword.value.length < 4) {
			alert("<?php echo $lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong; ?>!");
			frm.txtUserPassword.focus();
			return;
		}

		if(frm.txtUserPassword.value != frm.txtUserConfirmPassword.value) {
			alert("<?php echo $lang_Admin_Users_ErrorsPasswordMismatch; ?>!");
			frm.txtUserPassword.focus();
			return;
		}

		<?php } ?>

		if(!frm.chkUserIsAdmin && frm.cmbUserEmpID.value == '') {
			alert("<?php echo $lang_Admin_Users_Errors_EmployeeIdShouldBeDefined; ?>");
			frm.cmbUserEmpID.focus();
			return;
		}


		if(frm.chkUserIsAdmin && frm.cmbUserGroupID.value == '0') {
			alert("<?php echo $lang_Admin_Users_Errors_FieldShouldBeSelected; ?>!");
			frm.cmbUserGroupID.focus();
			return;
		}

		document.frmUsers.sqlState.value = "NewRecord";
		document.frmUsers.submit();
	}

	function toggleAdmin(obj) {
		if (obj.checked) {
			document.getElementById("lyrUserGroupID").style.visibility = 'visible';
			document.getElementById("lyrUserGroupID1").style.visibility = 'visible';
		} else {
			document.getElementById("lyrUserGroupID").style.visibility = 'hidden';
			document.getElementById("lyrUserGroupID1").style.visibility = 'hidden';
		}
	}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2><?php echo $lang_view_Users; ?> : <?php echo (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes')) ? $lang_view_HRAdmin : $lang_view_ESS; ?> <?php echo $lang_view_Users; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmUsers" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&isAdmin=<?php echo $_GET['isAdmin']?>">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      <?php
		if (isset($this->getArr['msg'])) {
			$expString  = $this->getArr['msg'];
			$expString = explode ("_",$expString);
			$length = sizeof($expString);

			for ($x=0; $x < $length; $x++) {
				echo " " . $expString[$x];
			}
		}
		?>

    </font> </td>
  </tr><td width="177">
</table>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="450">
                  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr>
							    <td nowrap="nowrap"><span class="error">*</span> <?php echo $lang_Admin_Users_UserName; ?></td>
							    <td><input type="text" name="txtUserName"></td>
								<td></td>
								<td nowrap="nowrap"></td>
							  	<td></td>
						  </tr>
						  <tr>
							  <td nowrap="nowrap"><?php if ($_SESSION['ldap'] == "enabled") {} else {?><span class="error">*</span><?php } ?> <?php echo $lang_Admin_Users_Password; ?></td>
							  <td><input type="password" name="txtUserPassword"></td>
							  <td></td>
							  <td nowrap="nowrap"><?php if ($_SESSION['ldap'] == "enabled") {} else {?><span class="error">*</span><?php } ?> <?php echo $lang_Admin_Users_ConfirmPassword; ?></td>
							  <td><input type="password" name="txtUserConfirmPassword"></td>
						  </tr>
						  <tr valign="top">
							  <td><?php echo $lang_Admin_Users_Status; ?></td>
						   	  <td><select name="cmbUserStatus">
						   			<option value="Enabled"><?php echo $lang_Admin_Users_Enabled; ?></option>
						   			<option value="Disabled"><?php echo $lang_Admin_Users_Disabled; ?></option>
						   		  </select></td>
							  <td></td>
							  <td><span id="lyrEmpID" class="error"><?php echo ($_GET['isAdmin']=='No')? '*' : '' ?></span> <?php echo $lang_Admin_Users_Employee; ?></td>
							  <td nowrap="nowrap"><input type="text" readonly name="txtUserEmpID"><input type="hidden" readonly name="cmbUserEmpID">&nbsp;&nbsp;<input type="button" value="..." onClick="popEmpList();"></td>
						   </tr>
						   <?php if ($_GET['isAdmin'] == 'Yes') { ?>
						   <tr>
							   <td><span class="error">*</span> <?php echo $lang_Admin_Users_UserGroup; ?></div></td>
							   <td><select name="cmbUserGroupID" id ="cmbUserGroupID">
							  		<option value="0">--<?php echo $lang_Admin_Users_SelectUserGroup; ?>--</option>
<?php									$uglist=$this->popArr['uglist'] ;
									for($c=0;$uglist && count($uglist)>$c;$c++)
										echo "<option value='" . $uglist[$c][0] ."'>" .$uglist[$c][1]. "</option>";
?>
							  </select></td>
							   <td>&nbsp;</td>
							   <td>&nbsp;</td>
							   <td><input type="hidden" name="chkUserIsAdmin" value="true"></td>
						   </tr>
						  <?php } else { ?>
						   <input type="hidden" name="cmbUserGroupID" value="0" >
						   <?php } ?>
					  <tr>
					  <td align="right" width="100%"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif"></td>
					  <td><img onClick="document.frmUsers.reset();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" src="../../themes/beyondT/pictures/btn_clear.gif"></td>
					  <td></td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  </tr>
                  </table>
                  </td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>


</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script>

function name(txt)
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


	function goBack() {
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&isAdmin=<?php echo $_GET['isAdmin']?>";
	}

function mout() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function mover() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function popEmpList() {
	var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400');
    if(!popup.opener) popup.opener=self;
	popup.focus();
}

function edit() {
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}

	var frm=document.frmUsers;

	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.gif";
	document.Edit.title="Save";
}

	function addUpdate() {

		var frm=document.frmUsers;
		if (frm.txtUserName.value.length < 5 ) {
			alert ("<?php echo $lang_Admin_Users_Errors_UsernameShouldBeAtleastFiveCharactersLong; ?>!");
			frm.txtUserName.focus();
			return false;
		}

		if(!frm.chkUserIsAdmin && frm.cmbUserEmpID.value == '') {
			alert("<?php echo $lang_Admin_Users_Errors_EmployeeIdShouldBeDefined; ?>");
			frm.cmbUserEmpID.focus();
			return;
		}

		if(frm.chkUserIsAdmin && frm.cmbUserGroupID.value == '0') {
			alert("<?php echo $lang_Admin_Users_Errors_FieldShouldBeSelected; ?>!");
			frm.cmbUserGroupID.focus();
			return;
		}

		<?php if ($_GET['isAdmin'] == 'No') { ?>
		<?php if ($_SESSION['ldap'] == "enabled") {} else { ?>
		if (frm.txtUserPassword.value != '') {
			if (frm.txtUserPassword.value.length < 4) {
				alert("<?php echo $lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong; ?>.");
				frm.txtUserPassword.focus();
				return;
			}

			if(frm.txtUserPassword.value != frm.txtUserConfirmPassword.value) {
				alert("<?php echo $lang_Admin_Users_Errors_PasswordsAreNotMatchingRetypeYourNewPassword; ?>");
				frm.txtUserPassword.focus();
				return;
			}
		}
		<?php } ?>
		<?php } ?>
		document.frmUsers.sqlState.value = "UpdateRecord";
		document.frmUsers.submit();
	}

	function toggleAdmin(obj) {
		if (obj.checked) {
			document.getElementById("lyrUserGroupID").style.visibility = 'visible';
			document.getElementById("lyrUserGroupID1").style.visibility = 'visible';
			document.getElementById("lyrEmpID").style.visibility = 'hidden';
		} else {
			document.getElementById("lyrUserGroupID").style.visibility = 'hidden';
			document.getElementById("lyrUserGroupID1").style.visibility = 'hidden';
			document.getElementById("lyrEmpID").style.visibility = 'visible';
		}
	}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2><?php echo $lang_view_Users; ?> : <?php echo (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes')) ? $lang_view_HRAdmin : $lang_view_ESS; ?> <?php echo $lang_view_Users; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmUsers" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&isAdmin=<?php echo $_GET['isAdmin']?>">

  <tr>
    <td height="27" valign='top'> <p>
		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      <?php
		if (isset($this->getArr['msg'])) {
			$expString  = $this->getArr['msg'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {
				echo " " . $expString[$x];
			}
		}
		?>
    </font> </td>
  </tr><td width="177">
</table>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr>
							    <td><?php echo $lang_Commn_code; ?></td>
							    <td> <input type="hidden"  name="txtUserID" value=<?php echo $message[0][0]?>> <strong><?php echo $message[0][0]?></strong> </td>
								<td></td>
								<td></td>
								<td></td>
						  </tr>
						  <tr>
							    <td valign="top" nowrap><span class="error">*</span> <?php echo $lang_Admin_Users_UserName; ?></td>
							    <td><input type="text" name="txtUserName" disabled value="<?php echo $message[0][1]?>"></td>
								<td></td>
								<td valign="top" nowrap></td>
							  	<td></td>
						  </tr>
						  <tr valign="top">
						  	  <td><?php echo $lang_Admin_Users_Status; ?></td>
							  <td><select name="cmbUserStatus" disabled>
							   			<option value="Enabled"><?php echo $lang_Admin_Users_Enabled; ?></option>
							   			<option <?php echo $message[0][8]=='Disabled' ? 'selected' : ''?> value="Disabled"><?php echo $lang_Admin_Users_Disabled; ?></option>
							   	</select></td>
							  <td></td>
							  <td valign="top" nowrap><span id="lyrEmpID" class="error"><?php echo ($message[0][3]=='No')? '*' : '' ?></span> <?php echo $lang_Admin_Users_Employee; ?></td>
							  <td nowrap="nowrap"><input type="text" name="txtUserEmpID" readonly disabled value="<?php echo (isset($message[0][11]) && ($message[0][11] != "")) ?$message[0][11] : $message[0][2] ?><?php echo (isset($message[0][10]) && ($message[0][10] != "")) ?" - ".$message[0][10] : "" ?>"><input type="hidden" name="cmbUserEmpID" disabled value="<?php echo $message[0][2]?>">&nbsp;&nbsp;<input type="button" value="..." disabled onClick="popEmpList()"></td>
						   </tr>
						<?php if ($_GET['isAdmin'] == 'Yes') { ?>
						   <tr>
							   <td valign="top" nowrap><span class="error">*</span> <?php echo $lang_Admin_Users_UserGroup; ?></td>
							   <td><select name="cmbUserGroupID" disabled>
							  		<option value="0">--<?php echo $lang_Admin_Users_SelectUserGroup; ?>--</option>
<?php									$uglist=$this->popArr['uglist'] ;
									for($c=0;$uglist && count($uglist)>$c;$c++)
										if($message[0][9]==$uglist[$c][0])
											echo "<option selected value='" . $uglist[$c][0] ."'>" .$uglist[$c][1]. "</option>";
										else
											echo "<option value='" . $uglist[$c][0] ."'>" .$uglist[$c][1]. "</option>";
?>
							  </select></td>
							  <td>&nbsp;</td>
							   <td><?php if ($message[0][3]=='Yes') { ?>
							   		<input type="hidden" name="chkUserIsAdmin" value="true">
								   <?php } ?></td>
							   <td></td>
						   </tr>
						   <?php } else { ?>
						   <input type="hidden" name="cmbUserGroupID" value="0" >
						   <tr>
							  <td nowrap="nowrap"><?php echo $lang_Admin_Users_NewPassword; ?></td>
							  <td><input type="password" name="txtUserPassword"></td>
							  <td></td>
							  <td nowrap="nowrap"><?php echo $lang_Admin_Users_ConfirmNewPassword; ?></td>
							  <td><input type="password" name="txtUserConfirmPassword"></td>
						  </tr>
						   <?php } ?>
					  <tr>
					  	<td></td>
					  	<td align="right" width="100%">
			<?php	if($locRights['edit']) { ?>
						<img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
			<?php	} else { ?>
						<img src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">
			<?php	}  ?>
						</td>
						<td>
						<img src="../../themes/beyondT/pictures/btn_clear.gif" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" onClick="clearAll();" >
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
                  </table></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>


</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
<?php } ?>
