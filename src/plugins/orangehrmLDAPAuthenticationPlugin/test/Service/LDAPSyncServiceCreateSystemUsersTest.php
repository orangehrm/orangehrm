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

namespace OrangeHRM\Tests\LDAP\Service;

use DateTime;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dao\LDAPDao;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Ldap\Entry;

/**
 * @group Admin
 * @group LDAP
 * @group Service
 */
class LDAPSyncServiceCreateSystemUsersTest extends KernelTestCase
{
    private string $fixtureDir;

    protected function setUp(): void
    {
        $this->fixtureDir = Config::get(Config::PLUGINS_DIR) . '/orangehrmLDAPAuthenticationPlugin/test/fixtures';
        $now = new DateTime('2022-09-07 08:30');
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => clone $now);

        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP'),
        ]);
    }

    public function testCreateSystemUsers(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class, User::class, Employee::class]);

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(5, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(Employee::class)->count([]));
    }

    public function testCreateSystemUsersWithLocalUsers(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_2.yaml');

        $this->assertEquals(3, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(0, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(Employee::class)->count([]));

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));
    }

    public function testCreateSystemUsersWithEmployeeMapping(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_3.yaml');

        $this->assertEquals(5, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(0, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(11, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(['firstName' => 'Jacqueline']);
        $this->assertEquals('#', $employee->getLastName());

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(10, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(15, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(['firstName' => 'Jacqueline']);
        $this->assertEquals('White', $employee->getLastName());
    }

    public function testCreateSystemUsersWhenUsernameChanged(): void
    {
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_4.yaml');

        $this->assertEquals(6, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(1, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(9, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => 'Linda']);
        $this->assertEquals(2, $user->getEmpNumber());

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(10, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(13, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => 'Linda']);
        $this->assertNull($user);
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => 'Linda.Anderson']);
        $this->assertEquals(2, $user->getEmpNumber());
    }

    public function testCreateSystemUsersWhenFieldsNotChanged(): void
    {
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_5.yaml');

        $this->assertEquals(6, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(1, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(9, $this->getEntityManager()->getRepository(Employee::class)->count([]));

        $ldapSyncDao = $this->getMockBuilder(LDAPDao::class)
            ->onlyMethods(['getUserByUserName'])
            ->getMock();
        $ldapSyncDao->expects($this->exactly(5))
            ->method('getUserByUserName')
            ->willReturnCallback(function ($username) {
                if ($username === 'Linda.Anderson') {
                    $authProvider = $this->getEntityManager()
                        ->getRepository(UserAuthProvider::class)
                        ->findOneBy(['ldapUserUniqueId' => 'ba79db9bc13dc13d512f3b82bcdb8d47']);
                    $user = $this->getMockBuilder(User::class)
                        ->onlyMethods(['getEmployee'])
                        ->getMock();
                    $user->expects($this->never())
                        ->method('getEmployee');
                    $user->setAuthProviders([$authProvider]);
                    return $user;
                }
                return $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => $username]);
            });
        $ldapSyncService = $this->getMockBuilder(LDAPSyncService::class)
            ->onlyMethods(['getLDAPDao'])
            ->getMock();
        $ldapSyncService->expects($this->exactly(14))
            ->method('getLDAPDao')
            ->willReturn($ldapSyncDao);
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(10, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(13, $this->getEntityManager()->getRepository(Employee::class)->count([]));
    }

    public function testCreateSystemUsersWhenFieldsChanged(): void
    {
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_6.yaml');

        $this->assertEquals(6, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(1, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(9, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(
            ['workEmail' => 'Anderson@example.org']
        );
        $this->assertEquals('Anderson', $employee->getLastName());

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers());

        $this->assertEquals(10, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(13, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(
            ['workEmail' => 'Anderson@example.org']
        );
        $this->assertNull($employee);
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(
            ['workEmail' => 'Linda@example.org']
        );
        $this->assertEquals('Anderson', $employee->getLastName());
    }

    public function testCreateSystemUsersWithNonUniqueEmployeeIdAndWorkEmail(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncUsers_7.yaml');

        $this->assertEquals(3, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(0, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(8, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)
            ->findOneBy(['workEmail' => 'Fiona@example.org']);
        $this->assertEquals('Scott', $employee->getLastName());
        $employee = $this->getEntityManager()->getRepository(Employee::class)
            ->findOneBy(['workEmail' => 'cecil@example.org']);
        $this->assertEquals('Bonaparte', $employee->getLastName());

        $ldapSyncService = new LDAPSyncService();
        $ldapSyncService->createSystemUsers($this->getLDAPUsers([]));

        $this->assertEquals(6, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(3, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(11, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $employee = $this->getEntityManager()->getRepository(Employee::class)
            ->findOneBy(['workEmail' => 'Fiona@example.org']);
        $this->assertEquals('Scott', $employee->getLastName());
        $employee = $this->getEntityManager()->getRepository(Employee::class)
            ->findOneBy(['workEmail' => 'cecil@example.org']);
        $this->assertEquals('Bonaparte', $employee->getLastName());
    }

    /**
     * @param array $employeeSelectorMapping
     * @return LDAPUser[]
     */
    private function getLDAPUsers(
        array $employeeSelectorMapping = [['field' => 'workEmail', 'attributeName' => 'mail']]
    ): array {
        $ldapUsers = [];
        $lookupSetting = LDAPUserLookupSetting::createFromArray([
            'baseDN' => 'ou=admin,ou=users,dc=example,dc=org',
            'userUniqueIdAttribute' => 'entryUUID',
            'employeeSelectorMapping' => $employeeSelectorMapping,
        ]);
        $users = ['Linda.Anderson', 'Rebecca.Harmony', 'Lisa.Andrews', 'Jacqueline.White', 'Fiona.Grace'];
        foreach ($users as $i => $user) {
            $names = explode('.', $user);
            $entry = new Entry(
                "cn=$user,ou=admin,ou=users,dc=example,dc=org",
                [
                    'cn' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)],
                    'userPassword' => [$user],
                    'entryUUID' => [md5($user)],
                    'mail' => [$names[0] . '@example.org'],
                ]
            );
            $ldapUsers[] = (new LDAPUser())
                ->setUserDN($entry->getDn())
                ->setUsername($entry->getAttribute('cn')[0])
                ->setUserUniqueId($entry->getAttribute('entryUUID')[0])
                ->setFirstName($entry->getAttribute('givenName')[0])
                ->setLastName($entry->getAttribute('sn')[0])
                ->setEmployeeId("010$i")
                ->setWorkEmail($entry->getAttribute('mail')[0])
                ->setUserLookupSetting($lookupSetting)
                ->setEntry($entry);
        }

        return $ldapUsers;
    }
}
