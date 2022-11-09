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
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;

class ReportingMethodConfigurationDao extends BaseDao
{
    /**
     * @param ReportingMethod $reportingMethod
     * @return ReportingMethod
     * @throws DaoException
     */
    public function saveReportingMethod(ReportingMethod $reportingMethod): ReportingMethod
    {
        try {
            $this->persist($reportingMethod);
            return $reportingMethod;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return ReportingMethod|null
     * @throws DaoException
     */
    public function getReportingMethodById(int $id): ?ReportingMethod
    {
        try {
            $reportingMethod = $this->getRepository(ReportingMethod::class)->find($id);
            if ($reportingMethod instanceof ReportingMethod) {
                return $reportingMethod;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return ReportingMethod|null
     * @throws DaoException
     */
    public function getReportingMethodByName(string $name): ?ReportingMethod
    {
        try {
            $query = $this->createQueryBuilder(ReportingMethod::class, 'rm');
            $trimmed = trim($name, ' ');
            $query->andWhere('rm.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return Paginator
     */
    public function getReportingMethodListPaginator(
        ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $this->setSortingAndPaginationParams($q, $reportingMethodSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getReportingMethodCount(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams): int
    {
        try {
            $paginator = $this->getReportingMethodListPaginator($reportingMethodSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getReportingMethodList(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams)
    {
        try {
            $paginator = $this->getReportingMethodListPaginator($reportingMethodSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteReportingMethods($toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
            $q->delete();
            $q->where($q->expr()->in('rm.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $reportingMethodName
     * @return bool
     * @throws DaoException
     */
    public function isExistingReportingMethodName(string $reportingMethodName): bool
    {
        try {
            $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
            $trimmed = trim($reportingMethodName, ' ');
            $q->where('rm.name = :name');
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
     * @return int[]
     * @throws DaoException
     */
    public function getReportingMethodIdsInUse(): array
    {
        try {
            $query = $this->createQueryBuilder(ReportTo::class, 'rt');
            $query->leftJoin('rt.reportingMethod', 'rm');
            $query->select('rm.id');
            $result = $query->getQuery()->getScalarResult();
            return array_column($result, 'id');
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
