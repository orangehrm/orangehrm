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

namespace OrangeHRM\LDAP\Service;

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\ConnectionInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Adapter\ExtLdap\Connection;
use Symfony\Component\Ldap\Adapter\ExtLdap\EntryManager;
use Symfony\Component\Ldap\Adapter\QueryInterface;

class LDAPService
{
    use ConfigServiceTrait;

    protected ?AdapterInterface $adapter = null;

    /**
     * @return ConnectionInterface|Connection
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->getAdapter()->getConnection();
    }

    /**
     * @return AdapterInterface
     * @internal
     */
    public function getAdapter(): AdapterInterface
    {
        if (!$this->adapter instanceof AdapterInterface) {
            $ldapSetting = $this->getConfigService()->getLDAPSetting();
            $this->adapter = new Adapter([
                'host' => $ldapSetting->getHost(),
                'port' => $ldapSetting->getPort(),
                'encryption' => $ldapSetting->getEncryption(),
                'version' => $ldapSetting->getVersion(),
                'referrals' => $ldapSetting->isOptReferrals(),
            ]);
        }
        return $this->adapter;
    }

    /**
     * @param UserCredential $credential
     */
    public function bind(UserCredential $credential): void
    {
        $this->getConnection()->bind($credential->getUsername(), $credential->getPassword());
    }

    /**
     * @param string $dn
     * @param string $query
     * @param array $options
     * @return QueryInterface
     */
    public function query(string $dn, string $query, array $options = []): QueryInterface
    {
        return $this->getAdapter()->createQuery($dn, $query, $options);
    }

    /**
     * @param string $subject
     * @param string $ignore
     * @param int $flags
     * @return string
     */
    public function escape(string $subject, string $ignore = '', int $flags = 0): string
    {
        return $this->getAdapter()->escape($subject, $ignore, $flags);
    }

    /**
     * @return EntryManager
     */
    public function getEntryManager(): EntryManager
    {
        return $this->getAdapter()->getEntryManager();
    }
}
