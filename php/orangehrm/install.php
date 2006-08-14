<?php

function sockComm($postArr) {

	$host = 'orangehrm.com';
	$method = 'POST';
	$path = '/registration/register.php';
	$data = "userName=" . $postArr['userName'] 
			. "&userEmail=" . $postArr['userEmail'] 
			. "&userComments=" . $postArr['userComments'] 
			. "&updates=" . isset($postArr['chkUpdates']) ? '1' : '0';
	
	    $fp = fsockopen($host, 80);
	    
	    fputs($fp, "POST $path HTTP/1.1\r\n");
	    fputs($fp, "Host: $host\r\n");
	    fputs($fp,"Content-type: application/x-www-form-urlencoded\r\n");
	    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
	    fputs($fp, "User-Agent: MSIE\r\n");
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



if(!isset($_SESSION['SID']))
	session_start();
	
define('ROOT_PATH', dirname(__FILE__));

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
										 
						if(isset($_POST['chkSameUser'])) {
							 $dbInfo['dbOHRMUserName'] = trim($_POST['dbOHRMUserName']);
							 $dbInfo['dbOHRMPassword'] = trim($_POST['dbOHRMPassword']);
						}
						
						$_SESSION['dbInfo'] = $dbInfo;
										 
						if(@mysql_connect($dbInfo['dbHostName'].':'.$dbInfo['dbHostPort'], $dbInfo['dbUserName'], $dbInfo['dbPassword'])) {
							$mysqlHost = substr(mysql_get_server_info(), 0, strpos(mysql_get_server_info(), "-"));
							
							if(intval(substr($mysqlHost,0,1)) < 4 || substr($mysqlHost,0,3) == '4.0')
								$dbConnectError = 'WRONGDBVER';
							elseif(mysql_select_db($dbInfo['dbName'])) 
									$dbConnectError = 'DBEXISTS';
								elseif(isset($_POST['chkSameUser'])) {
									
									mysql_select_db('mysql');
									$rset = mysql_query("SELECT USER FROM user WHERE USER = '" .$dbInfo['dbOHRMUserName'] . "'");
									
									if(mysql_num_rows($rset) > 0)
										$dbConnectError = 'DBUSEREXISTS';
									else $_SESSION['DBCONFIG'] = 'OK';	
									
								} else $_SESSION['DBCONFIG'] = 'OK';	
								
									
						} else $dbConnectError = 'WRONGDBINFO';
						
						break;
						
		case 'DEFUSERINFO' :
								$_SESSION['defUser']['AdminUserName'] = trim($_POST['OHRMAdminUserName']);
								$_SESSION['defUser']['AdminPassword'] = trim($_POST['OHRMAdminPassword']);
								$_SESSION['DEFUSER'] = 'OK';
								break;

		case 'CANCEL' :			session_destroy();
								header("Location: ./install.php");
								break;
								
		case 'CONFIRMED' :
		
								if(!@mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], $_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
									$error = 'Database Connection Error!';
									break;
								}
								
								mysql_query("CREATE DATABASE " . $_SESSION['dbInfo']['dbName']);
								if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
									$error = 'Unable to create Database!';
									break;
								}
								
								$queryFile = ROOT_PATH . "/dbscript/dbscript.sql";
								$fp    = fopen($queryFile, 'r');
								$query = fread($fp, filesize($queryFile));
								fclose($fp);
								
								$dbScriptStatements = explode(";", $query);
								
								for($c=0;(count($dbScriptStatements)-1)>$c;$c++)
									if(!mysql_query($dbScriptStatements[$c])) {  
									     $error = mysql_error();
									     break;
									}
									
								if(isset($error))
									break;
									
if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {
	
	$dbName = $_SESSION['dbInfo']['dbName'];
	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
	$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
	
      $query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"localhost"
IDENTIFIED BY '$dbOHRMPassword';
USRSQL;

      if(!mysql_query($query)) {
         $error = mysql_error();
         break;
      }

      $query = <<< USRSQL
set password for "$dbOHRMUser"@"localhost"
 = old_password('$dbOHRMPassword');
USRSQL;

      if(!mysql_query($query)) {
         $error = mysql_error();
         break;
      }
      
	$dbName = $_SESSION['dbInfo']['dbName'];
	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
	$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
	
      $query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"%"
IDENTIFIED BY '$dbOHRMPassword';
USRSQL;

      if(!mysql_query($query)) {
         $error = mysql_error();
         break;
      }

      $query = <<< USRSQL
set password for "$dbOHRMUser"@"%"
 = old_password('$dbOHRMPassword');
USRSQL;

      if(!mysql_query($query)) {
         $error = mysql_error();
         break;
      }
}

								if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
									$error = 'Unable to access OrangeHRM Database!';
									break;
								}
								
$query = "INSERT INTO `hs_hr_users` VALUES ('USR001','" .$_SESSION['defUser']['AdminUserName']. "','".md5($_SESSION['defUser']['AdminPassword'])."','Admin','',null,'','Yes','1','','0000-00-00 00:00:00','0000-00-00 00:00:00',null,null,'','','','','','','','','','Enabled','','','','','','',0,'','USG001')";

						      if(!mysql_query($query)) {
						         $error = 'Unable to Create OrangeHRM Admin User Account';
						         break;
						      }

						      
						      $dbHost = $_SESSION['dbInfo']['dbHostName'];
							  $dbHostPort = $_SESSION['dbInfo']['dbHostPort'];
							  $dbName = $_SESSION['dbInfo']['dbName'];
							  
if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {
	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
	$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];
} else {	
	$dbOHRMUser = $_SESSION['dbInfo']['dbUserName'];
	$dbOHRMPassword = $_SESSION['dbInfo']['dbPassword'];
}

      $confContent = <<< CONFCONT
<?
class Conf {

	var \$smtphost;
	var \$dbhost;
	var \$dbport;
	var \$dbname;
	var \$dbuser;
	var \$dbpass;

	function Conf() {
		
	\$this->dbhost	= '$dbHost';
	\$this->dbport 	= '$dbHostPort';
	\$this->dbname	= '$dbName';
	\$this->dbuser	= '$dbOHRMUser';
	\$this->dbpass	= '$dbOHRMPassword';
	\$this->smtphost = 'mail.beyondm.net';
	}
}
?>
CONFCONT;
						      
		$filename = ROOT_PATH . '/lib/confs/Conf.php';
	    $handle = fopen($filename, 'a+');
		fwrite($handle, $confContent);
	 
    	fclose($handle);
    	
    						$_SESSION['CONFDONE'] = 'OK';
								break;
								
		case 'REGINFO' :	$reqAccept = sockComm($_POST);
							
							break;

		case 'LOGIN'   :	session_destroy();
							header("Location: ./login.php");
							break;
	}

if(isset($_SESSION['QQQQQ'])) {
} elseif(isset($_SESSION['CONFDONE'])) {
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">

function goToScreen(screenNo) {
	document.frmInstall.txtScreen.value = screenNo;
}

</script>
</head>
<body>
<form name="frmInstall" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<input type="hidden" name="txtScreen" value="<?=$currScreen?>">
<input type="hidden" name="actionResponse">
<?php


switch ($currScreen) {
	
	default :
	case 0 	: 	require(ROOT_PATH . '/installer/welcome.php'); break;
	case 1 	: 	require(ROOT_PATH . '/installer/license.php'); break;
	case 2 	: 	require(ROOT_PATH . '/installer/checkSystem.php'); break;
	case 3 	: 	require(ROOT_PATH . '/installer/dbConfig.php'); break;
	case 4 	: 	require(ROOT_PATH . '/installer/defaultUser.php'); break;
	case 5 	: 	require(ROOT_PATH . '/installer/confirmation.php'); break;
	case 6 	: 	require(ROOT_PATH . '/installer/registration.php'); break;
}
?>

</form>
</body>
</html>