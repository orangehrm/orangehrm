<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Dashboard\Dao;

use Exception;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Config\Config;
use OrangeHRM\Dashboard\Dao\ChartDao;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Dashboard
 * @group Dao
 */
class ChartDaoTest extends KernelTestCase
{
    private ChartDao $chartDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->chartDao = new ChartDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmDashboardPlugin/test/fixtures/ChartDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeCount(): void
    {
        $this->createKernelWithMockServices([
            Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService()
        ]);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(2);
        $count =  $this->chartDao->getUnassignedEmployeeCount();
        $this->assertEquals(3, $count);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(null);
        $count =  $this->chartDao->getUnassignedEmployeeCount();
        $this->assertEquals(3, $count);
    }

    public function testGetEmployeeDistributionBySubunit(): void
    {
        $this->createKernelWithMockServices([
            Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService()
        ]);

        $result = $this->chartDao->getEmployeeDistributionBySubunit();

        $this->assertTrue(is_array($this->chartDao->getEmployeeDistributionBySubunit()));
        $this->assertCount(9, $result);
        $this->assertEquals(3, $result[0]->getCount());
        $this->assertEquals('Engineering', $result[1]->getSubunit()->getName());
        $this->assertEquals(7, $result[2]->getSubunit()->getId());
        $this->assertEquals(1, $result[3]->getSubunit()->getLevel());
    }

    public function testGetEmployeeDistributionByLocation(): void
    {
        $result = $this->chartDao->getEmployeeDistributionByLocation();

        $this->assertTrue(is_array($this->chartDao->getEmployeeDistributionByLocation()));
        $this->assertCount(10, $result);
        $this->assertEquals('location 2', $result[0]->getLocationName());
        $this->assertEquals('4', $result[1]->getLocationId());
        $this->assertEquals('2', $result[3]->getEmployeeCount());
    }

    public function testGetTotalActiveEmployeeCount(): void
    {
        $result = $this->chartDao->getTotalActiveEmployeeCount();
        $this->assertEquals(25, $result);
    }
}
