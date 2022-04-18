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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\LicenseDao;
use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\License;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class LicenseDaoTest extends TestCase
{
    private LicenseDao $licenseDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->licenseDao = new LicenseDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LicenseDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddLicense(): void
    {
        $license = new License();
        $license->setName('Bicycle');

        $this->licenseDao->saveLicense($license);

        $savedLicense = TestDataService::fetchLastInsertedRecord('License', 'a.id');

        $this->assertTrue($savedLicense instanceof License);
        $this->assertEquals('Bicycle', $savedLicense->getName());
    }

    public function testEditLicense(): void
    {
        $license = TestDataService::fetchObject('License', 3);
        $license->setName('Moon Pilot');

        $this->licenseDao->saveLicense($license);

        $savedLicense = TestDataService::fetchLastInsertedRecord('License', 'a.id');

        $this->assertTrue($savedLicense instanceof License);
        $this->assertEquals('Moon Pilot', $savedLicense->getName());
    }

    public function testGetLicenseById(): void
    {
        $license = $this->licenseDao->getLicenseById(1);

        $this->assertTrue($license instanceof License);
        $this->assertEquals('Ship Captain', $license->getName());
    }

    public function testGetLicenseList(): void
    {
        $licenseFilterParams = new LicenseSearchFilterParams();
        $result = $this->licenseDao->getLicenseList($licenseFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof License);
    }

    public function testDeleteLicenses(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->licenseDao->deleteLicenses($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->licenseDao->deleteLicenses([4]);

        $this->assertEquals(0, $result);
    }

    public function testIsExistingLicenseName(): void
    {
        $this->assertTrue($this->licenseDao->isExistingLicenseName('Ship Captain'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('SHIP CAPTAIN'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('ship captain'));
        $this->assertTrue($this->licenseDao->isExistingLicenseName('  Ship Captain  '));
    }

    public function testGetLicenseByName(): void
    {
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
        $this->assertFalse($object instanceof License);
    }
}
