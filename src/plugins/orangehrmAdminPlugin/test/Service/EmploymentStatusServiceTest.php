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

use OrangeHRM\Admin\Dao\EmploymentStatusDao;
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Exception;

/**
 * @group Admin
 * @group Service
 */
class EmploymentStatusServiceTest extends TestCase
{
    private EmploymentStatusService $employmentStatusService;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employmentStatusService = new EmploymentStatusService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EmploymentStatusDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testDeleteEmploymentStatus(): void
    {
        $toBeDeletedEmploymentStatusIds = [1, 2];

        $employmentStatusDao = $this->getMockBuilder(EmploymentStatusDao::class)->getMock();

        $employmentStatusDao->expects($this->once())
            ->method('deleteEmploymentStatus')
            ->with($toBeDeletedEmploymentStatusIds)
            ->will($this->returnValue(2));

        $this->employmentStatusService->setEmploymentStatusDao($employmentStatusDao);
        $result = $this->employmentStatusService->deleteEmploymentStatus($toBeDeletedEmploymentStatusIds);
        $this->assertEquals(2, $result);
    }

    public function testGetEmploymentStatusById(): void
    {
        $employmentStatusList = TestDataService::loadObjectList('EmploymentStatus', $this->fixture, 'EmploymentStatus');
        $employmentStatusDao = $this->getMockBuilder(EmploymentStatusDao::class)->getMock();

        $employmentStatusDao->expects($this->once())
            ->method('getEmploymentStatusById')
            ->with(1)
            ->will($this->returnValue($employmentStatusList[0]));

        $this->employmentStatusService->setEmploymentStatusDao($employmentStatusDao);
        $result = $this->employmentStatusService->getEmploymentStatusById(1);
        $this->assertEquals($employmentStatusList[0], $result);
    }

    public function testSaveEmploymentStatus(): void
    {
        $employmentStatus = new EmploymentStatus();
        $employmentStatus->setName("Permanent");

        $employmentStatusDao = $this->getMockBuilder(EmploymentStatusDao::class)->getMock();

        $employmentStatusDao->expects($this->once())
            ->method('saveEmploymentStatus')
            ->with($employmentStatus)
            ->will($this->returnValue($employmentStatus));

        $this->employmentStatusService->setEmploymentStatusDao($employmentStatusDao);
        $result = $this->employmentStatusService->saveEmploymentStatus($employmentStatus);
        $this->assertEquals($employmentStatus, $result);
    }

    public function testSearchEmploymentStatus(): void
    {
        $employmentStatusList = TestDataService::loadObjectList('EmploymentStatus', $this->fixture, 'EmploymentStatus');
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $employmentStatusDao = $this->getMockBuilder(EmploymentStatusDao::class)->getMock();

        $employmentStatusDao->expects($this->once())
            ->method('searchEmploymentStatus')
            ->with($employmentStatusSearchParams)
            ->will($this->returnValue($employmentStatusList));

        $this->employmentStatusService->setEmploymentStatusDao($employmentStatusDao);
        $result = $this->employmentStatusService->searchEmploymentStatus($employmentStatusSearchParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof EmploymentStatus);
    }

    public function testGetSearchEmploymentStatusesCount(): void
    {
        $employmentStatusSearchParams = new EmploymentStatusSearchFilterParams();
        $employmentStatusDao = $this->getMockBuilder(EmploymentStatusDao::class)->getMock();

        $employmentStatusDao->expects($this->once())
            ->method('getSearchEmploymentStatusesCount')
            ->with($employmentStatusSearchParams)
            ->will($this->returnValue(3));

        $this->employmentStatusService->setEmploymentStatusDao($employmentStatusDao);
        $result = $this->employmentStatusService->getSearchEmploymentStatusesCount($employmentStatusSearchParams);
        $this->assertEquals(3, $result);
    }
}
