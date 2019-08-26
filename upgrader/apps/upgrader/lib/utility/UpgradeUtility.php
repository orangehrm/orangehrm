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
        $this->dbConnection->set_charset("utf8");
        mysqli_autocommit($this->dbConnection, FALSE);
        return $this->dbConnection;
    }

    public function getDbError() {
        if ($this->dbConnection) {
            return mysqli_error($this->dbConnection);
        } else {
            return '';
        }
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
        
        UpgradeLogger::writeLogMessage('Executing SQL:' . $query);
        
        if (!$result) {
            $logMessage = 'MySQL Error: ' . mysqli_error($this->dbConnection) . ". \nQuery: $query\n";
            UpgradeLogger::writeErrorMessage($logMessage, true);
        }

        return $result;
    }
    
    public function fetchArray($tableData) {
        return mysqli_fetch_array($tableData);
    }
    
    public function escapeString($string) {
        return mysqli_real_escape_string($this->dbConnection, $string);
    }
    
    public function decodeHtmlEntity($string) {
        return html_entity_decode($string, ENT_QUOTES);
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
        if (@include_once sfConfig::get('sf_root_dir')."/../lib/confs/sysConf.php") {
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

        \$this->dbhost  = '$dbHost';
        \$this->dbport  = '$dbHostPort';
        if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
        \$this->dbname    = 'test_$dbName';     
        }else {
        \$this->dbname    = '$dbName';
        }
        \$this->dbuser    = '$dbOHRMUser';
        \$this->dbpass  = '$dbOHRMPassword';
        \$this->version = '$ohrmVersion';

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
    
    public function getRowCount($result) {
        return mysqli_num_rows($result);
    }
    /**
     * @todo Look at SchemaIncrement directory and calculate the end Increment
     */
    public function getEndIncrementNumber() {
        
        return 72;
        
    }
    
    public function getVersionAndIncrementerNumbers() {
        
        /*
         * NOTE: The increment numbers listed here
         * are the schema increment to start with when upgrading the given
         * version. 
         * 
         * Not the schema increment number that corresponds to that versions db schema.
         * 
         */
        /*
        $a['2.6']       = zz;
        $a['2.6.0.1']   = zz;
        $a['2.6.0.2']   = zz;
        $a['2.6.1']     = zz;
        $a['2.6.2']     = zz;
        $a['2.6.3']     = zz;
        $a['2.6.4']     = zz;
        */
        $a['2.6.5']     = 41; 
        $a['2.6.6']     = 41;                
        $a['2.6.7']     = 42;
        $a['2.6.8']     = 43; 
        $a['2.6.8.1']   = 43; // No db change between 2.6.8 -> 2.6.8.1       
        $a['2.6.9']     = 44;        
        $a['2.6.9.1']   = 44; // No db change between 2.6.9 -> 2.6.9.1       
        $a['2.6.10']    = 45;
        $a['2.6.11']    = 46;
        $a['2.6.11.1']  = 47;
        $a['2.6.11.2']  = 48;
        $a['2.6.11.3']  = 48;
        $a['2.6.12']    = 49;
        $a['2.6.12.1']  = 50;
        $a['2.7']       = 51;
        $a['2.7.1']     = 55;
        $a['3.0']       = 56;
        $a['3.0.1']     = 57;
        $a['3.1']       = 58;
        $a['3.1.1']     = 59;
        $a['3.1.2']     = 60;
        $a['3.1.3']     = 61;
        $a['3.1.4']     = 61;
        $a['3.2']       = 62;
        $a['3.2.1']     = 62;
        $a['3.3']       = 63;
        $a['3.3.1']     = 63; // No db change between 3.3 -> 3.3.1
        $a['3.3.2']     = 64; // No db change between 3.3.2 -> 3.3.3
        $a['3.3.3']     = 64;// 3.3.2 to 4.0
        $a['4.0']       = 65;// 4.0 to 4.1
        $a['4.1']       = 66;// 4.1 to 4.1.1
        $a['4.1.1']     = 67;// 4.1.1 to 4.1.2
        $a['4.1.2']     = 68;// 4.1.2 to 4.2
        $a['4.2']       = 69;// No db change between 4.2 to 4.2.0.1
        $a['4.2.0.1']   = 69; //4.2.0.1 to 4.3
        $a['4.3']       = 70; //4.3 to 4.3.1
        $a['4.3.1']     = 71; //4.3.1 to 4.3.2
        $a['4.3.2']     = 72; //4.3.2 to 4.3.3
        return $a;
        
    }
    
    /**
     * @todo Get the new version which need to upgrade the system
     */
    public function getNewVersion() {

        if (@include_once sfConfig::get('sf_root_dir')."/../lib/confs/sysConf.php") {
            $conf = new sysConf();
            return $conf->getVersion();
        }
        return '';
        
    }
    
    /**
     * @todo Get the current version of the system
     * 
     * unused, therefore commented out.
     */
    /*public function getCurrentVersion($incrementNumber) {
        
        return '2.11.3';
        
    }*/
    
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
        $sql = "SHOW TABLES LIKE 'ohrm_upgrade_status'";
        $result = $this->executeSql($sql);
                
        if (mysqli_num_rows($result)>0) {
            
            return false;
        } else {
           
            return true;
        }
    }
    
    public function dropUpgradeStatusTable() {
        $sql = "DROP TABLE IF EXISTS `ohrm_upgrade_status`";
        $result = $this->executeSql($sql);
    }
    
    public function insertUpgradeHistory($fromVersion, $toVersion, $fromIncrement, $toIncrement, $date) {
        $fromVersion = $fromVersion ? $fromVersion : 'NULL';
        $toVersion = $toVersion ? $toVersion : 'NULL';
        $valueString = "'$fromVersion' , '$toVersion' ,$fromIncrement, $toIncrement , '$date' ";
        $sql= "INSERT INTO `ohrm_upgrade_history`
                            (`start_version`, `end_version`, `start_increment`, `end_increment`, `upgraded_date`) 
                            VALUES ($valueString);";
        
        $result = $this->executeSql($sql);
        $this->commitDatabaseChanges();
        return $result;
    }
    
    public function saveImage($filePath, $imageData) {
        file_put_contents( $filePath, $imageData );
    }
    
    /**
     * @param int $startIncNumber
     * @param int $endIncNumber
     * @return array
     */
    public function getNotes($startIncNumber, $endIncNumber) {
        
        $notes = array();
        
        $notes[] = "If you have enabled data encryption in your current version, you need to copy the file 'lib/confs/cryptokeys/key.ohrm' from your current installation to corresponding location in the new version.";
        
        for ($i = $startIncNumber; $i <= $endIncNumber; $i++) {
            
            $className      = 'SchemaIncrementTask' . $i;      
            $schemaObject   = new $className;
            $schemaNotes    = $schemaObject->getNotes();
            
            if (!empty($schemaNotes)) {
                $notes = array_merge($notes, $schemaNotes);
            }
            
        }
        
        return $notes;
        
    }

    public function getInstalledAddons() {
        $query = 'select * from ohrm_marketplace_addon where plugin_name is not null';
        return mysqli_fetch_all($this->executeSql($query), MYSQLI_ASSOC);
    }

    public function getMarketplaceBaseUrl() {
        $query = 'select `value` from hs_hr_config where `key` = "base_url"';
        return $this->executeSql($query)->fetch_assoc()['value'];
    }

    public function getMarketplaceAccessToken() {
        $baseUrl = $this->getMarketplaceBaseUrl();
        $query = 'select `key`, `value` from hs_hr_config where `key` in ("client_id", "client_secret")';
        $result = mysqli_fetch_all($this->executeSql($query), MYSQLI_ASSOC);
        $data = array_column($result, 'value', 'key');
        $data['grant_type'] = 'client_credentials';
        $ch = curl_init("$baseUrl/oauth/v2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response)->access_token;
    }
}
