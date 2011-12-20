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
class EducationDaoTest extends PHPUnit_Framework_TestCase {

	private $educationDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->educationDao = new EducationDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/EducationDao.yml';
		TestDataService::populate($this->fixture);
	}

    public function testAddEducation() {
        
        $education = new Education();
        $education->setName('PMP');
        
        $this->educationDao->saveEducation($education);
        
        $savedEducation = TestDataService::fetchLastInsertedRecord('Education', 'id');
        
        $this->assertTrue($savedEducation instanceof Education);
        $this->assertEquals('PMP', $savedEducation->getName());
        
    }
    
    public function testEditEducation() {
        
        $education = TestDataService::fetchObject('Education', 3);
        $education->setName('MSc New');
        
        $this->educationDao->saveEducation($education);
        
        $savedEducation = TestDataService::fetchLastInsertedRecord('Education', 'id');
        
        $this->assertTrue($savedEducation instanceof Education);
        $this->assertEquals('MSc New', $savedEducation->getName());
        
    }
    
    public function testGetEducationById() {
        
        $education = $this->educationDao->getEducationById(1);
        
        $this->assertTrue($education instanceof Education);
        $this->assertEquals('PhD', $education->getName());
        
    }
    
    public function testGetEducationList() {
        
        $educationList = $this->educationDao->getEducationList();
        
        foreach ($educationList as $education) {
            $this->assertTrue($education instanceof Education);
        }
        
        $this->assertEquals(3, count($educationList));        
        
        /* Checking record order */
        $this->assertEquals('BSc', $educationList[0]->getName());
        $this->assertEquals('PhD', $educationList[2]->getName());
        
    }
    
    public function testDeleteEducations() {
        
        $result = $this->educationDao->deleteEducations(array(1, 2));
        
        $this->assertEquals(2, $result);
        $this->assertEquals(1, count($this->educationDao->getEducationList()));       
        
    }
    
    public function testDeleteWrongRecord() {
        
        $result = $this->educationDao->deleteEducations(array(4));
        
        $this->assertEquals(0, $result);
        
    }
    
    public function testIsExistingEducationName() {
        
        $this->assertTrue($this->educationDao->isExistingEducationName('PhD'));
        $this->assertTrue($this->educationDao->isExistingEducationName('PHD'));
        $this->assertTrue($this->educationDao->isExistingEducationName('phd'));
        $this->assertTrue($this->educationDao->isExistingEducationName('  PhD  '));
        
    }
    
    public function testGetEducationByName() {
        
        $object = $this->educationDao->getEducationByName('PhD');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->educationDao->getEducationByName('PHD');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->educationDao->getEducationByName('phd');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());

        $object = $this->educationDao->getEducationByName('  PhD  ');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->educationDao->getEducationByName('MBA');
        $this->assertFalse($object);        
        
    }      
    
}
