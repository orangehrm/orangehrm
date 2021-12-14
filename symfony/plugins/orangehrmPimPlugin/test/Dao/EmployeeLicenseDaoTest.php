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

namespace OrangeHRM\Tests\Pim\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;
use OrangeHRM\Pim\Dao\EmployeeLicenseDao;
use OrangeHRM\Pim\Dto\EmployeeAllowedLicenseSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeLicenseSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeLicenseDaoTest extends TestCase
{
    private EmployeeLicenseDao $employeeLicenseDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeLicenseDao = new EmployeeLicenseDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeLicenseDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeLicense(): void
    {
        $result = $this->employeeLicenseDao->getEmployeeLicense(1, 1);
        $this->assertEquals('1', $result->getLicenseNo());
        $this->assertEquals('2020-01-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2021-01-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testDeleteEmployeeLicense(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeLicenseDao->deleteEmployeeLicenses(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeLicense(): void
    {
        $employeeLicenseSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeLicenseSearchParams->setEmpNumber(1);
        $result = $this->employeeLicenseDao->searchEmployeeLicense($employeeLicenseSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeLicense);
    }

    public function testSearchEmployeeLicenseWithLimit(): void
    {
        $employeeLicenseSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeLicenseSearchParams->setEmpNumber(1);
        $employeeLicenseSearchParams->setLimit(1);

        $result = $this->employeeLicenseDao->searchEmployeeLicense($employeeLicenseSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeLicense(): void
    {
        $employeeLicense = new EmployeeLicense();
        $employeeLicense->getDecorator()->setLicenseByLicenseId(1);
        $employeeLicense->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeLicense->setLicenseNo('05');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2020-05-23'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-23'));
        $result = $this->employeeLicenseDao->saveEmployeeLicense($employeeLicense);
        $this->assertTrue($result instanceof EmployeeLicense);
        $this->assertEquals("05", $result->getLicenseNo());
        $this->assertEquals('2020-05-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2020-05-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testEditEmployeeLicense(): void
    {
        $employeeLicense = $this->employeeLicenseDao->getEmployeeLicense(1, 1);
        $employeeLicense->setLicenseNo("07");
        $employeeLicense->setLicenseIssuedDate(new DateTime('2020-07-23'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2021-07-23'));
        $result = $this->employeeLicenseDao->saveEmployeeLicense($employeeLicense);
        $this->assertTrue($result instanceof EmployeeLicense);
        $this->assertEquals("07", $result->getLicenseNo());
        $this->assertEquals('2020-07-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2021-07-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testGetSearchEmployeeLicensesCount(): void
    {
        $employeeLicenseSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeLicenseSearchParams->setEmpNumber(1);
        $result = $this->employeeLicenseDao->getSearchEmployeeLicensesCount($employeeLicenseSearchParams);
        $this->assertEquals(2, $result);
    }

    public function testGetEmployeeAllowedLicenses(): void
    {
        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(1);
        $licenses = $this->employeeLicenseDao->getEmployeeAllowedLicenses($searchFilterParams);

        $this->assertEquals(
            ['NAITA'],
            array_map(
                function (License $license) {
                    return $license->getName();
                },
                $licenses
            )
        );
        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(2);
        $licenses = $this->employeeLicenseDao->getEmployeeAllowedLicenses($searchFilterParams);
        $this->assertCount(2, $licenses);
        $this->assertEquals(
            ['CIMA', 'NAITA'],
            array_map(
                function (License $license) {
                    return $license->getName();
                },
                $licenses
            )
        );

        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(100);
        $licenses = $this->employeeLicenseDao->getEmployeeAllowedLicenses($searchFilterParams);
        $this->assertCount(3, $licenses);
        $this->assertEquals(
            ['CCNA', 'CIMA', 'NAITA'],
            array_map(
                function (License $license) {
                    return $license->getName();
                },
                $licenses
            )
        );
    }

    public function testGetEmployeeAllowedLicensesCount(): void
    {
        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(1);
        $licensesCount = $this->employeeLicenseDao->getEmployeeAllowedLicensesCount($searchFilterParams);
        $this->assertEquals(1, $licensesCount);

        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(2);
        $licensesCount = $this->employeeLicenseDao->getEmployeeAllowedLicensesCount($searchFilterParams);
        $this->assertEquals(2, $licensesCount);

        $searchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $searchFilterParams->setEmpNumber(100);
        $licensesCount = $this->employeeLicenseDao->getEmployeeAllowedLicensesCount($searchFilterParams);
        $this->assertEquals(3, $licensesCount);
    }
}
