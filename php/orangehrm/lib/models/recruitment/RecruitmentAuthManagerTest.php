<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

// Call RecruitmentAuthManagerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "RecruitmentAuthManagerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/recruitment/JobApplication.php";
require_once ROOT_PATH."/lib/models/recruitment/JobVacancy.php";
require_once ROOT_PATH."/lib/models/recruitment/RecruitmentAuthManager.php";
require_once ROOT_PATH."/lib/common/UniqueIDGenerator.php";
require_once ROOT_PATH."/lib/common/authorize.php";

/**
 * Test class for RecruitmentAuthManager.
 */
class RecruitmentAuthManagerTest extends PHPUnit_Framework_TestCase {

    private $jobApplications;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("RecruitmentAuthManagerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $conf = new Conf();
        $this->connection = mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);
        mysql_select_db($conf->dbname);
        $this->_deleteTables();

        // Insert job titles
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB001', 'Manager', 'Manager job title', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB002', 'Driver', 'Driver job title', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB003', 'Typist', 'Typist job title', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB004', 'Programmer', 'Software Programmer', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB005', 'Director', 'Company Director', 'no comments', null)");

        // Insert employees (managers)
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(11, '0011', 'Rajasinghe', 'Saman', 'Marlon', 'JOB001')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(12, '0022', 'Jayasinghe', 'Aruna', 'Shantha', 'JOB001')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(13, '0042', 'Jayaweera', 'Nimal', 'T', 'JOB001')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(14, '0044', 'Karunarathne', 'Jaya', 'S', 'JOB001')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(15, '0054', 'Ranasinghe', 'Kamal', 'Z', 'JOB001')");

        // Non manager
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(16, '0055', 'Karunanayake', 'Kamal', 'S', 'JOB003')");

        // Directors
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(17, '0056', 'Ramanayake', 'Dasun', 'K', 'JOB005')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
                    "VALUES(18, '0057', 'Suraweera', 'Nuwan', 'E', 'JOB005')");

        // Insert to hs_hr_users table
        $this->_runQuery("INSERT INTO `hs_hr_users`(id, user_name, emp_number, is_admin) VALUES ('USR111','demo', 15, 'Yes')");
        $this->_runQuery("INSERT INTO `hs_hr_users`(id, user_name, emp_number, is_admin) VALUES ('USR112','demo2', 11, 'Yes')");
        $this->_runQuery("INSERT INTO `hs_hr_users`(id, user_name, emp_number, is_admin) VALUES ('USR113','demo3', 13, 'Yes')");
        $this->_runQuery("INSERT INTO `hs_hr_users`(id, user_name, emp_number, is_admin) VALUES ('USR114','demo4', 14, 'Yes')");
        $this->_runQuery("INSERT INTO `hs_hr_users`(id, user_name, emp_number, is_admin) VALUES ('USR117','demo5', 17, 'No')");

        // Insert Job Vacancies
        $this->_runQuery("INSERT INTO hs_hr_job_vacancy(vacancy_id, jobtit_code, manager_id, active, description) " .
                         "VALUES(1, 'JOB001', 11, " . JobVacancy::STATUS_ACTIVE . ", 'Job vacancy 1')");
        $this->_runQuery("INSERT INTO hs_hr_job_vacancy(vacancy_id, jobtit_code, manager_id, active, description) " .
                         "VALUES(2, 'JOB002', 11, " . JobVacancy::STATUS_INACTIVE . ", 'Job vacancy 2')");
        $this->_runQuery("INSERT INTO hs_hr_job_vacancy(vacancy_id, jobtit_code, manager_id, active, description) " .
                         "VALUES(3, 'JOB003', 12, " . JobVacancy::STATUS_INACTIVE . ", 'Job vacancy 3')");

        // Insert Job Applications
        $application = $this->_getJobApplication(1, 1, 'Janaka', 'T', 'Kulathunga', '111 Main Street', 'Apt X2',
                'Colombo', 'Western', '2222', 'Sri Lanka', '01121111121', '077282828282', 'janaka@example.com',
                'aaa bbb', JobApplication::STATUS_SUBMITTED);
        $application->setHiringManagerName('Saman Rajasinghe');
        $application->setJobTitleName('Manager');
        $this->jobApplications[1] = $application;

        $application = $this->_getJobApplication(2, 2, 'Kamal', 'S', 'Manawarathne', '222 Sea Street', 'Suite B2',
                'Kandy', 'Central', '111111', 'England', '33211121', '079982828282', 'kamal@etst.com',
                'asdfasdf', JobApplication::STATUS_SUBMITTED);
        $application->setHiringManagerName('Saman Rajasinghe');
        $application->setJobTitleName('Driver');
        $this->jobApplications[2] = $application;

        $application = $this->_getJobApplication(3, 3, 'Ruwan', 'S', 'Nawarathne', '393 Hill Street', '#2',
                'Nuwaraeliya', 'Central', '2333', 'Sri Lanka', '05121111121', '072282828282', 'rywab@sfmple.com',
                'aaa sdf bbb', JobApplication::STATUS_SUBMITTED);
        $application->setHiringManagerName('Aruna Jayasinghe');
        $application->setJobTitleName('Typist');
        $this->jobApplications[3] = $application;

        $application = $this->_getJobApplication(4, 3, 'Ruwan', 'S', 'Nawarathne', '393 Hill Street', '#2',
                'Nuwaraeliya', 'Central', '2333', 'Sri Lanka', '05121111121', '072282828282', 'rywab@sfmple.com',
                'aaa sdf bbb', JobApplication::STATUS_SUBMITTED);
        $application->setHiringManagerName('Aruna Jayasinghe');
        $application->setJobTitleName('Typist');
        $this->jobApplications[4] = $application;

        $application = $this->_getJobApplication(5, 2, 'Ruwan', 'S', 'Nawarathne', '393 Hill Street', '#2',
                'Nuwaraeliya', 'Central', '2333', 'Sri Lanka', '05121111121', '072282828282', 'rywab@sfmple.com',
                'aaa sdf bbb', JobApplication::STATUS_PENDING_APPROVAL);
        $application->setHiringManagerName('Aruna Jayasinghe');
        $application->setJobTitleName('Typist');
        $this->jobApplications[5] = $application;

        $this->_createJobApplications($this->jobApplications);

        // Create job application events
        $createdTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT, strtotime("-1 hours"));
        $eventTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT, strtotime("+5 days"));

        // Events for first job application
        $this->_createEvent(1, 1, $createdTime, 'USR111', 13, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_FINISHED,
            "1st Interview notes, here");

        $createdTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT, strtotime("-0.6 hours"));
        $eventTime = date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT, strtotime("+6 days"));
        $this->_createEvent(2, 1, $createdTime, 'USR111', 14, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "2nd Interview notes, here");

        // Events for second job application
        $this->_createEvent(3, 2, $createdTime, 'USR111', 14, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "3rd Interview notes, here");

        // Events for 3rd job application
        $this->_createEvent(4, 3, $createdTime, 'USR111', 12, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "3rd Interview notes, here");

        $this->_createEvent(5, 3, $createdTime, 'USR111', 12, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "3rd Interview notes, here");

        // Events for 4th job application
        $this->_createEvent(6, 4, $createdTime, 'USR111', 13, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "3rd Interview notes, here");

        $this->_createEvent(7, 4, $createdTime, 'USR111', 13, $eventTime,
            JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW, JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED,
            "3rd Interview notes, here");

        // Events for 5th job application
        $this->_createEvent(8, 5, $createdTime, 'USR111', 17, $eventTime,
            JobApplicationEvent::EVENT_SEEK_APPROVAL, null,
            "Seeking approval to hire");

        UniqueIDGenerator::getInstance()->resetIDs();
    }

    /**
     * Tears down the fixture, removed database entries created during test.
     *
     * @access protected
     */
    protected function tearDown() {
        $this->_deleteTables();
        UniqueIDGenerator::getInstance()->resetIDs();
    }

    private function _deleteTables() {
        $this->_runQuery("DELETE FROM `hs_hr_users` WHERE id IN ('USR111', 'USR112', 'USR113', 'USR114', 'USR117')");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_application`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_vacancy`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_title`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_employee`");
    }

    /**
     * Test case for testGetRoleForApplication().
     */
    public function testGetRoleForApplication() {

        $authManager = new RecruitmentAuthManager();

        // Admin user
        $auth = new authorize(null, authorize::YES);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_ADMIN, $role);

        // Hiring - manager
        $auth = new authorize('011', authorize::NO);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_HIRING_MANAGER, $role);

        // 1st Interviewer
        $auth = new authorize(13, authorize::NO);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_INTERVIEW1_MANAGER, $role);

        $app = JobApplication::getJobApplication(2);
        $auth = new authorize(14, authorize::NO);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_INTERVIEW1_MANAGER, $role);

        // 2nd Interviewer
        $auth = new authorize(14, authorize::NO);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_INTERVIEW2_MANAGER, $role);

        // Admin also Hiring - Manager : Should be ADMIN ROLE
        $auth = new authorize(11, authorize::YES);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_ADMIN, $role);

        // Admin also 1st Interviewer : Should be ADMIN ROLE
        $auth = new authorize(13, authorize::YES);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_ADMIN, $role);

        // Admin also 2nd Interviewer : Should be ADMIN ROLE
        $auth = new authorize(14, authorize::YES);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_ADMIN, $role);

        // Hiring-Manager also 1st and 2nd Interviewer: Should be Hiring Manager
        $auth = new authorize(12, authorize::NO);
        $app = JobApplication::getJobApplication(3);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_HIRING_MANAGER, $role);

        // 1st Interviewer also 2nd Interviewer: Should be 2nd Interviewer
        $auth = new authorize(13, authorize::NO);
        $app = JobApplication::getJobApplication(4);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_INTERVIEW2_MANAGER, $role);

        // Other manager
        $auth = new authorize(15, authorize::NO);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_OTHER_MANAGER, $role);

        // Other non-manager
        $auth = new authorize(16, authorize::NO);
        $app = JobApplication::getJobApplication(1);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_OTHER, $role);

        // Director
        $auth = new authorize(17, authorize::NO);
        $app = JobApplication::getJobApplication(5);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_DIRECTOR, $role);

        // Other Director
        $auth = new authorize(18, authorize::NO);
        $app = JobApplication::getJobApplication(5);
        $role = $authManager->getRoleForApplication($auth, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_OTHER_DIRECTOR, $role);

    }

    /**
     * test testGetAllowedActions().
     */
    public function testGetAllowedActions() {

        $authManager = new RecruitmentAuthManager();
        $app = JobApplication::getJobApplication(1);
        $app5 = JobApplication::getJobApplication(5);

        // Different users
        $admin = new authorize(null, authorize::YES);
        $hiring = new authorize('011', authorize::NO);
        $first = new authorize(13, authorize::NO);
        $second = new authorize(14, authorize::NO);
        $manager = new authorize(15, authorize::NO);
        $nonManager = new authorize(16, authorize::NO);
        $director = new authorize(17, authorize::NO);
        $otherDirector = new authorize(18, authorize::NO);

        /* SUBMITTED */
        $app->setStatus(JobApplication::STATUS_SUBMITTED);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        /* FIRST INTERVIEW SCHEDULED (scheduled) */
        $app->setStatus(JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);

        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT);
        $role = $authManager->getRoleForApplication($first, $app);
        $this->assertEquals(RecruitmentAuthManager::ROLE_INTERVIEW1_MANAGER, $role);

        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        /* FIRST INTERVIEW SCHEDULED (finished) */
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW);
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        /* SECOND INTERVIEW SCHEDULED (scheduled) */
        $app->setStatus(JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);

        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);
        $app->setEvents(array($event));

        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        /* SECOND INTERVIEW SCHEDULED (finished) */
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB);
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        // Job Offered
        $app->setStatus(JobApplication::STATUS_JOB_OFFERED);

        $expected = array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL);
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL);
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        // Offer Declined
        $app->setStatus(JobApplication::STATUS_OFFER_DECLINED);

        $expected = array();
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        // Pending approval
        $app5->setStatus(JobApplication::STATUS_PENDING_APPROVAL);

        $expected = array();
        $actions = $authManager->getAllowedActions($admin, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($hiring, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_APPROVE);
        $actions = $authManager->getAllowedActions($director, $app5);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app5);
        $this->assertEquals($expected, $actions);

        // Hired
        $app->setStatus(JobApplication::STATUS_HIRED);

        $expected = array();
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);

        // Rejected
        $app->setStatus(JobApplication::STATUS_REJECTED);

        $expected = array();
        $actions = $authManager->getAllowedActions($admin, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($hiring, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($first, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($second, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($manager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($nonManager, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($director, $app);
        $this->assertEquals($expected, $actions);

        $expected = array();
        $actions = $authManager->getAllowedActions($otherDirector, $app);
        $this->assertEquals($expected, $actions);
    }

    /**
     * test testIsActionAllowed().
     */
    public function testIsActionAllowed() {
        $authManager = new RecruitmentAuthManager();
        $app = JobApplication::getJobApplication(1);
        $app5 = JobApplication::getJobApplication(5);

        // Different users
        $admin = new authorize(null, authorize::YES);
        $hiring = new authorize('011', authorize::NO);
        $first = new authorize(13, authorize::NO);
        $second = new authorize(14, authorize::NO);
        $manager = new authorize(15, authorize::NO);
        $nonManager = new authorize(16, authorize::NO);
        $director = new authorize(17, authorize::NO);
        $otherDirector = new authorize(18, authorize::NO);

        /* SUBMITTED */
        $app->setStatus(JobApplication::STATUS_SUBMITTED);
        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW));
        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW));
        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        /* FIRST INTERVIEW SCHEDULED (scheduled) */
        $app->setStatus(JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);

        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertTrue($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        /* FIRST INTERVIEW SCHEDULED (finished) */
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW);

        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW));
        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertTrue($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        /* SECOND INTERVIEW SCHEDULED (scheduled) */
        $app->setStatus(JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);

        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);

        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertTrue($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        /* SECOND INTERVIEW SCHEDULED (finished) */
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB);

        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertTrue($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($second, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        // Job Offered
        $app->setStatus(JobApplication::STATUS_JOB_OFFERED);

        $expected = array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL);
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_MARK_OFFER_DECLINED));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_MARK_OFFER_DECLINED));
        $this->assertTrue($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_MARK_OFFER_DECLINED));
        $this->assertTrue($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_MARK_OFFER_DECLINED));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        // Offer Declined
        $app->setStatus(JobApplication::STATUS_OFFER_DECLINED);

        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));

        // Pending approval
        $app5->setStatus(JobApplication::STATUS_PENDING_APPROVAL);

        $this->assertFalse($authManager->isActionAllowed($otherDirector, $app5, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($otherDirector, $app5, JobApplication::ACTION_REJECT));
        $this->assertTrue($authManager->isActionAllowed($director, $app5, JobApplication::ACTION_APPROVE));
        $this->assertTrue($authManager->isActionAllowed($director, $app5, JobApplication::ACTION_REJECT));

        $this->assertFalse($authManager->isActionAllowed($admin, $app5, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app5, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app5, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertFalse($authManager->isActionAllowed($hiring, $app5, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app5, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app5, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($second, $app5, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($second, $app5, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($first, $app5, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app5, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app5, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app5, JobApplication::ACTION_SEEK_APPROVAL));

        // Hired
        $app->setStatus(JobApplication::STATUS_HIRED);

        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));

        // Rejected
        $app->setStatus(JobApplication::STATUS_REJECTED);

        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_APPROVE));
        $this->assertFalse($authManager->isActionAllowed($director, $app, JobApplication::ACTION_REJECT));

        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($admin, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));

        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($hiring, $app, JobApplication::ACTION_SEEK_APPROVAL));

        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($second, $app, JobApplication::ACTION_OFFER_JOB));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($first, $app, JobApplication::ACTION_REJECT));
        $this->assertFalse($authManager->isActionAllowed($manager, $app, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW));
        $this->assertFalse($authManager->isActionAllowed($nonManager, $app, JobApplication::ACTION_SEEK_APPROVAL));
    }

    /**
     * Test method isAllowedToEditEvent()
     */
    public function testIsAllowedToEditEvent() {
        $authManager = new RecruitmentAuthManager();
        $app = JobApplication::getJobApplication(1);
        $app5 = JobApplication::getJobApplication(5);

        // Different users
        $admin = new authorize(null, authorize::YES);
        $hiring = new authorize('011', authorize::NO);
        $first = new authorize(13, authorize::NO);
        $second = new authorize(14, authorize::NO);
        $manager = new authorize(15, authorize::NO);
        $nonManager = new authorize(16, authorize::NO);
        $director = new authorize(17, authorize::NO);
        $otherDirector = new authorize(18, authorize::NO);

        // Admin, Hiring Manager and interviewer(owner) allowed to edit event
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $this->assertTrue($authManager->isAllowedToEditEvent($admin, $event));
        $this->assertTrue($authManager->isAllowedToEditEvent($hiring, $event));
        $this->assertTrue($authManager->isAllowedToEditEvent($first, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($second, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($manager, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($nonManager, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($director, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($otherDirector, $event));

        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $this->assertTrue($authManager->isAllowedToEditEvent($admin, $event));
        $this->assertTrue($authManager->isAllowedToEditEvent($hiring, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($first, $event));
        $this->assertTrue($authManager->isAllowedToEditEvent($second, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($manager, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($nonManager, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($director, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($otherDirector, $event));

        $event = $app5->getEventOfType(JobApplicationEvent::EVENT_SEEK_APPROVAL);
        $this->assertFalse($authManager->isAllowedToEditEvent($director, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($otherDirector, $event));

        $this->assertTrue($authManager->isAllowedToEditEvent($admin, $event));
        $this->assertTrue($authManager->isAllowedToEditEvent($hiring, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($first, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($second, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($manager, $event));
        $this->assertFalse($authManager->isAllowedToEditEvent($nonManager, $event));

    }

    /**
     * Test method isAllowedToChangeEventStatus()
     */
    public function testIsAllowedToChangeEventStatus() {
        $authManager = new RecruitmentAuthManager();
        $app = JobApplication::getJobApplication(1);

        // Different users
        $admin = new authorize(null, authorize::YES);
        $hiring = new authorize('011', authorize::NO);
        $first = new authorize(13, authorize::NO);
        $second = new authorize(14, authorize::NO);
        $manager = new authorize(15, authorize::NO);
        $nonManager = new authorize(16, authorize::NO);

        // Admin, Hiring Manager and interviewer(owner) allowed to change status event when there are
        // no newer events.
        $app->setStatus(JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);
        $app->save();
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $this->assertTrue($authManager->isAllowedToChangeEventStatus($admin, $event));
        $this->assertTrue($authManager->isAllowedToChangeEventStatus($hiring, $event));
        $this->assertTrue($authManager->isAllowedToChangeEventStatus($first, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($second, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($manager, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($nonManager, $event));

        //Not allowed to change status when not current event.
        $app->setStatus(JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);
        $app->save();
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($admin, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($hiring, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($first, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($second, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($manager, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($nonManager, $event));

        //Not allowed to change status when not current event.
        $app->setStatus(JobApplication::STATUS_REJECTED);
        $app->save();
        $event = $app->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($admin, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($hiring, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($first, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($second, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($manager, $event));
        $this->assertFalse($authManager->isAllowedToChangeEventStatus($nonManager, $event));
    }

    /**
     * Create a JobApplication object with the passed parameters
     */
    private function _getJobApplication($id, $vacancyId, $firstName, $middleName, $lastName, $street1, $street2,
            $city, $province, $zip, $country, $mobile, $phone, $email, $qualifications, $status = JobApplication::STATUS_SUBMITTED) {
        $application = new JobApplication($id);
        $application->setVacancyId($vacancyId);
        $application->setFirstName($firstName);
        $application->setMiddleName($middleName);
        $application->setLastName($lastName);
        $application->setStreet1($street1);
        $application->setStreet2($street2);
        $application->setCity($city);
        $application->setProvince($province);
        $application->setZip($zip);
        $application->setCountry($country);
        $application->setMobile($mobile);
        $application->setPhone($phone);
        $application->setEmail($email);
        $application->setQualifications($qualifications);
        $application->setStatus($status);
        $application->setAppliedDateTime(date(LocaleUtil::STANDARD_TIMESTAMP_FORMAT));
        return $application;
    }

    /**
     * Saves the given JobApplication objects in the database
     *
     * @param array $applications Array of JobApplication objects to save.
     */
    private function _createJobApplications($applications) {
        foreach ($applications as $application) {

            $sql = sprintf("INSERT INTO hs_hr_job_application(application_id, vacancy_id, firstname, middlename, ".
                        "lastname, street1, street2, city, country_code, province, zip, " .
                        "phone, mobile, email, qualifications, status, applied_datetime) " .
                        "VALUES(%d, %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s')",
                        $application->getId(), $application->getVacancyId(), $application->getFirstName(),
                        $application->getMiddleName(), $application->getLastName(), $application->getStreet1(),
                        $application->getStreet2(), $application->getCity(), $application->getCountry(),
                        $application->getProvince(), $application->getZip(), $application->getPhone(),
                        $application->getMobile(), $application->getEmail(),
                        $application->getQualifications(), $application->getStatus(),
                        $application->getAppliedDateTime());
            $this->assertTrue(mysql_query($sql), mysql_error());
        }
        UniqueIDGenerator::getInstance()->initTable();
    }

    /**
     * Create job application event with the passed parameters
     *
     * @param int $id
     * @param int $applicationId
     * @param String $createdTime
     * @param String $createdBy
     * @param int $ownerId
     * @param String $eventTime
     * @param int $eventType
     * @param int $eventStatus
     * @param String $notes
     */
    private function _createEvent($id, $applicationId, $createdTime, $createdBy, $ownerId, $eventTime,
        $eventType, $eventStatus, $notes) {

        $sql = sprintf("INSERT INTO `hs_hr_job_application_events`(`id`,`application_id`,`created_time`," .
                        "`created_by`, `owner`, `event_time`, `event_type`, `status`, `notes`) " .
                        "VALUES (%d, %d, '%s', '%s', %d, '%s', %d, %d, '%s')",
                        $id, $applicationId, $createdTime, $createdBy, $ownerId, $eventTime,
                        $eventType, $eventStatus, $notes);
        $this->assertTrue(mysql_query($sql), mysql_error());
        UniqueIDGenerator::getInstance()->initTable();
    }

    /**
     * Run given sql query, checking the return value
     */
    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error());
    }
}

// Call RecruitmentAuthManagerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "RecruitmentAuthManagerTest::main") {
    RecruitmentAuthManagerTest::main();
}
?>
