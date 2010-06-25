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

require_once ROOT_PATH.'/lib/common/UniqueIDGenerator.php';

// Installing
function createDB() {

	if ($_SESSION['dbCreateMethod'] == 'existing') { // If the user wants to use an existing empty database

		$dbName = $_SESSION['dbInfo']['dbName'];
		$dbHost = $_SESSION['dbInfo']['dbHostName'];
		$dbPort = $_SESSION['dbInfo']['dbHostPort'];
		$dbUser = $_SESSION['dbInfo']['dbUserName'];
		$dbPassword = $_SESSION['dbInfo']['dbPassword'];

		if (mysql_connect($dbHost.':'.$dbPort, $dbUser, $dbPassword)) {

			if (mysql_select_db($dbName)) {

				$result = mysql_query("SHOW TABLES");

				if (mysql_num_rows($result) > 0) {
					$_SESSION['error'] = 'Given database is not empty.';
				}

			} else {
			    $_SESSION['error'] = 'Cannot connect to the database. '.mysql_error();
			}

		} else {
		    $_SESSION['error'] = 'Cannot make a database connection using given details. '.mysql_error();
		}

	} elseif ($_SESSION['dbCreateMethod'] == 'new') { // If the user wants OrangeHRM to create the database for him

		connectDB();
		$dbName = '`'.$_SESSION['dbInfo']['dbName'].'`';
		mysql_query("CREATE DATABASE " . $dbName);

		if(!@mysql_select_db($_SESSION['dbInfo']['dbName'])) {
			$mysqlErrNo = mysql_errno();

			$errorMsg = mysql_error();
			if(!isset($errorMsg) || $errorMsg == '') {
				$errorMsg = 'Unable to create Database.';
			}

			if (isset($mysqlErrNo)) {
				if ($mysqlErrNo == '1102') {
					$errorMsg .= '. Please use valid name for database.';
				}
			}

			$_SESSION['error'] = $errorMsg.' '.mysql_error();

			return;
		}

	}

}

function connectDB() {

	if(!@mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';
		return;
	}

}

/**
 * Initialize unique ID's
 */
function initUniqueIDs() {
	connectDB();

	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to connect to Database!';
		error_log (date("r")." Initializing unique id's. Error - Unable to connect to Database\n",3, "installer/log.txt");
		return false;
	}

	/* Initialize the hs_hr_unique_id table */
	try {
		UniqueIDGenerator::getInstance()->initTable();
	} catch (IDGeneratorException $e) {
		$errMsg = $e->getMessage() . ". Trace = " . $e->getTraceAsString();
		$_SESSION['error'] = $errMsg;
		error_log (date("r")." Initializing hs_hr_unique_id table failed with: $errMsg\n",3, "log.txt");
		return false;
	}
	return true;
}

function fillData($phase=1, $source='/dbscript/dbscript-') {
	$source .= $phase.'.sql';
	connectDB();

	error_log (date("r")." Fill Data Phase $phase - Connected to the DB Server\n",3, "installer/log.txt");

	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Cannot select the given database! '.mysql_error();
		error_log (date("r")." Fill Data Phase $phase - Error - Cannot select the given database\n",3, "installer/log.txt");
		return;
	}

	error_log (date("r")." Fill Data Phase $phase - Selected the DB\n",3, "installer/log.txt");
	error_log (date("r")." Fill Data Phase $phase - Reading DB Script\n",3, "installer/log.txt");

	$queryFile = ROOT_PATH . $source;
	$fp    = fopen($queryFile, 'r');

	error_log (date("r")." Fill Data Phase $phase - Opened DB Script\n",3, "installer/log.txt");

	$query = fread($fp, filesize($queryFile));
	fclose($fp);

	error_log (date("r")." Fill Data Phase $phase - Read DB script\n",3, "installer/log.txt");

	$dbScriptStatements = explode(";", $query);

	error_log (date("r")." Fill Data Phase $phase - There are ".count($dbScriptStatements)." Statements in the DB script\n",3, "installer/log.txt");

	for($c=0;(count($dbScriptStatements)-1)>$c;$c++)
		if(!@mysql_query($dbScriptStatements[$c])) {
			$_SESSION['error'] = mysql_error();
			$error = mysql_error();
			error_log (date("r")." Fill Data Phase $phase - Error Statement # $c \n",3, "installer/log.txt");
			error_log (date("r")." ".$dbScriptStatements[$c]."\n",3, "installer/log.txt");
			return;
		}

	if(isset($error))
		return;
}

function createDBUser() {

if ($_SESSION['dbCreateMethod'] == 'new') {

	connectDB();

	if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {

		$dbName = $_SESSION['dbInfo']['dbName'];
		$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];

		$querryIdentifiedBy = (isset($dbOHRMPassword) && ($dbOHRMPassword !== ''))? "IDENTIFIED BY '$dbOHRMPassword'": '';


      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"localhost"
$querryIdentifiedBy;
USRSQL;

      	if(!@mysql_query($query)) {
         	$_SESSION['error'] = mysql_error() or die();
         	return;
      	}

      	$query = <<< USRSQL
set password for "$dbOHRMUser"@"localhost"
 = old_password('$dbOHRMPassword');
USRSQL;

		if (isset($dbOHRMPassword) && ($dbOHRMPassword !== '')) {
      		if (!@mysql_query($query)) {
        		$_SESSION['error'] = mysql_error() or die();
         		return;
      		}
		}

	  	$dbName = $_SESSION['dbInfo']['dbName'];
	  	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];

      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX
ON `$dbName`.*
TO "$dbOHRMUser"@"%"
$querryIdentifiedBy;
USRSQL;

      	if(!@mysql_query($query)) {
         	$_SESSION['error'] = mysql_error() or die();
         	return;
      	}

      	$query = <<< USRSQL
set password for "$dbOHRMUser"@"%"
 = old_password('$dbOHRMPassword');
USRSQL;

		if (isset($dbOHRMPassword) && ($dbOHRMPassword !== '')) {
      		if(!@mysql_query($query)) {
         		$_SESSION['error'] = mysql_error() or die();
         		return;
      		}
		}
	}

}

}

function createUser() {

	connectDB();

	if(!@mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to access OrangeHRM Database!';
		return;
	}

	$query = "INSERT INTO `hs_hr_users` (`id`, `user_name`, `user_password`, `first_name`, `last_name`, `emp_number`, `user_hash`, `is_admin`, `receive_notification`, `description`, `modified_user_id`, `created_by`, `title`, `department`, `phone_home`, `phone_mobile`,   `phone_work`, `phone_other`, `phone_fax`, `email1`, `email2`, `status`, `address_street`, `address_city`, `address_state`,  `address_country`, `address_postalcode`, `user_preferences`, `deleted`, `employee_status`, `userg_id`) VALUES ('USR001','" .$_SESSION['defUser']['AdminUserName']. "','".md5($_SESSION['defUser']['AdminPassword'])."','Admin','',null,'','Yes','1','',null,null,'','','','','','','','','','Enabled','','','','','','',0,'','USG001')";

	if(!mysql_query($query)) {
		$_SESSION['error'] = 'Unable to Create OrangeHRM Admin User Account';
		return;
	}

}

function writeConfFile() {

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
<?php
class Conf {

	var \$smtphost;
	var \$dbhost;
	var \$dbport;
	var \$dbname;
	var \$dbuser;
	var \$version;

	function Conf() {

		\$this->dbhost	= '$dbHost';
		\$this->dbport 	= '$dbHostPort';
		if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
		\$this->dbname    = 'test_$dbName';		
		}else {
		\$this->dbname    = '$dbName';
		}
		\$this->dbuser    = '$dbOHRMUser';
		\$this->dbpass	= '$dbOHRMPassword';
		\$this->version = '2.6-beta.7';

		\$this->emailConfiguration = dirname(__FILE__).'/mailConf.php';
		\$this->errorLog =  realpath(dirname(__FILE__).'/../logs/').'/';
	}
}
?>
CONFCONT;

	$filename = ROOT_PATH . '/lib/confs/Conf.php';
	$handle = fopen($filename, 'w');
	fwrite($handle, $confContent);

    fclose($handle);

}

function writeSymfonyDbConfigFile() {

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
all:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: 'mysql:host=$dbHost;dbname=$dbName'
      username: $dbOHRMUser
      password: $dbOHRMPassword
      port: $dbHostPort
      attributes: { export: tables }
test:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: 'mysql:host=$dbHost;dbname=test_$dbName'
      username: $dbOHRMUser
      password: $dbOHRMPassword
      port: $dbHostPort
CONFCONT;

	$filename = ROOT_PATH . '/symfony/config/databases.yml';
	$handle = fopen($filename, 'w');
	fwrite($handle, $confContent);

    fclose($handle);

}

function writeLog() {
	$Content = "Client Info\n\n";

	$Content .= "User Agent : ".$_SERVER['HTTP_USER_AGENT']."\n";
	$Content .= "Remote Address : ".$_SERVER['REMOTE_ADDR']."\n\n";

	$Content .= "Server Info\n\n";
	$Content .= "Host : ".$_SERVER['HTTP_HOST']."\n";
	$Content .= "PHP Version : ".constant('PHP_VERSION')."\n";
	$Content .= "Server : ".$_SERVER['SERVER_SOFTWARE']."\n";
	$Content .= "Admin : ".$_SERVER['SERVER_ADMIN']."\n\n";

	$Content .= "Document Root : ".$_SERVER['DOCUMENT_ROOT']."\n";
	$Content .= "ROOT_PATH : ".ROOT_PATH."\n\n";

	$Content .= "OrangeHRM Installation Log\n\n";

	$filename = 'installer/log.txt';
	$handle = fopen($filename, 'w');
	fwrite($handle, $Content);
	fclose($handle);
}

   if (isset($_SESSION['INSTALLING'])) {
	switch ($_SESSION['INSTALLING']) {
		case 0	:	writeLog();
					error_log (date("r")." DB Creation - Starting\n",3, "installer/log.txt");
					createDB();
					error_log (date("r")." DB Creation - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 1;
						error_log (date("r")." DB Creation - No Errors\n",3, "installer/log.txt");
					} else {
						error_log (date("r")." DB Creation - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}

					break;

		case 1	:	error_log (date("r")." Fill Data Phase 1 - Starting\n",3, "installer/log.txt");
					fillData();
					error_log (date("r")." Fill Data Phase 1 - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 2;
						error_log (date("r")." Fill Data Phase 1 - No Errors\n",3, "installer/log.txt");
					} else {
						error_log (date("r")." Fill Data Phase 1 - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}
					break;

		case 2	:	error_log (date("r")." Fill Data Phase 2 - Starting\n",3, "installer/log.txt");
					fillData(2);
					error_log (date("r")." Fill Data Phase 2 - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$res = initUniqueIDs();
						if ($res) {
							$_SESSION['INSTALLING'] = 3;
							error_log (date("r")." Fill Data Phase 2 - No Errors\n",3, "installer/log.txt");
						}
					} else {
						error_log (date("r")." Fill Data Phase 2 - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}
					break;

		case 3	:	error_log (date("r")." Create DB user - Starting\n",3, "installer/log.txt");
					createDBUser();
					error_log (date("r")." Create DB user - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 4;
						error_log (date("r")." Create DB user - No Errors\n",3, "installer/log.txt");
					} else {
						error_log (date("r")." Create DB user - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}
					break;

		case 4	:	error_log (date("r")." Create OrangeHRM user - Starting\n",3, "installer/log.txt");
					createUser();
					error_log (date("r")." Create OrangeHRM user - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 5;
						error_log (date("r")." Create OrangeHRM user - No Errors\n",3, "installer/log.txt");
					} else {
						error_log (date("r")." Create OrangeHRM user - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}
					break;

		case 5 :	error_log (date("r")." Write Conf - Starting\n",3, "installer/log.txt");
					writeConfFile();
					writeSymfonyDbConfigFile();
					error_log (date("r")." Write Conf - Done\n",3, "installer/log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 6;
						error_log (date("r")." Write Conf - No Errors\n",3, "installer/log.txt");
					} else {
						error_log (date("r")." Write Conf - Errors\n",3, "installer/log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "installer/log.txt");
					}
					break;

	}
  }
?>
