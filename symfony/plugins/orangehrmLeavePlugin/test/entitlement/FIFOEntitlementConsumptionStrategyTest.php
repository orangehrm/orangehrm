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
 * Description of FIFOEntitlementConsumptionStrategyTest
 * @group Leave 
 */
class FIFOEntitlementConsumptionStrategyTest extends PHPUnit_Framework_TestCase {

    private $strategy;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->strategy = new FIFOEntitlementConsumptionStrategy();

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/FIFOEntitlementStrategy.yml';
        TestDataService::populate($this->fixture);        
    }

    /**
     * No entitlements available for leave type
     */
    public function testGetAvailableEntitlementsNone() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        ;
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        ;
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlements = array();

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue(!$results);
    }

    /**
     * One entitlement available, but not enough for leave request
     */
    public function testGetAvailableEntitlementsOne1() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-13',
            'to_date' => '2012-11-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array(
            $entitlement1
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results == false);
    }

    /**
     * One entitlement with enough days, but not possible to support given leave dates
     */
    public function testGetAvailableEntitlementsOne21() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(2);
        $entitlement1->setEmpNumber($empNumber);
        $entitlement1->setNoOfDays(5);
        $entitlement1->setLeaveTypeId($leaveType);
        $entitlement1->setFromDate('2012-09-01');
        $entitlement1->setToDate('2012-09-15');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlements = array(
            $entitlement1
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue(array_values($entitlements)));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results == false);
    }

    /**
     * One entitlement with enough days but not enough available
     */
    public function testGetAvailableEntitlementsOne211() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(2);
        $entitlement1->setEmpNumber($empNumber);
        $entitlement1->setNoOfDays(5);
        $entitlement1->setDaysUsed(2);
        $entitlement1->setLeaveTypeId($leaveType);
        $entitlement1->setFromDate('2012-09-01');
        $entitlement1->setToDate('2012-09-19');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlements = array(
            $entitlement1
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue(array_values($entitlements)));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results == false);
    }

    /**
     * One entitlement, enough to satisfy leave request
     */
    public function testGetAvailableEntitlementsOne22() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(2);
        $entitlement1->setEmpNumber($empNumber);
        $entitlement1->setNoOfDays(5);
        $entitlement1->setLeaveTypeId($leaveType);
        $entitlement1->setFromDate('2012-09-13');
        $entitlement1->setToDate('2012-11-28');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlements = array(
            $entitlement1
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue(array_values($entitlements)));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15', '2012-09-16'), array(array(2 => 1), array(2 => 1), array(2 => 1), array(2 => 1)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * First leave date before entitlement start date
     */
    public function testGetAvailableEntitlementsOneEdges() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(2);
        $entitlement1->setEmpNumber($empNumber);
        $entitlement1->setNoOfDays(5);
        $entitlement1->setLeaveTypeId($leaveType);
        $entitlement1->setFromDate('2012-09-14');
        $entitlement1->setToDate('2012-11-28');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlements = array(
            $entitlement1
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue(array_values($entitlements)));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results === false);
    }

    /**
     * Two entitlements, verify assigned to first expiring entitlemenmt
     */
    public function testGetAvailableEntitlementsTwo() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(6);
        $entitlement1->setEmpNumber(1);
        $entitlement1->setNoOfDays(5);
        $entitlement1->setLeaveTypeId(2);
        $entitlement1->setFromDate('2012-09-11');
        $entitlement1->setToDate('2012-09-15');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->setId(2);
        $entitlement2->setEmpNumber(1);
        $entitlement2->setNoOfDays(4);
        $entitlement2->setLeaveTypeId(2);
        $entitlement2->setFromDate('2012-09-12');
        $entitlement2->setToDate('2012-09-18');
        $entitlement2->setCreditedDate('2012-05-01');
        $entitlement2->setNote('Created by Unit test');
        $entitlement2->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement2->setDeleted(0);

        $entitlements = array(
            $entitlement1, $entitlement2
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-15', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15'), array(array(6 => 1), array(6 => 1), array(6 => 1)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * Two entitlements, first one not enough. Verify assigned first to earlier expiring entitlement and the rest to the 
     * next entitlement   
     */
    public function testGetAvailableEntitlementsTwo1() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(6);
        $entitlement1->setEmpNumber(1);
        $entitlement1->setNoOfDays(2);
        $entitlement1->setLeaveTypeId(2);
        $entitlement1->setFromDate('2012-09-11');
        $entitlement1->setToDate('2012-09-15');
        $entitlement1->setCreditedDate('2012-05-01');
        $entitlement1->setNote('Created by Unit test');
        $entitlement1->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement1->setDeleted(0);

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->setId(2);
        $entitlement2->setEmpNumber(1);
        $entitlement2->setNoOfDays(4);
        $entitlement2->setLeaveTypeId(2);
        $entitlement2->setFromDate('2012-09-12');
        $entitlement2->setToDate('2012-09-18');
        $entitlement2->setCreditedDate('2012-05-01');
        $entitlement2->setNote('Created by Unit test');
        $entitlement2->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $entitlement2->setDeleted(0);

        $entitlements = array(
            $entitlement1, $entitlement2
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15', '2012-09-16'), array(array(6 => 1), array(6 => 1), array(2 => 1), array(2 => 1)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * Five entitlements, first two and 4th not have dates, verify assigned to 3rd and 5th
     */
    public function testGetAvailableEntitlementsFive1() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-01',
            'to_date' => '2012-09-21',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->fromArray(array(
            'id' => 2,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-02',
            'to_date' => '2012-09-31',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement3 = new LeaveEntitlement();
        $entitlement3->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-13',
            'to_date' => '2012-11-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement4 = new LeaveEntitlement();
        $entitlement4->fromArray(array(
            'id' => 4,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-11',
            'to_date' => '2012-11-30',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement5 = new LeaveEntitlement();
        $entitlement5->fromArray(array(
            'id' => 5,
            'emp_number' => 1,
            'no_of_days' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-12-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array(
            $entitlement1, $entitlement2, $entitlement3, $entitlement4, $entitlement5
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15', '2012-09-16'), array(array(3 => 1), array(3 => 1), array(5 => 1), array(5 => 1)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * Five entitlements, first one not available for first date.
     * First date assigned to second entitlement
     * Second date assigned to third entitlement
     * Third date and forth date assigned to first entitlement
     */
    public function testGetAvailableEntitlementsFive2() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 0,
            'leave_type_id' => 2,
            'from_date' => '2012-09-15',
            'to_date' => '2012-09-21',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->fromArray(array(
            'id' => 2,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-02',
            'to_date' => '2012-09-31',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement3 = new LeaveEntitlement();
        $entitlement3->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-13',
            'to_date' => '2012-11-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement4 = new LeaveEntitlement();
        $entitlement4->fromArray(array(
            'id' => 4,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-11',
            'to_date' => '2012-11-30',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement5 = new LeaveEntitlement();
        $entitlement5->fromArray(array(
            'id' => 5,
            'emp_number' => 1,
            'no_of_days' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-12-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array(
            $entitlement1, $entitlement2, $entitlement3, $entitlement4, $entitlement5
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15', '2012-09-16'), array(array(2 => 1), array(3 => 1), array(1 => 1), array(1 => 1)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * Five entitlements, first one not available for first date.
     * First date assigned to second entitlement
     * Second date assigned to third entitlement
     * Third date assigned to first (0.5) and third.
     * Forth date assigned to third and fifth
     */
    public function testGetAvailableEntitlementsFive3() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => NULL, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2, $leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2.5,
            'leave_type_id' => 2,
            'from_date' => '2012-09-15',
            'to_date' => '2012-09-21',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->fromArray(array(
            'id' => 2,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-02',
            'to_date' => '2012-09-31',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement3 = new LeaveEntitlement();
        $entitlement3->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-13',
            'to_date' => '2012-11-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement4 = new LeaveEntitlement();
        $entitlement4->fromArray(array(
            'id' => 4,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-11',
            'to_date' => '2012-11-30',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement5 = new LeaveEntitlement();
        $entitlement5->fromArray(array(
            'id' => 5,
            'emp_number' => 1,
            'no_of_days' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-12-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array(
            $entitlement1, $entitlement2, $entitlement3, $entitlement4, $entitlement5
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-16', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14', '2012-09-15', '2012-09-16'), array(array(2 => 1), array(3 => 1), array(1 => 0.5, 3 => 0.5), array(3 => 0.5, 5 => 0.5)));

        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }

    /**
     * Five entitlements, first one not available for first date.
     * First date assigned to second entitlement
     * Second date assigned to third entitlement
     * Third date assigned to first (0.5) and third.
     * Forth date assigned to third and fifth
     */
    public function testGetAvailableEntitlementsFive4() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-14', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => 3, 'date' => '2012-09-15', 'length_days' => 1));
        $leave4 = new Leave();
        $leave4->fromArray(array('id' => 4, 'date' => '2012-09-16', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $existingLeaveDates = array($leave3, $leave4);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2.5,
            'leave_type_id' => 2,
            'from_date' => '2012-09-15',
            'to_date' => '2012-09-21',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->fromArray(array(
            'id' => 2,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-02',
            'to_date' => '2012-09-31',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement3 = new LeaveEntitlement();
        $entitlement3->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-13',
            'to_date' => '2012-11-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement4 = new LeaveEntitlement();
        $entitlement4->fromArray(array(
            'id' => 4,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-11',
            'to_date' => '2012-11-30',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlement5 = new LeaveEntitlement();
        $entitlement5->fromArray(array(
            'id' => 5,
            'emp_number' => 1,
            'no_of_days' => 3,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-12-28',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array(
            $entitlement1, $entitlement2, $entitlement3, $entitlement4, $entitlement5
        );

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements', 'getLinkedLeaveRequests'));
        $mockService->expects($this->any())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-13', '2012-09-14', 'to_date', 'ASC')
                ->will($this->returnValue($entitlements));

        $mockService->expects($this->any())
                ->method('getLinkedLeaveRequests')
                ->with(array(1, 2, 3, 4, 5), array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL, Leave::LEAVE_STATUS_LEAVE_APPROVED))
                ->will($this->returnValue($existingLeaveDates));

        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->getAvailableEntitlements($empNumber, $leaveType, $leaveDates);
        $this->assertTrue($results !== false);
        $current = array_combine(array('2012-09-13', '2012-09-14'), array(array(2 => 1), array(3 => 1)));

        $changes = array_combine(array(3, 4), array(array(1 => 0.5, 3 => 0.5), array(3 => 0.5, 5 => 0.5)));

        $this->verifyEntitlements($results, $current, $changes);
    }

    public function testHandleLeaveStatusChangeNoEntitlements() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        ;
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        ;
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlements = array();

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->exactly(2))
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertTrue(!$results);

        // Assign, $allowNoEntitlements = true 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $expected = array('current' => array(), 'change' => array());
        $this->assertEquals($expected, $results);
    }

    public function testHandleLeaveStatusChangeOneEntitlementBeforeApply() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 0,
            'leave_type_id' => 2,
            'from_date' => '2012-09-01',
            'to_date' => '2012-09-11',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertTrue(!$results);      
    }
    
    public function testHandleLeaveStatusChangeOneEntitlementBeforeAssign() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 1,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 0,
            'leave_type_id' => 2,
            'from_date' => '2012-09-01',
            'to_date' => '2012-09-11',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);
        
        // Assign, $allowNoEntitlements = true 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);               
        $this->assertTrue(is_array($results), $results);      
        
        $current = array('2012-09-11' => array(1 => 1));
        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);        
    }    
    
    public function testHandleLeaveStatusChangeOneEntitlementAfterApply() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 4,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-12',
            'to_date' => '2012-09-13',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertTrue(!$results);      
    }    
    
    public function testHandleLeaveStatusChangeOneEntitlementAfterAssign() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 3,
            'emp_number' => 1,
            'no_of_days' => 4,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-12',
            'to_date' => '2012-09-13',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);
        
        // Assign, $allowNoEntitlements = true 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);               
        $this->assertTrue(is_array($results), $results);      
        
        $current = array('2012-09-12' => array(3 => 1));
        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);           
    }        
    
    public function testHandleLeaveStatusChangeOneEntitlementNotSufficientApply() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        
        $leaveDates = array($leave1, $leave2, $leave3);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-13', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $this->assertTrue(!$results);

    }    
    
    public function testHandleLeaveStatusChangeOneEntitlementNotSufficientAssign() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));
        $leave3 = new Leave();
        $leave3->fromArray(array('id' => NULL, 'date' => '2012-09-13', 'length_days' => 1));
        
        $leaveDates = array($leave1, $leave2, $leave3);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 2,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-13', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);


        // Assign, $allowNoEntitlements = true 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates, true);
        $current = array('2012-09-11' => array(6 => 1));
        $changes = array();

        $this->verifyEntitlements($results, $current, $changes);
    }
    
    public function testHandleLeaveStatusChangeOneEntitlementExactAmount() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 3,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);

        $expected = array('current' => array('2012-09-11' => array(6=>1), '2012-09-12' => array(6=>1)), 'change' => array());
        $this->assertEquals($expected, $results);
    }     
    
    public function testHandleLeaveStatusChangeOneEntitlementExtra() {

        $empNumber = 1;
        $leaveType = 2;

        $leave1 = new Leave();
        ;
        $leave1->fromArray(array('id' => NULL, 'date' => '2012-09-11', 'length_days' => 1));
        $leave2 = new Leave();
        ;
        $leave2->fromArray(array('id' => NULL, 'date' => '2012-09-12', 'length_days' => 1));

        $leaveDates = array($leave1, $leave2);

        $entitlement1 = new LeaveEntitlement();
        $entitlement1->fromArray(array(
            'id' => 6,
            'emp_number' => 1,
            'no_of_days' => 4,
            'days_used' => 1,
            'leave_type_id' => 2,
            'from_date' => '2012-09-10',
            'to_date' => '2012-09-14',
            'credited_date' => '2012-05-01',
            'note' => 'Created by Unit test',
            'entitlement_type' => LeaveEntitlement::ENTITLEMENT_TYPE_ADD,
            'deleted' => 0));

        $entitlements = array($entitlement1);

        $mockService = $this->getMock('LeaveEntitlementService', array('getValidLeaveEntitlements'));
        $mockService->expects($this->once())
                ->method('getValidLeaveEntitlements')
                ->with($empNumber, $leaveType, '2012-09-11', '2012-09-12', 'from_date', 'ASC')
                ->will($this->returnValue($entitlements));
        $this->strategy->setLeaveEntitlementService($mockService);

        // Apply, $allowNoEntitlements = false 
        $results = $this->strategy->handleLeaveCreate($empNumber, $leaveType, $leaveDates);
        $expected = array('current' => array('2012-09-11' => array(6=>1), '2012-09-12' => array(6=>1)), 'change' => array());
        $this->assertEquals($expected, $results);
    }      
    
    public function testGetLeaveWithoutEntitlementDateLimitsForLeaveBalance() {
        
        $mockService = $this->getMock('LeavePeriodService', array('getCurrentLeavePeriodByDate'));
        $mockService->expects($this->any())
                ->method('getCurrentLeavePeriodByDate')
                ->will($this->returnValue(array('2012-01-01', '2012-12-31')));
        
        $this->strategy->setLeavePeriodService($mockService);
        
        $result = $this->strategy->getLeaveWithoutEntitlementDateLimitsForLeaveBalance('2012-01-03', '2012-02-02');
        
        $this->assertEquals(2, count($result));
        
        $this->assertEquals('2012-01-01', $result[0]);
        $this->assertEquals('2012-12-31', $result[1]);                        
    }
   

    
    // Check leap year
    
    // check does not affect entitlements unless both from/to date match previous from/to date
    
    // Check leave assigned to old entitlements are redistributed
    
    protected function _compareEntitlements($expected, $results) {
        $this->assertEquals(count($expected), count($results));
        
        for ($i = 0; $i < count($expected); $i++) {         
            $this->_compareEntitlement($expected[$i], $results[$i]);
        }
    }
    
    protected function _compareEntitlement($expected, $actual) {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getEmpNumber(), $actual->getEmpNumber());
        $this->assertEquals($expected->getNoOfDays(), $actual->getNoOfDays());
        $this->assertEquals($expected->getLeaveTypeId(), $actual->getLeaveTypeId());
        $this->assertEquals($expected->getFromDate(), $actual->getFromDate());
        $this->assertEquals($expected->getToDate(), $actual->getToDate());
        $this->assertEquals($expected->getCreditedDate(), $actual->getCreditedDate());
        $this->assertEquals($expected->getNote(), $actual->getNote());
        $this->assertEquals($expected->getEntitlementType(), $actual->getEntitlementType());
        $this->assertEquals($expected->getDeleted(), $actual->getDeleted());
        
    }
    
    protected function getDeletedEntitlements() {
        $deletedEntitlements = array();
        $entitlements = Doctrine_Query::create()
                        ->from('LeaveEntitlement le')
                        ->where('le.deleted = 1')
                        ->orderBy('le.id ASC')
                        ->execute();
        
        foreach ($entitlements as $e) {
            $deletedEntitlements[$e->getId()] = $e;
        }        
        
        return $deletedEntitlements;
    }
    
    protected function getEntitlements() {
        $deletedEntitlements = array();
        $entitlements = Doctrine_Query::create()
                        ->from('LeaveEntitlement le')
                        ->where('le.deleted = 0')
                        ->orderBy('le.id ASC')
                        ->execute();
        
        foreach ($entitlements as $e) {
            $deletedEntitlements[$e->getId()] = $e;
        }        
        
        return $deletedEntitlements;
    }    
        
    
    /**
     * Verify entitlement results
     * @param type $results
     * @param type $current
     * @param type $change
     */
    public function verifyEntitlements($results, $current, $change) {

        $this->assertTrue(isset($results['current']));
        $this->assertTrue(is_array($results['current']));
        $this->assertEquals($current, $results['current']);

        $this->assertTrue(isset($results['change']));
        $this->assertTrue(is_array($results['change']));
        $this->assertEquals($change, $results['change']);
    }

}

