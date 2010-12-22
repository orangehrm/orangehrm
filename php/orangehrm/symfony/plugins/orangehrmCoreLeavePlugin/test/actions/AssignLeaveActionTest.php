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
require_once sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/modules/coreLeave/actions/assignLeaveAction.class.php';

/**
 * Test of AssignLeaveAction
 *
 * @author sujith
 */
class AssignLeaveActionTest extends PHPUnit_Framework_TestCase {

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
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/AssignLeaveAction.yml';
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

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $leaveRequestService = $assignLeaveAction->getLeaveRequestService();
        $this->assertTrue($leaveRequestService instanceof LeaveRequestService);
    }


    /**
     * Test to Earger loading of EmployeeService
     */
    public function testEagerLoadEmployeeService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $employeeService = $assignLeaveAction->getEmployeeService();
        $this->assertTrue($employeeService instanceof EmployeeService);
    }

    /**
     * Test to Earger loading of LeavePeriodService
     */
    public function testEagerLoadLeavePeriodService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $leavePeriodService = $assignLeaveAction->getLeavePeriodService();
        $this->assertTrue($leavePeriodService instanceof LeavePeriodService);
    }

    /**
     * Test to Earger loading of LeaveNotificationService
     */
    public function testEagerLoadLeaveNotificationService() {
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $leaveNotificationService = $assignLeaveAction->getLeaveNotificationService();
        $this->assertTrue($leaveNotificationService instanceof LeaveNotificationService);
    }

    /**
     * Fails and displays error message if leave is overlapping
     */
    public function testLeaveOverlapAssign() {
        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getOverlappingLeave'));
        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue($leaves));

        //mocking LeaveTypeService
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');
        $leaveTypeService = $this->getMock('LeaveTypeService', array('getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $assignLeaveAction->setLeaveRequestService($leaveRequestService);
        $assignLeaveAction->setLeaveTypeService($leaveTypeService);

        //mocking the form
        $form = $this->getMock('AssignLeaveForm', array('isValid'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $assignLeaveAction->setForm($form);

        try {
            $assignLeaveAction->execute($request);
            $this->assertTrue(!isset($assignLeaveAction->templateMessage['SUCCESS']));
            $this->assertTrue(isset($assignLeaveAction->overlapLeaves));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks for the success scenario in case if an employee assigned a leave
     */
    public function testAssignLeaveSuccess() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getOverlappingLeave', 'saveLeaveRequest'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        $leaveRequestService->expects($this->once())
                ->method('saveLeaveRequest')
                ->will($this->returnValue(true));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType', 'getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));

        //mocking leave notification service
        $leaveNotificationService = $this->getMock('LeaveNotificationService', array('sendAssignLeaveNotification'));
        $leaveNotificationService->expects($this->once())
                ->method('sendAssignLeaveNotification');
    
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

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $assignLeaveAction->setLeaveRequestService($leaveRequestService);
        $assignLeaveAction->setLeaveTypeService($leaveTypeService);
        $assignLeaveAction->setLeavePeriodService($leavePeriodService);
        $assignLeaveAction->setLeaveNotificationService($leaveNotificationService);

        //mocking the form
        $form = $this->getMock('AssignLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange'));
        $form->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));

        $leaves = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $form->expects($this->once())
                ->method('createLeaveObjectListForAppliedRange')
                ->will($this->returnValue($leaves));

        $assignLeaveAction->setForm($form);

        try {
            $assignLeaveAction->execute($request);
            $this->assertTrue(isset($assignLeaveAction->templateMessage['SUCCESS']));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks for Exception thrown scenario
     */
    public function testAssignLeaveFailureOnLeavePeriodDoesntExist() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getOverlappingLeave', 'saveLeaveRequest'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        $leaveRequestService->expects($this->once())
                ->method('saveLeaveRequest')
                ->will($this->returnValue(true));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType', 'getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));

        //mocking leave notification service
        $leaveNotificationService = $this->getMock('LeaveNotificationService', array('sendAssignLeaveNotification'));
        $leaveNotificationService->expects($this->once())
                ->method('sendAssignLeaveNotification');

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

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $assignLeaveAction->setLeaveRequestService($leaveRequestService);
        $assignLeaveAction->setLeaveTypeService($leaveTypeService);
        $assignLeaveAction->setLeavePeriodService($leavePeriodService);
        $assignLeaveAction->setLeaveNotificationService($leaveNotificationService);

        //mocking the form
        $form = $this->getMock('AssignLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange', 'getLeaveRequest'));
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
        
        $assignLeaveAction->setForm($form);

        try {
            $assignLeaveAction->execute($request);
        } catch(Exception $e) {
            $this->assertTrue(isset($assignLeaveAction->templateMessage['WARNING']));
            $this->assertTrue($e instanceof DaoException);
        }
    }

    /**
     * This test checks for the success scenario in case if an employee assigned a leave
     */
    public function testAssignLeaveFailsOnHolidays() {

        // Set post parameters
        $parameters = array('txtFromDate'=>'2010-11-23',
                'txtToDate'=>'2010-11-24',
                'txtEmpID'=>'0001',
                'txtLeaveType'=>'LT001');

        $request = $this->context->request;
        $request->setPostParameters($parameters);

        // Set request to POST method
        $request->setMethod(sfRequest::POST);

        //mocking LeaveRequestService
        $leaveRequestService = $this->getMock('LeaveRequestService', array('getOverlappingLeave'));
        $leaveTypes = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveTypes');

        $leaveRequestService->expects($this->once())
                ->method('getOverlappingLeave')
                ->will($this->returnValue(array()));

        //mocking LeaveTypeService
        $leaveTypeService = $this->getMock('LeaveTypeService', array('readLeaveType', 'getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('readLeaveType')
                ->will($this->returnValue($leaveTypes[0]));

        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));

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

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $assignLeaveAction->setLeaveRequestService($leaveRequestService);
        $assignLeaveAction->setLeaveTypeService($leaveTypeService);
        $assignLeaveAction->setLeavePeriodService($leavePeriodService);

        //mocking the form
        $form = $this->getMock('AssignLeaveForm', array('isValid', 'createLeaveObjectListForAppliedRange'));
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

        $assignLeaveAction->setForm($form);

        try {
            $assignLeaveAction->execute($request);
            $this->assertTrue(isset($assignLeaveAction->templateMessage['SUCCESS']));
        } catch(Exception $e) {

        }
    }

    /**
     * This test checks if any single leave type is not defined
     */
    public function testDisplayErrorForLeaveTypeNotDefined() {
        $request = $this->context->request;

        // Set request to POST method
        $request->setMethod(sfRequest::GET);

        //mocking LeaveTypeService - we just assign empty array to make the function fail
        $leaveTypes = array();
        $leaveTypeService = $this->getMock('LeaveTypeService', array('getLeaveTypeList'));
        $leaveTypeService->expects($this->once())
                ->method('getLeaveTypeList')
                ->will($this->returnValue($leaveTypes));

        $assignLeaveAction = new assignLeaveAction($this->context, "coreLeave", "execute");
        $assignLeaveAction->setLeaveTypeService($leaveTypeService);

        try {
            $assignLeaveAction->execute($request);
            $this->assertTrue(isset($assignLeaveAction->templateMessage['WARNING']));
        } catch(Exception $e) {

        }
    }
}
?>
