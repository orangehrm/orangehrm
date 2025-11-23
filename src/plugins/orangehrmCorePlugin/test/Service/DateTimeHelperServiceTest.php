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

namespace OrangeHRM\Tests\Core\Service;

use DateTime;
use DateTimeZone;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Service
 */
class DateTimeHelperServiceTest extends TestCase
{
    private DateTimeHelperService $dateTimeHelperService;

    protected function setUp(): void
    {
        $this->dateTimeHelperService = new DateTimeHelperService();
    }

    public function testFormatDateTimeToYmd(): void
    {
        $date = '2020-05-23';
        $this->assertEquals($date, $this->dateTimeHelperService->formatDateTimeToYmd(new DateTime($date)));

        $dateTime = new DateTime('2020-05-23', new DateTimeZone('Pacific/Auckland'));
        $dateTime->setTimezone(new DateTimeZone('Europe/London'));
        $this->assertEquals('2020-05-22', $this->dateTimeHelperService->formatDateTimeToYmd($dateTime));

        $date = '2020-05-23';
        $dateTime = new DateTime($date, new DateTimeZone('Europe/London'));
        $this->assertEquals($date, $this->dateTimeHelperService->formatDateTimeToYmd($dateTime));

        $this->assertNull($this->dateTimeHelperService->formatDateTimeToYmd(null));
    }

    public function testFormatDateTimeToTimeString(): void
    {
        $date = '2020-05-23 10:13:03';
        $this->assertEquals('10:13', $this->dateTimeHelperService->formatDateTimeToTimeString(new DateTime($date)));

        $dateTime = new DateTime('2020-05-23 10:13:03', new DateTimeZone('Pacific/Auckland'));
        $dateTime->setTimezone(new DateTimeZone('Europe/London'));
        $this->assertEquals('23:13', $this->dateTimeHelperService->formatDateTimeToTimeString($dateTime));

        $date = '2020-05-23 10:13';
        $dateTime = new DateTime($date, new DateTimeZone('Europe/London'));
        $this->assertEquals('10:13', $this->dateTimeHelperService->formatDateTimeToTimeString($dateTime));

        $this->assertNull($this->dateTimeHelperService->formatDateTimeToTimeString(null));
    }

    public function testIsDatesEqual(): void
    {
        $date = '2020-05-23';
        $this->assertTrue($this->dateTimeHelperService->isDatesEqual(new DateTime($date), new DateTime($date)));

        $this->assertFalse(
            $this->dateTimeHelperService->isDatesEqual(
                new DateTime('2020-05-23'),
                new DateTime('2020-05-24')
            )
        );

        $this->assertTrue(
            $this->dateTimeHelperService->isDatesEqual(
                new DateTime('2020-05-23', new DateTimeZone('Pacific/Auckland')),
                new DateTime('2020-05-23', new DateTimeZone('Europe/London'))
            )
        );

        $date1 = new DateTime('2020-05-23', new DateTimeZone('Pacific/Auckland'));
        $date2 = new DateTime('2020-05-23', new DateTimeZone('Pacific/Auckland'));
        $date2->setTimezone(new DateTimeZone('Europe/London'));
        $this->assertFalse($this->dateTimeHelperService->isDatesEqual($date1, $date2));

        $date1 = new DateTime('2020-05-23', new DateTimeZone('Pacific/Auckland'));
        $date2 = new DateTime('2020-05-24', new DateTimeZone('Pacific/Auckland'));
        $date2->setTimezone(new DateTimeZone('Europe/London'));
        $this->assertTrue($this->dateTimeHelperService->isDatesEqual($date1, $date2));

        $date = '2020-05-23';
        $this->assertFalse($this->dateTimeHelperService->isDatesEqual(new DateTime($date), null));

        $date = '2020-05-23';
        $this->assertFalse($this->dateTimeHelperService->isDatesEqual(null, new DateTime($date)));

        $this->assertFalse($this->dateTimeHelperService->isDatesEqual(null, null));
        $this->assertTrue($this->dateTimeHelperService->isDatesEqual(null, null, true));
    }

    public function testDateDiffInHours(): void
    {
        $this->assertEquals(
            8,
            $this->dateTimeHelperService
                ->dateDiffInHours(new DateTime('09:00'), new DateTime('17:00'))
        );

        $this->assertEquals(
            8.25,
            $this->dateTimeHelperService
                ->dateDiffInHours(new DateTime('08:45'), new DateTime('17:00'))
        );

        $this->assertEquals(
            32.25,
            $this->dateTimeHelperService
                ->dateDiffInHours(new DateTime('2021-08-04 08:45'), new DateTime('2021-08-05 17:00'))
        );

        $this->assertEquals(
            776.25,
            $this->dateTimeHelperService
                ->dateDiffInHours(new DateTime('2021-08-04 08:45'), new DateTime('2021-09-05 17:00'))
        );
    }

    public function testDateRange(): void
    {
        $dates = $this->dateTimeHelperService
            ->dateRange(new DateTime('2021-10-05'), new DateTime('2021-10-10'));
        $this->assertDateArray(
            [
                '2021-10-05',
                '2021-10-06',
                '2021-10-07',
                '2021-10-08',
                '2021-10-09',
                '2021-10-10',
            ],
            $dates
        );

        $dates = $this->dateTimeHelperService
            ->dateRange(new DateTime('2020-02-27'), new DateTime('2020-03-02'));
        $this->assertDateArray(
            [
                '2020-02-27',
                '2020-02-28',
                '2020-02-29',
                '2020-03-01',
                '2020-03-02',
            ],
            $dates
        );

        $dates = $this->dateTimeHelperService
            ->dateRange(new DateTime('2021-02-27'), new DateTime('2021-03-02'));
        $this->assertDateArray(
            [
                '2021-02-27',
                '2021-02-28',
                '2021-03-01',
                '2021-03-02',
            ],
            $dates
        );

        $dates = $this->dateTimeHelperService
            ->dateRange(new DateTime('2021-02-27'), new DateTime('2021-03-02'), 'P2D');
        $this->assertDateArray(
            [
                '2021-02-27',
                '2021-03-01',
            ],
            $dates
        );

        $dates = $this->dateTimeHelperService
            ->dateRange(new DateTime('2021-02-27'), new DateTime('2021-02-27'));
        $this->assertDateArray(
            ['2021-02-27'],
            $dates
        );
    }

    /**
     * @param array $expected
     * @param DateTime[] $actual
     */
    private function assertDateArray(array $expected, array $actual): void
    {
        $this->assertEquals(
            $expected,
            array_map(
                function (DateTime $dateTime) {
                    return $dateTime->format('Y-m-d');
                },
                $actual
            )
        );
    }

    public function testGetNow()
    {
        $this->assertEquals((new DateTime())->getTimezone(), $this->dateTimeHelperService->getNow()->getTimezone());
        $this->assertEquals((new DateTime())->getOffset(), $this->dateTimeHelperService->getNow()->getOffset());
    }

    public function testGetTimezoneByTimezoneOffset(): void
    {
        $this->assertEquals(new DateTimeZone('+0530'), $this->dateTimeHelperService->getTimezoneByTimezoneOffset(5.5));
        $this->assertEquals(new DateTimeZone('-0530'), $this->dateTimeHelperService->getTimezoneByTimezoneOffset(-5.5));
        $this->assertEquals(new DateTimeZone('+0500'), $this->dateTimeHelperService->getTimezoneByTimezoneOffset(5.0));
    }

    public function testGetWeekBoundaryForGivenDate(): void
    {
        //week start date is monday => index 1
        $this->assertEquals(
            ['2022-09-12', '2022-09-18'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-18'), 1)
        );
        $this->assertEquals(
            ['2022-09-19', '2022-09-25'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 1)
        );
        $this->assertEquals(
            ['2022-09-19', '2022-09-25'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 1)
        );
        $this->assertEquals(
            ['2022-09-19', '2022-09-25'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 1)
        );
        $this->assertEquals(
            ['2022-09-19', '2022-09-25'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 1)
        );
        $this->assertEquals(
            ['2022-09-26', '2022-10-02'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 1)
        );
        $this->assertEquals(
            ['2022-12-26', '2023-01-01'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 1)
        );

        //week start date is tuesday => index 2
        $this->assertEquals(
            ['2022-09-13', '2022-09-19'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 2)
        );
        $this->assertEquals(
            ['2022-09-20', '2022-09-26'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 2)
        );
        $this->assertEquals(
            ['2022-09-20', '2022-09-26'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 2)
        );
        $this->assertEquals(
            ['2022-09-20', '2022-09-26'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 2)
        );
        $this->assertEquals(
            ['2022-09-20', '2022-09-26'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 2)
        );
        $this->assertEquals(
            ['2022-12-20', '2022-12-26'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 2)
        );

        //week start date is wednesday => index 3
        $this->assertEquals(
            ['2022-09-14', '2022-09-20'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 3)
        );
        $this->assertEquals(
            ['2022-09-14', '2022-09-20'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 3)
        );
        $this->assertEquals(
            ['2022-09-21', '2022-09-27'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 3)
        );
        $this->assertEquals(
            ['2022-09-21', '2022-09-27'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 3)
        );
        $this->assertEquals(
            ['2022-09-21', '2022-09-27'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 3)
        );
        $this->assertEquals(
            ['2022-12-21', '2022-12-27'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 3)
        );

        //week start date is thursday => index 4
        $this->assertEquals(
            ['2022-09-15', '2022-09-21'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-18'), 4)
        );
        $this->assertEquals(
            ['2022-09-15', '2022-09-21'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 4)
        );
        $this->assertEquals(
            ['2022-09-15', '2022-09-21'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 4)
        );
        $this->assertEquals(
            ['2022-09-22', '2022-09-28'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 4)
        );
        $this->assertEquals(
            ['2022-09-22', '2022-09-28'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 4)
        );
        $this->assertEquals(
            ['2022-09-22', '2022-09-28'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 4)
        );
        $this->assertEquals(
            ['2022-12-22', '2022-12-28'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 4)
        );

        //week start date is friday => index 5
        $this->assertEquals(
            ['2022-09-16', '2022-09-22'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-18'), 5)
        );
        $this->assertEquals(
            ['2022-09-16', '2022-09-22'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 5)
        );
        $this->assertEquals(
            ['2022-09-16', '2022-09-22'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 5)
        );
        $this->assertEquals(
            ['2022-09-23', '2022-09-29'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 5)
        );
        $this->assertEquals(
            ['2022-09-23', '2022-09-29'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 5)
        );
        $this->assertEquals(
            ['2022-09-23', '2022-09-29'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 5)
        );
        $this->assertEquals(
            ['2022-12-23', '2022-12-29'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 5)
        );

        //week start date is saturday => index 6
        $this->assertEquals(
            ['2022-09-17', '2022-09-23'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-18'), 6)
        );
        $this->assertEquals(
            ['2022-09-17', '2022-09-23'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 6)
        );
        $this->assertEquals(
            ['2022-09-17', '2022-09-23'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 6)
        );
        $this->assertEquals(
            ['2022-09-24', '2022-09-30'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 6)
        );
        $this->assertEquals(
            ['2022-09-24', '2022-09-30'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 6)
        );
        $this->assertEquals(
            ['2022-09-24', '2022-09-30'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 6)
        );
        $this->assertEquals(
            ['2022-12-24', '2022-12-30'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 6)
        );

        //week start date is sunday => index 7
        $this->assertEquals(
            ['2022-09-11', '2022-09-17'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-17'), 7)
        );
        $this->assertEquals(
            ['2022-09-18', '2022-09-24'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-18'), 7)
        );
        $this->assertEquals(
            ['2022-09-18', '2022-09-24'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-19'), 7)
        );
        $this->assertEquals(
            ['2022-09-18', '2022-09-24'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-20'), 7)
        );
        $this->assertEquals(
            ['2022-09-18', '2022-09-24'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-24'), 7)
        );
        $this->assertEquals(
            ['2022-09-25', '2022-10-01'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-25'), 7)
        );
        $this->assertEquals(
            ['2022-09-25', '2022-10-01'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-09-26'), 7)
        );
        $this->assertEquals(
            ['2022-12-25', '2022-12-31'],
            $this->dateTimeHelperService->getWeekBoundaryForGivenDate(new DateTime('2022-12-26'), 7)
        );
    }
}
