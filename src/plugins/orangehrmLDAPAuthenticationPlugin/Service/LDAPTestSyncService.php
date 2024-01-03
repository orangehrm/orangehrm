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
use OrangeHRM\LDAP\Dto\LDAPSetting;

class LDAPTestSyncService extends LDAPSyncService
{
    /**
     * @param LDAPSetting $ldapSetting
     */
    public function __construct(LDAPSetting $ldapSetting)
    {
        $this->ldapSetting = $ldapSetting;
    }

    /**
     * @inheritDoc
     */
    protected function getLDAPService(): LDAPService
    {
        if (!$this->ldapService instanceof LDAPTestService) {
            $this->ldapService = new LDAPTestService($this->getLDAPSetting());
            $bindCredentials = new UserCredential();
            if (!$this->getLDAPSetting()->isBindAnonymously()) {
                $bindCredentials->setUsername($this->getLDAPSetting()->getBindUserDN());
                $bindCredentials->setPassword($this->getLDAPSetting()->getBindUserPassword());
            }
            $this->ldapService->bind($bindCredentials);
        }
        return $this->ldapService;
    }

    /**
     * @inheritDoc
     */
    protected function getLDAPSetting(): LDAPSetting
    {
        return $this->ldapSetting;
    }
}
