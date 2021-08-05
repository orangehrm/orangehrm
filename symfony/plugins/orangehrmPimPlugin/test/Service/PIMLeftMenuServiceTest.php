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

namespace OrangeHRM\Tests\Pim\Service;

use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Pim\Service\PIMLeftMenuService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Service
 */
class PIMLeftMenuServiceTest extends TestCase
{
    private function getAllMenuItems(bool $isTaxMenuEnabled = false): array
    {
        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['isTaxMenuEnabled'])
            ->getMock();
        $leftMenuService->expects($this->once())
            ->method('isTaxMenuEnabled')
            ->will($this->returnValue($isTaxMenuEnabled));

        return $this->invokePrivateMethodOnMock(
            PIMLeftMenuService::class,
            $leftMenuService,
            'getAvailableActions'
        );
    }

    public function testGetMenuItemsAllMenusAccessible(): void
    {
        $empNumber = 12;
        $self = false;
        $permission = new ResourcePermission(true, true, true, true);

        $allMenuItems = $this->getAllMenuItems();
        $cache = [$empNumber => $allMenuItems];

        $getAttributeMap = [
            [PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, true],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
        ];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getAttribute')
            ->will($this->returnValueMap($getAttributeMap));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $authUser->expects($this->exactly(0))
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $mockUserRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $mockUserRoleManager->expects($this->any())
            ->method('getDataGroupPermissions')
            ->will($this->returnValue($permission));

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['showPimTaxExemptions'])
            ->getMock();
        $configService->expects($this->exactly(0))
            ->method('showPimTaxExemptions')
            ->will($this->returnValue(true));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getUserRoleManager', 'getAuthUser', 'getConfigService', 'isTaxMenuEnabled'])
            ->getMock();
        $leftMenuService->expects($this->exactly(10))
            ->method('getUserRoleManager')
            ->will($this->returnValue($mockUserRoleManager));
        $leftMenuService->expects($this->exactly(2))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));
        $leftMenuService->expects($this->exactly(0))
            ->method('getConfigService')
            ->will($this->returnValue($configService));
        $leftMenuService->expects($this->once())
            ->method('isTaxMenuEnabled')
            ->will($this->returnValue(false));

        $menu = $leftMenuService->getMenuItems($empNumber, $self);


        $this->assertEquals(array_keys($allMenuItems), array_keys($menu));
    }

    public function testGetMenuItemsOnlyDependentsAccessible(): void
    {
        $empNumber = 12;
        $self = false;

        $allMenuItems = $this->getAllMenuItems();
        $expected['viewDependents'] = $allMenuItems['viewDependents'];

        $cache = [$empNumber => $expected];

        $getAttributeMap = [
            [PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, false, true],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
        ];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(3))
            ->method('getAttribute')
            ->will($this->returnValueMap($getAttributeMap));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $authUser->expects($this->once())
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $dataGroupClosure = function ($dataGroups, $rolesToExclude, $rolesToInclude, $self, $entities) {
            if (in_array('dependents', $dataGroups)) {
                $permission = new ResourcePermission(true, true, true, true);
            } else {
                $permission = new ResourcePermission(false, false, false, false);
            }

            return $permission;
        };
        $mockUserRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $mockUserRoleManager->expects($this->any())
            ->method('getDataGroupPermissions')
            ->will($this->returnCallback($dataGroupClosure));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getUserRoleManager', 'getAuthUser'])
            ->getMock();
        $leftMenuService->expects($this->exactly(12))
            ->method('getUserRoleManager')
            ->will($this->returnValue($mockUserRoleManager));
        $leftMenuService->expects($this->exactly(3))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));

        $menu = $leftMenuService->getMenuItems($empNumber, $self);

        $this->assertEquals(1, count($menu));
        $this->assertTrue(isset($menu['viewDependents']));
    }

    public function testClearCachedMenuOneEmployee(): void
    {
        $empNumber = 42;

        $allMenuItems = $this->getAllMenuItems();
        $cache = [
            $empNumber => $allMenuItems,
            34 => $allMenuItems,
            55 => $allMenuItems
        ];

        $clearedCache = $cache;
        unset($clearedCache[$empNumber]);
        $this->assertEquals(count($clearedCache) + 1, count($cache));

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(1))
            ->method('getAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [])
            ->will($this->returnValue($cache));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $clearedCache);
        $authUser->expects($this->exactly(0))
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getAuthUser'])
            ->getMock();
        $leftMenuService->expects($this->exactly(1))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));

        $leftMenuService->clearCachedMenu($empNumber);
    }

    public function testClearCachedMenuOneEmployeeNotCached(): void
    {
        $empNumber = 42;

        $allMenuItems = $this->getAllMenuItems();
        $cache = [34 => $allMenuItems, 55 => $allMenuItems];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(1))
            ->method('getAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [])
            ->will($this->returnValue($cache));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $authUser->expects($this->exactly(0))
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getAuthUser'])
            ->getMock();
        $leftMenuService->expects($this->exactly(1))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));

        $leftMenuService->clearCachedMenu($empNumber);
    }

    public function testClearCachedMenuAll(): void
    {
        $allMenuItems = $this->getAllMenuItems();
        $cache = [34 => $allMenuItems, 55 => $allMenuItems, 101 => $allMenuItems];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(1))
            ->method('getAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [])
            ->will($this->returnValue($cache));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, []);
        $authUser->expects($this->exactly(0))
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getAuthUser'])
            ->getMock();
        $leftMenuService->expects($this->exactly(1))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));

        $leftMenuService->clearCachedMenu();
    }

    public function testIsPimAccessibleTrue(): void
    {
        $permission = new ResourcePermission(true, true, true, true);

        $allMenuItems = $this->getAllMenuItems(true);
        $cache = ['default' => $allMenuItems];

        $getAttributeMap = [
            [PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, false, true],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
        ];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(3))
            ->method('getAttribute')
            ->will($this->returnValueMap($getAttributeMap));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $authUser->expects($this->once())
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $mockUserRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $mockUserRoleManager->expects($this->any())
            ->method('getDataGroupPermissions')
            ->will($this->returnValue($permission));

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['showPimTaxExemptions'])
            ->getMock();
        $configService->expects($this->exactly(0))
            ->method('showPimTaxExemptions')
            ->will($this->returnValue(true));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getUserRoleManager', 'getAuthUser', 'getConfigService'])
            ->getMock();
        $leftMenuService->expects($this->exactly(11))
            ->method('getUserRoleManager')
            ->will($this->returnValue($mockUserRoleManager));
        $leftMenuService->expects($this->exactly(3))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));
        $leftMenuService->expects($this->exactly(0))
            ->method('getConfigService')
            ->will($this->returnValue($configService));

        $accessible = $leftMenuService->isPimAccessible(null, false);
        $this->assertTrue($accessible);
    }

    public function testIsPimAccessibleFalse(): void
    {
        $permission = new ResourcePermission(false, false, false, false);

        $cache = ['default' => []];

        $getAttributeMap = [
            [PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, false, true],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
            [PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, [], []],
        ];

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute', 'setAttribute', 'hasAttribute'])
            ->getMock();
        $authUser->expects($this->exactly(3))
            ->method('getAttribute')
            ->will($this->returnValueMap($getAttributeMap));
        $authUser->expects($this->once())
            ->method('setAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $authUser->expects($this->once())
            ->method('hasAttribute')
            ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
            ->will($this->returnValue(true));

        $mockUserRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataGroupPermissions'])
            ->getMock();
        $mockUserRoleManager->expects($this->any())
            ->method('getDataGroupPermissions')
            ->will($this->returnValue($permission));

        $leftMenuService = $this->getMockBuilder(PIMLeftMenuService::class)
            ->onlyMethods(['getUserRoleManager', 'getAuthUser'])
            ->getMock();
        $leftMenuService->expects($this->exactly(12))
            ->method('getUserRoleManager')
            ->will($this->returnValue($mockUserRoleManager));
        $leftMenuService->expects($this->exactly(3))
            ->method('getAuthUser')
            ->will($this->returnValue($authUser));


        $accessible = $leftMenuService->isPimAccessible(null, false);
        $this->assertTrue(!$accessible);
    }
}
