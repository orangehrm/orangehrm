<?php
function sockComm($postArr) {	

	$host = 'www.orangehrm.com';
	$method = 'POST';
	$path = '/registration/registerAcceptor.php';
	$data = "userName=".$postArr['userName']
			."&userEmail=".$postArr['userEmail']
			."&userComments=".$postArr['userComments']
			."&updates=".(isset($postArr['chkUpdates']) ? '1' : '0');	
			
	$fp = @fsockopen($host, 80);
	  
	if ($fp) {
	    if(!$fp)
	    	return false;
	    	
	    if(!$fp)
	    	return false;
	    	
	    fputs($fp, "POST $path HTTP/1.1\r\n");
	    fputs($fp, "Host: $host\r\n");
	    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
	    fputs($fp, "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n");
	    fputs($fp, "Connection: close\r\n\r\n");
	    fputs($fp, $data);
	    
	    $resp = '';
	    while (!feof($fp)) {
	        $resp .= fgets($fp,128);
	    }
	        
	    fclose($fp);
	    
	    
	    if(strpos($resp, 'SUCCESSFUL') === false) 
	    	return false;
	}
	
	return true;
}

function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 	switch ($currScreen) {
	
		default :
		case 0 	: 	unset($_SESSION['WELCOME']); break;
		case 1 	: 	unset($_SESSION['LICENSE']); break;
		case 2 	: 	unset($_SESSION['DISCLAIMER']); break;
		case 4 	: 	unset($_SESSION['LOCCONFOPT']);	break;
		case 3 	: 	unset($_SESSION['DBCONFOPT']); break;		
		case 5 	: 	unset($_SESSION['LOCCONF']); break;
		case 6 	: 	unset($_SESSION['DOWNLOAD']); break;		
		case 7 	: 	unset($_SESSION['SYSCHECK']); break;
		case 8 	: 	unset($_SESSION['RESTORE']);
					if(isset($_SESSION['DATABASE_BACKUP'])) {
				 		include(ROOT_PATH.'/upgrader/restore/restoreBackup.php');
					}
					break;		
	
		case 9 	: 	return false; break;
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

function extractDbInfo() {
	$dbInfo = array('dbHostName' => trim($_POST['dbHostName']),
					'dbHostPort' => trim($_POST['dbHostPort']),
					'dbName' => trim($_POST['dbName']),
					'dbUserName' => trim($_POST['dbUserName']),
					'dbPassword' => trim($_POST['dbPassword']));
										 
	if(!isset($_POST['chkSameUser'])) {
		$dbInfo['dbOHRMUserName'] = trim($_POST['dbOHRMUserName']);
		$dbInfo['dbOHRMPassword'] = trim($_POST['dbOHRMPassword']);
	}
}

function validateMime($mime) {
	$allowedMimes = array("application/octet-stream", "application/sql", "text/plain", "application/plain");

	foreach ($allowedMimes as $allowedMime) {
		if ($allowedMime == $mime) {
			return true;
		}
	}
	
	return false;
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
		case 'LOCCONFOK' 	: $_SESSION['dbInfo']['locationOhrm'] = $_POST['locationOhrm'];
							  if (fetchDbInfo( $_SESSION['dbInfo']['locationOhrm'])) {							  	
							  	$_SESSION['LOCCONF'] = 'OK';							  	
							  } else {
							  	$error = "failed";
							  }
							  break;		
		case 'DBCONF'		: $_SESSION['DBCONFOPT'] = 'OK'; break;
		case 'LOCCONF'		: $_SESSION['LOCCONFOPT'] = 'OK'; break;
		case 'DBINFO'		: extractDbInfo();
		case 'DOWNLOADOK' 	: $_SESSION['DOWNLOAD'] = 'OK'; break;
		
		case 'UPLOADOK' 	:	if ($_FILES['file']['size'] < 0) {
									$error = "UPLOAD THE BACK UP FILE!";
								}else if (!validateMime($_FILES['file']['type'])) { 
	 								$error = "WRONG FILE FORMAT! <br/> Got ".$_FILES['file']['type']; 
	 									 								
								} else  {									
									$_SESSION['RESTORING'] = 0;
								
									$_SESSION['FILEDUMP'] = file_get_contents($_FILES['file']['tmp_name']);
									$_SESSION['DATABASE_BACKUP']="";										  							
								}
							  	break;
		case 'SYSCHECKOK'	: $_SESSION['SYSCHECK'] = 'OK'; break;
		
		case 'CANCEL' 		:	session_destroy();							
								header("Location: upgrade.php");
								exit(0);
								break;
								
		case 'REGISTER'  :	$_SESSION['CONFDONE'] = 'OK';
							break;
							
								
		case 'REGINFO' 	:	$reqAccept = sockComm($_POST);							
							break;
							
		case 'NOREG' 	:	$reqAccept = sockComm($_POST);

		case 'LOGIN'   	:	session_destroy();
							setcookie('PHPSESSID', '', time()-3600, '/');
							header("Location: ./");
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
	include(ROOT_PATH.'/upgrader/restore/restoringData.php');
}

header('Location: upgrader/upgraderUI.php');
?>