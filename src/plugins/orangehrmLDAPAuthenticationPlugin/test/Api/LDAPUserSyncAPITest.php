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

use DateTime;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Config;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Api\LDAPUserSyncAPI;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\LDAP\LDAPConnectionHelperTrait;
use OrangeHRM\Tests\LDAP\LDAPServerConfig;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group LDAP
 * @group APIv2
 */
class LDAPUserSyncAPITest extends EndpointIntegrationTestCase
{
    use LDAPConnectionHelperTrait;

    private static LDAPServerConfig $serverConfig;

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        TestDataService::truncateSpecificTables([LDAPSyncStatus::class]);
        $this->populateFixtures('LDAPUserSync.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LDAPUserSyncAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('LDAPUserSyncTestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        if (!self::isLDAPServerConfigured()) {
            parent::markTestSkipped('Configure LDAP server config: ' . self::getLDAPServerConfigFilePath());
        }
        self::$serverConfig = self::getLDAPServerConfig();

        $this->populateFixtures('LDAPUserSync.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LDAPUserSyncAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('LDAPUserSyncTestCases.yaml', 'Create');
    }

    public static function syncPassPreHook(): void
    {
        $ldapSyncStatus = new LDAPSyncStatus();
        $ldapSyncStatus->setSyncStartedAt(new DateTime('2022-10-12 01:31'));
        $ldapSyncStatus->setSyncFinishedAt(new DateTime('2022-10-12 01:32'));
        $ldapSyncStatus->getDecorator()->setSyncedUserByUserId(1);
        $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED);

        Doctrine::getEntityManager()->persist($ldapSyncStatus);
        Doctrine::getEntityManager()->flush($ldapSyncStatus);
    }

    public static function syncFailedPreHook(): void
    {
        $ldapSyncStatus = new LDAPSyncStatus();
        $ldapSyncStatus->setSyncStartedAt(new DateTime('2022-10-12 01:31'));
        $ldapSyncStatus->getDecorator()->setSyncedUserByUserId(1);
        $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_FAILED);

        Doctrine::getEntityManager()->persist($ldapSyncStatus);
        Doctrine::getEntityManager()->flush($ldapSyncStatus);
    }

    public static function ldapConfigPreHook(): void
    {
        $ldapSettings = new LDAPSetting(self::$serverConfig->host, self::$serverConfig->port, 'OpenLDAP', 'none');
        $ldapSettings->setVersion(3);
        $ldapSettings->setOptReferrals(false);
        $ldapSettings->setBindAnonymously(true);
        $ldapSettings->setBindUserDN(null);
        $ldapSettings->setBindUserPassword(null);
        $ldapSettings->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSettings->getDataMapping()
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setUserStatusAttribute(null)
            ->setWorkEmailAttribute('mail')
            ->setEmployeeIdAttribute(null);
        $ldapSettings->setSyncInterval(60);
        $ldapSettings->setEnable(false);

        $config = new Config();
        $config->setName(ConfigService::KEY_LDAP_SETTINGS);
        $config->setValue((string)$ldapSettings);
        Doctrine::getEntityManager()->persist($config);
        Doctrine::getEntityManager()->flush($config);
    }

    public static function ldapConfigSyncEnablePreHook(): void
    {
        $ldapSettings = new LDAPSetting(self::$serverConfig->host, self::$serverConfig->port, 'OpenLDAP', 'none');
        $ldapSettings->setVersion(3);
        $ldapSettings->setOptReferrals(false);
        $ldapSettings->setBindAnonymously(true);
        $ldapSettings->setBindUserDN(null);
        $ldapSettings->setBindUserPassword(null);
        $ldapSettings->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSettings->getDataMapping()
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setUserStatusAttribute(null)
            ->setWorkEmailAttribute('mail')
            ->setEmployeeIdAttribute(null);
        $ldapSettings->setSyncInterval(60);
        $ldapSettings->setEnable(true);

        $config = new Config();
        $config->setName(ConfigService::KEY_LDAP_SETTINGS);
        $config->setValue((string)$ldapSettings);
        Doctrine::getEntityManager()->persist($config);
        Doctrine::getEntityManager()->flush($config);
    }

    public static function ldapInvalidConfigPreHook(): void
    {
        $ldapSettings = new LDAPSetting(self::$serverConfig->host, self::$serverConfig->port, 'OpenLDAP', 'none');
        $ldapSettings->setVersion(3);
        $ldapSettings->setOptReferrals(false);
        $ldapSettings->setBindAnonymously(true);
        $ldapSettings->setBindUserDN(null);
        $ldapSettings->setBindUserPassword(null);
        $ldapSettings->addUserLookupSetting(
            (new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=invalidHere'))
                ->setSearchScope('sub')
                ->setUserNameAttribute('cn')
                ->setUserSearchFilter('objectClass=inetOrgPerson')
                ->setUserUniqueIdAttribute('entryUUID')
        );
        $ldapSettings->getDataMapping()
            ->setFirstNameAttribute('givenName')
            ->setLastNameAttribute('sn')
            ->setUserStatusAttribute(null)
            ->setWorkEmailAttribute('mail')
            ->setEmployeeIdAttribute(null);
        $ldapSettings->setSyncInterval(60);
        $ldapSettings->setEnable(true);

        $config = new Config();
        $config->setName(ConfigService::KEY_LDAP_SETTINGS);
        $config->setValue((string)$ldapSettings);
        Doctrine::getEntityManager()->persist($config);
        Doctrine::getEntityManager()->flush($config);
    }

    public function testGetAll(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetAll();
    }

    public function testUpdate(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testDelete(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LDAPUserSyncAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
