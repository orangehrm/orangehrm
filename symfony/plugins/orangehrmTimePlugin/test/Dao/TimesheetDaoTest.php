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
use LogicException;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Dao\TimesheetDao;
use OrangeHRM\Time\Dto\DefaultTimesheetSearchFilterParams;
use OrangeHRM\Time\Dto\EmployeeReportsSearchFilterParams;
use OrangeHRM\Time\Dto\EmployeeTimesheetListSearchFilterParams;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;
use OrangeHRM\Time\Dto\TimesheetSearchFilterParams;

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
    private int $empNumber = 1;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->timesheetDao = new TimesheetDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/TimesheetDao.yml';
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
        $myTimesheetParamHolder = new TimesheetSearchFilterParams();
        $myTimesheetParamHolder->setEmpNumber($this->empNumber);
        $myTimesheetParamHolder->setFromDate(new DateTime('2011-04-18'));
        $myTimesheetParamHolder->setToDate(new DateTime('2011-05-27'));
        $myTimeSheets = $this->timesheetDao->getTimesheetByStartAndEndDate(
            $myTimesheetParamHolder
        );
        $this->assertCount(1, $myTimeSheets);
        $this->assertInstanceOf(Timesheet::class, $myTimeSheets[0]);
    }

    public function testIsDuplicateTimesheetItem(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR).'/orangehrmTimePlugin/test/fixtures/TimesheetProjectActivityAPITest.yml';
        TestDataService::populate($this->fixture);
        $isDuplicateItem = $this->timesheetDao->isDuplicateTimesheetItem(1, 1, 1);
        $this->assertTrue($isDuplicateItem);
        $isDuplicateItem = $this->timesheetDao->isDuplicateTimesheetItem(1, 2, 1);
        $this->assertFalse($isDuplicateItem);
    }

    public function testAddTimesheetItem(): void
    {
        $timesheetItem = new TimesheetItem();
        $timesheetItem->setDate(new DateTime("2021-12-20"));
        $timesheetItem->getDecorator()->setTimesheetById(1);
        $timesheetItem->setComment("Good Job");
        $timesheetItem->getDecorator()->setProjectById(1);
        $timesheetItem->getDecorator()->setProjectActivityById(1);
        $timesheetItem->getDecorator()->setEmployeeByEmployeeNumber(1);

        $result = $this->timesheetDao->saveTimesheetItem($timesheetItem);

        $this->assertTrue($result instanceof TimesheetItem);
        $this->assertEquals(new DateTime("2021-12-20"), $result->getDate());
        $this->assertEquals("Good Job", $result->getComment());
        $this->assertEquals(1, $result->getProject()->getId());
        $this->assertEquals(1, $result->getProject()->getId());
        $this->assertEquals(1, $result->getEmployee()->getEmpNumber());
    }

    public function testGetTimesheetItemById(): void
    {
        $result = $this->timesheetDao->getTimesheetItemByTimesheetIdAndTimesheetItemId(1, 1);

        $this->assertTrue($result instanceof TimesheetItem);
        $this->assertEquals(new DateTime("2020-09-28"), $result->getDate());
        $this->assertEquals("Include meetings", $result->getComment());
        $this->assertEquals(1, $result->getProject()->getId());
        $this->assertEquals(1, $result->getProject()->getId());
        $this->assertEquals(1, $result->getEmployee()->getEmpNumber());
    }

    public function testGetTimesheetById(): void
    {
        $result = $this->timesheetDao->getTimesheetById(1);

        $this->assertTrue($result instanceof Timesheet);
        $this->assertEquals(new DateTime("2011-04-18"), $result->getStartDate());
        $this->assertEquals(new DateTime("2011-04-24"), $result->getEndDate());
        $this->assertEquals("CREATED", $result->getState());
        $this->assertEquals(1, $result->getEmployee()->getEmpNumber());
    }

    public function testSaveAndUpdateTimesheetItemsExceptionCase(): void
    {
        $timesheetItem = new TimesheetItem();
        $timesheetItem->getDecorator()->setProjectById(1);
        $timesheetItem->getDecorator()->setProjectActivityById(3);
        $timesheetItem->getDecorator()->setTimesheetById(1);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The project activity (id: 3) not belongs to provided project (id: 1)');
        $this->timesheetDao->saveAndUpdateTimesheetItems([$timesheetItem]);
    }

    public function testGetEmployeeTimesheetList(): void
    {
        $employeeTimesheetActionSearchParamHolder = new EmployeeTimesheetListSearchFilterParams();
        $employeeTimesheetActionSearchParamHolder->setEmployeeNumbers([2]);
        $employeeTimesheetActionSearchParamHolder->setActionableStatesList(["SUBMITTED"]);
        $result = $this->timesheetDao
            ->getEmployeeTimesheetList($employeeTimesheetActionSearchParamHolder);

        $this->assertTrue($result[0] instanceof Timesheet);
        $this->assertEquals(new DateTime("2011-05-22"), $result[0]->getStartDate());
        $this->assertEquals(new DateTime("2011-05-29"), $result[0]->getEndDate());
        $this->assertEquals("SUBMITTED", $result[0]->getState());
        $this->assertEquals(2, $result[0]->getEmployee()->getEmpNumber());
    }

    public function testGetEmployeeTimesheetListCount(): void
    {
        $employeeTimesheetActionSearchParamHolder = new EmployeeTimesheetListSearchFilterParams();
        $employeeTimesheetActionSearchParamHolder->setEmployeeNumbers([2]);
        $employeeTimesheetActionSearchParamHolder->setActionableStatesList(["SUBMITTED"]);
        $result = $this->timesheetDao
            ->getEmployeeTimesheetListCount($employeeTimesheetActionSearchParamHolder);

        $this->assertEquals(1, $result);
    }

    /**
     * @throws Exception
     */
    public function testGetLatestTimesheet(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR).'/orangehrmTimePlugin/test/fixtures/DefaultTimesheetTest.yml';
        TestDataService::populate($this->fixture);
        $defaultTimesheetSearchFilterParams = new DefaultTimesheetSearchFilterParams();
        //with date params
        $defaultTimesheetSearchFilterParams->setEmpNumber(2);
        $defaultTimesheetSearchFilterParams->setFromDate(new DateTime('2011-06-13'));
        $defaultTimesheetSearchFilterParams->setToDate(new DateTime('2011-06-19'));
        $timesheet = $this->timesheetDao->getDefaultTimesheet($defaultTimesheetSearchFilterParams);
        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals(4, $timesheet->getId());
        $this->assertEquals(new DateTime('2011-06-13'), $timesheet->getStartDate());
        $this->assertEquals(new DateTime('2011-06-19'), $timesheet->getEndDate());

        //without date params
        $defaultTimesheetSearchFilterParams = new DefaultTimesheetSearchFilterParams();
        $defaultTimesheetSearchFilterParams->setEmpNumber(2);
        $timesheet = $this->timesheetDao->getDefaultTimesheet($defaultTimesheetSearchFilterParams);
        $this->assertTrue($timesheet instanceof Timesheet);
        $this->assertEquals(4, $timesheet->getId());
        $this->assertEquals(new DateTime('2011-06-13'), $timesheet->getStartDate());
        $this->assertEquals(new DateTime('2011-06-19'), $timesheet->getEndDate());

        //no result
        $defaultTimesheetSearchFilterParams->setEmpNumber(2);
        $defaultTimesheetSearchFilterParams->setFromDate(new DateTime('2011-06-20'));
        $defaultTimesheetSearchFilterParams->setToDate(new DateTime('2011-06-26'));
        $timesheet = $this->timesheetDao->getDefaultTimesheet($defaultTimesheetSearchFilterParams);
        $this->assertFalse($timesheet instanceof Timesheet);
    }

    /**
     * @throws Exception
     */
    public function testGetTimesheetItemsForEmployeeReportAndTotalHours(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ).'/orangehrmTimePlugin/test/fixtures/EmployeeReportDataTest.yml';
        TestDataService::populate($this->fixture);
        //without filters
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 01', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(10800, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(18000, $totalDuration);

        //with filters - projectId
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setProjectId(2);
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 01', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(10800, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(10800, $totalDuration);

        //with filter - activityId
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setActivityId(2);
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 01', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(10800, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(10800, $totalDuration);

        //with filter - fromDate
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setFromDate(new DateTime('2020-10-23'));
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 02', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(3600, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(3600, $totalDuration);

        //with filter - toDate
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setToDate(new DateTime('2020-10-23'));
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 01', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(10800, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(14400, $totalDuration);

        //with filter - from date and to date
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setFromDate(new DateTime('2020-09-28'));
        $employeeReportsSearchFilterParams->setToDate(new DateTime('2020-09-29'));
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 00', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(3600, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(3600, $totalDuration);

        //with filter - timesheetState - all
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setIncludeTimesheets(
            EmployeeReportsSearchFilterParams::INCLUDE_TIMESHEETS_ALL
        );
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 02', $timesheetItems[1][0]->getProject()->getName());
        $this->assertEquals(3600, $timesheetItems[1]['totalDurationByGroup']);
        $this->assertEquals(18000, $totalDuration);

        //with filter - timesheetState - onlyApproved
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setIncludeTimesheets(
            EmployeeReportsSearchFilterParams::INCLUDE_TIMESHEETS_APPROVED_ONLY
        );
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 02', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(3600, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(3600, $totalDuration);

        //with filter - all filters
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(1);
        $employeeReportsSearchFilterParams->setProjectId(1);
        $employeeReportsSearchFilterParams->setActivityId(1);
        $employeeReportsSearchFilterParams->setFromDate(new DateTime('2020-09-27'));
        $employeeReportsSearchFilterParams->setToDate(new DateTime('2020-10-25'));
        $employeeReportsSearchFilterParams->setIncludeTimesheets(
            EmployeeReportsSearchFilterParams::INCLUDE_TIMESHEETS_APPROVED_ONLY
        );
        $timesheetItems = $this->timesheetDao->getTimesheetItemsForEmployeeReport($employeeReportsSearchFilterParams);
        $totalDuration = $this->timesheetDao->getTotalDurationForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals('Core project 00', $timesheetItems[0][0]->getProject()->getName());
        $this->assertEquals(7200, $timesheetItems[0]['totalDurationByGroup']);
        $this->assertEquals(7200, $totalDuration);
    }

    /**
     * @throws Exception
     */
    public function testGetTimesheetItemsCountForEmployeeReport(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ).'/orangehrmTimePlugin/test/fixtures/EmployeeReportDataTest.yml';
        TestDataService::populate($this->fixture);
        $employeeReportsSearchFilterParams = new EmployeeReportsSearchFilterParams();
        $employeeReportsSearchFilterParams->setEmpNumber(2);
        $employeeReportsSearchFilterParams->setIncludeTimesheets('onlyApproved');
        $count = $this->timesheetDao->getTimesheetItemsCountForEmployeeReport($employeeReportsSearchFilterParams);
        $this->assertEquals(1, $count);
    }

    public function testTimesheetAvailability(): void
    {
        $timesheetItem = $this->timesheetDao->getTimesheetItemByProjectIdAndTimesheetIdAndActivityIdAndDate(
            1,
            1,
            1,
            new DateTime("2021-01-01")
        );
        $this->assertNull($timesheetItem);
    }
}
