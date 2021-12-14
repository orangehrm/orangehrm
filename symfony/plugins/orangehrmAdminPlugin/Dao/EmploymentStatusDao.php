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
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\ORM\Paginator;

class EmploymentStatusDao extends BaseDao
{
    /**
     * @param int $id
     * @return EmploymentStatus|null
     * @throws DaoException
     */
    public function getEmploymentStatusById(int $id): ?EmploymentStatus
    {
        try {
            $employmentStatus = $this->getRepository(EmploymentStatus::class)->find($id);
            if ($employmentStatus instanceof EmploymentStatus) {
                return $employmentStatus;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param EmploymentStatus $employmentStatus
     * @return EmploymentStatus
     * @throws DaoException
     */
    public function saveEmploymentStatus(EmploymentStatus $employmentStatus): EmploymentStatus
    {
        try {
            $this->persist($employmentStatus);
            return $employmentStatus;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param array $toBeDeletedEmploymentStatusIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmploymentStatus(array $toBeDeletedEmploymentStatusIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmploymentStatus::class, 'es');
            $q->delete()
                ->where($q->expr()->in('es.id', ':ids'))
                ->setParameter('ids', $toBeDeletedEmploymentStatusIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search Employment Statuses
     *
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return array
     * @throws DaoException
     */
    public function searchEmploymentStatus(EmploymentStatusSearchFilterParams $employmentStatusSearchParams): array
    {
        try {
            $q = $this->getSearchEmploymentStatusPaginator($employmentStatusSearchParams);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
     * @throws DaoException
     */
    public function getEmploymentStatuses(): array
    {
        try {
            return $this->getRepository(
                EmploymentStatus::class
            )->findAll();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Count of Search Query
     *
     * @param EmploymentStatusSearchFilterParams $employmentStatusSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchEmploymentStatusesCount(
        EmploymentStatusSearchFilterParams $employmentStatusSearchParams
    ): int {
        try {
            $paginator = $this->getSearchEmploymentStatusPaginator($employmentStatusSearchParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
