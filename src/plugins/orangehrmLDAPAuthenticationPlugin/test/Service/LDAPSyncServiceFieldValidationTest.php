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

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dto\EntryCollection;
use OrangeHRM\LDAP\Dto\EntryCollectionLookupSettingPair;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserDataMapping;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPSyncService;
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
class LDAPSyncServiceFieldValidationTest extends KernelTestCase
{
    public function testDeleteLocalUsersWhoRemovedFromLDAPServer(): void
    {
        TestDataService::truncateSpecificTables([UserAuthProvider::class, User::class, Employee::class]);

        $entries = $this->getLDAPEntries();
        $ldapUserDataMapping = (new LDAPUserDataMapping())
            ->setFirstNameAttribute('givenName')
            ->setMiddleNameAttribute('displayName')
            ->setLastNameAttribute('sn')
            ->setEmployeeIdAttribute('employeeNumber')
            ->setWorkEmailAttribute('mail');
        $ldapSetting = $this->getMockBuilder(LDAPSetting::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataMapping'])
            ->getMock();
        $ldapSetting->expects($this->exactly(count($entries)))
            ->method('getDataMapping')
            ->willReturn($ldapUserDataMapping);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn($ldapSetting);

        $logger = new MockLogger();
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::LDAP_LOGGER => $logger,
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
            ->willReturn($this->getEntryCollection($entries, $lookupSetting));

        $collection = $ldapSyncService->fetchAllLDAPUsers();

        $this->assertCount(12, $collection->getFailedUsers());
        $this->assertCount(8, $collection->getLDAPUsers());
        $logs = $logger->getTestHandler()->getRecords();
        $this->assertEquals('Username attribute `cn` not exist', $logs[0]['message']);
        $this->assertEquals('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org', $logs[0]['context'][0]);

        $this->assertEquals('First name attribute `givenName` not exist', $logs[1]['message']);
        $this->assertEquals('cn=Rebecca.Harmony,ou=admin,ou=users,dc=example,dc=org', $logs[1]['context'][0]);

        $this->assertEquals('Last name attribute `sn` not exist', $logs[2]['message']);
        $this->assertEquals('cn=Lisa.Andrews,ou=admin,ou=users,dc=example,dc=org', $logs[2]['context'][0]);

        $this->assertEquals('First name length should not exceed 30 characters', $logs[3]['message']);
        $this->assertEquals('cn=Lisa.Andrews,ou=hr,ou=users,dc=example,dc=org', $logs[3]['context'][0]);

        $this->assertEquals('Last name length should not exceed 30 characters', $logs[4]['message']);
        $this->assertEquals('cn=Fiona.Grace,ou=hr,ou=users,dc=example,dc=org', $logs[4]['context'][0]);

        $this->assertEquals('Middle name length should not exceed 30 characters', $logs[5]['message']);
        $this->assertEquals('cn=David.Morris,ou=hr,ou=users,dc=example,dc=org', $logs[5]['context'][0]);

        $this->assertEquals('Username length should be at least 5 characters', $logs[6]['message']);
        $this->assertEquals('uid=Garry.White,ou=hr,ou=users,dc=example,dc=org', $logs[6]['context'][0]);

        $this->assertEquals('Username length should not exceed 40 characters', $logs[7]['message']);
        $this->assertEquals('uid=Jasmine.Morgan,ou=hr,ou=users,dc=example,dc=org', $logs[7]['context'][0]);

        $this->assertEquals('Employee Id length should not exceed 10 characters', $logs[8]['message']);
        $this->assertEquals('uid=John.Smith,ou=hr,ou=users,dc=example,dc=org', $logs[8]['context'][0]);

        $this->assertEquals('Employee work email length should not exceed 50 characters', $logs[9]['message']);
        $this->assertEquals('uid=Kevin.Mathews,ou=hr,ou=users,dc=example,dc=org', $logs[9]['context'][0]);

        $this->assertEquals('Invalid employee work email', $logs[10]['message']);
        $this->assertEquals('uid=Peter.Anderson,ou=hr,ou=users,dc=example,dc=org', $logs[10]['context'][0]);

        $this->assertEquals('User DN length should not exceed 255 characters', $logs[11]['message']);
        $this->assertEquals(
            'uid=' . str_repeat('Sara.Morgan', 20) . ',ou=hr,ou=user,dc=example,dc=org',
            $logs[11]['context'][0]
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
     * @return Entry[]
     */
    private function getLDAPEntries(): array
    {
        $entries = [];
        $users = [
            [ // username attribute `cn` not exist
                'dn' => 'uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org',
                'sn' => ['Anderson'],
                'givenName' => ['Linda'],
                'entryUUID' => [md5('Linda.Anderson')],
                'mail' => ['Linda@example.org'],
                'employeeNumber' => ['0100']
            ],
            [ // missing first name attribute `givenName`
                'dn' => 'cn=Rebecca.Harmony,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Rebecca.Harmony'],
                'sn' => ['Harmony'],
                'entryUUID' => [md5('Rebecca.Harmony')],
                'mail' => ['Rebecca@example.org'],
                'employeeNumber' => ['0101']
            ],
            [ // missing last name attribute `sn`
                'dn' => 'cn=Lisa.Andrews,ou=admin,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews'],
                'givenName' => ['Lisa'],
                'entryUUID' => [md5('Lisa.Andrews')],
                'mail' => ['Lisa@example.org'],
                'employeeNumber' => ['0102']
            ],
            [ // exceed first name max length
                'dn' => 'cn=Lisa.Andrews,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews'],
                'sn' => ['Andrews'],
                'givenName' => [str_repeat('abcde', 6) . 'f'],
                'entryUUID' => [md5('Lisa.Andrews_2')],
                'mail' => ['Andrews@example.org'],
                'employeeNumber' => ['0103']
            ],
            [ // not exceed first name max length
                'dn' => 'cn=Lisa.Andrews_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Lisa.Andrews_2'],
                'sn' => ['Andrews'],
                'givenName' => [str_repeat('abcde', 6)],
                'entryUUID' => [md5('cn=Lisa.Andrews_2')],
                'employeeNumber' => ['00103']
            ],
            [ // exceed last name max length
                'dn' => 'cn=Fiona.Grace,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Fiona.Grace'],
                'sn' => [str_repeat('abcde', 6) . 'f'],
                'givenName' => ['Fiona'],
                'entryUUID' => [md5('Fiona.Grace')],
                'employeeNumber' => ['0104']
            ],
            [ // not exceed last name max length
                'dn' => 'cn=Fiona.Grace_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Fiona.Grace_2'],
                'sn' => [str_repeat('abcde', 6)],
                'givenName' => ['Fiona'],
                'entryUUID' => [md5('Fiona.Grace_2')],
                'employeeNumber' => ['00104']
            ],
            [ // exceed middle name max length
                'dn' => 'cn=David.Morris,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['David.Morris'],
                'sn' => ['Morris'],
                'displayName' => [str_repeat('abcde', 6) . 'f'],
                'givenName' => ['David'],
                'entryUUID' => [md5('David.Morris')],
                'employeeNumber' => ['0105']
            ],
            [ // not exceed middle name max length
                'dn' => 'cn=David.Morris_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['David.Morris_2'],
                'sn' => ['Morris'],
                'displayName' => [str_repeat('abcde', 6)],
                'givenName' => ['David'],
                'entryUUID' => [md5('David.Morris_2')],
                'employeeNumber' => ['00105']
            ],
            [ // username less than min length
                'dn' => 'uid=Garry.White,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Test'],
                'sn' => ['White'],
                'givenName' => ['Garry'],
                'entryUUID' => [md5('Garry.White')],
                'employeeNumber' => ['0106']
            ],
            [ // username length = min length
                'dn' => 'uid=Garry.White_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Garry'],
                'sn' => ['White'],
                'givenName' => ['Garry'],
                'entryUUID' => [md5('Garry.White_2')],
                'employeeNumber' => ['00106']
            ],
            [ // exceed username max length
                'dn' => 'uid=Jasmine.Morgan,ou=hr,ou=users,dc=example,dc=org',
                'cn' => [str_repeat('abcde', 8) . 'f'],
                'sn' => ['Morgan'],
                'givenName' => ['Jasmine'],
                'entryUUID' => [md5('uid=Jasmine.Morgan')],
                'employeeNumber' => ['0107']
            ],
            [ // not exceed username max length
                'dn' => 'uid=Jasmine.Morgan_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => [str_repeat('abcde', 8)],
                'sn' => ['Morgan'],
                'givenName' => ['Jasmine'],
                'entryUUID' => [md5('uid=Jasmine.Morgan_2')],
                'employeeNumber' => ['00107']
            ],
            [ // exceed employee id max length
                'dn' => 'uid=John.Smith,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['John.Smith'],
                'sn' => ['Smith'],
                'givenName' => ['John'],
                'entryUUID' => [md5('uid=John.Smith')],
                'employeeNumber' => [str_repeat('12345', 2) . '6']
            ],
            [ // not exceed employee id max length
                'dn' => 'uid=John.Smith_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['John.Smith_2'],
                'sn' => ['Smith'],
                'givenName' => ['John'],
                'entryUUID' => [md5('uid=John.Smith_2')],
                'employeeNumber' => [str_repeat('12345', 2)]
            ],
            [ // exceed employee work email max length
                'dn' => 'uid=Kevin.Mathews,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Kevin.Mathews'],
                'sn' => ['Mathews'],
                'givenName' => ['Kevin'],
                'entryUUID' => [md5('uid=Kevin.Mathews')],
                'employeeNumber' => ['0108'],
                'mail' => [str_repeat('abcdef', 6) . 'abc@example.org']
            ],
            [ // not exceed employee work email max length
                'dn' => 'uid=Kevin.Mathews_2,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Kevin.Mathews_2'],
                'sn' => ['Mathews'],
                'givenName' => ['Kevin'],
                'entryUUID' => [md5('uid=Kevin.Mathews_2')],
                'employeeNumber' => ['00108'],
                'mail' => [str_repeat('abcdef', 6) . 'ab@example.org']
            ],
            [ // invalid employee work email
                'dn' => 'uid=Peter.Anderson,ou=hr,ou=users,dc=example,dc=org',
                'cn' => ['Peter.Anderson'],
                'sn' => ['Anderson'],
                'givenName' => ['Peter'],
                'entryUUID' => [md5('uid=Peter Anderson')],
                'employeeNumber' => ['0109'],
                'mail' => ['invalid-mail']
            ],
            [ // exceed user DN max length
                'dn' => 'uid=' . str_repeat('Sara.Morgan', 20) . ',ou=hr,ou=user,dc=example,dc=org',
                'cn' => ['Sara.Morgan'],
                'sn' => ['Morgan'],
                'givenName' => ['Sara'],
                'entryUUID' => [md5('uid=Sara.Morgan')],
                'employeeNumber' => ['0110'],
            ],
            [ // not exceed user DN max length
                'dn' => 'uid=' . str_repeat('Sara.Morgan', 20) . ',ou=adminuser,dc=example,dc=org',
                'cn' => ['Sara.Morgan_2'],
                'sn' => ['Morgan'],
                'givenName' => ['Sara'],
                'entryUUID' => [md5('uid=Sara.Morgan_2')],
                'employeeNumber' => ['00110'],
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
