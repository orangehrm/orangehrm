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

namespace OrangeHRM\Tests\LDAP\Api;

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Api\LDAPConfigAPI;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group LDAP
 * @group APIv2
 */
class LDAPConfigAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('LDAPConfig.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LDAPConfigAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('LDAPConfigTestCases.yaml', 'GetOne');
    }

    public static function saveLDAPConfigPreHook(): void
    {
        $ldapSettings = new LDAPSetting('localhost', 389, 'OpenLDAP', 'none', null);
        $ldapSettings->setVersion(3);
        $ldapSettings->setOptReferrals(false);
        $ldapSettings->setBindAnonymously(true);
        $ldapSettings->setBindUserDN(null);
        $ldapSettings->setBindUserPassword(null);
        $ldapSettings->setSearchScope('sub');
        $ldapSettings->setUserNameAttribute('cn');
        $ldapSettings->setDataMapping([
            "firstname" => "givenName",
            "lastname" => "sn",
            "userStatus" => null,
            "workEmail" => null,
            "employeeId" => null
        ]);
        $ldapSettings->setGroupObjectClass('group');
        $ldapSettings->setGroupObjectFilter("(&(objectClass=group)(cn=*))");
        $ldapSettings->setGroupNameAttribute('cn');
        $ldapSettings->setGroupMembersAttribute('member');
        $ldapSettings->setGroupMembershipAttribute('memberOf');
        $ldapSettings->setSyncInterval(60);
        $ldapSettings->setEnable(false);

        $config = new Config();
        $config->setName(ConfigService::KEY_LDAP_SETTINGS);
        $config->setValue($ldapSettings->getEncodedAttributes());
        Doctrine::getEntityManager()->persist($config);
        Doctrine::getEntityManager()->flush($config);
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('LDAPConfig.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LDAPConfigAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('LDAPConfigTestCases.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new LDAPConfigAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LDAPConfigAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
