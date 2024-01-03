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

namespace OrangeHRM\LDAP\Dto;

class LDAPUserCollection
{
    private array $ldapUsers;
    private array $duplicateUsernames;
    private array $usersOfDuplicateUsernames;
    private array $failedUsers;
    private ?int $duplicateUserCount = null;

    /**
     * @param array<string,LDAPUser> $ldapUsers e.g. ['user1' => LDAPUser, 'user2' => LDAPUser, ...]
     * @param string[] $duplicateUsernames
     * @param array<string,LDAPUser[]> $usersOfDuplicateUsernames e.g. ['user' => [LDAPUser, LDAPUser], ...]
     * @param string[] $failedUsers Failed user DNs
     */
    public function __construct(
        array $ldapUsers,
        array $duplicateUsernames,
        array $usersOfDuplicateUsernames,
        array $failedUsers = []
    ) {
        $this->ldapUsers = $ldapUsers;
        $this->duplicateUsernames = $duplicateUsernames;
        $this->usersOfDuplicateUsernames = $usersOfDuplicateUsernames;
        $this->failedUsers = $failedUsers;
    }

    /**
     * @return array<string,LDAPUser>
     */
    public function getLDAPUsers(): array
    {
        return $this->ldapUsers;
    }

    /**
     * @return string[]
     */
    public function getDuplicateUsernames(): array
    {
        return $this->duplicateUsernames;
    }

    /**
     * @return array<string,LDAPUser[]>
     */
    public function getUsersOfDuplicateUsernames(): array
    {
        return $this->usersOfDuplicateUsernames;
    }

    /**
     * @return int
     */
    public function getDuplicateUserCount(): int
    {
        if (is_null($this->duplicateUserCount)) {
            $this->duplicateUserCount = 0;
            foreach ($this->usersOfDuplicateUsernames as $username => $users) {
                $this->duplicateUserCount += count($users);
            }
        }
        return $this->duplicateUserCount;
    }

    /**
     * @return string[]
     */
    public function getFailedUsers(): array
    {
        return $this->failedUsers;
    }
}
