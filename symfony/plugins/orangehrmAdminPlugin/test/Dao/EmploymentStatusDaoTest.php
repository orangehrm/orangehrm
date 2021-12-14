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
use OrangeHRM\Admin\Dao\EmploymentStatusDao;
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class EmploymentStatusDaoTest extends TestCase
{
    private EmploymentStatusDao $employmentStatusDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employmentStatusDao = new EmploymentStatusDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EmploymentStatusDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmploymentStatusesList(): void
    {
        $result = $this->employmentStatusDao->getEmploymentStatuses();
        $this->assertCount(3, $result);
    }

    public function testGetEmploymentStatusById(): void
    {
        $result = $this->employmentStatusDao->getEmploymentStatusById(1);
        $this->assertEquals('Full Time', $result->getName());
    }

    public function testDeleteEmploymentStatus(): void
    {
        $toTobedeletedIds = [3, 2];
        $result = $this->employmentStatusDao->deleteEmploymentStatus($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmploymentStatus(): void
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();

        $result = $this->employmentStatusDao->searchEmploymentStatus($employmentStatusSearchParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof EmploymentStatus);
    }

    public function testSearchEmploymentStatusWithLimit(): void
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $employmentStatusSearchParams->setLimit(2);

        $result = $this->employmentStatusDao->searchEmploymentStatus($employmentStatusSearchParams);
        $this->assertCount(2, $result);
    }

    public function testSaveEmploymentStatus(): void
    {
        $employmentStatus = new EmploymentStatus();
        $employmentStatus->setName("Permanent");
        $result = $this->employmentStatusDao->saveEmploymentStatus($employmentStatus);
        $this->assertTrue($result instanceof EmploymentStatus);
        $this->assertEquals("Permanent", $result->getName());
    }

    public function testEditEmploymentStatus(): void
    {
        $employmentStatus = $this->employmentStatusDao->getEmploymentStatusById(1);
        $employmentStatus->setName("Permanent");
        $result = $this->employmentStatusDao->saveEmploymentStatus($employmentStatus);
        $this->assertTrue($result instanceof EmploymentStatus);
        $this->assertEquals("Permanent", $result->getName());
        $this->assertEquals(1, $result->getId());
    }

    public function testGetEmploymentStatuses(): void
    {
        $result = $this->employmentStatusDao->getEmploymentStatuses();
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof EmploymentStatus);
    }

    public function testGetSearchEmploymentStatusesCount(): void
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();

        $result = $this->employmentStatusDao->getSearchEmploymentStatusesCount($employmentStatusSearchParams);
        $this->assertEquals(3, $result);
    }
}
