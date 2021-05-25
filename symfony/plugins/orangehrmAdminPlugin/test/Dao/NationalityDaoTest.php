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

namespace OrangeHRM\Admin\Tests\Dao;

use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class NationalityDaoTest extends TestCase
{
    private NationalityDao $nationalityDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->nationalityDao = new NationalityDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/NationalityDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetNationalityList(): void
    {
        $result = $this->nationalityDao->getNationalityList();
        $this->assertEquals(count($result), 3);
    }

    public function testGetNationalityById(): void
    {
        $result = $this->nationalityDao->getNationalityById(1);
        $this->assertEquals($result->getName(), 'nationality 1');
    }

    public function testDeleteNationalities(): void
    {
        $result = $this->nationalityDao->deleteNationalities([1, 2, 3]);
        $this->assertEquals($result, 3);
    }
}
