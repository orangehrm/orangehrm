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
$locRights = $_SESSION['localRights'];

function chkPassword($value)
{
    $mtview_controller = new MTViewController() ;
    $matchResult = $mtview_controller->xajaxObjCall($value, 'CPW', 'password');

    $objResponse = new xajaxResponse();

    if ($matchResult)
        $objResponse->addScript("addUpdate();");
    else
        $objResponse->addAlert("Mismatch with your old password");

    return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('chkPassword');
$objAjax->processRequests();

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $message = $this->popArr['editArr'];

    ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php $objAjax->printJavascript(); ?>
<script>

function goBack() {
	javascript:history.back()
}

function clearAll() {

	document.frmchange.txtOldPassword.value = "";
	document.frmchange.txtNewPassword.value = "";
	document.frmchange.txtConfirmPassword.value = "";
}

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

function chkboxCheck() {

	if(document.Edit.title=='Save') {
		xajax_chkPassword(document.frmchange.txtOldPassword.value);
	    return;
	}

    if(document.Edit.title!='Save') {
    	edit();
     	return;
    }
}

function edit() {
	var frm=document.frmchange;

	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;

	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function addUpdate() {
	if(document.frmchange.txtUserName.value=='') {
		alert("Enter Your Username");
		document.frmchange.txtUserName.focus();
		return;
	}

	if(document.frmchange.txtOldPassword.value=='') {
		alert("<?php echo $lang_Admin_Change_Password_Errors_EnterYourOldPassword; ?>");
		document.frmchange.txtOldPassword.focus();
		return;
	}

	if(document.frmchange.txtNewPassword.value=='') {
		alert("<?php echo $lang_Admin_Change_Password_Errors_EnterYourNewPassword; ?>");
		document.frmchange.txtNewPassword.focus();
		return;
	}

	if(document.frmchange.txtConfirmPassword.value=='') {
		alert("<?php echo $lang_Admin_Change_Password_Errors_RetypeYourNewPassword; ?>");
		document.frmchange.txtConfirmPassword.focus();
		return;
	}

	if(document.frmchange.txtNewPassword.value != document.frmchange.txtConfirmPassword.value) {
		alert("<?php echo $lang_Admin_Change_Password_Errors_PasswordsAreNotMatchingRetypeYourNewPassword; ?>");
		document.frmchange.txtConfirmPassword.focus();
		return;
	}

	if(document.frmchange.txtOldPassword.value == document.frmchange.txtNewPassword.value) {
		alert("<?php echo $lang_Admin_Change_Password_Errors_YourOldNewPasswordsAreEqual; ?>");
		document.frmchange.txtNewPassword.focus();
		return;
	}

	var frm=document.frmchange;

	if(document.frmchange.txtNewPassword.value.length < 4) {
		alert("<?php echo $lang_Admin_Users_Errors_PasswordShouldBeAtleastFourCharactersLong; ?>!");
		document.frmchange.txtNewPassword.focus();
		return;
	}

	document.frmchange.sqlState.value = "UpdateRecord";
	document.frmchange.submit();
}

function prepCPW() {

	if (document.getElementById("checkChange").checked) {

		document.getElementById("txtOldPassword").disabled = false;
		document.getElementById("txtNewPassword").disabled = false;
		document.getElementById("txtConfirmPassword").disabled = false;

	} else {

		document.getElementById("txtOldPassword").disabled = true;
		document.getElementById("txtNewPassword").disabled = true;
		document.getElementById("txtConfirmPassword").disabled = true;

	}
}
</script>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body style="padding-left:10px;">
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> </td>
    <td width='100%'><h2><?php echo $lang_index_ChangePassword; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmchange" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&mtcode=<?php echo $this->getArr['mtcode']?>&capturemode=updatemode">

  <tr>
    <td height="27" valign='top'> <p>  <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';" src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'>
	<?php
		if (isset($this->getArr['msg'])) {

			$expString  = $this->getArr['msg'];
			$expString = explode ("_",$expString);
			$length = count($expString);

			$col_def=$expString[$length-1];

			$expString=$this->getArr['msg'];
	?>
			<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
	<?php
				echo eval('return $lang_mtview_'.$expString.';');
	?>
			</font>
	<?php
		}
		?></td>
  </tr>
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
							    <td><?php echo $lang_Commn_code; ?></td>
							    <td><input type="hidden"  name="txtUserID" value="<?php echo $message[0][0]?>"><strong><?php echo $message[0][0]?></strong> </td>
						  </tr>
						  <tr>
							    <td nowrap="nowrap"><?php echo $lang_Admin_Users_UserName; ?></td>
							    <td nowrap="nowrap"><b><?php echo $message[0][1]?></b><input type="hidden" name="txtUserName" value="<?php echo $message[0][1]?>"></td>
						  </tr>
						  <tr>
							    <td nowrap="nowrap"><?php echo $lang_Admin_Change_Password_OldPassword; ?></td>
							    <td><input type="password" disabled name="txtOldPassword" id="txtOldPassword" ></td>
						  </tr>

						  <tr>
							    <td nowrap="nowrap"><?php echo $lang_Admin_Users_NewPassword; ?></td>
							    <td><input type="password" disabled name="txtNewPassword" id="txtNewPassword"  ></td>
						  </tr>
						  <tr>
							    <td nowrap="nowrap"><?php echo $lang_Admin_Users_ConfirmNewPassword; ?></td>
							    <td><input type="password" disabled name="txtConfirmPassword" id="txtConfirmPassword" ></td>
						  </tr>



					  <tr><td></td><td align="right" width="100%">

			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="chkboxCheck();"  >

					<img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >

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
<?php } ?>
