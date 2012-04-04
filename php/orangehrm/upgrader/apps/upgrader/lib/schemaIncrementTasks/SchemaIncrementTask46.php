<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask46 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 46;
        parent::execute();
        
        $result[] = $this->updateSalaryCurrencyDetail();
        
        for($i = 0; $i <= 1; $i++) {
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
        
        $sql[0] = "UPDATE hs_hr_emp_basicsalary 
                        SET currency_id = 'ZAR' WHERE currency_id = 'SAR'";
        
        $sql[1] = "UPDATE hs_hr_currency_type 
                        SET code = '173', currency_name = 'Saudi Arabia Riyal' WHERE currency_id = 'SAR'";
        
        $this->sql = $sql;
    }
    
    private function updateSalaryCurrencyDetail() {
        $salaryCurrancyDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_currency_detail");
        $success = true;
        if($salaryCurrancyDetails) {
            while($row = $this->upgradeUtility->fetchArray($salaryCurrancyDetails))
            {
                $salGrdCode = $row['sal_grd_code'];
                $currencyId = $row['currency_id'];
                if ($currencyId == 'SAR') {
                    $duplicateSalaryCurrencyDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_currency_detail WHERE currency_id = 'ZAR' AND sal_grd_code = '$salGrdCode'");
                    if ($this->upgradeUtility->fetchArray($duplicateSalaryCurrencyDetails)) {
                        $sql = "DELETE FROM hs_pr_salary_currency_detail 
                            WHERE currency_id = 'SAR' AND sal_grd_code = '$salGrdCode'";
                        
                        $result = $this->upgradeUtility->executeSql($sql);
                        if(!$result) {
                            $success = false;
                        }
                    } else {
                        $sql = "UPDATE hs_pr_salary_currency_detail 
                            SET currency_id = 'ZAR' WHERE currency_id = 'SAR' AND sal_grd_code = '$salGrdCode'";
                        
                        $result = $this->upgradeUtility->executeSql($sql);
                        if(!$result) {
                            $success = false;
                        }
                    }
                }
            }
        }
        return $success;
    }
}