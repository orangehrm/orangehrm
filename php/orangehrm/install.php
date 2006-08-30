<?php

function sockComm($postArr) {	

	$host = 'orangehrm.com';
	$method = 'POST';
	$path = '/registration/registerAcceptor.php';
	$data = "userName=".$postArr['userName']
			."&userEmail=".$postArr['userEmail']
			."&userComments=".$postArr['userComments']
			."&updates=".(isset($postArr['chkUpdates']) ? '1' : '0');	
			
	    $fp = fsockopen($host, 80);
	    
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
	    else 
	    	return true;
}

function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 switch ($currScreen) {
	
	default :
	case 0 	: 	unset($_SESSION['WELCOME']); break;
	case 1 	: 	unset($_SESSION['LICENSE']); break;
	case 2 	: 	unset($_SESSION['SYSCHECK']); break;
	case 3 	: 	unset($_SESSION['DBCONFIG']); break;
	case 4 	: 	unset($_SESSION['DEFUSER']); break;
	case 5 	: 	unset($_SESSION['CONFDONE']); break;
	case 6 	: 	unset($_SESSION['CONFDONE']);
				unset($_SESSION['INSTALLING']);
				break;
	case 7 	: 	return false; break;
 }

 $currScreen--;
 }

return true;
}

define('ROOT_PATH', dirname(__FILE__));

if(!isset($_SESSION['SID']))
	session_start();

clearstatcache();
	
if (is_file(ROOT_PATH . '/lib/confs/Conf.php') && !isset($_SESSION['INSTALLING'])) {
	header('Location: ./index.php');
	exit ();
}
	
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}

if(isset($_POST['actionResponse']))
	switch($_POST['actionResponse']) {
		
		case 'WELCOMEOK' : $_SESSION['WELCOME'] = 'OK'; break;
		case 'LICENSEOK' : $_SESSION['LICENSE'] = 'OK'; break;
		case 'SYSCHECKOK' : $_SESSION['SYSCHECK'] = 'OK'; break;

		case 'DBINFO' : $dbInfo = array( 'dbHostName' => trim($_POST['dbHostName']),
										 'dbHostPort' => trim($_POST['dbHostPort']),
										 'dbName' => trim($_POST['dbName']),
										 'dbUserName' => trim($_POST['dbUserName']),
										 'dbPassword' => trim($_POST['dbPassword']));
										 
						if(!isset($_POST['chkSameUser'])) {
							 $dbInfo['dbOHRMUserName'] = trim($_POST['dbOHRMUserName']);
							 $dbInfo['dbOHRMPassword'] = trim($_POST['dbOHRMPassword']);
						}
						
						$_SESSION['dbInfo'] = $dbInfo;
										 
						if(@mysql_connect($dbInfo['dbHostName'].':'.$dbInfo['dbHostPort'], $dbInfo['dbUserName'], $dbInfo['dbPassword'])) {
							$mysqlHost = mysql_get_server_info();
							
							if(intval(substr($mysqlHost,0,1)) < 4 || substr($mysqlHost,0,3) === '4.0')
								$error = 'WRONGDBVER';
							elseif(mysql_select_db($dbInfo['dbName'])) 
									$error = 'DBEXISTS';
								elseif(!isset($_POST['chkSameUser'])) {
									
									mysql_select_db('mysql');
									$rset = mysql_query("SELECT USER FROM user WHERE USER = '" .$dbInfo['dbOHRMUserName'] . "'");
									
									if(mysql_num_rows($rset) > 0)
										$error = 'DBUSEREXISTS';
									else $_SESSION['DBCONFIG'] = 'OK';	
									
								} else $_SESSION['DBCONFIG'] = 'OK';	
								
									
						} else $error = 'WRONGDBINFO';
						
						break;
						
		case 'DEFUSERINFO' :
								$_SESSION['defUser']['AdminUserName'] = trim($_POST['OHRMAdminUserName']);
								$_SESSION['defUser']['AdminPassword'] = trim($_POST['OHRMAdminPassword']);
								$_SESSION['DEFUSER'] = 'OK';
								break;

		case 'CANCEL' :		session_destroy();							
							header("Location: ./install.php");
							exit(0);
							break;
		
		case 'BACK'		 :	back($_POST['txtScreen']);
							break;
								
		case 'CONFIRMED' :	$_SESSION['INSTALLING'] = 0;														
							break;
								
		case 'REGISTER'  :	$_SESSION['CONFDONE'] = 'OK';
							break;
							
								
		case 'REGINFO' :	$reqAccept = sockComm($_POST);							
							break;

		case 'LOGIN'   :	session_destroy();
							header("Location: ./login.php");
							exit(0);							
							break;
	}

if (isset($error)) {
	$_SESSION['error'] = $error;
}

if (isset($reqAccept)) {
	$_SESSION['reqAccept'] = $reqAccept;
}

if (isset($_SESSION['INSTALLING'])) {
	include(ROOT_PATH.'/installer/applicationSetup.php');
}

header('Location: ./installer/installerUI.php');
						
?>