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

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\IDGeneratorDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Dao
 */
class IDGeneratorDaoTest extends TestCase
{
    /**
     * @var IDGeneratorDao|null
     */
    private ?IDGeneratorDao $iDGeneratorDao = null;

    protected function setUp(): void
    {
        $this->iDGeneratorDao = new IDGeneratorDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/IDGeneratorDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetCurrentID(): void
    {
        $this->assertEquals(0, $this->iDGeneratorDao->getCurrentID(Employee::class));
        $this->assertEquals(6, $this->iDGeneratorDao->getCurrentID(Module::class));
        $this->assertEquals(0, $this->iDGeneratorDao->getCurrentID(User::class));
    }

    public function testUpdateNextId(): void
    {
        $this->assertEquals(1, $this->iDGeneratorDao->updateNextId(Employee::class, 1));
        $this->assertEquals(1, $this->iDGeneratorDao->getCurrentID(Employee::class));
    }
}
