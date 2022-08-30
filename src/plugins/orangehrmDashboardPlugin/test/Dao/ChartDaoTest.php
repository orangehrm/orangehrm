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

namespace OrangeHRM\Dashboard\test\Dao;

use Exception;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Config\Config;
use OrangeHRM\Dashboard\Dao\ChartDao;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

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
        $count =  $this->chartDao->getEmployeeCount($employeeSearchFilterParams);
        $this->assertEquals(6, $count);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $count =  $this->chartDao->getEmployeeCount($employeeSearchFilterParams);
        $this->assertEquals(2, $count);
    }

    public function testGetEmployeeDistributionBySubunit(): void
    {
        $this->createKernelWithMockServices([
            Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService()
        ]);

        $result = $this->chartDao->getEmployeeDistributionBySubunit();
        $this->assertTrue(is_array($this->chartDao->getEmployeeDistributionBySubunit()));
        $this->assertCount(4, $result);
    }
}
