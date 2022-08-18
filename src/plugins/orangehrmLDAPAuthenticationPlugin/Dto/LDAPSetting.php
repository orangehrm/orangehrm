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
use Symfony\Component\Ldap\Adapter\QueryInterface;

class LDAPSetting
{
    private bool $enable;
    private string $host;
    private int $port;
    private string $encryption;
    private string $implementation;
    private string $version = '3';
    private bool $optReferrals = false;

    private bool $bindAnonymously = true;
    private ?string $bindUserDN = null;
    private ?string $bindUserPassword = null;

    private ?string $baseDN = null;
    private string $searchScope = QueryInterface::SCOPE_SUB;

    private string $userNameAttribute;

    private array $dataMapping;

    private string $groupObjectClass;
    private string $groupObjectFilter;
    private string $groupNameAttribute;
    private string $groupMembersAttribute;
    private string $groupMembershipAttribute;
    private string $syncInterval;


    /**
     * @param string $host
     * @param int $port
     * @param string $implementation
     * @param string $encryption
     * @param string|null $baseDN
     */
    public function __construct(string $host, int $port, string $implementation, string $encryption, ?string $baseDN)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setImplementation($implementation);
        $this->setEncryption($encryption);
        $this->setBaseDN($baseDN);
    }


    /**
     * @param string $string
     * @return static
     */
    public static function fromString(string $string): self
    {
        $config = json_decode($string);
        $setting = new self(
            $config['host'],
            $config['port'],
            $config['implementation'],
            $config['encryption'],
            $config['baseDN']
        );
        $setting->setVersion($config['version']);
        $setting->setOptReferrals($config['optReferrals']);
        $setting->setBindAnonymously($config['bindAnonymously']);
        $setting->setBindUserDN($config['bindUserDN']);
        $setting->setBindUserPassword($config['bindUserPassword']);
        $setting->setSearchScope($config['searchScope']);
        $setting->setUserNameAttribute($config['userNameAttribute']);
        $setting->setDataMapping($config['dataMapping']);
        $setting->setGroupObjectClass($config['groupObjectClass']);
        $setting->setGroupObjectFilter($config['groupObjectFilter']);
        $setting->setGroupNameAttribute($config['groupNameAttribute']);
        $setting->setGroupMembersAttribute($config['groupMembersAttribute']);
        $setting->setGroupMembershipAttribute($config['groupMembershipAttribute']);
        $setting->setSyncInterval($config['syncInterval']);

        return $setting;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode([
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'encryption' => $this->getEncryption(),
            'implementation' => $this->getImplementation(),
            'version' => $this->getVersion(),
            'optReferrals' => $this->isOptReferrals(),
            'bindAnonymously'=>$this->isBindAnonymously(),
            'bindUserDN'=>$this->getBindUserDN(),
            'bindUserPassword' => $this->getBindUserPassword(),
            'baseDN' => $this->getBaseDN(),
            'searchScope' => $this->getSearchScope()
        ]);
    }

    /**
     * @return string
     */
    public function getEncodedAttributes(): string
    {
        return json_encode([
            'enable' => $this->isEnable(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'encryption' => $this->getEncryption(),
            'implementation' => $this->getImplementation(),
            'version' => $this->getVersion(),
            'optReferrals' => $this->isOptReferrals(),
            'bindAnonymously' => $this->isBindAnonymously(),
            'bindUserDN' => $this->getBindUserDN(),
            'bindUserPassword' => $this->getBindUserPassword(),
            'baseDN' => $this->getBaseDN(),
            'searchScope' => $this->getSearchScope(),
            'dataMapping' => $this->getDataMapping(),
            'groupObjectClass' => $this->getGroupObjectClass(),
            'groupObjectFilter' => $this->getGroupObjectFilter(),
            'groupNameAttribute' => $this->getGroupNameAttribute(),
            'groupMembersAttribute' => $this->getGroupMembersAttribute(),
            'groupMembershipAttribute' => $this->getGroupMembershipAttribute(),
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
     * @return string|null
     */
    public function getBaseDN(): ?string
    {
        return $this->baseDN;
    }

    /**
     * @param string|null $baseDN
     */
    public function setBaseDN(?string $baseDN): void
    {
        $this->baseDN = $baseDN;
    }

    /**
     * @return string
     */
    public function getSearchScope(): string
    {
        return $this->searchScope;
    }

    /**
     * @param string $searchScope
     */
    public function setSearchScope(string $searchScope): void
    {
        if (!in_array($searchScope, [QueryInterface::SCOPE_SUB, QueryInterface::SCOPE_ONE])) {
            throw new InvalidArgumentException("Invalid search scope: `$searchScope`");
        }
        $this->searchScope = $searchScope;
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
    public function getUserNameAttribute(): string
    {
        return $this->userNameAttribute;
    }

    /**
     * @param string $userNameAttribute
     */
    public function setUserNameAttribute(string $userNameAttribute): void
    {
        $this->userNameAttribute = $userNameAttribute;
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
     * @return string
     */
    public function getSyncInterval(): string
    {
        return $this->syncInterval;
    }

    /**
     * @param string $syncInterval
     */
    public function setSyncInterval(string $syncInterval): void
    {
        $this->syncInterval = $syncInterval;
    }

    /**
     * @return array
     */
    public function getDataMapping(): array
    {
        return $this->dataMapping;
    }

    /**
     * @param array $dataMapping
     */
    public function setDataMapping(array $dataMapping): void
    {
        $this->dataMapping = $dataMapping;
    }
}
