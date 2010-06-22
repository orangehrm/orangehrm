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
$GLOBALS['lang_Admin_Users_ErrorsPasswordMismatch'] = $lang_Admin_Users_ErrorsPasswordMismatch;

function chkPassword($value)
{
    $mtview_controller = new MTViewController() ;
    $matchResult = $mtview_controller->xajaxObjCall($value, 'CPW', 'password');

    $objResponse = new xajaxResponse();

    if ($matchResult)
        $objResponse->addScript("addUpdate();");
    else
        $objResponse->addAlert($GLOBALS['lang_Admin_Users_ErrorsPasswordMismatch']);

    return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('chkPassword');
$objAjax->processRequests();

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode') && isset($this->popArr['editArr']) && is_array($this->popArr['editArr']) ) {
    $editData = $this->popArr['editArr'];

    ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php $objAjax->printJavascript(); ?>
<script type="text/javascript">
//<![CDATA[

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

function chkboxCheck() {

    var editBtn = $('editBtn');

	if(editBtn.title=='<?php echo $lang_Common_Save;?>') {
		xajax_chkPassword(document.frmchange.txtOldPassword.value);
	} else {
        edit();
	}

}

function edit() {
    var editBtn = $('editBtn');
	var frm = document.frmchange;

	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
    }

	editBtn.title = '<?php echo $lang_Common_Save;?>';
    editBtn.value = '<?php echo $lang_Common_Save;?>';
	editBtn.className = 'savebutton';
}

function addUpdate() {

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
//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
</head>

<body>
    <div class="formpage">
        <div class="navigation">
	    	<input type="button" class="savebutton"
		        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		        value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_index_ChangePassword;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_mtview_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

<form name="frmchange" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&mtcode=<?php echo $this->getArr['mtcode']?>&capturemode=updatemode">

    <input type="hidden" name="sqlState" value=""/>
    <input type="hidden"  name="txtUserID" value="<?php echo $editData[0][0]?>"/>
    <span class="formLabel"><?php echo $lang_Commn_code; ?></span>
    <span class="formValue"><?php echo $editData[0][0]?></span>
    <br class="clear"/>

    <span class="formLabel"><?php echo $lang_Admin_Users_UserName; ?></span>
    <span class="formValue"><?php echo $editData[0][1]?></span>
    <input type="hidden" name="txtUserName" value="<?php echo $editData[0][1]?>">
    <br class="clear"/>

    <label for="txtOldPassword"><?php echo $lang_Admin_Change_Password_OldPassword; ?></label>
    <input type="password" disabled name="txtOldPassword" id="txtOldPassword" >
    <br class="clear"/>

    <label for="txtNewPassword"><?php echo $lang_Admin_Users_NewPassword; ?></label>
    <input type="password" disabled name="txtNewPassword" id="txtNewPassword" />
    <br class="clear"/>

    <label for="txtConfirmPassword"><?php echo $lang_Admin_Users_ConfirmNewPassword; ?></label>
    <input type="password" disabled name="txtConfirmPassword" id="txtConfirmPassword" />
	<br class="clear"/>

    <div class="formbuttons">
        <input type="button" class="editbutton" id="editBtn"
            onclick="chkboxCheck();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            title="<?php echo $lang_Common_Edit;?>"
            value="<?php echo $lang_Common_Edit;?>" />
        <input type="button" class="clearbutton" onclick="clearAll();"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
             value="<?php echo $lang_Common_Reset;?>" />
    </div>

</form>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</body>
</html>
<?php } ?>
