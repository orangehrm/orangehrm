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
use OrangeHRM\Entity\Decorator\EmployeeLicenseDecorator;
use OrangeHRM\Pim\Dao\EmployeeLicenseDao;
use OrangeHRM\Pim\Dto\EmployeeLicenseSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeLicenseDaoTest extends TestCase
{

    private EmployeeLicenseDao $employeeSkillDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeSkillDao = new EmployeeLicenseDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeLicenseDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeLicense(): void
    {
        $result = $this->employeeSkillDao->getEmployeeLicense(1, 1);
        $this->assertEquals('1', $result->getLicenseNo());
        $this->assertEquals('2020-01-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2021-01-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testDeleteEmployeeLicense(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeSkillDao->deleteEmployeeLicenses(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeSkill(): void
    {
        $employeeSkillSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $result = $this->employeeSkillDao->searchEmployeeLicense($employeeSkillSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeLicense);
    }

    public function testSearchEmployeeSkillWithLimit(): void
    {
        $employeeSkillSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $employeeSkillSearchParams->setLimit(1);

        $result = $this->employeeSkillDao->searchEmployeeLicense($employeeSkillSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeLicense(): void
    {
        $employeeSkill = new EmployeeLicense();
        $employeeSkill->getDecorator()->setLicenseByLicenseId(1);
        $employeeSkill->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeSkill->setLicenseNo('05');
        $employeeSkill->setLicenseIssuedDate(new DateTime('2020-05-23'));
        $employeeSkill->setLicenseExpiryDate(new DateTime('2021-05-23'));
        $result = $this->employeeSkillDao->saveEmployeeLicense($employeeSkill);
        $this->assertTrue($result instanceof EmployeeLicense);
        $this->assertEquals("05", $result->getLicenseNo());
        $this->assertEquals('2020-05-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2020-05-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testEditEmployeeSkill(): void
    {
        $employeeSkill = $this->employeeSkillDao->getEmployeeLicense(1, 1);
        $employeeSkill->setLicenseNo("07");
        $employeeSkill->setLicenseIssuedDate(new DateTime('2020-07-23'));
        $employeeSkill->setLicenseExpiryDate(new DateTime('2021-07-23'));
        $result = $this->employeeSkillDao->saveEmployeeLicense($employeeSkill);
        $this->assertTrue($result instanceof EmployeeLicense);
        $this->assertEquals("07", $result->getLicenseNo());
        $this->assertEquals('2020-07-23', $result->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2021-07-23', $result->getLicenseExpiryDate()->format('Y-m-d'));
    }

    public function testGetSearchEmployeeSkillsCount(): void
    {
        $employeeSkillSearchParams = new EmployeeLicenseSearchFilterParams();
        $employeeSkillSearchParams->setEmpNumber(1);
        $result = $this->employeeSkillDao->getSearchEmployeeLicensesCount($employeeSkillSearchParams);
        $this->assertEquals(2, $result);
    }
}
