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
require_once 'PHPUnit/Framework.php';
/**
 * Leave Type rule service
 */
class LeaveRequestServiceTest extends PHPUnit_Framework_TestCase {

    protected $leaveRequestService;
    protected $fixture;

    /**
     * PHPUnit setup function
     */
    public function setup() {

        $this->leaveRequestService = new LeaveRequestService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveRequestService.yml';

    }

    /* Tests for saveLeaveRequest() */

    public function testSaveLeaveRequest() {

        $leaveRequestList = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'set1');
        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set2');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('saveLeaveRequest'));
        $leaveRequestDao->expects($this->once())
                ->method('saveLeaveRequest')
                ->with($leaveRequestList[0], $leaveList)
                ->will($this->returnValue(true));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertTrue($this->leaveRequestService->saveLeaveRequest($leaveRequestList[0], $leaveList));

    }

    /* Tests for getNumOfLeave() */

    public function testGetNumOfLeave() {

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('getNumOfLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('getNumOfLeave')
                ->with(1, 'LTY001')
                ->will($this->returnValue(10));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(10, $this->leaveRequestService->getNumOfLeave(1, 'LTY001'));

    }

    /* Tests for getNumOfAvaliableLeave() */

    public function testGetNumOfAvaliableLeave() {

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('getNumOfAvaliableLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('getNumOfAvaliableLeave')
                ->with(1, 'LTY002')
                ->will($this->returnValue(15));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(15, $this->leaveRequestService->getNumOfAvaliableLeave(1, 'LTY002'));

    }

    /* Tests for fetchLeaveRequest() */

    public function testFetchLeaveRequest() {

        $leaveRequestList = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'set1');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('fetchLeaveRequest'));
        $leaveRequestDao->expects($this->once())
                ->method('fetchLeaveRequest')
                ->with(1)
                ->will($this->returnValue($leaveRequestList[0]));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $returnedLeaveRequest = $this->leaveRequestService->fetchLeaveRequest(1);

        $this->assertTrue($returnedLeaveRequest instanceof LeaveRequest);
        $this->assertEquals(1, $leaveRequestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $leaveRequestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $leaveRequestList[0]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[0]->getEmpNumber());

    }

    /* Tests for searchLeave() */

    public function testSearchLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set2');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('fetchLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('fetchLeave')
                ->with(1)
                ->will($this->returnValue($leaveList));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $returnedLeaveList = $this->leaveRequestService->searchLeave(1);

        foreach ($returnedLeaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(2, count($returnedLeaveList));

        $this->assertEquals(1, $returnedLeaveList[0]->getLeaveId());
        $this->assertEquals('LTY001', $returnedLeaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $returnedLeaveList[0]->getEmployeeId());
        $this->assertEquals(1, $returnedLeaveList[0]->getLeaveRequestId());
        $this->assertEquals('2010-09-01', $returnedLeaveList[0]->getLeaveDate());
        $this->assertEquals(1, $returnedLeaveList[0]->getLeaveStatus());

        $this->assertEquals(2, $returnedLeaveList[1]->getLeaveId());
        $this->assertEquals('LTY001', $returnedLeaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $returnedLeaveList[1]->getEmployeeId());
        $this->assertEquals(1, $returnedLeaveList[1]->getLeaveRequestId());
        $this->assertEquals('2010-09-02', $returnedLeaveList[1]->getLeaveDate());
        $this->assertEquals(1, $returnedLeaveList[1]->getLeaveStatus());

    }

    /* Tests for getScheduledLeavesSum() */

    public function testGetScheduledLeavesSum() {

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('getScheduledLeavesSum'));
        $leaveRequestDao->expects($this->once())
                ->method('getScheduledLeavesSum')
                ->with(1, 'LTY001', 1)
                ->will($this->returnValue(8));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(8, $this->leaveRequestService->getScheduledLeavesSum(1, 'LTY001', 1));

    }

    /* Tests for getTakenLeaveSum() */

    public function testGetTakenLeaveSum() {

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('getTakenLeaveSum'));
        $leaveRequestDao->expects($this->once())
                ->method('getTakenLeaveSum')
                ->with(5, 'LTY002', 1)
                ->will($this->returnValue(2));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(2, $this->leaveRequestService->getTakenLeaveSum(5, 'LTY002', 1));

    }

    /* Tests for modifyOverlapLeaveRequest() */

    public function testModifyOverlapLeaveRequest() {

        $leaveRequestList = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'set3');
        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('modifyOverlapLeaveRequest'));
        $leaveRequestDao->expects($this->once())
                ->method('modifyOverlapLeaveRequest')
                ->with($leaveRequestList[0], $leaveList)
                ->will($this->returnValue(true));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertTrue($this->leaveRequestService->modifyOverlapLeaveRequest($leaveRequestList[0], $leaveList));

    }

    /* Tests for searchLeaveRequests() */

    public function testSearchLeaveRequests() {

        $searchParameters = new ParameterStubService();
        $dateRange = new DateRangeStubService();
        $searchParameters->setParameter('dateRange', $dateRange);

        $leaveRequestList = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'set1');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('searchLeaveRequests'));
        $leaveRequestDao->expects($this->once())
                ->method('searchLeaveRequests')
                ->with($searchParameters, 1)
                ->will($this->returnValue($leaveRequestList[0]));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $returnedLeaveRequest = $this->leaveRequestService->searchLeaveRequests($searchParameters, 1);

        $this->assertTrue($returnedLeaveRequest instanceof LeaveRequest);
        $this->assertEquals(1, $leaveRequestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $leaveRequestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $leaveRequestList[0]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[0]->getEmpNumber());

    }

    public function testGetEmployeeAllowedToApplyLeaveTypes() {
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set5');

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));
        $this->leaveRequestService->setLeaveTypeService($leaveTypeService);

        //mocking LeaveEntitlementService
        $leaveEntitlementService = $this->getMock('LeaveEntitlementService', array('getLeaveBalance'));
        $leaveEntitlementService->expects($this->any())
                ->method('getLeaveBalance')
                ->will($this->returnValue(2));
        $this->leaveRequestService->setLeaveEntitlementService($leaveEntitlementService);

        //mocking LeavePeriodService
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(1);
        $leavePeriodService = $this->getMock('LeavePeriodService', array('getCurrentLeavePeriod'));
        $leavePeriodService->expects($this->exactly(2))
                ->method('getCurrentLeavePeriod')
                ->will($this->returnValue($leavePeriod));
        $this->leaveRequestService->setLeavePeriodService($leavePeriodService);

        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $result = $this->leaveRequestService->getEmployeeAllowedToApplyLeaveTypes($employee);

        $this->assertTrue(is_array($result));
        foreach($result as $leaveType) {
            $this->assertTrue($leaveType instanceof LeaveType);
        }

        // Test handling of exceptions
        // Replace LeaveTypeService with mock that throws an exception
        $leaveTypeService = $this->getMock('LeaveTypeService', array('getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->throwException(new LeaveServiceException()));
        $this->leaveRequestService->setLeaveTypeService($leaveTypeService);

        try {
            $this->leaveRequestService->getEmployeeAllowedToApplyLeaveTypes($employee);
            $this->fail("Expected exception");
        } catch (LeaveServiceException $e) {
            // expected
        }

    }

    public function testGetOverlappingLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('getOverlappingLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('getOverlappingLeave')
                ->with('2010-02-03', '2010-02-05', 12)
                ->will($this->returnValue($leaveList));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $returnVal = $this->leaveRequestService->getOverlappingLeave('2010-02-03', '2010-02-05', 12);
        $this->assertTrue($returnVal == $leaveList);
    }

    public function testSaveLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');
        $leave = $leaveList[0];

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('saveLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('saveLeave')
                ->with($leave)
                ->will($this->returnValue(true));
        
        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $this->assertTrue($this->leaveRequestService->saveLeave($leave));
    }

    public function testReadLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');
        $leave = $leaveList[0];

        $leaveRequestDao = $this->getMock('LeaveRequestDao', array('readLeave'));
        $leaveRequestDao->expects($this->once())
                ->method('readLeave')
                ->with($leave->leave_id)
                ->will($this->returnValue($leave));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $leaveReturned = $this->leaveRequestService->readLeave($leave->leave_id);

        $this->assertEquals($leaveReturned->toArray(), $leave->toArray());

    }

    public function testGetLeaveNotificationService() {
        $service = $this->leaveRequestService->getLeaveNotificationService();
        $this->assertTrue(is_a($service, LeaveNotificationService) );
    }

    public function testGetLeaveEntitlementService() {
        $service = $this->leaveRequestService->getLeaveEntitlementService();
        $this->assertTrue(is_a($service, LeaveEntitlementService) );
    }

    public function testGetLeaveTypeService() {
        $service = $this->leaveRequestService->getLeaveTypeService();
        $this->assertTrue(is_a($service, LeaveTypeService) );
    }

    public function testGetLeavePeriodService() {
        $service = $this->leaveRequestService->getLeavePeriodService();
        $this->assertTrue(is_a($service, LeavePeriodService) );
    }


    //getLeaveRequestStatus

    //adjustLeavePeriodOverlapLeaves
}


class ParameterStubService {

    private $dateRange;
    private $statuses;
    private $employeeFilter;
    private $leavePeriod;
    private $leaveType;

    public function setParameter($property, $value) {
        $this->$property = $value;
    }

    public function getParameter($property) {
        return $this->$property;
    }

}

class DateRangeStubService {

    private $fromDate;
    private $toDate;

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    public function getToDate() {
        return $this->toDate;
    }

}