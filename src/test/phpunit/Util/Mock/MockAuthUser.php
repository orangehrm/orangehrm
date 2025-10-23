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

use Exception;
use OrangeHRM\Authentication\Auth\User;

class MockAuthUser extends User
{
    /**
     * @inheritDoc
     */
    public function setAttribute(string $name, $value): void
    {
        throw $this->getException(__METHOD__, $name);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $name, $default = null)
    {
        throw $this->getException(__METHOD__, $name);
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute(string $name): bool
    {
        throw $this->getException(__METHOD__, $name);
    }

    /**
     * @inheritDoc
     */
    public function removeAttribute(string $name)
    {
        throw $this->getException(__METHOD__, $name);
    }

    /**
     * @inheritDoc
     */
    public function getAllAttributes(): array
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function addFlash(string $type, $message): void
    {
        throw $this->getException(__METHOD__, $type);
    }

    /**
     * @inheritDoc
     */
    public function getFlash(string $type, array $default = []): array
    {
        throw $this->getException(__METHOD__, $type);
    }

    /**
     * @inheritDoc
     */
    public function hasFlash(string $type): bool
    {
        throw $this->getException(__METHOD__, $type);
    }

    /**
     * @param string $method
     * @return Exception
     */
    private function getException(string $method, string $key = ''): Exception
    {
        return new Exception(
            "This `$method` should not call" .
            (empty($key) ? '' : " with first arg `$key`") .
                '. Hint: Mock this method'
        );
    }
}
