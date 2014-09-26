<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask43 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 43;
        parent::execute();
        
        $result = array();
        
        foreach($this->sql as $sql) {
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
    
        $sql[0] = "ALTER TABLE ohrm_display_field
                   ADD COLUMN `default_value` varchar(255) null AFTER text_alignment_style";
        
        $sql[1] = "ALTER TABLE ohrm_composite_display_field
                   ADD COLUMN `default_value` varchar(255) null AFTER text_alignment_style";
        
        $sql[2] = "ALTER TABLE ohrm_summary_display_field
                   ADD COLUMN `default_value` varchar(255) null AFTER text_alignment_style";        
        
        $sql[3] = "INSERT INTO `ohrm_workflow_state_machine` 
            SELECT COALESCE(MAX(id),0)+1, '0','INITIAL','ADMIN','7','NOT SUBMITTED' FROM `ohrm_workflow_state_machine`";
            
        $sql[4] = "INSERT INTO `ohrm_workflow_state_machine` 
            SELECT COALESCE(MAX(id),0)+1,'0','INITIAL','ESS USER','7','NOT SUBMITTED' FROM `ohrm_workflow_state_machine`";
        
        $sql[5] = "INSERT INTO `ohrm_workflow_state_machine` 
            SELECT COALESCE(MAX(id),0)+1,'0','INITIAL','SUPERVISOR','7','NOT SUBMITTED' FROM `ohrm_workflow_state_machine`";
                            
        // Update workflow action values for attendance configuration
        $sql[7] = "UPDATE `ohrm_workflow_state_machine` SET action = 8 WHERE (workflow = 1) AND (
            (state = 'INITIAL' AND role = 'ESS USER' AND action = '2' and resulting_state = 'INITIAL') OR
            (state = 'PUNCHED IN' AND role = 'ESS USER' AND action = '3' and resulting_state = 'PUNCHED IN')
            )";
        
        $sql[8] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_workflow_state_machine`
            WHERE 1) WHERE table_name='ohrm_workflow_state_machine' AND field_name='id'";        
        
        $sql[9] = <<< CORE_SQL_UPDATE
        UPDATE ohrm_report_group SET 
           core_sql = 'SELECT selectCondition FROM hs_hr_employee LEFT JOIN (SELECT * FROM ohrm_attendance_record WHERE ( ( ohrm_attendance_record.punch_in_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) AND ( ohrm_attendance_record.punch_out_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) ) ) AS ohrm_attendance_record ON (hs_hr_employee.emp_number = ohrm_attendance_record.employee_id) WHERE hs_hr_employee.emp_number = #@employeeId@,@hs_hr_employee.emp_number AND (hs_hr_employee.emp_status != "EST000" OR hs_hr_employee.emp_status is null) @# AND (hs_hr_employee.job_title_code = #@"jobTitle")@,@hs_hr_employee.job_title_code OR hs_hr_employee.job_title_code is null)@# AND (hs_hr_employee.work_station IN (#@subUnit)@,@SELECT id FROM hs_hr_compstructtree) OR hs_hr_employee.work_station is null@#) AND (hs_hr_employee.emp_status = #@"employeeStatus")@,@hs_hr_employee.emp_status OR hs_hr_employee.emp_status is null)@#' WHERE report_group_id = 2
CORE_SQL_UPDATE;
        
        $sql[10] = "UPDATE ohrm_display_field 
            SET element_property = '<xml><labelGetter>activityname</labelGetter><placeholderGetters><id>activity_id</id><total>totalduration</total><projectId>projectId</projectId><from>fromDate</from><to>toDate</to><approved>onlyIncludeApprovedTimesheets</approved></placeholderGetters><urlPattern>../../displayProjectActivityDetailsReport?reportId=3#activityId={id}#total={total}#from={from}#to={to}#projectId={projectId}#onlyIncludeApprovedTimesheets={approved}</urlPattern></xml>' 
            WHERE display_field_id = 2";
        
        $sql[11] = "INSERT INTO `ohrm_selected_filter_field` VALUES (3, 7, 3, null, null, null)";
        
        $sql[12] = "UPDATE ohrm_composite_display_field set default_value = 'Deleted Employee'
                   WHERE composite_display_field_id = 1";
        
        $this->sql = $sql;
    }
    
    public function getNotes() {
        
    }
    
}