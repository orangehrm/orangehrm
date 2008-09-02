<?php
if(!isset($_SESSION))
	session_start();
if (isset($_SESSION['error'])) {
	unset($_SESSION['error']);
}

unset($error);

//require_once(ROOT_PATH.'/upgrader/applicationSetup.php');
require_once ROOT_PATH.'/upgrader/restore/Restore.php';
require_once ROOT_PATH.'/upgrader/backup/Backup.php';
require_once ROOT_PATH.'/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH.'/lib/utils/ConstraintHandler.php';

function createDB() {

	connectDB();
	mysql_query("CREATE DATABASE " . $_SESSION['dbInfo']['dbName']);

	if(!@mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		return;
	}


}

function connectDB() {

	if(!$connect = @mysql_connect($_SESSION['dbInfo']['dbHostName'].':'.$_SESSION['dbInfo']['dbHostPort'], 		$_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'])) {
		$_SESSION['error'] =  'Database Connection Error!';
		return false;
	}
	return $connect;
}

function selectDB() {
	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to connect to Database!';
		error_log (date("r")." Unable to connect to Database\n",3, "log.txt");
		return false;
	}
	return true;
}

/**
 * Apply database constraints.
 *
 * @return boolean true if succeeded, false if failed.
 */
function applyConstraints() {
	if (!connectDB()) {
		return false;
	}
	if (!selectDB()) {
		return false;
	}

	require_once ROOT_PATH.'/dbscript/constraints.php';

	$result = true;

	/* $fkConstraints is set in above file */
	$constraintHandler = new ConstraintHandler();
	$constraintHandler->setLogFile("constraints.log");

	try {
		$failed = $constraintHandler->applyConstraints($fkConstraints);

		if (count($failed) > 0) {
			foreach($failed as $constraint) {
				$failedList[] = $constraintHandler->getConstraintSQL($constraint);
			}

			$_SESSION['error'] = "Failed applying constraints: " . implode(",",$failedList);
			$result = false;
		} else {

			/* Double check that all the constraints have been applied */
			$missing = $constraintHandler->getMissingConstraints($fkConstraints);

			$numMissing = count($missing);
			if ($numMissing > 0) {
				foreach($missing as $constraint) {
					$missingList[] = $constraintHandler->getConstraintSQL($constraint);
				}

				$_SESSION['error'] = "Following $numMissing constraint(s) were missing: " . implode(",",$missingList);
				$result = false;
			}
		}

	} catch (ConstraintHandlerException $e) {
		$_SESSION['error'] = $e->getMessage();
		$result = false;
	}

	return $result;

}

/**
 * Convenience method that adds the given message to the upgrade notes list
 *
 * @param string $note The upgrade note to add to the list
 */
function _appendUpgradeNote($note) {

	if (isset($_SESSION['UPGRADE_NOTES'])) {
		$upgradeNotes = $_SESSION['UPGRADE_NOTES'];
	}
	$upgradeNotes[] = $note;
	$_SESSION['UPGRADE_NOTES'] = $upgradeNotes;
}

/**
 * Convenience method that returns the count by running the given sql
 *
 * Throws an exception on error.
 * @return int count
 */
function _getCount($sql) {

	$result = mysql_query($sql);

	if (!$result) {
		throw new Exception("Error when running query: {$sql}. MysqlError:" . mysql_error());
	}
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$count = $row[0];
	return $count;
}

/**
 * Fix issue with 2.1 and 2.0 where admin groups didn't have permission for
 * the leave module by default. The admin groups could view the leave module without permission
 * due to a bug.
 */
function fixLeaveModulePermissions() {

	if (isset($_SESSION['PREV_VERSION'])) {
		$prevVersion = $_SESSION['PREV_VERSION'];

		/* check if version between 2.0 and 2.1 (inclusive) */
		if (version_compare($prevVersion, "2.0", ">=") && version_compare($prevVersion, "2.1", "<=")) {

			$leaveModId = "MOD005";
			error_log (date("r")." Previous version {$prevVersion} is between 2.0 and 2.1.\n",3, "log.txt");

			/* Grant access to all admin groups to the leave module. */
			$addRightsSql = "INSERT IGNORE INTO hs_hr_rights(userg_id, mod_id, addition, editing, deletion, viewing) " .
						    "SELECT hs_hr_user_group.userg_id, '{$leaveModId}', 1, 1, 1, 1 FROM hs_hr_user_group";
			$result = mysql_query($addRightsSql);
			if (!$result) {
				throw new Exception("Error when running query: {$addRightsSql}. MysqlError:" . mysql_error());
			}

			$message = "Due to changes in the way OrangeHRM handles access rights, all admin user groups have " .
					   "been granted full access to the Leave Module. Please use the \"Users->Admin User Groups\" " .
					   "menu item in the Admin module to restrict access to this module as needed.";
			_appendUpgradeNote($message);
		}
	}
}

/**
 * Checks whether a module entry is there and inserts a new entry if not.
 *
 * @param string $modId Module ID
 * @param string $name  Module Name
 * @param string $owner Module owner
 * @param string $ownerEmail Owner Email
 * @param string $version Module version
 * @param string $description Module description.
 */
function checkAndInsertModule($modId, $name, $owner, $ownerEmail, $version, $description) {

	// Check if module already there
	$countSql = "SELECT COUNT(mod_id) FROM hs_hr_module WHERE mod_id = '{$modId}'";
	$count = _getCount($countSql);

	// If module is missing
	if ($count == 0) {

		$note = "A new module named {$name} was added. All Admin user groups have been granted access to this module. " .
                "Please use the \"Users->Admin User Groups\" menu item in the Admin module to restrict access to this module as needed.";
		_appendUpgradeNote($note);

		// Insert entry into modules table
		$insertSql = sprintf("INSERT INTO `hs_hr_module`(mod_id, name, owner, owner_email, version, description) " .
							 "VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
							 $modId, $name, $owner, $ownerEmail, $version, $description);

		$result = mysql_query($insertSql);
		if (!$result || mysql_affected_rows() != 1) {
			throw new Exception("Error when running query: $insertSql. MysqlError:" . mysql_error());
		}

		// Add rights to admin user groups for the new module
		$addRightsSql = sprintf("INSERT IGNORE INTO hs_hr_rights(userg_id, mod_id, addition, editing, deletion, viewing) " .
					"SELECT hs_hr_user_group.userg_id, '%s', 1, 1, 1, 1 FROM hs_hr_user_group", $modId);
		$result = mysql_query($addRightsSql);
		if (!$result) {
			throw new Exception("Error when running query: $addRightsSql. MysqlError:" . mysql_error());
		}
	}

}

/**
 * Migrate old data from temporary tables
 */
function migrateOldData() {

	if (isset($_SESSION['OLD_LEAVE_TABLE']) && ($_SESSION['OLD_LEAVE_TABLE'] === true)) {

		// Double check that there are no entries in hs_hr_leave or hs_hr_leave_requests
		$sql = "SELECT COUNT(*) FROM `hs_hr_leave`";
		$leaveCount = _getCount($sql);

		$sql = "SELECT COUNT(*) FROM `hs_hr_leave_requests`";
		$requestCount = _getCount($sql);

		if (($leaveCount == 0) && ($requestCount == 0)) {

			/* We use the leave_id as the leave_request_id */
			$insertRequestsSql = "insert into hs_hr_leave_requests(leave_request_id, leave_type_id, " .
					"leave_type_name, date_applied, employee_id) select A.leave_id, A.leave_type_id, " .
					"A.leave_type_name, A.date_applied, A.employee_id From hs_hr_temp_leave As A;";

			$result = mysql_query($insertRequestsSql);
			if (!$result) {
				throw new Exception("Error when running query: $insertRequestsSql. MysqlError:" . mysql_error());
			}

	 		$insertLeave = "insert into hs_hr_leave(leave_id, leave_date, leave_length, leave_status, " .
	 				"leave_comments, leave_request_id, leave_type_id, employee_id) select A.leave_id, " .
	 				"A.leave_date, A.leave_length, A.leave_status, A.leave_comments, A.leave_id, A.leave_type_id, " .
	 				"A.employee_id From hs_hr_temp_leave As A;";

			$result = mysql_query($insertLeave);
			if (!$result) {
				throw new Exception("Error when running query: $insertLeave. MysqlError:" . mysql_error());
			}

		} else {
			$errMsg = "Data migration: unexpected entries found in leave tables";
			error_log (date("r")." {$errMsg}\n",3, "log.txt");
			throw new Exception($errMsg);
		}
	}
}

/**
 * Create temporary tables
 */
function createTempTables() {

	if (isset($_SESSION['OLD_LEAVE_TABLE']) && ($_SESSION['OLD_LEAVE_TABLE'] === true)) {

		$sql = 'create table `hs_hr_temp_leave` (' .
			  '`leave_id` int(11) not null, ' .
			  '`employee_id` varchar(6) not null, ' .
			  '`leave_type_id` varchar(6) not null, ' .
			  '`leave_type_name` varchar(20) not null, ' .
			  '`date_applied` date default null, ' .
			  '`leave_date` date default null, ' .
			  '`leave_length` smallint(6) default null, ' .
			  '`leave_status` smallint(6) default null, ' .
			  '`leave_comments` varchar(80) default null, ' .
			  'primary key  (`leave_id`,`employee_id`,`leave_type_id`) ' .
			  ') engine=innodb default charset=utf8';


		$result = mysql_query($sql);
		if (!$result) {
			$errMsg = mysql_error();
			$_SESSION['error'] = $errMsg;
			error_log (date("r")." Create hs_hr_temp_leave table Failed\nError: {$errMsg}",3, "log.txt");
			return false;
		}
	}

	return true;
}

/**
 * Drop temporary tables created earlier.
 */
function dropTempTables() {
	mysql_query("DROP TABLE IF EXISTS `hs_hr_temp_leave`");
}

function alterOldData() {
	connectDB();
	$sqlQString = "UPDATE `hs_hr_employee` SET `employee_id` = `emp_number` WHERE `employee_id` IS NULL";

	$res = mysql_query($sqlQString);

	if (!$res) {
		$err = mysql_errno();
		error_log (date("r")." Alter Old Data failed  with $err\n",3, "log.txt");
		return false;
	}

	/* Insert any missing modules and assign module rights to new modules inserted */
	try {
		checkAndInsertModule("MOD005", "Leave", "Mohanjith", "mohanjith@beyondm.net", "VER001", "Leave Tracking");
		checkAndInsertModule("MOD006", "Time", "Mohanjith", "mohanjith@orangehrm.com", "VER001", "Time Tracking");
	} catch (Exception $e) {
		$errMsg = $e->getMessage();
		$_SESSION['error'] = $errMsg;
		error_log (date("r")." Insert module failed with: $errMsg\n",3, "log.txt");
		return false;
	}

	try {
		fixLeaveModulePermissions();
	} catch (Exception $e) {
		$errMsg = $e->getMessage();
		$_SESSION['error'] = $errMsg;
		error_log (date("r")." Fixing leave module failed with: $errMsg\n",3, "log.txt");
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


function fillData($phase=1, $source='/dbscript/dbscript-u') {
	$source .= $phase.'.sql';
	connectDB();

	error_log (date("r")." Fill Data Phase $phase - Connected to the DB Server\n",3, "log.txt");

	if(!mysql_select_db($_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to create Database!';
		error_log (date("r")." Fill Data Phase $phase - Error - Unable to create Database\n",3, "log.txt");
		return;
	}

	error_log (date("r")." Fill Data Phase $phase - Selected the DB\n",3, "log.txt");
	error_log (date("r")." Fill Data Phase $phase - Reading DB Script\n",3, "log.txt");

	$queryFile = ROOT_PATH . $source;
	$fp    = fopen($queryFile, 'r');

	error_log (date("r")." Fill Data Phase $phase - Opened DB Script\n",3, "log.txt");

	$query = fread($fp, filesize($queryFile));
	fclose($fp);

	error_log (date("r")." Fill Data Phase $phase - Read DB script\n",3, "log.txt");

	$dbScriptStatements = explode(";", $query);

	error_log (date("r")." Fill Data Phase $phase - There are ".count($dbScriptStatements)." Statements in the DB script\n",3, "log.txt");

	for($c=0;(count($dbScriptStatements)-1)>$c;$c++)
		if(!@mysql_query($dbScriptStatements[$c])) {
			$_SESSION['error'] = mysql_error();
			$error = mysql_error();
			error_log (date("r")." Fill Data Phase $phase - Error Statement # $c \n",3, "log.txt");
			error_log (date("r")." ".$dbScriptStatements[$c]."\n",3, "log.txt");
			return;
		}

	if(isset($error))
		return;
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
	var \$dbpass;

	function Conf() {

	\$this->dbhost	= '$dbHost';
	\$this->dbport 	= '$dbHostPort';
	\$this->dbname	= '$dbName';
	\$this->dbuser	= '$dbOHRMUser';
	\$this->dbpass	= '$dbOHRMPassword';
	\$this->version	= '2.4-beta.8';
	\$this->upgrade	= true;

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

	$Content .= "OrangeHRM Upgrading Log\n\n";

	$filename = 'upgrader/log.txt';
	$handle = fopen($filename, 'a');
	fwrite($handle, $Content);
	fclose($handle);
}

if (isset($_SESSION['RESTORING'])) {

	$conn = connectDB();

	if ($conn)
	switch ($_SESSION['RESTORING']) {
		case -1 : $db = mysql_select_db($_SESSION['dbInfo']['dbName']);
					if ($db) {

						$dump = new Backup();
						$connec = connectDB();
						$dump->setConnection($conn);
						$dump->setDatabase($_SESSION['dbInfo']['dbName']);
						$_SESSION['DATABASE_BACKUP']=$dump->dumpDatabase(true);

						$_SESSION['DATABASE_CONSTRAINTS']=$dump->getConstraints();

						error_log (date("r")." Going to drop existing database- \n",3, "log.txt");

						@mysql_query('DROP DATABASE `'.$_SESSION['dbInfo']['dbName']."`");

						error_log (date("r")."database ".$_SESSION['dbInfo']['dbName']." is droped".mysql_errno()."- \n",3, "log.txt");

						$_SESSION['RESTORING'] = 0;
				    };

		case 0	:	$db = mysql_select_db($_SESSION['dbInfo']['dbName']);
					writeLog();
					error_log (date("r")." DB ".$_SESSION['dbInfo']['dbName']." selected".$db." - Starting\n",3, "log.txt");
					if (!$db) {
						error_log (date("r")." DB Creation - Starting\n",3, "log.txt");
						createDB();
						error_log (date("r")." DB Creation - Done\n",3, "log.txt");
						if (!isset($error) || !isset($_SESSION['error'])) {
							$_SESSION['RESTORING'] = 1;
							error_log (date("r")." DB Creation - No Errors\n",3, "log.txt");
						} else {
							error_log (date("r")." DB Creation - Errors\n",3, "log.txt");
							error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "log.txt");
						}
					}
				   error_log (date("r")." Next step".$_SESSION['RESTORING']." - Starting\n",3, "log.txt");
					break;
		case 1 	:	error_log (date("r")." Fill Data Phase 1 - Starting\n",3, "log.txt");
					fillData();
					error_log (date("r")." Fill Data Phase 1 - Done\n",3, "log.txt");
					if (!isset($error) || !isset($_SESSION['error'])) {
						$_SESSION['RESTORING'] = 2;
						error_log (date("r")." Fill Data Phase 1 - No Errors\n",3, "log.txt");
					} else {
						error_log (date("r")." Fill Data Phase 1 - Errors\n",3, "log.txt");
						error_log (date("r")." ".(isset($error)? $error: $_SESSION['error'])."\n",3, "log.txt");
					}
					break;

		case 2	:	error_log (date("r")." Getting the file content  - \n",3, "log.txt");
					error_log (date("r")." File content ok  - \n",3, "log.txt");

					$res = connectDB();
					if ($res) {
						$res = selectDB();
					}

					if ($res) {
						$res = createTempTables();
					}

					if ($res) {
						$restorex = new Restore();
						//$connection = mysql_connect($_SESSION['dbInfo']['dbHostName'], $_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword']);	 			//
						//mysql_close();
						$restorex->setConnection($conn);
						$restorex->setDatabase($_SESSION['dbInfo']['dbName']);
						$restorex->setFileSource($_SESSION['FILEDUMP']);
						error_log (date("r")." Fill Data  - Starting\n",3, "log.txt");
						$res = $restorex->fillDatabase();
						if ($res) {
							error_log (date("r")." Fill Data - Finished \n",3, "log.txt");

							/* Migrate old data */
							try {
								migrateOldData();
								$_SESSION['RESTORING'] = 3;
								error_log (date("r")." Data migration finished\n",3, "log.txt");
							} catch (Exception $e) {
								$errMsg = $e->getMessage();
								$_SESSION['error'] = $errMsg;
								error_log (date("r")." Data migration failed with: $errMsg\n",3, "log.txt");
							}
						} else {
							$_SESSION['error'] = mysql_error();
							error_log (date("r")." Fill Data - Failed \n",3, "log.txt");
							error_log (date("r")." Fill Data - Error \n ".mysql_error()."\n" ,3, "log.txt");
						}
					}
					dropTempTables();
					break;
		case 3 	:	if(applyConstraints()) {
						fillData(2);
						if (alterOldData()) {
							$_SESSION['RESTORING'] = 4;
						}
					}
					break;
		case 4 	:	writeConfFile();
					$_SESSION['RESTORING'] = 5;
					break;
	}
}


?>
