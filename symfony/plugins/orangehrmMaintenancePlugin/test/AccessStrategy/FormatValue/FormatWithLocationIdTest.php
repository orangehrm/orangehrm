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

namespace OrangeHRM\Tests\Maintenance\AccessStrategy\FormatValue;

use OrangeHRM\Config\Config;
use OrangeHRM\Maintenance\AccessStrategy\FormatValue\FormatWithLocationId;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class FormatWithLocationIdTest extends KernelTestCase
{
    private string $fixture;
    private FormatWithLocationId $formatWithLocationId;

    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
        $this->formatWithLocationId = new FormatWithLocationId();
    }

    public function testGetFormattedValue(): void
    {
        $this->assertEquals('location 1', $this->formatWithLocationId->getFormattedValue(1));
        $this->assertEquals(null, $this->formatWithLocationId->getFormattedValue(5));
    }
}
