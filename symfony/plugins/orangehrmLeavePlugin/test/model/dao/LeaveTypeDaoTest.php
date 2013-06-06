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
 * Description of LeaveTypeDaoTest
 * @group Leave
 */
class LeaveTypeDaoTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LeaveTypeDao 
     */
    private $dao;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->dao = new LeaveTypeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveType.yml';
        TestDataService::populate($this->fixture);
    }
    
    public function testGetLeaveTypeList() {
             
        $entitlementList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveType');
        $expected = array($entitlementList[3], $entitlementList[0], $entitlementList[6], 
                          $entitlementList[1], $entitlementList[5]);
        $results = $this->dao->getLeaveTypeList();
        
        $this->_compareLeaveTypes($expected, $results);                
    }    
    
    public function testGetLeaveTypeListByCountryId() {
             
        $entitlementList = TestDataService::loadObjectList('LeaveType', $this->fixture, 'LeaveType');
        $expected = array($entitlementList[3], $entitlementList[1]);
        $results = $this->dao->getLeaveTypeList(1);
        
        $this->_compareLeaveTypes($expected, $results); 
        
        // country id without leave types
        $results = $this->dao->getLeaveTypeList(2);
        $this->assertEquals(0, count($results));
        
        // non-existing country id
        $results = $this->dao->getLeaveTypeList(12);
        $this->assertEquals(0, count($results));        
        
    }        
    
    /* Common methods */

    private function _getLeaveTypeObjectWithValues() {

        $leaveType = new LeaveType();
        $leaveType->setName('Special');
        $leaveType->setDeleted(0);

        return $leaveType;
    }

    /* Testing saveLeaveType() */

    public function testSaveLeaveTypeReturnValue() {
        TestDataService::truncateTables(array('LeaveType'));
        $this->assertTrue($this->dao->saveLeaveType($this->_getLeaveTypeObjectWithValues()));
    }

    public function testSaveLeaveTypeCheckSavedType() {
        TestDataService::truncateTables(array('LeaveType'));

        $this->dao->saveLeaveType($this->_getLeaveTypeObjectWithValues());

        $savedLeaveTypes = TestDataService::fetchLastInsertedRecords('LeaveType', 1);

        $this->assertEquals(0, $savedLeaveTypes[0]->getDeleted());
        $this->assertEquals('Special', $savedLeaveTypes[0]->getName());
    }

    /**
     * @expectedException DaoException
     */
    public function testSaveLeaveTypeDuplicateKey() {

        $this->dao->saveLeaveType($this->_getLeaveTypeObjectWithValues());

        $savedLeaveTypes = TestDataService::fetchLastInsertedRecords('LeaveType', 1);
        $savedId = $savedLeaveTypes[0]->getId();
        
        /* Following should throw an exception for  */
        $leaveType = $this->_getLeaveTypeObjectWithValues();
        $leaveType->setId($savedId);
        $this->dao->saveLeaveType($leaveType);
    }

    public function testSaveLeaveTypeWithOperationalCountry() {

        TestDataService::truncateSpecificTables(array('LeaveType'));

        $leaveType = $this->_getLeaveTypeObjectWithValues();
        $leaveType->setOperationalCountryId(1);

        $this->dao->saveLeaveType($leaveType);

        $savedLeaveType = TestDataService::fetchLastInsertedRecord('LeaveType', 'id');
        $this->assertEquals(1, $savedLeaveType->getOperationalCountryId());
    }

    public function testSaveLeaveTypeWithoutOperationalCountry() {

        TestDataService::truncateSpecificTables(array('LeaveType'));

        $leaveType = $this->_getLeaveTypeObjectWithValues();

        $this->dao->saveLeaveType($leaveType);

        $savedLeaveType = TestDataService::fetchLastInsertedRecord('LeaveType', 'id');
        $this->assertTrue(is_null($savedLeaveType->getOperationalCountryId()));
    }

    /**
     * @expectedException DaoException
     */
    public function testSaveLeaveTypeWithInvalidOperationalCountry() {

        $leaveType = $this->_getLeaveTypeObjectWithValues();
        $leaveType->setOperationalCountryId(41);

        $this->dao->saveLeaveType($leaveType);
    }    
    
    /* Testing deleteLeaveType() */

    public function testDeleteLeaveTypeReturnValue() {

        $this->assertTrue($this->dao->deleteLeaveType(array(1, 2)));
        $this->assertTrue($this->dao->deleteLeaveType(array(4)));
    }

    public function testDeleteLeaveTypeValues() {

        $this->assertTrue($this->dao->deleteLeaveType(array(1)));
        $deletedTypeObject = TestDataService::fetchObject('LeaveType', 1);

        $this->assertEquals(1, $deletedTypeObject->getId());
        $this->assertEquals(1, $deletedTypeObject->getDeleted());
    }
    
    
    /* Testing getDeletedLeaveTypeList() */    

    public function testDeleteLeaveTypeList() {

        $this->assertTrue($this->dao->deleteLeaveType(array(1, 2)));

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        $this->assertEquals(4, count($leaveTypeList));

        $this->assertEquals(1, $leaveTypeList[0]->getId());
        $this->assertEquals(2, $leaveTypeList[1]->getId());
    }

    public function testGetDeletedLeaveTypeListObjectTypes() {

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertTrue($leaveTypeObj instanceof LeaveType);
        }
    }

    public function testGetDeletedLeaveTypeListCount() {

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();
        $this->assertEquals(2, count($leaveTypeList));
    }

    public function testGetDeletedLeaveTypeListWrongResult() {

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertNotEquals(1, $leaveTypeObj->getId());
            $this->assertNotEquals('Casual', $leaveTypeObj->getName());
        }
    }

    public function testGetDeletedLeaveTypeListValuesAndOrder() {

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList();

        $this->assertEquals(3, $leaveTypeList[0]->getId());
        $this->assertEquals('Company', $leaveTypeList[0]->getName());

        $this->assertEquals(5, $leaveTypeList[1]->getId());
        $this->assertEquals('Sick', $leaveTypeList[1]->getName());
    }

    public function testGetDeletedLeaveTypeListForOperationalCountry() {
        $leaveTypeList = $this->dao->getDeletedLeaveTypeList(2);
        $this->assertEquals(0, count($leaveTypeList));

        $leaveTypeList = $this->dao->getDeletedLeaveTypeList(1);
        $this->assertEquals(1, count($leaveTypeList));
        $this->assertEquals(5, $leaveTypeList[0]->getId());
    }
    
    /* Testing undeleteLeaveType() */

    public function testUndeleteLeaveTypeReturnValue() {

        $this->assertTrue($this->dao->undeleteLeaveType(3));
        $this->assertFalse($this->dao->undeleteLeaveType(1));
    }

    public function testUndeleteLeaveTypeValues() {

        $this->assertTrue($this->dao->undeleteLeaveType(3));
        $undeletedTypeObject = TestDataService::fetchObject('LeaveType', 3);

        $this->assertEquals(3, $undeletedTypeObject->getId());
        $this->assertEquals(0, $undeletedTypeObject->getDeleted());
    }

    public function testUndeleteLeaveTypeList() {

        $this->assertTrue($this->dao->undeleteLeaveType(3));

        $leaveTypeList = $this->dao->getLeaveTypeList();

        $this->assertEquals(6, count($leaveTypeList));
        $this->assertEquals('Company', $leaveTypeList[3]->getName());
    }    
    
    /* Testing readLeaveTypeByName() */

    public function testReadLeaveTypeByNameType() {

        $this->assertTrue($this->dao->readLeaveTypeByName('Casual') instanceof LeaveType);
        $this->assertTrue($this->dao->readLeaveTypeByName('Casual ') instanceof LeaveType);
    }

    public function testReadLeaveTypeByNameValues() {

        $leaveTypeObject = $this->dao->readLeaveTypeByName('Casual');

        $this->assertEquals(1, $leaveTypeObject->getId());
        $this->assertEquals('Casual', $leaveTypeObject->getName());
    }
    
    /* Testing readLeaveType() */

    public function testReadLeaveTypeObjectType() {

        // Active type
        $this->assertTrue($this->dao->readLeaveType(1) instanceof LeaveType);

        // Deleted type
        $this->assertTrue($this->dao->readLeaveType(3) instanceof LeaveType);
    }

    public function testReadLeaveTypeObjectWrongArgument() {

        $this->assertFalse($this->dao->readLeaveType('Casual'));
    }

    public function testReadLeaveTypeValues() {

        $leaveTypeObject = $this->dao->readLeaveType(1);

        $this->assertEquals(1, $leaveTypeObject->getId());
        $this->assertEquals('Casual', $leaveTypeObject->getName());
    }    
    
    /* Testing getLeaveTypeList() */

    public function testGetLeaveTypeListObjectTypes() {

        $leaveTypeList = $this->dao->getLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertTrue($leaveTypeObj instanceof LeaveType);
        }
    }

    public function testGetLeaveTypeListCount() {

        $leaveTypeList = $this->dao->getLeaveTypeList();
        $this->assertEquals(5, count($leaveTypeList));
    }

    public function testGetLeaveTypeListWrongResult() {

        $leaveTypeList = $this->dao->getLeaveTypeList();

        foreach ($leaveTypeList as $leaveTypeObj) {
            $this->assertNotEquals(3, $leaveTypeObj->getId());
            $this->assertNotEquals('Company', $leaveTypeObj->getName());
        }
    }

    public function testGetLeaveTypeListValuesAndOrder() {

        $leaveTypeList = $this->dao->getLeaveTypeList();

        $this->assertEquals(4, $leaveTypeList[0]->getId());
        $this->assertEquals('Annual', $leaveTypeList[0]->getName());

        $this->assertEquals(6, $leaveTypeList[4]->getId());
        $this->assertEquals('Wesak', $leaveTypeList[4]->getName());
    }

    public function testGetLeaveTypeListForOperationalCountry() {
        $leaveTypeList = $this->dao->getLeaveTypeList(1);
        $this->assertEquals(2, count($leaveTypeList));
        $this->assertEquals(4, $leaveTypeList[0]->getId());
        $this->assertEquals(2, $leaveTypeList[1]->getId());

        $leaveTypeList = $this->dao->getLeaveTypeList(2);
        $this->assertEquals(0, count($leaveTypeList));
    }
    
    protected function _compareLeaveTypes($expected, $results) {
        $this->assertEquals(count($expected), count($results));
        
        for ($i = 0; $i < count($expected); $i++) {                     
            $this->_compareLeaveType($expected[$i], $results[$i]);
        }
    }
    
    protected function _compareLeaveType($expected, $actual) {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->getDeleted(), $actual->getDeleted());
        $this->assertEquals($expected->getOperationalCountryId(), $actual->getOperationalCountryId());        
    }    
}

    
