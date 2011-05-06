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
 */
 class LeaveRequestDaoTest extends PHPUnit_Framework_TestCase{
 	
  	public $leaveRequestDao ;
  	public $leaveType ;
  	public $leavePeriod ;
  	public $employee ;
        private $fixture;
 	
 	protected function setUp() {

            $this->leaveRequestDao	=	new LeaveRequestDao();
            $fixtureFile = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveRequestDao.yml';
            TestDataService::populate($fixtureFile);
            $this->fixture = sfYaml::load($fixtureFile);
    	
    }
    
    /* Tests for fetchLeaveRequest() */

    public function testFetchLeaveRequest() {

        $leaveRequest = $this->leaveRequestDao->fetchLeaveRequest(1);

        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $this->assertEquals(1, $leaveRequest->getLeavePeriodId(1));
        $this->assertEquals('LTY001', $leaveRequest->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequest->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $leaveRequest->getDateApplied());
        $this->assertEquals(1, $leaveRequest->getEmpNumber());

    }

    /* Tests for fetchLeave() */

    public function testFetchLeave() {

        $leaveList = $this->leaveRequestDao->fetchLeave(1);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(2, count($leaveList));

        $this->assertEquals(2, $leaveList[0]->getLeaveId());
        $this->assertEquals('LTY001', $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmployeeId());
        $this->assertEquals('2010-09-02', $leaveList[0]->getLeaveDate());
        $this->assertEquals(1, $leaveList[0]->getLeaveStatus());

        $this->assertEquals(1, $leaveList[1]->getLeaveId());
        $this->assertEquals('LTY001', $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmployeeId());
        $this->assertEquals('2010-09-01', $leaveList[1]->getLeaveDate());
        $this->assertEquals(1, $leaveList[1]->getLeaveStatus());

    }

    /* Tests for getLeaveById() */

    public function testGetLeaveById() {

        $leave = $this->leaveRequestDao->getLeaveById(1);

        $this->assertTrue($leave instanceof Leave);

        $this->assertEquals(1, $leave->getLeaveId());
        $this->assertEquals(8, $leave->getLeaveLengthHours());
        $this->assertEquals(1, $leave->getLeaveLengthDays());
        $this->assertEquals(1, $leave->getLeaveRequestId());
        $this->assertEquals('LTY001', $leave->getLeaveTypeId());
        $this->assertEquals(1, $leave->getEmployeeId());
        $this->assertEquals('2010-09-01', $leave->getLeaveDate());
        $this->assertEquals(1, $leave->getLeaveStatus());

    }

    /* Tests for getNumOfLeave() */

    public function testGetNumOfLeave() {

        $this->assertEquals(8.75, $this->leaveRequestDao->getNumOfLeave(1, 'LTY001'));
        $this->assertNull($this->leaveRequestDao->getNumOfLeave(1, 'LTY100'));

    }

    /* Tests for getLeaveRecordCount() */

    public function testGetLeaveRecordCount() {

        $this->assertEquals(32, $this->leaveRequestDao->getLeaveRecordCount());

    }

    /* Tests for getNumOfAvaliableLeave() */

    public function testGetNumOfAvaliableLeave() {

        $this->assertEquals(4, $this->leaveRequestDao->getNumOfAvaliableLeave(1, 'LTY002'));
        $this->assertEquals(2, $this->leaveRequestDao->getNumOfAvaliableLeave(2, 'LTY001'));

    }

    /* Tests for getScheduledLeavesSum() */

    public function testGetScheduledLeavesSum() {

        $this->assertEquals(2.75, $this->leaveRequestDao->getScheduledLeavesSum(1, 'LTY001', 1));
        $this->assertEquals(1, $this->leaveRequestDao->getScheduledLeavesSum(2, 'LTY002', 1));

    }

    /* Tests for getTakenLeaveSum() */

    public function testGetTakenLeaveSum() {

        $this->assertEquals(2, $this->leaveRequestDao->getTakenLeaveSum(5, 'LTY002', 1));

    }

    /* Tests for getLeavePeriodOverlapLeaves() */

    public function testGetLeavePeriodOverlapLeaves() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);
        $leaveList = $this->leaveRequestDao->getLeavePeriodOverlapLeaves($leavePeriod);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        /* Because of groupBy only first leave (2011-02-07) is
         * returned instead of both (2011-02-07, 2011-02-08)
         */
        $this->assertEquals(1, count($leaveList));

    }

    /* Tests for getOverlappingLeave() */

    public function testGetOverlappingLeave() {

        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-01-01', '2010-12-31', 1);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(11, count($leaveList));

        $this->assertEquals(1, $leaveList[0]->getLeaveId());
        $this->assertEquals(18, $leaveList[10]->getLeaveId());

    }

    /* Common methods */

    private function _getLeaveRequestData() {

        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeavePeriodId(1);
        $leaveRequest->setLeaveTypeId('LTY001');
        $leaveRequest->setLeaveTypeName('Casual');
        $leaveRequest->setDateApplied('2010-09-01');
        $leaveRequest->setEmpNumber(1);
        $leaveRequest->setLeaveComments("Testing comment i add");

        $leave1 = new Leave();
        $leave1->setLeaveLengthHours(8);
        $leave1->setLeaveLengthDays(1);
        $leave1->setLeaveDate('2010-12-01');
        $leave1->setLeaveStatus(1);

        $leave2 = new Leave();
        $leave2->setLeaveLengthHours(6);
        $leave2->setLeaveLengthDays(0.75);
        $leave2->setLeaveDate('2010-12-02');
        $leave2->setLeaveStatus(1);

        return array($leaveRequest, array($leave1, $leave2));

    }

    /* Tests for saveLeaveRequest() */

    public function testSaveLeaveRequestNewRequest() {

        $leaveRequestData = $this->_getLeaveRequestData();

        $this->assertTrue($this->leaveRequestDao->saveLeaveRequest($leaveRequestData[0], $leaveRequestData[1]));

        $leaveRequestList = TestDataService::fetchLastInsertedRecords('LeaveRequest', 1);
        $leaveRequest = $leaveRequestList[0];

        $this->assertEquals(20, $leaveRequest->getLeaveRequestId());
        $this->assertEquals(1, $leaveRequest->getLeavePeriodId());
        $this->assertEquals('LTY001', $leaveRequest->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequest->getLeaveTypeName());
        $this->assertEquals('2010-09-01', $leaveRequest->getDateApplied());
        $this->assertEquals(1, $leaveRequest->getEmpNumber());
        $this->assertEquals("Testing comment i add", $leaveRequest->getLeaveComments());

        $leaveList = TestDataService::fetchLastInsertedRecords('Leave', 2);

        $this->assertEquals(33, $leaveList[0]->getLeaveId());
        $this->assertEquals(8, $leaveList[0]->getLeaveLengthHours());
        $this->assertEquals(1, $leaveList[0]->getLeaveLengthDays());
        $this->assertEquals(20, $leaveList[0]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmployeeId());
        $this->assertEquals('2010-12-01', $leaveList[0]->getLeaveDate());
        $this->assertEquals(1, $leaveList[0]->getLeaveStatus());

        $this->assertEquals(34, $leaveList[1]->getLeaveId());
        $this->assertEquals(6, $leaveList[1]->getLeaveLengthHours());
        $this->assertEquals(0.75, $leaveList[1]->getLeaveLengthDays());
        $this->assertEquals(20, $leaveList[1]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmployeeId());
        $this->assertEquals('2010-12-02', $leaveList[1]->getLeaveDate());
        $this->assertEquals(1, $leaveList[1]->getLeaveStatus());

    }

    /*public function testSaveLeaveRequestUpdateRequest() {

        $leaveRequest = TestDataService::fetchObject('LeaveRequest', 1);
        $leave1 = TestDataService::fetchObject('Leave', 1);
        $leave2 = TestDataService::fetchObject('Leave', 2);

        $leave1->setLeaveStatus(2);
        $leave2->setLeaveStatus(2);

        $this->assertTrue($this->leaveRequestDao->saveLeaveRequest($leaveRequest, array($leave1, $leave2)));

        $leave1 = TestDataService::fetchObject('Leave', 1);
        $leave2 = TestDataService::fetchObject('Leave', 2);

        $this->assertEquals(1, $leave1->getLeaveId());
        $this->assertEquals(8, $leave1->getLeaveLengthHours());
        $this->assertEquals(1, $leave1->getLeaveLengthDays());
        $this->assertEquals(1, $leave1->getLeaveRequestId());
        $this->assertEquals('LTY001', $leave1->getLeaveTypeId());
        $this->assertEquals(1, $leave1->getEmployeeId());
        $this->assertEquals('2010-09-01', $leave1->getLeaveDate());
        $this->assertEquals(2, $leave1->getLeaveStatus());

        $this->assertEquals(1, $leave1->getLeaveId());
        $this->assertEquals(8, $leave1->getLeaveLengthHours());
        $this->assertEquals(1, $leave1->getLeaveLengthDays());
        $this->assertEquals(1, $leave1->getLeaveRequestId());
        $this->assertEquals('LTY001', $leave1->getLeaveTypeId());
        $this->assertEquals(1, $leave1->getEmployeeId());
        $this->assertEquals('2010-09-02', $leave1->getLeaveDate());
        $this->assertEquals(2, $leave1->getLeaveStatus());

    }*/


    /* Tests for modifyOverlapLeaveRequest() */

    public function testModifyOverlapLeaveRequest() {

        /* Preparing required data */

        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeavePeriodId(1);
        $leaveRequest->setLeaveTypeId('LTY001');
        $leaveRequest->setLeaveTypeName('Casual');
        $leaveRequest->setDateApplied('2010-12-01');
        $leaveRequest->setEmpNumber(1);

        $leave[0] = new Leave();
        $leave[0]->setLeaveLengthHours(8);
        $leave[0]->setLeaveLengthDays(1);
        $leave[0]->setLeaveDate('2010-12-30');
        $leave[0]->setLeaveStatus(1);

        $leave[1] = new Leave();
        $leave[1]->setLeaveLengthHours(8);
        $leave[1]->setLeaveLengthDays(1);
        $leave[1]->setLeaveDate('2010-12-31');
        $leave[1]->setLeaveStatus(1);

        $leave[2] = new Leave();
        $leave[2]->setLeaveLengthHours(8);
        $leave[2]->setLeaveLengthDays(1);
        $leave[2]->setLeaveDate('2011-01-01');
        $leave[2]->setLeaveStatus(1);

        $leave[3] = new Leave();
        $leave[3]->setLeaveLengthHours(8);
        $leave[3]->setLeaveLengthDays(1);
        $leave[3]->setLeaveDate('2011-01-02');
        $leave[3]->setLeaveStatus(1);

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        /* Executing tests */

        /* At use, modifyOverlapLeaveRequest() is called after calling
         * saveLeaveRequest()
         */

        $this->assertTrue($this->leaveRequestDao->saveLeaveRequest($leaveRequest, $leave));
        $this->assertTrue($this->leaveRequestDao->modifyOverlapLeaveRequest($leaveRequest, $leave, $leavePeriod));

        $leaveRequestList = TestDataService::fetchLastInsertedRecords('LeaveRequest', 2);

        $this->assertEquals(20, $leaveRequestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $leaveRequestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $leaveRequestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-12-01', $leaveRequestList[0]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[0]->getEmpNumber());

        $this->assertEquals(21, $leaveRequestList[1]->getLeaveRequestId());
        $this->assertEquals(2, $leaveRequestList[1]->getLeavePeriodId());
        $this->assertEquals('LTY001', $leaveRequestList[1]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[1]->getLeaveTypeName());
        $this->assertEquals('2010-12-01', $leaveRequestList[1]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[1]->getEmpNumber());

        $leaveList = TestDataService::fetchLastInsertedRecords('Leave', 4);

        $this->assertEquals(33, $leaveList[0]->getLeaveId());
        $this->assertEquals(8, $leaveList[0]->getLeaveLengthHours());
        $this->assertEquals(1, $leaveList[0]->getLeaveLengthDays());
        $this->assertEquals(20, $leaveList[0]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmployeeId());
        $this->assertEquals('2010-12-30', $leaveList[0]->getLeaveDate());
        $this->assertEquals(1, $leaveList[0]->getLeaveStatus());

        $this->assertEquals(34, $leaveList[1]->getLeaveId());
        $this->assertEquals(8, $leaveList[1]->getLeaveLengthHours());
        $this->assertEquals(1, $leaveList[1]->getLeaveLengthDays());
        $this->assertEquals(20, $leaveList[1]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmployeeId());
        $this->assertEquals('2010-12-31', $leaveList[1]->getLeaveDate());
        $this->assertEquals(1, $leaveList[1]->getLeaveStatus());

        $this->assertEquals(35, $leaveList[2]->getLeaveId());
        $this->assertEquals(8, $leaveList[2]->getLeaveLengthHours());
        $this->assertEquals(1, $leaveList[2]->getLeaveLengthDays());
        $this->assertEquals(21, $leaveList[2]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[2]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[2]->getEmployeeId());
        $this->assertEquals('2011-01-01', $leaveList[2]->getLeaveDate());
        $this->assertEquals(1, $leaveList[2]->getLeaveStatus());

        $this->assertEquals(36, $leaveList[3]->getLeaveId());
        $this->assertEquals(8, $leaveList[3]->getLeaveLengthHours());
        $this->assertEquals(1, $leaveList[3]->getLeaveLengthDays());
        $this->assertEquals(21, $leaveList[3]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[3]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[3]->getEmployeeId());
        $this->assertEquals('2011-01-02', $leaveList[3]->getLeaveDate());
        $this->assertEquals(1, $leaveList[3]->getLeaveStatus());

    }


    /* Tests for searchLeaveRequests() */

    public function testSearchLeaveRequestsAll() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(19, count($requestList));
        $this->assertEquals(19, $requestCount);

        /* Checking values and order */

        $this->assertEquals(19, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(2, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-20', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(18, $requestList[18]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[18]->getLeavePeriodId());
        $this->assertEquals('LTY002', $requestList[18]->getLeaveTypeId());
        $this->assertEquals('Medical', $requestList[18]->getLeaveTypeName());
        $this->assertEquals('2010-03-15', $requestList[18]->getDateApplied());
        $this->assertEquals(5, $requestList[18]->getEmpNumber());

    }

    public function testSearchLeaveRequestsDateRange() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-09-01');
        $dateRange->setToDate('2010-09-30');

        $searchParameters->setParameter('dateRange', $dateRange);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(9, count($requestList));
        $this->assertEquals(9, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(1, $requestList[8]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[8]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[8]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[8]->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());
        
    }

    public function testSearchLeaveRequestsStates() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-01-01');
        $dateRange->setToDate('2010-12-31');

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('statuses', array(1, -1, 3));

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY002', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Medical', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[8]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[8]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[8]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[8]->getLeaveTypeName());
        $this->assertEquals('2010-06-09', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterId() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('employeeFilter', 1);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[10]->getLeavePeriodId());
        $this->assertEquals('LTY003', $requestList[10]->getLeaveTypeId());
        $this->assertEquals('Company', $requestList[10]->getLeaveTypeName());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterSingleEmployee() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $searchParameters->setParameter('employeeFilter', $employee);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[10]->getLeavePeriodId());
        $this->assertEquals('LTY003', $requestList[10]->getLeaveTypeId());
        $this->assertEquals('Company', $requestList[10]->getLeaveTypeName());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterMultipleEmployees() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);

        $employee1 = new Employee();
        $employee1->setEmpNumber(1);
        $employee2 = new Employee();
        $employee2->setEmpNumber(2);

        $searchParameters->setParameter('employeeFilter', array($employee1, $employee2));

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(14, count($requestList));
        $this->assertEquals(14, $requestCount);

        /* Checking values and order */

        $this->assertEquals(14, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-12', $requestList[0]->getDateApplied());
        $this->assertEquals(2, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[13]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[13]->getLeavePeriodId());
        $this->assertEquals('LTY003', $requestList[13]->getLeaveTypeId());
        $this->assertEquals('Company', $requestList[13]->getLeaveTypeName());
        $this->assertEquals('2010-06-08', $requestList[13]->getDateApplied());
        $this->assertEquals(1, $requestList[13]->getEmpNumber());

    }

    public function testSearchLeaveRequestsLeavePeriod() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leavePeriod', 1);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(18, count($requestList));
        $this->assertEquals(18, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY002', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Medical', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(18, $requestList[17]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[17]->getLeavePeriodId());
        $this->assertEquals('LTY002', $requestList[17]->getLeaveTypeId());
        $this->assertEquals('Medical', $requestList[17]->getLeaveTypeName());
        $this->assertEquals('2010-03-15', $requestList[17]->getDateApplied());
        $this->assertEquals(5, $requestList[17]->getEmpNumber());

    }

    public function testSearchLeaveRequestsLeaveType() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leaveType', 'LTY001');

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(10, count($requestList));
        $this->assertEquals(10, $requestCount);

        /* Checking values and order */

        $this->assertEquals(19, $requestList[0]->getLeaveRequestId());
        $this->assertEquals(2, $requestList[0]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-08-20', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[9]->getLeaveRequestId());
        $this->assertEquals(1, $requestList[9]->getLeavePeriodId());
        $this->assertEquals('LTY001', $requestList[9]->getLeaveTypeId());
        $this->assertEquals('Casual', $requestList[9]->getLeaveTypeName());
        $this->assertEquals('2010-06-09', $requestList[9]->getDateApplied());
        $this->assertEquals(1, $requestList[9]->getEmpNumber());

    }


    /**
     * Test the readLeave() function
     */
    public function testReadLeave() {

        //
        // Unavailable leave id
        //
        Doctrine_Query::create()
		->delete('*')
		->from('Leave l')
		->where('leave_id = 999');

        $leave = $this->leaveRequestDao->readLeave(999);

        $this->assertFalse($leave, 'should return false for unavailable leave id');

        //
        // Available leave id
        //
        $leaveFixture = $this->fixture['Leave'][1];

        $savedLeave = $this->leaveRequestDao->readLeave($leaveFixture['leave_id']);

        // Compare leave id
        $this->assertEquals($savedLeave->leave_id, $leaveFixture['leave_id'], 'leave id should match');
        
        // Compare other properties
        foreach ($leaveFixture as $property => $value) {
            $this->assertEquals($savedLeave->$property, $value, $property . ' should match ');
        }
    }

    public function testSaveLeave() {

        // Try and save leave with id that exists - should throw error
        $existingLeave = new Leave();
        $existingLeave->fromArray($this->fixture['Leave'][1]);

        try {
            $this->leaveRequestDao->saveLeave($existingLeave);
            $this->fail("Dao exception expected");
        } catch (DaoException $e) {
            // expected
        }

        // Try to save new leave (without id)
        $leaveRequestId = $this->fixture['LeaveRequest'][1]['leave_request_id'];
        $leave = new Leave();
        $leave->leave_length_hours = 8;
        $leave->leave_length_days = 1;
        $leave->leave_request_id = $leaveRequestId;
        $leave->leave_type_id = $this->fixture['LeaveType'][0]['leaveTypeId'];
        $leave->employee_id = $this->fixture['Employee'][0]['empNumber'];
        $leave->leave_date = '2010-09-09';
        $leave->leave_status = 1;
        $this->leaveRequestDao->saveLeave($leave);

        // Verify id assigned
        $this->assertTrue(!empty($leave->leave_id));


        // Verify saved by retrieving
        $result = Doctrine_Query::create()
                                    ->select()
                                    ->from('Leave l')
                                    ->where('leave_id = ?', $leave->leave_id)
                                    ->execute();
        $this->assertTrue($result->count() == 1);
        $this->assertTrue(is_a($result[0], Leave));

        $origAsArray = $leave->toArray();
        $savedAsArray = $result[0]->toArray();

        $this->assertEquals($origAsArray, $savedAsArray);        
    }

    
 }


 class ParameterStub {

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

 class DateRangeStub {

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