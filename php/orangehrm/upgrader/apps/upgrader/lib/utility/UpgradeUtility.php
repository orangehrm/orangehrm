<?php

class UpgradeUtility {
    
    private $connection = null;
    private $applicationRootPath = null;
    
    public function getDbConnection($host, $username, $password, $dbname, $port) {
        if (!$this->connection) {
            if (!$port) {
                $this->connection = mysqli_connect($host, $username, $password, $dbname);
            } else {
                $this->connection = mysqli_connect($host, $username, $password, $dbname, $port);
            }
        }
        
        if (!$this->connection)
        {
            die('Could not connect: ' . mysqli_connect_error());
        }
        mysqli_autocommit($this->connection, FALSE);
        return $this->connection;
    }
    
    public function setDbConnection($connection) {
        $this->connection = $connection;
    }
    
    public function finalizeTransaction($transactionComplete) {
        if(!$transactionComplete) {
            mysqli_rollback($this->connection);
        } else {
            mysqli_commit($this->connection);
        }
    }
    
    public function closeDbConnection() {
        mysqli_close($this->connection);
    }
    
    public function executeSql($query) {
        
        return mysqli_query($this->connection, $query);        

       /* $result = mysqli_query($this->connection, $query);
        
        if (!$result) {
            $logMessage = 'MySQL Error: ' . mysqli_error($this->connection) . ". \nQuery:\n";
            // Call your logger here.
        }

        return $result;        */
        
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
            $connection = new mysqli($host, $username, $password, $dbname);
        } else {
            $connection = new mysqli($host, $username, $password, $dbname, $port);
        }
        if ($connection->connect_error) {
            return false;
        } else {
            return true;
        }
    }
}