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

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class UserDaoTest extends TestCase
{
    private $systemUserDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->systemUserDao = new UserDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/SystemUser.yml';
        TestDataService::truncateSpecificTables(['User']);
        TestDataService::populate($this->fixture);
    }

    /**
     * @param int $id
     * @return UserRole
     */
    private function getUserRole($id = 1): UserRole
    {
        return Doctrine::getEntityManager()->getRepository(UserRole::class)->find($id);
    }

    /**
     * @param int $empNumber
     * @return Employee
     */
    private function getEmployee($empNumber = 1): Employee
    {
        return Doctrine::getEntityManager()->getRepository(Employee::class)->find($empNumber);
    }

    public function testSaveSystemUser(): void
    {
        $systemUser = new User();
        $systemUser->setUserRole($this->getUserRole());
        $systemUser->setEmployee($this->getEmployee(2));
        $systemUser->setUserName('orangehrm');
        $systemUser->setUserPassword('orangehrm');

        $this->systemUserDao->saveSystemUser($systemUser);

        $saveUser = Doctrine::getEntityManager()->getRepository(User::class)->find($systemUser->getId());
        $this->assertEquals($saveUser->getUserName(), 'orangehrm');
        $this->assertEquals($saveUser->getUserPassword(), 'orangehrm');
    }

    public function testIsExistingSystemUserForNonEsistingUser(): void
    {
        $result = $this->systemUserDao->isExistingSystemUser(new UserCredential('google'));
        $this->assertNull($result);
    }

    public function testIsExistingSystemUserForEsistingUser(): void
    {
        $result = $this->systemUserDao->isExistingSystemUser(new UserCredential('Samantha'));
        $this->assertTrue($result instanceof User);
    }

    public function testGetSystemUser(): void
    {
        $result = $this->systemUserDao->getSystemUser(1);

        $this->assertEquals($result->getUserName(), 'samantha');
        $this->assertEquals($result->getUserPassword(), 'samantha');
    }

    public function testGetSystemUserForNonExistingId(): void
    {
        $result = $this->systemUserDao->getSystemUser(100);
        $this->assertNull($result);
    }

    public function testGetSystemUsers(): void
    {
        $result = $this->systemUserDao->getSystemUsers();
        $this->assertEquals(3, count($result));
    }

    public function testDeleteSystemUsers(): void
    {
        $this->systemUserDao->deleteSystemUsers([1, 2, 3]);
        $result = $this->systemUserDao->getSystemUsers();
        $this->assertEquals(0, count($result));
    }

    public function testGetAssignableUserRoles(): void
    {
        $result = $this->systemUserDao->getAssignableUserRoles();
        $this->assertEquals($result[0]->getName(), 'Admin');
        $this->assertEquals($result[1]->getName(), 'Admin2');
        $this->assertEquals(7, count($result));
    }

    public function testGetAdminUserCount(): void
    {
        $this->assertEquals(1, $this->systemUserDao->getAdminUserCount());
        $this->assertEquals(2, $this->systemUserDao->getAdminUserCount(false));
        $this->assertEquals(2, $this->systemUserDao->getAdminUserCount(true, false));
        $this->assertEquals(3, $this->systemUserDao->getAdminUserCount(false, false));
    }

    public function testUpdatePassword(): void
    {
        $this->assertEquals(1, $this->systemUserDao->updatePassword(1, 'samantha2'));

        $userObject = TestDataService::fetchObject('User', 1);

        $this->assertEquals('samantha2', $userObject->getUserPassword());
    }

    public function testGetSystemUserIdList(): void
    {
        $result = $this->systemUserDao->getSystemUserIdList();

        $this->assertEquals(3, count($result));
        $this->assertEquals([1, 2, 3], $result);
    }

    public function testGetSystemUserIdListForOneActiveUser(): void
    {
        $q = Doctrine::getEntityManager()->createQueryBuilder();
        $q->update(User::class, 'u')
            ->set('u.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('u.id', ':ids'))
            ->setParameter('ids', [2, 3]);
        $q->getQuery()->execute();

        $result = $this->systemUserDao->getSystemUserIdList();

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]);
    }

    /**
     * @covers \OrangeHRM\Admin\Dao\UserDao::getNonPredefinedUserRoles
     */
    public function testGetNonPredefinedUserRoles(): void
    {
        $userRoleNames = ['Admin2', 'TestAdmin', 'UserRole1', 'UserRole2', 'UserRole3'];

        $useRoles = $this->systemUserDao->getNonPredefinedUserRoles();
        $this->assertEquals(count($userRoleNames), count($useRoles));
        for ($i = 0; $i < count($userRoleNames); $i++) {
            $userRole = $useRoles[$i];
            $this->assertTrue($userRole instanceof UserRole);
            $this->assertEquals($userRoleNames[$i], $userRole->getName());
        }
    }

    public function testGetEmployeesByUserRole(): void
    {
        // default
        $employees = $this->systemUserDao->getEmployeesByUserRole('Admin');
        $this->assertEquals(2, count($employees));

        // with terminated employees
        $employees = $this->systemUserDao->getEmployeesByUserRole('Admin', false, true);
        $this->assertEquals(2, count($employees));

        $employees = $this->systemUserDao->getEmployeesByUserRole('Ess', false, true);
        $this->assertEquals(1, count($employees));

        $employees = $this->systemUserDao->getEmployeesByUserRole('TestAdmin', false, true);
        $this->assertEquals(0, count($employees));
    }

    public function testUserStatusByUserName(): void
    {
        $status = $this->systemUserDao->isUserNameExistByUserName('samantha');
        $this->assertTrue($status);
    }
}
