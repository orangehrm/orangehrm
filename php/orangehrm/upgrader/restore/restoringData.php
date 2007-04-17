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

function alterOldData() {
	connectDB();
	$sqlQString = "UPDATE `hs_hr_employee` SET `employee_id` = `emp_number` WHERE `employee_id` IS NULL";

	$res = mysql_query($sqlQString);

	$err = mysql_errno();
	error_log (date("r")." Alter Old Data failed  with $err\n",3, "log.txt");

	if ($res) {
		return true;
	}

	if (empty($err)) {
		return true;
	}

	return $err;
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
	\$this->version	= '2.2_beta_2';
	\$this->upgrade	= true;
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
					$restorex = new Restore();
					//$connection = mysql_connect($_SESSION['dbInfo']['dbHostName'], $_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword']);	 			//
					//mysql_close();
					$restorex->setConnection($conn);
					$restorex->setDatabase($_SESSION['dbInfo']['dbName']);
					$restorex->setFileSource($_SESSION['FILEDUMP']);
					error_log (date("r")." Fill Data  - Starting\n",3, "log.txt");
					$res = $restorex->fillDatabase();
					if ($res) {
						$_SESSION['RESTORING'] = 3;
						error_log (date("r")." Fill Data - Finished \n",3, "log.txt");

					} else {
						$_SESSION['error'] = mysql_error();
						error_log (date("r")." Fill Data - Failed \n",3, "log.txt");
						error_log (date("r")." Fill Data - Error \n ".mysql_error()."\n" ,3, "log.txt");
					}
					break;
		case 3 	:	fillData(2);
					if (alterOldData()) {
						$_SESSION['RESTORING'] = 4;
					}
					break;
		case 4 	:	writeConfFile();
					$_SESSION['RESTORING'] = 5;
					break;
	}
}


?>
