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

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Dashboard\Dao\QuickLaunchDao;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Dashboard
 * @group Dao
 */
class QuickLaunchDaoTest extends KernelTestCase
{
    private QuickLaunchDao $quickLaunchDao;
    private string $fixtureDir;

    protected function setUp(): void
    {
        $this->quickLaunchDao = new QuickLaunchDao();
        $this->fixtureDir = Config::get(Config::PLUGINS_DIR) . '/orangehrmDashboardPlugin/test/fixtures';
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
        ]);
    }

    public function testGetQuickLaunchList1(): void
    {
        // Modules enabled and periods defined
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao1.yml');
        $expected = ['leave.assign_leave', 'leave.leave_list', 'leave.apply_leave', 'leave.my_leave', 'time.employee_timesheet', 'time.my_timesheet'];
        $this->assertEquals($expected, $this->quickLaunchDao->getQuickLaunchList());
    }

    public function testGetQuickLaunchListForESS1(): void
    {
        // Modules enabled and periods defined
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao1.yml');
        $expected = ['leave.apply_leave', 'leave.my_leave', 'time.my_timesheet'];
        $this->assertEquals($expected, $this->quickLaunchDao->getQuickLaunchListForESS());
    }

    public function testGetQuickLaunchList2(): void
    {
        // Modules enabled and periods undefined
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao2.yml');
        $this->assertEmpty($this->quickLaunchDao->getQuickLaunchList());
    }

    public function testGetQuickLaunchListForESS2(): void
    {
        // Modules enabled and periods undefined
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao2.yml');
        $this->assertEmpty($this->quickLaunchDao->getQuickLaunchListForESS());
    }

    public function testGetQuickLaunchList3(): void
    {
        // Leave module disabled
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao3.yml');
        $expected = ['time.employee_timesheet', 'time.my_timesheet'];
        $this->assertEquals($expected, $this->quickLaunchDao->getQuickLaunchList());
    }

    public function testGetQuickLaunchListForESS3(): void
    {
        // Leave module disabled
        TestDataService::populate($this->fixtureDir . '/QuickLaunchDao3.yml');
        $expected = ['time.my_timesheet'];
        $this->assertEquals($expected, $this->quickLaunchDao->getQuickLaunchListForESS());
    }
}
