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
use OrangeHRM\Admin\Dto\MembershipSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Membership;
use OrangeHRM\ORM\Paginator;

class MembershipDao extends BaseDao
{
    /**
     * @param Membership $membership
     * @return Membership
     * @throws DaoException
     */
    public function saveMembership(Membership $membership): Membership
    {
        try {
            $this->persist($membership);
            return $membership;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return Membership|null
     * @throws DaoException
     */
    public function getMembershipById(int $id): ?Membership
    {
        try {
            $membership = $this->getRepository(Membership::class)->find($id);
            if ($membership instanceof Membership) {
                return $membership;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return Membership|null
     * @throws DaoException
     */
    public function getMembershipByName(string $name): ?Membership
    {
        try {
            $query = $this->createQueryBuilder(Membership::class, 'm');
            $trimmed = trim($name, ' ');
            $query->andWhere('m.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchFilterParams
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getMembershipList(MembershipSearchFilterParams $membershipSearchFilterParams)
    {
        try {
            $paginator = $this->getMembershipListPaginator($membershipSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchFilterParams
     * @return Paginator
     */
    public function getMembershipListPaginator(MembershipSearchFilterParams $membershipSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(Membership::class, 'm');
        $this->setSortingAndPaginationParams($q, $membershipSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getMembershipCount(MembershipSearchFilterParams $membershipSearchFilterParams): int
    {
        try {
            $paginator = $this->getMembershipListPaginator($membershipSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteMemberships(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(Membership::class, 'm');
            $q->delete()
                ->where($q->expr()->in('m.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $membershipName
     * @return bool
     * @throws DaoException
     */
    public function isExistingMembershipName($membershipName): bool
    {
        try {
            $q = $this->createQueryBuilder(Membership::class, 'm');
            $trimmed = trim($membershipName, ' ');
            $q->Where('m.name = :name');
            $q->setParameter('name', $trimmed);
            $count = $this->count($q);
            if ($count > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
