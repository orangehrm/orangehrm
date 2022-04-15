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

namespace OrangeHRM\Tests\Maintenance\AccessStrategy\downloadFormat;

use OrangeHRM\Config\Config;
use OrangeHRM\Maintenance\DownloadFormats\JsonDownloadFormat;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class DownloadFormatTest extends TestCase
{
    private string $fixture;
    protected JsonDownloadFormat $jsonDownloadFormat;

    protected function setUp(): void
    {
        $this->jsonDownloadFormat=new JsonDownloadFormat();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeDownloadFormat.yml';
        TestDataService::populate($this->fixture);
    }

    public function testDownloadFileName(): void
    {
        $fileName=$this->jsonDownloadFormat->getDownloadFileName(1);
        $this->assertEquals('Kayla T Abbey.json', $fileName);

        $fileName=$this->jsonDownloadFormat->getDownloadFileName(2);
        $this->assertEquals('Ashley£$ ST Abel (Past Employee).json', $fileName);
    }
}
