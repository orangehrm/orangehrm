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

namespace OrangeHRM\Core\Authorization\Exception;

use Exception;

class AuthorizationException extends Exception
{
    /**
     * @param string $entityType
     * @param string $method
     * @return static
     */
    public static function entityNotSupported(string $entityType, string $method): self
    {
        return new self("Entity `$entityType` not supported, @ `$method`");
    }

    /**
     * @param string $entityType
     * @param string $method
     * @return static
     */
    public static function entityNotImplemented(string $entityType, string $method): self
    {
        return new self("Entity `$entityType` not implemented, @ `$method`");
    }

    /**
     * @param string $method
     * @return static
     */
    public static function methodNotImplemented(string $method): self
    {
        return new self("Method `$method` not implemented");
    }
}
