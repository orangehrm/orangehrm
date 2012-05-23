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
        
        $notes[] = "If you have enabled data encryption in your current version, you need to copy 'key.ohrm' file located at 'lib/confs/cryptokeys' of the root of current installation to corresponding location of new version.";
        $notes[] = "If you have already set email configuration details, you have to reset the details at Admin > Email Notifications > Configuration.";
        $notes[] = "You need to reset the leave period starting date in the Leave module even though you would see it as set. Simply click Edit button and save same value.";
        
        return $notes;
    }
    
}