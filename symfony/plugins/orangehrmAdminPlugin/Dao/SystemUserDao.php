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

use Doctrine\ORM\QueryBuilder;
use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\SystemUser;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\DoctrineQuery;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Authentication\Dto\UserCredential;

class SystemUserDao
{

    /**
     * Save System User
     *
     * @param SystemUser $systemUser
     * @return SystemUser
     * @throws \DaoException
     */
    public function saveSystemUser(SystemUser $systemUser): SystemUser
    {
        try {
            Doctrine::getEntityManager()->persist($systemUser);
            Doctrine::getEntityManager()->flush();
            return $systemUser;
        } catch (Exception $e) {
            throw new \DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Check is existing user according to user name
     *
     * @param UserCredential $credentials
     * @param int|null $userId
     * @return SystemUser|null
     * @throws DaoException
     */
    public function isExistingSystemUser(UserCredential $credentials, ?int $userId = null): ?SystemUser
    {
        try {
            $query = Doctrine::getEntityManager()->getRepository(
                SystemUser::class
            )->createQueryBuilder('u');
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
     * @return SystemUser|null
     * @throws DaoException
     */
    public function getSystemUser(int $userId): ?SystemUser
    {
        try {
            $user = Doctrine::getEntityManager()->getRepository(SystemUser::class)->find($userId);
            if ($user instanceof SystemUser) {
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
     * @return SystemUser[]
     * @throws DaoException
     */
    public function getSystemUsers(): array
    {
        try {
            $query = Doctrine::getEntityManager()->getRepository(
                SystemUser::class
            )->createQueryBuilder('u');
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
     * @throws DaoException
     */
    public function getSystemUserIdList(): array
    {
        try {
            $query = Doctrine::getEntityManager()->getRepository(
                SystemUser::class
            )->createQueryBuilder('u');
            $query->select('u.id');
            $query->andWhere('u.deleted = :deleted');
            $query->setParameter('deleted', false);

            $result = $query->getQuery()->execute(null, DoctrineQuery::HYDRATE_SINGLE_SCALAR);

            if (is_string($result)) {
                $result = [$result];
            }

            return $result;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->update(SystemUser::class, 'u')
                ->set('u.deleted', true)
                ->add('where', $q->expr()->in('u.id', $deletedIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get System Users
     *
     * @return UserRole[]
     * @throws DaoException
     */
    public function getAssignableUserRoles(): array
    {
        try {
            $query = Doctrine::getEntityManager()->getRepository(
                UserRole::class
            )->createQueryBuilder('ur');
            $query->andWhere('ur.isAssignable = :isAssignable');
            $query->setParameter('isAssignable', false);
            $query->addOrderBy('ur.name', ListSorter::ASCENDING);

            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $roleName
     * @return UserRole|null
     * @throws DaoException
     */
    public function getUserRole(string $roleName): ?UserRole
    {
        try {
            $query = Doctrine::getEntityManager()->getRepository(
                UserRole::class
            )->createQueryBuilder('ur');
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
            $userRole = Doctrine::getEntityManager()->getRepository(UserRole::class)->find($id);
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
            $query = Doctrine::getEntityManager()->getRepository(
                UserRole::class
            )->createQueryBuilder('ur');
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
     * @param array $searchClues
     * @return int
     * @throws DaoException
     */
    public function getSearchSystemUsersCount(array $searchClues): int
    {
        try {
            $q = $this->_buildSearchQuery($searchClues);
            $paginator = new Paginator($q, true);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Search System Users
     *
     * @param array $searchClues
     * @return array
     * @throws DaoException
     */
    public function searchSystemUsers(array $searchClues): array
    {
        try {
            // Set defaults to sort order and limits
            $sortField = empty($searchClues['sortField']) ? 'u.userName' : $searchClues['sortField'];
            $sortOrder = strcasecmp(
                $searchClues['sortOrder'],
                ListSorter::DESCENDING
            ) === 0 ? ListSorter::DESCENDING : ListSorter::ASCENDING;
            $offset = empty($searchClues['offset']) ? null : $searchClues['offset'];
            $limit = empty($searchClues['limit']) ? null : $searchClues['limit'];

            $q = $this->_buildSearchQuery($searchClues);
            $q->addOrderBy($sortField, $sortOrder);

            if ($limit) {
                $q->setFirstResult($offset)
                    ->setMaxResults($limit);
            }

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $searchClues
     * @return QueryBuilder
     */
    private function _buildSearchQuery(array $searchClues): QueryBuilder
    {
        $q = Doctrine::getEntityManager()->getRepository(
            SystemUser::class
        )->createQueryBuilder('u');
        $q->leftJoin('u.userRole', 'r');

        if (!empty($searchClues['userName'])) {
            $q->andWhere('u.userName = :userName');
            $q->setParameter('userName', $searchClues['userName']);
        }
        if (!empty($searchClues['userType'])) {
            if (is_array($searchClues['userType'])) {
                $q->add('where', $q->expr()->in('r.id', $searchClues['userType']));
            } else {
                $q->andWhere('r.id = :userRoleId');
                $q->setParameter('userRoleId', $searchClues['userType']);
            }
        }
        if (!empty($searchClues['empNumber'])) {
            $q->andWhere('u.empNumber = :empNumber');
            $q->setParameter('empNumber', $searchClues['empNumber']);
        }
        if (isset($searchClues['status']) && $searchClues['status'] != '') {
            $q->andWhere('u.status = :status');
            $q->setParameter('status', $searchClues['status']);
        }

        if (isset($searchClues['location']) && $searchClues['location'] && $searchClues['location'] != '-1') {
            $q->leftJoin('u.Employee', 'e');
            $q->leftJoin('e.EmpLocations', 'l');
            $q->add('where', $q->expr()->in('l.locationId', explode(',', $searchClues['location'])));
        }

        if (isset($searchClues['user_ids']) && is_array($searchClues['user_ids'])) {
            $q->add('where', $q->expr()->in('u.id', $searchClues['user_ids']));
        }

        $q->andWhere('u.deleted = :deleted');
        $q->setParameter('deleted', false);

        return $q;
    }

    public function getAdminUserCount($enabledOnly = true, $undeletedOnly = true)
    {
        $q = Doctrine_Query::create()->from('SystemUser')
            ->where('user_role_id = ?', SystemUser::ADMIN_USER_ROLE_ID);

        if ($enabledOnly) {
            $q->addWhere('status = ?', SystemUser::ENABLED);
        }

        if ($undeletedOnly) {
            $q->addWhere('deleted = ?', SystemUser::UNDELETED);
        }

        return $q->count();
    }

    public function updatePassword($userId, $password)
    {
        try {
            $q = Doctrine_Query::create()
                ->update('SystemUser')
                ->set('user_password', '?', $password)
                ->where('id = ?', $userId);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getEmployeesByUserRole($roleName, $includeInactive = false, $includeTerminated = false)
    {
        try {
            $query = Doctrine_Query::create()
                ->from('Employee e')
                ->innerJoin('e.SystemUser s')
                ->leftJoin('s.UserRole r')
                ->where('r.name = ?', $roleName);

            if (!$includeInactive) {
                $query->andWhere('s.deleted = 0');
            }

            if (!$includeTerminated) {
                $query->andWhere('e.termination_id IS NULL');
            }

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
