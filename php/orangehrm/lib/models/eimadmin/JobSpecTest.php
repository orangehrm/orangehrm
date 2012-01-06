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

// Call JobSpecTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "JobSpecTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/eimadmin/JobSpec.php";
require_once ROOT_PATH."/lib/common/UniqueIDGenerator.php";

/**
 * Test class for JobSpec
 */
class JobSpecTest extends PHPUnit_Framework_TestCase {

	private $jobSpecs;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ProjectSpecTest");
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

		mysql_query("TRUNCATE TABLE `hs_hr_job_spec`");

		// Insert data for tests
		$this->jobSpecs[1] = $this->_getJobSpec(1, 'Job Spec 1', 'desc for job spec 1', 'Duties, dutiess 1 etc.');
        $this->jobSpecs[2] = $this->_getJobSpec(2, 'Job Spec 2', 'xa for job spec 2', 'Duties, dutiess 2 etc.');
		$this->jobSpecs[3] = $this->_getJobSpec(3, 'Job Spec 3', 'qb for job spec 3', 'Duties, dutiess 3 etc.');
		$this->jobSpecs[4] = $this->_getJobSpec(4, 'Job Spec 4', 'dd for job spec 4', 'Duties, dutiess 4 etc.');
		$this->_createJobSpecs($this->jobSpecs);
		UniqueIDGenerator::getInstance()->resetIDs();
    }

    /**
     * Tears down the fixture, removed database entries created during test.
     *
     * @access protected
     */
    protected function tearDown() {
		mysql_query("TRUNCATE TABLE `hs_hr_job_spec`");
		UniqueIDGenerator::getInstance()->resetIDs();
    }

	/**
	 * test the JobSpec delete function.
	 */
	public function testDelete() {

		$before = $this->_getNumRows();

		// invalid params
		try {
			JobSpec::delete(34);
			$this->fail("Exception not thrown");
		} catch (JobSpecException $e) {

		}

		// invalid params
		try {
			JobSpec::delete(array(1, 'w', 12));
			$this->fail("Exception not thrown");
		} catch (JobSpecException $e) {

		}

		// empty array
		$res = JobSpec::delete(array());
		$this->assertEquals(0, $res);

		// no matches
		$res = JobSpec::delete(array(12, 22));
		$this->assertEquals(0, $res);

		// one match
		$res = JobSpec::delete(array(1, 21));
		$this->assertEquals(1, $res);
		$this->assertEquals(1, $before - $this->_getNumRows());

		$before = $this->_getNumRows();

		// one more the rest
		$res = JobSpec::delete(array(3));
		$this->assertEquals(1, $res);
		$this->assertEquals(1, $before - $this->_getNumRows());

		$before = $this->_getNumRows();

		// rest
		$res = JobSpec::delete(array(4, 2));
		$this->assertEquals(2, $res);
		$this->assertEquals(2, $before - $this->_getNumRows());

	}

	/**
	 * Test the save function
	 */
	public function testSave() {

		// no name defined
		$before = $this->_getNumRows();
		$spec = $this->_getJobSpec(null, null, 'Desc1', 'teach');

		try {
			$spec->save();
			$this->fail('Should throw exception');
		} catch (JobSpecException $e) {
		}
		$this->assertEquals($before, $this->_getNumRows());

		// new
		$before = $this->_getNumRows();
		$spec = $this->_getJobSpec(null, 'A test Spec', 'Desc1', 'teach');
		$id = $spec->save();
		$this->assertEquals(($before + 1), $this->_getNumRows());
		$this->assertEquals(1, $this->_getNumRows("jobspec_name = 'A test Spec' AND jobspec_desc = 'Desc1' AND jobspec_duties='teach'"));

		// update
		$before = $this->_getNumRows();
		$spec = $this->_getJobSpec(1, 'XYZ', 'AAA', 'bbb');
		$id = $spec->save();
		$this->assertEquals(1, $id);
		$this->assertEquals($before, $this->_getNumRows());
		$this->assertEquals(1, $this->_getNumRows("jobspec_name = 'XYZ' AND jobspec_desc = 'AAA' AND jobspec_duties='bbb'"));

		// update without name
		$before = $this->_getNumRows();
		$spec = $this->_getJobSpec(2, null, 'AAA', 'bbb');
		try {
			$spec->save();
			$this->fail('Should throw exception');
		} catch (JobSpecException $e) {
		}
		$this->assertEquals($before, $this->_getNumRows());

	}

	/**
	 * Test count method
	 */
	public function testCount() {

		// Count all
		$count = JobSpec::getCount();
		$this->assertEquals(4, $count);

		// Match of ID
		$count = JobSpec::getCount(2, 0);
		$this->assertEquals(1, $count);

		// ID - no match
		$count = JobSpec::getCount(21, 0);
		$this->assertEquals(0, $count);

		// no match
		$count = JobSpec::getCount('XYZ', 1);
		$this->assertEquals(0, $count);

		// Partial match of name
		$count = JobSpec::getCount('Job', 1);
		$this->assertEquals(4, $count);

		// partial match of desc
		$count = JobSpec::getCount('xa', 2);
		$this->assertEquals(1, $count);

		// Full match of name
		$count = JobSpec::getCount('Job Spec 2', 1);
		$this->assertEquals(1, $count);
	}

	/**
	 * Test the getJobSpec function
	 */
	public function testGetJobSpec() {

		// unknown id
		$spec = JobSpec::getJobSpec(383);
		$this->assertNull($spec);

		// invalid id
		try {
			$spec = JobSpec::getJobSpec('7da');
			$this->fail('Should throw exception');
		} catch (JobSpecException $e) {
		}

		// available spec
		$spec = JobSpec::getJobSpec(2);
		$this->assertNotNull($spec);
		$this->assertTrue($this->jobSpecs[2] == $spec);
	}

	/**
	 * Test the getListForView function
	 */
	public function testGetListForView() {

		// Get all
		$list = JobSpec::getListForView();
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$this->_compareSpecsWithOrder($this->jobSpecs, $list);

		// Get all in reverse order by name
		$list = JobSpec::getListForView(0, '', -1, 1, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$expected = array($this->jobSpecs[4],$this->jobSpecs[3],$this->jobSpecs[2],$this->jobSpecs[1]);
		$this->_compareSpecsWithOrder($expected, $list);

		// Search by name with exact match
		$list = JobSpec::getListForView(0, 'Job Spec 3', 1, 1, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobSpecs[3]);
		$this->_compareSpecsWithOrder($expected, $list);


		// Search by name with multiple matches
		$list = JobSpec::getListForView(0, 'Job Spec', 1, 1, 'DESC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$expected = array($this->jobSpecs[4],$this->jobSpecs[3],$this->jobSpecs[2],$this->jobSpecs[1]);
		$this->_compareSpecsWithOrder($expected, $list);

		// Search by description with one match
		$list = JobSpec::getListForView(0, 'qb for job', 2, 1, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobSpecs[3]);
		$this->_compareSpecsWithOrder($expected, $list);

		// Search by id with one match
		$list = JobSpec::getListForView(0, '3', 0, 0, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(1, count($list));
		$expected = array($this->jobSpecs[3]);
		$this->_compareSpecsWithOrder($expected, $list);

		// when no job specs available
		$this->assertTrue(mysql_query('DELETE from hs_hr_job_spec'), mysql_error());
		$list = JobSpec::getAll();
		$this->assertTrue(is_array($list));
		$this->assertEquals(0, count($list));

		// Insert data for tests
		for ($i=1; $i<251; $i++) {

			$inc = 100 + $i;
			if ($i % 2 == 0) {
				$desc = "Even ";
				$even = true;
			} else {
				$desc = "Odd ";
				$even = false;
			}
			$spec = $this->_getJobSpec($i, "Spec-$inc", "$desc-$inc", "Duties");
			$specs[] = $spec;

			if ($even) {
				$evenSpecs[] = $spec;
			} else {
				$oddSpecs[] = $spec;
			}
		}
		
		$this->_createJobSpecs($specs);

		$sysConf = new sysConf();
		$pageSize = $sysConf->itemsPerPage;

		// check paging - without search

		// page 1
		$list = JobSpec::getListForView(1, '', -1, 1, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals($pageSize, count($list));
		$pages = array_chunk($specs, $pageSize);
		$this->_compareSpecsWithOrder($pages[0], $list);
		
		// page 3
		$list = JobSpec::getListForView(3, '', -1, 1, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals($pageSize, count($list));
		$this->_compareSpecsWithOrder($pages[2], $list);

		// paging with search

		// Separate even rows to pages
		$pages = array_chunk($evenSpecs, $pageSize);

		// Search only for even rows and check page 1
		$list = JobSpec::getListForView(1, 'Even', 2, 1, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(count($pages[0]), count($list));
		$this->_compareSpecsWithOrder($pages[0], $list);

		$list = JobSpec::getListForView(3, 'Even', 2, 1, 'ASC');
		$this->assertTrue(is_array($list));
		$this->assertEquals(count($pages[2]), count($list));
		$this->_compareSpecsWithOrder($pages[2], $list);
	}

	/**
	 * test the getAll function
	 */
	public function testGetAll() {

		// Get all
		$list = JobSpec::getAll();
		$this->assertTrue(is_array($list));
		$this->assertEquals(4, count($list));
		$this->_compareSpecs($this->jobSpecs, $list);

		// when no job specs available
		$this->assertTrue(mysql_query('DELETE from hs_hr_job_spec'), mysql_error());
		$list = JobSpec::getAll();
		$this->assertTrue(is_array($list));
		$this->assertEquals(0, count($list));
	}

    /**
     * Returns the number of rows in the hs_hr_job_spec table
     *
     * @param  string $where where clause
     * @return int number of rows
     */
    private function _getNumRows($where = null) {

    	$sql = "SELECT COUNT(*) FROM hs_hr_job_spec";
    	if (!empty($where)) {
    		$sql .= " WHERE " . $where;
    	}
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $count = $row[0];
		return $count;
    }

    /**
     * Compares two array of JobSpec objects verifing they contain the same
     * objects, without considering the order
     *
     * Objects in first array should be indexed by their id's
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareSpecs($expected, $result) {
    	$this->assertEquals(count($expected), count($result));

		foreach ($result as $spec) {
			$this->assertTrue($spec instanceof JobSpec, "Should return JobSpec objects");

			$id = $spec->getId();
			$this->assertEquals($expected[$id], $spec);
		}
    }

    /**
     * Compares two array of JobSpec objects verifing they contain the same
     * objects and considering the order
     *
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareSpecsWithOrder($expected, $result) {
    	$this->assertEquals(count($expected), count($result));

		$i = 0;
		foreach ($expected as $spec) {
			$this->assertEquals($spec->getId(), $result[$i][0]);
			$this->assertEquals($spec->getName(), $result[$i][1]);
			$this->assertEquals($spec->getDesc(), $result[$i][2]);
			$i++;
		}

    }

    /**
     * Checks that the attributes of the Job Spec object and the database row match.
     *
     * @param JobSpec $spec
     * @param array  $row
     */
    private function _checkRow($spec, $row) {
		$this->assertEquals($spec->getName(), $row['jobspec_name'], "Name not correct");
		$this->assertEquals($spec->getDescription(), $row['jobspec_desc'], "Description not correct");
		$this->assertEquals($spec->getId(), $row['jobspec_id'], "ID not correct");
		$this->assertEquals($spec->getDuties(), $row['jobspec_duties'], "Duties not correct");
    }

    /**
     * Create a JobSpec object with the passed parameters
     */
    private function _getJobSpec($id, $name, $desc, $duties) {
    	$spec = new JobSpec($id);
    	$spec->setName($name);
    	$spec->setDesc($desc);
    	$spec->setDuties($duties);
    	return $spec;
    }

    /**
     * Saves the given JobSpec objects in the databas
     *
     * @param array $specs Array of JobSpec objects to save.
     */
    private function _createJobSpecs($specs) {
		foreach ($specs as $spec) {
			$sql = sprintf("INSERT INTO hs_hr_job_spec(jobspec_id, jobspec_name, jobspec_desc, jobspec_duties) " .
                           "VALUES(%d, '%s', '%s', '%s')",
                           $spec->getId(), $spec->getName(), $spec->getDesc(),
                           $spec->getDuties());
            $this->assertTrue(mysql_query($sql), mysql_error());
		}
        UniqueIDGenerator::getInstance()->initTable();
    }
}

// Call JobSpecTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "JobSpecTest::main") {
    JobSpecTest::main();
}
?>
