<?php

abstract class SchemaIncrementTask {
    
    protected $userInputs;
    protected $upgradeUtility;
    protected $sql;
    protected $transactionComplete = true;
    protected $incrementNumber;
    protected $dbInfo;
    
    public function __construct($dbInfo = null) {
        $this->dbInfo = $dbInfo;
    }
    
    public function initDB(){
        $this->upgradeUtility = new UpgradeUtility();
        $this->upgradeUtility->getDbConnection($this->dbInfo['host'],$this->dbInfo['username'],$this->dbInfo['password'],$this->dbInfo['database'],$this->dbInfo['port']);
    }
    
    public function execute() {
        $this->initDB();
        $this->createOhrmUpgradeStatus($this->incrementNumber);
        $this->loadSql();
    }
    
    abstract public function loadSql();
    abstract public function getUserInputWidgets();
    abstract public function setUserInputs();
    abstract public function getNotes();
    
    public function getProgress(){
        if($this->transactionComplete) {
            return 100;
        } else {
            return 0;
        }
    }
    
    public function checkTransactionComplete($results) {
        foreach($results as $result) {
            if(!$result) {
                $this->transactionComplete = false;
                break;
            }
        }
    }
    
    public function createOhrmUpgradeStatus($id) {
        $sql= "CREATE TABLE IF NOT EXISTS `ohrm_upgrade_status` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `status` varchar(250) NOT NULL,
                  PRIMARY KEY (`id`)
                ) engine=innodb default charset=utf8;";
        
        $result = $this->upgradeUtility->executeSql($sql);
        
        $valueString = "'".$id."' , 'started'";
        $sql= "INSERT INTO ohrm_upgrade_status
                            (id, status) 
                            VALUES($valueString);";
        
        $result = $this->upgradeUtility->executeSql($sql);
        $this->upgradeUtility->commitDatabaseChanges();
    }
    
    public function updateOhrmUpgradeInfo($transactionComplete, $id) {
        if ($transactionComplete) {
            $sql = "UPDATE ohrm_upgrade_status 
                        SET status = 'completed' WHERE id = '$id'";
           
            $result = $this->upgradeUtility->executeSql($sql);
            $this->upgradeUtility->commitDatabaseChanges();
        }
    }
}