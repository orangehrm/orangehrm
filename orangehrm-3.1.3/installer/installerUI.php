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
include_once('../lib/confs/log_settings.php');

session_start();

$cupath = realpath(dirname(__FILE__).'/../');

define('ROOT_PATH', $cupath);


if(isset($_SESSION['CONFDONE'])) {
	$currScreen = 7;
} elseif(isset($_SESSION['INSTALLING'])) {
	$currScreen = 6;
} elseif(isset($_SESSION['DEFUSER'])) {
	$currScreen = 5;
} elseif(isset($_SESSION['SYSCHECK'])) {
	$currScreen = 4;
} elseif(isset($_SESSION['DBCONFIG'])) {
	$currScreen = 3;
} elseif(isset($_SESSION['LICENSE'])) {
	$currScreen = 2;
} elseif(isset($_SESSION['WELCOME'])) {
	$currScreen = 1;
} else $currScreen = 0;

if (isset($_SESSION['error'])) {
	$error = $_SESSION['error'];
}

if (isset($_SESSION['reqAccept'])) {
	$reqAccept = $_SESSION['reqAccept'];
}

$steps = array('welcome', 'license', 'database configuration', 'system check', 'admin user creation', 'confirmation', 'Installing', 'registration');

$helpLink = array("#welcome", "#license", "#DBCreation", "#systemChk", "#adminUsrCrt", "#confirm", "#installing", "#registration");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OrangeHRM Web Installation Wizard</title>
<link href="favicon.ico" rel="icon" type="image/gif"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript">

function goToScreen(screenNo) {
	document.frmInstall.txtScreen.value = screenNo;
}

function cancel() {
	document.frmInstall.actionResponse.value  = 'CANCEL';
	document.frmInstall.submit();
}

function back() {
	document.frmInstall.actionResponse.value  = 'BACK';
	document.frmInstall.submit();
}

</script>
<link href="./style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="body">
  <a href="http://www.orangehrm.com"><img src="../symfony/web/themes/default/images/logo.png" alt="OrangeHRM" name="logo"  width="283" height="56" border="0" id="logo" style="margin-left: 10px;margin-bottom: 15px;" title="OrangeHRM"></a>
<form name="frmInstall" action="../install.php" method="POST">
<input type="hidden" name="txtScreen" value="<?php echo $currScreen?>">
<input type="hidden" name="actionResponse">

<table border="0" cellpadding="0" cellspacing="0">
  <tr>
<?php
	$tocome = '';
	for ($i=0; $i < count($steps); $i++) {
		if ($currScreen == $i) {
			$tabState = 'Active';
		} else {
			$tabState = 'Inactive';
		}
?>

    <td nowrap="nowrap" class="left_<?php echo $tabState?>">&nbsp;</td>
    <td nowrap="nowrap" class="middle_<?php echo $tabState.$tocome?>"><?php echo $steps[$i]?></td>
	<td nowrap="nowrap" class="right_<?php echo $tabState?>">&nbsp;</td>

    <?php
		if ($tabState == 'Active') {
			$tocome = '_tocome';
		}
	}
	?>
  </tr>
</table>
<a href="./guide/<?php echo $helpLink[$currScreen]?>" id="help" target="_blank">[Help ?]</a>
<?php

switch ($currScreen) {

	default :
	case 0 	: 	require(ROOT_PATH . '/installer/welcome.php'); break;
	case 1 	: 	require(ROOT_PATH . '/installer/license.php'); break;
	case 2 	: 	require(ROOT_PATH . '/installer/dbConfig.php'); break;
	case 3 	: 	require(ROOT_PATH . '/installer/checkSystem.php'); break;
	case 4 	: 	require(ROOT_PATH . '/installer/defaultUser.php'); break;
	case 5 	: 	require(ROOT_PATH . '/installer/confirmation.php'); break;
	case 6 	: 	require(ROOT_PATH . '/installer/progress.php'); break;
	case 7 	: 	require(ROOT_PATH . '/installer/registration.php'); break;
}
?>

</form>
<div id="footer"><?php include_once(ROOT_PATH . "/symfony/apps/orangehrm/templates/_copyright.php");?></div>  
</div>
</body>
</html>
