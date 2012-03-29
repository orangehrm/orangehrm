<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask46 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 46;
        parent::execute();
        
        for($i = 0; $i <= 2; $i++) {
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
    
        $sql[0] = "UPDATE hs_pr_salary_currency_detail 
                        SET currency_id = 'ZAR' WHERE currency_id = 'SAR'";
        
        $sql[1] = "UPDATE hs_hr_emp_basicsalary 
                        SET currency_id = 'ZAR' WHERE currency_id = 'SAR'";
        
        $sql[2] = "UPDATE hs_hr_currency_type 
                        SET code = '173', currency_name = 'Saudi Arabia Riyal' WHERE currency_id = 'SAR'";
        
        $this->sql = $sql;
    }
}