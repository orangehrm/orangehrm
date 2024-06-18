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

use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\ORM\Paginator;

class EmploymentStatusDao extends BaseDao
{
    /**
     * @param int $id
     * @return EmploymentStatus|null
     */
    public function getEmploymentStatusById(int $id): ?EmploymentStatus
    {
        $employmentStatus = $this->getRepository(EmploymentStatus::class)->find($id);
        if ($employmentStatus instanceof EmploymentStatus) {
            return $employmentStatus;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingEmploymentStatusIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(EmploymentStatus::class, 'employmentStatus');
        $qb->select('employmentStatus.id')
            ->andWhere($qb->expr()->in('employmentStatus.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param EmploymentStatus $employmentStatus
     * @return EmploymentStatus
     */
    public function saveEmploymentStatus(EmploymentStatus $employmentStatus): EmploymentStatus
    {
        $this->persist($employmentStatus);
        return $employmentStatus;
    }

    /**
     * @param array $toBeDeletedEmploymentStatusIds
     * @return int
     */
    public function deleteEmploymentStatus(array $toBeDeletedEmploymentStatusIds): int
    {
        $q = $this->createQueryBuilder(EmploymentStatus::class, 'es');
        $q->delete()
            ->where($q->expr()->in('es.id', ':ids'))
            ->setParameter('ids', $toBeDeletedEmploymentStatusIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return array
     */
    public function searchEmploymentStatus(EmploymentStatusSearchFilterParams $employmentStatusSearchParams): array
    {
        $q = $this->getSearchEmploymentStatusPaginator($employmentStatusSearchParams);
        return $q->getQuery()->execute();
    }

    /**
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return Paginator
     */
    private function getSearchEmploymentStatusPaginator(
        EmploymentStatusSearchFilterParams $employmentStatusSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmploymentStatus::class, 'es');
        $this->setSortingAndPaginationParams($q, $employmentStatusSearchParams);

        if (!empty($employmentStatusSearchParams->getName())) {
            $q->andWhere('es.name = :name');
            $q->setParameter('name', $employmentStatusSearchParams->getName());
        }
        return $this->getPaginator($q);
    }

    /**
     * Get Employment Statuses
     *
     * @return EmploymentStatus[]
     */
    public function getEmploymentStatuses(): array
    {
        return $this->getRepository(
            EmploymentStatus::class
        )->findAll();
    }

    /**
     * Get Count of Search Query
     *
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return int
     */
    public function getSearchEmploymentStatusesCount(
        EmploymentStatusSearchFilterParams $employmentStatusSearchParams
    ): int {
        $paginator = $this->getSearchEmploymentStatusPaginator($employmentStatusSearchParams);
        return $paginator->count();
    }
}
