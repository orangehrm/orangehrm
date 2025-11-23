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
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPService;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use OrangeHRM\Tests\LDAP\LDAPConnectionHelperTrait;
use OrangeHRM\Tests\LDAP\LDAPServerConfig;
use OrangeHRM\Tests\LDAP\LDAPUsersFixture;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group LDAP
 * @group Service
 */
class LDAPSyncServiceTest extends KernelTestCase
{
    use LDAPConnectionHelperTrait;

    private static LDAPServerConfig $serverConfig;
    private static bool $configured = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::isLDAPServerConfigured()) {
            parent::markTestSkipped('Configure LDAP server config: ' . self::getLDAPServerConfigFilePath());
        }
        self::$serverConfig = self::getLDAPServerConfig();
    }

    protected function setUp(): void
    {
        if (!self::$configured) {
            self::$configured = true;
            $this->configureServerAndLoadData();
        }
        TestDataService::truncateSpecificTables([UserAuthProvider::class, User::class, Employee::class]);
    }

    protected function configureServerAndLoadData(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn(
                new LDAPSetting(
                    self::$serverConfig->host,
                    self::$serverConfig->port,
                    'OpenLDAP',
                    self::$serverConfig->encryption
                )
            );
        $this->createKernelWithMockServices([Services::CONFIG_SERVICE => $configService]);

        $ldapAuthService = new LDAPService();
        $ldapAuthService->bind(
            new UserCredential(self::$serverConfig->configAdminDN, self::$serverConfig->configAdminPassword)
        );

        $query = $ldapAuthService->query('cn=config', 'cn=config');
        $configEntry = $query->execute()->toArray()[0];

        /**
         * Enable anonymous binding
         */
        if ($configEntry->hasAttribute('olcDisallows')) {
            $ldapAuthService->getEntryManager()->removeAttributeValues($configEntry, 'olcDisallows', ['bind_anon']);
        }

        /**
         * Define LDAP server page size as 500
         */
        $sizeLimit = $configEntry->getAttribute('olcSizeLimit');
        if (!is_null($sizeLimit)) {
            $ldapAuthService->getEntryManager()->removeAttributeValues($configEntry, 'olcSizeLimit', $sizeLimit);
        }
        $ldapAuthService->getEntryManager()->addAttributeValues($configEntry, 'olcSizeLimit', ['500']);

        /**
         * Clean and Load predefined LDAP user
         */
        $ldapAuthService->bind(
            new UserCredential(self::$serverConfig->adminDN, self::$serverConfig->adminPassword)
        );
        $fixture = new LDAPUsersFixture($ldapAuthService->getAdapter());
        $fixture->clean();
        $fixture->load();
    }

    public function testFetchEntryCollections(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $lookupSetting = new LDAPUserLookupSetting('ou=engineering,ou=users,dc=example,dc=org');
        $lookupSetting->setSearchScope('sub');
        $ldapSetting->addUserLookupSetting($lookupSetting);
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $entryCollections = $ldapSyncService->fetchEntryCollections();
        $this->assertCount(1103, $entryCollections);
    }

    public function testFetchEntryCollectionsWithOneLevelLookupEmptyBase(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $lookupSetting = new LDAPUserLookupSetting('ou=sales,ou=users,dc=example,dc=org');
        $lookupSetting->setSearchScope('one');
        $ldapSetting->addUserLookupSetting($lookupSetting);
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $entryCollections = $ldapSyncService->fetchEntryCollections();
        $this->assertCount(0, $entryCollections);
    }

    public function testFetchEntryCollectionsWithOneLevelLookup(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $lookupSetting = new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org');
        $lookupSetting->setSearchScope('one');
        $ldapSetting->addUserLookupSetting($lookupSetting);
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $entryCollections = $ldapSyncService->fetchEntryCollections();
        $this->assertCount(7, $entryCollections);
    }

    public function testFetchEntryCollectionsWithMultipleLookups(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=legal,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=engineering,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
        );
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $entryCollections = $ldapSyncService->fetchEntryCollections();
        $this->assertCount(1110, $entryCollections);
    }

    public function testFetchEntryCollectionsBindAnon(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $lookupSetting = new LDAPUserLookupSetting('ou=engineering,ou=users,dc=example,dc=org');
        $ldapSetting->addUserLookupSetting($lookupSetting);
        $ldapSetting->setBindAnonymously(true);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $entryCollections = $ldapSyncService->fetchEntryCollections();
        // When bind anonymously, results limit for server max limit
        $this->assertCount(500, $entryCollections);
    }

    public function testFetchAllLDAPUsers(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('uid')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=legal,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('uid')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=engineering,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('uid')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=marketing,ou=sales,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('mail')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=finance,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $timeStart = microtime(true);
        $ldapSyncService = new LDAPSyncService();
        $ldapUserCollection = $ldapSyncService->fetchAllLDAPUsers();

        // check possibility of serializing
        $filePath = __DIR__ . '/ldap_user_collection.txt';
        file_put_contents($filePath, serialize($ldapUserCollection));
        $ldapUserCollection = unserialize(file_get_contents($filePath));
        $timeEnd = microtime(true);
        unlink($filePath);
        //echo "\nExecution time:" . ($timeEnd - $timeStart);

        $expectedFailedCount = 4;
        $expectedDuplicateUserCount = 204;
        $this->assertCount($expectedFailedCount, $ldapUserCollection->getFailedUsers());
        $this->assertCount(102, $ldapUserCollection->getDuplicateUsernames());
        $this->assertCount(102, $ldapUserCollection->getUsersOfDuplicateUsernames());
        $this->assertEquals($expectedDuplicateUserCount, $ldapUserCollection->getDuplicateUserCount());
        $this->assertCount(
            1116 - $expectedFailedCount - $expectedDuplicateUserCount,
            $ldapUserCollection->getLDAPUsers()
        );
    }

    public function testFetchAllLDAPUsersWithSpecialCharacters(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=client services,ou=sales,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute(null)
        );
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $ldapSyncService = new LDAPSyncService();
        $ldapUserCollection = $ldapSyncService->fetchAllLDAPUsers();
        $ldapUsers = $ldapUserCollection->getLDAPUsers();

        $this->assertCount(0, $ldapUserCollection->getFailedUsers());
        $this->assertCount(0, $ldapUserCollection->getDuplicateUsernames());
        $this->assertCount(0, $ldapUserCollection->getUsersOfDuplicateUsernames());
        $this->assertEquals(0, $ldapUserCollection->getDuplicateUserCount());
        $this->assertCount(12, $ldapUsers);

        $users = [
            ['#Aaliyah+Haq', 'Aaliyah', 'Haq', '\23Aaliyah\2BHaq'],
            ['Amar;(Anthony)', 'Amar', 'Anthony', 'Amar\3B(Anthony)'],
            ['Anthony\/Nolan', 'Anthony', 'Nolan', 'Anthony\5C/Nolan'],
            ['Cassidy!:Hope', 'Cassidy\\', 'Hope', 'Cassidy!:Hope'],
            ['Charlie<Carter>', 'Charlie', 'Carter', 'Charlie\3CCarter\3E'],
            ['Chenzira.Chuki', 'Chenzira', 'Chuki', 'Chenzira.Chuki'],
            ['James="Jim"-Smith', 'James', 'Smith, III', 'James\3D\22Jim\22-Smith'],
            ['Ehioze\'Ebo', 'Ehioze', 'Ebo', "Ehioze'Ebo"],
            ['Joe,Root', 'Joe', 'Root', 'Joe\2CRoot'],
            ['Jordan+uid=Mathews', 'Jordan', 'Mathews', 'Jordan\2Buid\3DMathews'],
            ['Luke,ou=Wright', 'Luke', 'Wright', 'Luke\2Cou\3DWright'],
            ['Jadine Jackie', 'Jadine', 'Jackie', 'Jadine Jackie'],
        ];
        foreach ($users as $user) {
            $ldapUser = $ldapUsers[$user[0]];
            $this->assertEquals($user[0], $ldapUser->getUsername());
            $this->assertEquals($user[1], $ldapUser->getFirstName());
            $this->assertEquals($user[2], $ldapUser->getLastName());
            $this->assertEquals(
                'cn=' . $user[3] . ',ou=client services,ou=sales,ou=users,dc=example,dc=org',
                $ldapUser->getUserDN()
            );
        }
    }

    public function testCreateSystemUsers(): void
    {
        $ldapSetting = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('uid')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=legal,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('uid')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=marketing,ou=sales,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('mail')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=finance,ou=users,dc=example,dc=org'))
                ->setSearchScope('one')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $now = new DateTime('2022-09-07 08:30');
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => clone $now);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->exactly(2))
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::CONFIG_SERVICE => $configService,
            Services::USER_SERVICE => new UserService(),
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP')
        ]);

        $timeStart = microtime(true);
        $ldapSyncService = new LDAPSyncService();
        $ldapUserCollection = $ldapSyncService->fetchAllLDAPUsers();
        $this->assertCount(4, $ldapUserCollection->getFailedUsers());
        $ldapSyncService->createSystemUsers(array_values($ldapUserCollection->getLDAPUsers()));
        $timeEnd = microtime(true);
        //echo "\nExecution time:" . ($timeEnd - $timeStart);

        $this->assertEquals(7, $this->getEntityManager()->getRepository(User::class)->count([]));
        $this->assertEquals(7, $this->getEntityManager()->getRepository(UserAuthProvider::class)->count([]));
        $this->assertEquals(7, $this->getEntityManager()->getRepository(Employee::class)->count([]));
    }
}
