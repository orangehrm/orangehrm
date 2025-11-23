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

namespace OrangeHRM\Tests\Maintenance\AccessStrategy\FormatValue;

use DateTime;
use OrangeHRM\Maintenance\AccessStrategy\FormatValue\FormatWithDateTime;
use OrangeHRM\Tests\Util\TestCase;

class FormatWithUtcTimeTest extends TestCase
{
    private FormatWithDateTime $formatWithUtcTime;

    protected function setUp(): void
    {
        $this->formatWithUtcTime = new FormatWithDateTime();
    }

    public function testGetFormattedValue(): void
    {
        $this->assertEquals(
            "2020-10-12 00:00:00",
            $this->formatWithUtcTime->getFormattedValue(new DateTime('2020-10-12'))
        );
        $this->assertEquals(
            null,
            $this->formatWithUtcTime->getFormattedValue('2020-10-12')
        );
    }
}
