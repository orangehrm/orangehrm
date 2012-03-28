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
    
    public function getStartIncrementNumber($selectedVersion) {
        
        $versions = $this->getVersionAndIncrementerNumbers();
        
        if (isset($versions[$selectedVersion])) {
            return $versions[$selectedVersion];
        }
        
        $dbConnection   = $this->getDbConnection();
        $query          = "SELECT `inc_num` as incNum * FROM `ohrm_upgrade_info` ORDER By `inc_num` DESC LIMIT 1";
        $result         = mysqli_query($dbConnection, $query);
        
        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);
            
            return $row['incNum'];
            
        } else {
            
            throw new Exception("No results found: " . $query);
            
        }
        
    }
    
    /**
     * @todo Look at SchemaIncrement directory and calculate the end Increment
     */
    public function getEndIncrementNumber() {
        
        return 50;
        
    }
    
    public function getVersionAndIncrementerNumbers() {
        
        /*
        $a['2.6']       = zz;
        $a['2.6.0.1']   = zz;
        $a['2.6.0.2']   = zz;
        $a['2.6.1']     = zz;
        $a['2.6.2']     = zz;
        $a['2.6.3']     = zz;
        $a['2.6.4']     = zz;
        $a['2.6.5']     = zz;
        $a['2.6.6']     = zz;
        $a['2.6.7']     = zz;
        $a['2.6.8']     = zz;
        $a['2.6.8.1']   = zz;
        $a['2.6.9']     = zz;
        $a['2.6.10']    = zz;
        */
        $a['2.6.11']    = 48;
        $a['2.6.11.1']  = 48;
        $a['2.6.11.2']  = 48;
        $a['2.6.11.3']  = 48;
        $a['2.6.12']    = 49;
        $a['2.6.12.1']  = 50;
        
        return $a;
        
    }
    
    public function getNewVersion() {
        
        return '2.7';
        
    }
    
    /**
     * @todo Check for upgrade info table and return true/false
     */
    public function isUpgradeInfoTableAvailable() {
        
        return false;
        
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