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
 *
 */

/* For logging PHP errors */
include_once('lib/confs/log_settings.php');

define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';

require_once ROOT_PATH . '/lib/common/Language.php';
$lan = new Language();
require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lan->getLangPath("full.php"));

session_start();

// To test a different style, can use http://host/orangehrm/login.php?styleSheet=abc
$styleSheet = CommonFunctions::getTheme();
$_SESSION['styleSheet'] = $styleSheet;

$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
$_SESSION['WPATH']= $wpath[0];

require_once ROOT_PATH . '/lib/models/eimadmin/Login.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

/* LDAP Module */

$ldapFile = ROOT_PATH . "/plugins/ldap/LdapLogin.php";
$_SESSION['ldap'] = "disabled";
$_SESSION['ldapStatus'] = "disabled";

if (file_exists($ldapFile)) {
	require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
	require_once ROOT_PATH . '/plugins/PlugInFactory.php';
	$_SESSION['ldap'] = "enabled";
	require_once $ldapFile;
	$ldap = PlugInFactory::factory("LDAP");
	if($ldap->checkAuthorizeLoginUser("Admin") && $ldap->checkAuthorizeModule("Admin")){
		$ldapStatus = $ldap->retrieveLdapStatus();
		$_SESSION['ldapStatus'] = $ldapStatus;
	}else{
		throw new PlugInFactoryException(PlugInFactoryException::PLUGIN_INSTALL_ERROR);
	}
}

/* LDAP Module */

/* Print Benefits Module */

$benefitsFile = ROOT_PATH . "/plugins/printBenefits/pdfHspSummary.php";
$_SESSION['printBenefits'] = "disabled";

if (file_exists($benefitsFile)) {
	$_SESSION['printBenefits'] = "enabled";
}

/* Print Benefits Module */

/* Saving user time zone offset in session: Begins */

if (!empty($_POST['hdnUserTimeZoneOffset'])) {
	$_SESSION['userTimeZoneOffset'] = $_POST['hdnUserTimeZoneOffset'];
} else {
	$_SESSION['userTimeZoneOffset'] = 0;
}

/* Saving user time zone offset in session: Ends */

if ((isset($_POST['actionID'])) && $_POST['actionID'] == 'chkAuthentication') {

	$login = new Login();

	$rset=$login->filterUser(trim($_POST['txtUserName']));

	if (md5("") == $rset[0][1] && $_SESSION['ldapStatus'] == "enabled") {
			$ldapAuth = $ldap->ldapAuth($rset[0][0], $_POST['txtPassword']);
			if ($ldapAuth) { // stuff in normal login process

				$_SESSION['ladpUser'] = true;

				if ($rset[0][5]=='Enabled') {
					if (($rset[0][7] == "Yes") || (($rset[0][7] == "No") && !empty($rset[0][6]))) {
						$_SESSION['user']=$rset[0][3];
						$_SESSION['userGroup']=$rset[0][4];
						$_SESSION['isAdmin']=$rset[0][7];
						$_SESSION['empID']=$rset[0][6]; // This is employee ID with leading zeros.
						$_SESSION['empNumber']=$rset[0][9]; // This is the real employee ID (emp_number) with no padding.

						$_SESSION['fname']=$rset[0][2];

						/* If not an admin user, check if a supervisor and/or project admin */
						$isSupervisor = false;
						$isProjectAdmin = false;
                		$isManager = false;
		                $isDirector = false;
		                $isAcceptor = false;
		                $isOfferer = false;

						if ($_SESSION['isAdmin'] == 'No') {

						$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
						$isSupervisor = $authorizeObj->isSupervisor();
						$isProjectAdmin = $authorizeObj->isProjectAdmin();
                    	$isManager = $authorizeObj->isManager();
	                    $isDirector = $authorizeObj->isDirector();
	                    $isAcceptor = $authorizeObj->isAcceptor();
	                    $isOfferer = $authorizeObj->isOfferer();

					}

					$_SESSION['isSupervisor'] = $isSupervisor;
					$_SESSION['isProjectAdmin'] = $isProjectAdmin;
                	$_SESSION['isManager'] = $isManager;
					$_SESSION['isDirector'] = $isDirector;
					$_SESSION['isAcceptor'] = $isAcceptor;
					$_SESSION['isOfferer'] = $isOfferer;

					$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
					$_SESSION['WPATH']= $wpath[0];

					// TODO: Can set user specific stylesheet here.
					$_SESSION['styleSheet'] = $styleSheet;

					setcookie('Loggedin', 'True', 0, '/');

					header("Location: ./index.php");
					} else {
						$InvalidLogin=3;
					}
				} else {
					$InvalidLogin=2;
				}
			} else {
				$InvalidLogin = 1;
			}

	}else if (md5($_POST['txtPassword']) == $rset[0][1]) {
		if ($rset[0][8] == EmploymentStatus::EMPLOYMENT_STATUS_ID_TERMINATED) {
			$InvalidLogin=5;
		} else if ($rset[0][5]=='Enabled') {
			if (($rset[0][7] == "Yes") || (($rset[0][7] == "No") && !empty($rset[0][6]))) {
				$_SESSION['user']=$rset[0][3];
				$_SESSION['userGroup']=$rset[0][4];
				$_SESSION['isAdmin']=$rset[0][7];
				$_SESSION['empID']=$rset[0][6]; // This is employee ID with leading zeros.
				$_SESSION['empNumber']=$rset[0][9]; // This is the real employee ID (emp_number) with no padding.

				$_SESSION['fname']=$rset[0][2];

				/* If not an admin user, check if a supervisor and/or project admin */
				$isSupervisor = false;
				$isProjectAdmin = false;
           		$isManager = false;
                $isDirector = false;
                $isAcceptor = false;
                $isOfferer = false;

				if ($_SESSION['isAdmin'] == 'No') {

					$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
					$isSupervisor = $authorizeObj->isSupervisor();
					$isProjectAdmin = $authorizeObj->isProjectAdmin();
                   	$isManager = $authorizeObj->isManager();
                    $isDirector = $authorizeObj->isDirector();
                    $isAcceptor = $authorizeObj->isAcceptor();
                    $isOfferer = $authorizeObj->isOfferer();

				}
				$_SESSION['isSupervisor'] = $isSupervisor;
				$_SESSION['isProjectAdmin'] = $isProjectAdmin;
                $_SESSION['isManager'] = $isManager;
				$_SESSION['isDirector'] = $isDirector;
				$_SESSION['isAcceptor'] = $isAcceptor;
				$_SESSION['isOfferer'] = $isOfferer;

				$wpath = explode('/login.php', $_SERVER['REQUEST_URI']);
				$_SESSION['WPATH']= $wpath[0];

				// TODO: Can set user specific stylesheet here.
				$_SESSION['styleSheet'] = $styleSheet;

				setcookie('Loggedin', 'True', 0, '/');

				header("Location: ./index.php");
			} else {
				$InvalidLogin=3;
			}
		} else $InvalidLogin=2;
	} else {
		$InvalidLogin=1;
	}
}

?>
<html>
<head>
<title><?php echo $lang_login_title; ?></title>
<link href="favicon.ico" rel="icon" type="image/gif"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript">

	function submitForm() {

		if(document.loginForm.txtUserName.value == "") {
				alert('<?php echo $lang_login_UserNameNotGiven; ?>');
				return false;
		   }

		if(document.loginForm.txtPassword.value == "") {
				alert("<?php echo $lang_login_PasswordNotGiven; ?>");
				return false;
		   }

		document.loginForm.actionID.value = "chkAuthentication";
		document.loginForm.hdnUserTimeZoneOffset.value = calculateUserTimeZoneOffset();
		document.loginForm.submit();
	}

	if (window.parent != window) {
		window.parent.location.reload();
	}

	function calculateUserTimeZoneOffset() {

		var myDate = new Date();
		var offset = (-1)*myDate.getTimezoneOffset()/60;

		return offset;

	}

</script>
<link href="themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
.bodyTXT {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
}
.style2 {color: #339900}
.loginTXT {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
	height: 19px;
	vertical-align: middle;
	padding-top:0;
}
-->
</style></head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
	<strong><font color='Red' style="padding-left:15px; text-decoration:blink;">
		<?php echo $lang_login_NeedJavascript;?>
		<a href="http://www.mozilla.com/firefox/" target="_blank"
			style="text-decoration:none;"><?php echo $lang_login_MozillaFirefox;?></a>
		</font>
	</strong>
</noscript>
<?php if (isset($_COOKIE['Loggedin']) && isset($_SERVER['HTTP_REFERER'])) { ?>
	<strong><font color='Red' style="padding-left:15px;"><?php echo $lang_login_YourSessionExpired;?></font>
	</strong>
<?php } ?>

<!-- ImageReady Slices (orange_new.psd) -->
<table id="Table_01" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="43%" align="right"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_02.gif" width="266" height="67"></td>
    <td width="57%" align="center">&nbsp;</td>
  </tr>
</table>
<table id="Table_01" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="10%" align="center" bgcolor="#E77817"><table id="Table_01" width="874" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="25"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_05.gif" width="25" height="14" alt=""></td>
        <td width="72"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_06.gif" width="72" height="14" alt=""></td>
        <td colspan="2"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_07.gif" width="107" height="14" alt=""></td>
        <td colspan="5"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_08.gif" width="610" height="14" alt=""></td>
        <td width="403"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_09.gif" width="49" height="14" alt=""></td>
        <td width="52"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_10.gif" width="10" height="14" alt=""></td>
      </tr>
    </table></td>
  </tr>
</table>
  <form name="loginForm" method="post" action="./login.php" onSubmit="submitForm(); return false;">
	<input type="hidden" name="actionID"/>
	<input type="hidden" name="hdnUserTimeZoneOffset" id="hdnUserTimeZoneOffset" value="" />
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="20%"><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="5" height="5" alt=""></td>
    <td width="60%"><table id="Table_01" width="717" height="379" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td rowspan="6"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_01.gif" width="5" height="338" alt=""></td>
        <td rowspan="5" valign="top"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_13.jpg" width="167" height="180">
          <table width="100%"  border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td width="45%">&nbsp;</td>
              <td width="55%">&nbsp;</td>
            </tr>
            <tr>
              <td align="right" class="bodyTXT"><?php echo $lang_login_LoginName; ?> : </td>
              <td>
<?php		if(isset($_POST['txtUserName'])) {?>
              <input name="txtUserName" type="text" class="loginText" value="<?php echo CommonFunctions::escapeHtml($_POST['txtUserName']); ?>" tabindex="1"/>
<?php		} else { ?>
              <input name="txtUserName" type="text" class="loginText" tabindex="1"/>
<?php		} ?>
              </td>
            </tr>
            <tr>
              <td align="right" class="bodyTXT"><?php echo $lang_login_Password; ?> : </td>
              <td><input name="txtPassword" type="password" class="loginText" tabindex="2"/></td>
            </tr>
            <tr>
			<td height="40" valign="bottom" align="center"><input type="Submit" name="Submit" value="<?php echo $lang_login_Login; ?>" class="button" tabindex="3"/> </td>
            <td align="center" valign="bottom"><input type="reset" name="clear" value="<?php echo $lang_login_Clear; ?>" class="button" tabindex="4"/></td>
            </tr>
            <tr>
             	<td></td>
<?php
			if(isset($InvalidLogin)) {
			   switch ($InvalidLogin) {

			   		case 1 : 	$InvalidLoginMes = $lang_login_InvalidLogin;
			   					break;
			   		case 2 : 	$InvalidLoginMes = $lang_login_UserDisabled;
			   					break;
			   		case 3 : 	$InvalidLoginMes = $lang_login_NoEmployeeAssigned;
			   					break;
			   		case 4 : 	$InvalidLoginMes = $lang_login_temporarily_unavailable;
			   					break;
			   		case 5 :    $InvalidLoginMes = $lang_login_EmployeeTerminated;
			   					break;

			   }
			} else {
		       $InvalidLoginMes = "&nbsp;";
			}

			$longMessage = "";

			if (strlen($InvalidLoginMes) > 14){
				$longMessage = $InvalidLoginMes;
				$InvalidLoginMes = "<a title='{$longMessage}' >".substr($InvalidLoginMes, 0, 11)."...</a>";
			}
?>
			<td align='center'><strong ><font color='Red'><?php echo $InvalidLoginMes; ?></font></strong></td>
            </tr>
          </table></td>
          </form>
        <td colspan="2" rowspan="3"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_14.jpg" width="94" height="116"></td>
        <td colspan="2"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_04.gif" width="451" height="29" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_05.gif" width="451" height="46" alt=""></td>
      </tr>
      <tr>
        <td colspan="2"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_06.gif" width="451" height="41" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_19.gif" width="23" height="22"></td>
        <td colspan="3"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_08.gif" width="522" height="22" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_09.gif" width="23" height="169" alt=""></td>
        <td colspan="3" valign="top"><table width="80%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="bodyTXT"><!--<strong>Orange<span class="style2">HRM</span></strong> comes as a comprehensive solution for the efficient management and development of your Human Resource. It will assist you in the complex and strategic process of managing this crucial resource of your enterprise. Based on modular architecture, it facilitates a vast range of HR activities, with features that reflect the main HR management activities. It comes as a web-enabled application and considering the available flexibility, OrangeHRM is a perfect platform for reengineering your HR processes and achieving a new level of HR Management.-->
            <font color="#6C7E89" size="2" face="Tahoma">Orange</font>
	<font size="2" face="Tahoma" color="#FF9933">HRM</font></b>
	<font color="#6C7E89" size="3" face="tahoma" style="line-height: 18px; font-size: 11.8px; font-family: tahoma;">
	<?php echo $lang_login_OrangeHRMDescription; ?>
	</font></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="bottom"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_new_13_2.jpg" width="167" height="25"></td>
        <td colspan="4"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_11.gif" width="545" height="31" alt=""></td>
      </tr>
      <tr>
        <td colspan="5"><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_12.gif" width="657" height="40" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/orange_newMain_13.gif" width="60" height="40" alt=""></td>
      </tr>
      <tr>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="5" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="167" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="23" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="71" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="391" height="1" alt=""></td>
        <td><img src="themes/<?php echo $styleSheet; ?>/pictures/spacer.gif" width="60" height="1" alt=""></td>
      </tr>
    </table></td>
    <td width="20%" valign="top">&nbsp;</td>
  </tr>
</table>
<!-- End ImageReady Slices -->
<table width="100%">
<tr>
<td align="center"><a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> ver 2.6.5 &copy; OrangeHRM Inc. 2005 - 2011 All rights reserved.</td>
</tr>
</table>

</body>
</html>
