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
 */

require_once 'PHPUnit/Framework.php';

class EmployeeTableTest extends PHPUnit_Framework_TestCase {

    /** File containing test fixtures */
    const DATA_FIXTURE = '/fixtures/db/employee_table_test.yml';

    private $employees;
    private $empStatuses;
    private $jobTitles;
    private $companyStructure;
    private $reportTo;

    /**
     * PHPUnit setup function
     */
    public function setup() {
        $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);
        new sfDatabaseManager($configuration);
        Doctrine::loadData(sfConfig::get('sf_test_dir') . self::DATA_FIXTURE);

        $this->_getEmployees();
    }

    /**
     * TestCase for getEmployeeList() function
     */
    public function testGetEmployeeList() {

        // do the default sort
        $employees = $this->_sortList($this->employees, 'empNumber', 'asc', SORT_NUMERIC);

        // Get all, default sort (emp_number) asc, no filters
        $list = Doctrine::getTable('Employee')->getEmployeeList();
        $this->_compareLists($list, $employees, 'empNumber asc');

        // try all other possible sorts
        // Todo: Add test for sort by supervisor names
        $sorts = array('employeeId', 'fullName', 'jobTitle', 'empStatus', 'subDivision');
        $orders = array('asc', 'desc');

        foreach ($sorts as $sort) {
            foreach ($orders as $order) {

                $list = Doctrine::getTable('Employee')->getEmployeeList($sort, $order);
                $expected = $this->_sortList($employees, $sort, $order);
                $this->_compareLists($list, $expected, 'sort: ' . $sort . ', order: ' . $order);
            }
        }

        // test filtering
        $filters = array(array('employeeId'=>'2'),
                         array('firstName'=>'K'),
                         array('middleName'=>'Paul'),
                         array('lastName'=>'S'),
                         array('jobTitle'=>'Engineering'),
                         array('employeeStatus'=>'Full Time'),
                         array('subDivision'=>'Division'),
                         array('employeeStatus'=>'Part Time', 'jobTitle'=>'Programmer'),
                         array('supervisor'=>'Lynn'));

        foreach ($filters as $filter) {
                $list = Doctrine::getTable('Employee')->getEmployeeList('empNumber', 'asc', $filter);
                $expected = $this->_filterList($employees, $filter);
                $this->_compareLists($list, $expected, 'filter: ' . implode(',', array_keys($filter)));
        }

        // Test that terminated employees are not fetched by default

        // Check if first sorted by direct supervisors, then indirect
    }

    /**
     * Tests that employee list hides terminated employees by default
     */
    public function testListHidesTerminatedEmployees() {
        $employees = $this->employees;

        $terminated = array($employees[2], $employees[4]);
        $notterminated = $employees;
        unset($notterminated[2]);
        unset($notterminated[4]);

        $ids[] = $employees[2]['empNumber'];
        $ids[] = $employees[4]['empNumber'];

        // mark two employees as terminated
        $query = Doctrine_Query::create()
                 ->update('Employee')
                 ->set('emp_status', '?', 'EST000')
                 ->wherein('empNumber', $ids);
        $updated = $query->execute();
        $this->assertEquals(2, $updated, "Both rows not marked as terminated");

        // verify they are not returned in list

        // do the default sort
        $notterminated = $this->_sortList($notterminated, 'empNumber', 'asc', SORT_NUMERIC);

        $list = Doctrine::getTable('Employee')->getEmployeeList();
        $this->_compareLists($list, $notterminated, 'Terminated employees should not be returned');

        // do a sort, terminated should still not be returned
        $sort = 'fullName';
        $order = 'desc';

        $list = Doctrine::getTable('Employee')->getEmployeeList($sort, $order);
        $expected = $this->_sortList($notterminated, $sort, $order);
        $this->_compareLists($list, $expected, 'Terminated employees should not be returned. sort by fullName');

        // do a search by different field, terminated should not be returned
        $filter = array('jobTitle'=>'engin');
        $list = Doctrine::getTable('Employee')->getEmployeeList($sort, $order, $filter);
        $this->assertTrue(count($list) > 2, 'Less than 3 employees with engin title in test data');
        $expected = $this->_sortList($notterminated, $sort, $order);
        $expected = $this->_filterList($expected, $filter);
        $this->_compareLists($list, $expected, 'Terminated employees should not be returned. ' .
                                               'Search by jobtitle');


        // search for employees with terminated status, check they are returned
        $filter = array('employeeStatus'=>'Terminated');
        $list = Doctrine::getTable('Employee')->getEmployeeList($sort, $order, $filter);
        $expected = $this->_sortList($terminated, $sort, $order);
        $this->_compareLists($list, $expected, 'Terminated employees should be returned if ' .
                                               'searching by terminated status');

        // search for employees with different status, terminated employees should be returned.
        $filter = array('employeeStatus'=>'Part Time');

        $list = Doctrine::getTable('Employee')->getEmployeeList($sort, $order, $filter);
        $expected = $this->_sortList($notterminated, $sort, $order);
        $expected = $this->_filterList($expected, $filter);
        $this->_compareLists($list, $expected, 'Terminated employees should not be returned if ' .
                                               'searching by different status');
    }

    /**
     * Test that search by subdivision name matches all levels of company structure
     * above the employees subdivision
     */
    public function testSearchMatchesSubdivisionsInHierarchy() {

        // Set an employee location to "Pre Sales Team", id 5, the hierarchy is:
        // OrangeHRM Test (1) -> Engineering Department (3)-> Pre Sales Team (5)
        $employees = $this->employees;
        $employee = $employees[5];
        $query = Doctrine_Query::create()
                 ->update('Employee')
                 ->set('work_station', '?', 5)
                 ->where('empNumber = ?', $employee['empNumber']);
        $updated = $query->execute();
        $this->assertEquals(1, $updated, "Employee workstation not updated");

        // search for "Pre Sales Team" should return above employee
        $filter = array('subDivision'=>'Pre-sales Team');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');

        // partial string search
        $filter = array('subDivision'=>'sales');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');


        // search for Engineering Department" should return above employee
        $filter = array('subDivision'=>'Engineering Department');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');

        // Partial string search
        $filter = array('subDivision'=>'Engineering');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');


        // Search for "OrangeHRM Test" should return above employee
        $filter = array('subDivision'=>'OrangeHRM Test');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');

        // partial string search
        $filter = array('subDivision'=>'HRM');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertTrue($this->_employeeInList($list, $employee), 'Employee not fetched');


        // Search for "Sales Department" should not return above employee
        $filter = array('subDivision'=>'Sales Department');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertFalse($this->_employeeInList($list, $employee), 'Employee should not be fetched');

        // Search for "Research  Division" should not return above employee
        $filter = array('subDivision'=>'Research  Division');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $this->assertFalse($this->_employeeInList($list, $employee), 'Employee should not be fetched');

    }

    /**
     * Test that searching by supervisor Id works. Used for supervisor view of PIM list
     */
    public function testSearchBySupervisorId() {
        $employees = $this->employees;
        $reportTo = $this->reportTo;

        // Setup one employee as supervisor of 5 other employees
        $supervisorId = $employees[5]['empNumber'];

        // delete any existing subordinates of above employee to
        // get to a known state to test from
        $count = Doctrine_Query::create()
                         ->delete()
                         ->from('ReportTo')
                         ->where('supervisorId = ?', $supervisorId)
                         ->execute();

        // Assign 5 employees
        $subordinates = array($employees[3], $employees[6],
                              $employees[7], $employees[8], $employees[9]);
        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare("INSERT into hs_hr_emp_reportto(erep_sup_emp_number, " .
                                    " erep_sub_emp_number, erep_reporting_mode)" .
        	                        " VALUES(?, ?, 1)");
        foreach ($subordinates as $subordinate) {
            $result = $statement->execute(array($supervisorId, $subordinate['empNumber']));
            $this->assertTrue($result, "Assign supervisors failed");
            $this->assertEquals(1, $statement->rowCount(), "insert failed");
        }

        // do the default sort
        $subordinates = $this->_sortList($subordinates, 'empNumber', 'asc', SORT_NUMERIC);

        // Search by supervisor Id
        $filter = array('supervisorId' => $supervisorId);
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);

        $expected = $subordinates;
        $this->assertEquals(5, count($expected), 'test data should contain 5 employees under supervisor');
        $this->_compareLists($list, $expected, 'filter by supervisorId with subdivision incorrect');

        // Search by supervisorId and status
        $filter = array('supervisorId' => $supervisorId, 'employeeStatus'=>'Part Time');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $expected = $this->_filterList($subordinates, array('employeeStatus'=>'Part Time'));
        $this->assertTrue(count($expected) > 1, 'test data should contain at least 2 part time ' .
                                                'employees under supervisor');
        $this->_compareLists($list, $expected, 'filter by supervisorId with subdivision incorrect');

        // Search by supervisorId and subDivision
        $filter = array('supervisorId' => $supervisorId, 'subDivision'=>'OrangeHRM Test');
        $list = Doctrine::getTable('Employee')->getEmployeeList(null, null, $filter);
        $expected = $this->_filterList($subordinates, array('subDivision'=>'OrangeHRM Test'));
        $this->assertTrue(count($expected) > 2, 'test data should contain at least two employees ' .
                                                'with subdivision under "OrangeHRM Test"');
        $this->_compareLists($list, $expected, 'filter by supervisorId with subdivision incorrect');

    }

    /**
     * Test that sorting by supervisor works when employee has multiple supervisors
     */
    public function testSortListBySupervisor() {

    }

    /**
     * Test case for delete() function
     */
    public function testDelete() {

       $employees = $this->employees;

       $empIds = array();
       foreach ($employees as $emp) {
           $empIds[] = $emp['empNumber'];
       }

       // delete 1 employee
       $ids = array_slice($empIds, 3, 1);
       $this->assertEquals(1, $this->_countEmployees($ids));

       $count = Doctrine::getTable('Employee')->delete($ids);
       $this->assertEquals(1, $count, '1 employee should be deleted');

       // verify deleted employee no longer available.
       $this->assertEquals(0, $this->_countEmployees($ids));

       // delete 3 employees
       $ids = array_slice($empIds, 5, 3);
       $this->assertEquals(3, $this->_countEmployees($ids));

       $count = Doctrine::getTable('Employee')->delete($ids);
       $this->assertEquals(3, $count, '3 employees should be deleted');

       $this->assertEquals(0, $this->_countEmployees($ids));

       // Try deleting already deleted employee
       $ids = array_slice($empIds, 3, 1);
       $count = Doctrine::getTable('Employee')->delete($ids);
       $this->assertEquals(0, $count, 'return 0 if employee already deleted');

    }

//
// Tests for private functions of this test class
//

    /**
     * Tests the _sortList function in this test class
     */
    public function testSortList() {
        $row1 = array('a'=>'syz', 'b'=>'jkl', 'c'=>'erkj');
        $row2 = array('a'=>'rad', 'b'=>'acd', 'c'=>'eee');
        $row3 = array('a'=>'eas', 'b'=>'ood', 'c'=>'adf');
        $data = array($row1, $row2, $row3);

        $sorted = $this->_sortList($data, 'b', 'asc');
        $expected = array($row2, $row1, $row3);
        $this->assertTrue($expected === $sorted);

        $sorted = $this->_sortList($data, 'c', 'desc');
        $expected = array($row1, $row2, $row3);
        $this->assertTrue($expected === $sorted);

        $sorted = $this->_sortList($data, 'a', 'asc');
        $expected = array($row3, $row2, $row1);
        $this->assertTrue($expected === $sorted);

    }

    /**
     * Tests the _filterList function in this test class
     */
    public function testFilterList() {

        $row1 = array('a'=>'John', 'b'=>'Richard', 'c'=>'Jackson');
        $row2 = array('a'=>'Anne', 'b'=>'Kerry', 'c'=>'Jack');
        $row3 = array('a'=>'Jane', 'b'=>'Leone', 'c'=>'John');
        $data = array($row1, $row2, $row3);

        $filtered = $this->_filterList($data, array('a'=>'J'));
        $expected = array($row1, $row3);
        $this->assertTrue($expected === $filtered);

        $filtered = $this->_filterList($data, array('b' => 'Richard'));
        $expected = array($row1);
        $this->assertTrue($expected === $filtered);

        $filtered = $this->_filterList($data, array('c' => 'Jack'));
        $expected = array($row1, $row2);
        $this->assertTrue($expected === $filtered);

        $filtered = $this->_filterList($data, array('b' => 'r'));
        $expected = array($row1, $row2);
        $this->assertTrue($expected === $filtered);
    }

    public function testGetCompanyHierarchy() {
        $companyStructure =
            array('CompanyStructure_1' =>
                      array('id'=>'1', 'title' => 'ABC Company', 'parnt' => '0'),
                  'CompanyStructure_2' =>
                      array('id'=>'1', 'title' => 'Engineering', 'parnt' => '1'),
                  'CompanyStructure_3' =>
                      array('id'=>'3', 'title' => 'QA Department', 'parnt' => '1'),
                  'CompanyStructure_4' =>
                      array('id'=>'4', 'title' => 'Professional Services', 'parnt' => '2'),
                  'CompanyStructure_5' =>
                      array('id'=>'5', 'title' => 'Pre Sales', 'parnt' => '4'),
                  'CompanyStructure_6' =>
                      array('id'=>'1', 'title' => 'Accounting', 'parnt' => '1'));

        /* Just root */
        $this->assertEquals('ABC Company', $this->_getCompanyHierarchy($companyStructure['CompanyStructure_1'], $companyStructure));

        /* Two levels */
        $this->assertEquals('ABC Company->Engineering',
                             $this->_getCompanyHierarchy($companyStructure['CompanyStructure_2'], $companyStructure));
        $this->assertEquals('ABC Company->QA Department',
                             $this->_getCompanyHierarchy($companyStructure['CompanyStructure_3'], $companyStructure));
        $this->assertEquals('ABC Company->Accounting',
                             $this->_getCompanyHierarchy($companyStructure['CompanyStructure_6'], $companyStructure));

        /* Three levels */
        $this->assertEquals('ABC Company->Engineering->Professional Services',
                             $this->_getCompanyHierarchy($companyStructure['CompanyStructure_4'], $companyStructure));

        /* Four levels */
        $this->assertEquals('ABC Company->Engineering->Professional Services->Pre Sales',
                             $this->_getCompanyHierarchy($companyStructure['CompanyStructure_5'], $companyStructure));
    }

    public function testEmployeeInList() {

    }

    /**
     * Checks if given employee is in the given list
     * @param array $list Array of employees
     * @param array $employee Employee
     * @return boolean True if employee in list, false otherwise
     */
    private function _employeeInList($list, $employee) {
        $found = false;
        foreach ($list as $row){
            if ( ($row['empNumber'] == $employee['empNumber']) &&
                     ($row['lastName'] == $employee['lastName']) &&
                     ($row['middleName'] == $employee['middleName']) &&
                     ($row['employeeId'] == $employee['employeeId']) ) {

                $found = true;
                break;
            }

        }
        return $found;
    }


    /**
     * Sorts the given 2 dimentional array by the given column and sort order and
     * returns the sorted array
     *
     * String sorting is case insensitive to match mysql default behavior
     *
     * @param array  $data   Array to be sorted
     * @param String $column Column name (key) to sort the array by
     * @param String $order  Sort order. 'asc' or 'desc'
     * @return Array sorted array
     */
    private function _sortList(array $data, $column, $order, $sortType = SORT_STRING) {
        $sortOrder = $order === 'asc' ? SORT_ASC : SORT_DESC;

        $sortColumn = array();
        foreach ($data as $key => $row) {
            $sortColumn[$key]  = strtolower($row[$column]);
            $defaultColumn[$key] = $row['empNumber'];
        }

        if ($column != 'empNumber') {
            array_multisort($sortColumn, $sortOrder, $sortType, $defaultColumn, SORT_ASC, SORT_NUMERIC, $data);
        } else {
            array_multisort($sortColumn, $sortOrder, $sortType, $data);
        }
        return $data;
    }

    /**
     * Filters array using given filters
     *
     * @param array $data array to filter
     * @param array $filters Filters with key=>value pairs to filter by
     * @return array filtered array
     */
    private function _filterList(array $data, array $filters) {
        $filtered = array();

        foreach ($data as $row) {

            $match = true;

            foreach ($filters as $field=>$value) {

                if ($field == 'subDivision') {
                    $match = $this->_matchSubdivisionTree($row[$field], $value);
                } else if (stripos($row[$field], $value) === false) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $filtered[] = $row;
            }
        }
        return $filtered;
    }

    /**
     * Match $value in given subdivisionName or a division higher in the heirarchy
     *
     * @param $subDivisionName Subdivision Name
     * @param $value           Value to search for
     * @return bool            true if match found, false otherwise.
     */
    private function _matchSubdivisionTree($subDivisionName, $value) {

        /*
         * Get companystructure with given subDivisionName
         */
        $subDivision = null;
        foreach( $this->companyStructure as $key=>$companyStructure ){
            if( $companyStructure['title'] == $subDivisionName ){
                $subDivision = $companyStructure;
            }
        }

        if( is_null($subDivision) ){
            return false;
        }

        /* Get full hierarchy above given subDivision */
        $hierarchy = $this->_getCompanyHierarchy($subDivision, $this->companyStructure);

        /* Match value with full company structure */
        if (stripos($hierarchy, $value) === false) {
            $match = false;
        } else {
            $match = true;
        }

        return $match;
    }


    /**
     * Get complete company hierarchy above given subdivision as a string
     *
     * @param $id
     * @return unknown_type
     */
    private function _getCompanyHierarchy($subDivision, &$companyStructure) {

        $parentId = $subDivision['parnt'];

        if( $parentId !== '0' ){
            $parent = $companyStructure['CompanyStructure_' . $parentId];
            $parentHierarchy = $this->_getCompanyHierarchy($parent, &$companyStructure);
            return $parentHierarchy . '->' . $subDivision['title'];
        }
        else{
            return $subDivision['title'];
        }
    }

    /**
     * Compares the results with the expected array.
     * Fails test if they differ
     *
     * @param Array $results Results array
     * @param Array $expected Expected array
     */
    private function _compareLists($results, $expected, $msg = '') {

        // compare size
        $this->assertEquals(count($expected), count($results), 'No of results not correct: ' . $msg);

        $size = count($expected);

        for ($i = 0; $i < $size; $i++ ) {

            $exp = $expected[$i];
            $actual = $results[$i];

            // compare some fields
            $this->assertEquals($exp['empNumber'], $actual['empNumber'], $msg);
            $this->assertEquals($exp['firstName'], $actual['firstName'], $msg);
            $this->assertEquals($exp['lastName'], $actual['lastName'], $msg);
            $this->assertEquals($exp['middleName'], $actual['middleName'], $msg);
            $this->assertEquals($exp['employeeId'], $actual['employeeId'], $msg);
            $empStatus = $actual['employeeStatus'];
            if (!empty($empStatus)) {
                $this->assertEquals($exp['employeeStatus'], $empStatus->getName(), $msg);
            } else {
                $this->assertNull($exp['employeeStatus'], $msg);
            }
            $jobTitle = $actual['jobTitle'];
            if (!empty($jobTitle)) {
                $this->assertEquals($exp['jobTitle'], $jobTitle->getName(), $msg);
            } else {
                $this->assertNull($exp['jobTitle'], $msg);
            }

        }
    }

    /**
     * Returns count of employees with given ids
     * @param array $ids Array of ids (empNumbers)
     * @return int count
     */
    private function _countEmployees(array $ids) {
        $query = Doctrine_Query::create()
                                   ->from('Employee e')
                                   ->whereIn('e.empNumber', $ids);
        return $query->count();
    }
    /**
     * Parses the employees from the test fixture in a format suitable for sorting
     * by different fields, to make it easier to verify return values during testing
     */
    private function _getEmployees() {
        $fixture = sfYaml::load(sfConfig::get('sf_test_dir') . self::DATA_FIXTURE);

        $employees = $fixture['Employee'];
        $empStatuses = $fixture['EmployeeStatus'];
        $jobTitles = $fixture['JobTitle'];
        $companyStructure = $fixture['CompanyStructure'];
        $reportTo = $fixture['ReportTo'];

        $this->assertTrue(count($employees) >= 10, "At least 10 employees expected in data fixture");

        // change relation fields to actually contain the data to be sorted by
        // Note that we are getting $employee by reference since we need to change it.
        foreach ($employees as & $employee) {

            $employee['jobTitle'] = $jobTitles[$employee['jobTitle']]['name'];

            $employee['employeeStatus'] = $empStatuses[$employee['employeeStatus']]['name'];
            $employee['subDivision'] = $companyStructure[$employee['subDivision']]['title'];
            $employee['fullName'] = $this->_getFullName($employee['firstName'],
                                                    $employee['middleName'], $employee['lastName']);
            $empNumber = $employee['empNumber'];


            // Look for supervisors
            $supervisors = array();
            foreach ($reportTo as $rel) {

                if ($rel['subordinateId'] == $empNumber) {

                    $supervisorId = $rel['supervisorId'];
                    $supervisor = $employees['Employee_' . $supervisorId];

                    // These trims are to handle empty names and avoid extra spaces between names.
                    $supervisorName = trim(trim($supervisor['firstName']) . " " .
                                           trim($supervisor['lastName']));
                    $supervisors[] = $supervisorName;
                }
            }

            $employee['supervisor'] = implode(',', $supervisors);
        }

        // Set up member variables
        $this->employees = array_values($employees);
        $this->empStatuses = $empStatuses;
        $this->companyStructure = $companyStructure;
        $this->jobTitles = $jobTitles;
        $this->reportTo = $reportTo;
    }

    /**
     * Get the formatted full name by joining the first, middle and last names
     * @param String $firstName First Name
     * @param String $middleName Middle Name
     * @param String $lastName Last Name
     * @return String Full name
     */
    private function _getFullName($firstName, $middleName, $lastName) {
        $fullName = trim($firstName) . " " . trim($middleName);
	    $fullName = trim( trim($fullName) . " " . trim($lastName) );
	    return $fullName;
    }

}