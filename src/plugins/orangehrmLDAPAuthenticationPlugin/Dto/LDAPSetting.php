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

namespace OrangeHRM\LDAP\Dto;

use InvalidArgumentException;
use OrangeHRM\Core\Utility\EncryptionHelperTrait;

class LDAPSetting
{
    use EncryptionHelperTrait;

    private bool $enable = false;
    private string $host = 'localhost';
    private int $port = 389;
    private string $encryption = 'none';
    private string $implementation = 'OpenLDAP';
    private string $version = '3';
    private bool $optReferrals = false;

    private bool $bindAnonymously = false;
    private ?string $bindUserDN = null;
    private ?string $bindUserPassword = null;

    /**
     * @var LDAPUserLookupSetting[]
     */
    private array $userLookupSettings = [];

    private LDAPUserDataMapping $dataMapping;

    private bool $mergeLDAPUsersWithExistingSystemUsers = false;
    private int $syncInterval = 1;


    /**
     * @param string $host
     * @param int $port
     * @param string $implementation
     * @param string $encryption
     */
    public function __construct(string $host, int $port, string $implementation, string $encryption)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setImplementation($implementation);
        $this->setEncryption($encryption);
        $this->dataMapping = new LDAPUserDataMapping();
    }


    /**
     * @param string $string
     * @return static
     */
    public static function fromString(string $string): self
    {
        $config = json_decode($string, true);
        $setting = new self(
            $config['host'],
            $config['port'],
            $config['implementation'],
            $config['encryption']
        );
        $setting->setEnable($config['enable']);
        $setting->setVersion($config['version']);
        $setting->setOptReferrals($config['optReferrals']);
        // Bind settings
        $setting->setBindAnonymously($config['bindAnonymously']);
        $setting->setBindUserDN($config['bindUserDN']);
        $bindUserPassword = $config['bindUserPassword'];
        if (self::encryptionEnabled() && $bindUserPassword !== null) {
            $bindUserPassword = self::getCryptographer()->decrypt($bindUserPassword);
        }
        $setting->setBindUserPassword($bindUserPassword);
        // User Lookup Settings
        foreach ($config['userLookupSettings'] as $userLookupSetting) {
            $setting->addUserLookupSetting(LDAPUserLookupSetting::createFromArray($userLookupSetting));
        }
        // Data Mapping
        $setting->getDataMapping()->setAttributeNames($config['dataMapping']);
        // Additional Settings
        $setting->setMergeLDAPUsersWithExistingSystemUsers($config['mergeLDAPUsersWithExistingSystemUsers']);
        $setting->setSyncInterval($config['syncInterval']);

        return $setting;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $bindUserPassword = $this->getBindUserPassword();
        if (self::encryptionEnabled() && $bindUserPassword !== null) {
            $bindUserPassword = self::getCryptographer()->encrypt($bindUserPassword);
        }
        return json_encode([
            'enable' => $this->isEnable(),
            // Server Settings
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'encryption' => $this->getEncryption(),
            'implementation' => $this->getImplementation(),
            'version' => $this->getVersion(),
            'optReferrals' => $this->isOptReferrals(),
            // Bind settings
            'bindAnonymously' => $this->isBindAnonymously(),
            'bindUserDN' => $this->getBindUserDN(),
            'bindUserPassword' => $bindUserPassword,
            // User Lookup Settings
            'userLookupSettings' => array_map(
                fn (LDAPUserLookupSetting $lookupSetting) => $lookupSetting->toArray(),
                $this->getUserLookupSettings()
            ),
            // Data Mapping
            'dataMapping' => $this->getDataMapping()->toArray(),
            // Additional Settings
            'mergeLDAPUsersWithExistingSystemUsers' => $this->shouldMergeLDAPUsersWithExistingSystemUsers(),
            'syncInterval' => $this->getSyncInterval()
        ]);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getEncryption(): string
    {
        return $this->encryption;
    }

    /**
     * @param string $encryption
     */
    public function setEncryption(string $encryption): void
    {
        if (!in_array($encryption, ['none', 'tls', 'ssl'])) {
            throw new InvalidArgumentException("Invalid encryption: `$encryption` type");
        }
        $this->encryption = $encryption;
    }

    /**
     * @return string
     */
    public function getImplementation(): string
    {
        return $this->implementation;
    }

    /**
     * @param string $implementation
     */
    public function setImplementation(string $implementation): void
    {
        if (!in_array($implementation, ['OpenLDAP', 'ActiveDirectory'])) {
            throw new InvalidArgumentException("Invalid implementation: `$implementation` type");
        }
        $this->implementation = $implementation;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return bool
     */
    public function isOptReferrals(): bool
    {
        return $this->optReferrals;
    }

    /**
     * @param bool $optReferrals
     */
    public function setOptReferrals(bool $optReferrals): void
    {
        $this->optReferrals = $optReferrals;
    }

    /**
     * @return bool
     */
    public function isBindAnonymously(): bool
    {
        return $this->bindAnonymously;
    }

    /**
     * @param bool $bindAnonymously
     */
    public function setBindAnonymously(bool $bindAnonymously): void
    {
        $this->bindAnonymously = $bindAnonymously;
    }

    /**
     * @return string|null
     */
    public function getBindUserDN(): ?string
    {
        return $this->bindUserDN;
    }

    /**
     * @param string|null $bindUserDN
     */
    public function setBindUserDN(?string $bindUserDN): void
    {
        $this->bindUserDN = $bindUserDN;
    }

    /**
     * @return string|null
     */
    public function getBindUserPassword(): ?string
    {
        return $this->bindUserPassword;
    }

    /**
     * @param string|null $bindUserPassword
     */
    public function setBindUserPassword(?string $bindUserPassword): void
    {
        $this->bindUserPassword = $bindUserPassword;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    /**
     * @return bool
     */
    public function shouldMergeLDAPUsersWithExistingSystemUsers(): bool
    {
        return $this->mergeLDAPUsersWithExistingSystemUsers;
    }

    /**
     * @param bool $mergeLDAPUsersWithExistingSystemUsers
     */
    public function setMergeLDAPUsersWithExistingSystemUsers(bool $mergeLDAPUsersWithExistingSystemUsers): void
    {
        $this->mergeLDAPUsersWithExistingSystemUsers = $mergeLDAPUsersWithExistingSystemUsers;
    }

    /**
     * @return int
     */
    public function getSyncInterval(): int
    {
        return $this->syncInterval;
    }

    /**
     * @param int $syncInterval
     */
    public function setSyncInterval(int $syncInterval): void
    {
        $this->syncInterval = $syncInterval;
    }

    /**
     * @return LDAPUserDataMapping
     */
    public function getDataMapping(): LDAPUserDataMapping
    {
        return $this->dataMapping;
    }

    /**
     * @return LDAPUserLookupSetting[]
     */
    public function getUserLookupSettings(): array
    {
        return $this->userLookupSettings;
    }

    /**
     * @param LDAPUserLookupSetting $userLookupSetting
     */
    public function addUserLookupSetting(LDAPUserLookupSetting $userLookupSetting): void
    {
        $this->userLookupSettings[] = $userLookupSetting;
    }
}
