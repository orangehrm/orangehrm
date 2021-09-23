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

    public function testGetLeaveRecordsBetweenTwoDays(){
        $employeeId = 1;
        $leaveRecord = TestDataService::fetchObject('Leave', 2);
        $leaveRequestDaoMock = $this->getMockBuilder('LeaveRequestDao')
            ->setMethods( array('getLeaveRecordsBetweenTwoDays'))
            ->getMock();
        $leaveRequestDaoMock->expects($this->once())
            ->method('getLeaveRecordsBetweenTwoDays')
            ->with('2010-09-01','2010-09-02',1,[0,-1,1,2,3])
            ->will($this->returnValue($leaveRecord));

        $this->leaveRequestService->setLeaveRequestDao($leaveRequestDaoMock);
        $retrievedLeaveRecord = $this->leaveRequestService->getLeaveRecordsBetweenTwoDays('2010-09-01','2010-09-02',$employeeId,[0,-1,1,2,3]);
        $this->assertEquals($leaveRecord, $retrievedLeaveRecord);
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
