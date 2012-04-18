<?php
include_once 'SchemaIncrementTask.php';

/**
 *2.6.7 -> 2.6.8 
 */
class SchemaIncrementTask42 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 42;
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
    
        // Drop old time module related tables. These tables were replaced with new tables
        // in 2.6.7, but the old tables were only deleted in 2.6.8.
        // Upgrading the data in these tables will be done in the schema 41 task.        
        $sql[0] = "DROP TABLE hs_hr_attendance";
        $sql[1] = "DROP TABLE hs_hr_time_event";
        $sql[2] = "DROP TABLE hs_hr_timesheet";
        $sql[3] = "DROP TABLE hs_hr_employee_timesheet_period";
        $sql[4] = "DROP TABLE hs_hr_timesheet_submission_period";
        
        $sql[5] = "ALTER TABLE ohrm_filter_field
                   MODIFY COLUMN `required` varchar(10) default null";
        
        $sql[6] = "ALTER TABLE ohrm_job_candidate_history
                   ADD COLUMN `interviewers` varchar(255) default null AFTER `note`";
        

        // NOTE: Although 2.6.7 had most of the recruitment related code and tables, the
        // symfony converted recruitment module was hidden in 2.6.7 and the old non-symfony
        // recruitment module was in use.        
        // Therefore, recruitment data has to be upgraded here (when moving from 2.6.7 -> 2.6.8)
        
        // update unique id table.
        $sql[7] = "DELETE FROM hs_hr_unique_id WHERE table_name IN (
            'hs_hr_timesheet', 'hs_hr_timesheet_submission_period',
            'hs_hr_time_event', 'hs_hr_job_vacancy',
            'hs_hr_job_application', 'hs_hr_job_application_events',
            'hs_hr_attendance')";
        
        $sql[8] = "UPDATE `hs_hr_unique_id` SET last_id = 77 
                   WHERE table_name = 'ohrm_workflow_state_machine' and field_name = 'id'";
        $sql[9] = "UPDATE ohrm_filter_field SET `required` = NULL where filter_field_id = 7";
        
        // Workflow changes        
        $sql[10] = "DELETE FROM ohrm_workflow_state_machine WHERE id >= 40 AND id <= 89";
        
        $sql[11] = "INSERT INTO ohrm_workflow_state_machine VALUES 
            ('40','2','SHORTLISTED','ADMIN','4','INTERVIEW SCHEDULED'),
            ('41','2','SHORTLISTED','ADMIN','3','REJECTED'),
            ('42','2','INTERVIEW SCHEDULED','ADMIN','3','REJECTED'),
            ('43','2','INTERVIEW SCHEDULED','ADMIN','5','INTERVIEW PASSED'),
            ('44','2','INTERVIEW SCHEDULED','ADMIN','6','INTERVIEW FAILED'),
            ('45','2','INTERVIEW PASSED','ADMIN','4','INTERVIEW SCHEDULED'),
            ('46','2','INTERVIEW PASSED','ADMIN','7','JOB OFFERED'),
            ('47','2','INTERVIEW PASSED','ADMIN','3','REJECTED'),
            ('48','2','INTERVIEW FAILED','ADMIN','3','REJECTED'),
            ('49','2','JOB OFFERED','ADMIN','8','OFFER DECLINED'),
            ('50','2','JOB OFFERED','ADMIN','3','REJECTED'),
            ('51','2','JOB OFFERED','ADMIN','9','HIRED'),
            ('52','2','OFFER DECLINED','ADMIN','3','REJECTED'),
            ('53','2','INITIAL','HIRING MANAGER','1','APPLICATION INITIATED'),
            ('54','2','APPLICATION INITIATED','HIRING MANAGER','2','SHORTLISTED'),
            ('55','2','APPLICATION INITIATED','HIRING MANAGER','3','REJECTED'),
            ('56','2','SHORTLISTED','HIRING MANAGER','4','INTERVIEW SCHEDULED'),
            ('57','2','SHORTLISTED','HIRING MANAGER','3','REJECTED'),
            ('58','2','INTERVIEW SCHEDULED','HIRING MANAGER','3','REJECTED'),
            ('59','2','INTERVIEW SCHEDULED','HIRING MANAGER','5','INTERVIEW PASSED'),
            ('60','2','INTERVIEW SCHEDULED','HIRING MANAGER','6','INTERVIEW FAILED'),
            ('61','2','INTERVIEW PASSED','HIRING MANAGER','4','INTERVIEW SCHEDULED'),
            ('62','2','INTERVIEW PASSED','HIRING MANAGER','7','JOB OFFERED'),
            ('63','2','INTERVIEW PASSED','HIRING MANAGER','3','REJECTED'),
            ('64','2','INTERVIEW FAILED','HIRING MANAGER','3','REJECTED'),
            ('65','2','JOB OFFERED','HIRING MANAGER','8','OFFER DECLINED'),
            ('66','2','JOB OFFERED','HIRING MANAGER','3','REJECTED'),
            ('67','2','JOB OFFERED','HIRING MANAGER','9','HIRED'),
            ('68','2','OFFER DECLINED','HIRING MANAGER','3','REJECTED'),
            ('69','2','INTERVIEW SCHEDULED','INTERVIEWER','5','INTERVIEW PASSED'),
            ('70','2','INTERVIEW SCHEDULED','INTERVIEWER','6','INTERVIEW FAILED'),
            ('71','1','INITIAL','ADMIN','5','PUNCHED IN'),
            ('72','1','PUNCHED IN','ADMIN','6','PUNCHED OUT'),
            ('73','1','PUNCHED IN','ADMIN','2','PUNCHED IN'),
            ('74','1','PUNCHED IN','ADMIN','7','N/A'),
            ('75','1','PUNCHED OUT','ADMIN','2','PUNCHED OUT'),
            ('76','1','PUNCHED OUT','ADMIN','3','PUNCHED OUT'),
            ('77','1','PUNCHED OUT','ADMIN','7','N/A')";
        
        // Upgrade recruitment data (based on osUpgrader-2.6.6-to-2.6.10.sql
        $sql[12] = "INSERT INTO `ohrm_job_vacancy` (ohrm_job_vacancy.`id`, ohrm_job_vacancy.`job_title_code`, ohrm_job_vacancy.`hiring_manager_id`, ohrm_job_vacancy.`name`, ohrm_job_vacancy.`description`, ohrm_job_vacancy.`status`, ohrm_job_vacancy.`defined_time`, ohrm_job_vacancy.`updated_time`)
            SELECT hs_hr_job_vacancy.`vacancy_id`, hs_hr_job_vacancy.`jobtit_code`, hs_hr_job_vacancy.`manager_id`, hs_hr_job_title.jobtit_name, hs_hr_job_vacancy.`description`, hs_hr_job_vacancy.`active`, CURDATE(), CURDATE()
            FROM `hs_hr_job_vacancy`
            LEFT JOIN hs_hr_job_title 
                ON hs_hr_job_vacancy.`jobtit_code` = hs_hr_job_title.jobtit_code";

        $sql[13] = "INSERT INTO `ohrm_job_candidate` (ohrm_job_candidate.`id`, ohrm_job_candidate.`first_name`, ohrm_job_candidate.`middle_name`, ohrm_job_candidate.`last_name`, ohrm_job_candidate.`email`, ohrm_job_candidate.`contact_number`, ohrm_job_candidate.`status`, ohrm_job_candidate.`mode_of_application`, ohrm_job_candidate.`date_of_application`)
            SELECT hs_hr_job_application.`application_id`, hs_hr_job_application.`firstname`, hs_hr_job_application.`middlename`, hs_hr_job_application.`lastname`, hs_hr_job_application.`email`, hs_hr_job_application.`mobile`, '1', '2', DATE(hs_hr_job_application.`applied_datetime`)
            FROM `hs_hr_job_application`";

        $sql[14] = "ALTER TABLE `ohrm_job_candidate_vacancy` CHANGE `id` `id` INT( 13 ) NULL DEFAULT NULL AUTO_INCREMENT";
        $sql[15] = "INSERT INTO `ohrm_job_candidate_vacancy` (ohrm_job_candidate_vacancy.`candidate_id`, ohrm_job_candidate_vacancy.`vacancy_id`, ohrm_job_candidate_vacancy.`applied_date`, ohrm_job_candidate_vacancy.`status`)
            SELECT hs_hr_job_application.`application_id`, hs_hr_job_application.`vacancy_id`, DATE(hs_hr_job_application.`applied_datetime`), hs_hr_job_application.`status`
            FROM `hs_hr_job_application`";
        $sql[16] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'APPLICATION INITIATED' WHERE status = 0";
        $sql[17] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'INTERVIEW SCHEDULED' WHERE status = 1 OR status = 2";
        $sql[18] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'JOB OFFERED' WHERE status = 3";
        $sql[19] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'OFFER DECLINED' WHERE status = 4";
        $sql[20] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'HIRED' WHERE status = 6";
        $sql[21] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'REJECTED' WHERE status = 7";
        $sql[22] = "ALTER TABLE `ohrm_job_candidate_vacancy` CHANGE `id` `id` INT( 13 ) NULL";

        $sql[23] = "INSERT INTO `ohrm_job_candidate_attachment` (ohrm_job_candidate_attachment.`candidate_id`, ohrm_job_candidate_attachment.`file_name`, ohrm_job_candidate_attachment.`file_size`, ohrm_job_candidate_attachment.`file_content`)
            SELECT hs_hr_job_application.`application_id`, COALESCE(hs_hr_job_application.`resume_name`, 'resume-file'), '0', hs_hr_job_application.`resume_data`
            FROM `hs_hr_job_application`";

        $sql[24] = "INSERT INTO `ohrm_job_interview` (ohrm_job_interview.`candidate_vacancy_id`, ohrm_job_interview.`candidate_id`, ohrm_job_interview.`interview_name`, ohrm_job_interview.`interview_date`, ohrm_job_interview.`interview_time`, ohrm_job_interview.`note`)
            SELECT ohrm_job_candidate_vacancy.`id`, hs_hr_job_application_events.`application_id`, hs_hr_job_application_events.`event_type`, DATE(hs_hr_job_application_events.`event_time`), TIME(hs_hr_job_application_events.`event_time`), hs_hr_job_application_events.`notes`
            FROM `hs_hr_job_application_events`
            LEFT JOIN ohrm_job_candidate_vacancy
            ON hs_hr_job_application_events.`application_id` = ohrm_job_candidate_vacancy.candidate_id
            WHERE event_type = 1 OR event_type = 2";

        $sql[25] = "INSERT INTO `ohrm_job_interview_interviewer` (ohrm_job_interview_interviewer.`interview_id`, ohrm_job_interview_interviewer.`interviewer_id`)
            SELECT DISTINCT(ohrm_job_interview.`id`), hs_hr_job_application_events.`owner`
            FROM `ohrm_job_interview` 
            LEFT JOIN hs_hr_job_application_events 
            ON ohrm_job_interview.`candidate_id` = hs_hr_job_application_events.application_id 
            WHERE hs_hr_job_application_events.event_type = 1 OR hs_hr_job_application_events.event_type = 2";

        $sql[26] = "INSERT INTO `ohrm_job_candidate_history` (ohrm_job_candidate_history.`candidate_id`, ohrm_job_candidate_history.`vacancy_id`, ohrm_job_candidate_history.`action`, ohrm_job_candidate_history.`performed_by`, ohrm_job_candidate_history.`performed_date`, ohrm_job_candidate_history.`note`, ohrm_job_candidate_history.`interviewers`)
            SELECT hs_hr_job_application_events.`application_id`, hs_hr_job_application.`vacancy_id`, hs_hr_job_application_events.`event_type`, hs_hr_job_application_events.`owner`, COALESCE(hs_hr_job_application_events.`event_time`, hs_hr_job_application.`applied_datetime`), hs_hr_job_application_events.`notes`, hs_hr_job_application_events.`owner` 
            FROM `hs_hr_job_application_events` 
            LEFT JOIN hs_hr_job_application 
            ON hs_hr_job_application_events.`application_id` = hs_hr_job_application.application_id 
            WHERE 1";
        
        $sql[27] = "UPDATE `ohrm_job_candidate_history` SET action = '3' WHERE action = 0";
        $sql[28] = "UPDATE `ohrm_job_candidate_history` SET action = '4' WHERE action = 1 OR action = 2";
        $sql[29] = "UPDATE `ohrm_job_candidate_history` SET action = '7' WHERE action = 3";
        $sql[30] = "UPDATE `ohrm_job_candidate_history` SET action = '8' WHERE action = 4";        
        
        // Update hs_hr_unique_id
        $sql[31] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate`
            WHERE 1) WHERE table_name='ohrm_job_candidate' AND field_name='id'";

        $sql[32] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_vacancy`
            WHERE 1) WHERE table_name='ohrm_job_candidate_vacancy' AND field_name='id'";

        $sql[33] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_history`
            WHERE 1) WHERE table_name='ohrm_job_candidate_history' AND field_name='id'";

        $sql[34] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_interview`
            WHERE 1) WHERE table_name='ohrm_job_interview' AND field_name='id'";

        $sql[35] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_vacancy_attachment`
            WHERE 1) WHERE table_name='ohrm_job_vacancy_attachment' AND field_name='id'";

        $sql[36] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_vacancy`
            WHERE 1) WHERE table_name='ohrm_job_vacancy' AND field_name='id'";

        $sql[37] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_attachment`
            WHERE 1) WHERE table_name='ohrm_job_candidate_attachment' AND field_name='id'";

        // Delete old recruitment tables
        $sql[38] = "DROP TABLE hs_hr_job_application_events";        
        $sql[39] = "DROP TABLE hs_hr_job_application";
        $sql[40] = "DROP TABLE hs_hr_job_vacancy";
        
        $this->sql = $sql;
    }

    public function getNotes() {
        
    }
}