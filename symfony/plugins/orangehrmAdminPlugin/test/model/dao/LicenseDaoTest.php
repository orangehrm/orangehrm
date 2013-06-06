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
class LicenseDaoTest extends PHPUnit_Framework_TestCase {

	private $licenseDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->licenseDao = new LicenseDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/LicenseDao.yml';
		TestDataService::populate($this->fixture);
	}

    public function testAddLicense() {
        
        $license = new License();
        $license->setName('Bicycle');
        
        $this->licenseDao->saveLicense($license);
        
        $savedLicense = TestDataService::fetchLastInsertedRecord('License', 'id');
        
        $this->assertTrue($savedLicense instanceof License);
        $this->assertEquals('Bicycle', $savedLicense->getName());
        
    }
    
    public function testEditLicense() {
        
        $license = TestDataService::fetchObject('License', 3);
        $license->setName('Moon Pilot');
        
        $this->licenseDao->saveLicense($license);
        
        $savedLicense = TestDataService::fetchLastInsertedRecord('License', 'id');
        
        $this->assertTrue($savedLicense instanceof License);
        $this->assertEquals('Moon Pilot', $savedLicense->getName());
        
    }
    
    public function testGetLicenseById() {
        
        $license = $this->licenseDao->getLicenseById(1);
        
        $this->assertTrue($license instanceof License);
        $this->assertEquals('Ship Captain', $license->getName());
        
    }
    
    public function testGetLicenseList() {
        
        $licenseList = $this->licenseDao->getLicenseList();
        
        foreach ($licenseList as $license) {
            $this->assertTrue($license instanceof License);
        }
        
        $this->assertEquals(3, count($licenseList));        
        
        /* Checking record order */
        $this->assertEquals('Driving', $licenseList[0]->getName());
        $this->assertEquals('Ship Captain', $licenseList[2]->getName());
        
    }
    
    public function testDeleteLicenses() {
        
        $result = $this->licenseDao->deleteLicenses(array(1, 2));
        
        $this->assertEquals(2, $result);
        $this->assertEquals(1, count($this->licenseDao->getLicenseList()));       
        
    }
    
    public function testDeleteWrongRecord() {
        
        $result = $this->licenseDao->deleteLicenses(array(4));
        
        $this->assertEquals(0, $result);
        
    }
    
    public function testIsExistingLicenseName() {
        
        $this->assertTrue($this->licenseDao->isExistingLicenseName('Ship Captain'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('SHIP CAPTAIN'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('ship captain'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('  Ship Captain  '));
        
    }
    
    public function testGetLicenseByName() {
        
        $object = $this->licenseDao->getLicenseByName('Ship Captain');
        $this->assertTrue($object instanceof License);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->licenseDao->getLicenseByName('SHIP CAPTAIN');
        $this->assertTrue($object instanceof License);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->licenseDao->getLicenseByName('ship captain');
        $this->assertTrue($object instanceof License);
        $this->assertEquals(1, $object->getId());

        $object = $this->licenseDao->getLicenseByName('  Ship Captain  ');
        $this->assertTrue($object instanceof License);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->licenseDao->getLicenseByName('Bike Riding');
        $this->assertFalse($object);        
        
    }        
    
}
