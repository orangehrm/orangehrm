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

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ReportGeneratorDao;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Dao
 */
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
        $displayFieldGroups = $this->reportGeneratorDao->getAllDisplayFieldGroups();
        $this->assertCount(16, $displayFieldGroups);
    }

    public function testGetAllDisplayFields(): void
    {
        $displayFields = $this->reportGeneratorDao->getAllDisplayFields();
        $this->assertCount(105, $displayFields);
    }

    /**
     * @return void
     */
    public function testGetAllFilterFields(): void
    {
        $filterFields = $this->reportGeneratorDao->getAllFilterFields();
        $this->assertCount(13, $filterFields);
    }
}
