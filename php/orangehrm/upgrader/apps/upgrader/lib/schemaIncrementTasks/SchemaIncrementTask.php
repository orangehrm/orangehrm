<?php

abstract class SchemaIncrementTask {
    
    protected $userInputs;
    protected $upgradeUtility;
    protected $sql;
    protected $result;
    protected $transactionComplete = true;
    
    public function execute() {
        $this->upgradeUtility = new UpgradeUtility();
        $this->upgradeUtility->connectDatabase();
        $this->loadSql();
    }
    
    abstract public function loadSql();
    abstract public function getUserInputWidgets();
    abstract public function setUserInputs();
    
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
            }
        }
    }
}