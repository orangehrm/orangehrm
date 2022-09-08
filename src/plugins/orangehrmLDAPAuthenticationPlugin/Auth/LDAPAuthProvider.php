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

namespace OrangeHRM\LDAP\Auth;

use OrangeHRM\Authentication\Auth\AbstractAuthProvider;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\LDAP\Service\LDAPService;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use Symfony\Component\Ldap\Exception\LdapException;

class LDAPAuthProvider extends AbstractAuthProvider
{
    private LDAPService $ldapService;
    private LDAPSyncService $ldapSyncService;

    /**
     * @return LDAPService
     */
    private function getLdapService(): LDAPService
    {
        return $this->ldapService ??= new LDAPService();
    }

    /**
     * @return LDAPSyncService
     */
    private function getLdapSyncService(): LDAPSyncService
    {
        return $this->ldapSyncService ??= new LDAPSyncService();
    }

    /**
     * @inheritDoc
     */
    public function authenticate(UserCredential $credential): bool
    {
        $user = $this->getLdapSyncService()
            ->getLdapDao()
            ->getUserByUserName($credential->getUsername());
        $ldapAuthProvider = $this->getLdapSyncService()->filterLDAPAuthProvider($user->getAuthProviders());
        if (!$ldapAuthProvider instanceof UserAuthProvider) {
            return false;
        }

        $ldapCredential = new UserCredential($ldapAuthProvider->getLdapUserDN(), $credential->getPassword());
        try {
            $this->getLdapService()->bind($ldapCredential);
            return true;
        } catch (LdapException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return 5000;
    }
}
