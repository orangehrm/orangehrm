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

require_once sfConfig::get('sf_test_dir') . '/util/MockContext.class.php';
require_once sfConfig::get('sf_test_dir') . '/util/MockWebRequest.class.php';
require_once sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/modules/leave/actions/applyLeaveAction.class.php';
/**
 * Testing ApplyLeaveAction
 *
 * @author sujith
 */
class ApplyLeaveActionTest extends PHPUnit_Framework_TestCase {

    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        /* Create mock objects required for testing */
        $this->context = MockContext::getInstance();

        $request = new MockWebRequest();

        // In sfConfigCache, we just need checkConfig method
        $configCache = $this->getMock('sfConfigCache', array('checkConfig'), array(), '', false);

        // Mock of controller, with redirect method mocked.
        $controller = $this->getMock('sfController', array('redirect', 'forward'), array(), '', false);
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/ApplyLeaveAction.yml';
        $this->context->request = $request;
        $this->context->configCache = $configCache;
        $this->context->controller = $controller;
    }

    /**
     * Test to Earger loading of LeaveRequestService
     */
    public function testEagerLoadLeaveRequestService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $leaveRequestService = $applyLeaveAction->getLeaveRequestService();
        $this->assertTrue($leaveRequestService instanceof LeaveRequestService);
    }


    /**
     * Test to Earger loading of EmployeeService
     */
    public function testEagerLoadEmployeeService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $employeeService = $applyLeaveAction->getEmployeeService();
        $this->assertTrue($employeeService instanceof EmployeeService);
    }

    /**
     * Test to Earger loading of LeavePeriodService
     */
    public function testEagerLoadLeavePeriodService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $leavePeriodService = $applyLeaveAction->getLeavePeriodService();
        $this->assertTrue($leavePeriodService instanceof LeavePeriodService);
    }

    /**
     * Test to Earger loading of LeaveNotificationService
     */
    public function testEagerLoadLeaveNotificationService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $leaveNotificationService = $applyLeaveAction->getLeaveNotificationService();
        $this->assertTrue($leaveNotificationService instanceof LeaveNotificationService);
    }

    /**
     * This test checks for Non-eligible leave error message
     */
    public function testDisplayErrorForNonEligibleLeaveType() {
        $request = $this->context->request;

        // Set request to POST method
        $request->setMethod(sfRequest::GET);
        //mocking employee service
        $employeeService = $this->getMock('EmployeeService', array('getEmployee'));
        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $employee->setFirstName("rand name");

        $employeeService->expects($this->once())
                ->method('getEmployee')
                ->will($this->returnValue($employee));

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getEmployeeAllowedToApplyLeaveTypes'));
        $leaveRequestService->expects($this->once())
                ->method('getEmployeeAllowedToApplyLeaveTypes')
                ->will($this->returnValue(array()));

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $applyLeaveAction->setEmployeeNumber('0001');
        $applyLeaveAction->setEmployeeService($employeeService);
        $applyLeaveAction->setLeaveRequestService($leaveRequestService);

        try {
            $applyLeaveAction->execute($request);
            $this->assertTrue(isset($applyLeaveAction->templateMessage['WARNING']));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks for the success scenario
     */
    public function testApplyLeaveSuccess() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);
        //mocking employee service
        $employeeService = $this->getMock('EmployeeService', array('getEmployee'));
        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $employee->setFirstName("rand name");

        $employeeService->expects($this->once())
                ->method('getEmployee')
                ->will($this->returnValue($employee));

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getEmployeeAllowedToApplyLeaveTypes', 'getOverlappingLeave', 'saveLeaveRequest'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        $leaveRequestService->expects($this->once())
                ->method('getEmployeeAllowedToApplyLeaveTypes')
                ->will($this->returnValue($leaveTypes));

        $leaveRequestService->expects($this->once())
                ->method('saveLeaveRequest')
                ->will($this->returnValue(true));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        //mocking leave period service
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(2);
        $leavePeriodService = $this->getMock('LeavePeriodService', array('isWithinNextLeavePeriod', 'createNextLeavePeriod'));
        $leavePeriodService->expects($this->once())
                ->method('isWithinNextLeavePeriod')
                ->will($this->returnValue(true));

        $leavePeriodService->expects($this->once())
                ->method('createNextLeavePeriod')
                ->will($this->returnValue($leavePeriod));

        //mocking leave notification service
        $leaveNotificationService = $this->getMock('LeaveNotificationService', array('sendApplyLeaveNotification'));
        $leaveNotificationService->expects($this->once())
                ->method('sendApplyLeaveNotification')
                ->will($this->returnValue(true));
        
        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $applyLeaveAction->setEmployeeNumber('0001');
        $applyLeaveAction->setEmployeeService($employeeService);
        $applyLeaveAction->setLeaveRequestService($leaveRequestService);
        $applyLeaveAction->setLeaveTypeService($leaveTypeService);
        $applyLeaveAction->setLeavePeriodService($leavePeriodService);
        $applyLeaveAction->setLeaveNotificationService($leaveNotificationService);

        //mocking the form
        $form = $this->getMock('ApplyLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $form->expects($this->once())
                ->method('createLeaveObjectListForAppliedRange')
                ->will($this->returnValue($leaves));

        $applyLeaveAction->setForm($form);

        try {
            $applyLeaveAction->execute($request);
            $this->assertTrue(isset($applyLeaveAction->templateMessage['SUCCESS']));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks for exception throwing scenario
     */
    public function testApplyLeaveFailureOnLeavePeriodDoesntExist() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);
        //mocking employee service
        $employeeService = $this->getMock('EmployeeService', array('getEmployee'));
        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $employee->setFirstName("rand name");

        $employeeService->expects($this->once())
                ->method('getEmployee')
                ->will($this->returnValue($employee));

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getEmployeeAllowedToApplyLeaveTypes', 'getOverlappingLeave', 'saveLeaveRequest'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        $leaveRequestService->expects($this->once())
                ->method('getEmployeeAllowedToApplyLeaveTypes')
                ->will($this->returnValue($leaveTypes));

        $leaveRequestService->expects($this->once())
                ->method('saveLeaveRequest')
                ->will($this->returnValue(true));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        //mocking leave period service
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(2);
        $leavePeriodService = $this->getMock('LeavePeriodService', array('isWithinNextLeavePeriod', 'createNextLeavePeriod'));
        $leavePeriodService->expects($this->once())
                ->method('isWithinNextLeavePeriod')
                ->will($this->returnValue(true));

        $leavePeriodService->expects($this->once())
                ->method('createNextLeavePeriod')
                ->will($this->returnValue($leavePeriod));

        //mocking leave notification service
        $leaveNotificationService = $this->getMock('LeaveNotificationService', array('sendApplyLeaveNotification'));
        $leaveNotificationService->expects($this->once())
                ->method('sendApplyLeaveNotification')
                ->will($this->returnValue(true));

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $applyLeaveAction->setEmployeeNumber('0001');
        $applyLeaveAction->setEmployeeService($employeeService);
        $applyLeaveAction->setLeaveRequestService($leaveRequestService);
        $applyLeaveAction->setLeaveTypeService($leaveTypeService);
        $applyLeaveAction->setLeavePeriodService($leavePeriodService);
        $applyLeaveAction->setLeaveNotificationService($leaveNotificationService);

        //mocking the form
        $form = $this->getMock('ApplyLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange', 'getLeaveRequest'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $form->expects($this->once())
                ->method('createLeaveObjectListForAppliedRange')
                ->will($this->returnValue($leaves));

        //returns empty LeaveRequest without a leave period id, that can make exception to be thrown
        $form->expects($this->once())
                ->method('getLeaveRequest')
                ->will($this->returnValue(new LeaveRequest()));

        $applyLeaveAction->setForm($form);

        try {
            $applyLeaveAction->execute($request);
        } catch(Exception $e) {
            $this->assertTrue(isset($applyLeaveAction->templateMessage['WARNING']));
            $this->assertTrue($e instanceof DaoException);
        }
    }

    /**
     * This test checks for the success scenario
     */
    public function testApplyLeaveFailedOnHoliday() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);
        //mocking employee service
        $employeeService = $this->getMock('EmployeeService', array('getEmployee'));
        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $employee->setFirstName("rand name");

        $employeeService->expects($this->once())
                ->method('getEmployee')
                ->will($this->returnValue($employee));

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getEmployeeAllowedToApplyLeaveTypes', 'getOverlappingLeave'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        $leaveRequestService->expects($this->once())
                ->method('getEmployeeAllowedToApplyLeaveTypes')
                ->will($this->returnValue($leaveTypes));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        //mocking leave period service
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(2);
        $leavePeriodService = $this->getMock('LeavePeriodService', array('isWithinNextLeavePeriod', 'createNextLeavePeriod'));
        $leavePeriodService->expects($this->once())
                ->method('isWithinNextLeavePeriod')
                ->will($this->returnValue(true));

        $leavePeriodService->expects($this->once())
                ->method('createNextLeavePeriod')
                ->will($this->returnValue($leavePeriod));

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $applyLeaveAction->setEmployeeNumber('0001');
        $applyLeaveAction->setEmployeeService($employeeService);
        $applyLeaveAction->setLeaveRequestService($leaveRequestService);
        $applyLeaveAction->setLeaveTypeService($leaveTypeService);
        $applyLeaveAction->setLeavePeriodService($leavePeriodService);

        //mocking the form
        $form = $this->getMock('ApplyLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');

        //making all leaves to holidays
        foreach($leaves as $k => $obj) {
            $leaves[$k]->setLeaveStatus(Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        }

        $form->expects($this->once())
                ->method('createLeaveObjectListForAppliedRange')
                ->will($this->returnValue($leaves));

        $applyLeaveAction->setForm($form);

        try {
            $applyLeaveAction->execute($request);
            $this->assertTrue(isset($applyLeaveAction->templateMessage['WARNING']));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks for the Leave Overlapping and failure in apply
     */
    public function testApplyLeaveFailLeaveOverlap() {
        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);

        //mocking employee service
        $employeeService = $this->getMock('EmployeeService', array('getEmployee'));
        $employee = new Employee();
        $employee->setEmployeeId('0001');
        $employee->setFirstName("rand name");

        $employeeService->expects($this->once())
                ->method('getEmployee')
                ->will($this->returnValue($employee));

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getEmployeeAllowedToApplyLeaveTypes', 'getOverlappingLeave'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue($leaves));

        $leaveRequestService->expects($this->once())
                ->method('getEmployeeAllowedToApplyLeaveTypes')
                ->will($this->returnValue($leaveTypes));

        $applyLeaveAction = new applyLeaveAction($this->context, "leave", "execute");
        $applyLeaveAction->setEmployeeNumber('0001');
        $applyLeaveAction->setEmployeeService($employeeService);
        $applyLeaveAction->setLeaveRequestService($leaveRequestService);

        //mocking the form
        $form = $this->getMock('ApplyLeaveForm', array('isValid'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $applyLeaveAction->setForm($form);

        try {
            $applyLeaveAction->execute($request);
            $this->assertTrue(!isset($applyLeaveAction->templateMessage['SUCCESS']));
            $this->assertTrue(isset($applyLeaveAction->overlapLeaves));
        } catch(Exception $e) {

        }
    }
}
?>
