<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask51 extends SchemaIncrementTask {
    
    public $userInputs;
    
    /**
     * 1. Removes entries from ohrm_user_role with duplicate 'name' fields and adds a unique constraint 
     * to that column. 
     */
    public function execute() {
        $this->incrementNumber = 50;
        parent::execute();
        
        
        for ($i = 0; $i < count($this->sql); $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
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

        $sql = array();

        $sql[0] = "delete from ohrm_user_role where (name='Interviewer' and id = 7) or (name='Offerer' and id = 8)";
        $sql[1] = "alter table ohrm_user_role add unique index user_role_name (`name`)";

        $sql[2] = "ALTER TABLE `ohrm_user` DROP FOREIGN KEY `ohrm_user_ibfk_2`";
        $sql[3] = "ALTER TABLE `ohrm_user` 
                        add constraint `ohrm_user_ibfk_2` foreign key (`user_role_id`)
        references ohrm_user_role(`id`) on delete restrict";

        $this->sql = $sql;
    }
    
    
    
    
    public function getNotes() {
        return array();
    }
    
}