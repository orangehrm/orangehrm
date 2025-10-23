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
    private UserDao $userDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->userDao = new UserDao();
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

        $this->userDao->saveSystemUser($systemUser);

        $saveUser = Doctrine::getEntityManager()->getRepository(User::class)->find($systemUser->getId());
        $this->assertEquals($saveUser->getUserName(), 'orangehrm');
        $this->assertEquals($saveUser->getUserPassword(), 'orangehrm');
    }

    public function testIsExistingSystemUserForNonExistingUser(): void
    {
        $result = $this->userDao->isExistingSystemUser(new UserCredential('google'));
        $this->assertNull($result);
    }

    public function testIsExistingSystemUserForExistingUser(): void
    {
        $result = $this->userDao->isExistingSystemUser(new UserCredential('Samantha'));
        $this->assertInstanceOf(User::class, $result);
    }

    public function testIsExistingSystemUserForDeletedUser(): void
    {
        $result = $this->userDao->isExistingSystemUser(new UserCredential('Chaturanga'));
        $this->assertNull($result);
    }

    public function testIsExistingSystemUserForEmptyPasswordUser(): void
    {
        $result = $this->userDao->isExistingSystemUser(new UserCredential('Morgan'));
        $this->assertNull($result);
    }

    public function testGetSystemUser(): void
    {
        $result = $this->userDao->getSystemUser(1);

        $this->assertEquals($result->getUserName(), 'samantha');
        $this->assertEquals($result->getUserPassword(), 'samantha');
    }

    public function testGetSystemUserForNonExistingId(): void
    {
        $result = $this->userDao->getSystemUser(100);
        $this->assertNull($result);
    }

    public function testGetAssignableUserRoles(): void
    {
        $result = $this->userDao->getAssignableUserRoles();
        $this->assertEquals($result[0]->getName(), 'Admin');
        $this->assertEquals($result[1]->getName(), 'Admin2');
        $this->assertEquals(7, count($result));
    }

    public function testGetSystemUserIdList(): void
    {
        $result = $this->userDao->getSystemUserIdList();

        $this->assertCount(4, $result);
        $this->assertEquals([1, 2, 3, 5], $result);
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

        $result = $this->userDao->getSystemUserIdList();

        $this->assertTrue(is_array($result));
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]);
    }

    public function testGetEmployeesByUserRole(): void
    {
        // default
        $employees = $this->userDao->getEmployeesByUserRole('Admin');
        $this->assertCount(2, $employees);

        // with terminated employees
        $employees = $this->userDao->getEmployeesByUserRole('Admin', false, true);
        $this->assertCount(2, $employees);

        $employees = $this->userDao->getEmployeesByUserRole('Ess', false, true);
        $this->assertCount(2, $employees);

        $employees = $this->userDao->getEmployeesByUserRole('TestAdmin', false, true);
        $this->assertCount(0, $employees);
    }

    public function testUserStatusByUserName(): void
    {
        $status = $this->userDao->isUserNameExistByUserName('samantha');
        $this->assertTrue($status);
    }
}
