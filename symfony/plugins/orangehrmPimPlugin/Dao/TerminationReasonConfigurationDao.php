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

namespace OrangeHRM\Pim\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\TerminationReasonConfigurationSearchFilterParams;

class TerminationReasonConfigurationDao extends BaseDao
{
    /**
     * @param TerminationReason $terminationReason
     * @return TerminationReason
     * @throws DaoException
     */
    public function saveTerminationReason(TerminationReason $terminationReason): TerminationReason
    {
        try {
            $this->persist($terminationReason);
            return $terminationReason;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return TerminationReason|null
     * @throws DaoException
     */
    public function getTerminationReasonById(int $id): ?TerminationReason
    {
        try {
            $terminationReason = $this->getRepository(TerminationReason::class)->find($id);
            if ($terminationReason instanceof TerminationReason) {
                return $terminationReason;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return TerminationReason|null
     * @throws DaoException
     */
    public function getTerminationReasonByName(string $name): ?TerminationReason
    {
        try {
            $query = $this->createQueryBuilder(TerminationReason::class, 'tr');
            $trimmed = trim($name, ' ');
            $query->andWhere('tr.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getTerminationReasonList(
        TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
    ): array {
        try {
            $paginator = $this->getTerminationReasonListPaginator($terminationReasonConfigurationSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
     * @throws DaoException
     */
    public function getTerminationReasonCount(
        TerminationReasonConfigurationSearchFilterParams $terminationReasonConfigurationSearchFilterParams
    ): int {
        try {
            $paginator = $this->getTerminationReasonListPaginator($terminationReasonConfigurationSearchFilterParams);
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
    public function deleteTerminationReasons(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
            $q->delete()
                ->where($q->expr()->in('tr.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $terminationReasonName
     * @return bool
     * @throws DaoException
     */
    public function isExistingTerminationReasonName(string $terminationReasonName): bool
    {
        try {
            $q = $this->createQueryBuilder(TerminationReason::class, 'tr');
            $trimmed = trim($terminationReasonName, ' ');
            $q->Where('tr.name = :name');
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

    /**
     * @return array
     * @throws DaoException
     */
    public function getReasonIdsInUse(): array
    {
        try {
            $query = $this->createQueryBuilder(Employee::class, 'e');
            $query->leftJoin('e.employeeTerminationRecord', 'et');
            $query->leftJoin('et.terminationReason', 'tr');
            $query->select('tr.id');
            $result = $query->getQuery()->getScalarResult();
            return array_column($result, 'id');
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
