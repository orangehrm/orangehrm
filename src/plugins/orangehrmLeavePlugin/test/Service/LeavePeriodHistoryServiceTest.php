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

namespace OrangeHRM\Tests\Leave\Service;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Service
 */
class LeavePeriodHistoryServiceTest extends KernelTestCase
{
    private LeavePeriodService $leavePeriodService;

    protected function setUp(): void
    {
        $this->leavePeriodService = new LeavePeriodService();
        $fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmLeavePlugin/test/fixtures/LeavePeriodHistoryService.yml';
        TestDataService::truncateSpecificTables([LeavePeriodHistory::class, \OrangeHRM\Entity\Config::class]);
        TestDataService::populate($fixture);
    }

    public function testGetGeneratedLeavePeriodListDateIsNotSet(): void
    {
        $this->expectException(ServiceException::class);
        $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
    }

    public function testGetGeneratedLeavePeriodListDefineAs2012Jan1st(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-02'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);


        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);

        $expected = [
            ['2010-01-01', '2010-12-31'],
            ['2011-01-01', '2011-12-31'],
            ['2012-01-01', '2012-12-31'],
            ['2013-01-01', '2013-12-31']
        ];

        // extend range till next year end:
        $now = new DateTime();

        $nextYear = intval($now->format('Y')) + 1;
        $this->assertTrue(
            $nextYear > 2012,
            'System clock set to past!. Test should be run with system date 2012 or later.'
        );

        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = [$year . '-01-01', $year . '-12-31'];
            }
        }

        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    public function testGetGeneratedLeavePeriodListDefineAs2010Jan1st(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-02'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $expected = [
            ['2010-01-01', '2010-12-31'],
            ['2011-01-01', '2011-12-31'],
            ['2012-01-01', '2012-12-31'],
            ['2013-01-01', '2013-12-31']
        ];

        // extend range till next year end:
        $now = new DateTime();

        $nextYear = intval($now->format('Y')) + 1;
        $this->assertTrue(
            $nextYear > 2012,
            'System clock set to past!. Test should be run with system date 2012 or later.'
        );

        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = [$year . '-01-01', $year . '-12-31'];
            }
        }

        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    /* Fails if run in 2014 */
    public function testGetGeneratedLeavePeriodListForLeapYear(): void
    {
        $thisYear = date('Y');

        $startYear = $thisYear - 3;
        $nextYear = $thisYear + 1;

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(3);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime($startYear . '-01-02'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $expected = [];

        for ($year = $startYear - 1; $year <= $nextYear; $year++) {
            $expected[] = [$year . '-03-01', $this->getLastDayInFebruary($year + 1)];
        }
        $currentDate = date('Y-m-d H:i:s');
        $leavePeriodStartDateForCurrentYear = date($this->getLastDayInFebruary(date('Y')) . ' H:i:s');
        if (strtotime($currentDate) < strtotime($leavePeriodStartDateForCurrentYear)) {
            array_pop($expected);
        }

        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    public function testGetGeneratedLeavePeriodListDefineAs2010Jan1stAnd2012Jan1st(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-10-02'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_PERIOD_SERVICE => $this->leavePeriodService,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2011-08-04'));

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2012-08-02'));

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);


        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);

        $expected = [
            ['2010-01-01', '2010-12-31'],
            ['2011-01-01', '2011-12-31'],
            ['2012-01-01', '2012-12-31'],
            ['2013-01-01', '2013-12-31']
        ];

        // extend range till next year end:
        $now = new DateTime();

        $nextYear = intval($now->format('Y')) + 1;
        $this->assertTrue(
            $nextYear > 2012,
            'System clock set to past!. Test should be run with system date 2012 or later.'
        );

        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = [$year . '-01-01', $year . '-12-31'];
            }
        }

        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    /* Fails if run in 2014 */
    public function testGetGeneratedLeavePeriodListCase1(): void
    {
        $thisYear = date('Y');
        $expected = [];

        $threeYearsAgo = $thisYear - 3;
        $twoYearsAgo = $thisYear - 2;
        $oneYearAgo = $thisYear - 1;
        $nextYear = $thisYear + 1;
        $nextNextYear = $thisYear + 2;

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime($threeYearsAgo . '-10-02'));
        $expected[] = [$threeYearsAgo . '-01-01', $threeYearsAgo . '-12-31'];


        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_PERIOD_SERVICE => $this->leavePeriodService,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(2);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime($twoYearsAgo . '-08-04'));
        $expected[] = [$twoYearsAgo . '-01-01', $oneYearAgo . '-01-31'];

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(3);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime($oneYearAgo . '-08-02'));
        $expected[] = [$oneYearAgo . '-02-01', $this->getLastDayInFebruary($thisYear)];

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $expected[] = [$thisYear . '-03-01', $this->getLastDayInFebruary($nextYear)];

        $currentDate = date('Y-m-d H:i:s');
        $leavePeriodStartDateForCurrentYear = date($this->getLastDayInFebruary(date('Y')) . ' H:i:s');
        if (strtotime($currentDate) >= strtotime($leavePeriodStartDateForCurrentYear)) {
            $expected[] = [$nextYear . '-03-01', $this->getLastDayInFebruary($nextNextYear)];
        }

        // work around for cached generated leave period list
        $newLeavePeriodService = new LeavePeriodService();
        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
            ]
        );
        $result = $newLeavePeriodService->getGeneratedLeavePeriodList(null, true);

        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    /**
     * Returns the last day of the given year in yyy-mm-dd format.
     *
     * @param String $year Full year with 4 digits
     */
    protected function getLastDayInFebruary($year)
    {
        $lastDay = date('L', strtotime("$year-01-01")) ? '29' : '28';
        return "{$year}-02-{$lastDay}";
    }

    public function testGetGeneratedLeavePeriodListCase2(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(2);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-01'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_PERIOD_SERVICE => $this->leavePeriodService,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-02'));

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(2);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-02'));

        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        // work around for cached generated leave period list
        $newLeavePeriodService = new LeavePeriodService();
        $result = $newLeavePeriodService->getGeneratedLeavePeriodList(null, true);

        $expected = [
            ['2009-02-01', '2011-01-01'],
            ['2011-01-02', '2012-01-01'],
            ['2012-01-02', '2013-01-01'],
            ['2013-01-02', '2014-01-01']
        ];

        // extend range till next year end:
        $now = new DateTime();

        $nextYear = intval($now->format('Y')) + 1;
        $this->assertTrue(
            $nextYear > 2012,
            'System clock set to past!. Test should be run with system date 2012 or later.'
        );

        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = [$year . '-01-02', ($year + 1) . '-01-01'];
            }
        }

        $this->assertEquals($expected, $this->convertLeavePeriodArrayToYmdArray($result));
    }

    public function testGetCurrentLeavePeriodByDate(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2010-01-02'));

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_PERIOD_SERVICE => $this->leavePeriodService,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $this->leavePeriodService->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);

        $result = $this->leavePeriodService->getCurrentLeavePeriodByDate(new DateTime('2012-01-01'), true);

        $this->assertEquals(['2012-01-01', '2012-12-31'], $this->convertLeavePeriodToArray($result));

        $result = $this->leavePeriodService->getCurrentLeavePeriodByDate(new DateTime('2013-01-04'), true);

        $this->assertEquals(['2013-01-01', '2013-12-31'], $this->convertLeavePeriodToArray($result));
    }

    /**
     * @param LeavePeriod[] $leavePeriods
     * @return string[]
     */
    private function convertLeavePeriodArrayToYmdArray(array $leavePeriods): array
    {
        return array_map(fn (LeavePeriod $leavePeriod) => $this->convertLeavePeriodToArray($leavePeriod), $leavePeriods);
    }

    /**
     * @param LeavePeriod $leavePeriod
     * @return string[]
     */
    private function convertLeavePeriodToArray(LeavePeriod $leavePeriod): array
    {
        return [$leavePeriod->getYmdStartDate(), $leavePeriod->getYmdEndDate()];
    }
}
