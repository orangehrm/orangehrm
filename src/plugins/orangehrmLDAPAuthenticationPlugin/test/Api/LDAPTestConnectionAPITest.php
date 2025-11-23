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

namespace OrangeHRM\Tests\LDAP\Api;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Api\LDAPConfigAPI;
use OrangeHRM\LDAP\Api\LDAPTestConnectionAPI;
use OrangeHRM\LDAP\Dto\EntryCollection;
use OrangeHRM\LDAP\Dto\EntryCollectionLookupSettingPair;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Service\LDAPTestService;
use OrangeHRM\LDAP\Service\LDAPTestSyncService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\LDAP\LDAPConnectionHelperTrait;
use OrangeHRM\Tests\LDAP\LDAPServerConfig;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;
use Symfony\Component\Ldap\Entry;

/**
 * @group LDAP
 * @group APIv2
 */
class LDAPTestConnectionAPITest extends EndpointTestCase
{
    use LDAPConnectionHelperTrait;

    private static LDAPServerConfig $serverConfig;

    public function setUp(): void
    {
        if (!self::isLDAPServerConfigured()) {
            parent::markTestSkipped('Configure LDAP server config: ' . self::getLDAPServerConfigFilePath());
        }
        self::$serverConfig = self::getLDAPServerConfig();
    }

    public function testCreate(): void
    {
        $ldapSetting = new LDAPSetting(self::$serverConfig->host, self::$serverConfig->port, 'OpenLDAP', 'none');
        $lookupSetting = new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org');
        $lookupSetting->setSearchScope('sub');
        $ldapSetting->addUserLookupSetting($lookupSetting);
        $ldapSetting->setBindAnonymously(false);
        $ldapSetting->setBindUserDN(self::$serverConfig->adminDN);
        $ldapSetting->setBindUserPassword(self::$serverConfig->adminPassword);

        $ldapTestService = $this->getMockBuilder(LDAPTestService::class)
            ->onlyMethods(['testAuthentication'])
            ->setConstructorArgs([$ldapSetting])
            ->getMock();
        $ldapTestService->expects($this->once())
            ->method('testAuthentication')
            ->will(
                $this->returnValue([
                    'message' => 'Ok',
                    'status' => LDAPTestService::STATUS_SUCCESS
                ])
            );

        $ldapTestSyncService = $this->getMockBuilder(LDAPTestSyncService::class)
            ->onlyMethods(['fetchEntryCollections'])
            ->setConstructorArgs([$ldapSetting])
            ->getMock();

        $users = ['Linda.Anderson', 'Rebecca.Harmony', 'Lisa.Andrews', 'Jacqueline.White'];
        $ldapTestSyncService->expects($this->exactly(2))
            ->method('fetchEntryCollections')
            ->willReturn($this->getEntryCollection($this->getLDAPEntries($users), $lookupSetting));

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getLDAPSetting'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getLDAPSetting')
            ->willReturn(null); //This will fetch the value stored in the DB.
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => $configService,
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::LDAP_LOGGER => LoggerFactory::getLogger('LDAP'),
        ]);

        /** @var MockObject&LDAPTestConnectionAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LDAPTestConnectionAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    LDAPConfigAPI::PARAMETER_HOSTNAME => self::$serverConfig->host,
                    LDAPConfigAPI::PARAMETER_PORT => self::$serverConfig->port,
                    LDAPConfigAPI::PARAMETER_ENCRYPTION => 'none',
                    LDAPConfigAPI::PARAMETER_LDAP_IMPLEMENTATION => 'OpenLDAP',
                    LDAPConfigAPI::PARAMETER_BIND_ANONYMOUSLY => 'false',
                    LDAPConfigAPI::PARAMETER_BIND_USER_DISTINGUISHED_NAME => 'cn=admin,dc=example,dc=org',
                    LDAPConfigAPI::PARAMETER_BIND_USER_PASSWORD => 'admin',
                    LDAPConfigAPI::PARAMETER_USER_LOOKUP_SETTINGS => [
                        [
                            LDAPConfigAPI::PARAMETER_BASE_DISTINGUISHED_NAME => 'ou=admin,ou=users,dc=example,dc=org',
                            LDAPConfigAPI::PARAMETER_SEARCH_SCOPE => 'sub',
                            LDAPConfigAPI::PARAMETER_USER_NAME_ATTRIBUTE => 'cn',
                            LDAPConfigAPI::PARAMETER_USER_UNIQUE_ID_ATTRIBUTE => null,
                            LDAPConfigAPI::PARAMETER_USER_SEARCH_FILTER => 'objectClass=person',
                            LDAPConfigAPI::PARAMETER_EMPLOYEE_SELECTOR_MAPPING => []
                        ]
                    ],
                    LDAPConfigAPI::PARAMETER_DATA_MAPPING => [
                        LDAPConfigAPI::PARAMETER_FIRST_NAME => 'givenName',
                        LDAPConfigAPI::PARAMETER_LAST_NAME => 'sn',
                        LDAPConfigAPI::PARAMETER_USER_STATUS => null,
                        LDAPConfigAPI::PARAMETER_WORK_EMAIL => null,
                        LDAPConfigAPI::PARAMETER_EMPLOYEE_ID => null
                    ]
                ]
            ]
        )
            ->onlyMethods(['getLDAPTestService','getLDAPTestSyncService'])
            ->getMock();

        $api->expects($this->once())
            ->method('getLDAPTestService')
            ->willReturn($ldapTestService);

        $api->expects($this->once())
            ->method('getLDAPTestSyncService')
            ->willReturn($ldapTestSyncService);

        $result = $api->create();
        $this->assertEquals(
            [
                [
                    "category" => "Login",
                    "checks" => [
                        [
                            "label" => "Authentication",
                            "value" => [
                                'message' => "Ok",
                                'status' => 1
                            ]
                        ]
                    ]
                ],
                [
                    "category" => "Lookup",
                    "checks" => [
                        [
                            "label" => "User lookup",
                            "value" => [
                                "message" => "Ok",
                                "status" => 1
                            ]
                        ],
                        [
                            "label" => "Search results",
                            "value" => [
                                "message" => "{count} user(s) found", //TODO::Show Actual Count
                                "status" => 1
                            ]
                        ],
                        [
                            "label" => "Users",
                            "value" => [
                                "message" => "{count} user(s) will be imported", //TODO::Show Actual Count
                                "status" => 1
                            ]
                        ]
                    ]
                ]
            ],
            $result->normalize()
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
            ->onlyMethods(['toArray', 'count'])
            ->getMock();
        $collection->expects($this->once())
            ->method('toArray')
            ->willReturn($entries);
        $collection->expects($this->once())
            ->method('count')
            ->willReturn(count($entries));
        $entryCollectionLookupSettingPair = $this->getMockBuilder(EntryCollectionLookupSettingPair::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCollection', 'getLookupSetting'])
            ->getMock();
        $entryCollectionLookupSettingPair->expects($this->exactly(2)) //Called twice in the API inside getNormalizeLDAPSettings method
            ->method('getCollection')
            ->willReturn($collection);
        $entryCollectionLookupSettingPair->expects($this->exactly(count($entries)))
            ->method('getLookupSetting')
            ->willReturn($lookupSetting);
        return $this->getMockBuilder(EntryCollection::class)
            ->setConstructorArgs([$entryCollectionLookupSettingPair])
            ->onlyMethods([])
            ->getMock();
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
}
