<?php
/**
 * OrangeLayed offM is a comprehensive Human Resource Management (Layed offM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeLayed offM Inc., http://www.orangehrm.com
 *
 * OrangeLayed offM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeLayed offM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class TerminationReasonConfigurationDaoTest extends PHPUnit_Framework_TestCase {

	private $terminationReasonConfigurationDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->terminationReasonConfigurationDao = new TerminationReasonConfigurationDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/TerminationReasonConfigurationDao.yml';
		TestDataService::populate($this->fixture);
	}

    public function testAddTerminationReason() {
        
        $terminationReason = new TerminationReason();
        $terminationReason->setName('Deceased');
        
        $this->terminationReasonConfigurationDao->saveTerminationReason($terminationReason);
        
        $savedTerminationReason = TestDataService::fetchLastInsertedRecord('TerminationReason', 'id');
        
        $this->assertTrue($savedTerminationReason instanceof TerminationReason);
        $this->assertEquals('Deceased', $savedTerminationReason->getName());
        
    }
    
    public function testEditTerminationReason() {
        
        $terminationReason = TestDataService::fetchObject('TerminationReason', 3);
        $terminationReason->setName('2011 Layed off');
        
        $this->terminationReasonConfigurationDao->saveTerminationReason($terminationReason);
        
        $savedTerminationReason = TestDataService::fetchLastInsertedRecord('TerminationReason', 'id');
        
        $this->assertTrue($savedTerminationReason instanceof TerminationReason);
        $this->assertEquals('2011 Layed off', $savedTerminationReason->getName());
        
    }
    
    public function testGetTerminationReasonById() {
        
        $terminationReason = $this->terminationReasonConfigurationDao->getTerminationReason(1);
        
        $this->assertTrue($terminationReason instanceof TerminationReason);
        $this->assertEquals('Resigned', $terminationReason->getName());
        
    }
    
    public function testGetTerminationReasonList() {
        
        $terminationReasonList = $this->terminationReasonConfigurationDao->getTerminationReasonList();
        
        foreach ($terminationReasonList as $terminationReason) {
            $this->assertTrue($terminationReason instanceof TerminationReason);
        }
        
        $this->assertEquals(3, count($terminationReasonList));        
        
        /* Checking record order */
        $this->assertEquals('Dismissed', $terminationReasonList[0]->getName());
        $this->assertEquals('Resigned', $terminationReasonList[2]->getName());
        
    }
    
    public function testDeleteTerminationReasons() {
        
        $result = $this->terminationReasonConfigurationDao->deleteTerminationReasons(array(1, 2));
        
        $this->assertEquals(2, $result);
        $this->assertEquals(1, count($this->terminationReasonConfigurationDao->getTerminationReasonList()));       
        
    }
    
    public function testDeleteWrongRecord() {
        
        $result = $this->terminationReasonConfigurationDao->deleteTerminationReasons(array(4));
        
        $this->assertEquals(0, $result);
        
    }
    
    public function testIsExistingTerminationReasonName() {
        
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('Resigned'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('RESIGNED'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('resigned'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('  Resigned  '));
        
    }
    
    public function testIsReasonInUse() {
        
        $empTermination = Doctrine::getTable('EmployeeTerminationRecord')->find(1);
        $empTermination->setEmpNumber(2);
        $empTermination->save();
        
        $this->assertTrue($this->terminationReasonConfigurationDao->isReasonInUse(array(1)));
        $this->assertFalse($this->terminationReasonConfigurationDao->isReasonInUse(array(2)));
        $this->assertFalse($this->terminationReasonConfigurationDao->isReasonInUse(array(3)));
        
    }
    
    public function testGetTerminationReasonByName() {
        
        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('Resigned');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('RESIGNED');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('resigned');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());

        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('  Resigned  ');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('Fired');
        $this->assertFalse($object);        
        
    }       
    
}
