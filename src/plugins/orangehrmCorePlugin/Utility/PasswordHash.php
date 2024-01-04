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

namespace OrangeHRM\Core\Utility;

/**
 * A simple wrapper around the php password_hash and password_verify functions
 */
class PasswordHash
{
    public const ALGORITHM = PASSWORD_BCRYPT;

    // 2^12 iterations
    public const COST = 12;

    /**
     * Create password hash
     *
     * @param string $password Password
     * @return false|string|null
     */
    public function hash(string $password)
    {
        $options = [
            'cost' => self::COST
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Verify password
     *
     * @param string $password Password
     * @param string $hash Hash
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
