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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeMembershipSearchFilterParams;

class EmployeeMembershipDao extends BaseDao
{
    /**
     * @param EmployeeMembership $employeeMembership
     * @return EmployeeMembership
     */
    public function saveEmployeeMembership(EmployeeMembership $employeeMembership): EmployeeMembership
    {
        $this->persist($employeeMembership);
        return $employeeMembership;
    }

    /**
     * @param int $empNumber
     * @param int $id
     * @return EmployeeMembership|null
     */
    public function getEmployeeMembershipById(int $empNumber, int $id): ?EmployeeMembership
    {
        $employeeMembership = $this->getRepository(EmployeeMembership::class)->findOneBy(
            [
                'employee' => $empNumber,
                'id' => $id,
            ]
        );
        if ($employeeMembership instanceof EmployeeMembership) {
            return $employeeMembership;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingEmployeeMembershipIdsForEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmployeeMembership::class, 'employeeMembership');

        $qb->select('employeeMembership.id')
            ->andWhere($qb->expr()->in('employeeMembership.id', ':ids'))
            ->andWhere($qb->expr()->eq('employeeMembership.employee', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEmployeeMemberships(int $empNumber, array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(EmployeeMembership::class, 'em');
        $q->delete()
            ->andWhere('em.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere($q->expr()->in('em.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return array
     */
    public function searchEmployeeMembership(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): array {
        $paginator = $this->getSearchEmployeeMembershipPaginator($employeeMembershipSearchParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return Paginator
     */
    private function getSearchEmployeeMembershipPaginator(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeMembership::class, 'em');
        $q->leftJoin('em.membership', 'm');
        $this->setSortingAndPaginationParams($q, $employeeMembershipSearchParams);

        $q->andWhere('em.employee = :empNumber')
            ->setParameter('empNumber', $employeeMembershipSearchParams->getEmpNumber());
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
     * @return int
     */
    public function getSearchEmployeeMembershipsCount(
        EmployeeMembershipSearchFilterParams $employeeMembershipSearchParams
    ): int {
        $paginator = $this->getSearchEmployeeMembershipPaginator($employeeMembershipSearchParams);
        return $paginator->count();
    }
}
