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

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Service\UserRoleManagerService;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Core
 * @group Service
 */
class UserRoleManagerServiceTest extends KernelTestCase
{
    private UserRoleManagerService $service;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->createKernel();
    }

    public function testGetUserRoleManagerClassName(): void
    {
        $configDao = $this->getMockBuilder(ConfigDao::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue('TestUserRoleManager'));

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getConfigDao'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getConfigDao')
            ->willReturn($configDao);

        $this->service = $this->getMockBuilder(UserRoleManagerService::class)
            ->onlyMethods(['getConfigService'])->getMock();
        $this->service->expects($this->once())
            ->method('getConfigService')
            ->willReturn($configService);

        $class = $this->service->getUserRoleManagerClassName();
        $this->assertEquals('TestUserRoleManager', $class);
    }

    public function testGetUserRoleManagerExistingClass(): void
    {
        $configDao = $this->getMockBuilder(ConfigDao::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue(UnitTestUserRoleManager::class));
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getConfigDao'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getConfigDao')
            ->willReturn($configDao);

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserId'])
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserId')
            ->willReturn(211);

        $systemUser = new User();
        $systemUser->setId(211);

        $systemUserService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();
        $systemUserService->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($systemUser));

        $this->service = $this->getMockBuilder(UserRoleManagerService::class)
            ->onlyMethods(['getUserService', 'getConfigService', 'getAuthUser'])->getMock();
        $this->service->expects($this->once())
            ->method('getUserService')
            ->willReturn($systemUserService);
        $this->service->expects($this->once())
            ->method('getConfigService')
            ->willReturn($configService);
        $this->service->expects($this->once())
            ->method('getAuthUser')
            ->willReturn($authUser);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $manager = $this->service->getUserRoleManager();
        $this->assertNotNull($manager);
        $this->assertTrue($manager instanceof AbstractUserRoleManager);
        $this->assertTrue($manager instanceof UnitTestUserRoleManager);
        $user = $manager->getUser();
        $this->assertEquals($systemUser, $user);
    }

    public function testGetUserRoleManagerInvalidClass(): void
    {
        $configDao = $this->getMockBuilder(ConfigDao::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue(InvalidUserRoleManager::class));
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getConfigDao'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getConfigDao')
            ->willReturn($configDao);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $this->service = $this->getMockBuilder(UserRoleManagerService::class)
            ->onlyMethods(['getConfigService'])->getMock();
        $this->service->expects($this->once())
            ->method('getConfigService')
            ->willReturn($configService);

        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("Should throw exception if user role manager is invalid");
        } catch (ServiceException $e) {
            $this->assertEquals(
                'User Role Manager class OrangeHRM\Tests\Core\Authorization\Service\InvalidUserRoleManager is not a subclass of OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager',
                $e->getMessage()
            );
        }
    }

    public function testGetUserRoleManagerNonExistingClass(): void
    {
        $className = 'xasdfasfdskfdaManager';
        $configDao = $this->getMockBuilder(ConfigDao::class)->onlyMethods(['getValue'])->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue($className));
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getConfigDao'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getConfigDao')
            ->willReturn($configDao);

        $this->service = $this->getMockBuilder(UserRoleManagerService::class)
            ->onlyMethods(['getConfigService'])->getMock();
        $this->service->expects($this->once())
            ->method('getConfigService')
            ->willReturn($configService);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("Should throw exception if user role manager class does not exist.");
        } catch (ServiceException $e) {
            $this->assertEquals("User Role Manager class $className not found.", $e->getMessage());
        }
    }

    public function testGetUserRoleManagerNoLoggedInUser(): void
    {
        $configDao = $this->getMockBuilder(ConfigDao::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue(UnitTestUserRoleManager::class));
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getConfigDao'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getConfigDao')
            ->willReturn($configDao);

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserId'])
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserId')
            ->willReturn(null);

        $systemUserService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();
        $systemUserService->expects($this->never())
            ->method('getSystemUser')
            ->with(100)
            ->will($this->returnValue(null));

        $this->service = $this->getMockBuilder(UserRoleManagerService::class)
            ->onlyMethods(['getConfigService', 'getAuthUser'])->getMock();
        $this->service->expects($this->once())
            ->method('getConfigService')
            ->willReturn($configService);
        $this->service->expects($this->once())
            ->method('getAuthUser')
            ->willReturn($authUser);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("User id should be null when no logged in user");
        } catch (ServiceException $e) {
            $this->assertEquals("No logged in user found.", $e->getMessage());
        }
    }
}

class InvalidUserRoleManager
{
}

class UnitTestUserRoleManager extends AbstractUserRoleManager
{
    public function getAccessibleEntities(
        string $entityType,
        ?string $operation = null,
        ?string $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requestedPermissions = []
    ): array {
        return [];
    }

    public function getAccessibleEntityIds(
        string $entityType,
        ?string $operation = null,
        $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array {
        return [];
    }

    public function isEntityAccessible(
        string $entityType,
        $entityId,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool {
        return false;
    }

    public function areEntitiesAccessible(
        string $entityType,
        array $entityIds,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool {
        return false;
    }

    public function getAccessibleEntityProperties(
        string $entityType,
        array $properties = [],
        ?string $orderField = null,
        ?string $orderBy = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array {
        return [];
    }

    public function getAccessibleModules(): array
    {
        return [];
    }

    public function isModuleAccessible(string $module): bool
    {
        return false;
    }

    public function isScreenAccessible(string $module, string $screen, string $field): bool
    {
        return false;
    }

    public function getScreenPermissions(string $module, string $screen): ResourcePermission
    {
        return new ResourcePermission(false, false, false, false);
    }

    public function getApiPermissions(string $apiClassName): ResourcePermission
    {
        return new ResourcePermission(false, false, false, false);
    }

    public function isFieldAccessible(string $module, string $screen, string $field): bool
    {
        return false;
    }

    public function getEmployeesWithRole(string $roleName, array $entities = []): array
    {
        return [];
    }

    protected function computeUserRoles(User $user): array
    {
        return [];
    }

    public function isActionAllowed(
        string $workFlowId,
        string $state,
        string $action,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): bool {
        return false;
    }

    public function getAllowedActions(
        string $workflow,
        string $state,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        return [];
    }

    public function getActionableStates(
        string $workflow,
        array $actions,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        return [];
    }

    public function getModuleDefaultPage(string $module): ?string
    {
        return null;
    }

    public function getHomePage(): ?string
    {
        return null;
    }

    public function getDataGroupPermissionCollection(
        DataGroupPermissionFilterParams $dataGroupPermissionFilterParams = null
    ): DataGroupPermissionCollection {
        return new DataGroupPermissionCollection();
    }

    public function getDataGroupPermissions(
        $dataGroupName,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        bool $selfPermission = false,
        array $entities = []
    ): ResourcePermission {
        return new ResourcePermission(false, false, false, false);
    }
}
