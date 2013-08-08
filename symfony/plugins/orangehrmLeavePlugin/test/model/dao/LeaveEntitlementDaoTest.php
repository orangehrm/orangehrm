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
class LeaveEntitlementDaoTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LeaveEntitlementDao 
     */
    private $dao;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->dao = new LeaveEntitlementDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlement.yml';
        TestDataService::populate($this->fixture);
    }
    
    /* Default search - should return all non-deleted records - sorted by fromdate */
    public function testSearchLeaveEntitlementsWithNoFilters() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[5], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);                
    }      
    
    /* Test sorting */
    public function testSearchLeaveEntitlementsSorting() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        // sort by leave type name
        $parameterHolder->setOrderBy('Desc');
        $parameterHolder->setOrderField('leave_type');

        $expected = array($entitlementList[1], $entitlementList[2], $entitlementList[3], $entitlementList[0], $entitlementList[5]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);  
        
        
        // sort by employee name
        $parameterHolder->setOrderBy('Asc');
        $parameterHolder->setOrderField('employee_name');

        $expected = array($entitlementList[1], $entitlementList[5], $entitlementList[0], $entitlementList[2], $entitlementList[3]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);          
        
    }  
    
    /* Test sorting */
    public function testSearchLeaveEntitlementsWithIdList() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $parameterHolder->setIdList(array(1,2));
        
        $expected = array($entitlementList[0],$entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);           
        
    }       
    
    public function testSearchLeaveEntitlementsWithAllFilters() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        $parameterHolder->setEmpNumber(2);
        $parameterHolder->setLeaveTypeId(6);
        $parameterHolder->setFromDate('2013-08-01');
        $parameterHolder->setToDate('2013-10-02');
        $expected = array($entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);           
    }  
    
    public function testSearchLeaveEntitlementsByValidDate() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        $parameterHolder->setValidDate('2013-08-06');
        
        $expected = array($entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $parameterHolder->setValidDate('2013-08-05');
        
        $expected = array($entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $parameterHolder->setValidDate('2013-09-01');
        
        $expected = array($entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $parameterHolder->setValidDate('2013-09-02');
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);        
        $this->assertEquals(1, count($results)) ;    
        
        $parameterHolder->setValidDate('2014-01-02');
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);        
        $this->assertEquals(0, count($results)) ;    
        
    }

    public function testSearchLeaveEntitlementsByLeaveType() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        $parameterHolder->setLeaveTypeId(2);

        $expected = array($entitlementList[2], $entitlementList[3]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->_compareEntitlements($expected, $results);          
        
        // Non existing leave type id
        $parameterHolder->setLeaveTypeId(21);

        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->assertEquals(0, count($results));

        // Leave type with no entitlements
        $parameterHolder->setLeaveTypeId(7);

        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->assertEquals(0, count($results));        
    }    
    
    public function testSearchLeaveEntitlementsByEmpNumber() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        // employee with multiple records
        $parameterHolder->setEmpNumber(1);
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);           
        
        // employee with one record
        $parameterHolder->setEmpNumber(2);
        $expected = array($entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);            
        
        // employee with no records
        $parameterHolder->setEmpNumber(4);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->assertEquals(0, count($results));        
        
        // non existing employee
        $parameterHolder->setEmpNumber(100);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->assertEquals(0, count($results));           
    }
    
    public function testSearchLeaveEntitlementsByDates() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        // date range with multiple records
        $parameterHolder->setFromDate('2012-03-01');
        $parameterHolder->setToDate('2012-07-01');
                
        $expected = array($entitlementList[2], $entitlementList[3]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);         
        
        // from date matching entitlement from date
        $parameterHolder->setFromDate('2012-04-04');
        $parameterHolder->setToDate('2012-04-05');        
        $expected = array($entitlementList[2]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);        
                
        $parameterHolder->setFromDate('2012-01-01');
        $parameterHolder->setToDate('2012-01-02');        
        $expected = array($entitlementList[0]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);   
        
        // to date matching entitlement to date
        $parameterHolder->setFromDate('2011-01-01');
        $parameterHolder->setToDate('2012-01-01');        
        $expected = array($entitlementList[0]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);        
        
        // from date matching entitlement to date
        $parameterHolder->setFromDate('2013-09-01');
        $parameterHolder->setToDate('2013-11-01');        
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        $this->assertEquals(0, count($results));
    }
    
    public function testSearchLeaveEntitlementsDeletedFlag() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        // default - non-deleted
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[5], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);          
        
        // only deleted
        $parameterHolder->setDeletedFlag(true);
        $expected = array($entitlementList[4]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);           
        
        // both deleted and non-deleted
        $parameterHolder->setDeletedFlag(NULL);
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[4], $entitlementList[5], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);         
    }
    
    public function testSearchByEntitlementType() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        // default - all non deleted
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[5], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);          
        
        // only entitlement type 1
        $parameterHolder->setEntitlementTypes(array(1));
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);           
        
        // only entitlement type 2
        $parameterHolder->setEntitlementTypes(array(2));
        $expected = array($entitlementList[5]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results); 
        
        // entitlement types 1 and 2
        $parameterHolder->setEntitlementTypes(array(1, 2));
        $expected = array($entitlementList[0], $entitlementList[2], $entitlementList[3], $entitlementList[5], $entitlementList[1]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);          
    }    
    
    public function testSearchByEntitlementTypeWithOtherParams() {
        $parameterHolder = new LeaveEntitlementSearchParameterHolder();
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
         
        
        // only entitlement type 1 and leave type 1
        $parameterHolder->setEntitlementTypes(array(1));
        $parameterHolder->setLeaveTypeId(1);
        $expected = array($entitlementList[0]);
        $results = $this->dao->searchLeaveEntitlements($parameterHolder);
        
        $this->_compareEntitlements($expected, $results);                
    }    
    
    public function testGetLeaveEntitlement() {
        $id = 3;
        $leaveEntitlement = $this->dao->getLeaveEntitlement($id);
        
        $this->assertTrue($leaveEntitlement instanceof LeaveEntitlement);
        $fromDb = Doctrine_Query::create()
                                ->from('LeaveEntitlement le')
                                ->where('le.id = ? ', $id)
                                ->fetchOne();        
        
        $this->_compareEntitlement($fromDb, $leaveEntitlement);
        
        // non existing id
        $nonExisting = $this->dao->getLeaveEntitlement(111);
        $this->assertTrue(is_null($nonExisting));
    }
    
    public function testSaveLeaveEntitlementNew() {
        $leaveEntitlement = new LeaveEntitlement();
        //$leaveEntitlement->setId();
        $leaveEntitlement->setEmpNumber(1);
        $leaveEntitlement->setNoOfDays(12);
        $leaveEntitlement->setLeaveTypeId(2);
        $leaveEntitlement->setFromDate('2012-09-13');
        $leaveEntitlement->setToDate('2012-11-28');
        $leaveEntitlement->setCreditedDate('2012-05-01');
        $leaveEntitlement->setNote('Created by Unit test');
        $leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setDeleted(0);
        
        $savedObj = $this->dao->saveLeaveEntitlement($leaveEntitlement);
        $this->assertTrue($savedObj instanceof LeaveEntitlement);
        
        $savedId = $savedObj->getId();
        $this->assertTrue(!empty($savedId));
        
        $leaveEntitlement->setId($savedId);
        $this->_compareEntitlement($leaveEntitlement, $savedObj);
        
        $fromDb = Doctrine_Query::create()
                ->from('LeaveEntitlement le')
                ->where('le.id = ? ', $savedId)
                ->fetchOne();
        $this->_compareEntitlement($leaveEntitlement, $fromDb);
        
    }
    
    public function testGetValidLeaveEntitlements() {
        
        $entitlementList = TestDataService::loadObjectList('LeaveEntitlement', $this->fixture, 'LeaveEntitlement');
        
        $empNumber = 1;
        $leaveTypeId = 2;
        $fromDate = '2012-06-01';
        $toDate = '2012-06-05';
        $orderField = 'from_date';
        $order = 'ASC';
        
        $expected = array($entitlementList[2], $entitlementList[3]);
        $results = $this->dao->getValidLeaveEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate, $orderField, $order);
        $this->_compareEntitlements($expected, $results);
    }
    
    public function testSaveLeaveEntitlementUpdate() {
        $id = 3;
        $existingEntitlement = Doctrine_Query::create()
                                ->from('LeaveEntitlement le')
                                ->where('le.id = ? ', $id)
                                ->fetchOne();   
        
        $existingEntitlement->setNoOfDays(41);
        $savedObj = $this->dao->saveLeaveEntitlement($existingEntitlement);
        
        $this->_compareEntitlement($existingEntitlement, $savedObj);
        
        $fromDb = Doctrine_Query::create()
                    ->from('LeaveEntitlement le')
                    ->where('le.id = ? ', $id)
                    ->fetchOne();   
        
        $this->_compareEntitlement($existingEntitlement, $fromDb);        
    }    
    
    public function testDeleteLeaveEntitlementsMultiple() {
        $deleted = array(5);
                
        // delete with invalid ids
        $ids = array(21, 31);
        $count = $this->dao->deleteLeaveEntitlements($ids);        
        $this->assertEquals(0, $count);                
        $this->_verifyDeletedFlags($deleted);
               
        
        // delete multiple 
        $ids = array(2, 3);
        $count = $this->dao->deleteLeaveEntitlements($ids);        
        $this->assertEquals(2, $count);                
        
        // verify deleted
        $deleted = array_merge($deleted, $ids);        
        $this->_verifyDeletedFlags($deleted);
        
        // delete one
        $ids = array(4);
        
        $count = $this->dao->deleteLeaveEntitlements($ids);        
        $this->assertEquals(1, $count);
        
        // verify deleted
        $deleted = array_merge($deleted, $ids);        
        $this->_verifyDeletedFlags($deleted);
        
        // delete already deleted entry
        $count = $this->dao->deleteLeaveEntitlements(array(2));  
        $this->_verifyDeletedFlags($deleted);        
    }
        
    public function testGetLeaveBalance() {
        
        $unlinkedDateLimits = array('2001-01-01', '2020-01-01');
        
        $mockStrategy = $this->getMock('FIFOEntitlementConsumptionStrategy', array('getLeaveWithoutEntitlementDateLimitsForLeaveBalance'));
        $mockStrategy->expects($this->any())
                    ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
                    //->with($parameterHolder)
                    ->will($this->returnValue($unlinkedDateLimits));        
        // Using AsAt
        $this->dao->setLeaveEntitlementStrategy($mockStrategy);
        
        // As at before entitlement start:         
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
                
        // On Start Date
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-05');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // Between start end
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-10');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // On End date
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-09-01');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // After End
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-09-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // Using Date - Before
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01', '2013-08-01');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // On Start Date
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01', '2013-08-05');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // Between start end
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01', '2013-08-10');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // On End date
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01', '2013-09-01');
        $expected = new LeaveBalance(4, 1, 0, 0.5, 0);
        $this->assertEquals($expected, $balance);
        
        // After End
        $balance = $this->dao->getLeaveBalance(2, 6, '2013-08-01', '2013-09-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // Two entitlements - before both
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // First day of one entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-04-04');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // After first day of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-05-01');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);

        // First day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-05-05');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);

        // After First day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-05-09');

        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);

        // Last day of first entitlement        
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-06-01');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // After first entitlement                
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-06-02');
        $expected = new LeaveBalance(2, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);

        // On last day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-08-01');
        $expected = new LeaveBalance(2, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);

        // After second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-08-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // With date - before first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-03-01');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // on start date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-04-04');
        $expected = new LeaveBalance(1, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);
        
        // after first date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-05-01');
        $expected = new LeaveBalance(1, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);      
        
        // on first date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-05-05');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        

        // after first date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-05-09');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        

        // on last date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-06-01');
        $expected = new LeaveBalance(3, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        

        // after last date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-06-02');
        $expected = new LeaveBalance(2, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        

        // last date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-08-01');
        $expected = new LeaveBalance(2, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        

        // after both entitlements end dates
        $balance = $this->dao->getLeaveBalance(1, 2, '2012-03-01', '2012-08-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);        
        
        // leave type with no leave entitlement
        $balance = $this->dao->getLeaveBalance(6, 7, '2012-03-01', '2012-08-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);      
        
        // Checking values for scheduled and pending
        $balance = $this->dao->getLeaveBalance(1, 1, '2012-03-01', '2012-07-02');
        $expected = new LeaveBalance(3, 0, 0.75, 0.5, 0);
        $this->assertEquals($expected, $balance);   
        
        // No entitlements for employee
        $balance = $this->dao->getLeaveBalance(3, 1, '2012-03-01', '2012-07-02');
        $expected = new LeaveBalance(0, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);   
        
    }
    
    
    public function testGetLeaveBalanceWithUnlinkedLeave() {
        
        $unlinkedDateLimits = array('2013-01-01', '2013-12-31');
        
        $mockStrategy = $this->getMock('FIFOEntitlementConsumptionStrategy', array('getLeaveWithoutEntitlementDateLimitsForLeaveBalance'));
        $mockStrategy->expects($this->once())
                    ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
                    ->with('2013-01-01', '2013-12-31')
                    ->will($this->returnValue($unlinkedDateLimits));        
        // Using AsAt
        $this->dao->setLeaveEntitlementStrategy($mockStrategy);
        
        $balance = $this->dao->getLeaveBalance(7, 1, '2013-01-01', '2013-12-31');
        
        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0 
        $expected = new LeaveBalance(5, 3, 3, 0, 1, 0, 0);
        $this->assertEquals($expected, $balance);        
    }
    
    public function testGetLeaveBalanceExcludingUnlinkedLeaveWhenAfterPeriod() {
        $unlinkedDateLimits = array('2013-01-01', '2014-12-31');
        
        $mockStrategy = $this->getMock('FIFOEntitlementConsumptionStrategy', array('getLeaveWithoutEntitlementDateLimitsForLeaveBalance'));
        $mockStrategy->expects($this->once())
                    ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
                    ->with('2013-01-01', '2013-12-31')
                    ->will($this->returnValue($unlinkedDateLimits));        
        // Using AsAt
        $this->dao->setLeaveEntitlementStrategy($mockStrategy);
        
        $balance = $this->dao->getLeaveBalance(7, 1, '2013-01-01', '2013-12-31');
        
        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0 
        $expected = new LeaveBalance(5, 5, 5, 0, 3, 0, 0);
        $this->assertEquals($expected, $balance);          
    }
    
    public function testGetLeaveBalanceExcludingUnlinkedBeforePeriod() {
        $unlinkedDateLimits = array('2012-01-01', '2013-12-31');
        
        $mockStrategy = $this->getMock('FIFOEntitlementConsumptionStrategy', array('getLeaveWithoutEntitlementDateLimitsForLeaveBalance'));
        $mockStrategy->expects($this->once())
                    ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
                    ->with('2013-01-01', '2013-12-31')
                    ->will($this->returnValue($unlinkedDateLimits));        
        // Using AsAt
        $this->dao->setLeaveEntitlementStrategy($mockStrategy);
        
        $balance = $this->dao->getLeaveBalance(7, 1, '2013-01-01', '2013-12-31');
        
        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0 
        $expected = new LeaveBalance(5, 4, 4, 0, 2, 0, 0);
        $this->assertEquals($expected, $balance);          
    }    
    
    public function testGetLeaveBalanceExcludingUnlinkedLeave() {
        
        $mockStrategy = $this->getMock('FIFOEntitlementConsumptionStrategy', array('getLeaveWithoutEntitlementDateLimitsForLeaveBalance'));
        $mockStrategy->expects($this->once())
                    ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
                    ->with('2013-01-01', '2013-12-31')
                    ->will($this->returnValue(false));        
        // Using AsAt
        $this->dao->setLeaveEntitlementStrategy($mockStrategy);
        
        $balance = $this->dao->getLeaveBalance(7, 1, '2013-01-01', '2013-12-31');
        
        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0 
        $expected = new LeaveBalance(5, 2, 2, 0, 0, 0, 0);
        $this->assertEquals($expected, $balance);          
    }    
    
    public function testGetLinkedLeaveRequests() {
        $requests = $this->dao->getLinkedLeaveRequests(array(3, 4), 
                array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL, 
                      Leave::LEAVE_STATUS_LEAVE_REJECTED));
        
        $this->assertEquals(0, count($requests));
        
        $requests = $this->dao->getLinkedLeaveRequests(array(1, 2, 3, 4, 5), 
                array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL, 
                      Leave::LEAVE_STATUS_LEAVE_REJECTED));        
        $this->assertEquals(5, count($requests));      
        $this->assertEquals(1, $requests[0]->getId());
        $this->assertEquals(2, $requests[1]->getId());
        $this->assertEquals(3, $requests[2]->getId());
        $this->assertEquals(4, $requests[3]->getId());
        $this->assertEquals(5, $requests[4]->getId());
        
        $requests = $this->dao->getLinkedLeaveRequests(array(1, 2, 3, 4, 5), 
                array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL));        
        $this->assertEquals(2, count($requests));
        $this->assertEquals(1, $requests[0]->getId());
        $this->assertEquals(2, $requests[1]->getId());        
    }
     
    public function testMatchingEntitlementsNoMatches() {
        $empNumber = 1;
        $leaveTypeId = 1;
        $fromDate = '2012-01-01';
        $toDate = '2012-08-02';
        
        $matches = $this->dao->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
        $this->assertEquals(0, count($matches));
    }

    public function testMatchingEntitlementsVerifyNoPartialMatches() {
        $empNumber = 2;
        $leaveTypeId = 6;
        $fromDate = '2013-08-05';
        $toDate = '2013-09-04';
        
        $matches = $this->dao->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
        $this->assertEquals(0, count($matches));      
    }
    
    public function testMatchingEntitlementsOneMatch() {
        $empNumber = 2;
        $leaveTypeId = 6;
        $fromDate = '2013-08-05';
        $toDate = '2013-09-01';
        
        $matches = $this->dao->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);

        $this->assertEquals(1, count($matches)); 
        $this->assertEquals(2, $matches[0]->getId());
    }
  
    
    public function testMatchingEntitlementsDoesNotMatchDeleted() {
        $empNumber = 5;
        $leaveTypeId = 1;
        $fromDate = '2012-06-06';
        $toDate = '2012-09-01';
        
        $matches = $this->dao->getMatchingEntitlements($empNumber, $leaveTypeId, $fromDate, $toDate);
        $this->assertEquals(0, count($matches));          
    }    
    
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
 
    protected function _verifyDeletedFlags($deleted) {
        
        $ids = array(1, 2, 3, 4, 5);

        $nonDeleted = array_diff($ids, $deleted);
    
        // verify deleted
        foreach($deleted as $id) {
            echo $i;
            $entitlement = TestDataService::fetchObject('LeaveEntitlement', $id);
            $this->assertEquals(1, $entitlement->getDeleted(), 'id=' . $id);
        }
        
        // verify non deleted
        foreach($nonDeleted as $id) {
            $entitlement = TestDataService::fetchObject('LeaveEntitlement', $id);
            $this->assertEquals(0, $entitlement->getDeleted(), 'id=' . $id);
        }        
        
    }  
    
    public function testSaveLeaveAdjustment(){
        $leaveAdjustment = new LeaveAdjustment();
        
        $leaveAdjustment->setEmpNumber(1);
        $leaveAdjustment->setNoOfDays(12);
        $leaveAdjustment->setLeaveTypeId(2);
        $leaveAdjustment->setFromDate('2012-09-13');
        $leaveAdjustment->setToDate('2012-11-28');
        $leaveAdjustment->setCreditedDate('2012-05-01');
        $leaveAdjustment->setNote('Created by Unit test');
        $leaveAdjustment->setAdjustmentType(1);
        
        $leaveAdjustment->setDeleted(0);
        
        $result = $this->dao->saveLeaveAdjustment($leaveAdjustment);
        $this->assertFalse( is_null($result->getId()));
    }
    
    public function testSearchLeaveEntitlementsHydrationMode(){
         $parameterHolder = new LeaveEntitlementSearchParameterHolder();
         $parameterHolder->setHydrationMode(Doctrine::HYDRATE_ARRAY);
         $parameterHolder->setEmpIdList(array(1,2,3));
         $parameterHolder->setFromDate('2012-01-01');
         $parameterHolder->setToDate('2012-08-01');
         
         $result = $this->dao->searchLeaveEntitlements($parameterHolder);
         
         $this->assertEquals(3,count($result));
         
         
    }
    
    public function testSearchLeaveEntitlementsSamePeriod() {
        Doctrine_Query::create()->delete()->from('LeaveEntitlement')->execute();

        $leaveEntitlements = array(
            array('id' => 1, 'leave_type_id' => 1, 'emp_number' => 1, 'no_of_days' => 3, 'from_date' => '2013-01-01 00:00:00',
                'to_date' => '2013-12-31 00:00:00', 'entitlement_type' => 1, 'deleted' => 0),
            array('id' => 2, 'leave_type_id' => 1, 'emp_number' => 2, 'no_of_days' => 3, 'from_date' => '2013-01-01 00:00:00',
                'to_date' => '2013-12-31 00:00:00', 'entitlement_type' => 1, 'deleted' => 0),
        );

        foreach ($leaveEntitlements as $entitlement) {
            $leaveEntitlement = new LeaveEntitlement();
            $leaveEntitlement->fromArray($entitlement);
            $leaveEntitlement->save();
        }

        $fromDate = '2013-01-01';
        $toDate = '2013-12-31';
        $employeeNumbers = array(1, 2);
        $leaveTypeId = 1;
        
        $leaveEntitlementSearchParameterHolder = new LeaveEntitlementSearchParameterHolder();
        $leaveEntitlementSearchParameterHolder->setFromDate($fromDate);
        $leaveEntitlementSearchParameterHolder->setLeaveTypeId($leaveTypeId);
        $leaveEntitlementSearchParameterHolder->setToDate($toDate);
        $leaveEntitlementSearchParameterHolder->setEmpIdList($employeeNumbers);
        $leaveEntitlementSearchParameterHolder->setHydrationMode(Doctrine::HYDRATE_ARRAY);

        $entitlementList = $this->dao->searchLeaveEntitlements($leaveEntitlementSearchParameterHolder);
        
        $this->assertEquals(2, count($entitlementList));
    }
    
    public function testBulkAssignLeaveEntitlements() {
        
        $empList = array(1,2,3);
       
       
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(1);

        $leaveEntitlement->setCreditedDate(date('Y-m-d'));


        $leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setDeleted(0);

        $leaveEntitlement->setNoOfDays(2);
        $leaveEntitlement->setFromDate('2012-01-01');
        $leaveEntitlement->setToDate('2012-08-01');

        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
       
       
        $this->assertEquals(count($empList),3);
        
        
        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
        $leaveEntitlement->setNoOfDays(1);
        
        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);
       
       
        $this->assertEquals(count($empList),$result);
       
    }
    
    public function testBulkAssignLeaveEntitlementsLinkingOfUnlinkedLeave() {
        $empList = array(7);
             
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setLeaveTypeId(1);
        $leaveEntitlement->setCreditedDate(date('Y-m-d'));
        $leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
        $leaveEntitlement->setDeleted(0);
        $leaveEntitlement->setNoOfDays(3);
        $leaveEntitlement->setFromDate('2014-01-01');
        $leaveEntitlement->setToDate('2014-12-31');

        $result = $this->dao->bulkAssignLeaveEntitlements($empList, $leaveEntitlement);              
        $this->assertEquals(count($empList), $result);
        
        // verify unlinked leave is now linked
        $conn = Doctrine_Manager::connection()->getDbh();
        $statement = $conn->prepare('SELECT * FROM ohrm_leave_leave_entitlement e WHERE e.leave_id = ?');
        $leaveIds = array(11, 12);
        
        foreach ($leaveIds as $leaveId) {
            $this->assertTrue($statement->execute(array($leaveId)));
            $results = $statement->fetchAll();
            $this->assertEquals(1, count($results));
            $this->assertEquals($leaveId, $results[0]['leave_id']);
            $this->assertEquals(1, $results[0]['length_days']);
        }    
    }
    
    public function testGetLeaveEntitlementTypeListDefaultSort() {
        $entitlementTypeList = TestDataService::loadObjectList('LeaveEntitlementType', $this->fixture, 'LeaveEntitlementType');
        $expected = array($entitlementTypeList[1], $entitlementTypeList[0]);
        $result = $this->dao->getLeaveEntitlementTypeList();
        $this->compareEntitlementTypes($expected, $result);
    }
    
    public function testGetLeaveEntitlementTypeListSpecificSort() {
        $entitlementTypeList = TestDataService::loadObjectList('LeaveEntitlementType', $this->fixture, 'LeaveEntitlementType');
        $expected = array($entitlementTypeList[0], $entitlementTypeList[1]);
        $result = $this->dao->getLeaveEntitlementTypeList('id', 'ASC');
        $this->compareEntitlementTypes($expected, $result);
    }    
    
    protected function compareEntitlementTypes($expected, $results) {
        $this->assertEquals(count($expected), count($results));
        
        for ($i = 0; $i < count($expected); $i++) {         
            $this->compareEntitlementType($expected[$i], $results[$i]);
        }
    }    
    
    protected function compareEntitlementType($expected, $actual) {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->getIsEditable(), $actual->getIsEditable());
        
    }
}

    