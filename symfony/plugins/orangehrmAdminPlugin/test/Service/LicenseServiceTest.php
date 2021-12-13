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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\LicenseDao;
use OrangeHRM\Admin\Dto\LicenseSearchFilterParams;
use OrangeHRM\Admin\Service\LicenseService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\License;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class LicenseServiceTest extends TestCase
{
    private LicenseService $licenseService;
    private string $fixture;

    public function testGetLicenseList(): void
    {
        $licenseList = TestDataService::loadObjectList('License', $this->fixture, 'License');
        $licenseFilterParams = new LicenseSearchFilterParams();
        $licenseDao = $this->getMockBuilder(LicenseDao::class)->getMock();
        $licenseDao->expects($this->once())
            ->method('getLicenseList')
            ->with($licenseFilterParams)
            ->will($this->returnValue($licenseList));
        $this->licenseService->setLicenseDao($licenseDao);
        $result = $this->licenseService->getLicenseList($licenseFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof License);
    }

    public function testDeleteLicenses(): void
    {
        $toBeDeletedLicenseIds = [1, 2];
        $licenseDao = $this->getMockBuilder(LicenseDao::class)->getMock();
        $licenseDao->expects($this->once())
            ->method('deleteLicenses')
            ->with($toBeDeletedLicenseIds)
            ->will($this->returnValue(2));
        $this->licenseService->setLicenseDao($licenseDao);
        $result = $this->licenseService->deleteLicenses($toBeDeletedLicenseIds);
        $this->assertEquals(2, $result);
    }

    public function testGetLicenseById(): void
    {
        $licenseList = TestDataService::loadObjectList('License', $this->fixture, 'License');
        $licenseDao = $this->getMockBuilder(LicenseDao::class)->getMock();
        $licenseDao->expects($this->once())
            ->method('getLicenseById')
            ->with(1)
            ->will($this->returnValue($licenseList[0]));
        $this->licenseService->setLicenseDao($licenseDao);
        $result = $this->licenseService->getLicenseById(1);
        $this->assertEquals($licenseList[0], $result);
    }

    public function testGetLicenseByName(): void
    {
        $licenseList = TestDataService::loadObjectList('License', $this->fixture, 'License');
        $licenseDao = $this->getMockBuilder(LicenseDao::class)->getMock();
        $licenseDao->expects($this->once())
            ->method('getLicenseByName')
            ->with(1)
            ->will($this->returnValue($licenseList[0]));
        $this->licenseService->setLicenseDao($licenseDao);
        $result = $this->licenseService->getLicenseByName(1);
        $this->assertEquals($result, $licenseList[0]);
    }

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->licenseService = new LicenseService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LicenseDao.yml';
        TestDataService::populate($this->fixture);
    }
}
