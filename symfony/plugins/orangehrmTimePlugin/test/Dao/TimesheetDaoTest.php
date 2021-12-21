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
use OrangeHRM\Time\Dto\MyTimesheetSearchFilterParams;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;

class TimesheetDaoTest extends KernelTestCase
{
    /**
     * @var TimesheetDao
     */
    private TimesheetDao $timesheetDao;

    /**
     * @var string
     */
    protected string $fixtures;

    /**
     * @var int
     */
    private int $timesheetId = 1;

    /**
     * @var int
     */
    private int $authEmpNumber=1;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->timesheetDao = new TimesheetDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/MyTimesheetAPITest.yml';
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
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/TimesheetActionLogDao.yml';
        TestDataService::populate($this->fixture);
        $resultFalse = $this->timesheetDao->hasTimesheetForStartDate(2, new DateTime('2011-03-18'));
        $resultTrue = $this->timesheetDao->hasTimesheetForStartDate(1, new DateTime('2011-04-18'));
        $this->assertFalse($resultFalse);
        $this->assertTrue($resultTrue);
    }

    public function testGetTimesheetActionLogs(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/TimesheetActionLogDao.yml';
        TestDataService::populate($this->fixture);
        $timesheetActionLogSearchFilterParamHolder = new TimesheetActionLogSearchFilterParams();
        $timesheetActionLogs = $this->timesheetDao
            ->getTimesheetActionLogs($this->timesheetId, $timesheetActionLogSearchFilterParamHolder);
        $this->assertCount(6, $timesheetActionLogs);
        $this->assertInstanceOf(TimesheetActionLog::class, $timesheetActionLogs[1]);
    }

    public function testAddTimesheetActionLog(): void
    {
        $timesheet = $this->timesheetDao->getTimesheetById(1);
        $timesheetActionLog = new TimesheetActionLog();
        $timesheetActionLog->setAction("APPROVED");
        $timesheetActionLog->setComment("Good Job");
        $timesheetActionLog->setTimesheet($timesheet);
        $timesheetActionLog->setDate(new DateTime("2021-12-20"));
        $timesheetActionLog->getDecorator()->setUserId(1);

        $result = $this->timesheetDao->saveTimesheetActionLog($timesheetActionLog);

        $this->assertTrue($result instanceof TimesheetActionLog);
        $this->assertEquals(new DateTime("2021-12-20"), $result->getDate());
        $this->assertEquals("APPROVED", $result->getAction());
        $this->assertEquals("Good Job", $result->getComment());
        $this->assertEquals(1, $result->getPerformedUser()->getId());
    }

    public function testGetMyTimesheets(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/MyTimesheetAPITest.yml';
        TestDataService::populate($this->fixture);
        $myTimesheetParamHolder = new MyTimesheetSearchFilterParams();
        $myTimesheetParamHolder->setAuthEmpNumber(1);
        $myTimesheetParamHolder->setFromDate(new DateTime('2011-04-18'));
        $myTimesheetParamHolder->setToDate(new DateTime('2011-05-27'));
        $myTimeSheets = $this->timesheetDao->getTimesheetByStartAndEndDate(
            $myTimesheetParamHolder
        );
        $this->assertCount(1, $myTimeSheets);
        $this->assertInstanceOf(Timesheet::class, $myTimeSheets[0]);
    }
}
