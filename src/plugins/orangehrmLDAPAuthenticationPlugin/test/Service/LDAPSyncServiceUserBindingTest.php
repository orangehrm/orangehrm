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
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\LDAP\Dto\LDAPUserCollection;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockLogger;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Ldap\Entry;

/**
 * A local user found by user unique id stays bound to its own LDAP entry while that entry is still
 * in the directory: another entry that only carries the same unique id must not take the user over.
 *
 * Fixture: user 8 `Linda` (Admin role, employee 2) is bound to
 * `cn=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org` with unique id md5('Linda.Anderson').
 *
 * @group Admin
 * @group LDAP
 * @group Service
 */
class LDAPSyncServiceUserBindingTest extends KernelTestCase
{
    private const BOUND_DN = 'cn=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org';
    private const OTHER_DN = 'cn=Other.User,ou=sales,ou=users,dc=example,dc=org';

    private MockLogger $logger;

    protected function setUp(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2022-09-07 08:30'));
        $this->logger = new MockLogger();

        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::LDAP_LOGGER => $this->logger,
        ]);
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmLDAPAuthenticationPlugin/test/fixtures/LDAPSyncUsers_4.yaml'
        );
    }

    private function getBoundUserUniqueId(): string
    {
        return md5('Linda.Anderson');
    }

    private function createLDAPUser(
        string $username,
        string $dn,
        ?string $userUniqueId,
        string $userUniqueIdAttribute = 'employeeNumber'
    ): LDAPUser {
        $lookupSetting = LDAPUserLookupSetting::createFromArray([
            'baseDN' => 'ou=users,dc=example,dc=org',
            'userUniqueIdAttribute' => $userUniqueIdAttribute,
            'employeeSelectorMapping' => [],
        ]);
        $names = explode('.', $username);
        $attributes = ['cn' => [$username], 'givenName' => [$names[0]], 'sn' => [$names[1] ?? $names[0]]];
        if ($userUniqueId !== null) {
            $attributes[$userUniqueIdAttribute] = [$userUniqueId];
        }
        $entry = new Entry($dn, $attributes);
        return (new LDAPUser())
            ->setUserDN($dn)
            ->setUsername($username)
            ->setUserUniqueId($userUniqueId)
            ->setFirstName($names[0])
            ->setLastName($names[1] ?? $names[0])
            ->setUserLookupSetting($lookupSetting)
            ->setEntry($entry);
    }

    /**
     * @param LDAPUser[] $ldapUsers
     * @param string[] $failedUserDNs
     * @param LDAPUser[] $usersOfDuplicateUsername
     */
    private function createCollection(
        array $ldapUsers,
        array $failedUserDNs = [],
        array $usersOfDuplicateUsername = []
    ): LDAPUserCollection {
        $ldapUsersByUsername = [];
        foreach ($ldapUsers as $ldapUser) {
            $ldapUsersByUsername[$ldapUser->getUsername()] = $ldapUser;
        }
        $duplicates = [];
        foreach ($usersOfDuplicateUsername as $ldapUser) {
            $duplicates[$ldapUser->getUsername()][] = $ldapUser;
        }
        return new LDAPUserCollection($ldapUsersByUsername, array_keys($duplicates), $duplicates, $failedUserDNs);
    }

    private function assertBoundUserUnchanged(string $username = 'Linda'): void
    {
        $this->getEntityManager()->clear();
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->find(8);
        $this->assertEquals($username, $user->getUserName());
        $this->assertEquals(1, $user->getUserRole()->getId());
        $this->assertEquals(2, $user->getEmployee()->getEmpNumber());
        $this->assertEquals('Linda', $user->getEmployee()->getFirstName());
        $authProvider = $this->getEntityManager()->getRepository(UserAuthProvider::class)->find(1);
        $this->assertEquals(self::BOUND_DN, $authProvider->getLDAPUserDN());
        $this->assertEquals($this->getBoundUserUniqueId(), $authProvider->getLDAPUserUniqueId());
        $this->assertNull($this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => 'Other.User']));
        $this->assertNull($this->getEntityManager()->getRepository(Employee::class)->findOneBy(['firstName' => 'Other']));
    }

    private function assertWarningLogged(string $message): void
    {
        $this->assertTrue(
            $this->logger->getTestHandler()->hasWarningThatContains($message),
            "Expected warning: $message"
        );
    }

    /**
     * Report scenario: a second entry carries the unique id of a user whose entry is still in the directory.
     */
    public function testUniqueIdOfUserWhoseEntryStillExistsDoesNotRebind(): void
    {
        $bound = $this->createLDAPUser('Linda', self::BOUND_DN, $this->getBoundUserUniqueId());
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        foreach ([[$other, $bound], [$bound, $other]] as $ldapUsers) {
            (new LDAPSyncService())->createSystemUsers($ldapUsers, $this->createCollection($ldapUsers));
            $this->assertBoundUserUnchanged();
        }
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    /**
     * The bound entry counts as still in the directory also when it could not be read in this sync.
     */
    public function testBoundEntryThatFailedToLoadStillCounts(): void
    {
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers([$other], $this->createCollection([$other], [self::BOUND_DN]));

        $this->assertBoundUserUnchanged();
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    /**
     * ... or when it was dropped as a duplicate username.
     */
    public function testBoundEntryDroppedAsDuplicateUsernameStillCounts(): void
    {
        $bound = $this->createLDAPUser('Linda', self::BOUND_DN, null);
        $decoy = $this->createLDAPUser('Linda', 'cn=Linda,ou=sales,ou=users,dc=example,dc=org', null);
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers(
            [$other],
            $this->createCollection([$other], [], [$bound, $decoy])
        );

        $this->assertBoundUserUnchanged();
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    public function testBoundEntryIsMatchedIgnoringDNCase(): void
    {
        $bound = $this->createLDAPUser('Linda', strtoupper(self::BOUND_DN), $this->getBoundUserUniqueId());
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers([$other], $this->createCollection([$other, $bound]));

        $this->assertBoundUserUnchanged();
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    /**
     * Username change of the same entry: the user follows, as before.
     */
    public function testUniqueIdMatchWithSameDNUpdatesUsername(): void
    {
        $bound = $this->createLDAPUser('Linda.Anderson', self::BOUND_DN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers([$bound], $this->createCollection([$bound]));

        $this->assertBoundUserUnchanged('Linda.Anderson');
        $this->assertFalse(
            $this->logger->getTestHandler()->hasWarningThatContains('User unique id matches'),
            implode("\n", array_column($this->logger->getTestHandler()->getRecords(), 'message'))
        );
    }

    /**
     * Renamed or moved entry (the bound entry is gone): the user follows, as before.
     */
    public function testUniqueIdMatchFollowsRenamedEntryWhenBoundEntryIsGone(): void
    {
        $renamedDN = 'cn=Linda.Smith,ou=finance,ou=users,dc=example,dc=org';
        $renamed = $this->createLDAPUser('Linda.Smith', $renamedDN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers([$renamed], $this->createCollection([$renamed]));

        $this->getEntityManager()->clear();
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->find(8);
        $this->assertEquals('Linda.Smith', $user->getUserName());
        $this->assertEquals(1, $user->getUserRole()->getId());
        $this->assertEquals('Smith', $user->getEmployee()->getLastName());
        $authProvider = $this->getEntityManager()->getRepository(UserAuthProvider::class)->find(1);
        $this->assertEquals($renamedDN, $authProvider->getLDAPUserDN());
    }

    /**
     * Without the collection (callers other than sync()), only the given entries are known, as before.
     */
    public function testWithoutCollectionTheGivenEntriesAreUsed(): void
    {
        $bound = $this->createLDAPUser('Linda', self::BOUND_DN, $this->getBoundUserUniqueId());
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        (new LDAPSyncService())->createSystemUsers([$other, $bound]);

        $this->assertBoundUserUnchanged();
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    public function testUniqueIdMissingOnIncomingEntryCreatesSeparateUser(): void
    {
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, null);

        (new LDAPSyncService())->createSystemUsers([$other], $this->createCollection([$other]));

        $this->getEntityManager()->clear();
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => 'Other.User']);
        $this->assertNotEquals(8, $user->getId());
        $this->assertEquals(2, $user->getUserRole()->getId());
        $authProvider = $this->getEntityManager()->getRepository(UserAuthProvider::class)->find(1);
        $this->assertEquals(self::BOUND_DN, $authProvider->getLDAPUserDN());
    }

    /**
     * Report scenario through sync(): the victim keeps the account and is not deleted, no account for the attacker.
     */
    public function testSyncKeepsUserWhenAnotherEntryCopiesItsUniqueId(): void
    {
        $bound = $this->createLDAPUser('Linda', self::BOUND_DN, $this->getBoundUserUniqueId());
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, $this->getBoundUserUniqueId());

        $this->getSyncServiceReturning($this->createCollection([$other, $bound]))->sync();

        $this->assertFalse($this->isUserDeleted(8));
        $this->assertBoundUserUnchanged();
        $this->assertWarningLogged('User unique id matches a user bound to another LDAP entry, skipped');
    }

    public function testSyncDeletesUserWhoseEntryIsGone(): void
    {
        $other = $this->createLDAPUser('Other.User', self::OTHER_DN, '9999');

        $this->getSyncServiceReturning($this->createCollection([$other]))->sync();

        $this->assertTrue($this->isUserDeleted(8));
    }

    /**
     * @param LDAPUserCollection $ldapUserCollection
     * @return LDAPSyncService
     */
    private function getSyncServiceReturning(LDAPUserCollection $ldapUserCollection): LDAPSyncService
    {
        $syncService = $this->getMockBuilder(LDAPSyncService::class)
            ->onlyMethods(['fetchAllLDAPUsers'])
            ->getMock();
        $syncService->method('fetchAllLDAPUsers')->willReturn($ldapUserCollection);
        return $syncService;
    }

    private function isUserDeleted(int $userId): bool
    {
        $this->getEntityManager()->clear();
        return $this->getEntityManager()->getRepository(User::class)->find($userId)->isDeleted();
    }
}
