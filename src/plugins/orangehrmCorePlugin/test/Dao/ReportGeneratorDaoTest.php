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

namespace Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ReportGeneratorDao;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ReportGeneratorDaoTest extends KernelTestCase
{
    /**
     * @var ReportGeneratorDao
     */
    private ReportGeneratorDao $reportGeneratorDao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/ReportGeneratorDao.yaml';
        TestDataService::populate($this->fixture);
        $this->reportGeneratorDao = new ReportGeneratorDao();
    }

    public function testGetDisplayFieldGroups(): void
    {
        $displayFieldGroups = $this->reportGeneratorDao->getDisplayFieldGroups();
        $this->assertCount(16, $displayFieldGroups);
    }

    public function testGetDisplayFields(): void
    {
        $displayFields = $this->reportGeneratorDao->getDisplayFields();
        $this->assertCount(16, $displayFields);
        $this->assertIsArray($displayFields);
        $this->assertEquals(1, $displayFields[0]['field_group_id']);
        $this->assertEquals(9, $displayFields[0]['fields'][0]['id']);
        $this->assertEquals('Employee Id', $displayFields[0]['fields'][0]['label']);
        $this->assertEquals(2, $displayFields[1]['field_group_id']);
        $this->assertEquals(20, $displayFields[1]['fields'][0]['id']);
        $this->assertEquals('Address', $displayFields[1]['fields'][0]['label']);
    }
}
