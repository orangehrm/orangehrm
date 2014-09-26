<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask52 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 52;
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
    
    public function getUserInputWidgets() {
        
    }
    
    public function setUserInputs() {
        
    }
    
    public function loadSql() {
    
        $sql[0] = "ALTER TABLE hs_hr_employee_leave_quota 
                      ADD KEY `per_emp_type_key` (`leave_period_id`,`employee_id`,`leave_type_id`)";
        
        $sql[1] = "ALTER TABLE hs_hr_leave ADD KEY `type_status` (`leave_request_id`,`leave_status`)";
        
        $sql[2] = "ALTER TABLE hs_hr_leave_requests 
                      ADD KEY `leave_period_id_2` (`leave_period_id`,`employee_id`,`leave_type_id`)";
        
        $sql[3] = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('include_supervisor_chain', 'No')";
        
        $sql[4] = "ALTER TABLE `ohrm_timesheet_item` MODIFY COLUMN `comment` text default null";
        
        $sql[5] = "ALTER TABLE `ohrm_attendance_record` 
                      ADD INDEX `emp_id_state` (`employee_id`, `state`),
                      ADD INDEX `emp_id_time` (`employee_id`, `punch_in_utc_time`, `punch_out_utc_time`)";
        
        $sql[6] = "ALTER TABLE `hs_hr_emp_skill` MODIFY COLUMN `years_of_exp` decimal(2,0) default null";
        
        $this->sql = $sql;
    }
    
    public function getNotes() {        
        return array();
    }
    
}