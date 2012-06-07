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
 * Leave period service test
 * @group CoreLeave 
 */
class LeaveEntitlementServiceTest extends PHPUnit_Framework_TestCase {

    private $leaveEntitlementService;
    private $fixture;

    protected function setUp() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveEntitlementService.yml';
        $this->leaveEntitlementService = new LeaveEntitlementService();
        
    }
    
    /* test getEmployeeLeaveEntitlementDays */
    
    public function testGetEmployeeLeaveEntitlementDays() {

        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');
        
        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('getEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('getEmployeeLeaveEntitlement')
                            ->will($this->returnValue($leaveEntitlements[0]));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
        $entitlementdays    =	$this->leaveEntitlementService->getEmployeeLeaveEntitlementDays("0001", 1, 2);
        
        $this->assertEquals($leaveEntitlements[0]->getNoOfDaysAllotted(), $entitlementdays);
        
    }

    /* test adjustEmployeeLeaveEntitlement */
    
    public function testAdjustEmployeeLeaveEntitlement() {

        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves[0]->setLeaveRequest($leaveRequests[0]);

        $leaveEntitlementDao = $this->getMock('LeaveEntitlementDao', array('adjustEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('adjustEmployeeLeaveEntitlement')
                            ->will($this->returnValue(true));
        
        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
        $this->assertTrue($this->leaveEntitlementService->adjustEmployeeLeaveEntitlement($leaves[0], "3"));

    }

    /* test saveEmployeeLeaveEntitlement returns null */
    
    public function testSaveEmployeeLeaveEntitlementReadReturnsNull() {
        
        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('readEmployeeLeaveEntitlement', 'saveEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('readEmployeeLeaveEntitlement')
                            ->will($this->returnValue(null));

        $leaveEntitlementDao->expects($this->once())
                            ->method('saveEmployeeLeaveEntitlement')
                            ->will($this->returnValue(true));
        
        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
                      
        $this->assertTrue($this->leaveEntitlementService->saveEmployeeLeaveEntitlement('0001', 3, 3, 10));
        
    }

    /* test saveEmployeeLeaveEntitlement when overWrite set to true */

    public function testSaveEmployeeLeaveEntitlementOverWriteSetTrue() {

        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');

        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('readEmployeeLeaveEntitlement', 'overwriteEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('readEmployeeLeaveEntitlement')
                            ->will($this->returnValue($leaveEntitlements[0]));

        $leaveEntitlementDao->expects($this->once())
                            ->method('overwriteEmployeeLeaveEntitlement')
                            ->will($this->returnValue(true));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $this->assertTrue($this->leaveEntitlementService->saveEmployeeLeaveEntitlement('0001', 1, 2, 2, true));

    }

    /* test saveEmployeeLeaveEntitlement when overWrite set to false */

    public function testSaveEmployeeLeaveEntitlementOverWriteSetFalse() {

        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');

        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('readEmployeeLeaveEntitlement', 'updateEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('readEmployeeLeaveEntitlement')
                            ->will($this->returnValue($leaveEntitlements[0]));

        $leaveEntitlementDao->expects($this->once())
                            ->method('updateEmployeeLeaveEntitlement')
                            ->will($this->returnValue(true));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $this->assertTrue($this->leaveEntitlementService->saveEmployeeLeaveEntitlement('0001', 1, 2, 2, false));

    }

    /* test readEmployeeLeaveEntitlement */

    public function testReadEmployeeLeaveEntitlement() {

        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');

        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('readEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('readEmployeeLeaveEntitlement')
                            ->will($this->returnValue($leaveEntitlements[0]));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $readLeaveEntitlement = $this->leaveEntitlementService->readEmployeeLeaveEntitlement('0001', 1, 2);

        $this->assertTrue($readLeaveEntitlement instanceof EmployeeLeaveEntitlement);
        $this->assertEquals($leaveEntitlements[0]->getNoOfDaysAllotted(), $readLeaveEntitlement->getNoOfDaysAllotted());
        $this->assertEquals($leaveEntitlements[0]->getLeaveTaken(), $readLeaveEntitlement->getLeaveTaken());
        $this->assertEquals($leaveEntitlements[0], $readLeaveEntitlement);
        
    }

    /* test saveEmployeeLeaveCarriedForward */
    
    public function testSaveEmployeeLeaveCarriedForward() {

        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('saveEmployeeLeaveCarriedForward'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('saveEmployeeLeaveCarriedForward')
                            ->will($this->returnValue(true));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
        
        $this->assertTrue($this->leaveEntitlementService->saveEmployeeLeaveCarriedForward("0001", 1, 2, 4));

    }

    /* test saveEmployeeLeaveBroughtForward */

    public function testSaveEmployeeLeaveBroughtForward() {

        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('saveEmployeeLeaveBroughtForward'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('saveEmployeeLeaveBroughtForward')
                            ->will($this->returnValue(true));

        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $this->assertTrue($this->leaveEntitlementService->saveEmployeeLeaveBroughtForward("0001", 1, 2, 2));

    }

    public function testGetLeaveEntitlementDaoNewInstance() {

        $leaveEntitlementDao = $this->leaveEntitlementService->getLeaveEntitlementDao();
        $this->assertTrue($leaveEntitlementDao instanceof LeaveEntitlementDao);

    }

    public function testGetLeaveBalanceExistingRecords() {

        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementDao = $this->getMock('LeaveEntitlementDao', array('getLeaveBalance'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('getLeaveBalance')
                            ->with(1, 'LTY001', 1)
                            ->will($this->returnValue(6));
        $leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $this->assertEquals(6, $leaveEntitlementService->getLeaveBalance(1, 'LTY001', 1));

    }

    public function testGetLeaveBalanceEmptyRecords() {

        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementDao = $this->getMock('LeaveEntitlementDao', array('getLeaveBalance'));
        $leaveEntitlementDao->expects($this->once())
                            ->method('getLeaveBalance')
                            ->with(1, 'LTY001', 1)
                            ->will($this->returnValue('0.00'));
        $leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);

        $this->assertEquals('0.00', $leaveEntitlementService->getLeaveBalance(1, 'LTY001', 1));

    }
    
    public function testSearchEmployeeLeaveEntitlement() {

        $employeeLeaveEntitlementCollection = new Doctrine_Collection('EmployeeLeaveEntitlement');
        $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
        $employeeLeaveEntitlement->setLeaveTypeId(1);
        $employeeLeaveEntitlement->setLeavePeriodId(1);
        $employeeLeaveEntitlement->setEmployeeId(1);
        $employeeLeaveEntitlement->setNoOfDaysAllotted(14);
        $employeeLeaveEntitlement->setLeaveBroughtForward(2);
        $employeeLeaveEntitlement->setLeaveCarriedForward(5);
        
        $employeeLeaveEntitlementCollection->add($employeeLeaveEntitlement);
        
        $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
        $employeeLeaveEntitlement->setLeaveTypeId(1);
        $employeeLeaveEntitlement->setLeavePeriodId(1);
        $employeeLeaveEntitlement->setEmployeeId(2);
        $employeeLeaveEntitlement->setNoOfDaysAllotted(20);
        $employeeLeaveEntitlement->setLeaveBroughtForward(3);
        $employeeLeaveEntitlement->setLeaveCarriedForward(4);
        
        $employeeLeaveEntitlementCollection->add($employeeLeaveEntitlement);
        
        $leaveEntitlementDao = $this->getMock('LeaveEntitlementDao', array('searchEmployeeLeaveEntitlement'));
        $leaveEntitlementDao->expects($this->once())
                                ->method('searchEmployeeLeaveEntitlement')
                                ->with(array(1, 2), array(1, 1), 1, 2)
                                ->will($this->returnValue($employeeLeaveEntitlementCollection));

        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
        $results = $leaveEntitlementService->searchEmployeeLeaveEntitlement(array(1, 2), array(1, 1), 1, 2);
        
        $this->assertEquals($employeeLeaveEntitlementCollection, $results);

    }
    
    public function testSaveEmployeeLeaveEntitlementCollection() {

        $employeeLeaveEntitlementCollection = new Doctrine_Collection('EmployeeLeaveEntitlement');
        $employeeLeaveEntitlementList = array();
        
        $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
        $employeeLeaveEntitlement->setLeaveTypeId(1);
        $employeeLeaveEntitlement->setLeavePeriodId(1);
        $employeeLeaveEntitlement->setEmployeeId(1);
        $employeeLeaveEntitlement->setNoOfDaysAllotted(14);
        $employeeLeaveEntitlement->setLeaveBroughtForward(2);
        $employeeLeaveEntitlement->setLeaveCarriedForward(5);
        
        $employeeLeaveEntitlementCollection->add($employeeLeaveEntitlement);
        $employeeLeaveEntitlementList[] = $employeeLeaveEntitlement;
        
        $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
        $employeeLeaveEntitlement->setLeaveTypeId(1);
        $employeeLeaveEntitlement->setLeavePeriodId(1);
        $employeeLeaveEntitlement->setEmployeeId(2);
        $employeeLeaveEntitlement->setNoOfDaysAllotted(20);
        $employeeLeaveEntitlement->setLeaveBroughtForward(3);
        $employeeLeaveEntitlement->setLeaveCarriedForward(4);
        
        $employeeLeaveEntitlementCollection->add($employeeLeaveEntitlement);
        $employeeLeaveEntitlementList[] = $employeeLeaveEntitlement;
        
        $leaveEntitlementDao = $this->getMock('LeaveEntitlementDao', array('saveEmployeeLeaveEntitlementCollection'));
        $leaveEntitlementDao->expects($this->once())
                                ->method('saveEmployeeLeaveEntitlementCollection')
                                ->with($employeeLeaveEntitlementList)
                                ->will($this->returnValue($employeeLeaveEntitlementCollection));

        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
        $results = $leaveEntitlementService->saveEmployeeLeaveEntitlementCollection($employeeLeaveEntitlementList);
        
        $this->assertEquals($employeeLeaveEntitlementCollection, $results);

    }
    
//    /**
//     * Test isEmployeeRequestProtectLeaveQuota For true Result
//     */
//    public function testIsEmployeeRequestProtectLeaveQuotaForTrue() {
//        
//        $requestedLeaveDays = 2;
//        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');
//        $parameters = array($leaveEntitlements[0]->getEmployeeId(), $leaveEntitlements[0]->getLeaveTypeId(), $leaveEntitlements[0]->getLeavePeriodId());
//        
//        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('getEmployeeLeaveEntitlement'));
//        $leaveEntitlementDao->expects($this->once())
//                            ->method('getEmployeeLeaveEntitlement')
//                            ->with($parameters[0], $parameters[1], $parameters[2])
//                            ->will($this->returnValue($leaveEntitlements[0]));
//        $this->leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
//        
//        $this->leaveEntitlementService = $this->getMock('LeaveEntitlementService', array('getEmployeeLeaveEntitlementDays'));
//        $this->leaveEntitlementService->expects($this->once())
//                                ->method('getEmployeeLeaveEntitlementDays')
//                                ->with($parameters[0], $parameters[1], $parameters[2])
//                                ->will($this->returnValue($leaveEntitlements[0]->getNoOfDaysAllotted()));
//
//        $leaveRequestService = $this->getMock('LeaveRequestService', array('getNumOfAvaliableLeave'));
//        $leaveRequestService->expects($this->once())
//                            ->method('getNumOfAvaliableLeave')
//                            ->with($parameters[0], $parameters[1])
//                            ->will($this->returnValue(15));        
//
//        
//        $this->leaveEntitlementService->setLeaveRequestService($leaveRequestService);
//        
//        
//        
//        
//        $result = $this->leaveEntitlementService->isEmployeeRequestProtectLeaveQuota($requestedLeaveDays, $leaveEntitlements[0]);
//        $this->assertTrue($result);
//        
//    }
//    
//    /**
//     * Test isEmployeeRequestProtectLeaveQuota For false Result
//     */
//    public function testIsEmployeeRequestProtectLeaveQuotaForFalse() {
//        
//        $requestedLeaveDays = 6;
//        $leaveEntitlements  = TestDataService::loadObjectList('EmployeeLeaveEntitlement', $this->fixture, 'EmployeeLeaveEntitlement');
//        $parameters = array($leaveEntitlements[0]->getEmployeeId(), $leaveEntitlements[0]->getLeaveTypeId(), $leaveEntitlements[0]->getLeavePeriodId());
//        
//        $leaveEntitlementService = $this->getMock('LeaveEntitlementService', array('getEmployeeLeaveEntitlementDays'));
//        $leaveEntitlementService->expects($this->once())
//                                ->method('getEmployeeLeaveEntitlementDays')
//                                ->with($parameters[0], $parameters[1], $parameters[2])
//                                ->will($this->returnValue($leaveEntitlements[0]->getNoOfDaysAllotted()));
//
//        $leaveEntitlementDao    = $this->getMock('LeaveEntitlementDao', array('getEmployeeLeaveEntitlement'));
//        $leaveEntitlementDao->expects($this->once())
//                            ->method('getEmployeeLeaveEntitlement')
//                            ->with($parameters[0], $parameters[1], $parameters[2])
//                            ->will($this->returnValue($leaveEntitlements[0]));
//        
//        $leaveRequestService = $this->getMock('LeaveRequestService', array('getNumOfAvaliableLeave'));
//        $leaveRequestService->expects($this->once())
//                            ->method('getNumOfAvaliableLeave')
//                            ->with($parameters[0], $parameters[1])
//                            ->will($this->returnValue(15));
//        
//        $leaveEntitlementService->setLeaveEntitlementDao($leaveEntitlementDao);
//        $leaveEntitlementService->setLeaveRequestService($leaveRequestService);
//        
//        
//        
//        
//        
//        
//        $result = $leaveEntitlementService->isEmployeeRequestProtectLeaveQuota($requestedLeaveDays, $leaveEntitlements[0]);
//        $this->assertFalse($result);
//        
//    }
    
}