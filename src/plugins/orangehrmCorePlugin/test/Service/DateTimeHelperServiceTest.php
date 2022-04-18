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
}
