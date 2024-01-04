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

namespace OrangeHRM\LDAP\Auth;

use OrangeHRM\Authentication\Auth\AbstractAuthProvider;
use OrangeHRM\Authentication\Dto\AuthParamsInterface;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Dto\UserCredentialInterface;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\LDAP\Service\LDAPService;
use OrangeHRM\LDAP\Service\LDAPSyncService;
use Throwable;

class LDAPAuthProvider extends AbstractAuthProvider
{
    use LoggerTrait;

    private LDAPService $ldapService;
    private LDAPSyncService $ldapSyncService;
    private AuthenticationService $authenticationService;

    /**
     * @return LDAPService
     */
    private function getLDAPService(): LDAPService
    {
        return $this->ldapService ??= new LDAPService();
    }

    /**
     * @return LDAPSyncService
     */
    private function getLDAPSyncService(): LDAPSyncService
    {
        return $this->ldapSyncService ??= new LDAPSyncService();
    }

    /**
     * @return AuthenticationService
     */
    private function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService ??= new AuthenticationService();
    }

    /**
     * @inheritDoc
     */
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        if (!$authParams->getCredential() instanceof UserCredentialInterface) {
            return false;
        }
        $credential = $authParams->getCredential();
        $user = $this->getLDAPSyncService()
            ->getLDAPDao()
            ->getNonLocalUserByUserName($credential->getUsername(), false);
        if ($user === null) {
            return false;
        }
        $ldapAuthProvider = $this->getLDAPSyncService()->filterLDAPAuthProvider($user->getAuthProviders());
        if (!$ldapAuthProvider instanceof UserAuthProvider) {
            return false;
        }

        $ldapCredential = new UserCredential($ldapAuthProvider->getLDAPUserDN(), $credential->getPassword());
        try {
            $this->getLDAPService()->bind($ldapCredential);
            return $this->getAuthenticationService()->setCredentialsForUser($user);
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (Throwable $e) {
            // Ignore logging stack trace to avoid dump bind passwords in the log file
            $this->getLogger()->error($e->getMessage());
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
