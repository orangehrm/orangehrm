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
 * @group Leave
 */
class WorkScheduleServiceTest extends PHPUnit_Framework_TestCase {

    private $service;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->service = new WorkScheduleService();
    }
    
    /**
     * Test for work schedule class not defined
     */
    public function testGetWorkScheduleNotDefined() {
        
        $mockService = $this->getMock('LeaveConfigurationService', array('getWorkScheduleImplementation'));
        $mockService->expects($this->once())
                    ->method('getWorkScheduleImplementation')
                    ->will($this->returnValue(NULL)); 
        
        $this->service->setLeaveConfigurationService($mockService);
        
        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
            
        }
    }   
    
    /**
     * Test for unavailable work schedule class
     */
    public function testGetWorkScheduleClassNotFound() {
        
        $mockService = $this->getMock('LeaveConfigurationService', array('getWorkScheduleImplementation'));
        $mockService->expects($this->once())
                    ->method('getWorkScheduleImplementation')
                    ->will($this->returnValue('xYzNotAvailable')); 
        
        $this->service->setLeaveConfigurationService($mockService);
        
        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
            
        }
    }     
    
    /**
     * Test for workschedule class not implementing work schedule interface
     */
    public function testGetWorkScheduleInvalidClass() {
        
        $mockService = $this->getMock('LeaveConfigurationService', array('getWorkScheduleImplementation'));
        $mockService->expects($this->once())
                    ->method('getWorkScheduleImplementation')
                    ->will($this->returnValue('TestWorkScheduleInvalidClass')); 
        
        $this->service->setLeaveConfigurationService($mockService);
        
        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
            
        }
    }     
    
    /**
     * Test for exception thrown when construction work schedule class
     */
    public function testGetWorkScheduleClassExceptionInConstructor() {
        
        $mockService = $this->getMock('LeaveConfigurationService', array('getWorkScheduleImplementation'));
        $mockService->expects($this->once())
                    ->method('getWorkScheduleImplementation')
                    ->will($this->returnValue('TestWorkScheduleInvalidClassExceptionInConstructor')); 
        
        $this->service->setLeaveConfigurationService($mockService);
        
        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
            
        }
    }    
    
    public function testGetWorkSchedule() {
        
        $mockService = $this->getMock('LeaveConfigurationService', array('getWorkScheduleImplementation'));
        $mockService->expects($this->once())
                    ->method('getWorkScheduleImplementation')
                    ->will($this->returnValue('TestWorkScheduleValidClass')); 
        
        $this->service->setLeaveConfigurationService($mockService);
        
        $workSchedule = $this->service->getWorkSchedule(3);        
        $this->assertTrue($workSchedule instanceof TestWorkScheduleValidClass);
        $this->assertEquals(3, $workSchedule->getEmpNumber());        
    }       
}

class TestWorkScheduleInvalidClass {
    
}

class TestWorkScheduleInvalidClassExceptionInConstructor {
    
    public function __construct() {
        throw new Exception("Exception in constructor");
    }        

}

class TestWorkScheduleValidClass implements WorkScheduleInterface {
    
    protected $empNumber;
    
    public function getWorkShiftLength() {
        return 5;
    }

    public function setEmpNumber($empNumber) {
        $this->empNumber = $empNumber;
    }
    
    public function getEmpNumber() {
        return $this->empNumber;
    }

    public function isHalfDay($day) {
        
    }

    public function isHalfdayHoliday($day) {
        
    }

    public function isHoliday($day) {
        
    }

    public function isWeekend($day, $fullDay) {
        
    }

    public function getWorkShiftStartEndTime() {
        
    }
}

