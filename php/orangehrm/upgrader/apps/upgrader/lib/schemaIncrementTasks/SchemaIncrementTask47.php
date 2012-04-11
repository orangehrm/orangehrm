<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask47 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 47;
        parent::execute();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[0]);
        
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
    
    public function getUserInputWidgets() {
        
    }
    
    public function setUserInputs() {
        
    }
    
    public function loadSql() {
    
        $sql[0] = "UPDATE ohrm_filter_field 
                        SET filter_field_widget = 'ohrmReportWidgetEmployeeListAutoFill' WHERE filter_field_id = '4'";
        
        $this->sql = $sql;
    }
    
    public function getNotes() {
        
        return $notes;
        
    }    
    
}