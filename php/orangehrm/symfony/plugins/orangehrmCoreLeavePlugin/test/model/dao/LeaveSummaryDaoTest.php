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

    private function _connectToMySql() {

        $configuration = ProjectConfiguration::getApplicationConfiguration(SF_APP_NAME, SF_ENV, true);

        $databases = include($configuration->getConfigCache()->checkConfig('config/databases.yml'));
        $this->assertTrue(isset($databases['doctrine']), "database not configured for test env in databases.yml");
        $doctrineConfig = $databases['doctrine'];

        $dsn = $doctrineConfig->getParameter('dsn');
        $dsnSplit = explode(';dbname=', $dsn);

        $this->assertTrue(count($dsnSplit) >= 2, 'DSN not well formed');
        $host = str_replace('mysql:host=', '', $dsnSplit[0]);
        $dbName = $dsnSplit[1];

        $user = $doctrineConfig->getParameter('username');
        $password = $doctrineConfig->getParameter('password');

        $link = mysql_connect($host, $user, $password);
        $this->assertTrue($link !== FALSE, 'mysql_connect failed!');

        $result = mysql_select_db($dbName);
        $this->assertTrue($result, 'mysql_select_db failed!');

    }

    public function testFetchRawLeaveSummaryRecordsAllRecords() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(10, mysql_num_rows($result));
        $this->assertEquals(10, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(3, $rows[5]['empNumber']);
        $this->assertEquals('Tyler', $rows[5]['empFirstName']);
        $this->assertEquals('Abraham', $rows[5]['empLastName']);
        $this->assertEquals('LTY002', $rows[5]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[5]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsLeaveType() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = 'LTY001';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(5, mysql_num_rows($result));
        $this->assertEquals(5, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

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

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(2, mysql_num_rows($result));
        $this->assertEquals(2, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

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

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(4, mysql_num_rows($result));
        $this->assertEquals(4, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(2, $rows[3]['empNumber']);
        $this->assertEquals('Ashley', $rows[3]['empFirstName']);
        $this->assertEquals('Abel', $rows[3]['empLastName']);
        $this->assertEquals('LTY002', $rows[3]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[3]['leaveTypeName']);

    }

    public function testFetchRawLeaveSummaryRecordsJobTitle() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = 'JOB001';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(4, mysql_num_rows($result));
        $this->assertEquals(4, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(5, $rows[3]['empNumber']);
        $this->assertEquals('James', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahamson', $rows[3]['empLastName']);
        $this->assertEquals('LTY002', $rows[3]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[3]['leaveTypeName']);

    }


    public function testFetchRawLeaveSummaryRecordsLocation() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = 'LOC001';
        $clues['subordinates'] = '';

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(4, mysql_num_rows($result));
        $this->assertEquals(4, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        $this->assertEquals(1, $rows[0]['empNumber']);
        $this->assertEquals('Kayla', $rows[0]['empFirstName']);
        $this->assertEquals('Abbey', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(4, $rows[3]['empNumber']);
        $this->assertEquals('Landon', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahams', $rows[3]['empLastName']);
        $this->assertEquals('LTY002', $rows[3]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[3]['leaveTypeName']);

    }


    public function testFetchRawLeaveSummaryRecordsSubordinates() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = 'Supervisor';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = array(2, 5);

        $this->_connectToMySql();
        $result = $this->leaveSummaryDao->fetchRawLeaveSummaryRecords($clues);

        /* Checking records count */
        $this->assertEquals(4, mysql_num_rows($result));
        $this->assertEquals(4, $this->leaveSummaryDao->fetchRawLeaveSummaryRecordsCount($clues));

        /* Checking values and order */

        while ($row = mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        $this->assertEquals(2, $rows[0]['empNumber']);
        $this->assertEquals('Ashley', $rows[0]['empFirstName']);
        $this->assertEquals('Abel', $rows[0]['empLastName']);
        $this->assertEquals('LTY001', $rows[0]['leaveTypeId']);
        $this->assertEquals('Casual', $rows[0]['leaveTypeName']);

        $this->assertEquals(5, $rows[3]['empNumber']);
        $this->assertEquals('James', $rows[3]['empFirstName']);
        $this->assertEquals('Abrahamson', $rows[3]['empLastName']);
        $this->assertEquals('LTY002', $rows[3]['leaveTypeId']);
        $this->assertEquals('Medical', $rows[3]['leaveTypeName']);

    }







}























?>