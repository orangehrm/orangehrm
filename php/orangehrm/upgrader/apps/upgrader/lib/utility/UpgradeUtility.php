<?php

class UpgradeUtility {
    
    private $dbConnection = null;
    private $applicationRootPath = null;
    
    public function getDbConnection($host, $username, $password, $dbname, $port) {
        if (!$this->dbConnection) {
            if (!$port) {
                $this->dbConnection = mysqli_connect($host, $username, $password, $dbname);
            } else {
                $this->dbConnection = mysqli_connect($host, $username, $password, $dbname, $port);
            }
        }
        
        if (!$this->dbConnection)
        {
            die('Could not connect: ' . mysqli_connect_error());
        }
        mysqli_autocommit($this->dbConnection, FALSE);
        return $this->dbConnection;
    }
    
    public function setDbConnection($dbConnection) {
        $this->dbConnection = $dbConnection;
    }
    
    public function finalizeTransaction($transactionComplete) {
        if(!$transactionComplete) {
            mysqli_rollback($this->dbConnection);
        } else {
            mysqli_commit($this->dbConnection);
        }
    }
    
    public function commitDatabaseChanges() {
        mysqli_commit($this->dbConnection);
    }
    
    public function closeDbConnection() {
        mysqli_close($this->dbConnection);
    }
    
    public function executeSql($query) {
        $result = mysqli_query($this->dbConnection, $query);
        
        if (!$result) {
            $logMessage = 'MySQL Error: ' . mysqli_error($this->dbConnection) . ". \nQuery: $query\n";
            UpgradeLogger::writeMessage($logMessage);
        }

        return $result;
    }
    
    public function fetchArray($tableData) {
        return mysqli_fetch_array($tableData);
    }
    
    public function setApplicationRootPath ($applicationRootPath) {
        $this->applicationRootPath = $applicationRootPath;
    }
    
    public function writeConfFile($host, $port, $dbName, $username, $password) {

        $dbHost = $host;
        $dbHostPort = $port;
        $dbName = $dbName;
        $dbOHRMUser = $username;
        $dbOHRMPassword = $password;

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

        \$this->dbhost  = '$dbHost';
        \$this->dbport  = '$dbHostPort';
        if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
        \$this->dbname    = 'test_$dbName';     
        }else {
        \$this->dbname    = '$dbName';
        }
        \$this->dbuser    = '$dbOHRMUser';
        \$this->dbpass  = '$dbOHRMPassword';
        \$this->version = '2.7-rc.1';

        \$this->emailConfiguration = dirname(__FILE__).'/mailConf.php';
        \$this->errorLog =  realpath(dirname(__FILE__).'/../logs/').'/';
    }
}
?>
CONFCONT;

        $filename = $this->applicationRootPath . '/lib/confs/Conf.php';
        $handle = fopen($filename, 'w');
        $result = fwrite($handle, $confContent);
    
        fclose($handle);
        return $result;
    }
    
    public function writeSymfonyDbConfigFile($host, $port, $dbName, $username, $password) {

        $dbHost = $host;
        $dbHostPort = $port;
        $dbName = $dbName;
        $dbOHRMUser = $username;
        $dbOHRMPassword = $password;
        
        $dsn = "mysql:host=$dbHost;dbname=$dbName";
        $testDsn = "mysql:host=$dbHost;dbname=test_$dbName";
        
        if (is_numeric($dbHostPort)) {
            $dsn = "mysql:host=$dbHost;port=$dbHostPort;dbname=$dbName";
            $testDsn = "mysql:host=$dbHost;port=$dbHostPort;dbname=test_$dbName";
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

        $filename = $this->applicationRootPath . '/symfony/config/databases.yml';
        $handle = fopen($filename, 'w');
        $result = fwrite($handle, $confContent);
    
        fclose($handle);
        return $result;
    }
    
    public function checkDatabaseConnection($host, $username, $password, $dbname, $port) {
        if (!$port) {
            $this->dbConnection = new mysqli($host, $username, $password, $dbname);
        } else {
            $this->dbConnection = new mysqli($host, $username, $password, $dbname, $port);
        }
        if ($this->dbConnection->connect_error) {
            return false;
        } else {
            return true;
        }
    }
    
    public function checkDatabaseStatus() {
        $sql = "select 1 from `ohrm_upgrade_status`";
        $result = $this->executeSql($sql);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }
    
    public function dropUpgradeStatusTable() {
        $sql = "DROP TABLE `ohrm_upgrade_status`";
        $result = $this->executeSql($sql);
    }
    
    public function insertUpgradeHistory($fromVersion, $toVersion, $fromIncrement, $toIncrement, $date) {
        $fromVersion = $fromVersion ? $fromVersion : 'NULL';
        $toVersion = $toVersion ? $toVersion : 'NULL';
        $valueString = "'$fromVersion' , '$toVersion' ,$fromIncrement, $toIncrement , '$date' ";
        $sql= "INSERT INTO `ohrm_upgrade_history`
                            (`from_version`, `to_version`, `from_increment`, `to_increment`, `date`) 
                            VALUES ($valueString);";
        
        $result = $this->executeSql($sql);
        $this->commitDatabaseChanges();
        return $result;
    }
}