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

namespace OrangeHRM\Authentication\Auth;

use InvalidArgumentException;
use OrangeHRM\Authentication\Dto\AuthParamsInterface;

class AuthProviderChain
{
    /**
     * @var AbstractAuthProvider[]
     */
    private array $providers = [];

    /**
     * @var string[]
     */
    private array $providerClasses = [];

    /**
     * @var int[]
     */
    private array $priorities = [];

    /**
     * @param AbstractAuthProvider $authProvider
     */
    public function addProvider(AbstractAuthProvider $authProvider): void
    {
        $providerClass = get_class($authProvider);
        if (in_array($providerClass, $this->providerClasses)) {
            throw new InvalidArgumentException("Instance of `$providerClass` already register as auth provider");
        }
        if (in_array($authProvider->getPriority(), $this->priorities)) {
            throw new InvalidArgumentException(
                "Conflicting priority value of `$providerClass` with another auth provider"
            );
        }
        $this->providers[] = $authProvider;
        $this->priorities[] = $authProvider->getPriority();
        $this->providerClasses[] = $providerClass;
    }

    /**
     * @param AuthParamsInterface $authParams
     * @return bool
     */
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        array_multisort($this->priorities, SORT_DESC, $this->providers);
        foreach ($this->providers as $authProvider) {
            if ($authProvider->authenticate($authParams)) {
                return true;
            }
        }
        return false;
    }
}
