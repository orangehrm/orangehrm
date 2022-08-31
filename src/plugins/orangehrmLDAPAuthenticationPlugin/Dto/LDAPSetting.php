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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\LDAP\Dto;

use InvalidArgumentException;

class LDAPSetting
{
    private bool $enable = false;
    private string $host = 'localhost';
    private int $port = 389;
    private string $encryption = 'none';
    private string $implementation = 'OpenLDAP';
    private string $version = '3';
    private bool $optReferrals = false;

    private bool $bindAnonymously = true;
    private ?string $bindUserDN = null;
    private ?string $bindUserPassword = null;

    /**
     * @var LDAPUserLookupSetting[]
     */
    private array $userLookupSettings = [];

    private LDAPUserDataMapping $dataMapping;

    private string $groupObjectClass = 'group';
    private string $groupObjectFilter = '(&(objectClass=group)(cn=*))';
    private string $groupNameAttribute = 'cn';
    private string $groupMembersAttribute = 'member';
    private string $groupMembershipAttribute = 'memberOf';
    private int $syncInterval = 60;


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
        $setting->setBindUserPassword($config['bindUserPassword']);
        // User Lookup Settings
        foreach ($config['userLookupSettings'] as $userLookupSetting) {
            $setting->addUserLookupSetting(LDAPUserLookupSetting::createFromArray($userLookupSetting));
        }
        $setting->setGroupObjectClass($config['groupObjectClass']);
        $setting->setGroupObjectFilter($config['groupObjectFilter']);
        $setting->setGroupNameAttribute($config['groupNameAttribute']);
        $setting->setGroupMembersAttribute($config['groupMembersAttribute']);
        $setting->setGroupMembershipAttribute($config['groupMembershipAttribute']);
        // Data Mapping
        $setting->getDataMapping()->setAttributeNames($config['dataMapping']);
        // Additional Settings
        $setting->setSyncInterval($config['syncInterval']);

        return $setting;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
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
            'bindUserPassword' => $this->getBindUserPassword(),
            // User Lookup Settings
            'userLookupSettings' => array_map(
                fn (LDAPUserLookupSetting $lookupSetting) => $lookupSetting->toArray(),
                $this->getUserLookupSettings()
            ),
            'groupObjectClass' => $this->getGroupObjectClass(),
            'groupObjectFilter' => $this->getGroupObjectFilter(),
            'groupNameAttribute' => $this->getGroupNameAttribute(),
            'groupMembersAttribute' => $this->getGroupMembersAttribute(),
            'groupMembershipAttribute' => $this->getGroupMembershipAttribute(),
            // Data Mapping
            'dataMapping' => $this->getDataMapping()->toArray(),
            // Additional Settings
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
     * @return string
     */
    public function getGroupObjectClass(): string
    {
        return $this->groupObjectClass;
    }

    /**
     * @param string $groupObjectClass
     */
    public function setGroupObjectClass(string $groupObjectClass): void
    {
        $this->groupObjectClass = $groupObjectClass;
    }

    /**
     * @return string
     */
    public function getGroupObjectFilter(): string
    {
        return $this->groupObjectFilter;
    }

    /**
     * @param string $groupObjectFilter
     */
    public function setGroupObjectFilter(string $groupObjectFilter): void
    {
        $this->groupObjectFilter = $groupObjectFilter;
    }

    /**
     * @return string
     */
    public function getGroupNameAttribute(): string
    {
        return $this->groupNameAttribute;
    }

    /**
     * @param string $groupNameAttribute
     */
    public function setGroupNameAttribute(string $groupNameAttribute): void
    {
        $this->groupNameAttribute = $groupNameAttribute;
    }

    /**
     * @return string
     */
    public function getGroupMembersAttribute(): string
    {
        return $this->groupMembersAttribute;
    }

    /**
     * @param string $groupMembersAttribute
     */
    public function setGroupMembersAttribute(string $groupMembersAttribute): void
    {
        $this->groupMembersAttribute = $groupMembersAttribute;
    }

    /**
     * @return string
     */
    public function getGroupMembershipAttribute(): string
    {
        return $this->groupMembershipAttribute;
    }

    /**
     * @param string $groupMembershipAttribute
     */
    public function setGroupMembershipAttribute(string $groupMembershipAttribute): void
    {
        $this->groupMembershipAttribute = $groupMembershipAttribute;
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
