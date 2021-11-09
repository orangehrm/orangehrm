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
use Exception;
use Generator;
use InvalidArgumentException;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeavePeriodDao;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Leave
 * @group Service
 */
class LeavePeriodServiceTest extends KernelTestCase
{
    private LeavePeriodService $leavePeriodService;

    protected function setUp(): void
    {
        $this->leavePeriodService = new LeavePeriodService();
    }

    public function testGetListOfMonths(): void
    {
        $expected = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $result = $this->leavePeriodService->getListOfMonths();
        $this->assertEquals($expected, $result);
    }

    public function testGetListOfDates(): void
    {
        /* Checking for days with 31 days */
        $expected = range(1, 31);
        $result = $this->leavePeriodService->getListOfDates(1); // January
        $this->assertEquals($expected, $result, 'Wrong date range fetched for January');
        $result = $this->leavePeriodService->getListOfDates(3); // March
        $this->assertEquals($expected, $result, 'Wrong date range fetched for March');
        $result = $this->leavePeriodService->getListOfDates(5); // May
        $this->assertEquals($expected, $result, 'Wrong date range fetched for May');
        $result = $this->leavePeriodService->getListOfDates(7); // July
        $this->assertEquals($expected, $result, 'Wrong date range fetched for July');
        $result = $this->leavePeriodService->getListOfDates(8); // August
        $this->assertEquals($expected, $result, 'Wrong date range fetched for August');
        $result = $this->leavePeriodService->getListOfDates(10); // October
        $this->assertEquals($expected, $result, 'Wrong date range fetched for October');
        $result = $this->leavePeriodService->getListOfDates(12); // December
        $this->assertEquals($expected, $result, 'Wrong date range fetched for December');

        /* Checking for days with 30 days */
        $expected = range(1, 30);
        $result = $this->leavePeriodService->getListOfDates(4); // April
        $this->assertEquals($expected, $result, 'Wrong date range fetched for April');
        $result = $this->leavePeriodService->getListOfDates(6); // June
        $this->assertEquals($expected, $result, 'Wrong date range fetched for June');
        $result = $this->leavePeriodService->getListOfDates(9); // September
        $this->assertEquals($expected, $result, 'Wrong date range fetched for September');
        $result = $this->leavePeriodService->getListOfDates(11); // November
        $this->assertEquals($expected, $result, 'Wrong date range fetched for November');

        /* Checking for February; Should return maximum 28 days */
        $expected = range(1, 28);
        $result = $this->leavePeriodService->getListOfDates(2);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February');

        /* Checking for February; Should return maximum 28 days if $isLeapYear parameter is false */
        $expected = range(1, 29);
        $result = $this->leavePeriodService->getListOfDates(2, true);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February for leap years');

        $expected = range(1, 28);
        $result = $this->leavePeriodService->getListOfDates(2, false);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February non leap years');


        /* Checking for invalid month values */
        try {
            $this->leavePeriodService->getListOfDates(-1);
            $this->fail('getListOfDates() should not accept invalid month values');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof InvalidArgumentException);
            $this->assertEquals(
                'Invalid value passed for month in ' . LeavePeriodService::class . '::getListOfDates',
                $e->getMessage()
            );
        }

        try {
            $this->leavePeriodService->getListOfDates(13);
            $this->fail('getListOfDates() should not accept invalid month values');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof InvalidArgumentException);
            $this->assertEquals(
                'Invalid value passed for month in ' . LeavePeriodService::class . '::getListOfDates',
                $e->getMessage()
            );
        }
    }

    public function testGetLeavePeriodDao(): void
    {
        $leavePeriodDao = $this->leavePeriodService->getLeavePeriodDao();
        $this->assertTrue($leavePeriodDao instanceof LeavePeriodDao);
    }

    public function testGetCurrentLeavePeriodStartDateAndMonth(): void
    {
        $leavePeriod = new LeavePeriodHistory();
        $leavePeriod->setStartMonth(1);
        $leavePeriod->setStartDay(2);
        $calledGetCurrentLeavePeriodStartDateAndMonth = 3;
        $dao = $this->getMockBuilder(LeavePeriodDao::class)
            ->onlyMethods(['getCurrentLeavePeriodStartDateAndMonth'])
            ->getMock();
        $dao->expects($this->exactly($calledGetCurrentLeavePeriodStartDateAndMonth))
            ->method('getCurrentLeavePeriodStartDateAndMonth')
            ->willReturn($leavePeriod, $leavePeriod, null);
        $this->leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getLeavePeriodDao'])
            ->getMock();
        $this->leavePeriodService->expects($this->exactly($calledGetCurrentLeavePeriodStartDateAndMonth))
            ->method('getLeavePeriodDao')
            ->willReturn($dao);
        $leavePeriod = $this->leavePeriodService->getCurrentLeavePeriodStartDateAndMonth();
        $this->assertEquals(1, $leavePeriod->getStartMonth());
        $this->assertEquals(2, $leavePeriod->getStartDay());

        // without force reload
        $leavePeriod = $this->leavePeriodService->getCurrentLeavePeriodStartDateAndMonth();
        $this->assertEquals(1, $leavePeriod->getStartMonth());
        $this->assertEquals(2, $leavePeriod->getStartDay());

        // with force reload
        $leavePeriod = $this->leavePeriodService->getCurrentLeavePeriodStartDateAndMonth(true);
        $this->assertEquals(1, $leavePeriod->getStartMonth());
        $this->assertEquals(2, $leavePeriod->getStartDay());

        // with null result
        $leavePeriod = $this->leavePeriodService->getCurrentLeavePeriodStartDateAndMonth(true);
        $this->assertNull($leavePeriod);
    }

    public function testGetCurrentLeavePeriodWhenNotDefined(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(false);

        $this->createKernelWithMockServices([Services::LEAVE_CONFIG_SERVICE => $leaveConfigService]);
        $this->assertNull($this->leavePeriodService->getCurrentLeavePeriod());
    }

    public function testGetCurrentLeavePeriod(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(true);

        $dataTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dataTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-08-25'));
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::DATETIME_HELPER_SERVICE => $dataTimeHelper
            ]
        );

        $this->leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodByDate'])
            ->getMock();
        $this->leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriodByDate')
            ->with(new DateTime('2021-08-25'))
            ->willReturn(new LeavePeriod());
        $this->assertTrue($this->leavePeriodService->getCurrentLeavePeriod() instanceof LeavePeriod);
    }

    public function testGetNormalizedCurrentLeavePeriod(): void
    {
        $this->leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriod'])
            ->getMock();
        $this->leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2021-01-01'), new DateTime('2021-12-31')));

        $this->createKernelWithMockServices([Services::NORMALIZER_SERVICE => new NormalizerService()]);
        $this->assertEquals(
            [
                'startDate' => '2021-01-01',
                'endDate' => '2021-12-31'
            ],
            $this->leavePeriodService->getNormalizedCurrentLeavePeriod()
        );
    }

    public function testGetMaxAllowedLeavePeriodEndDateWhenLeavePeriodNotDefined(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(false);

        $this->createKernelWithMockServices([Services::LEAVE_CONFIG_SERVICE => $leaveConfigService]);
        $this->assertNull($this->leavePeriodService->getMaxAllowedLeavePeriodEndDate());
    }

    /**
     * @dataProvider getMaxAllowedLeavePeriodEndDateDataProvider
     */
    public function testGetMaxAllowedLeavePeriodEndDate(
        string $now,
        string $createdAt,
        string $expected,
        int $startMonth = 1,
        int $startDay = 1
    ): void {
        $this->leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['_getLeavePeriodHistoryList'])
            ->getMock();

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth($startMonth);
        $leavePeriodHistory->setStartDay($startDay);
        $leavePeriodHistory->setCreatedAt(new DateTime($createdAt));
        $this->leavePeriodService->expects($this->once())
            ->method('_getLeavePeriodHistoryList')
            ->willReturn([$leavePeriodHistory]);

        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(true);
        $dateTimeHelperService = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelperService->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime($now));
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelperService
            ]
        );

        $this->assertEquals($expected, $this->leavePeriodService->getMaxAllowedLeavePeriodEndDate()->format('Y-m-d'));
    }

    /**
     * @return Generator
     */
    public function getMaxAllowedLeavePeriodEndDateDataProvider(): Generator
    {
        yield ['2021-11-01', '2020-02-15', '2022-12-31'];
        yield ['2022-11-01', '2020-02-15', '2023-12-31'];
        yield ['2022-11-01', '2020-02-15', '2024-01-14', 1, 15];
        yield ['2022-11-01', '2020-02-15', '2024-02-27', 2, 28];
        yield ['2022-11-01', '2000-02-15', '2024-02-27', 2, 28];
        yield ['2100-11-01', '2022-01-02', '2101-12-30', 12, 31];
    }
}
