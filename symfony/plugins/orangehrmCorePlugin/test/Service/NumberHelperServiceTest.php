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

use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Service
 */
class NumberHelperServiceTest extends TestCase
{
    private NumberHelperService $numberHelperService;

    protected function setUp(): void
    {
        $this->numberHelperService = new NumberHelperService();
    }

    public function testNumberFormat(): void
    {
        $this->assertEquals(1234, $this->numberHelperService->numberFormat('1234'));
        $this->assertEquals(1234, $this->numberHelperService->numberFormat('1234.33'));
        $this->assertEquals('1234.33', $this->numberHelperService->numberFormat('1234.33', 2));
        $this->assertEquals('1234.33', $this->numberHelperService->numberFormat('1234.333', 2));
        $this->assertEquals('1234.67', $this->numberHelperService->numberFormat('1234.666', 2));
        $this->assertEquals('1234.330', $this->numberHelperService->numberFormat('1234.33', 3));
        $this->assertEquals('10009.9900', $this->numberHelperService->numberFormat(10009.99, 4));
    }

    public function testNumberFormatWithGroupedThousands(): void
    {
        $this->assertEquals('1,234', $this->numberHelperService->numberFormatWithGroupedThousands('1234'));
        $this->assertEquals('1,234', $this->numberHelperService->numberFormatWithGroupedThousands('1234.33'));
        $this->assertEquals('1,234.33', $this->numberHelperService->numberFormatWithGroupedThousands('1234.33', 2));
        $this->assertEquals('1?234.3', $this->numberHelperService->numberFormatWithGroupedThousands('1234.33', 1, '?'));
        $this->assertEquals('1,234.330', $this->numberHelperService->numberFormatWithGroupedThousands('1234.33', 3));
    }
}
