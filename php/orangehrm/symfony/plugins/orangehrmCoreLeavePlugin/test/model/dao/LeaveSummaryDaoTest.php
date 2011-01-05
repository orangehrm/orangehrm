<?php
/*
 *
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

require_once  sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class LeaveSummaryDaoTest extends PHPUnit_Framework_TestCase {

    public $leaveSummaryDao ;

    protected function setUp() {

        $this->leaveSummaryDao = new LeaveSummaryDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveSummaryDao.yml');

    }

    public function testFetchRawLeaveSummaryRecordsAllRecords() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(15, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        $this->assertEquals(15, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(2, $rows[5]['empNumber']);
        $this->assertEquals('Ashley', $rows[5]['empFirstName']);
        $this->assertEquals('Abel', $rows[5]['empLastName']);
        $this->assertEquals('LTY003', $rows[5]['leaveTypeId']);
        $this->assertEquals('Company', $rows[5]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsLeaveType() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = 'LTY001';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(5, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        $this->assertEquals(5, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(5, $rows[4]['empNumber']);
        $this->assertEquals('James', $rows[4]['empFirstName']);
        $this->assertEquals('Abrahamson', $rows[4]['empLastName']);
        $this->assertEquals('LTY001', $rows[4]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[4]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsEmployeeId() {

        $clues['cmbEmpId'] = 1;
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(3, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }

        $this->assertEquals(3, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(1, $rows[1]['empNumber']);
        $this->assertEquals('Kayla', $rows[1]['empFirstName']);
        $this->assertEquals('Abbey', $rows[1]['empLastName']);
        $this->assertEquals('LTY002', $rows[1]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[1]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsSubDivision() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = 2;
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(6, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        $this->assertEquals(6, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(2, $rows[3]['empNumber']);
        $this->assertEquals('Ashley', $rows[3]['empFirstName']);
        $this->assertEquals('Abel', $rows[3]['empLastName']);
        $this->assertEquals('LTY001', $rows[3]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[3]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsJobTitle() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = 'JOB001';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(6, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        $this->assertEquals(6, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(5, $rows[3]['empNumber']);
        $this->assertEquals('James', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahamson', $rows[3]['empLastName']);
        $this->assertEquals('LTY001', $rows[3]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[3]['leaveTypeName']);

    }


    public function testFetchRawLeaveSummaryRecordsLocation() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = 'LOC001';
        $clues['subordinates'] = '';

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(6, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }
        $this->assertEquals(6, count($rows));

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(4, $rows[3]['empNumber']);
        $this->assertEquals('Landon', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahams', $rows[3]['empLastName']);
        $this->assertEquals('LTY001', $rows[3]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[3]['leaveTypeName']);

    }


    public function testFetchRawLeaveSummaryRecordsSubordinates() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = 'Supervisor';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = array(2, 5);

        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(6, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = $result->fetch()) {
            $rows[] = $row;
        }

        $this->assertEquals(6, count($rows));

        $this->assertEquals(2, $rows[0]['empNumber']);
        $this->assertEquals('Ashley', $rows[0]['empFirstName']);
        $this->assertEquals('Abel', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(5, $rows[3]['empNumber']);
        $this->assertEquals('James', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahamson', $rows[3]['empLastName']);
        $this->assertEquals('LTY001', $rows[3]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[3]['leaveTypeName']);

    }







}























?>