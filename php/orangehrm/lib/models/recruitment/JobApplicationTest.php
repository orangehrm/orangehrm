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

// Call JobApplicationTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "JobApplicationTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/recruitment/JobApplication.php";
require_once ROOT_PATH."/lib/models/recruitment/JobVacancy.php";
require_once ROOT_PATH."/lib/common/UniqueIDGenerator.php";
require_once ROOT_PATH."/lib/common/LocaleUtil.php";

/**
 * Test class for JobApplication
 */
class JobApplicationTest extends PHPUnit_Framework_TestCase {

	private $jobApplications;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("JobApplicationTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, making sure table is empty and creating database
     * entries needed during test.
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

		// Insert employees (managers)
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
        			"VALUES(11, '0011', 'Rajasinghe', 'Saman', 'Marlon', 'JOB001')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code) " .
        			"VALUES(12, '0022', 'Jayasinghe', 'Aruna', 'Shantha', 'JOB001')");

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

		$this->_createJobApplications($this->jobApplications);
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
		$this->_runQuery("TRUNCATE TABLE `hs_hr_job_application`");
		$this->_runQuery("TRUNCATE TABLE `hs_hr_job_vacancy`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_title`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_employee`");
	}

	/**
	 * Test the save function
	 */
	public function testSave() {

		// new
		$before = $this->_getNumRows();
		$application = $this->_getJobApplication(null, 2, 'Janaka', 'T', 'Kulathunga', '111 Main Street', 'Apt X2',
				'Colombo', 'Western', '2222', 'Sri Lanka', '01121111121', '077282828282', 'janaka@example.com',
				'aaa bbb');

		$id = $application->save();
		$this->assertEquals(($before + 1), $this->_getNumRows());
		$this->_checkExistsInDb($application);

		// update
		$before = $this->_getNumRows();
		$application = $this->_getJobApplication($id, 3, 'Ruwan', 'K', 'Ranathunga', '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', 'ruwan@example.com',
				'sdfsadfasdf fdsfdsndsfb');

		$newId = $application->save();
		$this->assertEquals($id, $newId);
		$this->assertEquals($before, $this->_getNumRows());
		$this->_checkExistsInDb($application);

		// without job_vacancy id
		$application = $this->_getJobApplication(3, null, 'Ruwan', 'K', 'Ranathunga', '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', 'ruwan@example.com',
				'sdfsadfasdf fdsfdsndsfb');
		try {
			$application->save();
			$this->fail("Exception expected");
		} catch (JobApplicationException $e) {
			$this->assertEquals(JobApplicationException::MISSING_PARAMETERS, $e->getCode());
		}

		// Invalid job vacancy id
		$application = $this->_getJobApplication(5, 'sf', 'Ruwan', 'K', 'Ranathunga', '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', 'ruwan@example.com',
				'sdfsadfasdf fdsfdsndsfb');
		try {
			$application->save();
			$this->fail("Exception expected");
		} catch (JobApplicationException $e) {
			$this->assertEquals(JobApplicationException::INVALID_PARAMETER, $e->getCode());
		}

		// Invalid ID
		$application = $this->_getJobApplication('22k1', 2, 'Ruwan', 'K', 'Ranathunga', '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', 'ruwan@example.com',
				'sdfsadfasdf fdsfdsndsfb');
		try {
			$application->save();
			$this->fail("Exception expected");
		} catch (JobApplicationException $e) {
			$this->assertEquals(JobApplicationException::INVALID_PARAMETER, $e->getCode());
		}

		// without firstname, last name
		$application = $this->_getJobApplication(7, 2, null, 'K', null, '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', 'ruwan@example.com',
				'sdfsadfasdf fdsfdsndsfb');

		try {
			$application->save();
			$this->fail("Exception expected");
		} catch (JobApplicationException $e) {
			$this->assertEquals(JobApplicationException::MISSING_PARAMETERS, $e->getCode());
		}

		// without email
		$application = $this->_getJobApplication(7, 2, 'Kamal', 'K', 'Thilakarathne', '222 Main Street', 'Suite B1',
				'Kandy', 'Central', '1111', 'England', '0331111121', '066282828282', null,
				'sdfsadfasdf fdsfdsndsfb');
		try {
			$application->save();
			$this->fail("Exception expected");
		} catch (JobApplicationException $e) {
			$this->assertEquals(JobApplicationException::MISSING_PARAMETERS, $e->getCode());
		}

	}

    /**
     * Test for function getList()
     */
    public function testGetList() {

        // get list
        $list = JobApplication::getList();
        $this->_compareApplications($this->jobApplications, $list);

        // get list for a manager with related applications
        $list = JobApplication::getList(11);
        $expected = array(1=>$this->jobApplications[1], 2=>$this->jobApplications[2]);
        $this->_compareApplications($expected, $list);

        // get list for a manager without any applications
        $list = JobApplication::getList(12);
        $expected = array(3=>$this->jobApplications[3]);
        $this->_compareApplications($expected, $list);

    }

    /**
     * Test function getPossibleActions
     */
    public function testGetPossibleActions() {
        $app = $this->jobApplications[1];

        /* SUBMITTED */
        $app->setStatus(JobApplication::STATUS_SUBMITTED);
        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        /* FIRST INTERVIEW SCHEDULED */
        $app->setStatus(JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);

        $event = new JobApplicationEvent(1);
        $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);
        $app->setEvents(array($event));

        // Interview status is scheduled
        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $event = new JobApplicationEvent(1);
        $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);
        $app->setEvents(array($event));

        // Interview status is finished
        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        /* SECOND INTERVIEW SCHEDULED */
        $app->setStatus(JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);

        $event = new JobApplicationEvent(1);
        $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED);
        $app->setEvents(array($event));

        // Interview status is scheduled
        $expected = array(JobApplication::ACTION_REJECT);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $event = new JobApplicationEvent(1);
        $event->setEventType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
        $event->setStatus(JobApplicationEvent::STATUS_INTERVIEW_FINISHED);
        $app->setEvents(array($event));

        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);


        $app->setStatus(JobApplication::STATUS_JOB_OFFERED);
        $expected = array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $app->setStatus(JobApplication::STATUS_OFFER_DECLINED);
        $expected = array();
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $app->setStatus(JobApplication::STATUS_PENDING_APPROVAL);
        $expected = array(JobApplication::ACTION_REJECT, JobApplication::ACTION_APPROVE);
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $app->setStatus(JobApplication::STATUS_HIRED);
        $expected = array();
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);

        $app->setStatus(JobApplication::STATUS_REJECTED);
        $expected = array();
        $actions = $app->getPossibleActions();
        $this->assertEquals($expected, $actions);
    }

	/**
	 * Check's that the passed appliation exists in the database
	 *
	 * @param JobApplication Job Application to check
	 */
	private function _checkExistsInDb($application) {

		$id = $application->getId();
		$vacancyId = $application->getVacancyId();
		$firstName = $application->getFirstName();
		$middleName = $application->getMiddleName();
		$lastName = $application->getLastName();
		$street1 = $application->getStreet1();
		$street2 = $application->getStreet2();
		$city = $application->getCity();
		$province = $application->getProvince();
		$zip = $application->getZip();
		$country = $application->getCountry();
		$mobile = $application->getMobile();
		$phone = $application->getPhone();
		$email = $application->getEmail();
		$qualifications = $application->getQualifications();

	    $this->assertEquals(1, $this->_getNumRows("application_id = {$id} AND vacancy_id = {$vacancyId} AND " .
	    		"firstname = '{$firstName}' AND middlename = '{$middleName}' AND  lastname = '{$lastName}' AND " .
	    		"street1 = '{$street1}'  AND street2 = '{$street2}'  AND city = '{$city}' AND " .
	    		"country_code = '{$country}'  AND province = '{$province}'  AND zip = '{$zip}' AND " .
				"phone = '{$phone}'  AND mobile= '{$mobile}'  AND email = '{$email}'  AND " .
				"qualifications = '{$qualifications}'"));
	}

    /**
     * Returns the number of rows in the hs_hr_job_application table
     *
     * @param  string $where where clause
     * @return int number of rows
     */
    private function _getNumRows($where = null) {

    	$sql = "SELECT COUNT(*) FROM hs_hr_job_application";
    	if (!empty($where)) {
    		$sql .= " WHERE " . $where;
		}

		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $count = $row[0];
		return $count;
    }

    /**
     * Compares two array of JobApplication objects verifing they contain the same
     * objects, without considering the order
     *
     * Objects in first array should be indexed by their id's
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareApplications($expected, $result) {
    	$this->assertEquals(count($expected), count($result));

		foreach ($result as $application) {
			$this->assertTrue($application instanceof JobApplication, "Should return JobApplication objects");

			$id = $application->getId();
			$this->assertEquals($expected[$id], $application);
		}
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
        $application->setEvents(array());
    	return $application;
    }

    /**
     * Compares two array of JobApplication objects verifing they contain the same
     * objects and considering the order
     *
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareApplicationsWithOrder($expected, $result) {
        $this->assertEquals(count($expected), count($result));
        $i = 0;
        foreach ($expected as $application) {
            $this->assertEquals($application, $result[$i]);
            $i++;
        }

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
	 * Run given sql query, checking the return value
	 */
    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error());
    }
}

// Call JobApplicationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "JobApplicationTest::main") {
    JobApplicationTest::main();
}
?>
