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

namespace OrangeHRM\Tests\OAuth\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ModuleDao;
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Service
 */
class ModuleServiceTest extends TestCase
{
    private ModuleService $moduleService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->moduleService = new ModuleService();
    }

    public function testGetModuleDao(): void
    {
        $moduleDao = $this->moduleService->getModuleDao();
        $this->assertTrue($moduleDao instanceof ModuleDao);
    }

    public function testGetModuleList(): void
    {
        $expectedModuleList = TestDataService::loadObjectList('Module', Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/ModuleDao.yml', 'Module');
        $moduleDao = $this->getMockBuilder(ModuleDao::class)->getMock();
        $moduleDao->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue($expectedModuleList));

        $this->moduleService->setModuleDao($moduleDao);
        $returnedModuleList = $this->moduleService->getModuleList();
        $this->assertEquals($returnedModuleList, $expectedModuleList);
    }

    public function testUpdateModuleStatus(): void
    {
        $expectedModuleList = TestDataService::loadObjectList('Module', Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/ModuleDao.yml', 'Module');
        $moduleUpdateArray = ['admin' => true, 'pim' => false, 'leave' => true, 'time' => true, 'recruitment' => true, 'performance' => false, 'maintenance' => true];
        $moduleDao = $this->getMockBuilder(ModuleDao::class)->getMock();
        $moduleDao->expects($this->once())
            ->method('updateModuleStatus')
            ->with($moduleUpdateArray)
            ->will($this->returnValue($expectedModuleList));

        $this->moduleService->setModuleDao($moduleDao);
        $returnedModuleList = $this->moduleService->updateModuleStatus($moduleUpdateArray);
        $this->assertEquals($returnedModuleList, $expectedModuleList);
    }
}
