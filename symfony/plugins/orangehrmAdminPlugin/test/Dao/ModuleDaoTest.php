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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ModuleDao;
use OrangeHRM\Entity\Module;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class ModuleDaoTest extends TestCase
{
    private ModuleDao $moduleDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->moduleDao = new ModuleDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/ModuleDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetModuleList(): void
    {
        $moduleList = $this->moduleDao->getModuleList();
        $this->assertEquals(7, count($moduleList));
        $this->assertTrue($moduleList[0] instanceof Module);
        $this->assertEquals('admin', $moduleList[0]->getName());
        $this->assertEquals(1, $moduleList[0]->getStatus());
    }

    public function testUpdateModuleStatus(): void
    {
        $moduleUpdateArray = ['admin' => true, 'pim' => false];
        $this->moduleDao->updateModuleStatus($moduleUpdateArray);
        $updatedAdminModule = TestDataService::fetchObject('Module', 1);
        $this->assertTrue($updatedAdminModule->getStatus());
        $updatedAdminModule = TestDataService::fetchObject('Module', 2);
        $this->assertFalse($updatedAdminModule->getStatus());
    }

    public function testUpdateModuleStatusForReturnedValues(): void
    {
        $moduleUpdateArray = ['admin' => true, 'pim' => false];
        $returedObjects = $this->moduleDao->updateModuleStatus($moduleUpdateArray);
        $this->assertEquals(7, count($returedObjects));
        $this->assertTrue($returedObjects[0]->getStatus());
        $this->assertFalse($returedObjects[1]->getStatus());
    }

    public function testUpdateModuleStatusWithModulesDoesNotExists(): void
    {
        $moduleUpdateArray = ['admin' => true, 'pim' => false, 'test' => true];
        $returedObjects = $this->moduleDao->updateModuleStatus($moduleUpdateArray);
        $this->assertEquals(7, count($returedObjects));
        foreach ($returedObjects as $returedObject) {
            $this->assertTrue($returedObject->getName() != 'test');
        }
    }

    public function testDisabledModuleList(): void
    {
        $disabledModules = $this->moduleDao->getDisabledModuleList();
        $this->assertEquals(1, count($disabledModules));
        foreach ($disabledModules as $disabledModule) {
            $this->assertTrue($disabledModule['name'] === 'maintenance');
        }
    }
}
