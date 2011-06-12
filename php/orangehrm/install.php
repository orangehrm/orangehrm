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

$rootPath = realpath(dirname(__FILE__));

define('ROOT_PATH', $rootPath);
require(ROOT_PATH . '/lib/utils/installUtil.php');

function sockComm($postArr) {

	$host = 'www.orangehrm.com';
	$method = 'POST';
	$path = '/registration/registerAcceptor.php';
	$data = "userName=".$postArr['userName']
			."&userEmail=".$postArr['userEmail']
                        ."&userTp=".$postArr['userTp']
			."&userComments=".$postArr['userComments']
			."&firstName=".$postArr['firstName']
			."&company=".$postArr['company']
			."&updates=".(isset($postArr['chkUpdates']) ? '1' : '0');

	$fp = @fsockopen($host, 80);

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

	return true;
}

function back($currScreen) {

 for ($i=0; $i < 2; $i++) {
 switch ($currScreen) {

	default :
	case 0 	: 	unset($_SESSION['WELCOME']); break;
	case 1 	: 	unset($_SESSION['LICENSE']); break;
	case 2 	: 	unset($_SESSION['DBCONFIG']); break;
	case 3 	: 	unset($_SESSION['SYSCHECK']); break;
	case 4 	: 	unset($_SESSION['DEFUSER']); break;
	case 5 	: 	unset($_SESSION['CONFDONE']); break;
	case 6 	: 	$_SESSION['UNISTALL'] = true;
				unset($_SESSION['CONFDONE']);
				unset($_SESSION['INSTALLING']);
				break;
	case 7 	: 	return false; break;
 }

 $currScreen--;
 }

return true;
}

//define('ROOT_PATH', dirname(__FILE__));

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

/* This $_SESSION['cMethod'] is used to determine wheter to use an existing database or a new one */

$_SESSION['cMethod'] = 'new';

if (isset($_POST['cMethod'])) {
	$_SESSION['cMethod'] = $_POST['cMethod'];
} 

if(isset($_POST['actionResponse']))
	switch($_POST['actionResponse']) {

		case 'WELCOMEOK' : $_SESSION['WELCOME'] = 'OK'; break;
		case 'LICENSEOK' : $_SESSION['LICENSE'] = 'OK'; break;
		case 'SYSCHECKOK' : $_SESSION['SYSCHECK'] = 'OK'; break;

		case 'DBINFO' :                 $uname = "";
                                                $passw = "";
                                                if (isset( $_POST['dbUserName'] )) {
                                                     $uname = trim($_POST['dbUserName']);
                                                }
                                                if (isset( $_POST['dbPassword'] )) {
                                                     $passw = trim($_POST['dbPassword']);
                                                }
                                                $dbInfo = array( 'dbHostName' => trim($_POST['dbHostName']),
										 'dbHostPort' => trim($_POST['dbHostPort']),
										 'dbHostPortModifier' => trim($_POST['dbHostPortModifier']),
										 'dbName' => trim($_POST['dbName']),
										 'dbUserName' => $uname,
										 'dbPassword' => $passw);

						if(!isset($_POST['chkSameUser'])) {
							 $dbInfo['dbOHRMUserName'] = trim($_POST['dbOHRMUserName']);
							 $dbInfo['dbOHRMPassword'] = trim($_POST['dbOHRMPassword']);
						}
						
						if ($_POST['dbCreateMethod'] == 'existing') {
							 $dbInfo['dbUserName'] = trim($_POST['dbOHRMUserName']);
							 $dbInfo['dbPassword'] = trim($_POST['dbOHRMPassword']);
						}
						
						$_SESSION['dbCreateMethod'] = $_POST['dbCreateMethod'];

						$_SESSION['dbInfo'] = $dbInfo;

						if(@mysql_connect($dbInfo['dbHostName'].':'.$dbInfo['dbHostPort'], $dbInfo['dbUserName'], $dbInfo['dbPassword'])) {
							$mysqlHost = mysql_get_server_info();

							if(intval(substr($mysqlHost,0,1)) < 4 || substr($mysqlHost,0,3) === '4.0')
								$error = 'WRONGDBVER';
							elseif($_POST['dbCreateMethod'] == 'new' && mysql_select_db($dbInfo['dbName']))
									$error = 'DBEXISTS';
								elseif($_POST['dbCreateMethod'] == 'new' && !isset($_POST['chkSameUser'])) {

									mysql_select_db('mysql');
									$rset = mysql_query("SELECT USER FROM user WHERE USER = '" .$dbInfo['dbOHRMUserName'] . "'");

									if(mysql_num_rows($rset) > 0)
										$error = 'DBUSEREXISTS';
									else $_SESSION['DBCONFIG'] = 'OK';

								} else $_SESSION['DBCONFIG'] = 'OK';


						} else $error = 'WRONGDBINFO';
							$errorMsg = mysql_error();
							$mysqlErrNo = mysql_errno();

						/* For Data Encryption: Begins */

						$_SESSION['ENCRYPTION'] = "Inactive";
						if (isset($_POST['chkEncryption'])) {

                            $keyResult = createKeyFile('key.ohrm');
                            if ($keyResult) {
                                $_SESSION['ENCRYPTION'] = "Active";
                            } else {
                                $_SESSION['ENCRYPTION'] = "Failed";
                            }
						}

						/* For Data Encryption: Ends */

						break;

		case 'DEFUSERINFO' :
								$_SESSION['defUser']['AdminUserName'] = trim($_POST['OHRMAdminUserName']);
								$_SESSION['defUser']['AdminPassword'] = trim($_POST['OHRMAdminPassword']);
								$_SESSION['DEFUSER'] = 'OK';
								break;

		case 'CANCEL' 	:	session_destroy();
							header("Location: ./install.php");
							exit(0);
							break;

		case 'BACK'		 :	back($_POST['txtScreen']);
							break;

		case 'CONFIRMED' :	$_SESSION['INSTALLING'] = 0;
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
	}

if (isset($error)) {
	$_SESSION['error'] = $error;
}

if (isset($mysqlErrNo)) {
	$_SESSION['mysqlErrNo'] = $mysqlErrNo;
}

if (isset($errorMsg)) {
	$_SESSION['errorMsg'] = $errorMsg;
}

if (isset($reqAccept)) {
	$_SESSION['reqAccept'] = $reqAccept;
}

if (isset($_SESSION['INSTALLING']) && !isset($_SESSION['UNISTALL'])) {
	include(ROOT_PATH.'/installer/applicationSetup.php');
}

if (isset($_SESSION['UNISTALL'])) {
	include(ROOT_PATH.'/installer/cleanUp.php');
}

header('Location: ./installer/installerUI.php');

?>
