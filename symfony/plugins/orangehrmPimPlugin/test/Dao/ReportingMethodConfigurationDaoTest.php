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
 */
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class ReportingMethodConfigurationDaoTest extends PHPUnit_Framework_TestCase {

	private $reportingMethodConfigurationDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->reportingMethodConfigurationDao = new ReportingMethodConfigurationDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/ReportingMethodConfigurationDao.yml';
		TestDataService::populate($this->fixture);
	}

    public function testAddReportingMethod() {
        
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Finance');
        
        $this->reportingMethodConfigurationDao->saveReportingMethod($reportingMethod);
        
        $savedReportingMethod = TestDataService::fetchLastInsertedRecord('ReportingMethod', 'id');
        
        $this->assertTrue($savedReportingMethod instanceof ReportingMethod);
        $this->assertEquals('Finance', $savedReportingMethod->getName());
        
    }
    
    public function testEditReportingMethod() {
        
        $reportingMethod = TestDataService::fetchObject('ReportingMethod', 3);
        $reportingMethod->setName('Finance HR');
        
        $this->reportingMethodConfigurationDao->saveReportingMethod($reportingMethod);
        
        $savedReportingMethod = TestDataService::fetchLastInsertedRecord('ReportingMethod', 'id');
        
        $this->assertTrue($savedReportingMethod instanceof ReportingMethod);
        $this->assertEquals('Finance HR', $savedReportingMethod->getName());
        
    }
    
    public function testGetReportingMethodById() {
        
        $reportingMethod = $this->reportingMethodConfigurationDao->getReportingMethod(1);
        
        $this->assertTrue($reportingMethod instanceof ReportingMethod);
        $this->assertEquals('Indirect', $reportingMethod->getName());
        
    }
    
    public function testGetReportingMethodList() {
        
        $reportingMethodList = $this->reportingMethodConfigurationDao->getReportingMethodList();
        
        foreach ($reportingMethodList as $reportingMethod) {
            $this->assertTrue($reportingMethod instanceof ReportingMethod);
        }
        
        $this->assertEquals(3, count($reportingMethodList));        
        
        /* Checking record order */
        $this->assertEquals('Direct', $reportingMethodList[0]->getName());
        $this->assertEquals('Indirect', $reportingMethodList[2]->getName());
        
    }
    
    public function testDeleteReportingMethods() {
        
        $result = $this->reportingMethodConfigurationDao->deleteReportingMethods(array(1, 2));
        
        $this->assertEquals(2, $result);
        $this->assertEquals(1, count($this->reportingMethodConfigurationDao->getReportingMethodList()));       
        
    }
    
    public function testDeleteWrongRecord() {
        
        $result = $this->reportingMethodConfigurationDao->deleteReportingMethods(array(4));
        
        $this->assertEquals(0, $result);
        
    }
    
    public function testIsExistingReportingMethodName() {
        
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('Indirect'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('INDIRECT'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('indirect'));
        $this->assertTrue($this->reportingMethodConfigurationDao->isExistingReportingMethodName('  Indirect  '));
        
    }
    
    public function testGetReportingMethodByName() {
        
        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('Indirect');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('INDIRECT');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('indirect');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());

        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('  Indirect  ');
        $this->assertTrue($object instanceof ReportingMethod);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->reportingMethodConfigurationDao->getReportingMethodByName('Supervisor');
        $this->assertFalse($object);        
        
    }      
    
}
