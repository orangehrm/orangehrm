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

/**
 * Leave Type rule service
 * @group Leave 
 */
class LeaveRequestServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LeaveRequestService
     */
    protected $leaveRequestService;
    protected $fixture;

    /**
     * PHPUnit setup function
     */
    public function setup() {

        $this->leaveRequestService = new LeaveRequestService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveRequestService.yml';

    }

    /* Tests for saveLeaveRequest() */

    public function testSaveLeaveRequest() {

        $leaveRequestList = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'set1');
        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set2');

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('saveLeaveRequest'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('saveLeaveRequest')
                ->with($leaveRequestList[0], $leaveList)
                ->will($this->returnValue(true));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $entitlements = array();
        $this->assertTrue($this->leaveRequestService->saveLeaveRequest($leaveRequestList[0], $leaveList, $entitlements));

    }

    /* Tests for getNumOfLeave() */

    public function testGetNumOfLeave() {

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getNumOfLeave'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('getNumOfLeave')
                ->with(1, 'LTY001')
                ->will($this->returnValue(10));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(10, $this->leaveRequestService->getNumOfLeave(1, 'LTY001'));

    }

    /* Tests for getNumOfAvaliableLeave() */

    public function testGetNumOfAvaliableLeave() {

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getNumOfAvaliableLeave'))
			->getMock();
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

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('fetchLeaveRequest'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('fetchLeaveRequest')
                ->with(1)
                ->will($this->returnValue($leaveRequestList[0]));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $returnedLeaveRequest = $this->leaveRequestService->fetchLeaveRequest(1);

        $this->assertTrue($returnedLeaveRequest instanceof LeaveRequest);
        $this->assertEquals(1, $returnedLeaveRequest->getLeaveTypeId());
        $this->assertEquals('2010-08-30', $returnedLeaveRequest->getDateApplied());
        $this->assertEquals(1, $returnedLeaveRequest->getEmpNumber());

    }

    /* Tests for searchLeave() */

    public function testSearchLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set2');

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('fetchLeave'))
			->getMock();
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

        $this->assertEquals(1, $returnedLeaveList[0]->getId());
        $this->assertEquals('LTY001', $returnedLeaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $returnedLeaveList[0]->getEmpNumber());
        $this->assertEquals(1, $returnedLeaveList[0]->getLeaveRequestId());
        $this->assertEquals('2010-09-01', $returnedLeaveList[0]->getDate());
        $this->assertEquals(1, $returnedLeaveList[0]->getStatus());

        $this->assertEquals(2, $returnedLeaveList[1]->getId());
        $this->assertEquals('LTY001', $returnedLeaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $returnedLeaveList[1]->getEmpNumber());
        $this->assertEquals(1, $returnedLeaveList[1]->getLeaveRequestId());
        $this->assertEquals('2010-09-02', $returnedLeaveList[1]->getDate());
        $this->assertEquals(1, $returnedLeaveList[1]->getStatus());

    }

    /* Tests for getScheduledLeavesSum() */

    public function testGetScheduledLeavesSum() {

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getScheduledLeavesSum'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('getScheduledLeavesSum')
                ->with(1, 'LTY001', 1)
                ->will($this->returnValue(8));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->assertEquals(8, $this->leaveRequestService->getScheduledLeavesSum(1, 'LTY001', 1));

    }

    /* Tests for getTakenLeaveSum() */

    public function testGetTakenLeaveSum() {

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getTakenLeaveSum'))
			->getMock();
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

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')->setMethods(array('modifyOverlapLeaveRequest'))->getMock();
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

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('searchLeaveRequests'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('searchLeaveRequests')
                ->with($searchParameters, 1)
                ->will($this->returnValue($leaveRequestList));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $returnedLeaveRequests = $this->leaveRequestService->searchLeaveRequests($searchParameters, 1);

        $this->assertTrue($returnedLeaveRequests[0] instanceof LeaveRequest);
        $this->assertEquals(1, $returnedLeaveRequests[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-30', $returnedLeaveRequests[0]->getDateApplied());
        $this->assertEquals(1, $returnedLeaveRequests[0]->getEmpNumber());

    }

    public function xtestGetEmployeeAllowedToApplyLeaveTypes() {
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set5');

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMockBuilder('LeaveTypeService')
			->setMethods( array('getLeaveTypeList'))
			->getMock();
        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));
        $this->leaveRequestService->setLeaveTypeService($leaveTypeService);

        //mocking LeaveEntitlementService
        $leaveEntitlementService = $this->getMockBuilder('LeaveEntitlementService')
			->setMethods( array('getLeaveBalance'))
			->getMock();
        $leaveEntitlementService->expects($this->any())
                ->method('getLeaveBalance')
                ->will($this->returnValue(2));
        $this->leaveRequestService->setLeaveEntitlementService($leaveEntitlementService);

        //mocking LeavePeriodService
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(1);
        $leavePeriodService = $this->getMockBuilder('LeavePeriodService')
			->setMethods( array('getCurrentLeavePeriod'))
			->getMock();
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
        $leaveTypeService = $this->getMockBuilder('LeaveTypeService')
			->setMethods( array('getLeaveTypeList'))
			->getMock();
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

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getOverlappingLeave'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('getOverlappingLeave')
                ->with('2010-02-03', '2010-02-05', 12, '00:00:00','00:00:00', 8)
                ->will($this->returnValue($leaveList));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $returnVal = $this->leaveRequestService->getOverlappingLeave('2010-02-03', '2010-02-05', 12, '00:00:00','00:00:00', 8);
        $this->assertTrue($returnVal == $leaveList);
    }
    

    public function testSaveLeave() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');
        $leave = $leaveList[0];

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('saveLeave'))
			->getMock();
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

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('readLeave'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('readLeave')
                ->with($leave->getId())
                ->will($this->returnValue($leave));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $leaveReturned = $this->leaveRequestService->readLeave($leave->getId());

        $this->assertEquals($leaveReturned->toArray(), $leave->toArray());

    }

    public function xtestGetLeaveNotificationService() {
        $service = $this->leaveRequestService->getLeaveNotificationService();
        $this->assertTrue(is_a($service, 'LeaveNotificationService') );
    }
    public function testGetLeaveEntitlementService() {
        $service = $this->leaveRequestService->getLeaveEntitlementService();
        $this->assertTrue(is_a($service, 'LeaveEntitlementService') );
    }

    public function testGetLeaveTypeService() {
        $service = $this->leaveRequestService->getLeaveTypeService();
        $this->assertTrue(is_a($service, 'LeaveTypeService') );
    }

    public function testGetLeavePeriodService() {
        $service = $this->leaveRequestService->getLeavePeriodService();
        $this->assertTrue(is_a($service, 'LeavePeriodService') );
    }

    public function testGetHolidayService() {
        $service = $this->leaveRequestService->getHolidayService();
        $this->assertTrue(is_a($service, 'HolidayService') );
    }

    public function testGetLeaveRequestStatus() {

        $leaveDate = '2010-03-29';
        $holidayService = $this->getMockBuilder('HolidayService')
			->setMethods( array('readHolidayByDate'))
			->getMock();
        $holidayService->expects($this->once())
                ->method('readHolidayByDate')
                ->with($leaveDate)
                ->will($this->returnValue(null));

        $this->leaveRequestService->setHolidayService($holidayService);
        $status = $this->leaveRequestService->getLeaveRequestStatus($leaveDate);

        $this->assertEquals($status, Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);

        $holidayService = $this->getMockBuilder('HolidayService')
			->setMethods( array('readHolidayByDate'))
			->getMock();
        $holidayService->expects($this->once())
                ->method('readHolidayByDate')
                ->with($leaveDate)
                ->will($this->returnValue(new Holiday()));

        $status = $this->leaveRequestService->setHolidayService($holidayService);
        $status = $this->leaveRequestService->getLeaveRequestStatus($leaveDate);

        $this->assertEquals($status, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);

        // Test exception case
        $holidayService = $this->getMockBuilder('HolidayService')
			->setMethods( array('readHolidayByDate'))
			->getMock();
        $holidayService->expects($this->once())
                ->method('readHolidayByDate')
                ->with($leaveDate)
                ->will($this->throwException(new LeaveServiceException()));

        $status = $this->leaveRequestService->setHolidayService($holidayService);

        try {
            $status = $this->leaveRequestService->getLeaveRequestStatus($leaveDate);
            $this->fail("Expected exception");
        } catch (LeaveServiceException $e) {
            // Expected
        }

    }

    public function xtestAdjustLeavePeriodOverlapLeaves() {
        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');

        $leavePeriod = new LeavePeriod();
        $leavePeriod->setLeavePeriodId(11);
        $leavePeriod->setStartDate("2008-01-31");
        $leavePeriod->setEndDate("2009-01-31");

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
                        ->setMethods( array('getLeavePeriodOverlapLeaves','fetchLeave','modifyOverlapLeaveRequest'))
                        ->getMock();
        
        $leave        = $leaveList[0];
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeaveRequestId($leave->getLeaveRequestId());
        $leave->setLeaveRequest($leaveRequest);
        
        $leaveRequestDao->expects($this->once())
                ->method('getLeavePeriodOverlapLeaves')
                ->with($leavePeriod)
                ->will($this->returnValue(array($leave)));

        $leaveRequestDao->expects($this->once())
                ->method('fetchLeave')
                ->with($leave->getLeaveRequestId())
                ->will($this->returnValue($leaveList));

        $leaveRequestDao->expects($this->once())
                ->method('modifyOverlapLeaveRequest')
                ->with($leave->getLeaveRequest(), $leaveList, $leavePeriod);

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $this->leaveRequestService->adjustLeavePeriodOverlapLeaves($leavePeriod);


    }

    public function xtestIsEmployeeHavingLeaveBalance() {

        $empId = 1;
        $leaveTypeId = "LT001";
        $leaveRequest = "";
        $leavePeriodId = 11;
        $dateApplied = '2010-09-11';
        $applyDays = 2;
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setDateApplied($dateApplied);
        $leaveRequest->setLeavePeriodId($leavePeriodId);

        $entitledDays = 5;
        $availableLeave = 3;
        $leaveEntitlement = new EmployeeLeaveEntitlement();
        $leaveEntitlement->setLeaveBroughtForward(2);
        $leaveBalance = 22;
        $currentLeavePeriod = new LeavePeriod();
        $currentLeavePeriod->setStartDate('2010-01-01');
        $currentLeavePeriod->setEndDate('2010-12-31');
        
        $nextLeavePeriod = new LeavePeriod();
        $nextLeavePeriod->setStartDate('2011-01-01');
        $nextLeavePeriod->setEndDate('2011-12-31');

        $leaveEntitlementService = $this->getMockBuilder('OldLeaveEntitlementService')
                                 ->setMethods( array('getEmployeeLeaveEntitlementDays','readEmployeeLeaveEntitlement','getLeaveBalance'))
                                 ->getMock();

        $leaveEntitlementService->expects($this->once())
                ->method('getEmployeeLeaveEntitlementDays')
                ->with($empId, $leaveTypeId, $leavePeriodId)
                ->will($this->returnValue($entitledDays));

        $leaveEntitlementService->expects($this->once())
                ->method('readEmployeeLeaveEntitlement')
                ->with($empId, $leaveTypeId, $leavePeriodId)
                ->will($this->returnValue($leaveEntitlement));

        $leaveEntitlementService->expects($this->exactly(2))
                ->method('getLeaveBalance')
                //->with($empId, $leaveTypeId, $leavePeriodId) not checking parameters since called twice
                ->will($this->returnValue($leaveBalance));


        $this->leaveRequestService->setLeaveEntitlementService($leaveEntitlementService);

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getNumOfAvaliableLeave'))
			->getMock();

        $leaveRequestDao->expects($this->once())
                ->method('getNumOfAvaliableLeave')
                ->with($empId, $leaveTypeId)
                ->will($this->returnValue($availableLeave));
        
        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $leavePeriodService = $this->getMockBuilder('LeavePeriodService')
                            ->setMethods( array('getLeavePeriod','createNextLeavePeriod','getCurrentLeavePeriod'))
                            ->getMock();

        $leavePeriodService->expects($this->once())
                ->method('getLeavePeriod')
                ->with(strtotime($leaveRequest->getDateApplied()))
                ->will($this->returnValue($currentLeavePeriod));

        $leavePeriodService->expects($this->once())
                ->method('createNextLeavePeriod')
                ->with(date('Y-m-d', strtotime('2010-09-13')))
                ->will($this->returnValue($nextLeavePeriod));

        $leavePeriodService->expects($this->once())
                ->method('getCurrentLeavePeriod')
                ->will($this->returnValue($currentLeavePeriod));

        $this->leaveRequestService->setLeavePeriodService($leavePeriodService);


        $retVal = $this->leaveRequestService->isEmployeeHavingLeaveBalance($empId, $leaveTypeId, $leaveRequest, $applyDays);
        $this->assertTrue($retVal);
    }


    public function xtestIsEmployeeHavingLeaveBalanceErrorCases() {

        $empId = 1;
        $leaveTypeId = "LT001";
        $leaveRequest = "";
        $leavePeriodId = 11;
        $dateApplied = '2010-09-11';
        $applyDays = 2;
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setDateApplied($dateApplied);
        $leaveRequest->setLeavePeriodId($leavePeriodId);

        $entitledDays = 5;
        $availableLeave = 3;
        $leaveEntitlement = new EmployeeLeaveEntitlement();
        $leaveEntitlement->setLeaveBroughtForward(2);
        $leaveBalance = 0;
        $currentLeavePeriod = new LeavePeriod();
        $currentLeavePeriod->setStartDate('2010-01-01');
        $currentLeavePeriod->setEndDate('2010-12-31');

        $nextLeavePeriod = new LeavePeriod();
        $nextLeavePeriod->setStartDate('2011-01-01');
        $nextLeavePeriod->setEndDate('2011-12-31');

        $leaveEntitlementService = $this->getMockBuilder('OldLeaveEntitlementService')
                                ->setMethods( array('getEmployeeLeaveEntitlementDays','readEmployeeLeaveEntitlement','getLeaveBalance'))
                                ->getMock();

        $leaveEntitlementService->expects($this->any())
                ->method('getEmployeeLeaveEntitlementDays')
                ->with($empId, $leaveTypeId, $leavePeriodId)
                ->will($this->returnValue($entitledDays));

        $leaveEntitlementService->expects($this->any())
                ->method('readEmployeeLeaveEntitlement')
                ->with($empId, $leaveTypeId, $leavePeriodId)
                ->will($this->returnValue($leaveEntitlement));

        $leaveEntitlementService->expects($this->any())
                ->method('getLeaveBalance')
                //->with($empId, $leaveTypeId, $leavePeriodId) not checking parameters since called twice
                ->will($this->returnValue($leaveBalance));


        $this->leaveRequestService->setLeaveEntitlementService($leaveEntitlementService);

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getNumOfAvaliableLeave'))
			->getMock();

        $leaveRequestDao->expects($this->any())
                ->method('getNumOfAvaliableLeave')
                ->with($empId, $leaveTypeId)
                ->will($this->returnValue($availableLeave));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $leavePeriodService = $this->getMock('LeavePeriodService',
                array('getLeavePeriod',
                      'createNextLeavePeriod',
                      'getCurrentLeavePeriod',
                    ));

        $leavePeriodService->expects($this->any())
                ->method('getLeavePeriod')
                ->with(strtotime($leaveRequest->getDateApplied()))
                ->will($this->returnValue($currentLeavePeriod));

        $leavePeriodService->expects($this->any())
                ->method('createNextLeavePeriod')
                ->with(date('Y-m-d', strtotime('2010-09-13')))
                ->will($this->returnValue($nextLeavePeriod));

        $leavePeriodService->expects($this->any())
                ->method('getCurrentLeavePeriod')
                ->will($this->returnValue($currentLeavePeriod));

        $this->leaveRequestService->setLeavePeriodService($leavePeriodService);

        // Trigger leave balance exceeded
        try {
            $retVal = $this->leaveRequestService->isEmployeeHavingLeaveBalance($empId, $leaveTypeId, $leaveRequest, $applyDays);
            $this->fail("Exception expected");
        } catch (LeaveServiceException $e) {

            // expected - check code thrown in LeaveRequestService
        }
        
    }
        
        
       public function testGetTotalLeaveDuration() {

        $leaveList = TestDataService::loadObjectList('Leave', $this->fixture, 'set4');

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getTotalLeaveDuration'))
			->getMock();
        $leaveRequestDao->expects($this->once())
                ->method('getTotalLeaveDuration')
                ->will($this->returnValue(10));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);
        $returnVal = $this->leaveRequestService->getTotalLeaveDuration('2010-02-03', '2010-02-05', 12, '00:00:00','00:00:00', 8);

        $this->assertEquals(10, $returnVal);
      }


    public function xtestChangeLeaveStatus() {

        $leave = new Leave();
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setId(1);
        
        $changes = array(1 =>'APPROVE',
                         21 => 'REJECT',
                         31 => 'CANCEL');
        
        $changeType = 'change_leave_request';

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('fetchLeave'))
			->getMock();

        $leaveRequestDao->expects($this->any())
                ->method('fetchLeave')
                ->will($this->returnValue(array($leave)));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $mockLeaveStateManager = new MockLeaveStateManager();
        $this->leaveRequestService->setLeaveStateManager($mockLeaveStateManager);

        $leaveNotificationService = $this->getMockBuilder('LeaveNotificationService')
			->setMethods( array('approve', 'cancel', 'reject', 'cancelEmployee'))
			->getMock();
        $this->leaveRequestService->setLeaveNotificationService($leaveNotificationService);

        $this->leaveRequestService->changeLeaveStatus($changes, $changeType);
    }

    public function xtestChangeLeaveStatusForLeave() {
        
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setId(1);

        $leave = new Leave();
        $leave->setLeaveRequest($leaveRequest);

        $changes = array(1 =>'APPROVE',
                         21 => 'REJECT',
                         31 => 'CANCEL');

        $changeType = 'change_leave';

        $leaveRequestDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getLeaveById'))
			->getMock();

        $leaveRequestDao->expects($this->any())
                ->method('getLeaveById')
                ->will($this->returnValue($leave));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDao);

        $mockLeaveStateManager = new MockLeaveStateManager();
        $this->leaveRequestService->setLeaveStateManager($mockLeaveStateManager);

        $leaveNotificationService = $this->getMockBuilder('LeaveNotificationService')
			->setMethods( array('approve', 'cancel', 'reject', 'cancelEmployee'))
			->getMock();
        $this->leaveRequestService->setLeaveNotificationService($leaveNotificationService);

        $this->leaveRequestService->changeLeaveStatus($changes, $changeType);
    }

    public function testChangeLeaveStatusErrors() {

        // 1. Call with empty changes list
        try {
            $retVal = $this->leaveRequestService->changeLeaveStatus(null, 'change_leave_request');
            $this->fail("Exception expected");
        } catch (LeaveServiceException $e) {
            // expected - check code thrown in LeaveRequestService
        }
        
    }
    
     /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetLeaveRequestSearchResultAsArray() {

        $mockDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getLeaveRequestSearchResultAsArray'))
			->getMock();
        $mockDao->expects($this->any())
                ->method('getLeaveRequestSearchResultAsArray')
                ->will($this->returnValue(array('em_fist_name'=>'employee1')));

        $service = new LeaveRequestService();
        $service->setLeaveRequestDao($mockDao);

        $this->assertEquals(1, sizeof($service->getLeaveRequestSearchResultAsArray(array())));
      
    }
    
    
     /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetDetailedLeaveRequestSearchResultAsArray() {

        $mockDao = $this->getMockBuilder('LeaveRequestDao')
			->setMethods( array('getDetailedLeaveRequestSearchResultAsArray'))
			->getMock();
        $mockDao->expects($this->any())
                ->method('getDetailedLeaveRequestSearchResultAsArray')
                ->will($this->returnValue(array('em_fist_name'=>'employee1')));

        $service = new LeaveRequestService();
        $service->setLeaveRequestDao($mockDao);

        $this->assertEquals(1, sizeof($service->getDetailedLeaveRequestSearchResultAsArray(array())));
      
    }    
    
    public function testGetLeaveRequestActions() {
        $loggedInEmpNumber = 4;
        
        $approveAction = new WorkflowStateMachine();
        $approveAction->fromArray(array('id' => 2, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'APPROVE',
            'resulting_state' => 'SCHEDULED','roles_to_notify' => '','priority' => 0));        
        $cancelAction = new WorkflowStateMachine();
        $cancelAction->fromArray(array('id' => 3, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'CANCEL',
            'resulting_state' => 'CANCELLED','roles_to_notify' => '','priority' => 0));        
        $rejectAction = new WorkflowStateMachine();
        $rejectAction->fromArray(array('id' => 5, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'REJECT',
            'resulting_state' => 'REJECTED','roles_to_notify' => '','priority' => 0));        
        
        $actions = array($approveAction, $cancelAction, $rejectAction);
        
        $leave = $this->getMockBuilder('LeaveRequest')->setMethods(array('isStatusDiffer', 'getEmpNumber', 'getLeaveStatusId'))->getMock();
        $leave->expects($this->once())
              ->method('isStatusDiffer')
              ->will($this->returnValue(false));
        $leave->expects($this->once())
              ->method('getEmpNumber')
              ->will($this->returnValue(5));
        $leave->expects($this->once())
              ->method('getLeaveStatusId')
              ->will($this->returnValue(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL));        
        
        $userManager = $this->getMockBuilder('BasicUserRoleManager')->setMethods(array('getAllowedActions'))->getMock();
        $userManager->expects($this->any())
                    ->method('getAllowedActions')
                    ->with(WorkflowStateMachine::FLOW_LEAVE, 
                           Leave::getTextForLeaveStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL), 
                           array(), array())
                    ->will($this->returnValue($actions));
        
        $this->leaveRequestService->setUserRoleManager($userManager);
        $result = $this->leaveRequestService->getLeaveRequestActions($leave, $loggedInEmpNumber);
        $this->verifyLeaveActions($actions, $result);          
    }
    
    public function testGetLeaveRequestActionsESS() {
        $loggedInEmpNumber = 4;
        
        $cancelAction = new WorkflowStateMachine();
        $cancelAction->fromArray(array('id' => 3, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'CANCEL',
            'resulting_state' => 'CANCELLED','roles_to_notify' => '','priority' => 0));           
        $actions = array($cancelAction);
        
        $leave = $this->getMockBuilder('LeaveRequest')->setMethods(array('isStatusDiffer', 'getEmpNumber', 'getLeaveStatusId'))->getMock();
        $leave->expects($this->once())
              ->method('isStatusDiffer')
              ->will($this->returnValue(false));
        $leave->expects($this->once())
              ->method('getEmpNumber')
              ->will($this->returnValue($loggedInEmpNumber));
        $leave->expects($this->once())
              ->method('getLeaveStatusId')
              ->will($this->returnValue(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL));        
        
        $userManager = $this->getMockBuilder('BasicUserRoleManager')->setMethods(array('getAllowedActions'))->getMock();
        $userManager->expects($this->any())
                    ->method('getAllowedActions')
                    ->with(WorkflowStateMachine::FLOW_LEAVE, 
                           Leave::getTextForLeaveStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL), 
                           array(), array('ESS'))
                    ->will($this->returnValue($actions));
        
        $this->leaveRequestService->setUserRoleManager($userManager);
        $result = $this->leaveRequestService->getLeaveRequestActions($leave, $loggedInEmpNumber);
        $this->verifyLeaveActions($actions, $result);          
    }
    
    public function testGetLeaveRequestActionsStatusDiffer() {
        $loggedInEmpNumber = 4;

        $leave = $this->getMockBuilder('LeaveRequest')
			->setMethods( array('isStatusDiffer'))
			->getMock();
        $leave->expects($this->once())
              ->method('isStatusDiffer')
              ->will($this->returnValue(true));
        
        $result = $this->leaveRequestService->getLeaveRequestActions($leave, $loggedInEmpNumber);
        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));
    }    
    
    public function testGetLeaveActionsESS() {
        $loggedInEmpNumber = 4;
        $leave = new Leave();
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
        $leave->setEmpNumber($loggedInEmpNumber);
        
        $cancelAction = new WorkflowStateMachine();
        $cancelAction->fromArray(array('id' => 3, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'CANCEL',
            'resulting_state' => 'CANCELLED','roles_to_notify' => '','priority' => 0));           
        $actions = array($cancelAction);
        
        $userManager = $this->getMockBuilder('BasicUserRoleManager')
			->setMethods( array('getAllowedActions'))
			->getMock();
        $userManager->expects($this->any())
                    ->method('getAllowedActions')
                    ->with(WorkflowStateMachine::FLOW_LEAVE, $leave->getTextLeaveStatus(), 
                            array(), array('ESS'))
                    ->will($this->returnValue($actions));
        
        $this->leaveRequestService->setUserRoleManager($userManager);
        $result = $this->leaveRequestService->getLeaveActions($leave, $loggedInEmpNumber);
        $this->verifyLeaveActions($actions, $result);         
    }
    public function testGetLeaveActions() {
        $loggedInEmpNumber = 4;
        $leave = new Leave();
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
        $leave->setEmpNumber(5);
        
        $approveAction = new WorkflowStateMachine();
        $approveAction->fromArray(array('id' => 2, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'APPROVE',
            'resulting_state' => 'SCHEDULED','roles_to_notify' => '','priority' => 0));        
        $cancelAction = new WorkflowStateMachine();
        $cancelAction->fromArray(array('id' => 3, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'CANCEL',
            'resulting_state' => 'CANCELLED','roles_to_notify' => '','priority' => 0));        
        $rejectAction = new WorkflowStateMachine();
        $rejectAction->fromArray(array('id' => 5, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL','role' => 'ADMIN', 'action' => 'REJECT',
            'resulting_state' => 'REJECTED','roles_to_notify' => '','priority' => 0));        
        
        $actions = array($approveAction, $cancelAction, $rejectAction);
        
        $userManager = $this->getMockBuilder('BasicUserRoleManager')
			->setMethods( array('getAllowedActions'))
			->getMock();
        $userManager->expects($this->any())
                    ->method('getAllowedActions')
                    ->with(WorkflowStateMachine::FLOW_LEAVE, $leave->getTextLeaveStatus(), 
                            array(), array())
                    ->will($this->returnValue($actions));
        
        $this->leaveRequestService->setUserRoleManager($userManager);
        $result = $this->leaveRequestService->getLeaveActions($leave, $loggedInEmpNumber);
        $this->verifyLeaveActions($actions, $result);        
    }   
    
    protected function verifyLeaveActions($actions, $result) {
        $this->assertEquals(count($actions), count($result));
        
        foreach ($actions as $action) {
            $found = false;

            foreach ($result as $id => $actionName) {

                if ($action->getId() == $id &&
                        ucfirst(strtolower($action->getAction())) == $actionName) {
                    $found = true;
                    break;
                }                
            }
            $this->assertTrue($found);
        }
    }

    public function testChangeLeaveRequestStatus() {
        $leaveRequestService = $this->getMockBuilder(LeaveRequestService::class)
            ->setMethods(['_changeLeaveStatus', 'getLeaveRequestActions'])
            ->getMock();
        $leaveRequestService->expects($this->once())
            ->method('_changeLeaveStatus');
        $leaveRequestService->expects($this->once())
            ->method('getLeaveRequestActions')
            ->will($this->returnValue([86 => 'Cancel']));

        $cancelAction = new WorkflowStateMachine();
        $cancelAction->fromArray(array('id' => 3, 'workflow' => 'leave',
            'state' => 'PENDING APPROVAL', 'role' => 'ADMIN', 'action' => 'CANCEL',
            'resulting_state' => 'CANCELLED', 'roles_to_notify' => '', 'priority' => 0));

        $accessFlowStateMachineService = $this->getMockBuilder(AccessFlowStateMachineService::class)
            ->setMethods(['getWorkflowItem'])
            ->getMock();
        $accessFlowStateMachineService->expects($this->once())
            ->method('getWorkflowItem')
            ->will($this->returnValue($cancelAction));

        $leaveRequest = new LeaveRequest();
        $leaveRequestService->setAccessFlowStateMachineService($accessFlowStateMachineService);
        $leaveRequestService->changeLeaveRequestStatus($leaveRequest, 'Cancel');
    }
}

/** TODO: Remove*/
class MockLeaveStateManager {

    public function __construct() {

    }
    public function approve() {

    }

    public function reject() {

    }

    public function cancel() {
        
    }
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