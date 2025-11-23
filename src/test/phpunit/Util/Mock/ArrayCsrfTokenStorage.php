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

namespace OrangeHRM\Tests\Util\Mock;

use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ArrayCsrfTokenStorage implements TokenStorageInterface
{
    private array $storage = [];

    /**
     * @inheritDoc
     */
    public function getToken(string $tokenId)
    {
        if (!$this->hasToken($tokenId)) {
            throw new TokenNotFoundException('The CSRF token with ID ' . $tokenId . ' does not exist.');
        }
        return $this->storage[$tokenId];
    }

    /**
     * @inheritDoc
     */
    public function setToken(string $tokenId, string $token)
    {
        $this->storage[$tokenId] = $token;
    }

    /**
     * @inheritDoc
     */
    public function removeToken(string $tokenId)
    {
        if (!$this->hasToken($tokenId)) {
            return null;
        }
        $token = $this->getToken($tokenId);

        unset($this->storage[$tokenId]);

        return $token;
    }

    /**
     * @inheritDoc
     */
    public function hasToken(string $tokenId)
    {
        return isset($this->storage[$tokenId]);
    }
}
