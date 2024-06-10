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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\TerminationReasonConfigurationSearchFilterParams;

class TerminationReasonConfigurationDao extends BaseDao
{
    /**
     * @param TerminationReason $terminationReason
     * @return TerminationReason
     */
    public function saveTerminationReason(TerminationReason $terminationReason): TerminationReason
    {
        $this->persist($terminationReason);
        return $terminationReason;
    }

    /**
     * @param int $id
     * @return TerminationReason|null
     */
    public function getTerminationReasonById(int $id): ?TerminationReason
    {
        $terminationReason = $this->getRepository(TerminationReason::class)->find($id);
        if ($terminationReason instanceof TerminationReason) {
            return $terminationReason;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingTerminationReasonIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(TerminationReason::class, 'terminationReason');

        $qb->select('terminationReason.id')
            ->andWhere($qb->expr()->in('terminationReason.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $name
     * @return TerminationReason|null
     */
    public function getTerminationReasonByName(string $name): ?TerminationReason
    {
        $query = $this->createQueryBuilder(TerminationReason::class, 'tr');
        $trimmed = trim($name, ' ');
        $query->andWhere('tr.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
     * @return array
     */
    public function getTerminationReasonList(
        TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
    ): array {
        $paginator = $this->getTerminationReasonListPaginator($terminationReasonConfigurationSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
     * @return Paginator
     */
    public function getTerminationReasonListPaginator(
        TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
        $this->setSortingAndPaginationParams($q, $terminationReasonConfigurationSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
     * @return int
     */
    public function getTerminationReasonCount(
        TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
    ): int {
        $paginator = $this->getTerminationReasonListPaginator($terminationReasonConfigurationSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteTerminationReasons(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
        $q->delete()
            ->where($q->expr()->in('tr.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param string $terminationReasonName
     * @return bool
     */
    public function isExistingTerminationReasonName(string $terminationReasonName): bool
    {
        $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
        $trimmed = trim($terminationReasonName, ' ');
        $q->where('tr.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getReasonIdsInUse(): array
    {
        $query = $this->createQueryBuilder(Employee::class, 'e');
        $query->leftJoin('e.employeeTerminationRecord', 'et');
        $query->leftJoin('et.terminationReason', 'tr');
        $query->select('tr.id');
        $result = $query->getQuery()->getScalarResult();
        return array_column($result, 'id');
    }
}
