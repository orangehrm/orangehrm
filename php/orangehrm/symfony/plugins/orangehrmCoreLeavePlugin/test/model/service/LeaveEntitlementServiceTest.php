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

        $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
        $employeeLeaveEntitlement->setNoOfDaysAllotted(14);
        $employeeLeaveEntitlement->setLeaveBroughtForward(2);
        $employeeLeaveEntitlement->setLeaveCarriedForward(5);

        $leaveEntitlementService = $this->getMock('LeaveEntitlementService', array('readEmployeeLeaveEntitlement'));
        $leaveEntitlementService->expects($this->once())
                                ->method('readEmployeeLeaveEntitlement')
                                ->with(1, 'LTY001', 1)
                                ->will($this->returnValue($employeeLeaveEntitlement));

        $leaveRequestService = $this->getMock('LeaveRequestService', array('getScheduledLeavesSum'));
        $leaveRequestService->expects($this->once())
                            ->method('getScheduledLeavesSum')
                            ->with(1, 'LTY001', 1)
                            ->will($this->returnValue(3));

        $leaveEntitlementService->setLeaveRequestService($leaveRequestService);

        $this->assertEquals(8, $leaveEntitlementService->getLeaveBalance(1, 'LTY001', 1));        

    }

    public function testGetLeaveBalanceEmptyRecords() {

        $leaveEntitlementService = $this->getMock('LeaveEntitlementService', array('readEmployeeLeaveEntitlement'));
        $leaveEntitlementService->expects($this->once())
                                ->method('readEmployeeLeaveEntitlement')
                                ->with(1, 'LTY001', 1)
                                ->will($this->returnValue(null));

        $leaveRequestService = $this->getMock('LeaveRequestService', array('getScheduledLeavesSum'));
        $leaveRequestService->expects($this->once())
                            ->method('getScheduledLeavesSum')
                            ->with(1, 'LTY001', 1)
                            ->will($this->returnValue(0));

        $leaveEntitlementService->setLeaveRequestService($leaveRequestService);

        $this->assertEquals('0.00', $leaveEntitlementService->getLeaveBalance(1, 'LTY001', 1));

    }
    
}