<?php


function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 switch ($currScreen) {
	
	default :
	case 0 	: 	unset($_SESSION['WELCOME']); break;
	case 1 	: 	unset($_SESSION['LICENSE']); break;
	case 2 	: 	unset($_SESSION['DISCLAIMER']); break;
	case 3 	: 	unset($_SESSION['DBCHOICEOK']); break;
	
	case 4 	: 	return false; break;
 }

 $currScreen--;
 }

return true;
}

define('ROOT_PATH', dirname(__FILE__));

if(!isset($_SESSION['SID']))
	session_start();

clearstatcache();
	
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}

if(isset($_POST['actionResponse']))
	switch($_POST['actionResponse']) {
		
		case 'WELCOMEOK' 	: $_SESSION['WELCOME'] = 'OK'; break;
		case 'LICENSEOK' 	: $_SESSION['LICENSE'] = 'OK'; break;
		case 'DISCLAIMEROK' : $_SESSION['DISCLAIMER'] = 'OK'; break;
		case 'DBCHOICEOK' 	: $_SESSION['DBCHOICE'] = 'OK'; break;
		case 'CANCEL' 		:	session_destroy();							
								header("Location: upgrade.php");
								exit(0);
								break;
		
		case 'BACK'		 	:	back($_POST['txtScreen']);
							break;
	}


if (isset($error)) {
	$_SESSION['error'] = $error;
}

if (isset($reqAccept)) {
	$_SESSION['reqAccept'] = $reqAccept;
}

header('Location: upgrader/upgraderUI.php');
?>