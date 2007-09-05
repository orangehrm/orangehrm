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


function needToUpgrade() {

	$currentVersion = '2.2.1-beta.1';

	if (is_file(ROOT_PATH . '/lib/confs/Conf.php') && !isset($_SESSION['RESTORING'])) {

		include_once ROOT_PATH . '/lib/confs/Conf.php';

		$confObj = new Conf();

		$installedVersion = $confObj->version;

		/* Need to handle 2.2_01 specially due to numbering change from then on.
                 * Next version was: 2.2.0.2
                 */
		if ($installedVersion == "2.2_01") {
			$installedVersion = "2.2.0.1";
		}
		if ((version_compare($installedVersion, $currentVersion) >= 0) && isset($confObj->upgrade) && ($confObj->upgrade)) {
			quit();
		}

	}
}

function parseOldData($str) {

	// Replace old employee ID's
	$newStr = preg_replace("/EMP([0-9]{3})/", "$1", $str);

	// Check for old format leave table (version 2.0.x)
	$_SESSION['OLD_LEAVE_TABLE'] = false;

	// Look for hs_hr_leave_requests table. It's only found in 2.1 upwards
	$pos1 = stripos($newStr, "`hs_hr_leave_requests`");

	if ($pos1 === false) {

		// Look for hs_hr_leave table.
		$pos2 = stripos($newStr, "`hs_hr_leave`");

		// If we have hs_hr_leave but no hs_hr_leave_requests, we have a 2.0.x database.
		if ($pos2 !== false) {

			$_SESSION['OLD_LEAVE_TABLE'] = true;

			// change hs_hr_leave to a temporary table.
			$newStr = str_ireplace("`hs_hr_leave`", "`hs_hr_temp_leave`", $newStr);
		}
	}

	return $newStr;
}

function quit() {
	header('Location: ./index.php');
	exit ();
}

function sockComm($postArr) {

	$host = 'www.orangehrm.com';
	$method = 'POST';
	$path = '/registration/registerAcceptor.php';
	$data = "userName=".$postArr['userName']
			."&userEmail=".$postArr['userEmail']
			."&userComments=".$postArr['userComments']
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
		case 2 	: 	unset($_SESSION['DISCLAIMER']); break;
		case 3 	: 	unset($_SESSION['LOCCONF']); break;
		case 4 	: 	unset($_SESSION['DOWNLOAD']); break;
		case 5 	: 	unset($_SESSION['DBCONFIG']); break;
		case 6 	: 	unset($_SESSION['SYSCHECK']); break;
		case 7 	: 	unset($_SESSION['RESTORE']); break;
		case 8 	: 	unset($_SESSION['RESTORING']);
					if(isset($_SESSION['DATABASE_BACKUP'])) {
				 		include(ROOT_PATH.'/upgrader/restore/restoreBackup.php');
					}
					break;

		case 9  	: 	return false; break;
		case 10 	: 	return false; break;
 	}

 	$currScreen--;
 }

return true;
}

function fetchDbInfo($location) {
	$path = realpath(ROOT_PATH."/../")."/".$location."/lib/confs/";

	if (@include_once $path."Conf.php") {

		$confObj = new Conf();

		$dbInfo = array( 'dbHostName' => $confObj->dbhost,
					 	 'dbHostPort' => $confObj->dbport,
					 	 'dbName' => $confObj->dbname,
					 	 'dbOHRMUserName' => $confObj->dbuser,
					 	 'dbOHRMPassword' => $confObj->dbpass
						);

		$_SESSION['dbInfo'] = $dbInfo;

		/* Conf->version only available from 2.0 */
		if (isset($confObj->version)) {
			$_SESSION['PREV_VERSION'] = ($confObj->version);
		}

		if(@mysql_connect($dbInfo['dbHostName'].':'.$dbInfo['dbHostPort'], $dbInfo['dbOHRMUserName'], $dbInfo['dbOHRMPassword'])) {

			if (!mysql_select_db($dbInfo['dbName'])) {
				$_SESSION['error'] = mysql_error();
				return false;
			}
		} else $error = 'Could not connect to the database server';

		return true;
	}

	$_SESSION['error'] = 'Conf.php file not found in '.$path;

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

	$_SESSION['dbInfo'] = $dbInfo;

	if(@mysql_connect($dbInfo['dbHostName'].':'.$dbInfo['dbHostPort'], $dbInfo['dbUserName'], $dbInfo['dbPassword'])) {
		$mysqlHost = mysql_get_server_info();

		if ($_SESSION['DBCONFOPT'] == 'OK') {
			if (@mysql_select_db($dbInfo['dbName'])) {
				$error="DBEXISTS";
			}
		}

		if(intval(substr($mysqlHost,0,1)) < 4 || substr($mysqlHost,0,3) === '4.0')
			$error = 'WRONGDBVER';

	} else {
		$error = 'WRONGDBINFO';
	}
	if (isset($error)) {
		$_SESSION['error'] = $error;
	}

}

function validateMime($mime) {
	$allowedMimes = array("application/octet-stream", "application/sql", "text/plain", "application/plain", "text/x-sql");

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

if(isset($_POST['actionResponse'])) {
	switch($_POST['actionResponse']) {
		case 'WELCOMEOK' 	: $_SESSION['WELCOME'] = 'OK'; break;
		case 'LICENSEOK' 	: $_SESSION['LICENSE'] = 'OK'; break;
		case 'DISCLAIMEROK' : $_SESSION['DISCLAIMER'] = 'OK'; break;
		case 'LOCCONFOK' 	: $_SESSION['dbInfo']['locationOhrm'] = $_POST['locationOhrm'];
							  if (fetchDbInfo($_SESSION['dbInfo']['locationOhrm'])) {
							  	$_SESSION['LOCCONF'] = 'OK';
							  }
							  //echo '1'.$error;
							  break;
		case 'DBINFO'		: extractDbInfo();
							  if (isset($_SESSION['error'])) {
							  	break;
							  } else {
							  	$_SESSION['DBCONFIG'] = 'OK';
							  }
							  break;
		case 'DOWNLOADOK' 	: $_SESSION['DOWNLOAD'] = 'OK'; break;

		case 'UPLOADOK' 	:	if (!$_FILES || !$_FILES['file']) {
									$error = "Back up file is greater than the maximum upload limit.";
								} else if ($_FILES['file']['error']) {
									$error=$_FILES['file']['error'];
								} else if ($_FILES['file']['size'] <= 0) {
									$error = "Please upload the back up file.";
								} else if (!validateMime($_FILES['file']['type'])) {
	 								$error = "Wrong file format! Got ".$_FILES['file']['type'];
								} else {
									$_SESSION['RESTORING'] = -1;

									$_SESSION['FILEDUMP'] = parseOldData(file_get_contents($_FILES['file']['tmp_name']));
									$_SESSION['DATABASE_BACKUP']="";
								}
							  	break;
		case 'SYSCHECKOK'	: $_SESSION['SYSCHECK'] = 'OK'; break;

		case 'CANCEL' 		:	session_destroy();
								header("Location: upgrade.php");
								exit(0);
								break;

		case 'REGISTER'  :	if (isset($_SESSION['UPGRADE_NOTES'])) {
								$_SESSION['CONFDONE'] = 'OK';
							} else {

								//
								$_SESSION['NOTESDONE'] = 'OK';
							}
							break;

		case 'NOTESOK'   :  $_SESSION['NOTESDONE'] = 'OK';
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
} else {
	needToUpgrade();
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
