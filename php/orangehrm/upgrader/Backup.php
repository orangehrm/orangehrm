<?php

define('ROOT_PATH', realpath(dirname(__FILE__)."/../"));

function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 	switch ($currScreen) {
	
		default :
		case 0 	: 	unset($_SESSION['LOCCONF']); break;
		case 1 	: 	unset($_SESSION['DOWNLOAD']); break;
		case 2 	: 	unset($_SESSION['DOWNLOADED']); break;		
	
		case 3 	: 	return false; break;
 	}

 	$currScreen--;
 }

return true;
}

function fetchDbInfo($location) {
	$path = realpath(ROOT_PATH."/../")."/".$location."/lib/confs/";
	
	if (@include $path."Conf.php") {
	
		$confObj = new Conf();
	
		$dbInfo = array( 'dbHostName' => $confObj->dbhost, 
					 	 'dbHostPort' => $confObj->dbport,
					 	 'dbName' => $confObj->dbname,
					 	 'dbUserName' => $confObj->dbuser,
					 	 'dbPassword' => $confObj->dbpass
						);
					
		$_SESSION['dbInfo'] = $dbInfo;
		return true;
	}
	
	$_SESSION['error'] = 'Conf.php file not found in '.$path." $location";
	
	return false;
}

if(!isset($_SESSION['SID']))
	session_start();

clearstatcache();
	
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}
//echo $_REQUEST['actionResponse'];

if(isset($_REQUEST['actionResponse']))
	switch($_REQUEST['actionResponse']) {
		
		case 'LOCCONF' 	: $_SESSION['LOCCONF'] = 'OK'; break;
		case 'LOCCONFOK' 	: $_SESSION['dbInfo']['locationOhrm'] = $_POST['locationOhrm'];
							  if (fetchDbInfo( $_SESSION['dbInfo']['locationOhrm'])) {
							  	$_SESSION['DOWNLOAD'] = 'OK';
							  	$error = "DONE";
							  } else {
							  	$error = "failed";
							  }
							  break;
		case 'DOWNLOADINGOK' : $_SESSION['DOWNLOADED'] = 'OK'; break;
		case 'DBCHOICEOK' 	: $_SESSION['DBCHOICE'] = 'OK'; break;
		case 'CANCEL' 		:	session_destroy();							
								header("Location: backup.php");
								exit(0);
								break;
		
		case 'BACK'		 	:	back($_POST['txtScreen']);
							break;
	}

//echo $_SESSION['DOWNLOADED'];

if (isset($error)) {
	$_SESSION['error'] = $error;
}

if (isset($reqAccept)) {
	$_SESSION['reqAccept'] = $reqAccept;
}

if (isset($_SESSION['DOWNLOADED'])) {	
	header('Location: ../upgrade.php');
	exit(0);
}

header('Location: backup/backupUI.php');

?>