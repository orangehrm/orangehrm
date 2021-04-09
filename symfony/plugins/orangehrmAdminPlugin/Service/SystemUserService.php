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
use OrangeHRM\Admin\Dao\SystemUserDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Entity\SystemUser;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Authentication\Dto\UserCredential;

class SystemUserService
{
    /**
     * @var SystemUserDao|null
     */
    protected ?SystemUserDao $systemUserDao = null;

    /** @property PasswordHash $passwordHasher */
    private ?PasswordHash $passwordHasher = null;

    /**
     * @return SystemUserDao
     */
    public function getSystemUserDao(): ?SystemUserDao
    {
        if (empty($this->systemUserDao)) {
            $this->systemUserDao = new SystemUserDao();
        }
        return $this->systemUserDao;
    }

    /**
     * @param SystemUserDao $systemUserDao
     */
    public function setSystemUserDao(SystemUserDao $systemUserDao): void
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

    public function setPasswordHasher(PasswordHash $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * Save System User
     *
     * @param SystemUser $systemUser
     * @param bool $changePassword
     * @return SystemUser|null
     * @throws ServiceException
     */
    public function saveSystemUser(SystemUser $systemUser, bool $changePassword = false): ?SystemUser
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
     * @return SystemUser|null
     * @throws ServiceException
     */
    public function isExistingSystemUser(string $userName, int $userId): ?SystemUser
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
     * @return SystemUser|null
     * @throws ServiceException
     */
    public function getSystemUser(int $userId): ?SystemUser
    {
        try {
            return $this->getSystemUserDao()->getSystemUser($userId);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System Users
     * @return SystemUser[]
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
     * @return Array of System User Ids
     * @version 2.7.1
     */
    public function getSystemUserIdList()
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
     * @return SystemUser[]
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

    public function isCurrentPassword($userId, $password)
    {
        $systemUser = $this->getSystemUserDao()->getSystemUser($userId);

        if (!($systemUser instanceof SystemUser)) {
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
     */
    public function updatePassword($userId, $password)
    {
        return $this->getSystemUserDao()->updatePassword($userId, $this->hashPassword($password));
    }

    public function getEmployeesByUserRole($roleName, $includeInactive = false, $includeTerminated = false)
    {
        return $this->getSystemUserDao()->getEmployeesByUserRole($roleName);
    }

    /**
     * @param UserCredential $credentials
     * @return SystemUser|null
     * @throws DaoException
     * @throws ServiceException
     */
    public function getCredentials(UserCredential $credentials): ?SystemUser
    {
        $user = $this->getSystemUserDao()->isExistingSystemUser($credentials);
        if ($user instanceof SystemUser) {
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
     * @return hashed password
     */
    public function hashPassword($password)
    {
        return $this->getPasswordHasher()->hash($password);
    }

    /**
     * Checks if the password hash matches the password.
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function checkPasswordHash($password, $hash)
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
    public function checkForOldHash($password, $hash)
    {
        $valid = false;

        if ($hash == md5($password)) {
            $valid = true;
        }

        return $valid;
    }

}
