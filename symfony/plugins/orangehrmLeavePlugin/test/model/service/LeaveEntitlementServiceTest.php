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
 *
 */

/**
 * LeaveEntitlementServiceTest
 * 
 * @group Leave 
 */
class LeaveEntitlementServiceTest extends PHPUnit_Framework_TestCase {

    private $service;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->service = new LeaveEntitlementService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlement.yml';        
    }
    
    public function testSearchLeaveEntitlements() {
        $leaveEntitlements  = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();

        $mockDao = $this->getMock('LeaveEntitlementDao', array('searchLeaveEntitlements'));
        $mockDao->expects($this->once())
                    ->method('searchLeaveEntitlements')
                    ->with($parameterHolder)
                    ->will($this->returnValue($leaveEntitlements));

        $this->service->setLeaveEntitlementDao($mockDao);
        $results = $this->service->searchLeaveEntitlements($parameterHolder);      
        
        $this->assertEquals($leaveEntitlements, $results);
    }
    
    public function testSaveLeaveEntitlement() {
        $leaveEntitlements  = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $leaveEntitlement = $leaveEntitlements[0];

        $mockDao = $this->getMock('LeaveEntitlementDao', array('saveLeaveEntitlement'));
        $mockDao->expects($this->once())
                    ->method('saveLeaveEntitlement')
                    ->with($leaveEntitlement)
                    ->will($this->returnValue($leaveEntitlement));

        $this->service->setLeaveEntitlementDao($mockDao);
        $result = $this->service->saveLeaveEntitlement($leaveEntitlement);      
        
        $this->assertEquals($leaveEntitlement, $result);        
    }
    
    public function testDeleteLeaveEntitlements() {
        $ids = array(2, 33, 12);

        $leaveEntitlement1 = new LeaveEntitlement();
        $leaveEntitlement1->fromArray(array('id' => 2, 'emp_number' => 1, 'no_of_days' => 3, 'days_used' => 0));
        
        $leaveEntitlement2 = new LeaveEntitlement();
        $leaveEntitlement2->fromArray(array('id' => 33, 'emp_number' => 1, 'no_of_days' => 3, 'days_used' => 0));
        
        $leaveEntitlement3 = new LeaveEntitlement();
        $leaveEntitlement3->fromArray(array('id' => 12, 'emp_number' => 1, 'no_of_days' => 3, 'days_used' => 0));   
        
        $leaveEntitlements = array($leaveEntitlement1, $leaveEntitlement2, $leaveEntitlement3);
        
        
        $mockDao = $this->getMock('LeaveEntitlementDao', array('deleteLeaveEntitlements', 'searchLeaveEntitlements'));
        $mockDao->expects($this->once())
                    ->method('deleteLeaveEntitlements')
                    ->with($ids)
                    ->will($this->returnValue(count($ids)));
        
        $mockDao->expects($this->once())
                    ->method('searchLeaveEntitlements')
                    ->will($this->returnValue($leaveEntitlements));
        

        $this->service->setLeaveEntitlementDao($mockDao);
        $result = $this->service->deleteLeaveEntitlements($ids);      
        
        $this->assertEquals(count($ids), $result);            
    }
    
    public function testGetLeaveEntitlement() {
        $id = 2;
        $leaveEntitlements = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $leaveEntitlement = $leaveEntitlements[0];

        $mockDao = $this->getMock('LeaveEntitlementDao', array('getLeaveEntitlement'));
        $mockDao->expects($this->once())
                ->method('getLeaveEntitlement')
                ->with($id)
                ->will($this->returnValue($leaveEntitlement));

        $this->service->setLeaveEntitlementDao($mockDao);
        $result = $this->service->getLeaveEntitlement($id);

        $this->assertEquals($leaveEntitlement, $result);
    }

    public function testGetMatchingEntitlements() {

        $leaveEntitlements = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $leaveEntitlement = $leaveEntitlements[0];
        $empNumber = $leaveEntitlement->getEmpNumber();
        $leaveTypeId = $leaveEntitlement->getLeaveTypeId();
        $fromDate = $leaveEntitlement->getFromDate();
        $toDate = $leaveEntitlement->getToDate();
        
        $mockDao = $this->getMock('LeaveEntitlementDao', array('getMatchingEntitlements'));
        $mockDao->expects($this->once())
                ->method('getMatchingEntitlements')
                ->with($empNumber, $leaveTypeId, $fromDate, $toDate)
                ->will($this->returnValue($leaveEntitlement));

        $this->service->setLeaveEntitlementDao($mockDao);
        $result = $this->service->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);

        $this->assertEquals($leaveEntitlement, $result);        
    }
    
    public function testGetLeaveEntitlementTypeList() {

        $sortField = 'id';
        $sortOrder = 'DESC';
        $leaveEntitlementTypeList = new Doctrine_Collection('LeaveEntitlementType');
        
        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $leaveEntitlementType->setName('ADD');
        $leaveEntitlementTypeList[] = $leaveEntitlementType;        
        
        $mockDao = $this->getMock('LeaveEntitlementDao', array('getLeaveEntitlementTypeList'));
        $mockDao->expects($this->once())
                ->method('getLeaveEntitlementTypeList')
                ->with($sortField, $sortOrder)
                ->will($this->returnValue($leaveEntitlementTypeList));

        $this->service->setLeaveEntitlementDao($mockDao);
        $result = $this->service->getLeaveEntitlementTypeList($sortField, $sortOrder);

        $this->assertEquals($leaveEntitlementTypeList, $result);        
    }    
}
