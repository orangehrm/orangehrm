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

// Call JobVacancyTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "JobVacancyTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/recruitment/JobVacancy.php";
require_once ROOT_PATH."/lib/common/UniqueIDGenerator.php";

/**
 * Test class for JobVacancy
 */
class JobVacancyTest extends PHPUnit_Framework_TestCase {

	private $jobVacancies;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("JobVacancyTest");
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

		// Insert data for tests
		$vacancy = $this->_getJobVacancy(1, 'JOB001', 11, true, 'Job vacancy 1');
		$vacancy->setJobTitleName('Manager');
		$vacancy->setManagerName('Saman Rajasinghe');
		$this->jobVacancies[1] = $vacancy;
        $vacancy = $this->_getJobVacancy(2, 'JOB002', 11, false, 'Job vacancy 2');
		$vacancy->setJobTitleName('Driver');
		$vacancy->setManagerName('Saman Rajasinghe');
        $this->jobVacancies[2] = $vacancy;
		$vacancy = $this->_getJobVacancy(3, 'JOB003', 12, false, 'Job vacancy 3');
		$vacancy->setJobTitleName('Typist');
		$vacancy->setManagerName('Aruna Jayasinghe');
		$this->jobVacancies[3] = $vacancy;
		$vacancy = $this->_getJobVacancy(4, 'JOB004', 12, true, 'Job vacancy 4');
		$vacancy->setJobTitleName('Programmer');
		$vacancy->setManagerName('Aruna Jayasinghe');
		$this->jobVacancies[4] = $vacancy;

		$this->_createJobVacancies($this->jobVacancies);
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
		$this->_runQuery("TRUNCATE TABLE `hs_hr_job_vacancy`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_title`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_employee`");
	}

	/**
	 * test the JobVacancy delete function.
	 */
	public function testDelete() {

		$before = $this->_getNumRows();

		// invalid params
		try {
			JobVacancy::delete(34);
			$this->fail("Exception not thrown");
		} catch (JobVacancyException $e) {

		}

		// invalid params
		try {
			JobVacancy::delete(array(1, 'w', 12));
			$this->fail("Exception not thrown");
		} catch (JobVacancyException $e) {

		}

		// empty array
		$res = JobVacancy::delete(array());
		$this->assertEquals(0, $res);
		$this->assertEquals($before, $this->_getNumRows());

		// no matches
		$res = JobVacancy::delete(array(12, 22));
		$this->assertEquals(0, $res);
		$this->assertEquals($before, $this->_getNumRows());

		// one match
		$res = JobVacancy::delete(array(1, 21));
		$this->assertEquals(1, $res);
		$this->assertEquals(1, $before - $this->_getNumRows());

		$before = $this->_getNumRows();

		// one more the rest
		$res = JobVacancy::delete(array(3));
		$this->assertEquals(1, $res);
		$this->assertEquals(1, $before - $this->_getNumRows());

		$before = $this->_getNumRows();

		// rest
		$res = JobVacancy::delete(array(4, 2));
		$this->assertEquals(2, $res);
		$this->assertEquals(2, $before - $this->_getNumRows());

	}

	/**
	 * Test the save function
	 */
	public function testSave() {

		// new
		$before = $this->_getNumRows();
		$vacancy = $this->_getJobVacancy(null, 'JOB004', 11, true, 'Job vacancy 111');

		$id = $vacancy->save();
		$this->assertEquals(($before + 1), $this->_getNumRows());
		$this->assertEquals(1, $this->_getNumRows("vacancy_id = {$id} AND jobtit_code = 'JOB004' AND manager_id = 11 AND active = '".JobVacancy::STATUS_ACTIVE."' AND description = 'Job vacancy 111'"));

		// update
		$before = $this->_getNumRows();
		$vacancy = $this->_getJobVacancy($id, 'JOB003', 12, true, 'Job vacancy 222');

		$newId = $vacancy->save();
		$this->assertEquals($id, $newId);
		$this->assertEquals($before, $this->_getNumRows());
		$this->assertEquals(1, $this->_getNumRows("vacancy_id = {$id} AND jobtit_code = 'JOB003' AND manager_id = 12 AND active = '".JobVacancy::STATUS_ACTIVE."' AND description = 'Job vacancy 222'"));

	}

	/**
	 * Test count method
	 */
	public function testCount() {

		// Count all
		$count = JobVacancy::getCount();
		$this->assertEquals(4, $count);

		// Match of ID
		$count = JobVacancy::getCount(2, 0);
		$this->assertEquals(1, $count);

		// ID - no match
		$count = JobVacancy::getCount(21, 0);
		$this->assertEquals(0, $count);

		// no match - job title name
		$count = JobVacancy::getCount('Administrator', 1);
		$this->assertEquals(0, $count);

		// match - job title name
		$vacancies[] = $this->_getJobVacancy(5, 'JOB004', 11, true, 'Job vacancy 4441');
		$this->_createJobVacancies($vacancies);

		$count = JobVacancy::getCount('Programmer', 1);
		$this->assertEquals(2, $count);

		$count = JobVacancy::getCount('Manager', 1);
		$this->assertEquals(1, $count);

		// Partial match - job title name
		$count = JobVacancy::getCount('Man', 1);
		$this->assertEquals(1, $count);

 		// No Match of manager name
		$count = JobVacancy::getCount('Brown', 2);
		$this->assertEquals(0, $count);

		// Match of manager name
		$count = JobVacancy::getCount('Saman Rajasinghe', 2);
		$this->assertEquals(3, $count);

		// partial match of manager name
		$count = JobVacancy::getCount('Arun', 2);
		$this->assertEquals(2, $count);

		// Match of status
		$count = JobVacancy::getCount(JobVacancy::STATUS_ACTIVE, 3);
		$this->assertEquals(3, $count);

		$count = JobVacancy::getCount(JobVacancy::STATUS_INACTIVE, 3);
		$this->assertEquals(2, $count);

		// No Match of description
		$count = JobVacancy::getCount('XYZ', 4);
		$this->assertEquals(0, $count);

		// Match of description
		$count = JobVacancy::getCount('Job vacancy 1', 4);
		$this->assertEquals(1, $count);

		// Partial Match of description
		$count = JobVacancy::getCount('Job', 4);
		$this->assertEquals(5, $count);

		// delete all
		$this->_runQuery("DELETE FROM hs_hr_job_vacancy");
		$count = JobVacancy::getCount();
		$this->assertEquals(0, $count);

	}

	/**
	 * Test the getJobVacancy function
	 */
	public function testGetJobVacancy() {

		// unknown id
		$vacancy = JobVacancy::getJobVacancy(383);
		$this->assertNull($vacancy);

		// invalid id
		try {
			$vacancy = JobVacancy::getJobVacancy('7da');
			$this->fail('Should throw exception');
		} catch (JobVacancyException $e) {
		}

		// available vacancy
		$vacancy = JobVacancy::getJobVacancy(2);
		$this->assertNotNull($vacancy);
		$expected = $this->jobVacancies[2];
		$this->assertTrue($expected == $vacancy);
	}

	/**
	 * Test the getListForView function
	 */
	public function testGetListForView() {

		// Get all
		$list = JobVacancy::getListForView();
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$this->_compareVacanciesWithOrder($this->jobVacancies, $list);

		// Get all in reverse order by job title name
		$list = JobVacancy::getListForView(0, '', JobVacancy::SORT_FIELD_NONE, JobVacancy::SORT_FIELD_JOBTITLE_NAME, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$expected = array($this->jobVacancies[3],$this->jobVacancies[4],$this->jobVacancies[1],$this->jobVacancies[2]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by job title name with exact match
		$list = JobVacancy::getListForView(0, 'Typist', JobVacancy::SORT_FIELD_JOBTITLE_NAME, JobVacancy::SORT_FIELD_JOBTITLE_NAME, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobVacancies[3]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by description with multiple matches
		$list = JobVacancy::getListForView(0, 'Job vacancy', JobVacancy::SORT_FIELD_DESCRIPTION, JobVacancy::SORT_FIELD_DESCRIPTION, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$expected = array($this->jobVacancies[4],$this->jobVacancies[3],$this->jobVacancies[2],$this->jobVacancies[1]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by description with one match
		$list = JobVacancy::getListForView(0, 'Job vacancy 2', JobVacancy::SORT_FIELD_DESCRIPTION, JobVacancy::SORT_FIELD_DESCRIPTION, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobVacancies[2]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by id with one match
		$list = JobVacancy::getListForView(0, '3', JobVacancy::SORT_FIELD_VACANCY_ID, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobVacancies[3]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by id with no matches
		$list = JobVacancy::getListForView(0, '13', JobVacancy::SORT_FIELD_VACANCY_ID, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertNull($list);

		// Search by manager name with matches
		$list = JobVacancy::getListForView(0, 'Aruna', JobVacancy::SORT_FIELD_MANAGER_NAME, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(2, count($list));
		$expected = array($this->jobVacancies[3], $this->jobVacancies[4]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// Search by manager name with no matches
		$list = JobVacancy::getListForView(0, 'Kamal', JobVacancy::SORT_FIELD_MANAGER_NAME, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertNull($list);

		// Search by active status
		$list = JobVacancy::getListForView(0, JobVacancy::STATUS_ACTIVE, JobVacancy::SORT_FIELD_ACTIVE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(2, count($list));
		$expected = array($this->jobVacancies[1], $this->jobVacancies[4]);
		$this->_compareVacanciesWithOrder($expected, $list);

		$list = JobVacancy::getListForView(0, JobVacancy::STATUS_INACTIVE, JobVacancy::SORT_FIELD_ACTIVE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(2, count($list));
		$expected = array($this->jobVacancies[2], $this->jobVacancies[3]);
		$this->_compareVacanciesWithOrder($expected, $list);

		// when no job vacancys available
		$this->assertTrue(mysql_query('DELETE from hs_hr_job_vacancy'), mysql_error());
		$list = JobVacancy::getListForView();
		$this->assertNull($list);

		// Insert data for paging tests
		for ($i=1; $i<251; $i++) {

			$inc = 100 + $i;
			if ($i % 2 == 0) {
				$desc = "Even ";
				$even = true;
				$status = true;
			} else {
				$desc = "Odd ";
				$even = false;
				$status = false;
			}
			$vacancy = $this->_getJobVacancy($i, 'JOB001', 11, $status, "$desc-$inc");
			$vacancy->setJobTitleName('Manager');
			$vacancy->setManagerName('Saman Rajasinghe');
			$vacancys[] = $vacancy;

			if ($even) {
				$evenVacancys[] = $vacancy;
			} else {
				$oddVacancys[] = $vacancy;
			}
		}

		$this->_createJobVacancies($vacancys);

		$sysConf = new sysConf();
		$pageSize = $sysConf->itemsPerPage;

		// check paging - without search
		// page 1
		$list = JobVacancy::getListForView(1, '', JobVacancy::SORT_FIELD_NONE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals($pageSize, count($list));
		$pages = array_chunk($vacancys, $pageSize);
		$this->_compareVacanciesWithOrder($pages[0], $list);

		// page 3
		$list = JobVacancy::getListForView(3, '', JobVacancy::SORT_FIELD_NONE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals($pageSize, count($list));
		$this->_compareVacanciesWithOrder($pages[2], $list);

		// paging with search

		// Separate even rows to pages
		$pages = array_chunk($evenVacancys, $pageSize);

		// Search only for even (status = active) rows and check page 1
		$list = JobVacancy::getListForView(1, JobVacancy::STATUS_ACTIVE, JobVacancy::SORT_FIELD_ACTIVE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(count($pages[0]), count($list));
		$this->_compareVacanciesWithOrder($pages[0], $list);

		$list = JobVacancy::getListForView(3, JobVacancy::STATUS_ACTIVE, JobVacancy::SORT_FIELD_ACTIVE, JobVacancy::SORT_FIELD_VACANCY_ID, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(count($pages[2]), count($list));
		$this->_compareVacanciesWithOrder($pages[2], $list);
	}

	/**
	 * test the getAll function
	 */
	public function testGetAll() {

		// Get all
		$list = JobVacancy::getAll();
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$this->_compareVacancys($this->jobVacancies, $list);

		// when no job vacancys available
		$this->assertTrue(mysql_query('DELETE from hs_hr_job_vacancy'), mysql_error());
		$list = JobVacancy::getAll();
		$this->assertTrue(is_array($list));
		$this->assertEquals(0, count($list));
	}

	/**
	 * test the getActive function
	 */
	public function testGetActive() {

		// Get all active
		$list = JobVacancy::getActive();
		$this->assertTrue(is_array($list));
		$this->assertEquals(2, count($list));
		$expected = array(1=>$this->jobVacancies[1], 4=>$this->jobVacancies[4]);
		$this->_compareVacancys($expected, $list);

		// Mark all as inactive and get all
		$sql = 'UPDATE hs_hr_job_vacancy SET active = ' . JobVacancy::STATUS_INACTIVE;
		$this->_runQuery($sql);
		$list = JobVacancy::getActive();
		$this->assertTrue(is_array($list));
		$this->assertEquals(0, count($list));

		// when no job vacancies available
		$this->_runQuery('DELETE from hs_hr_job_vacancy');
		$list = JobVacancy::getActive();
		$this->assertTrue(is_array($list));
		$this->assertEquals(0, count($list));
	}

    /**
     * Returns the number of rows in the hs_hr_job_vacancy table
     *
     * @param  string $where where clause
     * @return int number of rows
     */
    private function _getNumRows($where = null) {

    	$sql = "SELECT COUNT(*) FROM hs_hr_job_vacancy";
    	if (!empty($where)) {
    		$sql .= " WHERE " . $where;
    	}
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $count = $row[0];
		return $count;
    }

    /**
     * Compares two array of JobVacancy objects verifing they contain the same
     * objects, without considering the order
     *
     * Objects in first array should be indexed by their id's
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareVacancys($expected, $result) {
    	$this->assertEquals(count($expected), count($result));

		foreach ($result as $vacancy) {
			$this->assertTrue($vacancy instanceof JobVacancy, "Should return JobVacancy objects");

			$id = $vacancy->getId();
			$this->assertEquals($expected[$id], $vacancy);
		}
    }

    /**
     * Compares two array of JobVacancy objects verifing they contain the same
     * objects and considering the order
     *
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareVacanciesWithOrder($expected, $result) {
    	$this->assertEquals(count($expected), count($result));
		$i = 0;
		foreach ($expected as $vacancy) {
			$this->assertEquals($vacancy->getId(), $result[$i][0]);
			$this->assertEquals($vacancy->getJobTitleName(), $result[$i][1]);
			$this->assertEquals($vacancy->getManagerName(), $result[$i][2]);
			$this->assertEquals($vacancy->isActive(), $result[$i][3] == 1);
			$this->assertEquals($vacancy->getDescription(), $result[$i][4]);
			$i++;
		}

    }

    /**
     * Create a JobVacancy object with the passed parameters
     */
    private function _getJobVacancy($id, $jobTitleCode, $managerId, $active, $description) {

    	$vacancy = new JobVacancy($id);
		$vacancy->setJobTitleCode($jobTitleCode);
		$vacancy->setManagerId($managerId);
		$vacancy->setActive($active);
		$vacancy->setDescription($description);
    	return $vacancy;
    }

    /**
     * Saves the given JobVacancy objects in the databas
     *
     * @param array $vacancies Array of JobVacancy objects to save.
     */
    private function _createJobVacancies($vacancies) {
		foreach ($vacancies as $vacancy) {
			$active = $vacancy->isActive() ? JobVacancy::STATUS_ACTIVE : JobVacancy::STATUS_INACTIVE;
			$sql = sprintf("INSERT INTO hs_hr_job_vacancy(vacancy_id, jobtit_code, manager_id, active, description) " .
                           "VALUES(%d, '%s', %d, '%s', '%s')",
                           $vacancy->getId(), $vacancy->getJobTitleCode(), $vacancy->getManagerId(),
                           $active, $vacancy->getDescription());
            $this->assertTrue(mysql_query($sql), mysql_error());
		}
		UniqueIDGenerator::getInstance()->initTable();
    }

    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error());
    }
}

// Call JobVacancyTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "JobVacancyTest::main") {
    JobVacancyTest::main();
}
?>
