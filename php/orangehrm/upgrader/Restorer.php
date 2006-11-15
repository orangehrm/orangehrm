<?php 
if(!isset($_SESSION['SID']))
	session_start();
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}	

clearstatcache();

$root = realpath(dirname(__FILE__)."/../");

define('ROOT_PATH', $root, 1);
include(ROOT_PATH.'/upgrader/Backup.php');
//**********************************************************************
$dbInfo = array( 'dbHostName' => "localhost",
		         'dbHostPort' => "3306",
				 'dbName' => "hsenidco_hsenid",
				 'dbUserName' => "root",
				 'dbPassword' => "beyondm");

$_SESSION['dbInfo'] = $dbInfo;

//**********************************************************************


function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 switch ($currScreen) {
	
	default :
	case 0 	: 	unset($_SESSION['RESTORE']);
				if(isset($_SESSION['DATABASE_BACKUP'])) {
				 include(ROOT_PATH.'/upgrader/restoreBackup.php');
				}
				break;
	
	case 1 	: 	return false; break;
 }

 $currScreen--;
 }

return true;
}
	
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}


if(isset($_POST['actionResponse']))
	switch($_POST['actionResponse']) {
		
		case 'UPLOADOK' 	:	if($_FILES['file']['size']<0) {
								$error = "UPLOAD THE BACK UP FILE!";
								}else if ($_FILES['file']['type'] != "text/plain") { 
	 							$error = "WRONGFILEFORMAT!";  
								} else  { 
									
										 $_SESSION['RESTORING'] = 0;
								
										  $_SESSION['FILEDUMP'] = file_get_contents($_FILES['file']['tmp_name']);
										  $_SESSION['DATABASE_BACKUP']="";
										  							
									 }
							  break;
	
		case 'CANCEL' 		:	session_destroy();							
								header("Location: ./Upgrader.php");
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
if (isset($_SESSION['RESTORING'])) {
	include(ROOT_PATH.'/upgrader/RestoringData.php');
}


header('Location: ./RestorerUI.php');
?>