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

namespace OrangeHRM\Maintenance\test\accessStrategy\FormatValue;

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Maintenance\AccessStrategy\FormatValue\FormatWithCountryCode;
use OrangeHRM\Tests\Util\KernelTestCase;

class FormatWithCountryCodeTest extends KernelTestCase
{
    private FormatWithCountryCode $formatWithCountryCode;

    protected function setUp(): void
    {
        $this->createKernelWithMockServices([Services::COUNTRY_SERVICE=>new CountryService()]);
        $this->formatWithCountryCode = new FormatWithCountryCode();
    }

    public function testGetFormattedValue()
    {
        $this->assertEquals(null, $this->formatWithCountryCode->getFormattedValue('UK'));
    }
}
