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

namespace OrangeHRM\Tests\Maintenance\PurgeStrategy\FormatValue;

use DateTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Maintenance\PurgeStrategy\FormatValue\FormatWithPurgeTime;
use OrangeHRM\Tests\Util\KernelTestCase;

class FormatWithPurgeTimeTest extends KernelTestCase
{
    private FormatWithPurgeTime $formatWithPurgeTime;

    protected function setUp(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')->willReturn(new DateTime('2022-02-24'));

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $this->formatWithPurgeTime = new FormatWithPurgeTime();
    }

    public function testGetFormattedValue(): void
    {
        $result = $this->formatWithPurgeTime->getFormattedValue(new DateTime());
        $this->assertEquals("2022-02-24", date_format($result, 'Y-m-d'));

        $result = $this->formatWithPurgeTime->getFormattedValue(null);
        $this->assertEquals("2022-02-24", date_format($result, 'Y-m-d'));
    }
}
