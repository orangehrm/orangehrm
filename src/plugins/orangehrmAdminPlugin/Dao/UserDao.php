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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class UserDao extends BaseDao
{
    /**
     * @param User $systemUser
     * @return User
     */
    public function saveSystemUser(User $systemUser): User
    {
        $this->persist($systemUser);
        return $systemUser;
    }

    /**
     * @param UserCredential $credentials
     * @return User|null
     */
    public function isExistingSystemUser(UserCredential $credentials): ?User
    {
        $query = $this->createQueryBuilder(User::class, 'u');
        $query->andWhere('u.userName = :username')
            ->setParameter('username', $credentials->getUsername());
        $query->andWhere($query->expr()->isNotNull('u.userPassword'));
        $query->andWhere('u.deleted = :deleted')
            ->setParameter('deleted', false);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Get System User for given User Id
     * @param int $userId
     * @return User|null
     */
    public function getSystemUser(int $userId): ?User
    {
        return $this->getRepository(User::class)
            ->findOneBy(['id' => $userId, 'deleted' => false]);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getExistingSystemUserIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(User::class, 'user');

        $qb->select('user.id')
            ->andWhere($qb->expr()->in('user.id', ':ids'))
            ->andWhere($qb->expr()->eq('user.deleted', ':deleted'))
            ->setParameter('ids', $ids)
            ->setParameter('deleted', false);

        return $qb->getQuery()->getSingleColumnResult();
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
     */
    public function deleteSystemUsers(array $deletedIds): int
    {
        $q = $this->createQueryBuilder(User::class, 'u');
        $q->update()
            ->set('u.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('u.id', ':ids'))
            ->setParameter('ids', $deletedIds);
        return $q->getQuery()->execute();
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
     */
    public function getUserRole(string $roleName): ?UserRole
    {
        $query = $this->createQueryBuilder(UserRole::class, 'ur');
        $query->andWhere('ur.name = :name');
        $query->setParameter('name', $roleName);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Get Count of Search Query
     *
     * @param UserSearchFilterParams $userSearchParamHolder
     * @return int
     */
    public function getSearchSystemUsersCount(UserSearchFilterParams $userSearchParamHolder): int
    {
        $paginator = $this->getSearchUserPaginator($userSearchParamHolder);
        return $paginator->count();
    }

    /**
     * Search System Users
     *
     * @param UserSearchFilterParams $userSearchParamHolder
     * @return User[]
     */
    public function searchSystemUsers(UserSearchFilterParams $userSearchParamHolder): array
    {
        $paginator = $this->getSearchUserPaginator($userSearchParamHolder);
        return $paginator->getQuery()->execute();
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
        if (is_bool($userSearchParamHolder->hasPassword())) {
            $method = $userSearchParamHolder->hasPassword() ? 'isNotNull' : 'isNull';
            $q->andWhere($q->expr()->$method('u.userPassword'));
        }

        $q->andWhere('u.deleted = :deleted');
        $q->setParameter('deleted', false);

        return $this->getPaginator($q);
    }

    /**
     * @param string $roleName
     * @param bool $includeInactive
     * @param bool $includeTerminated
     * @return Employee[]
     */
    public function getEmployeesByUserRole(
        string $roleName,
        bool $includeInactive = false,
        bool $includeTerminated = false
    ): array {
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
    }

    /**
     **this function for validating the username availability. ( true -> username already exist, false - username is not exist )
     * @param string $userName
     * @param int|null $userId
     * @return bool
     */
    public function isUserNameExistByUserName(string $userName, ?int $userId = null): bool
    {
        $q = $this->createQueryBuilder(User::class, 'u')
            ->andWhere('u.userName = :userName')
            ->setParameter('userName', $userName)
            ->andWhere('u.deleted = :deleted')
            ->setParameter('deleted', false);

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
        return $this->getRepository(User::class)->findOneBy(['userName' => $username]);
    }

    /**
     * @return User|null
     */
    public function getDefaultAdminUser(): ?User
    {
        $q = $this->createQueryBuilder(User::class, 'user');
        return $q->andWhere($q->expr()->isNull('user.createdBy'))
            ->andWhere($q->expr()->isNotNull('user.userPassword'))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
