<?php
session_start();

$cupath = realpath(dirname(__FILE__).'/../');

define('ROOT_PATH', $cupath);


if(isset($_SESSION['CONFDONE'])) {
	$currScreen = 7;
} elseif(isset($_SESSION['INSTALLING'])) {
	$currScreen = 6;
} elseif(isset($_SESSION['DEFUSER'])) {
	$currScreen = 5;
} elseif(isset($_SESSION['DBCONFIG'])) {
	$currScreen = 4;
} elseif(isset($_SESSION['SYSCHECK'])) {
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

$steps = array('welcome', 'license', 'system check', 'database configuration', 'admin user creation', 'confirmation', 'Installing', 'registration');

$helpLink = array("#welcome", "#license", "#systemChk", "#DBCreation", "#adminUsrCrt", "#confirm", "#installing", "#registration");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OrangeHRM Web Installation Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
  <a href="http://www.orangehrm.com"><img src="../themes/beyondT/pictures/orange3.png" alt="OrangeHRM" name="logo"  width="264" height="62" border="0" id="logo" style="margin-left: 10px;" title="OrangeHRM"></a>
<form name="frmInstall" action="../install.php" method="POST">
<input type="hidden" name="txtScreen" value="<?=$currScreen?>">
<input type="hidden" name="actionResponse">
<a href="./guide/<?=$helpLink[$currScreen]?>" id="help" target="_blank">Help ?</a>
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

    <td nowrap="nowrap" class="left_<?=$tabState?>">&nbsp;</td>
    <td nowrap="nowrap" class="middle_<?=$tabState.$tocome?>"><?=$steps[$i]?></td>
	<td nowrap="nowrap" class="right_<?=$tabState?>">&nbsp;</td>
	
    <?php
		if ($tabState == 'Active') {		
			$tocome = '_tocome';
		}
	} 
	?>
  </tr>
</table>
<?php

switch ($currScreen) {
	
	default :
	case 0 	: 	require(ROOT_PATH . '/installer/welcome.php'); break;
	case 1 	: 	require(ROOT_PATH . '/installer/license.php'); break;
	case 2 	: 	require(ROOT_PATH . '/installer/checkSystem.php'); break;
	case 3 	: 	require(ROOT_PATH . '/installer/dbConfig.php'); break;
	case 4 	: 	require(ROOT_PATH . '/installer/defaultUser.php'); break;
	case 5 	: 	require(ROOT_PATH . '/installer/confirmation.php'); break;
	case 6 	: 	require(ROOT_PATH . '/installer/progress.php'); break;
	case 7 	: 	require(ROOT_PATH . '/installer/registration.php'); break;
}
?>

</form>
<div id="footer"><a href="http://www.orangehrm.com" target="_blank" tabindex="37">OrangeHRM</a> Web Installation Wizard ver 0.2 &copy; hSenid Software 2005 - 2006 All rights reserved.</div>
</div>
</body>
</html>