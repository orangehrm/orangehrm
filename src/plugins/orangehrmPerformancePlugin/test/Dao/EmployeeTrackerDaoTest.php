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

namespace OrangeHRM\Performance\test\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Performance\Dao\EmployeeTrackerDao;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeTrackerDaoTest extends TestCase
{
    private EmployeeTrackerDao $employeeTrackerDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeTrackerDao = new EmployeeTrackerDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/EmployeeTrackerDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeTrackerList(): void
    {
        $expectedTrackerList = TestDataService::loadObjectList(PerformanceTracker::class, $this->fixture, 'PerformanceTracker');
        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $result = $this->employeeTrackerDao->getEmployeeTrackerList($employeeTrackerSearchFilterParams);

        $this->assertSameSize($expectedTrackerList, $result);
        for ($i = 0; $i <= 2; $i++) {
            $this->assertEquals($expectedTrackerList[$i]->getTrackerName(), $result[$i]->getTrackerName());
            $this->assertEquals($expectedTrackerList[$i]->getAddedDate(), $result[$i]->getAddedDate());
            $this->assertEquals($expectedTrackerList[$i]->getEmployee(), $result[$i]->getEmployee());
        }
    }

    public function testGetEmployeeTrackerCount(): void
    {
        $expectedTrackerList = TestDataService::loadObjectList(PerformanceTracker::class, $this->fixture, 'PerformanceTracker');
        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $result = $this->employeeTrackerDao->getEmployeeTrackerCount($employeeTrackerSearchFilterParams);

        $this->assertEquals(count($expectedTrackerList), $result);
    }
}
