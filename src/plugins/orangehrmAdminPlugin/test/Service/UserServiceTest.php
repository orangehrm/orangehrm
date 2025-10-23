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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Admin
 * @group Service
 */
class UserServiceTest extends KernelTestCase
{
    private UserService $systemUserService;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->systemUserService = new UserService();
    }

    /**
     * @return UserRole
     */
    private function getUserRole(): UserRole
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');
        $userRole->setDisplayName('Admin');
        return $userRole;
    }

    public function testSaveSystemUserNoPasswordChange(): void
    {
        $password = 'y28#$!!';

        $user = new User();
        $user->setId(1);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->setUserPassword($password);

        $dao = $this->getMockBuilder(UserDao::class)->getMock();

        $dao->expects($this->once())
            ->method('saveSystemUser')
            ->will($this->returnArgument(0));

        $this->systemUserService->setUserDao($dao);
        $result = $this->systemUserService->saveSystemUser($user);

        $this->assertEquals($user, $result);
    }

    public function testSaveSystemUserWithPasswordChange(): void
    {
        if (Config::PRODUCT_MODE === Config::MODE_DEMO) {
            $this->markTestSkipped();
        }
        $password = 'y28#$!!';

        $user = new User();
        $user->setId(1);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->getDecorator()->setNonHashedPassword($password);

        $dao = $this->getMockBuilder(UserDao::class)->getMock();

        $dao->expects($this->once())
            ->method('saveSystemUser')
            ->will($this->returnArgument(0));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->saveSystemUser($user);

        // check password is hashed before saving
        $this->assertNotEquals($password, $result->getUserPassword());

        // verify correct hash
        $hashedPassword = $result->getUserPassword();
        $hasher = new PasswordHash();
        $this->assertTrue($hasher->verify($password, $hashedPassword));
    }

    public function testIsCurrentPasswordUserNotFound(): void
    {
        $userId = 5;
        $password = 'sadffas';

        $dao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();

        $dao->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue(null));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertFalse($result);
    }

    public function testIsCurrentPasswordTrue(): void
    {
        $userId = 5;
        $password = 'y28#$!!';

        $hasher = new PasswordHash();
        $hashedPassword = $hasher->hash($password);

        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);


        $dao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();

        $dao->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertTrue($result);
    }

    public function testIsCurrentPasswordWithNullPassword(): void
    {
        $user = new User();
        $user->setId(2);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->setUserPassword(null);

        $dao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();

        $dao->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->isCurrentPassword(2, '');
        $this->assertFalse($result);
    }

    public function testIsCurrentPasswordOldHash(): void
    {
        $userId = 5;
        $password = 'y28#$!!';

        $hashedPassword = md5($password);

        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);


        $dao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();

        $dao->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertTrue($result);
    }

    public function testIsCurrentPasswordFalse(): void
    {
        $userId = 5;
        $password = 'y28#$!!';

        $hashedPassword = 'asdfasfda';

        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);


        $dao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getSystemUser'])
            ->getMock();

        $dao->expects($this->once())
            ->method('getSystemUser')
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($dao);

        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertFalse($result);
    }

    public function testGetCredentialsUserNotFound(): void
    {
        $userName = 'adminUser1';
        $password = 'isd#@!';

        $credentials = new UserCredential($userName, $password);
        $mockDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['isExistingSystemUser'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('isExistingSystemUser')
            ->with($credentials)
            ->will($this->returnValue(null));
        $this->systemUserService->setUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($credentials);

        $this->assertNull($result);
    }

    public function testGetCredentialsValidPassword(): void
    {
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';

        $hashedPassword = 'asdfasfda';

        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);

        $credentials = new UserCredential($userName, $password);
        $mockHasher = $this->getMockBuilder(PasswordHash::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verify'])
            ->getMock();
        $mockHasher->expects($this->once())
            ->method('verify')
            ->with($password, $hashedPassword)
            ->will($this->returnValue(true));

        $mockDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['isExistingSystemUser'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('isExistingSystemUser')
            ->with($credentials)
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($mockDao);
        $this->systemUserService->setPasswordHasher($mockHasher);
        $result = $this->systemUserService->getCredentials($credentials);

        $this->assertTrue($result instanceof User);
        $this->assertEquals($user, $result);
    }

    public function testGetCredentialsOldHash(): void
    {
        if (Config::PRODUCT_MODE === Config::MODE_DEMO) {
            $this->markTestSkipped();
        }
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';

        $hashedPassword = md5($password);

        $credentials = new UserCredential($userName, $password);
        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);


        $mockDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['isExistingSystemUser', 'saveSystemUser'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('isExistingSystemUser')
            ->with($credentials)
            ->will($this->returnValue($user));

        $mockDao->expects($this->once())
            ->method('saveSystemUser')
            ->will($this->returnArgument(0));

        $this->systemUserService->setUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($credentials);

        $this->assertTrue($result instanceof User);

        // Check password correctly hashed
        $hasher = new PasswordHash();
        $this->assertTrue($hasher->verify($password, $result->getUserPassword()));
    }

    public function testGetCredentialsInvalidPassword(): void
    {
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';

        $hashedPassword = 'asdfasfe##';

        $credentials = new UserCredential($userName, $password);
        $user = new User();
        $user->setId($userId);
        $user->setUserRole($this->getUserRole());
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);


        $mockDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['isExistingSystemUser'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('isExistingSystemUser')
            ->with($credentials)
            ->will($this->returnValue($user));

        $this->systemUserService->setUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($credentials);

        $this->assertNull($result);
    }

    public function testGetUndeletableUserIds(): void
    {
        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $user = new User();
        $user->setId(1);
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user, null);

        $service = $this->getMockBuilder(UserService::class)->onlyMethods(['getUserRoleManager'])->getMock();
        $service->expects($this->exactly(2))
            ->method('getUserRoleManager')
            ->willReturn($userRoleManager);

        $this->assertEquals([1], $service->getUndeletableUserIds());
        $this->assertEmpty($service->getUndeletableUserIds());
    }
}
