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

namespace OrangeHRM\Core\Tests\Authorization\Service;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Service\UserRoleManagerService;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * Description of UserRoleManagerFactoryTest
 *
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
        $this->service = new UserRoleManagerService();
        $this->createKernel();
    }

    /**
     * Test the getConfigDao() and setConfigDao() method
     */
    public function testGetUserRoleManagerClassName(): void
    {
        $configDao = $this->getMockBuilder(ConfigDao::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $configDao->expects($this->once())
            ->method('getValue')
            ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
            ->will($this->returnValue('TestUserRoleManager'));

        $this->service->setConfigDao($configDao);
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

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getLoggedInUserId'])
            ->getMock();
        $authenticationService->expects($this->once())
            ->method('getLoggedInUserId')
            ->will($this->returnValue(211));

        $systemUser = new User();
        $systemUser->setId(211);

        $systemUserService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();
        $systemUserService->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($systemUser));

        $this->service->setConfigDao($configDao);
        $this->service->setAuthenticationService($authenticationService);
        $this->service->setUserService($systemUserService);

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

        $this->service->setConfigDao($configDao);

        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("Should throw exception if user role manager is invalid");
        } catch (ServiceException $e) {
            $this->assertEquals(
                'User Role Manager class OrangeHRM\Core\Tests\Authorization\Service\InvalidUserRoleManager is not a subclass of OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager',
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

        $this->service->setConfigDao($configDao);

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


        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getLoggedInUserId'])
            ->getMock();
        $authenticationService->expects($this->once())
            ->method('getLoggedInUserId')
            ->will($this->returnValue(null));


        $systemUserService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();
        $systemUserService->expects($this->never())
            ->method('getSystemUser')
            ->with(100)
            ->will($this->returnValue(null));

        $this->service->setConfigDao($configDao);
        $this->service->setAuthenticationService($authenticationService);
        $this->service->setUserService($systemUserService);

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
        $entityType,
        $operation = null,
        $returnType = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requestedPermissions = []
    ) {
    }

    public function getAccessibleModules()
    {
    }

    public function getAccessibleMenuItemDetails(): array
    {
        return [];
    }

    public function isModuleAccessible($module)
    {
    }

    public function isScreenAccessible($module, $screen, $field)
    {
    }

    public function isFieldAccessible($module, $screen, $field)
    {
    }

    protected function getUserRoles(User $user): array
    {
        return [];
    }

    public function getScreenPermissions(string $module, string $screen): ResourcePermission
    {
        return new ResourcePermission(true, true, true, true);
    }

    public function areEntitiesAccessible(
        $entityType,
        $entityIds,
        $operation = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    ) {
    }

    public function isEntityAccessible(
        $entityType,
        $entityId,
        $operation = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    ) {
    }

    public function getAccessibleEntityIds(
        $entityType,
        $operation = null,
        $returnType = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    ) {
    }

    protected function getAllowedActions(
        string $workflow,
        string $state,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ):array {
        return [];
    }

    protected function isActionAllowed(
        $workFlowId,
        $state,
        $action,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $entities = []
    ) :bool{
        return false;
    }

    public function getActionableStates(
        string $workflow,
        array $actions,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ):array {
        return [];
    }

    public function getAccessibleEntityProperties(
        $entityType,
        $properties = [],
        $orderField = null,
        $orderBy = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    ) {
    }

    public function getEmployeesWithRole($roleName, $entities = [])
    {
    }

    public function getHomePage(): ?string
    {
    }

    public function getModuleDefaultPage($module)
    {
    }
}
