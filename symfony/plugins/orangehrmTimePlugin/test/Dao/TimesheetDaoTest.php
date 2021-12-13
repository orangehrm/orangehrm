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

namespace OrangeHRM\Tests\Time\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\TimesheetDao;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;

class TimesheetDaoTest extends KernelTestCase
{
    private TimesheetDao $timesheetDao;
    private int $timesheetId = 1;
    protected string $fixtures;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->timesheetDao = new TimesheetDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR).'/orangehrmTimePlugin/test/fixtures/MyTimesheetAPITest.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddTimesheet(): void
    {
        $timesheet = new Timesheet();
        $timesheet->setState("NOT SUBMITTED");
        $timesheet->setStartDate(new DateTime("2021-01-01"));
        $timesheet->setEndDate(new DateTime("2021-01-06"));
        $timesheet->setEmployee($this->getEntityReference(Employee::class, 1));
        $result = $this->timesheetDao->saveTimesheet($timesheet);

        $this->assertTrue($result instanceof Timesheet);
        $this->assertEquals(new DateTime("2021-01-01"), $result->getStartDate());
        $this->assertEquals(new DateTime("2021-01-06"), $result->getEndDate());
        $this->assertEquals("NOT SUBMITTED", $result->getState());
    }

    public function testDuplicateTimesheet(): void
    {
        $resultFalse = $this->timesheetDao->hasTimesheetForStartDate(new DateTime('2011-04-18'));
        $resultTrue = $this->timesheetDao->hasTimesheetForStartDate(new DateTime('2011-03-18'));
        $this->assertFalse($resultFalse);
        $this->assertTrue($resultTrue);
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            .'/orangehrmTimePlugin/test/fixtures/TimesheetActionLogDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetTimesheetActionLogs(): void
    {
        $timesheetActionLogSearchFilterParamHolder = new TimesheetActionLogSearchFilterParams();
        $timesheetActionLogs = $this->timesheetDao->getTimesheetActionLogs(
            $this->timesheetId,
            $timesheetActionLogSearchFilterParamHolder
        );
        $this->assertCount(6, $timesheetActionLogs);
        $this->assertInstanceOf(TimesheetActionLog::class, $timesheetActionLogs[1]);
    }
}
