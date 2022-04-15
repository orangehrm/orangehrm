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

use OrangeHRM\Attendance\Dao\AttendanceDao;

/**
 *  @group Attendance
 */
class AttendanceDaoTest extends PHPUnit_Framework_TestCase {

    private $attendanceDao;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->attendanceDao = new AttendanceDao();
        TestDataService::truncateSpecificTables(array('AttendanceRecord','Employee'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmAttendancePlugin/test/fixtures/AttendanceDao.yml');
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSaveNewPunchRecord() {

        $punchRecord = new AttendanceRecord();

        $punchRecord->setState("PUNCHED IN");
        $punchRecord->setEmployeeId(2);
        $punchRecord->setPunchInUserTime('2011-05-27 12:10:00');
        $punchRecord->setPunchInTimeOffset('Asia/Calcutta');
        $punchRecord->setPunchInUtcTime('2011-05-27 5:10:23');

        $savedRecord = $this->attendanceDao->SavePunchRecord($punchRecord);

        $this->assertNotNull($savedRecord->getId());
        $this->assertEquals($savedRecord->getState(), "PUNCHED IN");
        $this->assertEquals($savedRecord->getPunchInUserTime(), '2011-05-27 12:10:00');
        $this->assertEquals($savedRecord->getPunchInTimeOffset(), 'Asia/Calcutta');
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSavePunchRecordForExistingPunchRecord() {

        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', 1);

        $attendanceRecord->setState("PUNCHED IN");

        $saveRecord = $this->attendanceDao->savePunchRecord($attendanceRecord);

        $this->assertEquals($saveRecord->getState(), 'PUNCHED IN');
        $this->assertEquals($saveRecord->getPunchInTimeOffset(), 'Asia/Calcutta');
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetLastPunchRecord() {

        $employeeId = 2;
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);

        $attendanceRecord = $this->attendanceDao->getLastPunchRecordByEmployeeNumberAndActionableList($employeeId, $actionableStatesList);

        $this->assertEquals($attendanceRecord->getId(), 2);
        $this->assertEquals($attendanceRecord->getEmployeeId(), $employeeId);
        $this->assertEquals($attendanceRecord->getPunchInTimeOffset(), 'Asia/Calcutta');
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetLastPunchRecordForNonExistingRecord() {

        $employeeId = 4;
        $actionableStatesList = array(PluginAttendanceRecord::STATE_PUNCHED_IN);

        $attendanceRecord = $this->attendanceDao->getLastPunchRecordByEmployeeNumberAndActionableList($employeeId, $actionableStatesList);

        $this->assertNull($attendanceRecord);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testCheckForPunchOutOverLappingRecords() {
        $punchInTime = "2011-06-10 15:21:00";
        $punchOutTime = "2011-06-10 15:40:00";
        $employeeId = 5;
        $recordId = 121;

        $records = $this->attendanceDao->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId);

        $this->assertEquals($records, 0);

        $punchInTime = "2011-06-10 15:21:00";
        $punchOutTime = "2011-06-10 15:50:00";
        $employeeId = 5;

        $records = $this->attendanceDao->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId);
        $this->assertEquals($records, 0);

        $punchInTime = "2011-06-10 15:21:00";
        $punchOutTime = "2011-06-10 15:50:00";
        $employeeId = 5;

        $records = $this->attendanceDao->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId);
        $this->assertEquals($records, 0);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testCheckForPunchInOverLappingRecords() {
        $punchInTime = "2011-04-03 15:21:00";
        $employeeId = 5;
        $records = $this->attendanceDao->checkForPunchInOverLappingRecords($punchInTime, $employeeId);
        $this->assertEquals($records, 0);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetSavedConfiguration() {

        $workflow = "ATTENDANCE";
        $state = "INITIAL";
        $role = "ESS USER";
        $action = "EDIT";
        $resultingState = "INITIAL";

        $RecordExist = $this->attendanceDao->hasSavedConfiguration($workflow, $state, $role, $action, $resultingState);

        $this->assertTrue($RecordExist);

        $workflow = "ATTENDANCE";
        $state = "PUNCHED OUT";
        $role = "ESS USER";
        $action = "EDIT";
        $resultingState = "PUNCHED OUT";

        $RecordExist = $this->attendanceDao->hasSavedConfiguration($workflow, $state, $role, $action, $resultingState);

        $this->assertFalse($RecordExist);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetAttendanceRecord() {
        $employeeId = 5;
        $date = "2011-12-12";

        $records = $this->attendanceDao->getAttendanceRecord($employeeId, $date);
        $firstRecord = $records[0];
        $secondRecord = $records[1];

        $this->assertEquals($firstRecord->getEmployeeId(), 5);
        $this->assertEquals($firstRecord->getPunchInUserTime(), "2011-12-12 15:26:00");
        $this->assertEquals($secondRecord->getEmployeeId(), 5);
        $this->assertEquals($secondRecord->getPunchInUserTime(), "2011-12-12 19:26:00");

        $employeeId = 5;
        $date = "2012-12-21";
        $records = $this->attendanceDao->getAttendanceRecord($employeeId, $date);

        $this->assertEquals($records[0]->getEmployeeId(), 5);
        $this->assertEquals($records[0]->getPunchInUserTime(), "2012-12-21 01:10:00");
        $this->assertEquals($records[0]->getPunchInTimeOffset(), -9);

        $employeeId = 5;
        $date = "2012-02-28";
        $records = $this->attendanceDao->getAttendanceRecord($employeeId, $date);

        $this->assertEquals($records[0]->getEmployeeId(), 5);
        $this->assertEquals($records[0]->getPunchInUserTime(), "2012-02-28 23:46:00");
        $this->assertEquals($records[0]->getPunchInTimeOffset(), 6.5);
        $this->assertEquals($records[0]->getPunchOutUserTime(), "2012-02-29 17:42:00");

        $employeeId = 5;
        $date = "2016-02-28";
        $records = $this->attendanceDao->getAttendanceRecord($employeeId, $date);
        $this->assertNull($records[0]);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testDeleteAttendanceRecords() {
        $attendanceRecordId = 4;
        $isDeleted = $this->attendanceDao->deleteAttendanceRecords($attendanceRecordId);

        $this->assertTrue($isDeleted);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetAttendanceRecordById() {
        $id = 5;
        $attendanceRecord = $this->attendanceDao->getAttendanceRecordById($id);
        $this->assertEquals(5, $attendanceRecord->getId());
        $this->assertEquals(5, $attendanceRecord->getEmployeeId());
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testCheckForPunchInOutOverLappingRecordsWhenEditing() {

        $punchInTime = "2012-02-27 23:10:00";
        $punchOutTime = "2012-02-28 23:15:00";
        $employeeId = 5;
        $recordId = 22;
        $isDeleted = $this->attendanceDao->checkForPunchInOutOverLappingRecordsWhenEditing($punchInTime, $punchOutTime, $employeeId, $recordId);
        $this->assertEquals(0, $isDeleted);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSearchAttendanceRecords1() {

        $attendanceRecords = $this->attendanceDao->searchAttendanceRecords(1);
        $this->assertEquals(1, sizeof($attendanceRecords));
    }

     /**
     * @group orangehrmAttendancePlugin
     */
    public function testSearchAttendanceRecords2() {

        $attendanceRecords = $this->attendanceDao->searchAttendanceRecords(5);
        $this->assertEquals(7, sizeof($attendanceRecords));
    }

     /**
     * @group orangehrmAttendancePlugin
     */
    public function testSearchAttendanceRecords3() {

        $attendanceRecords = $this->attendanceDao->searchAttendanceRecords(2, array(3));
        $this->assertEquals(0, sizeof($attendanceRecords));
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSearchAttendanceRecords4() {

        $attendanceRecords = $this->attendanceDao->searchAttendanceRecords(2, array(1));
        $this->assertEquals(1, sizeof($attendanceRecords));
    }

    public function testGetLatestPunchInRecordForNotPunchedInEmployee() {

        $employeeId = 3;

        $attendanceRecord = $this->attendanceDao->getLatestPunchInRecord($employeeId,PluginAttendanceRecord::STATE_PUNCHED_IN);
        $this->assertFalse($attendanceRecord);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetLatestPunchInRecordForNonExistingEmployee() {

        $employeeId = 1000;
        $attendanceRecord = $this->attendanceDao->getLatestPunchInRecord($employeeId,PluginAttendanceRecord::STATE_PUNCHED_IN);
        $this->assertFalse($attendanceRecord);
    }

    /**
     * @dataProvider dataProviderGetAttendanceRecordsByEmpNumbers
     * @param $empNumbers
     * @param $expectedCount
     * @param null $dateFrom
     * @param null $dateTo
     * @throws DaoException
     */
    public function testGetAttendanceRecordsByEmpNumbers($empNumbers,$expectedCount,$dateFrom = null, $dateTo = null) {
        $attendanceRecords = $this->attendanceDao->getAttendanceRecordsByEmpNumbers($empNumbers,$dateFrom,$dateTo);
        $this->assertEquals($expectedCount, count($attendanceRecords));
    }

    /**
     * @return Generator
     */
    public function dataProviderGetAttendanceRecordsByEmpNumbers()
    {
        yield [2, 1];
        yield [[2], 1];
        yield [[2, 5], 8];
        yield [[2, 5], 2, '2011-05-26', '2011-12-12'];
        yield [[2, 5], 3, '2011-04-20', '2011-12-12'];
    }

    public function testGetAttendanceRecordsBetweenTwoDaysForALLStates() {
        $employeeId = 5;
        $fromDate = '2011-12-12';
        $toDate = '2011-12-19';
        $state= "ALL";
        $attendanceRecord = $this->attendanceDao->getAttendanceRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$state);
        $this->assertEquals(2,count($attendanceRecord));
    }
    public function testGetAttendanceRecordsBetweenTwoDaysForPunchInState() {
        $employeeId = 5;
        $fromDate = '2011-04-01';
        $toDate = '2011-06-13';
        $state= "PUNCHED IN";
        $attendanceRecord = $this->attendanceDao->getAttendanceRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$state);
        $this->assertEquals(2,count($attendanceRecord));
    }
    public function testGetAttendanceRecordsBetweenTwoDaysForPunchOutState() {
        $employeeId = 5;
        $fromDate = '2011-04-01';
        $toDate = '2011-06-13';
        $state= "PUNCHED OUT";
        $attendanceRecord = $this->attendanceDao->getAttendanceRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$state);
        $this->assertEquals(1,count($attendanceRecord));
    }
    public function testGetAttendanceRecordsBetweenTwoDaysForEdgeDates() {
        $employeeId = 5;
        $fromDate = '2012-02-28 12:26:26';
        $toDate = '2012-12-21 23:26:26';
        $state= "ALL";
        $attendanceRecords = $this->attendanceDao->getAttendanceRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$state);
        $this->assertEquals(2,count($attendanceRecords));
    }
}
