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
}
