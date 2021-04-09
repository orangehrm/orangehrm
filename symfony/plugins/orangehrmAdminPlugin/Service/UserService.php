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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Admin\Service;

use Exception;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Authentication\Dto\UserCredential;

class UserService
{
    /**
     * @var UserDao|null
     */
    protected ?UserDao $systemUserDao = null;

    /** @property PasswordHash $passwordHasher */
    private ?PasswordHash $passwordHasher = null;

    /**
     * @return UserDao
     */
    public function getSystemUserDao(): ?UserDao
    {
        if (empty($this->systemUserDao)) {
            $this->systemUserDao = new UserDao();
        }
        return $this->systemUserDao;
    }

    /**
     * @param UserDao $systemUserDao
     */
    public function setSystemUserDao(UserDao $systemUserDao): void
    {
        $this->systemUserDao = $systemUserDao;
    }

    /**
     * @return PasswordHash
     */
    public function getPasswordHasher(): PasswordHash
    {
        if (empty($this->passwordHasher)) {
            $this->passwordHasher = new PasswordHash();
        }
        return $this->passwordHasher;
    }

    /**
     * @param PasswordHash $passwordHasher
     */
    public function setPasswordHasher(PasswordHash $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Save System User
     *
     * @param User $systemUser
     * @param bool $changePassword
     * @return User|null
     * @throws ServiceException
     */
    public function saveSystemUser(User $systemUser, bool $changePassword = false): ?User
    {
        try {
            if ($changePassword) {
                $systemUser->setUserPassword($this->hashPassword($systemUser->getUserPassword()));
            }

            return $this->getSystemUserDao()->saveSystemUser($systemUser);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Check is existing user according to user name
     * @param string $userName
     * @param int $userId
     * @return User|null
     * @throws ServiceException
     */
    public function isExistingSystemUser(string $userName, int $userId): ?User
    {
        try {
            $credentials = new UserCredential($userName);
            return $this->getSystemUserDao()->isExistingSystemUser($credentials, $userId);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System User for given User Id
     * @param int $userId
     * @return User|null
     * @throws ServiceException
     */
    public function getSystemUser(int $userId): ?User
    {
        try {
            return $this->getSystemUserDao()->getSystemUser($userId);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System Users
     * @return User[]
     * @throws ServiceException
     */
    public function getSystemUsers(): array
    {
        try {
            return $this->getSystemUserDao()->getSystemUsers();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Return an array of System User Ids
     *
     * <pre>
     *
     * The output will be an array like below.
     *
     * array(
     *          0 => 1,
     *          1 => 2,
     *          2 => 3
     * )
     * </pre>
     *
     * @return array
     * @throws DaoException
     */
    public function getSystemUserIdList(): array
    {
        return $this->getSystemUserDao()->getSystemUserIdList();
    }

    /**
     * Soft Delete System Users
     * @param array $deletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteSystemUsers(array $deletedIds): int
    {
        return $this->getSystemUserDao()->deleteSystemUsers($deletedIds);
    }

    /**
     * Get Pre Defined User Roles
     *
     * @return User[]
     * @throws ServiceException
     */
    public function getAssignableUserRoles(): array
    {
        try {
            return $this->getSystemUserDao()->getAssignableUserRoles();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get User role with given name
     * @param $roleName
     * @return UserRole|null
     * @throws ServiceException
     */
    public function getUserRole(string $roleName): ?UserRole
    {
        try {
            return $this->getSystemUserDao()->getUserRole($roleName);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return UserRole|null
     * @throws DaoException
     */
    public function getUserRoleById(int $id): ?UserRole
    {
        return $this->getSystemUserDao()->getUserRoleById($id);
    }

    /**
     * @return UserRole[]
     * @throws DaoException
     */
    public function getNonPredefinedUserRoles(): array
    {
        return $this->getSystemUserDao()->getNonPredefinedUserRoles();
    }

    /**
     * @param array $searchClues
     * @return int
     * @throws ServiceException
     */
    public function getSearchSystemUsersCount(array $searchClues): int
    {
        try {
            return $this->getSystemUserDao()->getSearchSystemUsersCount($searchClues);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $searchClues
     * @return array
     * @throws ServiceException
     */
    public function searchSystemUsers(array $searchClues): array
    {
        try {
            return $this->getSystemUserDao()->searchSystemUsers($searchClues);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $userId
     * @param string $password
     * @return bool
     * @throws DaoException
     */
    public function isCurrentPassword(int $userId, string $password): bool
    {
        $systemUser = $this->getSystemUserDao()->getSystemUser($userId);

        if (!($systemUser instanceof User)) {
            return false;
        }

        $hash = $systemUser->getUserPassword();
        if ($this->checkPasswordHash($password, $hash)) {
            return true;
        } else {
            if ($this->checkForOldHash($password, $hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Updates the password of given user
     *
     * @param int $userId User ID of the user
     * @param string $password Non-encrypted password
     * @return int
     * @throws DaoException
     */
    public function updatePassword(int $userId, string $password): int
    {
        return $this->getSystemUserDao()->updatePassword($userId, $this->hashPassword($password));
    }

    /**
     * @param string $roleName
     * @param bool $includeInactive
     * @param bool $includeTerminated
     * @return Employee[]
     * @throws DaoException
     */
    public function getEmployeesByUserRole(
        string $roleName,
        bool $includeInactive = false,
        bool $includeTerminated = false
    ): array {
        return $this->getSystemUserDao()->getEmployeesByUserRole($roleName, $includeInactive, $includeTerminated);
    }

    /**
     * @param UserCredential $credentials
     * @return User|null
     * @throws DaoException
     * @throws ServiceException
     */
    public function getCredentials(UserCredential $credentials): ?User
    {
        $user = $this->getSystemUserDao()->isExistingSystemUser($credentials);
        if ($user instanceof User) {
            $hash = $user->getUserPassword();
            if ($this->checkPasswordHash($credentials->getPassword(), $hash)) {
                return $user;
            } elseif ($this->checkForOldHash($credentials->getPassword(), $hash)) {
                // password matches, but in old format. Need to update hash
                $user->setUserPassword($credentials->getPassword());
                return $this->saveSystemUser($user, true);
            }
        }

        return null;
    }

    /**
     * Hash password for storage
     * @param string $password
     * @return string hashed password
     */
    public function hashPassword(string $password): string
    {
        return $this->getPasswordHasher()->hash($password);
    }

    /**
     * Checks if the password hash matches the password.
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function checkPasswordHash(string $password, string $hash): bool
    {
        return $this->getPasswordHasher()->verify($password, $hash);
    }

    /**
     * Check if password matches hash for hashes stored using older hash methods.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function checkForOldHash(string $password, string $hash): bool
    {
        $valid = false;

        if ($hash == md5($password)) {
            $valid = true;
        }

        return $valid;
    }
}
