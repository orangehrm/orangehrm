<?php
include_once 'SchemaIncrementTask.php';

/**
 *2.6.7 -> 2.6.8 
 */
class SchemaIncrementTask42 extends SchemaIncrementTask {
    
    public $userInputs;
    
    protected static $mimeTypes = array(
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt' => 'application/msword',
        'pdf' => 'application/pdf',
        'rtf' => 'application/rtf',
        'txt' => 'text/plain'
    );
    
    public function execute() {
        $this->incrementNumber = 42;
        parent::execute();
        
        $result = array();
        
        foreach($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        
        $res1 = $this->updateCandidateHistory();
        $res2 = $this->updateCandidateAttachment();
        
        $result = array_merge($result, $res1, $res2);
        
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
        $sql[10] = "DELETE FROM ohrm_workflow_state_machine WHERE workflow = '2'";
        
        $recruitmentStates = array(array('2','APPLICATION INITIATED','ADMIN','2','SHORTLISTED'),
                                   array('2','APPLICATION INITIATED','ADMIN','3','REJECTED'),
                                   array('2','SHORTLISTED','ADMIN','4','INTERVIEW SCHEDULED'),
                                   array('2','SHORTLISTED','ADMIN','3','REJECTED'),
                                   array('2','INTERVIEW SCHEDULED','ADMIN','3','REJECTED'),
                                   array('2','INTERVIEW SCHEDULED','ADMIN','5','INTERVIEW PASSED'),
                                   array('2','INTERVIEW SCHEDULED','ADMIN','6','INTERVIEW FAILED'),
                                   array('2','INTERVIEW PASSED','ADMIN','4','INTERVIEW SCHEDULED'),
                                   array('2','INTERVIEW PASSED','ADMIN','7','JOB OFFERED'),
                                   array('2','INTERVIEW PASSED','ADMIN','3','REJECTED'),
                                   array('2','INTERVIEW FAILED','ADMIN','3','REJECTED'),
                                   array('2','JOB OFFERED','ADMIN','8','OFFER DECLINED'),
                                   array('2','JOB OFFERED','ADMIN','3','REJECTED'),
                                   array('2','JOB OFFERED','ADMIN','9','HIRED'),
                                   array('2','OFFER DECLINED','ADMIN','3','REJECTED'),
                                   array('2','INITIAL','HIRING MANAGER','1','APPLICATION INITIATED'),
                                   array('2','APPLICATION INITIATED','HIRING MANAGER','2','SHORTLISTED'),
                                   array('2','APPLICATION INITIATED','HIRING MANAGER','3','REJECTED'),
                                   array('2','SHORTLISTED','HIRING MANAGER','4','INTERVIEW SCHEDULED'),
                                   array('2','SHORTLISTED','HIRING MANAGER','3','REJECTED'),
                                   array('2','INTERVIEW SCHEDULED','HIRING MANAGER','3','REJECTED'),
                                   array('2','INTERVIEW SCHEDULED','HIRING MANAGER','5','INTERVIEW PASSED'),
                                   array('2','INTERVIEW SCHEDULED','HIRING MANAGER','6','INTERVIEW FAILED'),
                                   array('2','INTERVIEW PASSED','HIRING MANAGER','4','INTERVIEW SCHEDULED'),
                                   array('2','INTERVIEW PASSED','HIRING MANAGER','7','JOB OFFERED'),
                                   array('2','INTERVIEW PASSED','HIRING MANAGER','3','REJECTED'),
                                   array('2','INTERVIEW FAILED','HIRING MANAGER','3','REJECTED'),
                                   array('2','JOB OFFERED','HIRING MANAGER','8','OFFER DECLINED'),
                                   array('2','JOB OFFERED','HIRING MANAGER','3','REJECTED'),
                                   array('2','JOB OFFERED','HIRING MANAGER','9','HIRED'),
                                   array('2','OFFER DECLINED','HIRING MANAGER','3','REJECTED'),
                                   array('2','INTERVIEW SCHEDULED','INTERVIEWER','5','INTERVIEW PASSED'),
                                   array('2','INTERVIEW SCHEDULED','INTERVIEWER','6','INTERVIEW FAILED'));
        
        $workflowInsertSql = "INSERT INTO ohrm_workflow_state_machine VALUES ";
        $startId = $this->getNextWorkflowId();
        
        for ($i = 0; $i < count($recruitmentStates); $i++) {
            if ($i > 0) {
                $workflowInsertSql .= ', ';
            }
            $id = $i + $startId;
            $state = $recruitmentStates[$i];
            $workflowInsertSql .= "('{$id}','{$state[0]}','{$state[1]}','{$state[2]}','{$state[3]}','{$state[4]}')";
        }
        $sql[11] = $workflowInsertSql;
        
        
        // Upgrade recruitment data (based on osUpgrader-2.6.6-to-2.6.10.sql
        $sql[12] = "INSERT INTO `ohrm_job_vacancy` (ohrm_job_vacancy.`id`, ohrm_job_vacancy.`job_title_code`, ohrm_job_vacancy.`hiring_manager_id`, ohrm_job_vacancy.`name`, ohrm_job_vacancy.`description`, ohrm_job_vacancy.`status`, ohrm_job_vacancy.`defined_time`, ohrm_job_vacancy.`updated_time`, ohrm_job_vacancy.`published_in_feed`)
            SELECT hs_hr_job_vacancy.`vacancy_id`, hs_hr_job_vacancy.`jobtit_code`, hs_hr_job_vacancy.`manager_id`, hs_hr_job_title.jobtit_name, hs_hr_job_vacancy.`description`, IF( hs_hr_job_vacancy.`active` = 1, 1, 2 ), CURDATE(), CURDATE(), hs_hr_job_vacancy.`active`
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
        $sql[16] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'APPLICATION INITIATED' WHERE status = '0'";
        $sql[17] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'INTERVIEW SCHEDULED' WHERE status = '1' OR status = '2'";
        $sql[18] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'JOB OFFERED' WHERE status = '3' OR status = '5'";
        $sql[19] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'OFFER DECLINED' WHERE status = '4'";
        $sql[20] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'HIRED' WHERE status = '6'";
        $sql[21] = "UPDATE `ohrm_job_candidate_vacancy` SET status = 'REJECTED' WHERE status = '7'";
        $sql[22] = "ALTER TABLE `ohrm_job_candidate_vacancy` CHANGE `id` `id` INT( 13 ) NULL";

        $sql[23] = "INSERT INTO `ohrm_job_candidate_attachment` (ohrm_job_candidate_attachment.`candidate_id`, ohrm_job_candidate_attachment.`file_name`, ohrm_job_candidate_attachment.`file_size`, ohrm_job_candidate_attachment.`file_content`)
            SELECT hs_hr_job_application.`application_id`, COALESCE(hs_hr_job_application.`resume_name`, 'resume-file'), OCTET_LENGTH(`resume_data`), hs_hr_job_application.`resume_data`
            FROM `hs_hr_job_application`
            WHERE hs_hr_job_application.`resume_name` IS NOT NULL";

        $sql[24] = "INSERT INTO `ohrm_job_interview` (ohrm_job_interview.`candidate_vacancy_id`, ohrm_job_interview.`candidate_id`, ohrm_job_interview.`interview_name`, ohrm_job_interview.`interview_date`, ohrm_job_interview.`interview_time`, ohrm_job_interview.`note`)
            SELECT ohrm_job_candidate_vacancy.`id`, ohrm_job_candidate_vacancy.`candidate_id`, hs_hr_job_application_events.`event_type`, DATE(hs_hr_job_application_events.`event_time`), TIME(hs_hr_job_application_events.`event_time`), hs_hr_job_application_events.`notes`
            FROM `hs_hr_job_application_events`
            LEFT JOIN ohrm_job_candidate_vacancy
            ON hs_hr_job_application_events.`application_id` = ohrm_job_candidate_vacancy.candidate_id
            WHERE (hs_hr_job_application_events.`event_type` = 1 OR hs_hr_job_application_events.`event_type` = 2) AND ohrm_job_candidate_vacancy.`candidate_id` IS NOT NULL";

        $sql[25] = "INSERT INTO `ohrm_job_interview_interviewer` (ohrm_job_interview_interviewer.`interview_id`, ohrm_job_interview_interviewer.`interviewer_id`)
            SELECT DISTINCT(ohrm_job_interview.`id`), hs_hr_job_application_events.`owner`
            FROM `ohrm_job_interview` 
            LEFT JOIN hs_hr_job_application_events 
            ON ohrm_job_interview.`candidate_id` = hs_hr_job_application_events.application_id AND  ohrm_job_interview.`interview_name` = hs_hr_job_application_events.event_type";
        
        $sql[26] = "INSERT INTO ohrm_job_candidate_history (candidate_id, vacancy_id, action, performed_by, performed_date, note, interviewers, candidate_vacancy_name)
            SELECT app.`application_id`, app.`vacancy_id`, 15, NULL, app.applied_datetime, NULL, NULL, (SELECT `name` FROM ohrm_job_vacancy WHERE app.vacancy_id = ohrm_job_vacancy.`id`)
            FROM hs_hr_job_application app";
        
        $sql[28] = "INSERT INTO `ohrm_job_candidate_history` (ohrm_job_candidate_history.`candidate_id`, ohrm_job_candidate_history.`vacancy_id`, ohrm_job_candidate_history.`action`, ohrm_job_candidate_history.`performed_by`, ohrm_job_candidate_history.`performed_date`, ohrm_job_candidate_history.`note`, ohrm_job_candidate_history.`interviewers`, ohrm_job_candidate_history.`candidate_vacancy_name`)
            SELECT hs_hr_job_application_events.`application_id`, hs_hr_job_application.`vacancy_id`, hs_hr_job_application_events.`event_type`, (SELECT emp_number FROM hs_hr_users WHERE id=hs_hr_job_application_events.created_by), hs_hr_job_application_events.`created_time`, hs_hr_job_application_events.`notes`, hs_hr_job_application_events.`owner`,  (SELECT `name` FROM ohrm_job_vacancy WHERE hs_hr_job_application.vacancy_id = ohrm_job_vacancy.`id`)
            FROM `hs_hr_job_application_events` 
            LEFT JOIN hs_hr_job_application 
            ON hs_hr_job_application_events.`application_id` = hs_hr_job_application.application_id";              
        
        // $sql[29] = "UPDATE `ohrm_job_candidate_history` SET action = 'REJ' WHERE action = 0";
        // UPDATE `ohrm_job_candidate_history` SET action = '4' WHERE action = 1 OR action = 2; -- moved to php
        $sql[30] = "UPDATE `ohrm_job_candidate_history` SET action = 7 WHERE action = 3 OR action = 5";
        $sql[31] = "UPDATE `ohrm_job_candidate_history` SET action = 8 WHERE action = 4";
        $sql[32] = "UPDATE `ohrm_job_candidate_history` SET action = 9 WHERE action = 6";
        $sql[33] = "UPDATE `ohrm_job_candidate_history` SET action = 3 WHERE action = 0";

        
        // Update hs_hr_unique_id
        $sql[34] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate`
            WHERE 1) WHERE table_name='ohrm_job_candidate' AND field_name='id'";

        $sql[35] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_vacancy`
            WHERE 1) WHERE table_name='ohrm_job_candidate_vacancy' AND field_name='id'";

        $sql[36] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_history`
            WHERE 1) WHERE table_name='ohrm_job_candidate_history' AND field_name='id'";

        $sql[37] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_interview`
            WHERE 1) WHERE table_name='ohrm_job_interview' AND field_name='id'";

        $sql[38] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_vacancy_attachment`
            WHERE 1) WHERE table_name='ohrm_job_vacancy_attachment' AND field_name='id'";

        $sql[39] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_vacancy`
            WHERE 1) WHERE table_name='ohrm_job_vacancy' AND field_name='id'";

        $sql[40] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_job_candidate_attachment`
            WHERE 1) WHERE table_name='ohrm_job_candidate_attachment' AND field_name='id'";

        $sql[41] = "UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
            FROM `ohrm_workflow_state_machine`
            WHERE 1) WHERE table_name='ohrm_workflow_state_machine' AND field_name='id'";

        
        // Delete old recruitment tables
        $sql[42] = "DROP TABLE hs_hr_job_application_events";        
        $sql[43] = "DROP TABLE hs_hr_job_application";
        $sql[44] = "DROP TABLE hs_hr_job_vacancy";
        
        $this->sql = $sql;
    }
    
    public function getNotes() {
        
        $notes = array();
        $notes[] = "In Recruitment module, applicant qualifications, experience and address details will be removed.";
        $notes[] = "Recruitment mail notifications have been removed.";
        
        return $notes;
        
    }
    
    private function updateCandidateHistory() {

        $results = array();
        
        $q = "SELECT * FROM `ohrm_job_interview` WHERE interview_name='1'";
        
        $res = $this->upgradeUtility->executeSql($q);

        while ($row = $this->upgradeUtility->fetchArray($res)) {
            $id = $row['id'];
            $candidateId = $row['candidate_id'];

            $query = "UPDATE ohrm_job_candidate_history SET
                    interview_id={$id}, interviewers=(SELECT interviewer_id FROM ohrm_job_interview_interviewer WHERE interview_id={$id})
                   WHERE candidate_id = {$candidateId} AND action = 1";

            $results[] = $this->upgradeUtility->executeSql($query);
        }

        $q = "SELECT * FROM `ohrm_job_interview` WHERE interview_name='2'";

       $res = $this->upgradeUtility->executeSql($q);

       while ($row = $this->upgradeUtility->fetchArray($res)) {
            $id = $row['id'];
            $candidateId = $row['candidate_id'];

            $query = "UPDATE ohrm_job_candidate_history SET
                    interview_id={$id}, interviewers=(SELECT interviewer_id FROM ohrm_job_interview_interviewer WHERE interview_id={$id})
                   WHERE candidate_id = {$candidateId} AND action = 2";

            $results[] = $this->upgradeUtility->executeSql($query);
        }

        $query = "UPDATE ohrm_job_candidate_history SET `action`=4 WHERE action=1 OR action=2";
        $results[] = $this->upgradeUtility->executeSql($query);

        $query = "UPDATE ohrm_job_candidate_history SET `interviewers`=concat(`interviewers`,'_')";
        $results[] = $this->upgradeUtility->executeSql($query);

        $query = "UPDATE ohrm_job_interview SET `interview_name`='First Interview' WHERE interview_name='1'";
        $results[] = $this->upgradeUtility->executeSql($query);
        
        $query = "UPDATE ohrm_job_interview SET `interview_name`='Second Interview' WHERE interview_name='2'";
        $results[] = $this->upgradeUtility->executeSql($query);

        return $results;
    }

    private function getContentTypeFromExtension($fileName) {
        
        $type = null;
        
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!empty($extension)) {
            if (isset(self::$mimeTypes[$extension])) {
                $type = self::$mimeTypes[$extension];
            }
        }
        
        return $type;
    }
    
    private function getContentTypeFromFinfo($path) {
        if (!function_exists('finfo_open') || !is_readable($path)) {
            return null;
        }

        if (!$finfo = new finfo(FILEINFO_MIME)) {
            return null;
        }

        $type = $finfo->file($path);

        // remove charset (added as of PHP 5.3)
        if (false !== $pos = strpos($type, ';')) {
            $type = substr($type, 0, $pos);
        }

        return $type;
    }
    
    
    
    private function updateCandidateAttachment() {

        $results = array();
        
        $haveFinfo = function_exists('finfo_open');
        $haveMimeContentType = function_exists('mime_content_type');
        
        $extractFiles = $haveFinfo || $haveMimeContentType;        
        
        $q = "SELECT id, file_name FROM  `ohrm_job_candidate_attachment`";
        
        if ($extractFiles) {
            $q = "SELECT * FROM `ohrm_job_candidate_attachment`";
        }
        
        $res = $this->upgradeUtility->executeSql($q);

        if ($extractFiles) {
            $path = tempnam(sys_get_temp_dir(), 'Oup');
            unlink($path);        
            mkdir($path, 0755);
        }
        
        while ($row = $this->upgradeUtility->fetchArray($res)) {
            $id = $row['id'];
            
            if ($extractFiles) {
                $data = $row['file_content'];
                file_put_contents($path . $row['file_name'], $data);    
                
                if ($haveFinfo) {
                    $mime = $this->getContentTypeFromFinfo($path . $row['file_name']);
                } else {
                    $mime = mime_content_type($path . $row['file_name']);
                }
                
                unlink($path . $row['file_name']);
            } else {
                $mime = $this->getContentTypeFromExtension($row['file_name']);
            }

            if (!empty($mime)) {
                $query1 = "UPDATE ohrm_job_candidate_attachment SET
                    file_type='{$mime}'
                    WHERE id = {$id}";

                $results[] = $this->upgradeUtility->executeSql($query1);
            }
        }

        if ($extractFiles) {        
            if (is_dir($path)) {
                $objects = scandir($path);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir") {
                            rrmdir($path . "/" . $object); 
                        } else {
                            unlink($dir . "/" . $object);
                        }
                    }
                }

                reset($objects);
                rmdir($path);
            }
        }
    }    
    
    protected function getNextWorkflowId() {
        $result = $this->upgradeUtility->executeSql('SELECT COALESCE(MAX(id),0) + 1 FROM `ohrm_workflow_state_machine`');
        $resultArray = $this->upgradeUtility->fetchArray($result);
        $nextId = $resultArray[0];
        return $nextId;
    }
}