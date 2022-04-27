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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class UserDao extends BaseDao
{
    /**
     * Save System User
     *
     * @param User $systemUser
     * @return User
     * @throws DaoException
     */
    public function saveSystemUser(User $systemUser): User
    {
        try {
            $this->persist($systemUser);
            return $systemUser;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Check is existing user according to user name
     *
     * @param UserCredential $credentials
     * @param int|null $userId
     * @return User|null
     * @throws DaoException
     */
    public function isExistingSystemUser(UserCredential $credentials, ?int $userId = null): ?User
    {
        try {
            $query = $this->createQueryBuilder(User::class, 'u');
            $query->andWhere('u.userName = :username');
            $query->setParameter('username', $credentials->getUsername());
            if (!empty($userId)) {
                $query->andWhere('u.id = :userId');
                $query->setParameter('userId', $userId);
            }

            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System User for given User Id
     * @param int $userId
     * @return User|null
     * @throws DaoException
     */
    public function getSystemUser(int $userId): ?User
    {
        try {
            $user = $this->getRepository(User::class)->find($userId);
            if ($user instanceof User) {
                return $user;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System Users
     *
     * @return User[]
     * @throws DaoException
     */
    public function getSystemUsers(): array
    {
        try {
            $query = $this->createQueryBuilder(User::class, 'u');
            $query->andWhere('u.deleted = :deleted');
            $query->setParameter('deleted', false);

            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Return an array of System User Ids
     * @return array
     */
    public function getSystemUserIdList(): array
    {
        $query = $this->createQueryBuilder(User::class, 'u');
        $query->select('u.id');
        $query->andWhere('u.deleted = :deleted');
        $query->setParameter('deleted', false);

        $result = $query->getQuery()->getScalarResult();
        return array_column($result, 'id');
    }

    /**
     * Soft Delete System Users
     * @param array $deletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteSystemUsers(array $deletedIds): int
    {
        try {
            $q = $this->createQueryBuilder(User::class, 'u');
            $q->update()
                ->set('u.deleted', ':deleted')
                ->setParameter('deleted', true)
                ->where($q->expr()->in('u.id', ':ids'))
                ->setParameter('ids', $deletedIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return UserRole[]
     */
    public function getAssignableUserRoles(): array
    {
        $query = $this->createQueryBuilder(UserRole::class, 'ur');
        $query->andWhere($query->expr()->in('ur.isAssignable', 1));
        $query->addOrderBy('ur.name', ListSorter::ASCENDING);

        return $query->getQuery()->execute();
    }

    /**
     * @param string $roleName
     * @return UserRole|null
     * @throws DaoException
     */
    public function getUserRole(string $roleName): ?UserRole
    {
        try {
            $query = $this->createQueryBuilder(UserRole::class, 'ur');
            $query->andWhere('ur.name = :name');
            $query->setParameter('name', $roleName);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return UserRole|null
     * @throws DaoException
     */
    public function getUserRoleById(int $id): ?UserRole
    {
        try {
            $userRole = $this->getRepository(UserRole::class)->find($id);
            if ($userRole instanceof UserRole) {
                return $userRole;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return UserRole[]
     * @throws DaoException
     */
    public function getNonPredefinedUserRoles(): array
    {
        try {
            $query = $this->createQueryBuilder(UserRole::class, 'ur');
            $query->andWhere('ur.isPredefined = :isPredefined');
            $query->setParameter('isPredefined', false);
            $query->addOrderBy('ur.name', ListSorter::ASCENDING);

            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e);
        }
    }

    /**
     * Get Count of Search Query
     *
     * @param UserSearchFilterParams $userSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getSearchSystemUsersCount(UserSearchFilterParams $userSearchParamHolder): int
    {
        try {
            $paginator = $this->getSearchUserPaginator($userSearchParamHolder);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Search System Users
     *
     * @param UserSearchFilterParams $userSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function searchSystemUsers(UserSearchFilterParams $userSearchParamHolder): array
    {
        try {
            $paginator = $this->getSearchUserPaginator($userSearchParamHolder);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UserSearchFilterParams $userSearchParamHolder
     * @return Paginator
     */
    private function getSearchUserPaginator(UserSearchFilterParams $userSearchParamHolder): Paginator
    {
        $q = $this->createQueryBuilder(User::class, 'u');
        $q->leftJoin('u.userRole', 'r');
        $q->leftJoin('u.employee', 'e');
        $this->setSortingAndPaginationParams($q, $userSearchParamHolder);

        if (!empty($userSearchParamHolder->getUsername())) {
            $q->andWhere('u.userName = :userName');
            $q->setParameter('userName', $userSearchParamHolder->getUsername());
        }
        if (!is_null($userSearchParamHolder->getUserRoleId())) {
            $q->andWhere('r.id = :userRoleId');
            $q->setParameter('userRoleId', $userSearchParamHolder->getUserRoleId());
        }
        if (!is_null($userSearchParamHolder->getEmpNumber())) {
            $q->andWhere('e.empNumber = :empNumber');
            $q->setParameter('empNumber', $userSearchParamHolder->getEmpNumber());
        }
        if (!is_null($userSearchParamHolder->getStatus())) {
            $q->andWhere('u.status = :status');
            $q->setParameter('status', $userSearchParamHolder->getStatus());
        }

        $q->andWhere('u.deleted = :deleted');
        $q->setParameter('deleted', false);

        return $this->getPaginator($q);
    }

    /**
     * @param bool $enabledOnly
     * @param bool $undeletedOnly
     * @return int
     */
    public function getAdminUserCount(bool $enabledOnly = true, bool $undeletedOnly = true): int
    {
        $q = $this->createQueryBuilder(User::class, 'u');
        $q->leftJoin('u.userRole', 'ur');
        $q->andWhere('ur.name = :userRoleName');
        $q->setParameter('userRoleName', 'Admin');
        if ($enabledOnly) {
            $q->andWhere('u.status = :status');
            $q->setParameter('status', true);
        }
        if ($undeletedOnly) {
            $q->andWhere('u.deleted = :deleted');
            $q->setParameter('deleted', false);
        }

        $paginator = new Paginator($q);
        return $paginator->count();
    }

    /**
     * @param int $userId
     * @param string $password
     * @return bool
     * @throws DaoException
     */
    public function updatePassword(int $userId, string $password): bool
    {
        try {
            $q = $this->createQueryBuilder(User::class, 'u');
            $q->update()
                ->set('u.userPassword', ':userPassword')
                ->setParameter('userPassword', $password)
                ->where('u.id = :id')
                ->setParameter('id', $userId);
            $result = $q->getQuery()->execute();
            return !($result < 1);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
        try {
            $q = $this->createQueryBuilder(Employee::class, 'e');
            $q->innerJoin('e.users', 'u');
            $q->leftJoin('u.userRole', 'r');
            $q->andWhere('r.name = :roleName');
            $q->setParameter('roleName', $roleName);

            if (!$includeInactive) {
                $q->andWhere('u.deleted = :deleted');
                $q->setParameter('deleted', false);
            }

            if (!$includeTerminated) {
                $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
            }

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     **this function for validating the username availability. ( true -> username already exist, false - username is not exist )
     * @param string $userName
     * @param int|null $userId
     * @return bool
     */
    public function isUserNameExistByUserName(string $userName, ?int $userId = null): bool
    {
        $q = $this->createQueryBuilder(User::class, 'u');
        $q->andWhere('u.userName = :userName');
        $q->setParameter('userName', $userName);
        if (!is_null($userId)) {
            $q->andWhere(
                'u.id != :userId'
            ); // we need to skip the current username on checking, otherwise count always return 1
            $q->setParameter('userId', $userId);
        }
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUserByUserName(string $username): ?User
    {
        $user = $this->getRepository(User::class)->findOneBy(['userName' => $username]);
        if ($user instanceof User) {
            return $user;
        }
        return null;
    }

    /**
     * @return User|null
     */
    public function getDefaultAdminUser(): ?User
    {
        return $this->getRepository(User::class)->findOneBy(['createdBy' => null]);
    }
}
