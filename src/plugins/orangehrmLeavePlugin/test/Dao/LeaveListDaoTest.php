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

namespace Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Leave\Dao\LeaveListDao;
use OrangeHRM\Leave\Dto\LeaveListSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class LeaveListDaoTest extends KernelTestCase
{
    private LeaveListDao $leaveListDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->leaveListDao = new LeaveListDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeaveListDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeOnLeaveList(): void
    {
        $leaveListSearchFilterParams = new LeaveListSearchFilterParams();
        $leaveListSearchFilterParams->setDate(new DateTime('2022-09-01'));

        $employeeOnLeave = $this->leaveListDao->getEmployeeOnLeaveList($leaveListSearchFilterParams);

        $this->assertCount(1, $employeeOnLeave);
        $this->assertTrue(is_array($this->leaveListDao->getEmployeeOnLeaveList($leaveListSearchFilterParams)));
        $this->assertEquals('Kayla', $employeeOnLeave[0]['firstName']);
        $this->assertEquals('0004', $employeeOnLeave[0]['employeeId']);

        $leaveListSearchFilterParams->setDate(new DateTime('2022-10-29'));
        $employeeOnLeave = $this->leaveListDao->getEmployeeOnLeaveList($leaveListSearchFilterParams);
        $this->assertCount(1, $employeeOnLeave);
        $this->assertEquals('1', $employeeOnLeave[0]['status']);
        $this->assertEquals('4.00', $employeeOnLeave[0]['lengthHours']);
    }
}
