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
 * Description of FIFOEntitlementConsumptionStrategyDaoTest
 */
class FIFOEntitlementConsumptionStrategyDaoTest extends PHPUnit_Framework_TestCase {
    private $dao;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->dao = new FIFOEntitlementConsumptionStrategyDao();

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/FIFOEntitlementStrategy.yml';
        TestDataService::populate($this->fixture);        
    }
    
    public function testHandleLeavePeriodChange() {
        
        $entitlementsBefore = $this->getEntitlements();
        $deletedEntitlementsBefore = $this->getDeletedEntitlements();
        
        $this->dao->handleLeavePeriodChange(array('2012-01-01', '2012-12-31'), 1, 1, 3, 27);
        
        $entitlementsAfter = $this->getEntitlements();
        $deletedEntitlementsAfter = $this->getDeletedEntitlements();
        
        $this->assertEquals(count($entitlementsBefore), count($entitlementsAfter));
        $this->assertEquals(count($deletedEntitlementsBefore), count($deletedEntitlementsAfter));

        // to do fix.
        $currentYear = 2012;
        
        foreach ($entitlementsAfter as $id => $entitlement) {
            $this->assertTrue(isset($entitlementsBefore[$id]));            
                                    
            $dateFromBefore = DateTime::createFromFormat('Y-m-d G:i:s', $entitlementsBefore[$id]->getFromDate());

            $year = $dateFromBefore->format('Y');
            
            if ($year < $currentYear) {
                // verify no change to entitlements in prev years.
                $this->_compareEntitlement($entitlementsBefore[$id], $entitlement);
            } else {
                
                // only end date changed for entitlements in current year
                // both start end dates changed for future entitlements                
                $dateFromAfter = DateTime::createFromFormat('Y-m-d G:i:s', $entitlement->getFromDate());

                $dateToBefore = DateTime::createFromFormat('Y-m-d G:i:s', $entitlementsBefore[$id]->getFromDate());
                $dateToAfter = DateTime::createFromFormat('Y-m-d G:i:s', $entitlementsBefore[$id]->getToDate());
                $expectedDateTo = $dateToBefore->setDate($dateToBefore->format('Y') + 1, 3, 26);

                if ($year == $currentYear) {
                    $expectedDateFrom = $dateFromBefore;
                } else {
                    $expectedDateFrom = $dateFromBefore->setDate($dateFromBefore->format('Y'), 3, 27);                    
                } 
                
                // check year not changed, but month and date changed
                $this->assertEquals($expectedDateFrom, $dateFromAfter);
                $this->assertEquals($expectedDateTo, $dateToAfter);
                
                $expected = $entitlementsBefore[$id];
                $expected->setFromDate($expectedDateFrom->format('Y-m-d G:i:s'));
                $expected->setToDate($expectedDateTo->format('Y-m-d G:i:s'));
                $this->_compareEntitlement($expected, $entitlement);
            }
            
        }
        
        // Verify no change to deleted entitlements
        foreach ($deletedEntitlementsAfter as $id => $entitlement) {
            $this->assertTrue(isset($deletedEntitlementsBefore[$id]));
            $this->_compareEntitlement($deletedEntitlementsBefore[$id], $entitlement);
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
    
    protected function getEntitlements() {
        $entitlements = array();
        $entitlements = Doctrine_Query::create()
                        ->from('LeaveEntitlement le')
                        ->where('le.deleted = 0')
                        ->orderBy('le.id ASC')
                        ->execute();
        
        foreach ($entitlements as $e) {
            $entitlements[$e->getId()] = $e;
        }        
        
        return $entitlements;
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
}
