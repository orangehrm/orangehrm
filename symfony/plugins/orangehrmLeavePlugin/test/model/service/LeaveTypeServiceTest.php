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
 class LeaveTypeServiceTest extends PHPUnit_Framework_TestCase{
    
    private $leaveTypeService;
    protected $fixture;

    /**
     * PHPUnit setup function
     */
    public function setup() {
            
        $this->leaveTypeService =   new LeaveTypeService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveTypeService.yml';
            
    }
    
    /* Tests for getLeaveTypeList() */

    public function testGetLeaveTypeList() {

        $leaveTypeList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set1');

        $leaveTypeDao = $this->getMock('LeaveTypeDao', array('getLeaveTypeList'));
        $leaveTypeDao->expects($this->once())
                     ->method('getLeaveTypeList')
                     ->will($this->returnValue($leaveTypeList));

        $this->leaveTypeService->setLeaveTypeDao($leaveTypeDao);
        $returnedLeaveTypeList = $this->leaveTypeService->getLeaveTypeList();
        
        $this->assertEquals(5, count($returnedLeaveTypeList));
        
        foreach ($returnedLeaveTypeList as $leaveType) {
            $this->assertTrue($leaveType instanceof LeaveType);
        }

    }

    public function testGetLeaveTypeListWithOperationalCountryId() {

        $leaveTypeList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set1');

        $leaveTypeDao = $this->getMock('LeaveTypeDao', array('getLeaveTypeList'));
        $leaveTypeDao->expects($this->once())
                     ->method('getLeaveTypeList')
                     ->with($this->equalTo(2))
                     ->will($this->returnValue($leaveTypeList));

        $this->leaveTypeService->setLeaveTypeDao($leaveTypeDao);
        $returnedLeaveTypeList = $this->leaveTypeService->getLeaveTypeList(2);
        
        $this->assertEquals(5, count($returnedLeaveTypeList));
        
        foreach ($returnedLeaveTypeList as $leaveType) {
            $this->assertTrue($leaveType instanceof LeaveType);
        }            
    }
    
    /* Tests for saveLeaveType() */

    public function testSaveLeaveType() {

        $leaveTypeList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set1');
        $leaveType = $leaveTypeList[0];

        $leaveTypeDao = $this->getMock('LeaveTypeDao', array('saveLeaveType'));
        $leaveTypeDao->expects($this->once())
                     ->method('saveLeaveType')
                     ->with($leaveType)
                     ->will($this->returnValue(true));

        $this->leaveTypeService->setLeaveTypeDao($leaveTypeDao);

        $this->assertTrue($this->leaveTypeService->saveLeaveType($leaveType));

    }

    /* Tests for readLeaveType */

    public function testReadLeaveType() {

        $leaveTypeList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'set1');
        $leaveType = $leaveTypeList[0];

        $leaveTypeDao = $this->getMock('LeaveTypeDao', array('readLeaveType'));
        $leaveTypeDao->expects($this->once())
                     ->method('readLeaveType')
                     ->with('LTY001')
                     ->will($this->returnValue($leaveType));

        $this->leaveTypeService->setLeaveTypeDao($leaveTypeDao);

        $leaveType = $this->leaveTypeService->readLeaveType('LTY001');

        $this->assertTrue($leaveType instanceof LeaveType);

    }


    
 }