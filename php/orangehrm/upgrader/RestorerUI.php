<?php
session_start();
$currScreen = 0;
$cupath = realpath(dirname(__FILE__).'/../');

define('ROOT_PATH', $cupath);

if (isset($_SESSION['RESTORING'])){ 
	$currScreen = 1;
}else if(isset($_SESSION['UPLOAD'])) {
	$currScreen = 0;
}
if (isset($_SESSION['error'])) {
	$error = $_SESSION['error'];
}

if (isset($_SESSION['reqAccept'])) {
	$reqAccept = $_SESSION['reqAccept'];
}

$steps = array('Upload', 'upgrading' ,'status');

$helpLink = array("#upload", '#upgrading','#status');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OrangeHRM Web Upgrader Wizard</title>
<link href="../favicon.ico" rel="icon" type="image/gif"/>
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
  <a href="http://www.orangehrm.com"><img src="../themes/beyondT/pictures/orange3.png" alt="OrangeHRM" name="logo"  width="264" height="62" border="0" id="logo" style="margin-left: 10px;" title="OrangeHRM"></a>
<form name="frmInstall" action="./Restorer.php" method="POST" enctype="multipart/form-data">
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
	case 0 	: 	require(ROOT_PATH . '/upgrader/Restoredata.php'); break;
	case 1 	: 	require(ROOT_PATH . '/upgrader/processing.php'); break;
	
	
}
?>

</form>
<div id="footer"><a href="http://www.orangehrm.com" target="_blank" tabindex="37">OrangeHRM</a> Web Upgrader Wizard ver 0.1 &copy; hSenid Software 2005 - 2006 All rights reserved. </div>
</div>
</body>
</html>