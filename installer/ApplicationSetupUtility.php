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

require_once ROOT_PATH.'/installer/utils/UniqueIDGenerator.php';
require_once ROOT_PATH.'/symfony/lib/vendor/phpseclib/phpseclib/phpseclib/Crypt/Random.php';
require_once ROOT_PATH.'/symfony/plugins/orangehrmCorePlugin/lib/utility/PasswordHash.php';
require_once ROOT_PATH.'/installer/SystemConfiguration.php';
require_once ROOT_PATH.'/installer/Messages.php';

class ApplicationSetupUtility {

    private static $conn;

    /**
     * Get the log file
     * @return string
     */
    public static function getErrorLogPath() {
        return realpath(dirname(__FILE__)). DIRECTORY_SEPARATOR . 'log.txt';
    }

    public static function createDB() {
        self::connectDB();

        if ($_SESSION['dbCreateMethod'] == 'existing') { // If the user wants to use an existing empty database

            if (self::$conn) {

                $dbName = mysqli_real_escape_string(self::$conn, $_SESSION['dbInfo']['dbName']);

                if (mysqli_select_db(self::$conn, $dbName)) {

                    $result = mysqli_query(self::$conn, "SHOW TABLES");

                    if (mysqli_num_rows($result) > 0) {
                        $_SESSION['error'] = sprintf(Messages::MYSQL_ERR_DB_NOT_EMPTY, $dbName);
                    }

                } else {
                    $mysqlErrNo = mysqli_errno(self::$conn);
                    $error = mysqli_error(self::$conn);
                    $errorMsg = sprintf(Messages::MYSQL_ERR_CANT_CONNECT_TO_DB, $dbName);
                    $errorMsg .= sprintf(Messages::MYSQL_ERR_MESSAGE, $mysqlErrNo, $error);
                    $_SESSION['error'] = $errorMsg;
                }

            }

        } elseif (self::$conn && $_SESSION['dbCreateMethod'] == 'new') { // If the user wants OrangeHRM to create the database for him

            $dbName = mysqli_real_escape_string(self::$conn, $_SESSION['dbInfo']['dbName']);
            $query = "CREATE DATABASE `$dbName`";
            mysqli_query(self::$conn, $query);

            $mysqlErrNo = mysqli_errno(self::$conn);
            $error = mysqli_error(self::$conn);

            if (!($mysqlErrNo == 0 && $error == '')) {
                $errorMsg = Messages::MYSQL_ERR_CANT_CREATE_DB;
                $errorMsg .= sprintf(Messages::MYSQL_ERR_MESSAGE, $mysqlErrNo, $error);
                $_SESSION['error'] = $errorMsg;
            }
        }

    }

    public static function connectDB() {

        if (!self::$conn = mysqli_connect($_SESSION['dbInfo']['dbHostName'], $_SESSION['dbInfo']['dbUserName'], $_SESSION['dbInfo']['dbPassword'], "", $_SESSION['dbInfo']['dbHostPort'])) {
            $error = mysqli_connect_error();
            $mysqlErrNo = mysqli_connect_errno();
            $errorMsg = Messages::MYSQL_ERR_DEFAULT_MESSAGE;
            $errorMsg .= Messages::MYSQL_ERR_MESSAGE;
            $_SESSION['error'] = sprintf($errorMsg, $mysqlErrNo, $error);
            return;
        }
        if (self::$conn instanceof mysqli) {
            self::$conn->set_charset("utf8mb4");
        }

    }

/**
 * Initialize unique ID's
 */
public static function initUniqueIDs() {
	self::connectDB();

	if(!mysqli_select_db(self::$conn, $_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to connect to Database!';
		error_log (date("r")." Initializing unique id's. Error - Unable to connect to Database\n",3, self::getErrorLogPath());
		return false;
	}

	/* Initialize the hs_hr_unique_id table */
	try {
		UniqueIDGenerator::getInstance()->initTable(self::$conn);
	} catch (IDGeneratorException $e) {
		$errMsg = $e->getMessage() . ". Trace = " . $e->getTraceAsString();
		$_SESSION['error'] = $errMsg;
		error_log (date("r")." Initializing hs_hr_unique_id table failed with: $errMsg\n",3, "log.txt");
		return false;
	}
	return true;
}

public static function fillData($phase=1, $source='/dbscript/dbscript-') {
	$source .= $phase.'.sql';
	self::connectDB();

	error_log (date("r")." Fill Data Phase $phase - Connected to the DB Server\n",3, self::getErrorLogPath());

	if(!mysqli_select_db(self::$conn, $_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Cannot select the given database! '.mysqli_error(self::$conn);
		error_log (date("r")." Fill Data Phase $phase - Error - Cannot select the given database\n",3, self::getErrorLogPath());
		return;
	}

	error_log (date("r")." Fill Data Phase $phase - Selected the DB\n",3, self::getErrorLogPath());
	error_log (date("r")." Fill Data Phase $phase - Reading DB Script\n",3, self::getErrorLogPath());

	$queryFile = ROOT_PATH . $source;
	$fp    = fopen($queryFile, 'r');

	error_log (date("r")." Fill Data Phase $phase - Opened DB Script\n",3, self::getErrorLogPath());

	$query = fread($fp, filesize($queryFile));
	fclose($fp);

	error_log (date("r")." Fill Data Phase $phase - Read DB script\n",3, self::getErrorLogPath());

	// Match ; followed by whitespaces and new line. Does not match ; inside a query.
        $dbScriptStatements   = preg_split('/;\s*$/m', $query);  
    
	error_log (date("r")." Fill Data Phase $phase - There are ".count($dbScriptStatements)." Statements in the DB script\n",3, self::getErrorLogPath());

	for($c=0;(count($dbScriptStatements)-1)>$c;$c++) {
                set_time_limit(30);
		if(!@mysqli_query(self::$conn, $dbScriptStatements[$c])) {
			$error = mysqli_error(self::$conn) . ". Query: " . $dbScriptStatements[$c];
            $_SESSION['error'] = $error;
			error_log (date("r")." Fill Data Phase $phase - Error Statement # $c \n",3, self::getErrorLogPath());
			error_log (date("r")." ".$dbScriptStatements[$c]."\n",3, self::getErrorLogPath());
			return;
		}
        }

	if (isset($error)) {
		return;
        }
}

    public static function insertCsrfKey() {
        $csrfKey = self::createCsrfKey();
        $phase = isset($_SESSION['INSTALLING'])?isset($_SESSION['INSTALLING']):2;
        self::connectDB();

        if (!@mysqli_select_db(self::$conn, $_SESSION['dbInfo']['dbName'])) {
            $_SESSION['error'] = 'Unable to access OrangeHRM Database!';
            return;
        }

        error_log (date("r")." Fill Data Phase $phase - Connected to the DB Server\n",3, self::getErrorLogPath());
        
        $query = "INSERT INTO `hs_hr_config` ( `key`, `value`) VALUES ('csrf_secret', '{$csrfKey}');";

        if (!mysqli_query(self::$conn, $query)) {
            $_SESSION['error'] = 'Unable to initialize csrf key (' . mysqli_error(self::$conn) . ')';
            return;
        }
    }

    public static function createCsrfKey() {
        return bin2hex(\phpseclib\Crypt\Random::string(55));
    }

public static function createDBUser() {

if ($_SESSION['dbCreateMethod'] == 'new') {

	self::connectDB();

	if(isset($_SESSION['dbInfo']['dbOHRMUserName'])) {

		$dbName = $_SESSION['dbInfo']['dbName'];
		$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];

		$querryIdentifiedBy = (isset($dbOHRMPassword) && ($dbOHRMPassword !== ''))? "IDENTIFIED BY '$dbOHRMPassword'": '';


      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, CREATE ROUTINE, ALTER ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, EXECUTE
ON `$dbName`.*
TO "$dbOHRMUser"@"localhost"
$querryIdentifiedBy;
USRSQL;

      	if(!@mysqli_query(self::$conn, $query)) {
         	$_SESSION['error'] = mysqli_error(self::$conn) or die();
         	return;
      	}

	  	$dbName = $_SESSION['dbInfo']['dbName'];
	  	$dbOHRMUser = $_SESSION['dbInfo']['dbOHRMUserName'];
		$dbOHRMPassword = $_SESSION['dbInfo']['dbOHRMPassword'];

      	$query = <<< USRSQL
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, CREATE ROUTINE, ALTER ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, EXECUTE
ON `$dbName`.*
TO "$dbOHRMUser"@"%"
$querryIdentifiedBy;
USRSQL;

      	if(!@mysqli_query(self::$conn, $query)) {
         	$_SESSION['error'] = mysqli_error(self::$conn) or die();
         	return;
      	}

	}

}

}

public static function createUser() {

	self::connectDB();

	if(!@mysqli_select_db(self::$conn, $_SESSION['dbInfo']['dbName'])) {
		$_SESSION['error'] = 'Unable to access OrangeHRM Database!';
		return;
	}

    $passwordHasher = new PasswordHash();
    $hash = $passwordHasher->hash($_SESSION['defUser']['AdminPassword']);

	$query = "INSERT INTO `ohrm_user` ( `user_name`, `user_password`,`user_role_id`) VALUES ('" .$_SESSION['defUser']['AdminUserName']. "','".$hash."','1')";

	if(!mysqli_query(self::$conn, $query)) {
		$_SESSION['error'] = 'Unable to Create OrangeHRM Admin User Account';
		return;
	}

}

public static function writeConfFile() {

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

    if (@include_once ROOT_PATH."/lib/confs/sysConf.php") {
        $conf = new sysConf();
        $ohrmVersion = $conf->getVersion();
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

	function __construct() {

		\$this->dbhost	= '$dbHost';
		\$this->dbport 	= '$dbHostPort';
		if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
		\$this->dbname    = 'test_$dbName';		
		}else {
		\$this->dbname    = '$dbName';
		}
		\$this->dbuser    = '$dbOHRMUser';
		\$this->dbpass	= '$dbOHRMPassword';
		\$this->version = '$ohrmVersion';

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

public static function writeSymfonyDbConfigFile() {

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
    
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $testDsn = "mysql:host=$dbHost;dbname=test_$dbName;charset=utf8mb4";
    
    if (is_numeric($dbHostPort)) {
        $dsn = "mysql:host=$dbHost;port=$dbHostPort;dbname=$dbName;charset=utf8mb4";
        $testDsn = "mysql:host=$dbHost;port=$dbHostPort;dbname=test_$dbName;charset=utf8mb4";
    }
	
    $confContent = <<< CONFCONT
all:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: '$dsn'
      username: $dbOHRMUser
      password: $dbOHRMPassword
      attributes: { export: tables }
test:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: '$testDsn'
      username: $dbOHRMUser
      password: $dbOHRMPassword
CONFCONT;

	$filename = ROOT_PATH . '/symfony/config/databases.yml';
	$handle = fopen($filename, 'w');
	fwrite($handle, $confContent);

    fclose($handle);

}

public static function writeLog() {
	$Content = "Client Info\n\n";

	$Content .= "User Agent : ".$_SERVER['HTTP_USER_AGENT']."\n";
	$Content .= "Remote Address : ".$_SERVER['REMOTE_ADDR']."\n\n";

	$Content .= "Server Info\n\n";
	$Content .= "Host : ".$_SERVER['HTTP_HOST']."\n";
	$Content .= "PHP Version : ".constant('PHP_VERSION')."\n";
	$Content .= "Server : ".$_SERVER['SERVER_SOFTWARE']."\n";

	if( array_key_exists('SERVER_ADMIN',$_SERVER)){
        $Content .= "Admin : ". $_SERVER['SERVER_ADMIN'] . "\n\n";
    }

	$Content .= "Document Root : ".$_SERVER['DOCUMENT_ROOT']."\n";
	$Content .= "ROOT_PATH : ".ROOT_PATH."\n\n";

	$Content .= "OrangeHRM Installation Log\n\n";

	$filename = 'installer/log.txt';
	$handle = fopen($filename, 'w');
	fwrite($handle, $Content);
	fclose($handle);
}

    /**
     * Set organization information,
     * Create admin user and assign employee to the admin,
     * Save instance ID to database
     */
    public static function insertSystemConfiguration() {
        $sys = new SystemConfiguration();

        $sys->setOrganizationName($_SESSION['defUser']['organizationName']);
        $sys->setCountry($_SESSION['defUser']['country']);
        if (!is_null($_SESSION['defUser']['language'])) {
            $sys->setLanguage($_SESSION['defUser']['language']);
        }
        $sys->setAdminName($_SESSION['defUser']['adminEmployeeFirstName'], $_SESSION['defUser']['adminEmployeeLastName']);
        $sys->setAdminEmail($_SESSION['defUser']['organizationEmailAddress']);
        $sys->setAdminContactNumber($_SESSION['defUser']['contactNumber']);
        $sys->createAdminUser($_SESSION['defUser']['AdminUserName'], $_SESSION['defUser']['AdminPassword']);
        $sys->setInstanceIdentifier(
            $_SESSION['defUser']['organizationName'],
            $_SESSION['defUser']['organizationEmailAddress'],
            $_SESSION['defUser']['adminEmployeeFirstName'],
            $_SESSION['defUser']['adminEmployeeLastName'],
            $_SERVER['HTTP_HOST'], $_SESSION['country'],
            $sys->getOhrmVersion()
        );
        $sys->setInstanceIdentifierChecksum(
            $_SESSION['defUser']['organizationName'],
            $_SESSION['defUser']['organizationEmailAddress'],
            $_SESSION['defUser']['adminEmployeeFirstName'],
            $_SESSION['defUser']['adminEmployeeLastName'],
            $_SERVER['HTTP_HOST'], $_SESSION['country'],
            $sys->getOhrmVersion()
        );
    }

public static function install() { 
   if (isset($_SESSION['INSTALLING'])) {
	switch ($_SESSION['INSTALLING']) {
		case 0	:	self::writeLog();
					error_log (date("r")." DB Creation - Starting\n",3, self::getErrorLogPath());
					self::createDB();
					error_log (date("r")." DB Creation - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 1;
						error_log (date("r")." DB Creation - No Errors\n",3, self::getErrorLogPath());
					} else {
						error_log (date("r")." DB Creation - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}

					break;

		case 1	:	error_log (date("r")." Fill Data Phase 1 - Starting\n",3, self::getErrorLogPath());
					self::fillData();                                        
                                        self::createMysqlProcedures();
					error_log (date("r")." Fill Data Phase 1 - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 2;
						error_log (date("r")." Fill Data Phase 1 - No Errors\n",3, self::getErrorLogPath());
					} else {
						error_log (date("r")." Fill Data Phase 1 - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}
					break;

		case 2	:	error_log (date("r")." Fill Data Phase 2 - Starting\n",3, self::getErrorLogPath());
					self::fillData(2);
					self::createMysqlEvents();
					self::insertCsrfKey();
					self::insertSystemConfiguration();
					error_log (date("r")." Fill Data Phase 2 - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$res = self::initUniqueIDs();
						if ($res) {
							$_SESSION['INSTALLING'] = 3;
							error_log (date("r")." Fill Data Phase 2 - No Errors\n",3, self::getErrorLogPath());
						}
					} else {
						error_log (date("r")." Fill Data Phase 2 - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}
					break;

		case 3	:	error_log (date("r")." Create DB user - Starting\n",3, self::getErrorLogPath());
					self::createDBUser();
					error_log (date("r")." Create DB user - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 4;
						error_log (date("r")." Create DB user - No Errors\n",3, self::getErrorLogPath());
					} else {
						error_log (date("r")." Create DB user - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}
					break;

		case 4	:	error_log (date("r")." Create OrangeHRM user - Starting\n",3, self::getErrorLogPath());
					error_log (date("r")." Create OrangeHRM user - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 5;
						error_log (date("r")." Create OrangeHRM user - No Errors\n",3, self::getErrorLogPath());
					} else {
						error_log (date("r")." Create OrangeHRM user - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}
					break;

		case 5 :	error_log (date("r")." Write Conf - Starting\n",3, self::getErrorLogPath());
					self::writeConfFile();
					self::writeSymfonyDbConfigFile();
					error_log (date("r")." Write Conf - Done\n",3, self::getErrorLogPath());
					if (!isset($_SESSION['error'])) {
						$_SESSION['INSTALLING'] = 6;
						error_log (date("r")." Write Conf - No Errors\n",3, self::getErrorLogPath());
					} else {
						error_log (date("r")." Write Conf - Errors\n",3, self::getErrorLogPath());
						error_log (date("r")." ".($_SESSION['error'])."\n",3, self::getErrorLogPath());
					}
					break;

	}
  }
}
 
    public static function createMysqlEvents() {
        
        self::connectDB();
        
        $eventTime = date('Y-m-d') . " 00:00:00";
        $query = "CREATE EVENT leave_taken_status_change
                    ON SCHEDULE EVERY 1 HOUR STARTS '$eventTime'
                    DO
                      BEGIN
                        UPDATE hs_hr_leave SET leave_status = 3 WHERE leave_status = 2 AND leave_date < DATE(NOW());
                      END";
        
        if (!mysqli_query(self::$conn, $query)) {
            error_log (date("r")." MySQL Event Error:".mysqli_error(self::$conn)."\n",3, self::getErrorLogPath());
            return false;
        }
        
        return true;
        
    }
    
    public static function createMysqlProcedures(){
        self::connectDB();
        
        $sql = array();
        $sql[] = "DROP FUNCTION IF EXISTS dashboard_get_subunit_parent_id;";
        
        $sql[] = "CREATE FUNCTION  dashboard_get_subunit_parent_id
                (
                  id INT
                )
                RETURNS INT
                DETERMINISTIC
                READS SQL DATA
                BEGIN
                SELECT (SELECT t2.id 
                               FROM ohrm_subunit t2 
                               WHERE t2.lft < t1.lft AND t2.rgt > t1.rgt    
                               ORDER BY t2.rgt-t1.rgt ASC LIMIT 1) INTO @parent
                FROM ohrm_subunit t1 WHERE t1.id = id;

                RETURN @parent;

                END;";
        
        foreach($sql as $query){
            if (!mysqli_query(self::$conn, $query)) {
                error_log (date("r")." MySQL Procedure Error:".mysqli_error(self::$conn)."\n",3, self::getErrorLogPath());
                return false;
            }
        }
        
        return true;
    }

}