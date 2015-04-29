<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SchemaIncrementTask63
 *
 * @author emma
 */
class SchemaIncrementTask63 extends SchemaIncrementTask {

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {
        $sql = array();

        $sql[] = 'ALTER TABLE `ohrm_ws_consumer` ENGINE = InnoDB DEFAULT CHARSET = utf8;';
        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function execute() {
        $this->incrementNumber = 63;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }

        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }

}
