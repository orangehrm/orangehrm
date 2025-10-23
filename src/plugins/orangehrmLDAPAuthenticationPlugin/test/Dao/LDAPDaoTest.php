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

namespace OrangeHRM\Tests\LDAP\Dao;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\LDAP\Dao\LDAPDao;
use OrangeHRM\LDAP\Dto\LDAPAuthProvider;
use OrangeHRM\LDAP\Dto\LDAPEmployeeSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group LDAP
 * @group Dao
 */
class LDAPDaoTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmLDAPAuthenticationPlugin/test/fixtures/LDAPDao.yaml';
        TestDataService::populate($fixture);
    }

    public function testGetEmpNumbersHaveManyUsers(): void
    {
        $ldapDao = new LDAPDao();
        $this->assertEquals([2, 6], $ldapDao->getEmpNumbersWhoHaveManyUsers());
    }

    public function testGetNonLocalUserByUserName(): void
    {
        $ldapDao = new LDAPDao();
        // Non existing user
        $this->assertNull($ldapDao->getNonLocalUserByUserName('Non Existing'));

        // Deleted user
        $this->assertNull($ldapDao->getNonLocalUserByUserName('Alice'));

        // Disable user, no password (non local)
        $user = $ldapDao->getNonLocalUserByUserName('Jane');
        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->getStatus());

        // Local user
        $this->assertNull($ldapDao->getNonLocalUserByUserName('Jasmine'));

        // LDAP, Local auth user
        $this->assertNull($ldapDao->getNonLocalUserByUserName('Peter'));

        // Non local user
        $user = $ldapDao->getNonLocalUserByUserName('Duval');
        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetUserByUsername(): void
    {
        $ldapDao = new LDAPDao();
        // Non existing user
        $this->assertNull($ldapDao->getUserByUserName('Non Existing'));

        // Deleted user
        $this->assertNull($ldapDao->getUserByUserName('Alice'));

        // Disable user
        $user = $ldapDao->getUserByUserName('Jane');
        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->getStatus());

        // Local auth user
        $user = $ldapDao->getUserByUserName('Adalwin');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('0001', $user->getEmployee()->getEmployeeId());
        $this->assertCount(0, $user->getAuthProviders());

        // Non LDAP auth user
        $user = $ldapDao->getUserByUserName('Duval');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('0003', $user->getEmployee()->getEmployeeId());
        $this->assertCount(1, $user->getAuthProviders());

        // LDAP, Local, Other auth user
        $user = $ldapDao->getUserByUserName('Linda');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('0002', $user->getEmployee()->getEmployeeId());
        $this->assertCount(2, $user->getAuthProviders());

        // LDAP, Local auth user
        $user = $ldapDao->getUserByUserName('Peter');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('0010', $user->getEmployee()->getEmployeeId());
        $this->assertCount(1, $user->getAuthProviders());
        $this->assertEquals(UserAuthProvider::TYPE_LDAP, $user->getAuthProviders()[0]->getType());
        $this->assertEquals(
            'cn=Peter.Anderson,ou=finance,ou=users,dc=example,dc=org',
            $user->getAuthProviders()[0]->getLDAPUserDN()
        );
        $this->assertEquals(
            'df2567d0-bca1-103c-98f9-f5289009f541',
            $user->getAuthProviders()[0]->getLDAPUserUniqueId()
        );
        $this->assertEquals('b698c9bc564c09b72faf894827d1b141', $user->getAuthProviders()[0]->getLDAPUserHash());
    }

    public function testGetEmployee(): void
    {
        $ldapDao = new LDAPDao();
        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        try {
            $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                'At least one parameter should define in OrangeHRM\LDAP\Dto\LDAPEmployeeSearchFilterParams',
                $e->getMessage()
            );
        }
        $ldapEmployeeSearchFilterParams->setEmployeeId('0001');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Odis', $employee->getFirstName());
        $this->assertEquals('Adalwin', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setEmpNumber(1);
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Odis', $employee->getFirstName());
        $this->assertEquals('Adalwin', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setSinNumber('890 785 234');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Kayla', $employee->getFirstName());
        $this->assertEquals('Abbey', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setDrivingLicenseNo(97204831);
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Jasmine', $employee->getFirstName());
        $this->assertEquals('Morgan', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setOtherId('86YH34567');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Garry', $employee->getFirstName());
        $this->assertEquals('White', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setWorkEmail('jadine@example.org');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Jadine', $employee->getFirstName());
        $this->assertEquals('Jackie', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setOtherEmail('david@example.com');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('David', $employee->getFirstName());
        $this->assertEquals('Morris', $employee->getLastName());

        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setSsnNumber('778-62-8144');
        $employee = $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Kevin', $employee->getFirstName());
        $this->assertEquals('Mathews', $employee->getLastName());

        $this->expectException(NonUniqueResultException::class);
        $ldapEmployeeSearchFilterParams = new LDAPEmployeeSearchFilterParams();
        $ldapEmployeeSearchFilterParams->setOtherId('78TH67845');
        $ldapDao->getEmployee($ldapEmployeeSearchFilterParams);
    }

    public function testGetAuthProviderByUserUniqueId(): void
    {
        $ldapDao = new LDAPDao();
        $authProvider = $ldapDao->getAuthProviderByUserUniqueId('df2567d0-bca1-103c-98f9-f5289009f541');
        $this->assertInstanceOf(UserAuthProvider::class, $authProvider);
        $this->assertEquals(11, $authProvider->getUser()->getId());

        // Non existing
        $authProvider = $ldapDao->getAuthProviderByUserUniqueId('invalid');
        $this->assertNull($authProvider);
    }

    public function testGetAllLDAPAuthProviders(): void
    {
        $ldapDao = new LDAPDao();
        $providers = $ldapDao->getAllLDAPAuthProviders();
        $this->assertCount(2, $providers);
        $this->assertInstanceOf(LDAPAuthProvider::class, $providers[0]);
        $this->assertInstanceOf(LDAPAuthProvider::class, $providers[1]);
        $this->assertEquals('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org', $providers[0]->getUserDN());
        $this->assertEquals(8, $providers[0]->getUserId());
        $this->assertEquals('cn=Peter.Anderson,ou=finance,ou=users,dc=example,dc=org', $providers[1]->getUserDN());
        $this->assertEquals(11, $providers[1]->getUserId());
    }

    public function testSaveLdapSyncStatus(): void
    {
        $ldapSyncStatus = new LDAPSyncStatus();
        $ldapSyncStatus->setSyncStartedAt(new DateTime('2022-10-12 01:31'));
        $ldapSyncStatus->setSyncFinishedAt(new DateTime('2022-10-12 01:32'));
        $ldapSyncStatus->getDecorator()->setSyncedUserByUserId(1);
        $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED);
        $ldapDao = new LDAPDao();
        $ldapSyncStatus = $ldapDao->saveLdapSyncStatus($ldapSyncStatus);
        $this->assertEquals(3, $ldapSyncStatus->getId());
        $this->assertInstanceOf(LDAPSyncStatus::class, $ldapSyncStatus);
        $this->assertInstanceOf(User::class, $ldapSyncStatus->getSyncedBy());
        $this->assertEquals(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED, $ldapSyncStatus->getSyncStatus());
        $this->assertEquals(new DateTime('2022-10-12 01:31'), $ldapSyncStatus->getSyncStartedAt());
        $this->assertEquals(new DateTime('2022-10-12 01:32'), $ldapSyncStatus->getSyncFinishedAt());
    }

    public function testGetLastLdapSyncStatus(): void
    {
        $ldapDao = new LDAPDao();
        $lastLdapSyncStatus = $ldapDao->getLastLDAPSyncStatus();
        $this->assertInstanceOf(LDAPSyncStatus::class, $lastLdapSyncStatus);
        $this->assertInstanceOf(User::class, $lastLdapSyncStatus->getSyncedBy());
        $this->assertEquals(2, $lastLdapSyncStatus->getId());
        $this->assertEquals(LDAPSyncStatus::SYNC_STATUS_FAILED, $lastLdapSyncStatus->getSyncStatus());
        $this->assertEquals(new DateTime('2022-10-12 02:31'), $lastLdapSyncStatus->getSyncStartedAt());
        $this->assertEquals(null, $lastLdapSyncStatus->getSyncFinishedAt());
    }
}
