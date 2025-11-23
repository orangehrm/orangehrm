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

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dto\EntryCollection;
use OrangeHRM\LDAP\Dto\EntryCollectionLookupSettingPair;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserDataMapping;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockLogger;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;
use Symfony\Component\Ldap\Entry;

/**
 * @group Admin
 * @group LDAP
 * @group Service
 */
class LDAPSyncServiceDeleteUsersTest extends KernelTestCase
{
    private string $fixtureDir;

    protected function setUp(): void
    {
        $this->fixtureDir = Config::get(Config::PLUGINS_DIR) . '/orangehrmLDAPAuthenticationPlugin/test/fixtures';
    }

    public function testDeleteLocalUsersWhoRemovedFromLDAPServer(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncDeleteUsers_1.yaml');

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));

        $ldapUserDataMapping = (new LDAPUserDataMapping())
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setEmployeeIdAttribute('employeeNumber')
            ->setWorkEmailAttribute('mail');
        $ldapSetting = $this->getMockBuilder(LDAPSetting::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataMapping'])
            ->getMock();
        $ldapSetting->expects($this->exactly(4))
            ->method('getDataMapping')
            ->willReturn($ldapUserDataMapping);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
        ]);

        $lookupSetting = LDAPUserLookupSetting::createFromArray([
            'baseDN' => 'ou=admin,ou=users,dc=example,dc=org',
            'userUniqueIdAttribute' => 'entryUUID',
            'employeeSelectorMapping' => [['field' => 'workEmail', 'attributeName' => 'mail']],
        ]);

        $ldapSyncService = $this->getMockBuilder(LDAPSyncService::class)
            ->onlyMethods(['fetchEntryCollections'])
            ->getMock();

        $users = ['Linda.Anderson', 'Rebecca.Harmony', 'Lisa.Andrews', 'Jacqueline.White'];
        $ldapSyncService->expects($this->once())
            ->method('fetchEntryCollections')
            ->willReturn($this->getEntryCollection($this->getLDAPEntries($users), $lookupSetting));
        $ldapSyncService->deleteLocalUsersWhoRemovedFromLDAPServer($ldapSyncService->fetchAllLDAPUsers());

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $this->assertEquals(1, $this->getEntityManager()->getRepository(User::class)->count(['deleted' => true]));
        $this->assertTrue(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Fiona.Grace'])
                ->isDeleted()
        );
    }

    public function testDeleteLocalUsersWhoRemovedFromLDAPServerWithDuplicateUserNames(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncDeleteUsers_2.yaml');

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));

        $ldapUserDataMapping = (new LDAPUserDataMapping())
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setEmployeeIdAttribute('employeeNumber')
            ->setWorkEmailAttribute('mail');
        $ldapSetting = $this->getMockBuilder(LDAPSetting::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataMapping'])
            ->getMock();
        $ldapSetting->expects($this->exactly(4))
            ->method('getDataMapping')
            ->willReturn($ldapUserDataMapping);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::LDAP_LOGGER => new MockLogger(),
        ]);

        $lookupSetting = LDAPUserLookupSetting::createFromArray([
            'baseDN' => 'ou=admin,ou=users,dc=example,dc=org',
            'userUniqueIdAttribute' => 'entryUUID',
            'employeeSelectorMapping' => [['field' => 'workEmail', 'attributeName' => 'mail']],
        ]);

        $ldapSyncService = $this->getMockBuilder(LDAPSyncService::class)
            ->onlyMethods(['fetchEntryCollections'])
            ->getMock();
        $ldapSyncService->expects($this->once())
            ->method('fetchEntryCollections')
            ->willReturn($this->getEntryCollection($this->getLDAPEntriesWithDuplicateUserNames(), $lookupSetting));
        $ldapSyncService->deleteLocalUsersWhoRemovedFromLDAPServer($ldapSyncService->fetchAllLDAPUsers());

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $this->assertEquals(2, $this->getEntityManager()->getRepository(User::class)->count(['deleted' => true]));
        $this->assertFalse(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Lisa.Andrews']) // Not deleting duplicated users
                ->isDeleted()
        );
        $this->assertTrue(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Jacqueline.White'])
                ->isDeleted()
        );
        $this->assertTrue(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Fiona.Grace'])
                ->isDeleted()
        );
    }

    public function testDeleteLocalUsersWhoRemovedFromLDAPServerWithFailedUsers(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class]);
        TestDataService::populate($this->fixtureDir . '/LDAPSyncDeleteUsers_3.yaml');

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $this->assertEquals(1, $this->getEntityManager()->getRepository(User::class)->count(['deleted' => true]));

        $ldapUserDataMapping = (new LDAPUserDataMapping())
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setEmployeeIdAttribute('employeeNumber')
            ->setWorkEmailAttribute('mail');
        $ldapSetting = $this->getMockBuilder(LDAPSetting::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataMapping'])
            ->getMock();
        $ldapSetting->expects($this->exactly(3))
            ->method('getDataMapping')
            ->willReturn($ldapUserDataMapping);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP'),
        ]);

        $lookupSetting = LDAPUserLookupSetting::createFromArray([
            'baseDN' => 'ou=admin,ou=users,dc=example,dc=org',
            'userUniqueIdAttribute' => 'entryUUID',
            'employeeSelectorMapping' => [['field' => 'workEmail', 'attributeName' => 'mail']],
        ]);

        $ldapSyncService = $this->getMockBuilder(LDAPSyncService::class)
            ->onlyMethods(['fetchEntryCollections'])
            ->getMock();
        $ldapSyncService->expects($this->once())
            ->method('fetchEntryCollections')
            ->willReturn($this->getEntryCollection($this->getLDAPEntriesWithFailedUsers(), $lookupSetting));
        $ldapSyncService->deleteLocalUsersWhoRemovedFromLDAPServer($ldapSyncService->fetchAllLDAPUsers());

        $this->assertEquals(8, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(5, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(10, $this->getEntityManager()->getRepository(Employee::class)->count([]));
        $this->assertEquals(2, $this->getEntityManager()->getRepository(User::class)->count(['deleted' => true]));
        $this->assertFalse(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Rebecca.Harmony']) // Not deleting failed users
                ->isDeleted()
        );
        $this->assertTrue(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Jacqueline.White'])
                ->isDeleted()
        );
        $this->assertTrue(
            $this->getEntityManager()
                ->getRepository(User::class)
                ->findOneBy(['userName' => 'Fiona.Grace']) // Already deleted
                ->isDeleted()
        );
    }

    /**
     * @param Entry[] $entries
     * @param LDAPUserLookupSetting $lookupSetting
     * @return EntryCollection
     */
    private function getEntryCollection(array $entries, LDAPUserLookupSetting $lookupSetting)
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])
            ->getMock();
        $collection->expects($this->once())
            ->method('toArray')
            ->willReturn($entries);
        $entryCollectionLookupSettingPair = $this->getMockBuilder(EntryCollectionLookupSettingPair::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCollection', 'getLookupSetting'])
            ->getMock();
        $entryCollectionLookupSettingPair->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);
        $entryCollectionLookupSettingPair->expects($this->exactly(count($entries)))
            ->method('getLookupSetting')
            ->willReturn($lookupSetting);
        $entryCollection = $this->getMockBuilder(EntryCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCollections'])
            ->getMock();
        $entryCollection->expects($this->once())
            ->method('getCollections')
            ->willReturn([$entryCollectionLookupSettingPair]);
        return $entryCollection;
    }

    /**
     * @param string[] $users
     * @return Entry[]
     */
    private function getLDAPEntries(array $users): array
    {
        $entries = [];
        foreach ($users as $i => $user) {
            $names = explode('.', $user);
            $entries[] = new Entry(
                "cn=$user,ou=admin,ou=users,dc=example,dc=org",
                [
                    'cn' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)],
                    'userPassword' => [$user],
                    'entryUUID' => [md5($user)],
                    'mail' => [$names[0] . '@example.org'],
                    'employeeNumber' => ["010$i"]
                ]
            );
        }

        return $entries;
    }

    /**
     * @return Entry[]
     */
    private function getLDAPEntriesWithDuplicateUserNames(): array
    {
        $entries = [];
        $users = [
            [
                'dn' => 'cn=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Linda.Anderson'],
                'sn' => ['Anderson'],
                'givenName' => ['Linda'],
                'displayName' => ['Linda Anderson'],
                'entryUUID' => [md5('Linda.Anderson')],
                'mail' => ['Linda@example.org'],
                'employeeNumber' => ['0100']
            ],
            [
                'dn' => 'cn=Rebecca.Harmony,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Rebecca.Harmony'],
                'sn' => ['Harmony'],
                'givenName' => ['Rebecca'],
                'displayName' => ['Rebecca Harmony'],
                'entryUUID' => [md5('Rebecca.Harmony')],
                'mail' => ['Rebecca@example.org'],
                'employeeNumber' => ['0101']
            ],
            [
                'dn' => 'cn=Lisa.Andrews,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews'],
                'sn' => ['Andrews'],
                'givenName' => ['Lisa'],
                'displayName' => ['Lisa Andrews'],
                'entryUUID' => [md5('Lisa.Andrews')],
                'mail' => ['Lisa@example.org'],
                'employeeNumber' => ['0102']
            ],
            [
                'dn' => 'cn=Lisa.Andrews,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews'],
                'sn' => ['Andrews'],
                'givenName' => ['Lisa'],
                'displayName' => ['Lisa Andrews'],
                'entryUUID' => [md5('Lisa.Andrews_2')],
                'mail' => ['Andrews@example.org'],
                'employeeNumber' => ['0103']
            ]
        ];
        foreach ($users as $user) {
            $dn = $user['dn'];
            unset($user['dn']);
            $entries[] = new Entry($dn, $user);
        }

        return $entries;
    }

    /**
     * @return Entry[]
     */
    private function getLDAPEntriesWithFailedUsers(): array
    {
        $entries = [];
        $users = [
            [
                'dn' => 'cn=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Linda.Anderson'],
                'sn' => ['Anderson'],
                'givenName' => ['Linda'],
                'displayName' => ['Linda Anderson'],
                'entryUUID' => [md5('Linda.Anderson')],
                'mail' => ['Linda@example.org'],
                'employeeNumber' => ['0100']
            ],
            [
                'dn' => 'cn=Rebecca.Harmony,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Rebecca.Harmony'],
                'sn' => ['Harmony'],
                // removed `givenName`, but it required for the application
                'displayName' => ['Rebecca Harmony'],
                'entryUUID' => [md5('Rebecca.Harmony')],
                'mail' => ['Rebecca@example.org'],
                'employeeNumber' => ['0101']
            ],
            [
                'dn' => 'cn=Lisa.Andrews,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews'],
                'sn' => ['Andrews'],
                'givenName' => ['Lisa'],
                'displayName' => ['Lisa Andrews'],
                'entryUUID' => [md5('Lisa.Andrews')],
                'mail' => ['Lisa@example.org'],
                'employeeNumber' => ['0102']
            ],
        ];
        foreach ($users as $user) {
            $dn = $user['dn'];
            unset($user['dn']);
            $entries[] = new Entry($dn, $user);
        }

        return $entries;
    }
}
