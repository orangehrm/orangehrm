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

// Call EmpLocationTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "EmpLocationTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/hrfunct/EmpLocation.php";
require_once ROOT_PATH."/lib/common/UniqueIDGenerator.php";

/**
 * Test class for EmpLocation
 */
class EmpLocationTest extends PHPUnit_Framework_TestCase {

    private $empLocations;
    private $locations;

    private $errorLevel;
    private $errorStr;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("EmpLocationTest");
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

		// Insert employees
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name) " .
                    "VALUES(11, '0011', 'Rajasinghe', 'Saman', 'Marlon')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name) " .
                    "VALUES(12, '0022', 'Jayasinghe', 'Aruna', 'Shantha')");
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name) " .
                    "VALUES(13, '0042', 'Jayaweera', 'Nimal', 'T')");

        // Insert locations
        $this->locations[] = array('LOC001', 'Kandy');
        $this->locations[] = array('LOC002', 'Colombo');
        $this->locations[] = array('LOC003', 'Matara');
        $this->locations[] = array('LOC004', 'Nuwara Eliya');

        foreach ($this->locations as $loc) {
            $this->_insertLocation($loc[0], $loc[1], 'LK', '111 Main St', '1111');
        }

        // Assign locations to employees
        $this->empLocations[0] = new EmpLocation(11, 'LOC001');
        $this->empLocations[0]->setLocationName('Kandy');
        $this->empLocations[1] = new EmpLocation(11, 'LOC002');
        $this->empLocations[1]->setLocationName('Colombo');
        $this->empLocations[2] = new EmpLocation(11, 'LOC003');
        $this->empLocations[2]->setLocationName('Matara');
        $this->empLocations[3] = new EmpLocation(11, 'LOC004');
        $this->empLocations[3]->setLocationName('Nuwara Eliya');
        $this->empLocations[4] = new EmpLocation(12, 'LOC002');
        $this->empLocations[4]->setLocationName('Colombo');
        $this->empLocations[5] = new EmpLocation(12, 'LOC003');
        $this->empLocations[5]->setLocationName('Matara');

        foreach ($this->empLocations as $loc) {
            $this->_assignLocation($loc);
        }

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

    /**
     * Delete data created during test
     */
    private function _deleteTables() {
        $this->_runQuery("TRUNCATE TABLE `hs_hr_emp_locations`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_location`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_employee`");
    }

    /**
     * Test case for save function
     */
    public function testSave() {

        // Invalid emp number
        $empLoc = new EmpLocation('a1', 'LOC001');
        try {
            $empLoc->save();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Invalid location code
        $empLoc = new EmpLocation(11, 'LOCX001');
        try {
            $empLoc->save();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Emp Number not belonging to any employee
        $this->_clearError();
        set_error_handler(array($this, 'errorHandler'));
        $empLoc = new EmpLocation(112, 'LOC011');
        try {
            $empLoc->save();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::DB_ERROR, $e->getCode());
        }

        restore_error_handler();
        $this->assertNotNull($this->errorLevel);

        // Location code not belonging to any location
        $this->_clearError();
        set_error_handler(array($this, 'errorHandler'));
        $empLoc = new EmpLocation(11, 'LOC011');
        try {
            $empLoc->save();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::DB_ERROR, $e->getCode());
        }
        restore_error_handler();
        $this->assertNotNull($this->errorLevel);

        // Emp Number and location ok
        $this->assertEquals(0, $this->_getNumRows('emp_number = 13'));
        $empLoc = new EmpLocation(13, 'LOC002');
        $empLoc->save();
        $this->assertEquals(1, $this->_getNumRows('emp_number = 13'));

        // Reassign same location
        $empLoc = new EmpLocation(13, 'LOC002');
        $empLoc->save();
        $this->assertEquals(1, $this->_getNumRows('emp_number = 13'));

        // Assign different location
        $empLoc = new EmpLocation(13, 'LOC004');
        $empLoc->save();
        $this->assertEquals(2, $this->_getNumRows('emp_number = 13'));
    }

    /**
     * Test case for delete function
     */
    public function testDelete() {

        // Invalid emp number
        $empLoc = new EmpLocation('a1', 'LOC001');
        try {
            $empLoc->delete();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Invalid location code
        $empLoc = new EmpLocation(11, 'LOCX001');
        try {
            $empLoc->delete();
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Not assigned location
        $this->assertEquals(0, $this->_getNumRows('emp_number = 13'));
        $empLoc = new EmpLocation(13, 'LOC002');
        $empLoc->delete();
        $this->assertEquals(0, $this->_getNumRows('emp_number = 13'));

        $this->assertEquals(2, $this->_getNumRows('emp_number = 12'));
        $empLoc = new EmpLocation(12, 'LOC004');
        $empLoc->delete();
        $this->assertEquals(2, $this->_getNumRows('emp_number = 12'));

        // Emp Number and location ok
        $empLoc = new EmpLocation(12, 'LOC002');
        $empLoc->delete();
        $this->assertEquals(1, $this->_getNumRows('emp_number = 12'));

        $empLoc = new EmpLocation(12, 'LOC003');
        $empLoc->delete();
        $this->assertEquals(0, $this->_getNumRows('emp_number = 12'));
    }

    /**
     * Test case for getEmpLocations function
     */
    public function testGetEmpLocations() {

        // Invalid emp number
        try {
            $list = EmpLocation::getEmpLocations('a1');
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Emp Number not belonging to any employee
        $list = EmpLocation::getEmpLocations(111);
        $this->assertTrue(is_array($list));
        $this->assertEquals(0, count($list));

        // employee without any locations assigned
        $list = EmpLocation::getEmpLocations(13);
        $this->assertTrue(is_array($list));
        $this->assertEquals(0, count($list));

        // employee with 2 locations assigned
        $list = EmpLocation::getEmpLocations(12);
        $this->assertTrue(is_array($list));
        $this->assertEquals(2, count($list));
        $this->_compareEmpLocations(array($this->empLocations[4], $this->empLocations[5]), $list);

        // employee with all location assigned
        $list = EmpLocation::getEmpLocations(11);
        $this->assertTrue(is_array($list));
        $this->assertEquals(4, count($list));
        $this->_compareEmpLocations(array($this->empLocations[0], $this->empLocations[1], $this->empLocations[2], $this->empLocations[3]), $list);
    }

    /**
     * Test case for getUnassignedLocations function
     */
    public function testGetUnassignedLocations() {

        // Invalid emp number
        try {
            $list = EmpLocation::getUnassignedLocations('a1');
            $this->fail("Exception expected");
        } catch (EmpLocationException $e) {
            $this->assertEquals(EmpLocationException::INVALID_PARAMETER, $e->getCode());
        }

        // Emp Number not belonging to any employee
        $list = EmpLocation::getUnassignedLocations(111);
        $this->assertTrue(is_array($list));
        $this->assertEquals(4, count($list));
        $this->_compareLocations($this->locations, $list);

        // employee without any locations assigned
        $list = EmpLocation::getUnassignedLocations(13);
        $this->assertTrue(is_array($list));
        $this->assertEquals(4, count($list));
        $this->_compareLocations($this->locations, $list);

        // employee with 2 locations assigned
        $list = EmpLocation::getUnassignedLocations(12);
        $this->assertTrue(is_array($list));
        $this->assertEquals(2, count($list));
        $this->_compareLocations(array($this->locations[0], $this->locations[3]), $list);

        // employee with all location assigned
        $list = EmpLocation::getUnassignedLocations(11);
        $this->assertTrue(is_array($list));
        $this->assertEquals(0, count($list));
    }


    /**
     * Returns the number of rows in the hs_hr_emp_locations table
     *
     * @param  string $where where clause
     * @return int number of rows
     */
    private function _getNumRows($where = null) {

    	$sql = "SELECT COUNT(*) FROM hs_hr_emp_locations";
    	if (!empty($where)) {
    		$sql .= " WHERE " . $where;
    	}
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $count = $row[0];
		return $count;
    }

    /**
     * Compares two arrays EmpLocation objects
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareEmpLocations($expected, $result) {
    	$this->assertEquals(count($expected), count($result));

        $i = 0;
		foreach ($result as $empLocation) {
			$this->assertTrue($empLocation instanceof EmpLocation, "Should return EmpLocation objects");
			$this->assertEquals($expected[$i], $empLocation);
            $this->assertEquals($expected[$i]->getLocationName(), $empLocation->getLocationName());
            $i++;
		}
    }

    /**
     * Compares two arrays of Locations
     *
     * @param array $expected Expected
     * @param array $result Result
     */
    private function _compareLocations($expected, $result) {
        $this->assertEquals(count($expected), count($result));

        $i = 0;
        foreach ($result as $location) {
            $this->assertTrue(is_array($location));
            $this->assertEquals(2, count($location));
            $this->assertTrue(is_array($expected[$i]));
            $this->assertEquals(2, count($expected[$i]));

            $this->assertEquals($expected[$i][0], $location[0]);
            $this->assertEquals($expected[$i][1], $location[1]);
            $i++;
        }
    }

    /**
     * Insert given location into the database
     */
    private function _insertLocation($code, $name, $country, $address, $zip) {
        $sql = sprintf("INSERT INTO hs_hr_location(loc_code, loc_name, loc_country, loc_add, loc_zip) " .
                       "VALUES('%s', '%s', '%s', '%s', '%s')",
                       $code, $name, $country, $address, $zip);
        $this->_runQuery($sql);
    }

    /**
     * Assign Location
     *
     * @param EmpLocation $empLocation Employee location to assign
     */
    private function _assignLocation($empLocation) {
		$sql = sprintf("INSERT INTO hs_hr_emp_locations(emp_number, loc_code) " .
                       "VALUES(%d, '%s')",
                       $empLocation->getEmpNumber(), $empLocation->getLocation());
        $this->_runQuery($sql);
    }

    /**
     * Run given sql query, checking the return value
     */
    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error() . ' SQL=' . $sql);
    }

    public function errorHandler($errlevel, $errstr, $errfile='', $errline='', $errcontext=''){
        $this->errorLevel = $errlevel;
        $this->errorStr = $errstr;
    }

    private function _clearError() {
        $this->errorLevel = null;
        $this->errorStr = null;
    }

}

// Call EmpLocationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "EmpLocationTest::main") {
    EmpLocationTest::main();
}
?>
