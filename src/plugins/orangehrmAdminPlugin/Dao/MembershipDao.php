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

use OrangeHRM\Admin\Dto\MembershipSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Membership;
use OrangeHRM\ORM\Paginator;

class MembershipDao extends BaseDao
{
    /**
     * @param Membership $membership
     * @return Membership
     */
    public function saveMembership(Membership $membership): Membership
    {
        $this->persist($membership);
        return $membership;
    }

    /**
     * @param int $id
     * @return Membership|null
     */
    public function getMembershipById(int $id): ?Membership
    {
        $membership = $this->getRepository(Membership::class)->find($id);
        if ($membership instanceof Membership) {
            return $membership;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingMembershipIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Membership::class, 'membership');
        $qb->select('membership.id')
            ->andWhere($qb->expr()->in('membership.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $name
     * @return Membership|null
     */
    public function getMembershipByName(string $name): ?Membership
    {
        $query = $this->createQueryBuilder(Membership::class, 'm');
        $trimmed = trim($name, ' ');
        $query->andWhere('m.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchFilterParams
     * @return int|mixed|string
     */
    public function getMembershipList(MembershipSearchFilterParams $membershipSearchFilterParams)
    {
        $paginator = $this->getMembershipListPaginator($membershipSearchFilterParams);
        return $paginator->getQuery()->execute();
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
     */
    public function getMembershipCount(MembershipSearchFilterParams $membershipSearchFilterParams): int
    {
        $paginator = $this->getMembershipListPaginator($membershipSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteMemberships(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Membership::class, 'm');
        $q->delete()
            ->where($q->expr()->in('m.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param $membershipName
     * @return bool
     */
    public function isExistingMembershipName($membershipName): bool
    {
        $q = $this->createQueryBuilder(Membership::class, 'm');
        $trimmed = trim($membershipName, ' ');
        $q->where('m.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
