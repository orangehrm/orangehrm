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

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Service\LDAPService;
use OrangeHRM\LDAP\Service\LDAPTestService;
use OrangeHRM\Tests\LDAP\LDAPConnectionHelperTrait;
use OrangeHRM\Tests\LDAP\LDAPServerConfig;
use OrangeHRM\Tests\LDAP\LDAPUsersFixture;
use OrangeHRM\Tests\Util\KernelTestCase;
use Symfony\Component\Ldap\Exception\ConnectionException;

class LDAPTestServiceAnonBindTest extends KernelTestCase
{
    use LDAPConnectionHelperTrait;

    /**
     * Use IP addresses in these range for invalid Host
     * 192.0.2.0 - 192.0.2.255
     * 198.51.100.0 - 198.51.100.255
     * 203.0.113.0 - 203.0.113.255
     */
    public const INVALID_HOST = '192.0.2.0';

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
                    self::$serverConfig->encryption,
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

    public function testAnonymousConnection(): void
    {
        $ldapSettings = new LDAPSetting(
            self::$serverConfig->host,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption,
        );
        $ldapTestService = new LDAPTestService($ldapSettings);
        $ldapTestService->testConnection();
        $this->assertTrue(true);
    }

    public function testInvalidHost(): void
    {
        $ldapSettings = new LDAPSetting(
            self::INVALID_HOST,
            self::$serverConfig->port,
            'OpenLDAP',
            self::$serverConfig->encryption,
        );
        $ldapTestService = new LDAPTestService($ldapSettings);
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage("Can't contact LDAP server");
        $ldapTestService->testConnection();
    }

    public function testInvalidPort(): void
    {
        $ldapSettings = new LDAPSetting(
            self::$serverConfig->host,
            89,
            'OpenLDAP',
            self::$serverConfig->encryption,
        );
        $ldapTestService = new LDAPTestService($ldapSettings);
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage("Can't contact LDAP server");
        $ldapTestService->testConnection();
    }

    public function testEncryptionWithPort(): void
    {
        $ldapSettings = new LDAPSetting(
            self::$serverConfig->host,
            389,
            'OpenLDAP',
            'ssl',
        );
        $ldapTestService = new LDAPTestService($ldapSettings);
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage("Can't contact LDAP server");
        $ldapTestService->testConnection();
    }
}
