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

namespace OrangeHRM\Tests\Core\Authorization\Service;

use OrangeHRM\Core\Authorization\Dao\ScreenDao;
use OrangeHRM\Core\Authorization\Dao\ScreenPermissionDao;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Service\ScreenPermissionService;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\Screen;
use OrangeHRM\Entity\ScreenPermission;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Service
 */
class ScreenPermissionServiceTest extends TestCase
{
    /**
     * @var ScreenPermissionService
     */
    private ScreenPermissionService $service;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->service = new ScreenPermissionService();
    }

    /**
     * Test case for when no permissions are defined for given user role(s).
     * Behavior is to allow access if the screen is not defined, unless prohibited through a rule in the database.
     * This allows to progressively update the rules in code.
     */
    public function testGetScreenPermissionsNoneWithNoScreen(): void
    {
        $module = 'xim';
        $action = 'doThis';
        $roles = [];

        $permissionDao = $this->getMockBuilder(ScreenPermissionDao::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();

        $permissionDao->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue([]));

        $this->service->setScreenPermissionDao($permissionDao);

        $screenDao = $this->getMockBuilder(ScreenDao::class)
            ->onlyMethods(['getScreen'])
            ->getMock();
        $screenDao->expects($this->once())
            ->method('getScreen')
            ->with($module, $action)
            ->will($this->returnValue(null));

        $this->service->setScreenDao($screenDao);

        $permissions = $this->service->getScreenPermissions($module, $action, $roles);

        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, true, true);
    }

    public function testGetScreenPermissionsNoneWithScreenDefined(): void
    {
        $module = 'xim';
        $action = 'doThis';
        $roles = [];

        $mockDao = $this->getMockBuilder(ScreenPermissionDao::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();

        $mockDao->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue([]));

        $this->service->setScreenPermissionDao($mockDao);

        $screen = new Screen();
        $screen->setName('abc');

        $screenDao = $this->getMockBuilder(ScreenDao::class)
            ->onlyMethods(['getScreen'])
            ->getMock();
        $screenDao->expects($this->once())
            ->method('getScreen')
            ->with($module, $action)
            ->will($this->returnValue($screen));

        $this->service->setScreenDao($screenDao);

        $permissions = $this->service->getScreenPermissions($module, $action, $roles);

        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, false, false, false, false);
    }

    public function testGetScreenPermissionsOne(): void
    {
        $module = 'xim';
        $action = 'doThis';
        $roles = ['Admin'];

        $userRole = new UserRole();
        $userRole->setId(1);

        $screen = new Screen();
        $screen->setId(1);

        $screenPermission1 = new ScreenPermission();
        $screenPermission1->setId(1);
        $screenPermission1->setUserRole($userRole);
        $screenPermission1->setScreen($screen);
        $screenPermission1->setCanRead(true);
        $screenPermission1->setCanCreate(false);
        $screenPermission1->setCanUpdate(false);
        $screenPermission1->setCanDelete(true);

        $screenPermission2 = new ScreenPermission();
        $screenPermission2->setId(2);
        $screenPermission2->setUserRole($userRole);
        $screenPermission2->setScreen($screen);
        $screenPermission2->setCanRead(false);
        $screenPermission2->setCanCreate(true);
        $screenPermission2->setCanUpdate(false);
        $screenPermission2->setCanDelete(true);

        $screenPermissions = [$screenPermission1, $screenPermission2];

        $mockDao = $this->getMockBuilder(ScreenPermissionDao::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue($screenPermissions));

        $this->service->setScreenPermissionDao($mockDao);

        $permissions = $this->service->getScreenPermissions($module, $action, $roles);

        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, false, true);
    }

    public function testGetScreenPermissionsTwo(): void
    {
        $module = 'xim';
        $action = 'doThis';
        $roles = ['Admin', 'ESS'];

        $userRole = new UserRole();
        $userRole->setId(1);

        $screen = new Screen();
        $screen->setId(1);

        $screenPermission1 = new ScreenPermission();
        $screenPermission1->setId(1);
        $screenPermission1->setUserRole($userRole);
        $screenPermission1->setScreen($screen);
        $screenPermission1->setCanRead(true);
        $screenPermission1->setCanCreate(false);
        $screenPermission1->setCanUpdate(false);
        $screenPermission1->setCanDelete(true);

        $screenPermission2 = new ScreenPermission();
        $screenPermission2->setId(2);
        $screenPermission2->setUserRole($userRole);
        $screenPermission2->setScreen($screen);
        $screenPermission2->setCanRead(false);
        $screenPermission2->setCanCreate(true);
        $screenPermission2->setCanUpdate(false);
        $screenPermission2->setCanDelete(true);

        $screenPermissions = [$screenPermission1, $screenPermission2];

        $mockDao = $this->getMockBuilder(ScreenPermissionDao::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue($screenPermissions));

        $this->service->setScreenPermissionDao($mockDao);

        $permissions = $this->service->getScreenPermissions($module, $action, $roles);

        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, false, true);
    }

    public function testGetScreenPermissionsMany(): void
    {
        $module = 'xim';
        $action = 'doThis';
        $roles = ['Admin', 'ESS', 'Supervisor'];

        $userRole = new UserRole();
        $userRole->setId(1);

        $screen = new Screen();
        $screen->setId(1);

        $screenPermission1 = new ScreenPermission();
        $screenPermission1->setId(1);
        $screenPermission1->setUserRole($userRole);
        $screenPermission1->setScreen($screen);
        $screenPermission1->setCanRead(false);
        $screenPermission1->setCanCreate(false);
        $screenPermission1->setCanUpdate(false);
        $screenPermission1->setCanDelete(false);

        $screenPermission2 = new ScreenPermission();
        $screenPermission2->setId(2);
        $screenPermission2->setUserRole($userRole);
        $screenPermission2->setScreen($screen);
        $screenPermission2->setCanRead(false);
        $screenPermission2->setCanCreate(true);
        $screenPermission2->setCanUpdate(false);
        $screenPermission2->setCanDelete(false);

        $screenPermission3 = new ScreenPermission();
        $screenPermission3->setId(2);
        $screenPermission3->setUserRole($userRole);
        $screenPermission3->setScreen($screen);
        $screenPermission3->setCanRead(false);
        $screenPermission3->setCanCreate(true);
        $screenPermission3->setCanUpdate(false);
        $screenPermission3->setCanDelete(true);

        $screenPermissions = [$screenPermission1, $screenPermission2, $screenPermission3];

        $mockDao = $this->getMockBuilder(ScreenPermissionDao::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue($screenPermissions));

        $this->service->setScreenPermissionDao($mockDao);

        $permissions = $this->service->getScreenPermissions($module, $action, $roles);

        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, false, true, false, true);
    }

    public function testGetScreen(): void
    {
        $module = 'xim';
        $action = 'doThis';

        $moduleObj = new Module();
        $moduleObj->setId(33);

        $expected = new Screen();
        $expected->setId(2);
        $expected->setName('test');
        $expected->setModule($moduleObj);
        $expected->setActionUrl($action);


        $screenDao = $this->getMockBuilder(ScreenDao::class)
            ->onlyMethods(['getScreen'])
            ->getMock();
        $screenDao->expects($this->once())
            ->method('getScreen')
            ->with($module, $action)
            ->will($this->returnValue($expected));

        $this->service->setScreenDao($screenDao);

        $result = $this->service->getScreen($module, $action);
        $this->assertEquals($expected, $result);
    }

    protected function verifyPermissions(
        ResourcePermission $permission,
        bool $read,
        bool $create,
        bool $update,
        bool $delete
    ): void {
        $this->assertEquals($read, $permission->canRead());
        $this->assertEquals($create, $permission->canCreate());
        $this->assertEquals($update, $permission->canUpdate());
        $this->assertEquals($delete, $permission->canDelete());
    }
}
