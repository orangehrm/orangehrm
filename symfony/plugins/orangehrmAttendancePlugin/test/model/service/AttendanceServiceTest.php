<?php

/**
 *  @group Attendance
 */
class AttendanceServiceTest extends PHPUnit_Framework_Testcase {

    private $attendanceService;
    private $fixture;

    protected function setUp() {


        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAttendancePlugin/test/fixtures/AttendanceService.yml';
        $this->attendanceService = new AttendanceService();
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSetAttendanceDao() {

        $attendanceDao = new AttendanceDao();
        $this->attendanceService->setAttendanceDao($attendanceDao);

        $this->assertTrue($this->attendanceService->getAttendanceDao() instanceof AttendanceDao);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetAttendanceDao() {

        $this->assertTrue($this->attendanceService->getAttendanceDao() instanceof AttendanceDao);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSavePunchAction() {

        $attendanceRecords = TestDataService::loadObjectList('AttendanceRecord', $this->fixture, 'AttendanceRecord');
        $attendanceRecord = $attendanceRecords[0];

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('savePunchRecord'));

        $attendanceDaoMock->expects($this->once())
                ->method('savePunchRecord')
                ->with($attendanceRecord)
                ->will($this->returnValue($attendanceRecord));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $this->assertTrue($this->attendanceService->savePunchRecord($attendanceRecord) instanceof AttendanceRecord);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testLastPunchRecord() {

        $employeeId = 1;
        $actionableStateList = array(AttendanceRecord::STATE_PUNCHED_IN);

        $lastPunchRecord = TestDataService::fetchObject('AttendanceRecord', 2);
        $attendanceDaoMock = $this->getMock('AttendanceDao', array('getLastPunchRecord'));
        $attendanceDaoMock->expects($this->once())
                ->method('getLastPunchRecord')
                ->with($employeeId, $actionableStateList)
                ->will($this->returnValue($lastPunchRecord));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $retrievedPunchRecord = $this->attendanceService->getLastPunchRecord($employeeId, $actionableStateList);
        $this->assertTrue($retrievedPunchRecord instanceof AttendanceRecord);
        $this->assertEquals($lastPunchRecord, $retrievedPunchRecord);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetSavedConfiguration() {

        $workflow = "ATTENDANCE";
        $state = "CREATED";
        $role = "ESS USER";
        $action = "EDIT";
        $resultingState = "CREATED";

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('getSavedConfiguration'));
        $attendanceDaoMock->expects($this->once())
                ->method('getSavedConfiguration')
                ->with($workflow, $state, $role, $action, $resultingState)
                ->will($this->returnValue(true));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);

        $this->assertTrue($this->attendanceService->getSavedConfiguration($workflow, $state, $role, $action, $resultingState));
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testCheckForPunchOutOverLappingRecords() {

        $punchInTime = "2011-06-10 15:21:00";
        $punchOutTime = "2011-06-10 15:40:00";
        $employeeId = 5;
        $isValid = "0";
        $recordId = 121;

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('checkForPunchOutOverLappingRecords'));
        $attendanceDaoMock->expects($this->once())
                ->method('checkForPunchOutOverLappingRecords')
                ->with($punchInTime, $punchOutTime, $employeeId, $recordId)
                ->will($this->returnValue($isValid));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);

        $this->assertEquals($isValid, $this->attendanceService->checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId));
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testCheckForPunchInOverLappingRecords() {

        $punchInTime = "2011-04-03 5:10:00";
        $employeeId = "5";
        $isValid = "0";

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('checkForPunchInOverLappingRecords'));
        $attendanceDaoMock->expects($this->once())
                ->method('checkForPunchInOverLappingRecords')
                ->with($punchInTime, $employeeId)
                ->will($this->returnValue($isValid));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);

        $this->assertEquals($isValid, $this->attendanceService->checkForPunchInOverLappingRecords($punchInTime, $employeeId));
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetAttendanceRecord() {

        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', 11);
        $date = "2012-02-28 23:46:00";
        $employeeId = 5;


        $attendanceDaoMock = $this->getMock('AttendanceDao', array('getAttendanceRecord'));
        $attendanceDaoMock->expects($this->once())
                ->method('getAttendanceRecord')
                ->with($employeeId, $date)
                ->will($this->returnValue($attendanceRecord));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $record = $this->attendanceService->getAttendanceRecord($employeeId, $date);
        $this->assertEquals($attendanceRecord, $record);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testDeleteAttendanceRecords() {
        $attendanceRecordId = 4;
        $isDeleted = true;

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('deleteAttendanceRecords'));
        $attendanceDaoMock->expects($this->once())
                ->method('deleteAttendanceRecords')
                ->with($attendanceRecordId)
                ->will($this->returnValue($isDeleted));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $deleted = $this->attendanceService->deleteAttendanceRecords($attendanceRecordId);
        $this->assertTrue($deleted);

        $attendanceRecordId = 12344;
        $isDeleted = false;

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('deleteAttendanceRecords'));
        $attendanceDaoMock->expects($this->once())
                ->method('deleteAttendanceRecords')
                ->with($attendanceRecordId)
                ->will($this->returnValue($isDeleted));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $deleted = $this->attendanceService->deleteAttendanceRecords($attendanceRecordId);
        $this->assertFalse($deleted);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetAttendanceRecordById() {

        $attendanceRecordId = 4;
        $attendanceRecord = TestDataService::fetchObject('AttendanceRecord', $attendanceRecordId);

        $attendanceDaoMock = $this->getMock('AttendanceDao', array('getAttendanceRecordById'));
        $attendanceDaoMock->expects($this->once())
                ->method('getAttendanceRecordById')
                ->with($attendanceRecordId)
                ->will($this->returnValue($attendanceRecord));

        $this->attendanceService->setAttendanceDao($attendanceDaoMock);
        $retrievedAttendanceRecord = $this->attendanceService->getAttendanceRecordById($attendanceRecordId);
        $this->assertTrue($retrievedAttendanceRecord instanceof AttendanceRecord);
        $this->assertEquals($retrievedAttendanceRecord->getId(), $attendanceRecordId);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetTimezone() {
        $timezoneArray = $this->attendanceService->getTimezoneArray();

        $value = 0;
        $this->assertEquals('GMT', $this->attendanceService->getTimezone($value));

        $value = 5;
        $this->assertEquals('5.0', $this->attendanceService->getTimezone($value));

        $value = 11;
        $this->assertEquals('9.5', $this->attendanceService->getTimezone($value));

        $value = 16;
        $this->assertEquals('-10', $this->attendanceService->getTimezone($value));

        $value = 22;
        $this->assertEquals('-4.00', $this->attendanceService->getTimezone($value));

        $value = 25;
        $this->assertEquals('-2.00', $this->attendanceService->getTimezone($value));
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetTimezoneArray() {

        $timezoneArray = $this->attendanceService->getTimezoneArray();
        $this->assertNotnull($timezoneArray);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testGetLocalTimezone() {

        $offset = -12.00;
        $timezone = $this->attendanceService->getLocalTimezone($offset);
        $this->assertEquals('Kwajalein', $timezone);

        $offset = -4.50;
        $timezone = $this->attendanceService->getLocalTimezone($offset);
        $this->assertEquals('America/Caracas', $timezone);

        $offset = -1.0;
        $timezone = $this->attendanceService->getLocalTimezone($offset);
        $this->assertEquals('Atlantic/Azores', $timezone);

        $offset = 3.0;
        $timezone = $this->attendanceService->getLocalTimezone($offset);
        $this->assertEquals('Asia/Kuwait', $timezone);

        $offset = 6.0;
        $timezone = $this->attendanceService->getLocalTimezone($offset);
        $this->assertEquals('Asia/Dhaka', $timezone);
    }

    /**
     * @group orangehrmAttendancePlugin
     */
    public function testSearchAttendanceRecords() {
        $attendanceDaoMock = $this->getMock('AttendanceDao', array('searchAttendanceRecords'));
        $attendanceDaoMock->expects($this->once())
                ->method('searchAttendanceRecords')
                ->with($attendanceRecordId)
                ->will($this->returnValue(array("id"=>1)));
       
        $attendanceService = new AttendanceService();
        $attendanceService->setAttendanceDao($attendanceDaoMock);
        $this->assertEquals(1, sizeof($attendanceService->searchAttendanceRecords()));
    }

}